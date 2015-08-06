<? 

    session_start();

    $host = "127.0.0.1";
    $user = "sevody";
    $pass = "";
    $db = "phantom_stock";
    
    if (($connection = mysql_connect($host, $user, $pass)) === false)
        die("Could not connect to database");
        
    if (mysql_select_db($db, $connection) === false)
        die("Could not select database");
        
    if (isset($_POST["username"]) && isset($_POST["password"]) && 
        isset($_POST["confirm_password"]) && isset($_POST["email"]))
    {
        if (empty($_POST["username"]))
        {
            echo "请输入用户名";
        }else if (empty($_POST["password"]))
        {
            echo "请输入密码";
        }else if (empty($_POST["confirm_password"]))
        {
            echo "请确认密码";
        }else if (empty($_POST["email"]))
        {
            echo "请输入邮箱";
        }else if ($_POST["password"] !== $_POST["confirm_password"])
        {
            echo "密码不一致";
        }
        else
        {
           
            //检查用户名和邮箱是否被注册
            $sql = sprintf("SELECT 1 FROM users WHERE user_name='%s'", 
                            strtolower(mysql_real_escape_string($_POST["user_name"])));
            
            $result = mysql_query($sql);
            if ($result ===false)
                die("Could not query database");
            
            if (mysql_num_rows($result) != 0)
            {
                echo "用户名已存在";
                exit;
            }
          
            $sql = sprintf("SELECT 1 FROM users WHERE email='%s'", 
                            strtolower(mysql_real_escape_string($_POST["email"])));  
                            
            $result = mysql_query($sql);
            if ($result ===false)
               die("Could not query database");
               
            if (mysql_num_rows($result) != 0)
            {
                echo "邮箱已被注册";
                exit;
            }
            
            //把注册信息写入数据库
            $sql = sprintf("INSERT INTO users (user_name, passwordhash, email) VALUES ('%s', '%s', '%s')",
                            mysql_real_escape_string(strtolower($_POST["username"])),
                            hash("SHA1",$_POST["password"]),
                            mysql_real_escape_string(strtolower($_POST["email"])));
           
            $result = mysql_query($sql);
            //print_r($result);
            if ($result !== false)
            {
                //注册成功
                $_SESSION["authenticated"] = true;
                    
                //设置cookies
                $userid = mysql_insert_id();
                $password = hash("SHA1",$_POST["password"]);
                $data = $userid."\t".hash("SHA1", $userid.$password);
                $sign = base64_encode($$data);
                setcookie("identity", $sign, time() + 7 * 24 * 60 * 60);
                
                //重定向到主页
                $host = $_SERVER["HTTP_HOST"];
                $path = rtrim(dirname($_SERVER["PHP_SELF"]), "/\\");
                header("Location: https://$host$path/index.php");
                exit;
            }
            else
            {
                echo "添加用户失败";
            }
        }
    }
    

?>





<!DOCTYPE html>

<html>
    <head>
        <title>注册</title>
        <meta charset="utf-8">
    </head>
    
    <body>
        <form action="<?= $_SERVER["PHP_SELF"]?>" method="post">
            <table>
                <tr>
                    <td>用户名</td>
                    <td><input type="text" name="username"/></td>
                </tr>
                <tr>
                    <td>密码</td>
                    <td><input type="password" name="password"/></td>
                </tr>
                <tr>
                    <td>确认密码</td>
                    <td><input type="password" name="confirm_password"/></td>
                </tr>
                <tr>
                    <td>邮箱</td>
                    <td><input type="text" name="email"/></td>
                </tr>
                <tr>
                    <td><input type="submit" value="提交"/></td>
                </tr>
            </table>
        </form>
    </body>
</html>


<? 

    session_start();

        
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
            //username允许存储大写,但不区分大小写
            $username = $_POST["username"];
            $password = $_POST["password"];
            //email全部小写
            $email = strtolower($_POST["email"]);
            
            include('conn.php');
            //检查用户名是否已被注册
            $check_username = $db->prepare('SELECT 1 FROM users 
                                            WHERE LOWER(user_name)=:username limit 1');
            $check_username->bindValue(':username', strtolower($username), PDO::PARAM_STR);
            $check_username->execute();
            
            $result = $check_username->fetchAll();
            
            if($result)
            {
                header("Content-type:text/html;charset=utf-8");
                echo "用户名已存在";
                exit;
            }
            
            //检查邮箱是否已被注册            
            $check_email = $db->prepare('SELECT 1 FROM users 
                                         WHERE email=:email limit 1');
            $check_email->bindValue(':email', $email, PDO::PARAM_STR);
            $check_email->execute();
            
            $result = $check_email->fetchAll();
            
            if($result)
            {
                header("Content-type:text/html;charset=utf-8");
                echo "邮箱已被注册";
                exit;
            }
            
            
            //把注册信息写入数据库
            $insert_user = $db->prepare('INSERT INTO users (user_name, passwordhash, email) 
                                         VALUES (:username, :passwordhash, :email)');
            $insert_user->execute(array(
                                        ':username'=>$username,
                                        ':passwordhash'=>hash("SHA1", $password),
                                        ':email'=>$email
                                        ));
            $lastId = $db->lastInsertId();
            
            //关闭连接
            $db = null;
            
            if($lastId)
            {
                
                //注册成功
                $_SESSION["authenticated"] = true;
                    
                //设置cookies
                $userid = $lastId;
                $password = hash("SHA1",$_POST["password"]);
                $data = $userid."\t".hash("SHA1", $userid.$password);
                $sign = base64_encode($data);
                setcookie("identity", $sign, time() + 7 * 24 * 60 * 60);
                
                //重定向到主页
                $host = $_SERVER["HTTP_HOST"];
                $path = rtrim(dirname($_SERVER["PHP_SELF"]), "/\\");
                header("Location: https://$host$path/index.php");
                exit;
            }
            else
            {
                header("Content-type:text/html;charset=utf-8");
                echo "注册失败";
                exit;
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


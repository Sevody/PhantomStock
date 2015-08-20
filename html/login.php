<?

    session_start();
    
    include('conn.php');
        
    if (isset($_POST["username"]) && isset($_POST["password"]))
    {
        if (!empty($_POST["username"]) && !empty($_POST["password"]))
        {
            $username = strtolower($_POST["username"]);
            $passwordhash = hash("SHA1",($_POST["password"]));
            
            $check_statement = $db->prepare('SELECT uid, passwordhash FROM users
                                             WHERE user_name=:username AND passwordhash=:passwordhash
                                             limit 1');
            
            $check_statement->execute(array(
                                            ':username'=>$username,
                                            ':passwordhash'=>$passwordhash
                                            ));
            $result = $check_statement->fetchAll();
            
            if($result && isset($result[0]['uid']))
            {
                
                //登录成功
                $_SESSION["authenticated"] = true;
                
                //设置cookies
                $userid = $result[0]['uid'];
                $passwordhash = $result[0]['passwordhash'];
                $data = $userid."\t".hash("SHA1", $userid.$passwordhash);
                $sign = base64_encode($data);
                setcookie("identity", $sign, time() + (7 * 24 * 60 * 60));
                
                //关闭连接
                $db = null;
                
                //重定向到主页
                $host = $_SERVER["HTTP_HOST"];
                $path = rtrim(dirname($_SERVER["PHP_SELF"]), "/\\");
                header("Location: https://$host$path/index.php");
                exit;
            }
            else
            {
                echo "密码错误或用户不存在";
            }
            
            
        }
        else
        {
            echo "请输入用户名和密码";
        }
    }
?>

<html>
    <head>
        <title>登录</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    </head>
    
    <body>
        <form action="<?= $_SERVER["PHP_SELF"] ?>" method="post">
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
                    <td><input type="submit" value="登录"/></td>
                </tr>
            </table>
        </form>
    </body>
</html>
<?    
    
    session_start();
    
   
    //flag for 是否已登录
    $logged = false;
    
    //session登录
    if (isset($_SESSION["authenticated"]) && 
              $_SESSION["authenticated"] === true)
    {
        $logged = true;
    }
    else
    {
        //cookies登录
        if (isset($_COOKIE["identity"]))
        {
            
            //连接数据库
            include('conn.php');
    
            //取出登陆信息的cookie值
            $sign = $_COOKIE["identity"];
            $data =base64_decode($sign);
            $data_array = explode("\t", $data);
            $userid = (int)$data_array[0];
            $uid_passwordhash_hash = $data_array[1];
            
            $passwordhash_statement = $db->prepare('SELECT passwordhash FROM users 
                                                    WHERE uid=:userid limit 1');
            $passwordhash_statementb->bindValue(':userid', $userid, PDO::PARAM_INT);
            $passwordhash_statementb->execute();
            $result = $passwordhash_statementb->fetchAll();
            
            //关闭连接
            $db = null;
            
            if ($result && isset($result[0]['passwordhash']))
            {
                $passwordhash = $result[0]['passwordhash'];
                $uid_passwordhash_hash2 = hash("SHA1", $userid.$passwordhash);
                
                if ($uid_passwordhash_hash === $uid_passwordhash_hash2)
                {
                    //cookies验证成功
                    $logged = true;
                }
            }
            
                            
        }
    }
    
?>
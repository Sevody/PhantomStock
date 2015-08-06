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
            $host = "127.0.0.1";
            $user = "sevody";
            $pass = "";
            $db = "phantom_stock";
  
            if (($connection = mysql_connect($host, $user, $pass)) === false)
            die("Could not connect to database");
        
            if (mysql_select_db($db, $connection) === false)
            die("Could not select database");
            
            //取出登陆信息的cookie值
            $sign = $_COOKIE["identity"];
            $data =base64_decode($sign);
            $data_array = explode("\t", $data);
            $userid = (int)$data_array[0];
            $uid_password_hash = $data_array[1];
            
            $sql = "SELECT passwordhash FROM users WHERE uid=$userid limit 1";
            $result = mysql_query($sql);
            
            if ($result)
            {
                $row = mysql_fetch_row($result);
                if (isset($row[0]))
                {
                    $passwordhash = $row[0];
                    $uid_password_hash2 = hash("SHA1", $userid.$passwordhash);
                    
                    if ($uid_password_hash === $uid_password_hash2)
                    {
                        //cookies验证成功
                        $logged = true;
                    }
                }
            }
            
                            
        }
    }
    
?>
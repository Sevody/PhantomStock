<? 


    $host = "127.0.0.1";
    $user = "sevody";
    $pass = "";
    $db = "phantom_stock";
    
    if (($connection = mysql_connect($host, $user, $pass)) === false)
        die("Could not connect to database");
        
    if (mysql_select_db($db, $connection) === false)
        die("Could not select database");

?>
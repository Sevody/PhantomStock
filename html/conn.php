<? 
    
    // 数据库配置信息
    define('DB_HOST', '127.0.0.1');
    define('DB_USER', 'sevody');
    define('DB_PASSWORD', '');
    define('DB_DATABASE', 'phantom_stock');

    // 连接服务器并选择数据库
    $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_DATABASE;
    
    try 
    {
        $db = new PDO($dsn, DB_USER, DB_PASSWORD);
        //防止中文乱码
        $db->exec("SET NAMES 'utf8'");
    } catch(PDOException $e) 
    {
        die('Could not connect to the database:<br/>' . $e);
    }
    
    // close connection
    // $db = null;
    

?>
<?


    session_start();

    setcookie("identity", "", time() - 3600);
    
    setcookie(session_name(), "", time() - 3600);
    
    session_destroy();
    
    
   
    $host = $_SERVER["HTTP_HOST"];
    $path = rtrim(dirname($_SERVER["PHP_SELF"]), "/\\");
    header("Location: https://$host$path/index.php");
    exit;
?>

<!DOCTYPE html>

<html>
    <head>
        <title>Log Out</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    </head>
    
    <body>
        <h3>You are logged out!</h3>
    </body>
</html>
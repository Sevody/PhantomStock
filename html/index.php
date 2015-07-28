<!--Home Page -->

<?
    session_start();
?>
<!DOCTYPE html>

<html>
    <head>
        <title>Phantom Stock</title>
        <meta charset="utf-8">
        
        <style type="text/css">
           *{
               margin:0;
               padding:0;
           }
           
           .center{
               text-align:center;
           }
           
           
        </style>
        
        <script type="text/javascript">
            
        </script>
    </head>
    
    <body>

        <div class="header">
            <h1 class="center">Phantom Stock</h1>
            <div class="guest">
                <? if (isset($_SESSION["authentucated"]) && $_SESSION["authentucated"] === true) {?>
                    <a class="profile" href="profile">Profile</a>
                <? } else { ?>
                    <a class="login" href="login.php">登录</a>
                    <a class="signup" href="signup.php">注册</a>
                <? } ?>
            </div>
        </div>
        
        <div class="container">Stocking!!!!</div>
        
        <div class="footer"></div>
        
    </body>
    
    
    
    
</html>
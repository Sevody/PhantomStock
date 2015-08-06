<!--Home Page -->

<?
    include('check_log.php');
?>
<!DOCTYPE html>

<html>
    <head>
        <title>Phantom Stock</title>
        <meta charset="utf-8">
        
        
        <link rel="stylesheet" href="header.css" type="text/css" />
        
        <style type="text/css">
           
        </style>
        
        <script type="text/javascript">
            
        </script>
    </head>
    
    <body>

        <? include('header.php'); ?>
        
        <div class="container">
            <div class="quote">
                <form action="quote.php" method="get">
                    <input type="text" name="symbol"/>
                    <input type="submit" value="Submit"/>
                </form>
            </div>
        </div>
        
        <div class="footer"></div>
        
    </body>
    
    
    
    
</html>
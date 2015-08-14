<?

   
   

    /*****************************************************************
    <--YQL API - The latest API where you're receiving XML or JSON formatted data (reference)-->
    
    $BASE_URL = "http://query.yahooapis.com/v1/public/yql";
    $yql_query = "select * from yahoo.finance.quote where symbol in ('{$s}')";
    $yql_query_url = $BASE_URL . "?q=" . urlencode($yql_query) . "&env=http://datatables.org/alltables.env"."&format=json";
    // Make call with cURL
    $session = curl_init($yql_query_url);
    curl_setopt($session, CURLOPT_RETURNTRANSFER,true);
    $json = curl_exec($session);
    // Convert JSON to PHP object
    $phpObj =  json_decode($json);
    
    if(!is_null($phpObj->query->results)){  
    // Safe to parse data  
    }  
    print "<pre>";
    print_r($phpObj);
    print"</pre>";
    *****************************************************************/
    
    
    include('check_log.php');
    include('quotes_source.php');
    
    //从google finance 中获取最新报价
    $stock = quoteList(urlencode($_GET["symbol"]), false);
    
    
?>

<!DOCTYPE html>


<html>
    <head>
        <title>quote</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="header.css" type="text/css" />
    </head>
    
    <body>
        <? include('header.php'); ?>
        <span><?= htmlspecialchars($stock[0]["symbol"])?> : </span>
        <span><?= htmlspecialchars($stock[0]["name"])?> : </span>
        <span><?= $stock[0]["last_trade"] ?> : </span>
        <span><?= $stock[0]["chg"] ?> : </span>
        <span><?= $stock[0]["change"] ?> : </span>
        <span><?= $stock[0]["volume"] ?> : </span>
        <span><?= $stock[0]["turnover"] ?> : </span>
        <span><?= $stock[0]["pe"] ?> : </span>
        <span><?= $stock[0]["open"] ?> : </span>
        <span><?= $stock[0]["close"] ?> : </span>
        <span><?= $stock[0]["high"] ?> : </span>
        <span><?= $stock[0]["low"] ?></span>
    </body>
</html>
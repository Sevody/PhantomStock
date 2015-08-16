<?


    
    include('check_log.php');
    include('quotes_source.php');
    include('chart_source.php');
    
    //获取最新报价
    $stock = quoteList(urlencode($_GET["symbol"]), false);
    
    //获取chart
    $chart_url = getChart($_GET["symbol"], "google");
    
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
        <div class="info">
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
        </div>
        <div class="chart">
            <img id="stock-chart" src="<?= $chart_url ?>"></img>
    
        </div>
    </body>
</html>
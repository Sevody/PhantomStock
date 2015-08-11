<?

    
    /*************************************************************
    http://www.google.com/finance/info?q=NASDAQ:GOOG
    http://www.google.com/finance/info?q=CURRENCY:GBPUSD
    http://finance.google.com/finance/info?client=ig&q=AAPL,YHOO
    https://www.google.com/finance/getchart?q=YELP
    http://www.google.com/finance/info?infotype=infoquoteall&q=C,JPM,AIG
    <--Google finance to get quote-->
    //Obtain Quote Info
    $quote = file_get_contents('http://finance.google.com/finance/info?client=ig&q=MSFT');

    //Remove CR's from ouput - make it one line
    $json = str_replace("\n", "", $quote);

    //Remove //, [ and ] to build qualified string  
    $data = substr($json, 4, strlen($json) -5);

    //decode JSON data
    $json_output = json_decode(utf8_decode($data));

    // get the last price
    $last = $json_output->l;

    //Output Stock price .
    echo 'QUOTE: ' . $last; 
    ***************************************************************/
    
    /**************************************************************
    <--Yahoo finance available api for shanghai stocks-->
    http://finance.yahoo.com/webservice/v1/symbols/600018.ss,000725.sz/quote?format=json
    
    $strQuote = file_get_contents("http://finance.yahoo.com/webservice/v1/symbols/{$s}/quote?format=json");
    
    $jsonQuote = json_decode($strQuote);
    
    print "<pre>";
    print_r($jsonQuote);
    print "</pre>";
    print "<pre>";
    print_r ($jsonQuote->list->resources[0]->resource->fields->price);
    print "</pre>";
    ****************************************************************/
    
    
    /****************************************************************
    <--CSV API - The API where you're receiving CSV formatted data-->
    $url = "http://download.finance.yahoo.com/d/quotes.csv?s={$s}&f=sl1d1t1c1ohgv&e=.csv";
    $handle = fopen($url, "r");
    $row = fgetcsv($handle);
    fclose($handle);
    ******************************************************************/

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
    
    
    //从google finance 中获取最新报价
    
    $s = urlencode($_GET["symbol"]);

    $quote = file_get_contents("http://finance.google.com/finance/info?client=ig&q={$s}");

    //Remove CR's from ouput - make it one line
    $json = str_replace("\n", "", $quote);

    //Remove //, [ and ] to build qualified string  
    $data = substr($json, 4, strlen($json) -5);

    //decode JSON data
    $json_output = json_decode(utf8_encode($data));

    // get the last price
    $last_trade = $json_output->l;
    $Chg = $json_output->cp;
    //Output Stock price .
    
    
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
        <span><?= htmlspecialchars($_GET["symbol"])?> : </span>
        <span><?= number_format($last_trade, 2) ?> : </span>
        <span>
            <? 
                if($Chg > 0) 
                {
                    echo "+".number_format($Chg, 2); 
                }
                else 
                {
                    echo number_format($Chr, 2);                           
                }
            ?>
        </span>
    </body>
</html>
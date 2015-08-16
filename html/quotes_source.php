


<?

    /*************************************************************
     *SHA:000001上证指数      SHE:399001深证指数
    http://www.google.com/finance/info?q=NASDAQ:GOOG
    http://www.google.com/finance/info?q=CURRENCY:GBPUSD
    http://finance.google.com/finance/info?client=ig&q=AAPL,YHOO
    https://www.google.com/finance/getchart?q=YELP
    http://www.google.com/finance/getchart?q=AAPL&p=12H&i=240
    https://www.google.com/finance/chart?tlf=12&q=aapl
    http://www.google.com/finance/historical?q=CSCO&output=csv
    http://www.google.com/finance/info?infotype=infoquoteall&q=C,JPM,AIG
    http://www.google.com/finance/getprices?q=000001&x=SHA&i=86400&p=6M&f=d,c,v,o,h,l&df=cpct&auto=1&ts=1259212443409
    https://www.google.com/finance/company_news?q=AAPL&gl=cn&output=rss
    https://www.google.com.hk/finance?q=SHE%3A000002&gl=cn&&output=json
    
    for ticker related news:
    http://www.google.com/finance/company_news?q=AAPL&output=rss
    financial: http://www.google.com/finance?q=CSCO&fstype=ii#
    related company: http://www.google.com/finance/related?q=csco#

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
     000001.SS上证指数    399001.SZ深证指数
    <--Yahoo finance available api for shanghai stocks-->
    http://finance.yahoo.com/webservice/v1/symbols/600018.ss,000725.sz/quote?format=json
    http://finance.yahoo.com/webservice/v1/symbols/000725.SZ/quote?format=json&view=detail
    
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
    
    include('getQuote_from_google.php');
    //include('getQuote_from_yahoo.php');

?>
<?
    include('helper.php')

    function getHistoricaldata($symbol, $startDate, $endDate)
    {
        $symbol=appendSuffix($symbol);
        /*$d=strtotime("-3 Months");
        $startDate=date("Y-m-d", $d);
        $endDate=date("Y-m-d");*/
        $BASE_URL = "http://query.yahooapis.com/v1/public/yql";
        $yql_query = "select * from yahoo.finance.historicaldata where symbol = '{$symbol}' and startDate = '{$startDate}' and endDate = '{$endDate}'";
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

    }
    //getHistoricaldata();
    
?>
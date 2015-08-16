<?  
    function getXchange($type="USDCNY")
    {
        
    
        $BASE_URL = "http://query.yahooapis.com/v1/public/yql";
        $yql_query = "select * from yahoo.finance.xchange where pair in ('{$type}')";
        $yql_query_url = $BASE_URL . "?q=" . urlencode($yql_query) . "&env=http://datatables.org/alltables.env"."&format=json";
        // Make call with cURL
        $session = curl_init($yql_query_url);
        curl_setopt($session, CURLOPT_RETURNTRANSFER,true);
        $json = curl_exec($session);
        // Convert JSON to PHP object
        $phpObj =  json_decode($json);
        
        if(!is_null($phpObj->query->results->rate->Rate))
        {  
            return $phpObj->query->results->rate->Rate;
        }
        else
        {
            return false;
        }
    }
   
?>
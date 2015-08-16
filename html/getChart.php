<?
    //google chart query formatting
    function googleChartFormat($symbol)
    {
        $reg = '/(\d)(\d{5})/';
        $result = preg_match($reg, $symbol, $array);
        if($result !== 0 && stripos($symbol, ".SS") === false && stripos($symbol, ".SZ") === false)
        {
            
            if($array[1] === "6" || $array[1] === "9")
            {
                return $symbol.".SS";
            }
            elseif($symbol !== "000001" || $symbol !== "399001" || $array[1] === "0" || $array[1] === "2" || $array[1] === "3") 
            {
                return $symbol.".SZ";
            }
            else
            {
                return $symbol;
            }
        
            
        }
        else
        {
            return strtoupper($symbol);
        }
    }
    
    //yahoo chart query formatting
    function yahooChartFormat($symbol)
    {
        $reg = '/(\d)(\d{5})/';
        $result = preg_match($reg, $symbol, $array);
        if($result !== 0 && stripos($symbol, ".SS") === false && stripos($symbol, ".SZ") === false)
        {
            
            if($symbol === "000001" || $array[1] === "6" || $array[1] === "9")
            {
                return $symbol.".SS";
            }
            elseif($array[1] === "0" || $array[1] === "2" || $array[1] === "3") 
            {
                return $symbol.".SZ";
            }
            else
            {
                return $symbol;
            }
        
            
        }
        else
        {
            return strtoupper($symbol);
        }
    }

    
    $period = urlencode($_GET["period"]);
    $interval = urlencode($_GET["interval"]);
    $type = urlencode($_GET["type"]);
    
    if(isset($_GET["source"]))
    {   
        if($_GET["source"] === "google")
        {
            $symbol = urlencode(googleChartFormat($_GET["symbol"]));
            $img = file_get_contents("http://www.google.com/finance/getchart?q={$symbol}&p={$period}&i={$interval}");
        }
        elseif($_GET["source"] === "yahoo")
        {
            $symbol = urlencode(yahooChartFormat($_GET["symbol"]));
            $img = file_get_contents("http://chart.finance.yahoo.com/z?s={$symbol}&t={$period}&q={$type}");

        }
        else 
        {
            $img = "";
        }
        
        //设定文件头 
        addslashes($img);
        header('Content-type: image/png');
        echo $img;
    }

?>


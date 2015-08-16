<?
    function getChart($symbol, $source="google", $period="1d", $type="l", $interval="240")
    {
        $host = $_SERVER["HTTP_HOST"];
        $path = rtrim(dirname($_SERVER["PHP_SELF"]), "/\\");
        $symbol = urlencode($symbol);
       
        return "https://$host$path/getChart.php?symbol=$symbol&source=$source&period=$period&type=$type&interval=$interval";

    }
?>
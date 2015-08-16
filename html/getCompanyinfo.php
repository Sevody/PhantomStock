<?


    include('helper.php');
    header("Content-type:text/html;charset=utf-8");
    
    $symbol = appendPrefix("FB");//$_GET["symbol"]);

    $url = "https://www.google.com/finance?q={$symbol}&gl=cn&output=json";
    $session = curl_init($url);
    curl_setopt($session, CURLOPT_RETURNTRANSFER,true);
    $raw_data = curl_exec($session);
    
    //$raw_data = file_get_contents("https://www.google.com/finance?q={$symbol}&gl=cn&output=json");
    
    //Formatting 返回的伪Json字符串
    $json = str_replace("\n", "", $raw_data);
    $json = substr($json, 4, strlen($json)-5);
    $data = str_ireplace(array('\x3c', '\x3e', '\x26', '\x2f', '\x3b'), array("<", ">", "&", "/", ";"), $json);
 
    $json_data = json_decode($data);
    
    echo $json_data->summary[0]->overview;
    echo "<pre>";
    print_r($json_data);
    echo "</pre>";




?>
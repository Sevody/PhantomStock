<?

    
    function quoteList($symbol_list, $full_quote = false)
    {
        
        $client_str = $symbol_list;//'{ "symbolArray":["aapl","600018", "000725", "002068"] }';
        $client_json = json_decode($client_str);
        
        foreach($client_json->symbolArray as $symbol)
        {
            $s .= $symbol.","; 
        }
        
        $str_symbol = urlencode(substr($s, 0, strlen($s)-1));
        
        //$quote = file_get_contents("http://finance.google.com/finance/info?client=ig&q={$str_symbol}");
        $quote = file_get_contents("http://www.google.com/finance/info?infotype=infoquoteall&q={$str_symbol}");
    
        //Remove CR's from ouput - make it one line
        $json = str_replace("\n", "", $quote);
        
        //Remove // to build qualified string  
        $data = substr($json, 3, strlen($json) -3);
        
        //decode JSON data
        $json_output = json_decode(utf8_encode($data));
    
        return $json_output;
        // get the last price
        //$last_trade = number_format($json_output[0]->l, 2);
        //$Chg = number_format($json_output[0]->cp, 2);
        //Output Stock price .
        
        
    }  
?>

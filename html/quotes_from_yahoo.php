<?

    include('helper.php');
    
    function quoteList($symbol_list, $list_quote = true)
    {
        //单个symbol查询
        if($list_quote === false)
        {
            $reg = '/(\d)(\d{5})/';
            $result = preg_match($reg, $symbol_list, $array);
            if($result !== 0 && stripos($symbol_list, ".SS") === false && stripos($symbol_list, ".SZ") === false)
            {
                //添加SS,SZ后缀
                if($array[1] === "6" || $array[1] === "9")
                {
                    $str_symbol = $symbol_list."SS";
                }
                elseif($array[1] === "0" || $array[1] === "2" || $array[1] === "3") 
                {
                    $str_symbol = $symbol_list."SZ";
                }
                else
                {
                    $str_symbol = $symbol_list;
                }
            
                
            }
            else
            {
                $str_symbol = $symbol_list;
            }
        }
        //复数symbol查询
        else
        {
            //$client_str = '{ "symbolArray":["fb", "aapl","600018.SS", "000725.SZ", "002068.SZ", "000002.SZ"] }';
            $client_str = $symbol_list;
            $client_json = json_decode($client_str);
            
            //构造查询String
            foreach($client_json->symbolArray as $symbol)
            {
                $s .= $symbol.","; 
            }
            
            //去除多余的","
            $str_symbol = urlencode(substr($s, 0, strlen($s)-1));
        }    
        $data = file_get_contents("http://finance.yahoo.com/webservice/v1/symbols/{$str_symbol}/quote?format=json&view=detail");

        
        //decode JSON data
        $json_quotes = json_decode(utf8_encode($data));
        
        
        //把需要的信息放到stockList中用以初始化
        for($i = 0; $i < $json_quotes->list->meta->count; $i++)
        {
            $stockList[$i]["symbol"] = getSimpleSymbol($json_quotes->list->resources[$i]->resource->fields->symbol);
            $stockList[$i]["name"] = getStockName($stockList[$i]["symbol"],
                                                  $json_quotes->list->resources[$i]->resource->fields->name);
            $stockList[$i]["last_trade"] = number_format($json_quotes->list->resources[$i]->resource->fields->price, 2);
            $stockList[$i]["chg"] = plusPlus($json_quotes->list->resources[$i]->resource->fields->chg_percent)."%";
            $stockList[$i]["change"] = plusPlus(number_format($json_quotes->list->resources[$i]->resource->fields->change, 2));
            $stockList[$i]["volume"] = getVolume_fromInt($json_quotes->list->resources[$i]->resource->fields->volume);
            $stockList[$i]["turnover"] = "-";
            $stockList[$i]["pe"] = "-";
            $stockList[$i]["open"] = "-";
            $stockList[$i]["close"] = getClose($stockList[$i]["last_trade"],
                                               number_format($json_quotes->list->resources[$i]->resource->fields->change, 2));
            $stockList[$i]["high"] = number_format($json_quotes->list->resources[$i]->resource->fields->day_high, 2);
            $stockList[$i]["low"] = number_format($json_quotes->list->resources[$i]->resource->fields->day_low, 2);
           
        }
        
        return $stockList;
       
        
    }  
    /*print "<pre>";
    print_r(quoteList("a"));
    print "</pre>";*/
?>

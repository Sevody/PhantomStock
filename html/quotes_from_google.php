<?
    include('helper.php');
    
    function quoteList($symbol_list, $list_quote = true)
    {
        //单个symbol查询
        if($list_quote === false)
        {
            $str_symbol = $symbol_list;
        }
        //复数symbol查询
        else
        {
            //'{ "symbolArray":["aapl","600018.SS", "000725.SZ", "002068.SZ"] }';
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
        
        $quote = file_get_contents("http://www.google.com/finance/info?infotype=infoquoteall&q={$str_symbol}");
    
        //Remove CR's from ouput - make it one line
        $json = str_replace("\n", "", $quote);
        
        //Remove // to build qualified string  
        $data = substr($json, 3, strlen($json) -3);
        
        //decode JSON data
        $json_quotes = json_decode(utf8_encode($data));
        
        //把需要的信息放到stockList中用以初始化
        for($i = 0; $i < count($json_quotes); $i++)
        {
            $stockList[$i]["symbol"] = $json_quotes[$i]->t;
            $stockList[$i]["name"] = getStockName($json_quotes[$i]->t, $json_quotes[$i]->name);
            $stockList[$i]["last_trade"] = $json_quotes[$i]->l;
            $stockList[$i]["chg"] = plusPlus($json_quotes[$i]->cp)."%";
            $stockList[$i]["change"] = $json_quotes[$i]->c;
            $stockList[$i]["volume"] = transformUnit_toChs($json_quotes[$i]->vo);
            $stockList[$i]["turnover"] = getTurnover($json_quotes[$i]->vo, $json_quotes[$i]->shares);
            $stockList[$i]["pe"] = $json_quotes[$i]->pe;
            $stockList[$i]["open"] = $json_quotes[$i]->op;
            $stockList[$i]["close"] = $json_quotes[$i]->pcls_fix;
            $stockList[$i]["high"] = $json_quotes[$i]->hi;
            $stockList[$i]["low"] = $json_quotes[$i]->lo;
        }
        
        return $stockList;
        
    }  
?>

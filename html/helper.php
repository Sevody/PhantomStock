<?

    
    //把M,B单位转换成Int volume单位是手用1,shares用2,3
    function transformUnit_toInt1($str_num)
    {
        $unit = substr($str_num, -1);
        switch ($unit)
        {
            case "M" : return substr($str_num, 0, strlen($str_num)-1)*10000;
                        
            case "B" : return substr($str_num, 0, strlen($str_num)-1)*10000000;

            default :  return $str_num;            
        }
    }
    function transformUnit_toInt2($str_num)
    {
        $unit = substr($str_num, -1);
        switch ($unit)
        {
            case "M" : return substr($str_num, 0, strlen($str_num)-1)*1000000;
                        
            case "B" : return substr($str_num, 0, strlen($str_num)-1)*1000000000;
                        
            default :  return $str_num;            
        }
    }
    function transformUnit_toInt3($str_num)
    {
        $unit = substr($str_num, -1);
        switch ($unit)
        {
            case "M" : return substr($str_num, 0, strlen($str_num)-1)*10000000;
                        
            case "B" : return substr($str_num, 0, strlen($str_num)-1)*1000000000;
                        
            default :  return $str_num;            
        }
    }
    
    //把M,B单位转换成万,亿 用于volume
    function transformUnit_toChs($str_num)
    {
        if($str_num === "-") return $str_num;
        $unit = substr($str_num, -1);
        switch ($unit)
        {
            case "M" : return (number_format((substr($str_num, 0, strlen($str_num)-1)), 1, ".", ""))."万";
                        
            case "B" : return (number_format((substr($str_num, 0, strlen($str_num)-1)*1000), 1, ".", ""))."万";
                        
            default :  return (int)$str_num;            
        }
    }
    
    //为正数前面添加"+",负数则返回原字符串
    function plusPlus($str_num)
    {
        $str_num = number_format($str_num, 2);
        if($str_num > 0)
        {
            return "+".$str_num;
        }
        else
        {
            return $str_num;
        }
    }
    
    //通过总手和流通股本计算换手率turnover
    function getTurnover($str_volume, $str_shares, $symbol)
    {   
        if($str_volume != null && $str_shares != null && $str_volume != "-")
        {
            $volume = transformUnit_toInt1($str_volume);
            $shares = transformUnit_toInt2($str_shares);
            //换手率＝(成交总手数×100÷流通股本)*100%
            $turn_over = number_format(($volume*100/$shares)*100, 2);
            return $turn_over."%";
        }
        else
        {
            //美股
            $data = file_get_contents("http://finance.yahoo.com/webservice/v1/symbols/{$symbol}/quote?format=json&view=detail");
            
            //decode JSON data
            $json_quotes = json_decode(utf8_encode($data));
            $volume = $json_quotes->list->resources[0]->resource->fields->volume;
            if($volume != null && $str_shares != null && $volume != "-")
            {
                $volume = $volume/100;
                $shares = transformUnit_toInt3($str_shares);
                //换手率＝(成交总手数×100÷流通股本)*100%
                $turn_over = number_format(($volume*100/$shares)*100, 2);
                return $turn_over."%";
            }
            else return "-";
        }
        
    }
    
    //根据股票代码从数据库空获取股票名称symbol
    function getStockName($stock_symbol, $default)
    {
        
        //中国A股
        if(strlen($stock_symbol) === 6 )
        {
            $reg =  '/(\d)(\d{5})/';
            if(preg_match($reg, $stock_symbol))
            {
                //连接数据库
                include('conn.php');

                $passwordhash_statement = $db->prepare('SELECT name FROM stocks 
                                                        WHERE symbol=:symbol limit 1');
                $passwordhash_statement->bindValue(':symbol', $stock_symbol, PDO::PARAM_STR);
                $passwordhash_statement->execute();
                $result = $passwordhash_statement->fetchAll();
                
                //关闭连接
                $db = null;
                
                if ($result && isset($result[0]['name'])) 
                {
                    
                    return $result[0]['name'];
                    
                }
            }
        }
        
        //数据库中找不到记录,返回默认值
        return $default;
    }
    
    //去掉SS,SZ后缀
    function getSimpleSymbol($stock_symbol)
    {
        $stock_symbol = str_replace(".SS", "", $stock_symbol);
        $stock_symbol = str_replace(".SZ", "", $stock_symbol);
        return $stock_symbol;
    }
    
    //把Int转换成万,亿/手
    function getVolume_fromInt($volume)
    {
        if($volume > 10000000000)
        {
            return number_format(($volume/10000000000), 1)."亿";
        }
        elseif($volume > 1000000)
        {
            return number_format(($volume/1000000), 1)."万";
        }
        else
        {
            return $volume;
        }
    }
    
    //根据最新报价和涨跌计算昨收
    function getClose($price, $change)
    {
        return floatval($price)-floatval($change);
    }

    //添加.SS,.SZ后缀for Yahoo Finance
    function appendSuffix($symbol)
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
            return $symbol;
        }
    
    }
    
    //添加SHA:,SHE:前缀for Google Finance
    function appendPrefix($symbol)
    {
        $reg = '/(\d)(\d{5})/';
        $result = preg_match($reg, $symbol, $array);
        if($result !== 0 && stripos($symbol, ".SS") === false && stripos($symbol, ".SZ") === false)
        {
            
            if($symbol === "000001" || $array[1] === "6" || $array[1] === "9")
            {
                return "SHA:".$symbol;
            }
            elseif($array[1] === "0" || $array[1] === "2" || $array[1] === "3") 
            {
                return "SHE:".$symbol;
            }
            else
            {
                return $symbol;
            }
        
            
        }
        else
        {
            return $symbol;
        }
    }
    
    function getVolume($volume, $symbol)
    {
        $reg = '/(\d)(\d{5})/';
        $result = preg_match($reg, $symbol);
        if($result !== 0)
        {
            return transformUnit_toChs($volume);
        }
        else
        {
            $data = file_get_contents("http://finance.yahoo.com/webservice/v1/symbols/{$symbol}/quote?format=json&view=detail");
            
            //decode JSON data
            $json_quotes = json_decode(utf8_encode($data));
            return getVolume_fromInt($json_quotes->list->resources[0]->resource->fields->volume);
        }
        
    }
    
?>
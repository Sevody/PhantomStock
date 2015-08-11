<!--Home Page -->

<?
    //连接数据库
    include('conn.php');
    
    //检查是否已登陆
    include('check_log.php');
    
    //通过google finance获取股票信息
    include('quotes.php');
    
    
    //把M,B单位转换成Int volume单位是手用1,shares用2
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
    
    //把M,B单位转换成万,亿 用于volume
    function transformUnit_toChs($str_num)
    {
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
        if(((int)$str_num) > 0)
        {
            return "+".$str_num;
        }
        else
        {
            return $str_num;
        }
    }
    
    //通过总手和流通股本计算换手率
    function getTurnover($str_volume, $str_shares)
    {   
        if($str_volume != null && $str_shares != null)
        {
            $volume = transformUnit_toInt1($str_volume);
            $shares = transformUnit_toInt2($str_shares);
            //换手率＝(成交总手数×100÷流通股本)*100%
            $turn_over = number_format(($volume*100/$shares)*100, 2);
            return $turn_over."%";
        }
    }
    
    //根据股票代码从数据库空获取股票名称
    function getStockName($stock_symbol, $default)
    {
        //中国A股
        if(strlen($stock_symbol) === 6 )
        {
            $reg =  '/(\d)(\d{5})/';
            if(preg_match($reg, $stock_symbol))
            {
                $sql = sprintf("SELECT name from stocks WHERE symbol='%s' limit 1",
                                mysql_real_escape_string($stock_symbol));
                //解决中文乱码
                mysql_query("SET NAMES 'utf8'");
                $result = mysql_query($sql);
               
                if ($result)
                {
                    $row = mysql_fetch_row($result);
                    if (isset($row[0]))
                    {   
                        return $row[0];
                    }
                }
            }
        }
        
        //数据库中找不到记录,返回默认值
        return $default;
    }
    
    
    
    $stock_symbols = '{ "symbolArray":["fb", "aapl","600018.SS", "000725.SZ", "002068.SZ", "000002.SZ"] }';
    
    //获取股票列表的最新报价
    $stockList = quoteList($stock_symbols, true);
    
    
    //把需要的信息放到stockArray中
    for($i = 0; $i < count($stockList); $i++)
    {
        $stockArray[$i]["symbol"] = $stockList[$i]->t;
        $stockArray[$i]["name"] = getStockName($stockList[$i]->t, $stockList[$i]->name);
        $stockArray[$i]["last_trade"] = $stockList[$i]->l;
        $stockArray[$i]["chg"] = plusPlus($stockList[$i]->cp)."%";
        $stockArray[$i]["change"] = $stockList[$i]->c;
        $stockArray[$i]["volume"] = transformUnit_toChs($stockList[$i]->vo);
        $stockArray[$i]["turnover"] = getTurnover($stockList[$i]->vo, $stockList[$i]->shares);
        $stockArray[$i]["pe"] = $stockList[$i]->pe;
        $stockArray[$i]["open"] = $stockList[$i]->op;
        $stockArray[$i]["close"] = $stockList[$i]->pcls_fix;
        $stockArray[$i]["high"] = $stockList[$i]->hi;
        $stockArray[$i]["low"] = $stockList[$i]->lo;
    }
    
   /*print "<pre>";
   print_r($stockList);
   print "</pre>";*/
?>
<!DOCTYPE html>

<html>
    <head>
        <title>Phantom Stock</title>
        <meta charset="utf-8">
        
        
        <link rel="stylesheet" href="header.css" type="text/css" />
        
        <style type="text/css">
            
            .container{
                width:1000px;
                margin:30px auto;
            }
            .quote{
                width:300px;
                position:relative;
                left:25px;
            }
            .stocks-board{
                width:1000px;
                margin:70px auto;
            }
            .stocks-table{
                width:1000px;
                
            }
            th{
                padding:5px 15px 5px 0px;
            }
            td{
                padding:5px 15px 5px 14px;
            }
        </style>
        
        <script type="text/javascript">
            /**********************HELPER**********************************************************************************************/
            /********************************************************************************************************************/
             //把M,B单位转换成Int volume用1,shares用2
            function transformUnit_toInt1(str_num)
            {
                var unit = str_num.substr(-1);
                switch (unit)
                {
                    case "M" : return str_num.substr(0, str_num.length-1)*10000;
                    case "B" : return str_num.substr(0, str_num.length-1)*10000000;
                    default :  return str_num;            
                }
            }
            function transformUnit_toInt2(str_num)
            {
                var unit = str_num.substr(-1);
                switch (unit)
                {
                    case "M" : return str_num.substr(0, str_num.length-1)*1000000;
                    case "B" : return str_num.substr(0, str_num.length-1)*1000000000;
                    default :  return str_num;            
                }
            }
            
            //把M,B单位转换成万,亿 用于volume
            function transformUnit_toChs(str_num)
            {   
                var unit = str_num.substr(-1);
                switch (unit)
                {
                    case "M" : return (number_format((str_num.substr(0, str_num.length-1)), 1, ".", ""))+"万";
    
                    case "B" : return (number_format((str_num.substr(0, str_num.length-1)*1000), 1, ".", ""))+"万";
    
                    default :  return str_num;            
                }
            }
            
            //为正数前面添加"+",负数则返回原字符串
            function plusPlus(str_num)
            {
                if((parseInt(str_num)) > 0)
                {
                    return "+"+str_num;
                }
                else
                {
                    return str_num;
                }
            }
            
            //通过总手和流通股本计算换手率
            function getTurnover(str_volume, str_shares)
            {   
                if(str_volume != null && str_shares != null)
                {
                    var volume = transformUnit_toInt1(str_volume);
                    var shares = transformUnit_toInt2(str_shares);
                    //换手率＝(成交总手数×100÷流通股本)*100%
                    var turn_over = number_format((volume*100/shares)*100, 2);
                    return turn_over+"%";
                }
            }
            
            //from internet 
            function number_format(number, decimals, dec_point, thousands_sep) {  
                var n = !isFinite(+number) ? 0 : +number,  
                    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),  
                    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,  
                    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,  
                    s = '',  
                    toFixedFix = function (n, prec) {  
                        var k = Math.pow(10, prec);  
                        return '' + Math.round(n * k) / k;        };  
                // Fix for IE parseFloat(0.55).toFixed(0) = 0;  
                s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');  
                if (s[0].length > 3) {  
                    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);      
                }  
                if ((s[1] || '').length < prec) {  
                    s[1] = s[1] || '';  
                    s[1] += new Array(prec - s[1].length + 1).join('0');  
                }    return s.join(dec);  
            }  
            /********************************************************************************************************************/
            /********************************************************************************************************************/
            
            var stock_symbols = '{ "symbolArray":["fb", "aapl","600018.SS", "000725.SZ", "002068.SZ", "000002.SZ"] }';
            //每5秒发送一次ajax请求,取回最新报价
            var int=setInterval(refreshQuotes, 5000);
            var xmlhttp;
            function refreshQuotes()
            {
               
                if (window.XMLHttpRequest)
                {
                    // code for IE7+, Firefox, Chrome, Opera, Safari
                    xmlhttp=new XMLHttpRequest();
                }
                //不支持IE6
                if (xmlhttp !=null)
                {
                    xmlhttp.onreadystatechange=state_Change;
                    
                    xmlhttp.open("GET","quotes.php?symbol_list="+stock_symbols+"&t="+Math.random(),true);
                    xmlhttp.send();
                    
                }
            }
            
            function state_Change()
                {
                    if (xmlhttp.readyState==4 && xmlhttp.status==200)
                    {
                        var json_quotes = JSON.parse(xmlhttp.responseText);
                        for(var i in json_quotes)
                        {
                            //通过element的id更新报价
                            var last_trade_id = json_quotes[i].t+"-"+"last_trade";
                            var chg_id = json_quotes[i].t+"-"+"chg";
                            var change_id = json_quotes[i].t+"-"+"change";
                            var volume_id = json_quotes[i].t+"-"+"volume";
                            var turnover_id = json_quotes[i].t+"-"+"turnover";
                            var pe_id = json_quotes[i].t+"-"+"pe";
                            var open_id = json_quotes[i].t+"-"+"open";
                            var close_id = json_quotes[i].t+"-"+"close";
                            var high_id = json_quotes[i].t+"-"+"high";
                            var low_id = json_quotes[i].t+"-"+"low";
                            document.getElementById(last_trade_id).innerHTML=json_quotes[i].l;
                            document.getElementById(chg_id).innerHTML=plusPlus(json_quotes[i].cp)+"%";
                            document.getElementById(change_id).innerHTML=json_quotes[i].c;
                            document.getElementById(volume_id).innerHTML=transformUnit_toChs(json_quotes[i].vo);
                            document.getElementById(turnover_id).innerHTML=getTurnover(json_quotes[i].vo, json_quotes[i].shares);
                            document.getElementById(open_id).innerHTML=json_quotes[i].op;
                            document.getElementById(close_id).innerHTML=json_quotes[i].pcls_fix;
                            document.getElementById(high_id).innerHTML=json_quotes[i].hi;
                            document.getElementById(low_id).innerHTML=json_quotes[i].lo+(Math.random()).toFixed(2);
                        }
                    }
                }
        </script>
    </head>
    
    <body>

        <? include('header.php'); ?>
        
        <div class="container">
            <div class="quote">
                <form action="quote.php" method="get">
                    <input type="text" name="symbol"/>
                    <input type="submit" value="Submit"/>
                </form>
            </div>
            <div class="stocks-board">
                <table class="stocks-table">
                    <tr>
                        <th>代码</th>
                        <th>名称</th>
                        <th>最新</th>
                        <th>涨幅</th>
                        <th>涨跌</th>
                        <th>总手</th>
                        <th>换手</th>
                        <th>市盈率</th>
                        <th>开盘</th>
                        <th>昨收</th>
                        <th>最高</th>
                        <th>最低</th>
                    </tr>
                    <?  
                        foreach($stockArray as $stock)
                        {
                            echo "<tr id=".$stock["symbol"].">";
                                foreach($stock as $key=>$item)
                                {
                                    echo "<td id=".$stock["symbol"]."-".$key.">";
                                    echo $item;
                                    echo "</td>";
                                }
                            echo "</tr>";
                        }    
                    
                    
                    ?>
                    <!--<tr>
                        <td>000725</td>
                        <td>京东方A</td>
                        <td>3.64</td>
                        <td>-1.36%</td>
                        <td>-0.05</td>
                        <td>397万</td>
                        <td>1.70%</td>
                        <td>32.83</td>
                        <td>3.62</td>
                        <td>3.69</td>
                        <td>3.69</td>
                        <td>3.61</td>
                    </tr>
                    <tr>
                        <td>600018</td>
                        <td>上港集团</td>
                        <td>7.28</td>
                        <td>-0.14%</td>
                        <td>-0.01</td>
                        <td>45.3万</td>
                        <td>0.20%</td>
                        <td>28.03</td>
                        <td>7.14</td>
                        <td>7.29</td>
                        <td>7.46</td>
                        <td>7.13</td>
                    </tr>
                    <tr>
                        <td>000002</td>
                        <td>万科A</td>
                        <td>14.34</td>
                        <td>-0.76%</td>
                        <td>-0.11</td>
                        <td>51万</td>
                        <td>0.52%</td>
                        <td>60.91</td>
                        <td>14.26</td>
                        <td>14.45</td>
                        <td>14.63</td>
                        <td>14.14</td>
                    </tr>-->
                </table>
            </div>
        </div>
        
        <div class="footer"></div>
        
    </body>
    
    
    
    
</html>
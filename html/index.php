<!--Home Page -->

<?
    
    
    //检查是否已登陆
    include('check_logged.php');
    
    //通过信息源获取股票信息
    include('quotes_source.php');
    
    
    
    $stock_symbols = '{ "symbolArray":["BIDU", "AAPL","600018", "000725", "002068", "000002"] }';
    $index_symbols = '{ "symbolArray":["000001","399001"] }';
    //获取股票列表的最新报价
    $stockList = quoteList($stock_symbols);
    $composite_index = quoteList($index_symbols);
    
    
    
    
  
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
            .composite-index{
                float:right;
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
            /* //把M,B单位转换成Int volume用1,shares用2
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
                if(str_num > 0)
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
            */
            
            var stock_symbols = '{ "symbolArray":["000001","399001","bidu", "aapl","600018", "000725", "002068", "000002"] }';
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
            //更新报价
            function state_Change()
                {
                    if (xmlhttp.readyState==4 && xmlhttp.status==200)
                    {
                        var json_quotes = JSON.parse(xmlhttp.responseText);
                        for(var i in json_quotes)
                        {
                            
                            //通过element的id更新报价等信息
                            var last_trade_id = json_quotes[i]["symbol"]+"-"+"last_trade";
                            var chg_id = json_quotes[i]["symbol"]+"-"+"chg";
                            var change_id = json_quotes[i]["symbol"]+"-"+"change";
                            var volume_id = json_quotes[i]["symbol"]+"-"+"volume";
                            var turnover_id = json_quotes[i]["symbol"]+"-"+"turnover";
                            var pe_id = json_quotes[i]["symbol"]+"-"+"pe";
                            var open_id = json_quotes[i]["symbol"]+"-"+"open";
                            var close_id = json_quotes[i]["symbol"]+"-"+"close";
                            var high_id = json_quotes[i]["symbol"]+"-"+"high";
                            var low_id = json_quotes[i]["symbol"]+"-"+"low";
                            document.getElementById(last_trade_id).innerHTML=json_quotes[i]["last_trade"];
                            document.getElementById(chg_id).innerHTML=json_quotes[i]["chg"];
                            document.getElementById(change_id).innerHTML=json_quotes[i]["change"];
                            //跳过上证和深证指数的其他项
                            if(json_quotes[i]["symbol"] === "000001" || json_quotes[i]["symbol"] === "399001")
                                continue;
                            document.getElementById(volume_id).innerHTML=json_quotes[i]["volume"];
                            document.getElementById(turnover_id).innerHTML=json_quotes[i]["turnover"];
                            document.getElementById(open_id).innerHTML=json_quotes[i]["open"];
                            document.getElementById(close_id).innerHTML=json_quotes[i]["close"];
                            document.getElementById(high_id).innerHTML=json_quotes[i]["high"];
                            document.getElementById(low_id).innerHTML=json_quotes[i]["low"];//+(Math.random()).toFixed(2);
                        }
                    }
                }
        </script>
    </head>
    
    <body>
        <div id="test"></div>

        <? include('header.php'); ?>
        
        <div class="container">
            <div class="quote">
                <form action="quote.php" method="get">
                    <input type="text" name="symbol"/>
                    <input type="submit" value="Submit"/>
                </form>
            </div>
            <div class="composite-index">
                <div class="SHA">
                    <span class="index">上证指数</span>
                    <span id="000001-last_trade"><?= $composite_index[0]["last_trade"] ?></span>
                    <span id="000001-change"><?= $composite_index[0]["change"] ?></span>
                    <span id="000001-chg"><?= $composite_index[0]["chg"] ?></span>
                </div>
                <div class="SHE">
                    <span id="index">深证指数</span>
                    <span id="399001-last_trade"><?= $composite_index[1]["last_trade"] ?></span>
                    <span id="399001-change"><?= $composite_index[1]["change"] ?></span>
                    <span id="399001-chg"><?= $composite_index[1]["chg"] ?></span>
                </div>
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
                        <th>市盈</th>
                        <th>开盘</th>
                        <th>昨收</th>
                        <th>最高</th>
                        <th>最低</th>
                    </tr>
                    <?  
                        foreach($stockList as $stock)
                        {
                            //根据symbol来记录element ID
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
                    <!--
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
                    -->
                </table>
            </div>
        </div>
        
        <div class="footer"></div>
        
    </body>
    
    
    
    
</html>
<?

    include('helper.php');
    header("Content-type:text/html;charset=utf-8");
    
    $symbol = appendPrefix("FB");//$_GET["symbol"]);

    $dom = simplexml_load_file("https://www.google.com/finance/company_news?q={$symbol}&gl=cn&output=rss");
    
    foreach ($dom->channel->item as $item)
         {
             $time = strtotime($item->pubDate);
             $date = date("Y-m-d", $time);
             print "<li>";
             print "<a href='{$item->link}'>";
             print $item->title;
             print "</a>";
             print " (" . $date . ")";
             print "</li>";
         }
         

    
?>

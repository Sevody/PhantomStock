<?

    //通过google finance 获取报价
    include('quotes_from_google.php');
    
    if(isset($_GET["symbol_list"]))
    {
        echo json_encode(quoteList($_GET["symbol_list"]));
    }
?>

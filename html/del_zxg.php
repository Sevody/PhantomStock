<?
    require('check_logged.php');
    
        
    if ($logged === true)
    {
        if (isset($_COOKIE["identity"]) && isset($_GET["symbol"]))
        {
            //要移除的自选股
            $symbol = $_GET["symbol"];
            
            //从登陆cookie中取出uid值
            $sign = $_COOKIE["identity"];
            $data =base64_decode($sign);
            $data_array = explode("\t", $data);
            $uid = (int)$data_array[0];
            
            require('conn.php');
            
            $delete_zxg = $db->prepare('DELETE FROM zxg
                                         WHERE uid=:uid AND symbol=:symbol');
            $delete_zxg->bindValue(':uid', $uid, PDO::PARAM_INT);
            $delete_zxg->bindValue(':symbol', $symbol, PDO::PARAM_STR);
            $delete_zxg->execute();
            //关闭连接
            $db = null;
            
            header("Content-type:text/html;charset=utf-8");
            echo "移除成功";
        
        }
    }
    else
    {
        header("Content-type:text/html;charset=utf-8");
        echo "请先登录";
    }

?>
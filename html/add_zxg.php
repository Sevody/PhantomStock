<?
    require('check_logged.php');
    
        
    if ($logged === true)
    {
        if (isset($_COOKIE["identity"]) && isset($_GET["symbol"]))
        {
            //要添加的自选股
            $symbol = $_GET["symbol"];
            
            //从登陆cookie中取出uid值
            $sign = $_COOKIE["identity"];
            $data =base64_decode($sign);
            $data_array = explode("\t", $data);
            $uid = (int)$data_array[0];
            
            require('conn.php');
            
            //检查股票是否已添加
            $check_symbol = $db->prepare('SELECT 1 FROM zxg 
                                            WHERE uid=:uid AND symbol=:symbol');
            $check_symbol->bindValue(':uid', $uid, PDO::PARAM_INT);
            $check_symbol->bindValue(':symbol', $symbol, PDO::PARAM_STR);
            $check_symbol->execute();
            
            $result = $check_symbol->fetchAll();
            
            if($result)
            {
                header("Content-type:text/html;charset=utf-8");
                echo "股票已添加";
                exit;
            }
            
            $insert_zxg = $db->prepare('INSERT INTO zxg (uid, symbol) 
                                         VALUES (:uid, :symbol)');
            $insert_zxg->bindValue(':uid', $uid, PDO::PARAM_INT);
            $insert_zxg->bindValue(':symbol', $symbol, PDO::PARAM_STR);
            $insert_zxg->execute();
            //关闭连接
            $db = null;
            
            header("Content-type:text/html;charset=utf-8");
            echo "添加成功";
        
        }
    }
    else
    {
        header("Content-type:text/html;charset=utf-8");
        echo "请先登录";
    }

?>
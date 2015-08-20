<?
    require('check_logged.php');

    if ($logged === true)
    {
        if (isset($_COOKIE["identity"]) && isset($_GET["symbol"]) && isset($_GET["quantity"]))
        {
            
            $symbol = $_GET["symbol"];
            $quantity = (int)$_GET["quantity"];
            
            //从登陆cookie中取出uid值
            $sign = $_COOKIE["identity"];
            $data =base64_decode($sign);
            $data_array = explode("\t", $data);
            $uid = (int)$data_array[0];
            //获取股票最新价格
            require('quotes_source.php');
            $stock_info = quoteList($symbol, false);
            $price = (float)$stock_info[0]["last_trade"];
            //返回价格正确
            if($price > 0)
            {
            
                require('conn.php');
                
                
                $get_cash = $db->prepare('SELECT cash FROM users 
                                                WHERE uid=:uid limit 1');
                $get_cash->bindValue(':uid', $uid, PDO::PARAM_INT);
                $get_cash->execute();
                
                $result = $get_cash->fetchAll();
                
                $cash = $result[0]['cash'];
                
                $balance = $cash - ($price * $quantity);
                
                //是否有足够现金购买
                if($balance < 0)
                {
                    header("Content-type:text/html;charset=utf-8");
                    echo "现金不足,无法购买";
                    exit;
                }
                
                //购买条件足够,写入数据
                try
                {
                    //开启事务处理
                    $db->beginTransaction();
                    //写入order表
                    $insert_order = $db->prepare('INSERT INTO orders (uid, symbol, quantity, action, price) 
                                                 VALUES (:uid, :symbol, :quantity, :action, :price)');
                    $insert_order->execute(array(
                                                ':uid'=>$uid,
                                                ':symbol'=>$symbol,
                                                ':quantity'=>$quantity,
                                                ':action'=>'bid',
                                                ':price'=>$price
                                                ));
                                                
                    //更新users表的cash
                    $fresh_cash = $db->prepare('UPDATE users 
                                                SET cash=:cash
                                                WHERE uid=:uid');
                    $fresh_cash->execute(array(
                                                ':cash'=>$balance,
                                                ':uid'=>$uid,
                                                ));
                                                
                    //检查portfolio表是否已有该股票
                    $check_stock = $db->prepare('SELECT quantity, avg_bid_price FROM portfolio 
                                                    WHERE owner_id=:uid AND symbol=:symbol');
                    $check_stock->bindValue(':uid', $uid, PDO::PARAM_INT);
                    $check_stock->bindValue(':symbol', $symbol, PDO::PARAM_STR);
                    $check_stock->execute();
                    
                    $result = $check_stock->fetchAll();
                    
                    //已有该股票
                    if($result && isset($result[0]['quantity']) && isset($result[0]['avg_bid_price']))
                    {
                        //计算平均购买股价
                        $own_quantity = $result[0]['quantity'];
                        $own_avg_price = $result[0]['avg_bid_price'];
                        $sum_quantity = $own_quantity + $quantity;
                        $avg_price = ($price * $quantity + $own_avg_price * $own_quantity)/$sum_quantity;
                    
                        //更新portfolio表
                        $insert_portfolio = $db->prepare('UPDATE portfolio 
                                                         SET quantity=:quantity, avg_bid_price=:price 
                                                         WHERE owner_id=:uid AND symbol=:symbol');
                        $insert_portfolio->execute(array(
                                                        ':quantity'=>$sum_quantity,
                                                        ':price'=>$avg_price,
                                                        ':uid'=>$uid,
                                                        ':symbol'=>$symbol,
                                                        ));
                    }
                    //没有该股票
                    else
                    {
                        //直接写入portfolio表
                        $insert_portfolio = $db->prepare('INSERT INTO portfolio (owner_id, symbol, quantity, avg_bid_price) 
                                                          VALUES (:uid, :symbol, :quantity, :price)');
                        $insert_portfolio->execute(array(
                                                ':uid'=>$uid,
                                                ':symbol'=>$symbol,
                                                ':quantity'=>$quantity,
                                                ':price'=>$price
                                                ));
                    }
                    
                    $db->commit();  
                }
                catch ( Exception $e )
                { 
                    $db -> rollBack ();
                    print "DBA FAIL:" . $e->getMessage();
                    $db = null;
                    exit;
                } 
                
                                            
                //关闭连接
                $db = null;
                
                header("Content-type:text/html;charset=utf-8");
                echo "购买成功";
            }
            else
            {
                header("Content-type:text/html;charset=utf-8");
                echo "无法购买";
            }
        }
    }
    else
    {
        header("Content-type:text/html;charset=utf-8");
        echo "请先登录";
    }
?>
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
                
                $get_quantity = $db->prepare('SELECT quantity FROM portfolio 
                                              WHERE owner_id=:uid AND symbol=:symbol');
                $get_quantity->bindValue(':uid', $uid, PDO::PARAM_INT);
                $get_quantity->bindValue(':symbol', $symbol, PDO::PARAM_STR);
                $get_quantity->execute();
                
                $result = $get_quantity->fetchAll();
                
                if(!$result)
                {
                    header("Content-type:text/html;charset=utf-8");
                    echo "没有可以出售的股票";
                    exit;
                }
                
                $own_quantity = $result[0]['quantity'];
                
                $rest_quantity = $own_quantity - $quantity;
                
                //是否有足够股票
                if($rest_quantity < 0)
                {
                    header("Content-type:text/html;charset=utf-8");
                    echo "没有足够的股票出售";
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
                                                ':action'=>'ask',
                                                ':price'=>$price
                                                ));
                                                
                    
                    if($rest_quantity > 0)
                    {
                        
                        //更新portfolio表
                        $reduce_portfolio = $db->prepare('UPDATE portfolio 
                                                         SET quantity=:quantity 
                                                         WHERE owner_id=:uid AND symbol=:symbol');
                        $reduce_portfolio->execute(array(
                                                        ':quantity'=>$rest_quantity,
                                                        ':uid'=>$uid,
                                                        ':symbol'=>$symbol,
                                                        ));
                    }
                    //出售全部该股票
                    else
                    {
                        //删除portfolio表记录
                        $dellete_portfolio = $db->prepare('DELETE FROM portfolio
                                                          WHERE owner_id=:uid AND symbol=:symbol');
                        $dellete_portfolio->execute(array(
                                                          ':uid'=>$uid,
                                                          ':symbol'=>$symbol,
                                                          ));
                    }
                    
                    //更新users表的cash
                    $get_cash = $db->prepare('SELECT cash FROM users 
                                                WHERE uid=:uid limit 1');
                    $get_cash->bindValue(':uid', $uid, PDO::PARAM_INT);
                    $get_cash->execute();
                
                    $result = $get_cash->fetchAll();
                    
                    $cash = $result[0]['cash'];
                    
                    $cash = $cash + ($price * $quantity);
                
                    $fresh_cash = $db->prepare('UPDATE users 
                                                SET cash=:cash
                                                WHERE uid=:uid');
                    $fresh_cash->execute(array(
                                                ':cash'=>$cash,
                                                ':uid'=>$uid,
                                                ));
                                                
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
                echo "出售成功";
            }
            else
            {
                header("Content-type:text/html;charset=utf-8");
                echo "无法出售";
            }
        }
    }
    else
    {
        header("Content-type:text/html;charset=utf-8");
        echo "请先登录";
    }
?>
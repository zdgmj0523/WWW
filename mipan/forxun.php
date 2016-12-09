<?
    $mysql_server_name="localhost"; //数据库服务器名称
    $mysql_username="root"; // 连接数据库用户名
    $mysql_password="RONMEIzh123456zh"; // 连接数据库密码
    $mysql_database="jiangxi"; // 数据库的名字
    
    // 连接到数据库
    $conn=mysql_connect($mysql_server_name, $mysql_username,$mysql_password);
    mysql_select_db($mysql_database);     


	ignore_user_abort();//关闭浏览器仍然执行
	set_time_limit(0);//让程序一直执行下去
	$interval=2;//每隔一定时间运行

     // 从表中提取信息的sql语句
    $strsql="SELECT oid,uid,pid,buyprice FROM `wp_order` where ostaus = 0";
    // 执行sql查询
    $result=mysql_query($strsql);
    // 获取查询结果
	while ($row = mysql_fetch_array($result)) {
		//取出结果并显示
		$oid = $row['oid'];//订单ID
		$uid = $row['uid'];//购买人
		$pid = $row['pid'];//商品ID
		$jiancj = $row['buyprice'];//建仓价
		//根据商品ID获取当前商品价格start
		$sql20 = "select b.cid,b.ask from wp_productinfo a INNER JOIN wp_catproduct b ON a.cid = b.cid where a.pid=".$pid;
		$res20 = mysql_query($sql20);
		$row20 = mysql_fetch_array($res20);
		if($row20['cid'] == 1)
		{
			$youjia = file_get_contents("./xh/you.txt");
		}else if($row20['cid'] == 2){
			$youjia = file_get_contents("./xh/baiyin.txt");
		}else{
			$youjia = file_get_contents("./xh/tong.txt");
		}
		
		//获取用户名信息
		$sql21 = "select a.*,b.* from wp_userinfo a INNER JOIN wp_accountinfo b ON a.uid=b.uid where a.uid=".$uid;
		$res21 = mysql_query($sql21);
		$user = mysql_fetch_array($res21);
		if($youjia <= 0)
		{
			continue;
		}else{
			jiesuan($uid,$oid,$youjia,$pid,$jiancj,$user);
		}
	}
    function jiesuan($uid,$oid,$youjia,$pid,$jiancj,$user){
        $arr = orderxq($youjia,$oid);
        //结余的金额，需要给当前用户的账户添加
        $bdyy=$arr['bdyy'];
        //盈亏资金
        $ykzj=$arr['ykzj'];
		//产品单价
		$uprice = $arr['uprice'];
		// ($bdyy.'+'.$jiancj.'+'.$ykzj.'+'.$uprice)
		//修改订单信息start
		$sql1="SELECT * FROM wp_order a INNER JOIN wp_productinfo b ON a.pid =b.pid where a.oid =".$oid;
		// 执行sql查询
		$result=mysql_query($sql1);
		$order = mysql_fetch_array($result);
        $orderno= build_order_no();
        $orderselltime=date(time());
        $orderostaus=1;
        $ordersellprice=$youjia;
        $orderploss=$ykzj;
        $orderfee = $order['feeprice']*$order['onumber'];
		$sql2 = "update wp_order SET selltime=".$orderselltime.",ostaus=".$orderostaus.",sellprice=".$ordersellprice.",ploss=".$orderploss.",fee=".$orderfee." where oid=".$oid;
        $msg= mysql_query($sql2);
		//修改订单信息end
        if ($msg) {
			//修改用户余额start
			$sql3 = "update wp_accountinfo SET balance = balance+".$bdyy." where uid=".$uid;
			$res3 = mysql_query($sql3);
			//修改用户余额end
			
			//根据商品id查询商品start
			$sql4 = "select ptitle from wp_productinfo where pid = ".$pid;
			$res4 = mysql_query($sql4);
            $goods=mysql_fetch_array($res4);
			//根据商品id查询商品end
			//添加平仓日志表
			$journal['jno']=$orderno;										//订单号
			$journal['uid'] = $uid;											//用户id
			$journal['jtype'] = '自动平仓';										//类型	
			$journal['jtime'] = date(time());								//操作时间
			$journal['jincome'] = $bdyy;									//收支金额【要予以删除】
			$journal['number'] = $order['onumber'];							//手数			
			$journal['remarks'] = $goods['ptitle'];							//产品名称	
			$journal['balance'] = $user['balance']+$bdyy;					//账户余额	
			if ($bdyy>$jiancj){
                  $journal['jstate']=1;										//盈利还是亏损
            }else{
                  $journal['jstate']=0;
            }			
			$journal['jusername'] = $user['username'];								//用户名
			$journal['jostyle'] = $order['ostyle'];							//涨、跌
			$journal['juprice'] = $uprice;									//单价
			$journal['jfee'] = $order['fee'];										//手续费
			$journal['jbuyprice'] = $order['buyprice'];						//入仓价
			$journal['jsellprice'] = $youjia;								//平仓价
			$journal['jaccess'] = $bdyy;									//出入金额
			$journal['jploss'] = $ykzj;										//出入金额
			$journal['oid'] = $oid;											//改订单流水的订单id
			$sql15 = "insert into wp_journal (jno,uid,jtime,jincome,number,remarks,balance,jstate,jusername,jostyle,juprice,jfee,jbuyprice,jsellprice,jaccess,jploss,oid) values('{$journal['jno']}','{$journal['uid']}','{$journal['jtime']}','{$journal['jincome']}','{$journal['number']}','{$journal['remarks']}','{$journal['balance']}','{$journal['jstate']}','{$journal['jusername']}','{$journal['jostyle']}','{$journal['juprice']}','{$journal['jfee']}','{$journal['jbuyprice']}','{$journal['jsellprice']}','{$journal['jaccess']}','{$journal['jploss']}','{$journal['oid']}')";
			// var_dump($sql15);die;
			mysql_query($sql15);
			$sql16 = "update wp_order SET commission=".$journal['balance']." where oid=".$oid;
			mysql_query($sql16);
        }else{
           $msg="平仓失败，稍后平仓";
        }     
    }
	//获取随时的动态值，计算盈亏金额和盈余数据
    function orderxq($youjia,$oid){
		if($youjia!=0){
	        // 从表中提取信息的sql语句
			$strsql="SELECT * FROM wp_order a INNER JOIN wp_productinfo b ON a.pid =b.pid where a.oid =".$oid;
			// 执行sql查询
			$result=mysql_query($strsql);
			$order = mysql_fetch_array($result);
	        $orderid=$order;
	        //建仓金额
	        if($order['eid']==0) {
	             $orderid['jc']=  round($order['uprice']*$order['onumber'],1);
	            //判断是买张还是买跌。0涨，1跌
	            if ( $orderid['ostyle']==0) {
	                 //盈亏资金
	                 $orderid['ykzj']=round(($youjia-$order['buyprice'])*$order['onumber']*$order['wave'],2);
	                 //本单盈余
	                 $orderid['bdyy']=round($orderid['jc']+$orderid['ykzj'],1);
	                 //盈亏百分百
	                 $orderid['ykbfb']=$orderid['ykzj']/ $orderid['jc']*1; 
	            }else{
	                //盈亏资金
                 	$orderid['ykzj']=round(($order['buyprice']-$youjia)*$order['onumber']*$order['wave'],2);
	                 //本单盈余
	                 $orderid['bdyy']=round($orderid['jc']+$orderid['ykzj'],1);
	                 //盈亏百分百
	                 $orderid['ykbfb']=$orderid['ykzj']/ $orderid['jc']*1;  
	            }
	        }else{
	             $orderid['jc']=0;
	            //判断是买张还是买跌。0涨，1跌
	            if ( $orderid['ostyle']==0) {
	                 //盈亏资金
                 	$orderid['ykzj']=round(($youjia-$order['buyprice'])*$order['onumber']*$order['wave'],2);
	                 //本单盈余
	                 $orderid['bdyy']=round($orderid['jc']+$orderid['ykzj'],1);
	                 //盈亏百分百
	                 $orderid['ykbfb']=$orderid['ykzj']/ $orderid['jc']*1; 
	                 if($orderid['ykzj']<0){
	                    $orderid['ykzj']=0;
	                    $orderid['bdyy']=0; 
	                 } 
	            }else{
	                //盈亏资金
                 	$orderid['ykzj']=round(($order['buyprice']-$youjia)*$order['onumber']*$order['wave'],2);
	                //本单盈余
	                $orderid['bdyy']=round($orderid['jc']+$orderid['ykzj'],1);
	                //盈亏百分百
	                $orderid['ykbfb']=$orderid['ykzj']/ $orderid['jc']*1; 
	                if ($orderid['ykzj']<0) {
	                    $orderid['ykzj']=0;
	                    $orderid['bdyy']=0; 
	                } 
	            }
	        }
	        return $orderid;
        }
    }
	function build_order_no(){
        return date(time()).substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 3);
    }
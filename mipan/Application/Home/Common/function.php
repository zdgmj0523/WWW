<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/12
 * Time: 9:05
 */
function getIsbuy($mypid){
	$time = date("Y-m-d",time());
	$y = date('w',time());
	if($y != 6 && $y != 0){
		if($mypid == 1 || $mypid == 2 || $mypid == 3){
			$ultime = strtotime($time." 09:30:00");
			$urtime = strtotime($time." 11:30:00");
			$dltime = strtotime($time." 13:00:00");
			$drtime = strtotime($time." 15:00:00");
			if((time() > $ultime && time() < $urtime) || (time() > $dltime && time() < $drtime))
			{
				
			}else{
				return 1;
			}
		}else{
			$ultime = strtotime($time." 04:00:00");
			$urtime = strtotime($time." 06:00:00");
			if(($y == 1) && (time() < $ultime)){
				return 1;
			}
			if(time() > $ultime && time() < $urtime ){
				return 1;
			}
		}
	}else if($y == 6){
		$ultime = strtotime($time." 04:00:00");
		if($mypid != 1 || $mypid != 2 || $mypid != 3){
			if(time() > $ultime){
				return 1;
			}
		}
	}else if($y == 0){
		return 1;
	}
}
function ceshi(){
    $asd=A("Admin/index");
    $result=$asd->ddd();
 
}

function qingcang(){
  
        $asd=A("Admin/index");
        $asd->olist();


}

function changephone($phone)
{
	$phone = substr_replace($phone,'****','4','3'); 
	return $phone;
}

function checkorderstatus($ordid){
    $Ord=M('Balance');
    $isverified=$Ord->where('balanceno='.$ordid)->getField('isverified');
    if($isverified==1){
        return true;
    }else{
        return false;
    }
}
function curl_file_get_contents($durl){  
    $ch = curl_init();//初始化一个curl会话
	curl_setopt($ch, CURLOPT_URL, $durl);
	curl_exec($ch);
	curl_close($ch);
}
function recommend($ordid,$money){
 
    $Ord=M('balance');
 $uid = $Ord->where("balanceno='$ordid'")->getField('uid');

        $money=$money;
        $rid=M("userinfo")->field("rid")->where("uid=$uid")->find();
        $rid=$rid['rid'];
if($rid!=0){


       $recommend = M("webconfig")->field("recommend")->where("id=1")->find();$recommend=$recommend['recommend'];
       
      $balance= M("accountinfo")->field("balance")->where("uid=$rid")->find();$balance=$balance['balance'];
      $data['balance']=$balance+$money*$recommend/100;
        M("accountinfo")->where("uid=$rid")->save($data);
        $data2['uid']=$rid;
        $data2['jno']=date("YmdHis",time());
        $data2['jtype']="推广";
        $data2['jtime']=time();
        $data2['jincome']=$money*$recommend/100;
        $data2['remarks']="第".$uid."号会员充值".$money."元";
        $data2['balance']=$balance+$money*$recommend/100;
    M("journal")->add($data2);
        $rid2=M("userinfo")->field("rid")->where("uid=$rid")->find();
        $rid2=$rid2['rid'];
    if($rid2!=0){
       $recommend = M("webconfig")->field("recommend,recommend2")->where("id=1")->find();
       $recommend2=$recommend['recommend2'];
       $recommend=$recommend['recommend'];
        
      $balance= M("accountinfo")->field("balance")->where("uid=$rid2")->find();$balance=$balance['balance'];
      $data4['balance']=$balance+$money*$recommend/100;
        M("accountinfo")->where("uid=$rid2")->save($data4);
        $data3['uid']=$rid;
        $data3['jno']=date("YmdHis",time());
        $data3['jtype']="推广";
        $data3['jtime']=time();
        $data3['jincome']=$money*$recommend/100*$recommend2/100;
        $data3['remarks']="第".$rid."号会员的下级被推广人".$uid."充值".$money."元";
        $data3['balance']=$balance+$money*$recommend/100*$recommend2/100;
    M("journal")->add($data3);
    }
    }

  
}

function orderhandle($parameter){
    file_put_contents("cccccccccccccc.txt", $parameter);
    $ordid=$parameter['order_no'];
    $data['isverified']  =1;
    $Ord=M('balance');
 $uid = $Ord->where("balanceno='$ordid'")->getField('uid');
    //充值金额
    $data['bpprice']  = $parameter['order_amount'];

    $balance = M('Accountinfo')->where("uid=".$uid)->getField('balance');
    $balance = $balance + $parameter['order_amount'];
    $da['balance'] = $balance;
    //更新总账户
  M('accountinfo')->where("uid=".$uid)->save($da);
    //更新充值金额及状态
  $Ord->where("balanceno='$ordid'")->save($data);

}
	function price(){
		$source=file_get_contents("xh/you.txt");
		return $source;
    }
	function byprice(){
		$source=file_get_contents("xh/baiyin.txt");
		return $source;
    }
	function toprice(){
		$source=file_get_contents("xh/tong.txt");
		return $source;
    }
	function updateore($oid,$youjia,$bdyy,$jiancj,$ykzj,$uprice){
		$yprice = price();//油价
		$byprice = byprice();//白银价
		$toprice = toprice();//铜价
        //获取账户余额
        $uid=$_SESSION['uid'];
		$orders = D('order');
        $users = D('userinfo');
		$jo = D('journal');
		$tq=C('DB_PREFIX');
		$order=M('order');
        $username = $_SESSION['husername'];
        $user=M('accountinfo')->where('uid='.$uid)->find();
		$detailed = A('Home/Detailed');
		$orderno = $detailed->build_order_no();
		$t = '';
        if($yprice <=0 || $byprice <=0 || $toprice <=0)
        {  
			
        }else{
			//获取当前订单的信息
			$myorder=M('order')->join($tq.'productinfo on '.$tq.'order.pid='.$tq.'productinfo.pid')->where($tq.'order.oid='.$oid)->find();
			$cid = $myorder['cid'];
			$fee = $myorder['feeprice']*$myorder['onumber'];//手续费
			$payprice = $myorder['uprice']*$myorder['onumber'];//保证金
			$onumber = $myorder['onumber'];//购买手数
			$ptitle = $myorder['ptitle'];//产品名称
			$ostyle = $myorder['ostyle'];//买涨买跌
			$uprice = $myorder['uprice'];//单价
			$buyprice = $myorder['buyprice'];//入仓价
			$myprice=M('accountinfo')->where('uid='.$uid)->find();
			$shengmoney = $myprice['balance'];
			$bdyy = $payprice + $ykzj;
			// 判断是否爆仓 先亏损
			// 先亏损完余额，再亏损到保证金的20%
			if($ykzj < 0)
			{
				if(abs($ykzj)>$shengmoney){
					if((abs($ykzj)-$shengmoney)/$payprice >= (0.8) ){
						/*** 写入记录* 爆仓记录* */
						$jour['jno'] = $orderno;			
						$jour['uid'] = $uid;
						$jour['jtype'] = '爆仓';
						$jour['jtime'] = date(time());
						$jour['number'] = $onumber;
						$jour['remarks'] = $ptitle;
						// $jour['balance'] = $shengmoney+$bdyy;
						$jour['balance'] = $payprice*0.2;
						$jour['jusername'] = $username;
						$jour['jostyle'] = $ostyle;
						$jour['juprice'] = $uprice;
						$jour['jfee'] = $fee;
						$jour['jincome'] = $bdyy;
						$jour['jbuyprice'] = intval($buyprice);
						if($cid==1){
							$jour['jsellprice'] = $yprice;	
						}elseif($cid==2){
							$jour['jsellprice'] = $byprice;
						}else{
							$jour['jsellprice'] = $toprice;
						}
						$jour['jaccess'] = $bdyy;
						$jour['jploss'] = $ykzj;
						$jour['jstate']=0;
						$jour['oid'] = $oid;
						$jour['explain'] = '系统自动爆仓';
						$jo->add($jour); 
						//修改订单信息
						if($cid==1){
						 $orders->where('oid='.$oid)->setField(array('selltime'=>date(time()),'ostaus'=>'1','sellprice'=>$yprice,'ploss'=>$ykzj,'is_hide'=>1));
					
						}elseif($cid==2){
						 $orders->where('oid='.$oid)->setField(array('selltime'=>date(time()),'ostaus'=>'1','sellprice'=>$byprice,'ploss'=>$ykzj,'is_hide'=>1));
					 
						}else{
						 $orders->where('oid='.$oid)->setField(array('selltime'=>date(time()),'ostaus'=>'1','sellprice'=>$toprice,'ploss'=>$ykzj,'is_hide'=>1));
						}
						//修改帐户信息
						$acco= M('accountinfo');
						$acco->uid=$uid;
						// $acco->balance=$shengmoney+$bdyy;
						$acco->balance=$payprice*0.2;
						$acco->save();
						$t = 1;
						
					}
				}
			}
			
		}
		if($t == 1)
		{
			return 1;
		}else{
			return 2;
		}
    }
	
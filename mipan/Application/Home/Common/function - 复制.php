<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/12
 * Time: 9:05
 */

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
    function build_order_no(){
        return date(time()).substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 3);
    }
 function updateore($oid,$youjia,$bdyy,$jiancj,$ykzj,$uprice){

         //获取账户余额
        $uid=$_SESSION['uid'];
        $users = D('userinfo');
        $username = $_SESSION['husername'];
        $user=M('accountinfo')->where('uid='.$uid)->find();
        //获取传递过来的值
        // $oid=$_REQUEST['oid'];
        //现在油价
        // $youjia=$_REQUEST['youjia'];
        if($youjia<=0)
        {  
        

        }
        else{
          
        //结余的金额，需要给当前用户的账户添加
        // $bdyy=$_REQUEST['bdyy'];
         //建仓金额
        // $jiancj= $_REQUEST['jiancj'];
        //盈亏资金
        // $ykzj=  $_REQUEST['ykzj'];
        //产品单价
        // $uprice =$_REQUEST['uprice'];
        //先修改订单信息，返回成功信息后修改账户余额和添加日志记录
        $orderno= build_order_no();
      
        $tq=C('DB_PREFIX');
        $myorder=M('order')->join($tq.'productinfo on '.$tq.'order.pid='.$tq.'productinfo.pid')->where($tq.'order.oid='.$oid)->find();
        $order=M('order');
        $order->selltime=date(time());
        $order->ostaus=1;
        $order->sellprice=$youjia;
        //盈亏资金
        $order->ploss=$ykzj;
        //手续费
        $fee = $myorder['feeprice']*$myorder['onumber'];
        $order->fee=$fee;
		//加上至盈至亏判断
		/*$endprofit = $myorder['endprofit']/100;//至盈
		$endloss = $myorder['endloss']/100;//至损
		if($endprofit > 0 && $ykzj > 0)
		{
			if(($ykzj/($myorder['uprice']*$myorder['onumber'])) > $endprofit)
			{
				
			}
		}else{
			return true;
		}
		if($endloss > 0 && $ykzj < 0)
		{
			if(abs($ykzj/($myorder['uprice']*$myorder['onumber'])) > $endloss)
			{
				
			}
		}else{
			return true;
		}*/
		
//      //佣金
//      $order->commission=$youjia-$myorder['buyprice']-$fee;
//      //盈亏
//      $order->ploss=$youjia-$myorder['buyprice'];
        $msg= $order->save();      

        if ($msg) {
            
            //根据商品id查询商品
            $goods=M('productinfo')->where('pid='.$myorder['pid'])->find();
            //用户亏损了，返点
            if($ykzj<0){
                //查询该用户级别
                $thisuser = $users->field('otype,oid')->where('uid='.$uid)->find();
                //返佣记录
                $otype = $thisuser['otype'];            //用户类型
                $ouid = $thisuser['oid'];               //上级id
                //如果有oid，是分销用户
                if($ouid!=""){
                    if($otype==0){
                        //此id用户是普通客户，oid为代理用户id
                        $otype = "客户";
                        //查会员单位返点比例
                        $agent = $users->field('oid,rebate,feerebate,otype,username')->where('uid='.$ouid)->find();
                        $agent_user=M('accountinfo')->where('uid='.$ouid)->find();
                        //判断上级用户，如果是代理商
                        if($agent['otype']==1){
                            $agent_rebate = $agent['rebate'];               //代理盈亏返点
                            $agent_feerebate = $agent['feerebate'];         //代理手续费返点
                            $menber_id = $agent['oid'];                     //用户的上级id
                            if($menber_id!=""){
                                $menber = $users->field('rebate,feerebate,username')->where('uid='.$menber_id)->find();
                                $menber_rebate = $menber['rebate'];                 //会员盈亏返点
                                $menber_feerebate = $agent['feerebate'];            //会员手续费返点
                                $newykzj = abs($ykzj);
                                $menber_ploss = $newykzj*$menber_rebate/100;            //会员盈亏反金
                                $menber_feeploss = $fee*$menber_feerebate/100;      //会员手续费反金
                                $agent_ploss = $menber_ploss*$agent_rebate/100;                 //代理盈亏反金
                                $agent_feeploss = $menber_feeploss*$agent_feerebate/100;        //代理手续费反金
                                $menber_user=M('accountinfo')->where('uid='.$menber_id)->find();
                                //写两条记录，一条代理，一条会员
                                $distribution = M('journal');
                                $disj['jno']=$orderno;                                      //订单号
                                $disj['uid'] = $ouid;                                       //用户id
                                $disj['jtype'] = '返点';                                      //类型
                                $disj['jtime'] = date(time());                              //操作时间
                                $disj['balance'] = $agent_user['balance']+$agent_ploss+$agent_feeploss;         //账户余额
                                $disj['jfee'] = $agent_feeploss;                            //手续费反金
                                $disj['jploss'] = $agent_ploss;                             //盈亏反金
                                $disj['jaccess'] = $agent_feeploss+$agent_ploss;            //出入金额
                                $disj['jusername'] = $agent['username'];                    //用户名
                                $disj['oid'] = $oid;                                    //订单id
                                $disj['explain'] = '代理反金';                                  //操作标记
                                $disj['remarks'] = $goods['ptitle'];                        //产品名称  
                                $disj['number'] = $myorder['onumber'];                      //数量    
                                $disj['jostyle'] = $myorder['ostyle'];                      //涨、跌
                                $disj['jbuyprice'] = $myorder['buyprice'];                  //入仓价
                                $disj['jsellprice'] = $youjia;                              //平仓价
                                $distribution->add($disj);
                                
                                //写入会员记录
                                $distribution = M('journal');
                                $mdisj['jno']=$orderno;                                     //订单号
                                $mdisj['uid'] = $ouid;                                      //用户id
                                $mdisj['jtype'] = '返点';                                     //类型
                                $mdisj['jtime'] = date(time());                             //操作时间
                                $mdisj['balance'] = $menber_user['balance']+$menber_ploss+$menber_feeploss;         //账户余额
                                $mdisj['jfee'] = $menber_feeploss;                          //手续费反金
                                $mdisj['jploss'] = $menber_ploss;                           //盈亏反金
                                $mdisj['jaccess'] = $menber_feeploss+$menber_ploss;         //出入金额
                                $mdisj['jusername'] = $menber['username'];                  //用户名
                                $mdisj['oid'] = $oid;                                   //订单id
                                $mdisj['explain'] = '会员反金';                             //操作标记
                                $mdisj['remarks'] = $goods['ptitle'];                       //产品名称  
                                $mdisj['number'] = $myorder['onumber'];                     //数量    
                                $mdisj['jostyle'] = $myorder['ostyle'];                     //涨、跌
                                $mdisj['jbuyprice'] = $myorder['buyprice'];                 //入仓价
                                $mdisj['jsellprice'] = $youjia;                             //平仓价
                                $distribution->add($mdisj);
                            }
                        }else if($agent['otype']==2){
                            //如果上级是会员
                            $menber_rebate = $agent['rebate'];              //代理盈亏返点
                            $menber_feerebate = $agent['feerebate'];        //代理手续费返点
                            $menber_ploss = $newykzj*$menber_rebate/100;            //会员盈亏反金
                            $menber_feeploss = $fee*$menber_feerebate/100;      //会员手续费反金
                            //echo $ykzj*$menber_rebate/100;
                            //echo $menber_rebate.'----------------';
                            //写入会员记录
                            $distribution = M('journal');
                            $mdisj['jno']=$orderno;                                     //订单号
                            $mdisj['uid'] = $ouid;                                      //用户id
                            $mdisj['jtype'] = '返点';                                     //类型
                            $mdisj['jtime'] = date(time());                             //操作时间
                            $mdisj['balance'] = $user['balance']+$menber_ploss+$menber_feeploss;            //账户余额
                            $mdisj['jfee'] = $menber_feeploss;                          //手续费反金
                            $mdisj['jploss'] = $menber_ploss;                           //盈亏反金
                            $mdisj['jaccess'] = $menber_feeploss+$menber_ploss;         //出入金额
                            $mdisj['jusername'] = $agent['username'];                   //用户名
                            $mdisj['oid'] = $oid;                                   //订单id
                            $mdisj['explain'] = '会员反金';                             //操作标记
                            $mdisj['remarks'] = $goods['ptitle'];                       //产品名称  
                            $mdisj['number'] = $myorder['onumber'];                     //数量    
                            $mdisj['jostyle'] = $myorder['ostyle'];                     //涨、跌
                            $mdisj['jbuyprice'] = $myorder['buyprice'];                 //入仓价
                            $mdisj['jsellprice'] = $youjia;                             //平仓价
                            $distribution->add($mdisj);
                        }else{
                            //上级是平台
                            
                        }
                    }else if($otype==1){
                        //此id用户是代理
                        $menber = $users->field('oid,rebate,feerebate,otype')->where('uid='.$ouid)->find();
                        if($menber['oid']!=""){
                            $menber_rebate = $menber['rebate'];             //会员盈亏返点
                            $menber_feerebate = $menber['feerebate'];       //会员手续费返点
                            $menber_ploss = $newykzj*$menber_rebate/100;            //会员盈亏反金
                            $menber_feeploss = $fee*$menber_feerebate/100;      //会员手续费反金
                            //写入会员记录
                            $distribution = M('journal');
                            $mdisj['jno']=$orderno;                                     //订单号
                            $mdisj['uid'] = $ouid;                                      //用户id
                            $mdisj['jtype'] = '返点';                                     //类型
                            $mdisj['jtime'] = date(time());                             //操作时间
                            $mdisj['balance'] = $user['balance']+$menber_ploss+$menber_feeploss;            //账户余额
                            $mdisj['jfee'] = $menber_feeploss;                          //手续费反金
                            $mdisj['jploss'] = $menber_ploss;                           //盈亏反金
                            $mdisj['jaccess'] = $menber_feeploss+$menber_ploss;         //出入金额
                            $mdisj['jusername'] = $menber['username'];                  //用户名
                            $mdisj['oid'] = $oid;                                   //订单id
                            $mdisj['explain'] = '会员反金';                                 //操作标记
                            $mdisj['remarks'] = $goods['ptitle'];                       //产品名称  
                            $mdisj['number'] = $myorder['onumber'];                     //数量    
                            $mdisj['jostyle'] = $myorder['ostyle'];                     //涨、跌
                            $mdisj['jbuyprice'] = $myorder['buyprice'];                 //入仓价
                            $mdisj['jsellprice'] = $youjia;                             //平仓价
                            $distribution->add($mdisj);
                        }
                    }else if($otype==2){
                        //此id用户是会员
                        
                    }               
                }else{
                    //不是分销用户
                    
                }
            }
            //添加平仓日志表
            //随机生成订单号
            $myjournal=M('journal');
            $journal['jno']=$orderno;                                       //订单号
            $journal['uid'] = $uid;                                         //用户id
            $journal['jtype'] = '平仓';                                       //类型    
            $journal['jtime'] = date(time());                               //操作时间
            $journal['jincome'] = $bdyy;                                    //收支金额【要予以删除】
            $journal['number'] = $myorder['onumber'];                       //数量            
            $journal['remarks'] = $goods['ptitle'];                         //产品名称  
            $journal['balance'] = $user['balance']+$bdyy;                   //账户余额  
            if ($bdyy>$jiancj){
                  $journal['jstate']=1;                                     //盈利还是亏损
            }else{
                  $journal['jstate']=0;
            }           
            $journal['jusername'] = $username;                              //用户名
            $journal['jostyle'] = $myorder['ostyle'];                       //涨、跌
            $journal['juprice'] = $uprice;                                  //单价
            $journal['jfee'] = $fee;                                        //手续费
            $journal['jbuyprice'] = $myorder['buyprice'];                   //入仓价
            $journal['jsellprice'] = $youjia;                               //平仓价
            $journal['jaccess'] = $bdyy;                                    //出入金额
            $journal['jploss'] = $ykzj;                                     //出入金额
            $journal['oid'] = $oid;                                         //改订单流水的订单id
            $journal['explain'] = $otype.'平仓';
            $myjournal->add($journal);
            $order->where('oid='.$oid)->setField('commission',$journal['balance']);
			
			$myprice=M('accountinfo')->where('uid='.$uid)->find();
            $acco= M('accountinfo');
            $acco->uid=$uid;
            $acco->balance=$myprice['balance']+$bdyy;
            $acco->save();
        }else{
           $msg="平仓失败，稍后平仓";
        }

       return $msg; 
        }        
    }
	
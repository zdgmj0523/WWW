<?php

namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {
 
    public function index()
    {
 
    	header("Content-type: text/html; charset=utf-8");
    	$user= A('Admin/User');
		$user->checklogin();
		
		$tq=C('DB_PREFIX');
    	$user = D("userinfo");
		$order = D("order");
		$product = D("productinfo");
		$account = D("accountinfo");
		
    	//访问量
		
    	//用户数量
    	$userCount = $user->where('ustatus=0')->count();
		
    	//今日订单数量，最近7天订单数量，最近30天交易总金额，订单信息
    	$orderDay = $order->where("date_format(from_UNIXTIME(selltime),'%Y-%m-%d')>='".date('Y-m-d')."'")->count();
		$sevenDay = date('Y-m-d',strtotime("-7 day"));
		$orderCount = $order->where("date_format(from_UNIXTIME(selltime),'%Y-%m-%d')>='".$sevenDay."'")->count();
		$last_day = date('Y-m-d',strtotime("-30 day"));
		$result = $order->where("date_format(from_UNIXTIME(selltime),'%Y-%m-%d')>='".$last_day."'")->select();
		for($i=0;$i<count($result);$i++){
			$total += ($result[$i]['onumber']*$result[$i]['buyprice']);
		}
		//最近30天交易总金额
		$total = number_format($total);
		
		$orders = $order->table('wp_userinfo u,wp_order o,wp_productinfo p')->where('u.uid=o.uid and o.pid=p.pid')->field('u.uid as uid,u.username as username,o.buytime as buytime,p.pid as pid,p.ptitle as ptitle,p.uprice as uprice,o.onumber as onumber,o.ostyle as ostyle,o.ostaus as ostaus,o.fee as fee,o.orderno as orderno')->order('o.oid desc')->limit(20)->select();
		
		
		//var_dump($orders[1]['pid']);
		//die;
		//产品信息展示
		$plist = $product->order('pid desc')->limit(5)->select();
		
		//产品交易数量
//		foreach($orders as $k => $v){
//			foreach($plist as $key => $value){
//				if($v['pid']==$value['pid']){
//					$onumber = $order->where('pid='.$value['pid'])->field('oid,onumber')->select();
//
//					//$plist['onumber'] = intval($onumber);
//				}else{
//					$plist['onumber'] = 0;
//				}	
//			}
//			$onum = intval($onumber[$k]['onumber']);

//			
//			$plist[$k]['onumber'] = intval($onumber);
//		}
		
		//var_dump($plist);
		$this->assign('orderDay',$orderDay);
    	$this->assign('userCount',$userCount);
		$this->assign('orderCount',$orderCount);
		$this->assign('total',$total);
		$this->assign('orders',$orders);
		$this->assign('plist',$plist);
		$this->display();
	}
 
	public function price(){
         // $source=file_get_contents("xh/you.txt");
         // $msg = explode(',',$source);
         // $myprice[0] = round(str_replace('price:', '',str_replace('"','',$msg[1])));//最新
         // return $myprice[0];
		 $source=file_get_contents("xh/you.txt");
 
		 return $source;



    }
	public function byprice(){
         // $source=file_get_contents("xh/baiyin.txt");
         // $msg = explode(',',$source);
         // $myprice[0] = round(str_replace('price:', '',str_replace('"','',$msg[1])));//最新

         // return $myprice[0];
          $source=file_get_contents("xh/baiyin.txt");
 
		 return $source;
    }
	public function toprice(){
         $source=file_get_contents("xh/tong.txt");
         $msg = explode(',',$source);
         $myprice[0] = round(str_replace('price:', '',str_replace('"','',$msg[1])));//最新
         return $myprice[0];
    }
	public function olist456(){
		//获取所有没有平仓的订单
		$tq=C('DB_PREFIX');
		$orders = D('order');
		$jo = D('journal');

		$detailed = A('Home/Detailed');

		$orderno = $detailed->build_order_no();
 
		$field = $tq.'order.oid as oid,'.$tq.'order.buyprice as buyprice,'.$tq.'order.onumber as onumber,'.$tq.'productinfo.wave as wave,'.$tq.'order.endprofit as endprofit,'.$tq.'order.endloss as endloss,'.$tq.'catproduct.cid as cid,'.$tq.'productinfo.uprice as uprice,'.$tq.'order.uid as uid,'.$tq.'order.ptitle as ptitle,'.$tq.'order.pid as pid,'.$tq.'accountinfo.balance as balance,'.$tq.'userinfo.username as username,'.$tq.'order.ostyle as ostyle,'.$tq.'order.fee as fee,'.$tq.'catproduct.myat as myat';
		//$olist = $orders->where('ostaus=0')->select();
		$order=$orders->join($tq.'productinfo on '.$tq.'order.pid='.$tq.'productinfo.pid')
        ->join($tq.'catproduct on '.$tq.'productinfo.cid='.$tq.'catproduct.cid')->join($tq.'userinfo on '.$tq.'order.uid='.$tq.'userinfo.uid')->join($tq.'accountinfo on '.$tq.'order.uid='.$tq.'accountinfo.uid')->field($field)->where('ostaus=0')->select();
		//获取最新产品价格
		$yprice = $this->price();//油价
		$byprice = $this->byprice();//白银价

		$toprice = $this->toprice();//铜价

		//设置盈亏比，爆仓
		foreach($order as $k => $v){
			$uid = $order[$k]['uid'];					//用户id
			$pid = $order[$k]['pid'];					//产品id
			$uoid = $order[$k]['uoid'];					//上级用户id
			$balance = $order[$k]['balance'];			//账户余额
			$username = $order[$k]['username'];			//用户名
			$fee = $order[$k]['fee'];					//手续费
			$ptitle = $order[$k]['ptitle'];				//产品
			$endprofit = $order[$k]['endprofit'];		//止盈
			$endloss = $order[$k]['endloss'];			//止亏
			$buyprice = $order[$k]['buyprice'];			//买入价格
			$onumber = $order[$k]['onumber'];			//买入数量
			$cid = $order[$k]['cid'];					//产品分类id，用于区分产品现价，1代表油价、2代表白银、3代表铜
			$ostyle = $order[$k]['ostyle'];				//涨、跌，0代表涨、1代表跌
			$wave = $order[$k]['wave'];					//浮动
			$uprice = $order[$k]['uprice'];				//单价
			$oid = $order[$k]['oid'];					//订单id
			//$myat = $order[$k]['myat'];					//波动，目前只有油的波动是100，其他的都是1
			 $myat=1;
			$payprice = $onumber*$uprice;				//保障金
			$min_payprice = $payprice*0.3;				//最低限制
			if($ostyle==0){
				if($cid==1){
					$ploss = round(($yprice-$buyprice)*$onumber*$wave*$myat,2);			//盈亏资金	
				}elseif($cid==2){

					$ploss = round(($byprice-$buyprice)*$onumber*$wave*$myat,2); 		//盈亏资金
				}else{
					$ploss = round(($toprice-$buyprice)*$onumber*$wave*$myat,2);		//盈亏资金
				}
			}else{
				if($cid==1){
					$ploss = round(($buyprice-$yprice)*$onumber*$wave*$myat,2);			//盈亏资金	
				}elseif($cid==2){
					$ploss = round(($buyprice-$byprice)*$onumber*$wave*$myat,2);		//盈亏资金
				}else{
					$ploss = round(($buyprice-$toprice)*$onumber*$wave*$myat,2);		//盈亏资金
				}
			}
			
			//1
			$percentage = round($ploss/($uprice*$onumber)*$myat,2);			//盈亏百分比
			$surplus = $payprice+$ploss;									//本单盈余	
			
			
			
			$myprice=M('accountinfo')->where('uid='.$uid)->find();
			$shengmoney = $myprice['balance'];
			// var_dump($shengmoney);die;
			//判断是否爆仓 先亏损
			//先亏损完余额，再亏损到保证金的20%
			if($ploss < 0)
			{
				if(abs($ploss)>$shengmoney){
					if(round((abs($ploss)-$shengmoney)/$payprice) >=0.2 ){
						$acco= M('accountinfo');
						$acco->uid=$uid;
						// if(($myprice['balance']+$surplus) < 0)
						// {
							// $acco->balance = 0;
						// }else{
							$acco->balance=$myprice['balance']+$surplus;
						// }
						$acco->save();
						/**
						 * 写入记录
						 * 爆仓记录
						 * */
						$jour['jno'] = $orderno;			
						$jour['uid'] = $uid;
						$jour['jtype'] = '爆仓';
						$jour['jtime'] = date(time());
						$jour['number'] = $onumber;
						$jour['remarks'] = $ptitle;
						$jour['balance'] = $balance+$surplus;
						$jour['jusername'] = $username;
						$jour['jostyle'] = $ostyle;
						$jour['juprice'] = $uprice;
						$jour['jfee'] = $fee;
						$jour['jincome'] = $surplus;
						$jour['jbuyprice'] = $buyprice;
						if($cid==1){
							$jour['jsellprice'] = $yprice;	
						}elseif($cid==2){
							$jour['jsellprice'] = $byprice;
						}else{
							$jour['jsellprice'] = $toprice;
						}
						$jour['jaccess'] = $surplus;
						$jour['jploss'] = $ploss;
						if($ploss>0){$jour['jstate']=1;}else{$jour['jstate']=0;}
						$jour['oid'] = $oid;
						$jour['explain'] = '系统自动爆仓';
						
						/**
						 * 保障金亏空，自动平仓
						 * 按盈亏比判断是否爆仓
						 * */
						//判断盈余是否为0，小于0表示保证金已经亏空自动平仓
						if($cid==1){
						 $orders->where('oid='.$oid)->setField(array('selltime'=>date(time()),'ostaus'=>'1','sellprice'=>$yprice,'ploss'=>$ploss,'is_hide'=>1));
					
						}elseif($cid==2){
						 $orders->where('oid='.$oid)->setField(array('selltime'=>date(time()),'ostaus'=>'1','sellprice'=>$byprice,'ploss'=>$ploss,'is_hide'=>1));
					 
						}else{
						 $orders->where('oid='.$oid)->setField(array('selltime'=>date(time()),'ostaus'=>'1','sellprice'=>$toprice,'ploss'=>$ploss,'is_hide'=>1));
						 
						}
						$jo->add($jour); 
					}else{
						continue;
					}
				}else{
					continue;
				}
			}else{
				continue;
			}
		}
		// $this->assign('olist',$order);
		echo "<script>alert('成功');window.history.back(-1);</script> ";
	}
	public function olist($oid,$youjia,$returnrate){
		// var_dump(111);die;
		$returnrate = $returnrate/100;
		$fee = 0;
         //获取账户余额
        $uid=$_SESSION['uid'];
        $users = D('userinfo');
        $username = $_SESSION['husername'];
        $user=M('accountinfo')->where('uid='.$uid)->find();
		$t = M('order')->where($tq.'oid='.$oid)->find();
		if($t['ostaus'] == 1)
		{
			return true;
		}
        if($youjia<=0)
        {  
        }
        else{
        //先修改订单信息，返回成功信息后修改账户余额和添加日志记录
        $orderno= build_order_no();
        $tq=C('DB_PREFIX');
        $myorder=M('order')->join($tq.'catproduct on '.$tq.'order.pid='.$tq.'catproduct.cid')->where($tq.'order.oid='.$oid)->find();
        $order=M('order');
        $order->selltime=date(time());//平仓时间
        $order->ostaus=1;//是否平仓
        $order->sellprice=$youjia;//当前油价
		if($myorder['ostyle'] == '1')
		{
			if($youjia > $myorder['buyprice'])
			{
				$ykzj = $myorder['money']*$returnrate*(-1);
				$bdyy = 0;
			}else if($youjia == $myorder['buyprice'])
			{
				$ykzj = 0;
				$bdyy = $myorder['money'];
			}else{
				$ykzj = $myorder['money']*$returnrate;
				$bdyy = $ykzj+$myorder['money'];
			}
		}else{
			if($youjia > $myorder['buyprice'])
			{
				$ykzj = $myorder['money']*$returnrate;
				$bdyy = $ykzj+$myorder['money'];
			}else if($youjia == $myorder['buyprice']){
				$ykzj = 0;
				$bdyy = $myorder['money'];
			}else{
				$ykzj = $myorder['money']*$returnrate*(-1);
				$bdyy = 0;
			}
		}
        $order->ploss=$ykzj;//盈亏资金
        $order->fee=0;//手续费
		$msg= $order->save();   
		
        if ($msg) {
            $myprice=M('accountinfo')->where('uid='.$uid)->find();
			//更改帐户余额 开始
            $acco= M('accountinfo');
            $acco->uid=$uid;
            $acco->balance=$myprice['balance']+$bdyy;
            $acco->save();
			//更改帐户余额 结束
			
            //根据商品id查询商品
            $goods=M('catproduct')->where('cid='.$myorder['pid'])->find();
            //用户亏损了，返点
            if($ykzj<0){
                $thisuser = $users->field('otype,oid')->where('uid='.$uid)->find(); //查询该用户级别
                $otype = $thisuser['otype'];            //用户类型
                $ouid = $thisuser['oid'];               //上级id
                //如果有oid，是分销用户
                if($ouid!=""){
                    if($otype==0){
                        $otype = "客户";//此id用户是普通客户，oid为代理用户id
                        $agent = $users->field('oid,rebate,feerebate,otype,username')->where('uid='.$ouid)->find();//查上级返点比例
                        $agent_user=M('accountinfo')->where('uid='.$ouid)->find();//查上级帐户信息
                        if($agent['otype']==1){//上级是经纪人
                            $agent_rebate = $agent['rebate'];               //经纪人盈亏返点
                            $agent_feerebate = $agent['feerebate'];         //经纪人手续费返点
                            $menber_id = $agent['oid'];                     //经纪人的上级id
							
                            if($menber_id!=""){
                                $menber = $users->field('rebate,feerebate,username')->where('uid='.$menber_id)->find();//普通会员信息
                                $menber_rebate = $menber['rebate'];                 //会员盈亏返点
                                $menber_feerebate = $agent['feerebate'];            //会员手续费返点
                                $newykzj = abs($ykzj);
                                $menber_ploss = $newykzj*$menber_rebate/100;            //会员盈亏反金
                                $menber_feeploss = $fee*$menber_feerebate/100;      //会员手续费反金
                                $agent_ploss = $menber_ploss*$agent_rebate/100;                 //代理盈亏反金
                                $agent_feeploss = $menber_feeploss*$agent_feerebate/100;        //代理手续费反金
                                $menber_user=M('accountinfo')->where('uid='.$menber_id)->find();//普通会员帐户信息
                                //写两条记录，一条代理，一条会员
                                $distribution = M('journal');
                                $disj['jno']=$orderno;                                      //订单号
                                $disj['uid'] = $ouid;                                       //经纪人id
                                $disj['jtype'] = '返点';                                    //类型
                                $disj['jtime'] = date(time());                              //操作时间
                                $disj['balance'] = $agent_user['balance']+$agent_ploss+$agent_feeploss;         //账户余额
                                $disj['jfee'] = $agent_feeploss;                            //手续费反金
                                $disj['jploss'] = $agent_ploss;                             //盈亏反金
                                $disj['jaccess'] = $agent_feeploss+$agent_ploss;            //出入金额
                                $disj['jusername'] = $agent['username'];                    //用户名
                                $disj['oid'] = $oid;                                    //订单id
                                $disj['explain'] = '代理反金';                                  //操作标记
                                $disj['remarks'] = $goods['cname'];                        //产品名称  
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
                                $mdisj['remarks'] = $goods['cname'];                       //产品名称  
                                $mdisj['number'] = $myorder['onumber'];                     //数量    
                                $mdisj['jostyle'] = $myorder['ostyle'];                     //涨、跌
                                $mdisj['jbuyprice'] = $myorder['buyprice'];                 //入仓价
                                $mdisj['jsellprice'] = $youjia;                             //平仓价
                                $distribution->add($mdisj);
                            }
                        }else if($agent['otype']==2){//如果上级是平台
                            $menber_rebate = $agent['rebate'];              //代理盈亏返点
                            $menber_feerebate = $agent['feerebate'];        //代理手续费返点
							$newykzj = abs($ykzj);
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
                            $mdisj['oid'] = $oid;                                   	//订单id
                            $mdisj['explain'] = '会员反金';                             //操作标记
                            $mdisj['remarks'] = $goods['cname'];                       //产品名称  
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
                            $mdisj['remarks'] = $goods['cname'];                       //产品名称  
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
            $journal['remarks'] = $goods['cname'];                         //产品名称  
            $journal['balance'] = $user['balance']+$bdyy;                   //账户余额  
            if ($bdyy > $myorder['money']){
                  $journal['jstate']=1;                                     //盈利还是亏损
            }else{
                  $journal['jstate']=0;
            }           
            $journal['jusername'] = $username;                              //用户名
            $journal['jostyle'] = $myorder['ostyle'];                       //涨、跌
			
            //$journal['juprice'] = $uprice;                                //单价
            $journal['juprice'] = $myorder['money'];                                  	//单价
            $journal['jfee'] = $fee;                                        //手续费
            $journal['jbuyprice'] = $myorder['buyprice'];                   //入仓价
            $journal['jsellprice'] = $youjia;                               //平仓价
            $journal['jaccess'] = $bdyy;                                    //出入金额
            $journal['jploss'] = $ykzj;                                     //盈亏资金
            $journal['oid'] = $oid;                                         //改订单流水的订单id
            $journal['explain'] = $otype.'平仓';
            $myjournal->add($journal);
            $order->where('oid='.$oid)->setField('commission',$journal['balance']);
        }else{
           $msg="平仓失败，稍后平仓";
        }

       return $msg; 
        }        
    }
}
<?php
// 明细表，包括收支明细和交易明细
namespace Home\Controller;
use Think\Controller;
class DetailedController extends Controller {

    //交易明细列表
    public function dtrading(){
        $uid=$_SESSION['uid'];
        //根据传来的时间查询对应的数据(只传递月份和时间便可以)
        $mytoday=I('get.today');
        // 判断是否是点击月份左右的按钮
        if($mytoday){
             // 判断是上个月的
             if(I('get.no')==1) {
                 $timestamp=strtotime($mytoday);
                 $firstday=date('Y-m-01',strtotime(date('Y',$timestamp).'-'.(date('m',$timestamp)-1).'-01'));
                 $lastday=date('Y-m-d',strtotime("$firstday +1 month -1 day"));
             //判断如果是本月的，则下个月数据不存在。
             }else if(I('get.no')==2&&$mytoday==date('Y-m-01', strtotime(date("Y-m-d")))){
                 $firstday=date('Y-m-01', strtotime(date("Y-m-d")));
                 $lastday=date('Y-m-d', strtotime("$BeginDate +1 month -1 day"));
             //判断是下个月的
             }else{
                $timestamp=strtotime($mytoday);
                 $arr=getdate($timestamp);
                 if($arr['mon'] == 12){
                    $year=$arr['year'] +1;
                    $month=$arr['mon'] -11;
                    $firstday=$year.'-0'.$month.'-01';
                    $lastday=date('Y-m-d',strtotime("$firstday +1 month -1 day"));
                 }else{
                    $firstday=date('Y-m-01',strtotime(date('Y',$timestamp).'-'.(date('m',$timestamp)+1).'-01'));
                    $lastday=date('Y-m-d',strtotime("$firstday +1 month -1 day"));
                 }
             }
             
             $begintime=$firstday;
             $endtime=$lastday;
        }else{
            $begintime=date('Y-m-01', strtotime(date("Y-m-d")));
            $endtime=date('Y-m-d', strtotime("$BeginDate +1 month -1 day"));
         }
        $tq=C('DB_PREFIX');
        $last_day1 =  strtotime(date('Y-m-01', strtotime($begintime)));
        $last_day2 =  strtotime(date('Y-m-d', strtotime($endtime)));
        $where = $last_day1.'<='.$tq.'order.selltime and '.$last_day2.'>='.$tq.'order.selltime';
        //查询多条记录
        $count = M('order')->join($tq.'productinfo on '.$tq.'order.pid='.$tq.'productinfo.pid')->where($tq.'order.uid='.$uid.' and '.$tq.'order.ostaus=1 and '.$where)->count();
        $pagecount = 10;
        $page = new \Think\Page($count , $pagecount);
        $page->parameter = $row; //此处的row是数组，为了传递查询条件
        $page->setConfig('first','首页');
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $page->setConfig('last','尾页');
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%  第 '.I('p',1).' 页/共 %TOTAL_PAGE% 页 ( '.$pagecount.' 条/页 共 %TOTAL_ROW% 条)');
        $show = $page->show();
        $list = M('order')->join($tq.'productinfo on '.$tq.'order.pid='.$tq.'productinfo.pid')->where($tq.'order.uid='.$uid.' and '.$tq.'order.ostaus=1 and '.$where)->order($tq.'order.selltime desc' )->limit($page->firstRow.','.$page->listRows)->select();   
        //计算出一段时间的盈亏beg

        //计算总收益
        $trading['money']=M('order')->where($tq.'order.uid='.$uid.' and '.$tq.'order.ostaus=1 and '.$where)->sum('ploss');
        //end
        //总单数
        $sumcount=M('order')->where($tq.'order.uid='.$uid.' and '.$tq.'order.ostaus=1 and '.$where)->count();
        $trading['count']=$sumcount;
        //总手数
        $sumonumber=M('order')->where($tq.'order.uid='.$uid.' and '.$tq.'order.ostaus=1 and '.$where)->sum('onumber');
        $trading['onumber']=$sumonumber;
        //时间
        $trading['time']=$last_day1;
     
        $this->assign('trading',$trading);
        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->display();//显示模板
    }
    //交易详情页
    public function tradingid(){
        $tq=C('DB_PREFIX');
        $order=M('order')->join($tq.'productinfo on '.$tq.'order.pid='.$tq.'productinfo.pid')->join($tq.'catproduct on '.$tq.'productinfo.cid='.$tq.'catproduct.cid')->where($tq.'order.oid='.I('oid'))->find();

        $orderid=$order;
		//建仓金额
        $orderid['jc']=$order['uprice']*$order['onumber'];
		//盈亏资金
		/*if($order['ostyle']==0){
			if($order['cid']==1){			
	       		$orderid['ykzj']=($order['sellprice']-$order['buyprice'])*$order['wave']*$order['onumber'];
			}else{
				$orderid['ykzj']=($order['sellprice']-$order['buyprice'])*$order['wave']*$order['onumber'];
			}
		}else{
			if($order['cid']==1){			
	       		$orderid['ykzj']=($order['buyprice']-$order['sellprice'])*$order['wave']*$order['onumber'];
			}else{
				$orderid['ykzj']=($order['buyprice']-$order['sellprice'])*$order['wave']*$order['onumber'];
			}
		}*/
		$orderid['ykzj'] = $order['ploss'];
        
        //百分比
        $orderid['bfb']=$orderid['ykzj']/$orderid['jc']*100;

        //本单盈余
        $orderid['bdyy']=$order['uprice']*$order['onumber']+$orderid['ykzj'];
        //平仓收入
        $orderid['pdsr']=$orderid['bdyy']-$order['feeprice']*$order['onumber'];
        $this->assign('order',$orderid);
        $this->display();
    }
     //收支明细列表(显示日志记录)
    public function drevenue(){
        $uid=$_SESSION['uid'];
        $count =M('journal')->where('uid='.$uid.' and jtype !="返点"')->count();
        $pagecount = 5;
        $page = new \Think\Page($count , $pagecount);
        $page->parameter = $row; //此处的row是数组，为了传递查询条件
        $page->setConfig('first','首页');
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $page->setConfig('last','尾页');
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%  第 '.I('p',1).' 页/共 %TOTAL_PAGE% 页 ( '.$pagecount.' 条/页 共 %TOTAL_ROW% 条)');
        $show = $page->show();
        $list = M('journal')->where('uid='.$uid.' and jtype !="返点"')->order('jtime desc, balance asc' )->limit($page->firstRow.','.$page->listRows)->select();  
        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->display();
    }
     //收支详细页
    public function revenueid(){
        $jno=I('jno');
        $order=M('journal')->where('jno='.$jno)->find();
        $fee=M('journal')->where('oid='.$order['oid']." and jtype='建仓'")->getField('jfee');
        $this->assign('order',$order);
        $this->assign('fee',$fee);
        $this->display();
    }
    //购买商品，获取信息，生成订单表。
    public function addorder(){
		//商品id
        $mypid=I('post.mypid');
		if(getIsbuy($mypid) == 1){
			$this->ajaxReturn(100);
		}
        $tq=C('DB_PREFIX');
        //数量
        $mysum=I('post.mysum');
        //所用费用
        $myfy=I('post.myfy');
        //方向
        $myfx=I('post.myfx');
         //手续费
        $mysxf=I('post.mysxf');		
        //体验卷值
        $mytyj=I('post.mytyj');
        //商品id
        $mypid=I('post.mypid');
        //入仓价
        $mygetpeice=I('post.mygetpeice');
        /*
        * 添加订单表。修改对应体验卷价格的状态。添加日志表。扣除用户账号余额
        * 添加订单之前再次判断账户余额（后续写） 
        */
        
     
        $uid=$_SESSION['uid'];
		$username = $_SESSION['husername'];
        //获取账户余额
        $user1=M('accountinfo')->where('uid='.$uid)->find();
        //根据商品id查询商品
        $goods=M('productinfo')->where('pid='.$mypid)->find();
		if(($myfy+$mysxf)>$user1['balance'])
		{
			$this->ajaxReturn(1);
		}
        //随机生成订单号
        $orderno=  $this->build_order_no();
        $order=M('order');
        //编写订单需要的数据
        $data['uid']=$uid;
        $data['pid']=$mypid;
        if ($myfx=='涨') {
           $data['ostyle']=0; 
        }else{
           $data['ostyle']=1; 
        }
        
        $data['buytime']=date(time());
        $data['onumber']=$mysum;
        $data['ostaus']=0;
        $data['fee']=$mysxf;
        if($mytyj==1){
          $data['eid']=1;  
          $data['fee']=0;
        }
        $data['buyprice']=$mygetpeice;
        $data['orderno']=$orderno;
        $data['ptitle']=$goods['ptitle'];

        $orderid = $order->add($data);
        if ($orderid) {
            //扣除用户账号金额，若是体验卷则不扣除。
            $accoun=M('accountinfo');
            $user=$accoun->where('uid='.$uid)->find();
            if($mytyj==0){
                $accoun->aid=$user['aid'];
                $accoun->uid=$uid;
                $accoun->balance=$user['balance']-$myfy-$mysxf;
                $accoun->save();
            }
            //判断是否使用优惠卷，然后改变优惠卷状态
            if($mytyj==1){
               //查询使用体验卷的信息
              $experienceinfo= M('experienceinfo');
              $tiyan=$experienceinfo->join($tq.'experience on '.$tq.'experienceinfo.exid='.$tq.'experience.eid')->where($tq.'experienceinfo.uid='.$uid.' and '.date(time()).' < '.$tq.'experience.endtime and '.$tq.'experienceinfo.getstyle=0 and '.$tq.'experience.eprice='.$goods['uprice'])->find();   
               $experienceinfo->exid=$tiyan['exid'];
               $experienceinfo->getstyle=1;
               $experienceinfo->save();
            }
            //添加日志表
            //随机生成订单号
            $orderno=  $this->build_order_no();
            $myjournal=M('journal');								
            $journal['jno']=$orderno;										//订单号
			$journal['uid'] = $uid;											//用户id
			$journal['jtype'] = '建仓';										//类型	
			$journal['jtime'] = date(time());								//操作时间
			$journal['jincome'] = $myfy;									//收支金额【要予以删除】
			$journal['number'] = $mysum;									//数量
			$journal['remarks'] = $goods['ptitle'];							//产品名称	
			$journal['balance'] = $user['balance']-$myfy-$mysxf;			//账户余额	
			$journal['jstate'] = 0;											//盈利还是亏损
			$journal['jusername'] = $username;								//用户名
			$journal['jostyle'] = $data['ostyle'];							//涨、跌
			$journal['juprice'] = $myfy/$mysum;								//单价
			$journal['jfee'] = $mysxf;										//手续费
			$journal['jbuyprice'] = $mygetpeice;							//入仓价
			$journal['jaccess'] = ($myfy+$mysxf)-($myfy+$mysxf)*2;			//支持金额，求负数
			$journal['oid'] = $orderid;										//改订单流水的订单id
			
            $myjournal->add($journal);
			$order->where('oid='.$orderid)->setField('commission',$journal['balance']);
        }else{
            $orderid=0;
        }
       $this->ajaxReturn($orderid); 
    }
    //判断是否购买过此类商品
    public function judgment(){
        //商品id
        $mypid=I('post.mypid');
        $uid=$_SESSION['uid'];
        $porder = M('order')->where('uid='.$uid.' and pid='.$mypid.' and selltime=0')->select();
        if(isset($porder))
        {
            $this->ajaxReturn(99); 
            //echo "<script>alert('亲，您已经购买了此产品')</script>";
        }else{
            $this->ajaxReturn(100); 
        }
    }
    //查询订单信息(接收修改后的订单，或者直接平仓，或者购买完后平仓，3中情况)
    public function orderid(){
        $tq=C('DB_PREFIX');
        $orderid=I('orderid');
        $order=M('order')->join($tq.'productinfo on '.$tq.'order.pid='.$tq.'productinfo.pid')
        ->join($tq.'catproduct on '.$tq.'productinfo.cid='.$tq.'catproduct.cid')->where('oid='.$orderid)->find();
    
        $this->assign('order',$order);
 
    
        $this->display();
    }
    //修改订单的止盈和止亏
    public function edityk(){
        $order=M('order');
        $order->oid=I('post.oid');
        $order->endprofit=I('post.zy');
        $order->endloss=I('post.zk');
        $order->save();
        $this->redirect('Detailed/orderid', array('orderid' =>I('post.oid')));
    }
    //获取随时的动态值，计算盈亏金额和盈余数据
    public function orderxq(){
        $tq=C('DB_PREFIX');
        $youjia=I('youjia');
		$cid = I('cid');
		if($youjia!=0){
	        $order=M('order')->join($tq.'productinfo on '.$tq.'order.pid='.$tq.'productinfo.pid')->where($tq.'order.oid='.I('oid'))->find();
	        $orderid=$order;
	        //建仓金额
	        if ($order['eid']==0) {
	             $orderid['jc']=  round($order['uprice']*$order['onumber'],1);
	            //判断是买张还是买跌。0涨，1跌
	            if ( $orderid['ostyle']==0) {
	                 //盈亏资金
	                 if($cid==1){
	                 	$orderid['ykzj']=round(($youjia-$order['buyprice'])*$order['onumber']*1*$order['wave'],2);	
	                 }else{
	                 	$orderid['ykzj']=round(($youjia-$order['buyprice'])*$order['onumber']*$order['wave'],2);
	                 }
	                 //本单盈余
	                 $orderid['bdyy']=round($orderid['jc']+$orderid['ykzj'],1);
	                 //盈亏百分百
	                 $orderid['ykbfb']=$orderid['ykzj']/ $orderid['jc']*1; 
	            }else{
	                //盈亏资金
	                if($cid==1){
	                 	$orderid['ykzj']=round(($order['buyprice']-$youjia)*$order['onumber']*1*$order['wave'],2);	
               		}else{
                 		$orderid['ykzj']=round(($order['buyprice']-$youjia)*$order['onumber']*$order['wave'],2);
                 	}
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
	                if($cid==1){
	                 	$orderid['ykzj']=round(($youjia-$order['buyprice'])*$order['onumber']*1*$order['wave'],2);	
               		}else{
                 		$orderid['ykzj']=round(($youjia-$order['buyprice'])*$order['onumber']*$order['wave'],2);
                 	}
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
					if($cid==1){
	                 	$orderid['ykzj']=round(($order['buyprice']-$youjia)*$order['onumber']*1*$order['wave'],2);	
               		}else{
                 		$orderid['ykzj']=round(($order['buyprice']-$youjia)*$order['onumber']*$order['wave'],2);
                 	}
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
	        
	        $this->ajaxReturn($orderid);
        }
    }

    //平仓
    public function updateore(){
         //获取账户余额
        $uid=$_SESSION['uid'];
		if($uid < 0){
			$this->ajaxReturn("平仓失败，稍后平仓");
		}
		$users = D('userinfo');
		$username = $_SESSION['husername'];
        $user=M('accountinfo')->where('uid='.$uid)->find();
        //获取传递过来的值
        $oid=I('post.oid');
		$mypid = M("order")->where("oid=".$oid)->getField("pid");
		if(getIsbuy($mypid) == 1){
			$this->ajaxReturn(9);
		}
		$mm=M('order')->where('oid='.$oid)->getField('ostaus');
		if($mm == 1){
			$this->ajaxReturn(19);
		}
        //现在油价
        $youjia=I('post.youjia');
        if($youjia<=0)
        {  
                
        }
        else{
        //结余的金额，需要给当前用户的账户添加
        $bdyy=I('post.bdyy');
         //建仓金额
        $jiancj=I('post.jiancj'); 
        //盈亏资金
        $ykzj=I('post.ykzj'); 
		//产品单价
		$uprice = I('post.uprice');
        //先修改订单信息，返回成功信息后修改账户余额和添加日志记录
        $orderno= $this->build_order_no();
        $tq=C('DB_PREFIX');
        $myorder=M('order')->join($tq.'productinfo on '.$tq.'order.pid='.$tq.'productinfo.pid')->where($tq.'order.oid='.$oid)->find();
        $order=M('order');
        $order->selltime=date(time());
        $order->ostaus=1;
        $order->sellprice=$youjia;
        //盈亏资金
        $order->ploss=$ykzj;
        //手续费
        // $fee = $myorder['feeprice']*$myorder['onumber'];
        $fee = $fee;
        $msg= $order->save();	
		$uid = $myorder['uid'];
        if ($msg) {
            $myprice=M('accountinfo')->where('uid='.$uid)->find();
            $acco= M('accountinfo');
            $acco->uid=$uid;
            $acco->balance=$myprice['balance']+$bdyy;
            $acco->save();
			//根据商品id查询商品
            $goods=M('productinfo')->where('pid='.$myorder['pid'])->find();
            //添加平仓日志表
            //随机生成订单号
            $myjournal=M('journal');
			$journal['jno']=$orderno;										//订单号
			$journal['uid'] = $uid;											//用户id
			$journal['jtype'] = '平仓';										//类型	
			$journal['jtime'] = date(time());								//操作时间
			$journal['jincome'] = $bdyy;									//收支金额【要予以删除】
			$journal['number'] = $myorder['onumber'];						//数量			
			$journal['remarks'] = $goods['ptitle'];							//产品名称	
			$journal['balance'] = $user['balance']+$bdyy;					//账户余额	
			if ($bdyy>$jiancj){
                  $journal['jstate']=1;										//盈利还是亏损
            }else{
                  $journal['jstate']=0;
            }			
			$journal['jusername'] = $username;								//用户名
			$journal['jostyle'] = $myorder['ostyle'];						//涨、跌
			$journal['juprice'] = $uprice;									//单价
			$journal['jfee'] = 0;										//手续费
			$journal['jbuyprice'] = $myorder['buyprice'];					//入仓价
			$journal['jsellprice'] = $youjia;								//平仓价
			$journal['jaccess'] = $bdyy;									//出入金额
			$journal['jploss'] = $ykzj;										//出入金额
			$journal['oid'] = $oid;											//改订单流水的订单id
			$journal['explain'] = $otype.'平仓';
            $myjournal->add($journal);
			$order->where('oid='.$oid)->setField('commission',$journal['balance']);
        }else{
           $msg="平仓失败，稍后平仓";
        }

        $this->ajaxReturn($msg); 
        }        
    }
    public function newrate(){
        $pid=$_REQUEST['pid'];
    $rate=M("productinfo")->field("rate")->where("pid=".$pid)->find();
    $this->ajaxReturn($rate);   
    }
    //随机生成订单编号
    function build_order_no(){
        return date(time()).substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 3);
    }
        

    
}

<?php
namespace Home\Controller;
class IndexController extends CommonController {
  

    public function index(){
        if(isset($_SESSION['uid'])) {
            $tq=C('DB_PREFIX');
            $uid=$_SESSION['uid'];
            //账号余额
            $userimg=M('userinfo')->where('uid='.$uid)->find();
            $price=M('accountinfo')->where('uid='.$uid)->find();
            //账号体验卷数
            // $endtime=date(time());
            //$exper=M('experienceinfo')->join($tq.'experience on '.$tq.'experienceinfo.exid='.$tq.'experience.eid')->where($tq.'experienceinfo.uid='.$uid.' and '.$endtime.' < '.$tq.'experience.endtime and '.$tq.'experienceinfo.getstyle=0')->count();			
            $user['price']=round($price['balance'],2);
            // $user['exper']=$exper;
            $user['username']=$userimg['username'];
            // $user['portrait']=$userimg['portrait'];
            $this->assign('user',$user);
       
        }else{
			 $this->redirect('User/login');
		}
        // $catgood=M('catproduct')->select();
    	$goods=M('productinfo')->select();
        $uorder=$this->userorder();

        //$news=$this->information();
        // $focus=$this->focus();
        // $notices=$this->notice();
        $isopen=$this->isopen();

        $rate=M("productinfo")->field("rate")->group("cid")->select();
		$count = M('order')->where('uid='.$uid.' and ostaus=0 and is_hide=0')->count();
        $this->assign('isopen',$isopen);
        $this->assign('nolist',$uorder);
        $this->assign('count',$count);
 
		$this->assign('rate',$rate);       	 
        //$this->assign('news',$news);
 
        // $this->assign('focus',$focus);
        // $this->assign('notices',$notices);
    	// $this->assign('catgood',$catgood);
    	$this->assign('goods',$goods);
		$this->display();
    }
	public function autoref(){
		$data['uid'] = $_SESSION['uid'];
		$data['ostaus'] = 0;
		$data['is_hide'] =0;
		$count = M('order')->where($data)->count();
		echo $count;die;
	}
    //查询网站是否关闭，关闭则不能购买，并且现价变成休市
    public function isopen(){
        $stye=M('webconfig')->select();
        return $stye[0]['isopen'];
    }
    //显示最新资讯
    public function information(){
        $news=M('newsinfo')->where('ncategory=1')->order('nid desc')->limit(3)->select();
    //     echo "<pre>";
    // var_dump($news);die;
        return $news;
    }
    //显示财经要闻Focus
    public function focus(){
    $news=M('newsinfo')->where('ncategory=2')->order('nid desc')->limit(3)->select();
    return $news;
    }
    //显示系统公告Notice
    public function notice(){
    $news=M('newsinfo')->where('ncategory=3')->order('nid desc')->limit(3)->select();
    return $news;
    }
    //获取动态油的价格，显示到页面
   public function price(){
         $source=file_get_contents("xh/you.txt");
         $msg = explode(',',$source);
         $myprice[0] = round(str_replace('price:', '',str_replace('"','',$msg[1])));//最新
         $myprice[8] = round(str_replace('jk:', '',str_replace('"','',$msg[4])));//今开
         $myprice[7] = round(str_replace('zk:', '',str_replace('"','',$msg[5])));//昨开
         $myprice[4] = round(str_replace('zg:', '',str_replace('"','',$msg[6])));//最高
         $myprice[5] = round(str_replace('zd:', '',str_replace('"','',$msg[7])));//最低
         $myprice[12] = $myprice[0]-$myprice[8];
         $this->ajaxReturn($myprice);
    }
    //获取动态白银的价格，显示到页面
    public function byprice(){
         $source=file_get_contents("xh/baiyin.txt");
         $msg = explode(',',$source);
         $myprice[0] = round(str_replace('price:', '',str_replace('"','',$msg[1])));//最新
         $myprice[8] = round(str_replace('jk:', '',str_replace('"','',$msg[4])));//今开
         $myprice[7] = round(str_replace('zk:', '',str_replace('"','',$msg[5])));//昨开
         $myprice[4] = round(str_replace('zg:', '',str_replace('"','',$msg[6])));//最高
         $myprice[5] = round(str_replace('zd:', '',str_replace('"','',$msg[7])));//最低
         $myprice[12] = $myprice[0]-$myprice[8];
         $this->ajaxReturn($myprice);
    }
    //获取动态铜的价格，显示到页面
    public function toprice(){
         $source=file_get_contents("xh/tong.txt");
         $msg = explode(',',$source);
         $myprice[0] = round(str_replace('price:', '',str_replace('"','',$msg[1])));//最新
         $myprice[8] = round(str_replace('jk:', '',str_replace('"','',$msg[4])));//今开
         $myprice[7] = round(str_replace('zk:', '',str_replace('"','',$msg[5])));//昨开
         $myprice[4] = round(str_replace('zg:', '',str_replace('"','',$msg[6])));//最高
         $myprice[5] = round(str_replace('zd:', '',str_replace('"','',$msg[7])));//最低
         $myprice[12] = $myprice[0]-$myprice[8];
         $this->ajaxReturn($myprice);
    }
    //根据传回来的id获取商品的信息
    public function selectid(){
        $tq=C('DB_PREFIX');
        $pid=I('post.pid');
        $uid=$_SESSION['uid'];
        //获取当前id对应的商品
        $good=M('productinfo')->where('pid='.$pid)->find();
        //查询用户时候有对应的体验卷
       $sum=M('experienceinfo')->join($tq.'experience on '.$tq.'experienceinfo.exid='.$tq.'experience.eid')->where($tq.'experienceinfo.uid='.$uid.' and '.date(time()).' < '.$tq.'experience.endtime and '.$tq.'experienceinfo.getstyle=0 and '.$tq.'experience.eprice='.$good['uprice'])->count();
      
        $good['sum']=$sum;
        $this->ajaxReturn($good);   
    }


    public function newrate(){
   $rate=M("productinfo")->field("rate")->group("cid")->select();
    $this->ajaxReturn($rate);   
    }

    public function cancel(){
    
        $oid=$_REQUEST['oid'];
        $jiancj=$_REQUEST['jiancj'];
        $youjia=$_REQUEST['youjia'];
        $bdyy=0;
        $ykzj=$_REQUEST['ykzj'];
        $uprice=$_REQUEST['uprice'];
        //$data['is_hide']=1;
      //M("order")->where("oid=$oid")->save($data);
		echo updateore($oid,$youjia,$bdyy,$jiancj,$ykzj,$uprice); 
 }

function puttxt(){
     $youjia=$_REQUEST['youjia'];
     $baiyinjia=$_REQUEST['baiyinjia'];
     $tojia=$_REQUEST['tojia'];
     file_put_contents("xh/you.txt", $youjia);file_put_contents("xh/baiyin.txt", $baiyinjia);file_put_contents("xh/tong.txt", $tojia);
}


    //查询当前用户正在交易的订单
    public function userorder(){
        $tq=C('DB_PREFIX');
        $uid = $_SESSION['uid'];
        $userorders=M('order')->join($tq.'productinfo on '.$tq.'order.pid='.$tq.'productinfo.pid')
        ->join($tq.'catproduct on '.$tq.'productinfo.cid='.$tq.'catproduct.cid')->where($tq.'order.uid='.$uid.' and ostaus=0 and is_hide=0')->order($tq.'catproduct.cid asc')->select();
        return $userorders;
    }
	//查询订单信息(接收修改后的订单，或者直接平仓，或者购买完后平仓，3中情况)
    public function orderid(){
        $tq=C('DB_PREFIX');
        $orderid=I('orderid');
        $order=M('order')->join($tq.'productinfo on '.$tq.'order.pid='.$tq.'productinfo.pid')
        ->join($tq.'catproduct on '.$tq.'productinfo.cid='.$tq.'catproduct.cid')->where('oid='.$orderid)->find();    	
    	$order['expend'] = $order['onumber']*$order['uprice'];	//支出
    	$order['paytime'] = date('Y-m-d',$order['buytime']);
		$this->ajaxReturn($order);
    }
	//修改订单的止盈和止亏
    public function edityk(){
        $order=M('order');
        $order->oid=I('post.oid');
        $order->endprofit=I('post.zy');
        $order->endloss=I('post.zk');
        $order->save();
        $this->redirect('Index/index');
    }

    public function refresh(){
		$bao=M("journal")->field("jtime")->where("jtype='爆仓'")->order("jtime desc")->limit(1)->find();
		$jtime=$bao['jtime'];
		$time=time();
		$lefttime=$time-$jtime;
		if($lefttime<=10){
			echo 1;
		}else{
			echo 2;
		}
    }
   //每个产品每天每人最多建仓15次
   public function ifexceed()
   {
	   $uid = $_SESSION['uid'];
	   $pid = $_REQUEST['mypid'];
	   $map['uid']=$uid;
	   $map['pid']=$pid;
	   $time = date("Y-m-d",time());
	   $lefttime = strtotime($time." 00:00:00");
	   $righttime = strtotime($time." 23:59:59");
	   $map['buytime']=array('between',"$lefttime,$righttime");
	   $data['num'] = M('order')->where($map)->count();
	   $data['count'] = M('order')->where($map)->sum("onumber");
	   $data['scount']=30-$data['count'];
	   $this->ajaxReturn($data);
   }
}
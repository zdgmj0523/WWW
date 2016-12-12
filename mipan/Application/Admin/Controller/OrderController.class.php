<?php
// 本类由系统自动生成，仅供测试用途
namespace Admin\Controller;
use Think\Controller;
class OrderController extends Controller {
    public function ocontent()
	{
		//判断用户是否登陆
		$user= A('Admin/User');
		$user->checklogin();
		$order = D('order');
		$users = D('userinfo');
		$binfo = D('bankinfo');
		$pinfo = D('productinfo');
		$manager = D('managerinfo');
		$account = D('accountinfo');
		//获取订单id
		$oid = I('get.oid');
		//查询订单数据基本信息
		$oinfo = $order->where('oid='.$oid)->find();		
		//客户信息
		$uinfo = $users->where('uid='.$oinfo['uid'])->find();
		//商品信息
		$goods = $pinfo->where('pid='.$oinfo['pid'])->find();
		//银行卡信息
		$bank = $binfo->where('uid='.$oinfo['uid'])->field('bnkmber')->find();
		//身份证信息
		$mger = $manager->where('uid='.$oinfo['uid'])->field('mname,brokerid')->find();
		//用户账户信息
		$acount = $account->where('uid='.$oinfo['uid'])->field('balance')->find();
		
		$this->assign('oinfo',$oinfo);
		$this->assign('uinfo',$uinfo);
		$this->assign('goods',$goods);
		$this->assign('bank',$bank);
		$this->assign('mger',$mger);
		$this->assign('acount',$acount);
		$this->display();
	}
	public function olist(){
		//判断用户是否登陆
		$user= A('Admin/User');
		$user->checklogin();

		$tq=C('DB_PREFIX');
		$order = D('order');
		$pinfo = D('productinfo');
		$step = I('get.step');
		//重命名数据库字段名，以免多表查询字段重复
		$liestr = $tq.'order.uid as uid,'.$tq.'order.selltime as selltime,'.$tq.'userinfo.username as username,'.$tq.'order.buytime as buytime,'.$tq.'order.ptitle as ptitle,'.$tq.'order.commission as commission,'.$tq.'order.oid as oid,'.$tq.'order.ploss as ploss,'.$tq.'order.onumber as onumber,'.$tq.'order.ostyle as ostyle,'.$tq.'order.ostaus as ostaus,'.$tq.'order.fee as fee,'.$tq.'order.pid as pid,'.$tq.'order.buyprice as buyprice,'.$tq.'order.sellprice as sellprice,'.$tq.'order.orderno as orderno,'.$tq.'accountinfo.balance as balance,'.$tq.'productinfo.cid as cid,'.$tq.'productinfo.wave as wave';
		//die;
		if($step == "search"){
			
			//获取订单号，生产模糊条件
			$orderno = I('post.orderno');
			//获取用户名，生产模糊条件
			$username = I('post.username');
			//获取订单时间
			$buytime = I('post.buytime');
			//获取订单类型
			$ostyle = I('post.ostyle');
			//获取订单盈亏
			$ploss = I('post.ploss');
			//获取订单状态
			$ostaus = I('post.ostaus');
			if($orderno){
				$where['orderno'] = array('like','%'.I('post.orderno').'%');
			}
			if($username){
				$where['username'] = array('like','%'.I('post.username').'%');
			}
			if($buytime){
				$today = date("Y-m-d",strtotime($buytime));
				$today = explode('-', $today);
				$begintime = mktime(0,0,0,$today[1],$today[2],$today[0]);
				$endtime = mktime(23,59,59,$today[1],$today[2],$today[0]);
				$where['buytime'] = array('between',array($begintime,$endtime));
			}
			if($ostyle!=""){
				$where['ostyle'] = $ostyle;	
			}
			if($ploss=='0'){
				$where['ploss'] = array('egt','0');
			}else if($ploss=='1'){
				$where['ploss'] = array('lt','0');
			}
			if($ostaus!=""){
				$where['ostaus'] = $ostaus;	
			}
//			$this->ajaxReturn($ploss);	
			
			$orders = $order->join($tq.'userinfo on '.$tq.'order.uid='.$tq.'userinfo.uid','left')->join($tq.'accountinfo on '.$tq.'accountinfo.uid='.$tq.'userinfo.uid','left')->join($tq.'productinfo on '.$tq.'order.pid='.$tq.'productinfo.pid','left')->field($liestr)->order($tq.'order.oid desc')->where($where)->select();
			//$this->ajaxReturn($order->getLastSql());
			foreach($orders as $k => $v){
				$orders[$k]['buytime'] = date("Y-m-d H:m",$orders[$k]['buytime']);
			}
			if($orders){
				$this->ajaxReturn($orders);	
			}else{
				$this->ajaxReturn("null");
			}
			
		}else{
			//分页
			$count = $order->count();
	        $pagecount = 15;
	        $page = new \Think\Page($count , $pagecount);
	        $page->parameter = $row; //此处的row是数组，为了传递查询条件
	        $page->setConfig('first','首页');
	        $page->setConfig('prev','&#8249;');
	        $page->setConfig('next','&#8250;');
	        $page->setConfig('last','尾页');
	        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
	        $show = $page->show();
			//订单列表
			$orders = $order->join($tq.'userinfo on '.$tq.'order.uid='.$tq.'userinfo.uid','left')->join($tq.'accountinfo on '.$tq.'accountinfo.uid='.$tq.'userinfo.uid','left')->join($tq.'productinfo on '.$tq.'order.pid='.$tq.'productinfo.pid','left')->field($liestr)->order($tq.'order.oid desc')->limit($page->firstRow.','.$page->listRows)->select();
			//今日统计
			$today = date("Y-m-d",time());
			$today = explode('-', $today);
			$begintime = mktime(0,0,0,$today[1],$today[2],$today[0]);
			$endtime = mktime(23,59,59,$today[1],$today[2],$today[0]);
			$where['buytime'] = array('between',array($begintime,$endtime));
			$statis = $order->join($tq.'productinfo on '.$tq.'order.pid='.$tq.'productinfo.pid')->field('onumber,uprice,ploss')->where($where)->select();
			foreach($statis as $k => $v){
				$total = $v['onumber']*$v['uprice'];
				$totals += $total;
				$number = $v['onumber'];
				$num += $number;
				$ploss = $v['ploss'];
				$tploss += $ploss;
			}
			//echo $v['onumber']*$v[''];
			$this->assign('totals',$totals);
			$this->assign('tploss',$tploss);
			$this->assign('num',$num);
			$this->assign('page',$show);
			$this->assign('orders',$orders);			
		}
		//统计
		//$today=strtotime(date('Y-m-d 00:00:00'));
		//create_time
		$this->display();
	}
	public function tlist(){
		//判断用户是否登陆
		$user= A('Admin/User');
		$user->checklogin();
		$tq=C('DB_PREFIX');
		$journal = D('journal');
		$user = D('userinfo');
		$huilist = $user->where("otype = 2")->select();
		$this->assign("huilist",$huilist);
		$where = "";
		$orderno = $_GET['orderno'];
		//获取用户名，生产模糊条件
		$username = $_GET['username'];
		//获取订单时间
		$starttime = date('Y-m-d',strtotime($_GET["starttime"]));
		$endtime = date('Y-m-d',strtotime($_GET["endtime"]));
		//获取订单类型
		$ostyle = $_GET['ostyle'];
		//获取订单盈亏
		$ploss = $_GET['ploss'];
		//获取订单状态
		$ostaus = $_GET['ostaus'];
		$oid = $_GET['oid'];
		if($oid)
		{
			$oids = getDownuids($oid);
			$where['uid'] = array("in",implode(',',$oids));
			$sea['oid'] = $oid;
		}
		if($orderno){
			$where['jno'] = array('like','%'.$orderno.'%');
		}
		if($username){
			$where['jusername'] = array('like','%'.$_GET["username"].'%');
			$sea['username'] = $_GET["username"];
		}
		if($_GET["starttime"] && $_GET["endtime"]){
			$starttime = strtotime($starttime." 00:00:00");
			$endtime = strtotime($endtime." 23:59:59");
			$where['jtime'] = array('between',array($starttime,$endtime));
			$sea['starttime'] = $_GET["starttime"];
			$sea['endtime'] = $_GET["endtime"];
		}
		
		if($ostyle!=""){
			$where['jostyle'] = array("eq",$ostyle);
			$sea['ostyle'] = $ostyle;
		}
		if($ploss=='0'){
			$where['jploss'] = array("egt",0);
			$sea['ploss'] = 0;
		}else if($ploss=='1'){
			$where['jploss'] = array("lt",0);	
			$sea['ploss'] = 1;
		}
		if($ostaus!=""){
			if($ostaus == '4')
			{
				$where['jtype'] = '建仓';	
				$sea['ostaus'] = 4;
			}
			if($ostaus == '1')
			{
				$where['jtype'] = '平仓';
				$sea['ostaus'] = 1;				
			}
			if($ostaus == '2')
			{
				$where['jtype'] = '爆仓';
				$sea['ostaus'] = 2;
			}
			if($ostaus == '3')
			{
				$where['jtype'] = '隔夜利息扣除';
				$sea['ostaus'] = 3;
			}
			if($ostaus == '5')
			{
				$where['jtype'] = '止盈';
				$sea['ostaus'] = 5;
			}
			if($ostaus == '6')
			{
				$where['jtype'] = '止损';
				$sea['ostaus'] = 6;
			}
		}
		//$where['jtype'] = array("neq",'返点');
		$this->assign("sea",$sea);
		$count = $journal->order('jtime desc,balance asc')->where($where)->count();				
		$pagecount = 10;
		$page = new \Think\Page($count , $pagecount);
		$page->parameter = $sea; //此处的row是数组，为了传递查询条件
		$page->setConfig('first','首页');
		$page->setConfig('prev','上一页');
		$page->setConfig('next','下一页');
		$page->setConfig('last','尾页');
		$page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
		$start = $page->firstRow;
		$end = $page->listRows;
		$tlist = $journal->order('jtime desc,balance DESC')->limit($start,$end)->where($where)->select();
		foreach($tlist as $key=>$val)
		{
			$ooid = M("userinfo")->where("uid=".$val['uid'])->getField('oid');
			$tlist[$key]['oid']=M("userinfo")->where("uid=".$ooid)->getField('username');
			$tlist[$key]['iddd']=$val['oid'];
		}
		$hlist = $journal->order('jtime desc')->where($where)->select();
		foreach($hlist as $key=>$val)
		{
			$sums = $val['juprice']*$val['number'];
			$sumbuymoney+=$sums;
			$sumploss += $val['jploss'];
			$sumfee += $val['jfee'];
			$sumgefee += $val['gefee'];
		}
		
		$show = $page->show();	
		// var_dump($page->firstRow);die;
		$this->assign('sumgefee',$sumgefee);
		$this->assign('sumbuymoney',$sumbuymoney);
		$this->assign('sumploss',round($sumploss,2));
		$this->assign('sumfee',round($sumfee,2));
		$this->assign('tlist',$tlist);
		$this->assign('show',$show);
		$this->display();
	}
	 public function daochu(){
		//判断用户是否登陆
		$user= A('Admin/User');
		$user->checklogin();
		$tq=C('DB_PREFIX');
		$journal = D('journal');
		$user = D('userinfo');
		$huilist = $user->where("otype = 2")->select();
		$this->assign("huilist",$huilist);
		$where = "";
		$orderno = $_GET['orderno'];
		//获取用户名，生产模糊条件
		$username = $_GET['username'];
		//获取订单时间
		$starttime = date('Y-m-d',strtotime($_GET["starttime"]));
		$endtime = date('Y-m-d',strtotime($_GET["endtime"]));
		//获取订单类型
		$ostyle = $_GET['ostyle'];
		//获取订单盈亏
		$ploss = $_GET['ploss'];
		//获取订单状态
		$ostaus = $_GET['ostaus'];
		$oid = $_GET['oid'];
		if($oid)
		{
			$oids = getDownuids($oid);
			$where['uid'] = array("in",implode(',',$oids));
			$sea['oid'] = $oid;
		}
		if($orderno){
			$where['jno'] = array('like','%'.$orderno.'%');
		}
		if($username){
			$where['jusername'] = array('like','%'.$_GET["username"].'%');
			$sea['username'] = $_GET["username"];
		}
		if($_GET["starttime"] && $_GET["endtime"]){
			$starttime = strtotime($starttime." 00:00:00");
			$endtime = strtotime($endtime." 23:59:59");
			$where['jtime'] = array('between',array($starttime,$endtime));
			$sea['starttime'] = $_GET["starttime"];
			$sea['endtime'] = $_GET["endtime"];
		}
		
		if($ostyle!=""){
			$where['jostyle'] = array("eq",$ostyle);
			$sea['ostyle'] = $ostyle;
		}
		if($ploss=='0'){
			$where['jploss'] = array("egt",0);
			$sea['ploss'] = 0;
		}else if($ploss=='1'){
			$where['jploss'] = array("lt",0);	
			$sea['ploss'] = 1;
		}
		if($ostaus!=""){
			if($ostaus == '4')
			{
				$where['jtype'] = '建仓';	
				$sea['ostaus'] = 4;
			}
			if($ostaus == '1')
			{
				$where['jtype'] = '平仓';
				$sea['ostaus'] = 1;				
			}
			if($ostaus == '2')
			{
				$where['jtype'] = '爆仓';
				$sea['ostaus'] = 2;
			}
			if($ostaus == '3')
			{
				$where['jtype'] = '返点';
				$sea['ostaus'] = 3;
			}
		}
		$tlist = $journal->order('jtime desc')->where($where)->select();
		$data[0] = array(
			'编号','用户','上级','类型','操作时间','产品信息','数量（手）','方向','金额','手续费','买价','卖价',"出入金","盈亏","隔夜利息"
		);
		foreach($tlist as $key=>$val)
		{
			$data[$key+1][] = $val['jno'];
			$data[$key+1][] = $val['jusername'];
			$ooid = M("userinfo")->where("uid=".$val['uid'])->getField('oid');
			$data[$key+1][]=M("userinfo")->where("uid=".$ooid)->getField('username');
			$data[$key+1][] = $val['jtype'];
			$data[$key+1][] = date("Y-m-d H:i:s",$val['jtime']);
			$data[$key+1][] = $val['remarks'];
			$data[$key+1][] = $val['number'];
			if($val['jostyle'] == 1)
			{
				$data[$key+1][] = "买跌";
			}else{
				$data[$key+1][] = "买涨";
			}
			$data[$key+1][] = $val['juprice']*$val['number'];
			$data[$key+1][] = $val['jfee'];
			$data[$key+1][] = $val['jbuyprice'];
			$data[$key+1][] = $val['jsellprice'];
			$data[$key+1][] = $val['jaccess'];
			$data[$key+1][] = $val['jploss'];
			$data[$key+1][] = $val['gefee'];
		}
		$name='Excelfile';  //生成的Excel文件文件名
		$res=$this->push($data,$name);
	}
	public function push($data,$name){
		import("Excel.class.php");
		$excel = new Excel();
		$excel->download($data,$name);
	}
}
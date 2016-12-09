<?php

namespace Admin\Controller;
use Think\Controller;
class UserController extends Controller {
	
	
	//管理员登陆
	public function signin()
	{
	 	if(IS_POST){
	 		header("Content-type: text/html; charset=utf-8");
			
			$user = D("userinfo");
				
			//查询条件
			$where = array();
			$where['username'] = I('post.username');
			$where['ustatus'] = 0;
			//$where['ustatus'] = "1";
			$result = $user->where($where)->field("uid,upwd,username,utel,utime,otype,ustatus,vertus")->find();			
			//验证用户
			if(empty($result)){
				$this->error('登录失败,用户名不存在!');
			}else{	
				$map['lastlog'] = time();
				M('userinfo')->where('username ="'.I('post.username').'"')->save($map);
				if($result['upwd'] == md5(I('post.password'))){
					if($result['vertus'] != 1)
					{
						$this->error('对不起,你还没通过平台审核，不能登录!');
					}
					//session
					
					if($result['otype']==2&&$result['ustatus']==0)
					{    
					    session('cuid',$result['uid']);
					    session('userotype',$result['otype']);
						session('newusername',$result['username']);
						
						$this->success('登录成功,正跳转至系统会员单位首页...', U('Ucenter/Ordinary/agentlist'));
					}
					
					elseif ($result['otype']==3&&$result['ustatus']==0)
				
					{
						session('userid',$result['uid']);
						session('userotype',$result['otype']);
						session('username',$result['username']);  
						$this->success('登录成功,正跳转至系统管理员首页...', U('Admin/Index/index'));
					}elseif ($result['otype']==4&&$result['ustatus']==0)
				
					{
						session('cuid',$result['uid']);
						session('newusername',$result['username']);  
						session('userotype',$result['otype']);
						$this->success('登录成功,正跳转至系统普通会员首页...', U('Ucenter/Account/agentlist'));
					}
					elseif ($result['otype']==1&&$result['ustatus']==0)
				
					{
						session('cuid',$result['uid']);
						session('newusername',$result['username']);  
						session('userotype',$result['otype']);  
						$this->success('登录成功,正跳转至系统经纪人首页...', U('Ucenter/Trade/tradelist'));
					}
					else{
						$this->error('登录失败,用户名不存在!');
					}
					
				}
				
			}
	 	}else{
	 		$this->display();
		}
	}
	
	//管理员信息
	public function personalinfo(){
		$this->checklogin();
		
		$uid = $_SESSION['uid'];
		$user = D('userinfo');
		$person = $user->where('uid='.$uid)->find();
		
		$this->assign('person',$person);
		$this->display();
	}
	
	/**
    * 用户注销
    */
    public function signinout()
    {
        // 清楚所有session
        header("Content-type: text/html; charset=utf-8");
        session(null);
        redirect(U('User/signin'), 2, '正在退出登录...');
    }
	
	
	//会员列表
    public function ulist()
    {
    	$this->checklogin();
    	$tq=C('DB_PREFIX');
    	$user = D('userinfo');
		$order = D('order');
		$account = D('accountinfo');
		$huilist = $user->where("otype = 2")->select();
		$this->assign("huilist",$huilist);
		
		$field = $tq.'userinfo.username as username,'.$tq.'userinfo.uid as uid,'.$tq.'userinfo.utel as utel,'.$tq.'userinfo.address as address,'.$tq.'userinfo.utime as utime,'.$tq.'userinfo.oid as oid,'.$tq.'userinfo.managername as managername,'.$tq.'userinfo.lastlog as lastlog,'.$tq.'accountinfo.balance as balance,'.$tq.'userinfo.otype as otype';
		$phone = $_GET['phone'];
		$username = $_GET['username'];
		$starttime = $_GET['starttime'];
		$endtime = $_GET['endtime'];
		$oid = $_GET['oid'];
		if($phone)
		{
			$where[$tq.'userinfo.utel'] = array('like','%'.$_GET["phone"].'%');
			$sea['phone'] = $_GET["phone"];
		}
		$username = $_GET['username'];
		if($username)
		{
			$where[$tq.'userinfo.username'] = array('like','%'.$_GET["username"].'%');
			$sea['username'] = $_GET["username"];
		}
		if($_GET["starttime"] && $_GET["endtime"]){
			$starttime = strtotime($starttime." 00:00:00");
			$endtime = strtotime($endtime." 23:59:59");
			$where[$tq.'userinfo.utime'] = array('between',array($starttime,$endtime));
			$sea['starttime'] = $_GET["starttime"];
			$sea['endtime'] = $_GET["endtime"];
		}
		if($oid)
		{
			$oids = getDownuids($oid);
			$where[$tq.'userinfo.uid'] = array("in",implode(',',$oids));
			$sea['oid'] = $oid;
		}
		$this->assign("sea",$sea);
		//分页
		$count = $user->where($where)->count();
		$pagecount = 10;
		$page = new \Think\Page($count , $pagecount);
		$page->parameter = $sea; //此处的row是数组，为了传递查询条件
		$page->setConfig('first','首页');
		$page->setConfig('prev','&#8249;');
		$page->setConfig('next','&#8250;');
		$page->setConfig('last','尾页');
		$page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
		$show = $page->show();
		//查询用户和账户信息
		$ulist = $user->join($tq.'accountinfo on '.$tq.'userinfo.uid='.$tq.'accountinfo.uid','left')->where($where)->field($field)->order($tq.'userinfo.uid desc')->limit($page->firstRow.','.$page->listRows)->select();
		$summoney = $user->join($tq.'accountinfo on '.$tq.'userinfo.uid='.$tq.'accountinfo.uid','left')->where($where)->field($field)->order($tq.'userinfo.uid desc')->sum("balance");
		//循环用户id，获取该用户的所有订单数
		foreach($ulist as $k => $v){
			//$v['uid'];
			$ocount = $order->where($tq.'order.uid='.$v['uid'])->count();
			$ulist[$k]['ocount'] = $ocount;
			$ulist[$k]['balance'] = number_format($ulist[$k]['balance'],2);
			$ulist[$k]['managername']=M('userinfo')->where('uid='.$v['oid'])->getField('username');
		}
		$this->assign('page',$show);
		$this->assign('ulist',$ulist);
    	

		//统计
		//用户数量
    	$userCount = $user->where('ustatus=0')->count();
		//交易手数
		$orders = $order->where('ostaus=1')->field('onumber')->select();
		//所有用户账户余额统计
		$acc = $account->field('balance')->select();
		$onumber = 0;
		$anumber = 0;
		foreach($orders as $k => $v){
			$onumber += $orders[$k]['onumber'];
		}
		foreach($acc as $k => $v){
			$anumber += $acc[$k]['balance'];
		}
		$anumber = number_format($anumber,2);
		$this->assign('summoney',$summoney);
		$this->assign('onumber',$onumber);
		$this->assign('anumber',$anumber);
		$this->assign('ucount',$userCount);
		$this->display();
	}
	//会员列表
    public function daochu()
    {
    	$this->checklogin();
    	$tq=C('DB_PREFIX');
    	$user = D('userinfo');
		$order = D('order');
		$account = D('accountinfo');
		$huilist = $user->where("otype = 2")->select();
		$this->assign("huilist",$huilist);
		
		$field = $tq.'userinfo.username as username,'.$tq.'userinfo.uid as uid,'.$tq.'userinfo.utel as utel,'.$tq.'userinfo.address as address,'.$tq.'userinfo.utime as utime,'.$tq.'userinfo.oid as oid,'.$tq.'userinfo.managername as managername,'.$tq.'userinfo.lastlog as lastlog,'.$tq.'accountinfo.balance as balance,'.$tq.'userinfo.otype as otype';
		$phone = $_GET['phone'];
		$username = $_GET['username'];
		$starttime = $_GET['starttime'];
		$endtime = $_GET['endtime'];
		$oid = $_GET['oid'];
		if($phone)
		{
			$where[$tq.'userinfo.utel'] = array('like','%'.$_GET["phone"].'%');
			$sea['phone'] = $_GET["phone"];
		}
		$username = $_GET['username'];
		if($username)
		{
			$where[$tq.'userinfo.username'] = array('like','%'.$_GET["username"].'%');
			$sea['username'] = $_GET["username"];
		}
		if($_GET["starttime"] && $_GET["endtime"]){
			$starttime = strtotime($starttime." 00:00:00");
			$endtime = strtotime($endtime." 23:59:59");
			$where[$tq.'userinfo.utime'] = array('between',array($starttime,$endtime));
			$sea['starttime'] = $_GET["starttime"];
			$sea['endtime'] = $_GET["endtime"];
		}
		if($oid)
		{
			$oids = getDownuids($oid);
			$where[$tq.'userinfo.uid'] = array("in",implode(',',$oids));
			$sea['oid'] = $oid;
		}
		
		//查询用户和账户信息
		$ulist = $user->join($tq.'accountinfo on '.$tq.'userinfo.uid='.$tq.'accountinfo.uid','left')->where($where)->field($field)->order($tq.'userinfo.uid desc')->select();
		$data[0] = array('编号','用户','手机号','创建时间','上级','订单数量','帐户余额','会员类别');
		foreach($ulist as $k => $v){
			$data[$k+1][] = $v['uid'];
			$data[$k+1][] = $v['username'];
			$data[$k+1][] = $v['utel'];
			$data[$k+1][] = date("Y-m-d H:i:s",$v['utime']);
			$data[$k+1][]=M("userinfo")->where("uid=".$v['oid'])->getField('username');
			$ocount = $order->where($tq.'order.uid='.$v['uid'])->count();
			$data[$k+1][] = $ocount;
			$data[$k+1][] = number_format($v['balance'],2);
			if($v['otype'] == 2 )
			{
				$data[$k+1][] = "会员单位";
			}else if($v['otype'] == 1){
				$data[$k+1][] = "经纪人";
			}else if($v['otype'] == 3){
				$data[$k+1][] = "管理员";
			}else{
				$data[$k+1][] = "客户";
			}
		}
		$name='Excelfile';  //生成的Excel文件文件名
		$res=$this->push($data,$name);
	}
	public function push($data,$name){
		import("Excel.class.php");
		$excel = new Excel();
		$excel->download($data,$name);
	}
	public function recommend(){
		$recommend=M("webconfig")->field("recommend,recommend2")->where("id=1")->find();
 
		$this->assign("recommend",$recommend['recommend']);
		$this->assign("recommend2",$recommend['recommend2']);
		$this->display();
	}
	public function act_recommend(){
 
		$recommend=$_POST['recommend'];
		$recommend2=$_POST['recommend2'];
		$data['recommend']=$recommend;
		$data['recommend2']=$recommend2;
		M("webconfig")->where("id=1")->save($data);
		$this->success("修改成功",U("index/index"));
	}
	//代理商申请列表
	public function agentlist(){
		$tq=C('DB_PREFIX');
    	$user = D('userinfo');
    	$managerinfo = D('managerinfo');
    	$list=$user->join($tq.'managerinfo on '.$tq.'userinfo.uid='.$tq.'managerinfo.uid')->where($tq.'userinfo.agenttype=1')->order($tq.'userinfo.uid desc')->select();

		$this->assign('list',$list);
		$this->display();
	}
	//处理代理申请是否通过
	public function edituser(){
		$user = D('userinfo');
		$uid=I('get.uid');
		$otype=I('get.otype');

		if ($otype==0) {
			//拒绝
			$date['uid']=$uid;
			$date['agenttype']=0;
			if($user->save($date)){
				M('managerinfo')->where('uid='.$uid)->delete();
			}

		}else{
			//通过
			$date['uid']=$uid;
			$date['agenttype']=2;
			$date['otype']=1;
			$user->save($date);
		}
	   $this->redirect('User/agentlist');
	}
	public function jingjishen()
	{
		$uid = $_REQUEST['uid'];
		$row = M('userinfo')->where('uid='.$uid)->find();
		if($row['agenttype'] == 1)
		{
			echo 3;exit;
		}
		$map['agenttype'] = 1;
		$t = M('userinfo')->where('uid='.$uid)->save($map);
		if($t)
		{
			echo 1;exit;
		}else{
			echo 2;exit;
		}
	}
	//修改会员
	public function updateuser()
    {
    	//检测用户是否登陆
    	$this->checklogin();
		
		//实例化数据表
		$tq=C('DB_PREFIX');    
		$user = D('userinfo');
		$manager = D('managerinfo');
		$bank = D('bankinfo');
		$acinfo = D('accountinfo');
		$order = D('order');
		//判断如果是post，执行修改用户方法，否则显示视图
		if(IS_POST){
			$uid = I('post.uid');				//用户id
			$username = I('post.username');		//用户名
			$mname = I('post.mname');			//真实姓名
			$upwd = I('post.upwd');				//密码
			$otype = I('post.otype');			//用户类型
			if($otype=='客户'){
				$otype=0;
			}else if($otype=='会员'){
				$otype=2;
			}else if($otype=='代理商'){
				$otype=1;
			}
			$utel = I('post.utel');				//手机号码
			$brokerid = I('post.brokerid');		//身份证号码
			$banknumber = I('post.banknumber');	//银行卡号
			$branch = I('post.branch');			//开户行地址
			$bankname = I('post.bankname');		//所属银行
			$province = I('post.province');		//省份
			$city = I('post.city');				//城市
			$busername = I('post.busername');	//持卡人		
			$balance = I('post.balance');		//账户余额
			//取值，如果没有做修改，保存原有值
			$users = $user->where('uid='.$uid)->find();
			$mginfo = $manager->where('uid='.$uid)->find();
			$banks = $bank->where('uid='.$uid)->find();
			$accinfo = $acinfo->where('uid='.$uid)->find();
			
			//判断密码是否为空
			if(!empty($upwd)){
				$users['upwd'] = md5($upwd);
			}
			//判断电话是否为空
			if(!empty($utel)){
				$users['utel'] = $utel;
			}
			//判断真实姓名是否为空
			if(!empty($mname)){
				$mginfo['mname'] = $mname;
			}
			//判断身份证号码是否为空
			if(!empty($brokerid)){	
				$mginfo['brokerid'] = $brokerid;
			}
			//判断银行卡号是否为空
			if(!empty($banknumber)){
				$banks['banknumber'] = $banknumber;
			}
			//判断开户行地址是否为空
			if(!empty($branch)){
				$banks['branch'] = $branch;
			}
			//判断所属银行是否为空
			if(!empty($bankname)){
				$banks['bankname'] = $bankname;
			}
			//判断省份是否为空
			if(!empty($province)){
				$banks['province'] = $province;
			}
			//判断城市是否为空
			if(!empty($city)){
				$banks['city'] = $city;
			}
			//判断持卡人是否为空
			if(!empty($busername)){
				$banks['busername'] = $busername;
			}
			//判断账户余额
			if(!empty($balance)){
				$accinfo['balance'] = $balance;
			}
			//修改用户基本信息
			$resultUser = $user->where('uid='.$uid)->save($users);
			//修改用户真实信息
			$resultManager = $manager->where('uid='.$uid)->save($mginfo);
			//修改账户余额
			$resultAcinfo = $acinfo->where('uid='.$uid)->setField('balance',$balance);
			//判断用户是否存在银行卡信息
			if($banks['uid']==$uid){
				//修改银行卡信息
				$resultBank = $bank->where('uid='.$uid)->save($banks);				
			}else{
				$banks['uid'] = $uid;
				//添加银行卡信息
				$resultBank = $bank->add($banks);
			}
			if($resultUser || $resultManager || $resultBank || $resultAcinfo){
				$this->success('修改成功');
			}else if($resultUser==0 || $resultManager==0 || $resultBank==0 || $resultAcinfo==0){
				$this->error('未做任何修改');
			}else{
				$this->error('修改失败');
			}
			
		}else{
			//根据获取的用户id查询该用户的信息，展示视图
			$uid=I('get.uid');
			//需要查询的字段
			$field = $tq.'userinfo.uid as uid,'.$tq.'userinfo.username as username,'.$tq.'userinfo.oid as oid,'.$tq.'userinfo.managername as managername,'.$tq.'userinfo.otype as otype,'.$tq.'userinfo.utel as utel,'.$tq.'managerinfo.mname as mname,'.$tq.'managerinfo.brokerid as brokerid,'.$tq.'bankinfo.bankname as bankname,'.$tq.'bankinfo.province as province,'.$tq.'bankinfo.city as city,'.$tq.'bankinfo.branch as branch,'.$tq.'bankinfo.banknumber as banknumber,'.$tq.'bankinfo.bankname as bankname,'.$tq.'bankinfo.busername as busername,'.$tq.'accountinfo.balance as balance,'.$tq.'userinfo.utime as utime,'.$tq.'userinfo.ustatus as ustatus'; 
			//修改用户显示的数据
			$userme = $user->join($tq.'managerinfo on '.$tq.'userinfo.uid='.$tq.'managerinfo.uid','left')->join($tq.'bankinfo on '.$tq.'userinfo.uid='.$tq.'bankinfo.uid','left')->join($tq.'accountinfo on '.$tq.'accountinfo.uid='.$tq.'bankinfo.uid','left')->field($field)->where($tq.'userinfo.uid='.$uid)->find();
			
			$sys = $user->field('otype')->where('uid='.$userme['oid'])->find();
			//账户余额
			$account = $acinfo->field('balance,frozen')->where('uid='.$uid)->find();
			$account['balance'] = number_format($account['balance'],2);
			//个人账户佣金
			
			
			$this->assign('sys',$sys);
			$this->assign('userme',$userme);
			$this->assign('account',$account);
			$this->display();
		}
		
	}
	public function index()
	{
		$this->display('ulist');		
	}
	/**
	 * 添加会员
	 * */
	public function addmenber(){
		
		$this->display();	
	}
	/**
	 * 添加客户
	 * */
	public function adduser(){
		
		$this->display();	
	}
	public function userdel(){
		$user = D('userinfo');
		//单个删除
		$uid = I('get.uid');
		$result = $user->where('uid='.$uid)->delete();
		if($result!==FALSE){
			$this->success("成功删除！",U("User/ulist"));
		}else{
			$this->error('删除失败！');
		}
	}
	public function daochu1(){
		$this->checklogin();
		//读出提现和充值列表
		$balance = D('balance');
		$tq=C('DB_PREFIX');
        $step = I('get.step');
		$user = M("userinfo");
		//查询多条记录
       	$field = $tq.'userinfo.username as username,'.$tq.'balance.uid as uid,'.$tq.'balance.bpid as bpid,'.$tq.'balance.bptype as bptype,'.$tq.'balance.bptime as bptime,'.$tq.'balance.bpprice as bpprice,'.$tq.'balance.remarks as remarks,'.$tq.'balance.isverified as isverified,'.$tq.'accountinfo.balance as balance,'.$tq.'balance.cltime as cltime,'.$tq.'userinfo.utime as utime,'.$tq.'userinfo.utel as utel,'.$tq.'userinfo.oid as oid';
		//过滤搜索
		$huilist = $user->where("otype = 2")->select();
		$this->assign("huilist",$huilist);
		$where = "";
		//获取用户名，生产模糊条件
		$username = $_GET['username'];
		//获取订单时间
		$starttime = date('Y-m-d',strtotime($_GET["starttime"]));
		$endtime = date('Y-m-d',strtotime($_GET["endtime"]));
		//获取订单类型
		$type = $_GET['type'];
		//获取订单盈亏
		$ploss = $_GET['ploss'];
		//获取订单状态
		$ostaus = $_GET['ostaus'];
		$oid = $_GET['oid'];
		if($oid)
		{
			$oids = getDownuids($oid);
			$where[$tq.'userinfo.uid'] = array("in",implode(',',$oids));
			$sea['oid'] = $oid;
		}
		if($username){
			$where[$tq.'userinfo.username'] = array('like','%'.$_GET["username"].'%');
			$sea['username'] = $_GET["username"];
		}
		if($_GET["starttime"] && $_GET["endtime"]){
			$starttime = strtotime($starttime." 00:00:00");
			$endtime = strtotime($endtime." 23:59:59");
			$where[$tq.'balance.bptime'] = array('between',array($starttime,$endtime));
			$sea['starttime'] = $_GET["starttime"];
			$sea['endtime'] = $_GET["endtime"];
		}
		
		if($type!=""){
			$where[$tq.'balance.bptype'] = array("eq",$type);
			$sea['type'] = $type;
		}
		if($where){
			$rechargelist = $balance->join($tq.'userinfo on '.$tq.'balance.uid='.$tq.'userinfo.uid','left')->join($tq.'accountinfo on '.$tq.'balance.uid='.$tq.'accountinfo.uid','left')->field($field)->where("(wp_balance.bptype = '充值' and wp_balance.isverified = 1) or (wp_balance.bptype = '提现')")->where($where)->order($tq.'balance.bptime desc')->select();
		}else{
			$rechargelist = $balance->join($tq.'userinfo on '.$tq.'balance.uid='.$tq.'userinfo.uid','left')->join($tq.'accountinfo on '.$tq.'balance.uid='.$tq.'accountinfo.uid','left')->field($field)->where("(wp_balance.bptype = '充值' and wp_balance.isverified = 1) or (wp_balance.bptype = '提现')")->order($tq.'balance.bptime desc')->select();
		}
		$data[0] = array('编号','用户','上级','手机号','金额','创建时间','帐户余额','类型','是否通过');
		foreach($rechargelist as $k => $v){
			$data[$k+1][] = $v['uid'];
			$data[$k+1][] = $v['username'];
			$data[$k+1][]=M("userinfo")->where("uid=".$v['oid'])->getField('username');
			$data[$k+1][] = $v['utel'];
			$data[$k+1][] = $v['bpprice'];
			$data[$k+1][] = date("Y-m-d H:i:s",$v['utime']);
			$data[$k+1][] = number_format($v['balance'],2);
			$data[$k+1][] = $v['bptype'];
			if($v['isverified'] == 1 )
			{
				$data[$k+1][] = "已通过";
			}else{
				$data[$k+1][] = "未通过";
			}
		}
		$name='Excelfile';  //生成的Excel文件文件名
		$res=$this->push($data,$name);	
	}
	public function recharge(){
		$this->checklogin();
		//读出提现和充值列表
		$balance = D('balance');
		$tq=C('DB_PREFIX');
        $step = I('get.step');
		$user = M("userinfo");
		//查询多条记录
       	$field = $tq.'userinfo.username as username,'.$tq.'balance.uid as uid,'.$tq.'balance.bpid as bpid,'.$tq.'balance.bptype as bptype,'.$tq.'balance.bptime as bptime,'.$tq.'balance.bpprice as bpprice,'.$tq.'balance.remarks as remarks,'.$tq.'balance.isverified as isverified,'.$tq.'accountinfo.balance as balance,'.$tq.'balance.cltime as cltime';
		//过滤搜索
		$huilist = $user->where("otype = 2")->select();
		$this->assign("huilist",$huilist);
		$where = "";
		//获取用户名，生产模糊条件
		$username = $_GET['username'];
		//获取订单时间
		$starttime = date('Y-m-d',strtotime($_GET["starttime"]));
		$endtime = date('Y-m-d',strtotime($_GET["endtime"]));
		//获取订单类型
		$type = $_GET['type'];
		//获取订单盈亏
		$ploss = $_GET['ploss'];
		//获取订单状态
		$ostaus = $_GET['ostaus'];
		$oid = $_GET['oid'];
		if($oid)
		{
			$oids = getDownuids($oid);
			$where[$tq.'userinfo.uid'] = array("in",implode(',',$oids));
			$sea['oid'] = $oid;
		}
		if($username){
			$where[$tq.'userinfo.username'] = array('like','%'.$_GET["username"].'%');
			$sea['username'] = $_GET["username"];
		}
		if($_GET["starttime"] && $_GET["endtime"]){
			$starttime = strtotime($starttime." 00:00:00");
			$endtime = strtotime($endtime." 23:59:59");
			$where[$tq.'balance.bptime'] = array('between',array($starttime,$endtime));
			$sea['starttime'] = $_GET["starttime"];
			$sea['endtime'] = $_GET["endtime"];
		}
		
		if($type!=""){
			$where[$tq.'balance.bptype'] = array("eq",$type);
			$sea['type'] = $type;
		}
		if($where){
			$count = $balance->join($tq.'userinfo on '.$tq.'balance.uid='.$tq.'userinfo.uid','left')->join($tq.'accountinfo on '.$tq.'balance.uid='.$tq.'accountinfo.uid','left')->field($field)->where("(wp_balance.bptype = '充值' and wp_balance.isverified = 1) or (wp_balance.bptype = '提现')")->where($where)->count();
		}else{
			$count = $balance->join($tq.'userinfo on '.$tq.'balance.uid='.$tq.'userinfo.uid','left')->join($tq.'accountinfo on '.$tq.'balance.uid='.$tq.'accountinfo.uid','left')->field($field)->where("(wp_balance.bptype = '充值' and wp_balance.isverified = 1) or (wp_balance.bptype = '提现')")->count();
		}
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
		$this->assign("sea",$sea);
		if($where){
			$rechargelist = $balance->join($tq.'userinfo on '.$tq.'balance.uid='.$tq.'userinfo.uid','left')->join($tq.'accountinfo on '.$tq.'balance.uid='.$tq.'accountinfo.uid','left')->field($field)->where("(wp_balance.bptype = '充值' and wp_balance.isverified = 1) or (wp_balance.bptype = '提现')")->where($where)->limit($start,$end)->order($tq.'balance.bptime desc')->select();
		}else{
			$rechargelist = $balance->join($tq.'userinfo on '.$tq.'balance.uid='.$tq.'userinfo.uid','left')->join($tq.'accountinfo on '.$tq.'balance.uid='.$tq.'accountinfo.uid','left')->field($field)->where("(wp_balance.bptype = '充值' and wp_balance.isverified = 1) or (wp_balance.bptype = '提现')")->limit($start,$end)->order($tq.'balance.bptime desc')->select();
		}
		foreach($rechargelist as $k => $v){
			$rechargelist[$k]['bptime'] = date("Y-m-d H:i:s",$rechargelist[$k]['bptime']);
			if($rechargelist[$k]['cltime']==""){
				$rechargelist[$k]['cltime']="";
			}else{
				$rechargelist[$k]['cltime'] = date("Y-m-d H:i:s",$rechargelist[$k]['cltime']);	
			}
			$oid = M("userinfo")->where("uid=".$rechargelist[$k]['uid'])->getField('oid');
			$rechargelist[$k]['oid'] = M("userinfo")->where("uid=".$oid)->getField('username');
			$sumsheng += $rechargelist[$k]['balance'];
			$summoeny += $rechargelist[$k]['bpprice'];
		}
		$show = $page->show();	
		$this->assign("rechargelist",$rechargelist);
		$this->assign("summoeny",$summoeny);
		$this->assign("sumsheng",$sumsheng);
		$this->assign("page",$show);
		$this->display();   	
	}

	//更新充值提现状态
	public function upbalance(){
		$this->checklogin();
		//获取参数
		$bpid=I('post.bpid');
		$isverified = I('post.isverified');
		$remarks = I('post.remarks');
		$rebpprce=I('post.rebpprce');
		$userid=I('post.userid');
		$balance = D('balance');
		$accountinfo=M('accountinfo');
		$cltime = time();
		$type = M("balance")->where("bpid = ".$bpid)->getField("bptype");
		if($isverified=="1"){
			if($type == "充值")
			{
				$isver=$balance->where('bpid='.$bpid)->setField(array('isverified'=>'1','remarks'=>$remarks,'cltime'=>$cltime));//1是同意	
				$date=$accountinfo->where('uid='.$userid)->find();
				$date['balance']=$date['balance']+$rebpprce;
				$accountinfo->where('uid='.$userid)->save($date);
			}else{
				$isver=$balance->where('bpid='.$bpid)->setField(array('isverified'=>'1','remarks'=>$remarks,'cltime'=>$cltime));//1是同意	
				// $date=$accountinfo->where('uid='.$userid)->find();
				// $date['balance']=$date['balance']-$rebpprce;
				// $accountinfo->where('uid='.$userid)->save($date);		
			}			
		}else if($isverified=="0"){
			$isver=$balance->where('bpid='.$bpid)->setField(array('isverified'=>'2','remarks'=>$remarks,'cltime'=>$cltime,));//2是拒绝
		}else{
			$isver=$balance->where('bpid='.$bpid)->setField(array('isverified'=>'0','remarks'=>$remarks,'cltime'=>$cltime));//0是初始值
		}
		
		if($isver){
			$this->ajaxReturn("success");	
		}else{
			$this->ajaxReturn("null");
		}
		
	}
	
	
	
	
	public function checklogin()
	{
		$uid=islogin(); 
		if(!$uid)
		{
		    $this->error('请登录','/index.php/Admin/User/signin');
		}
	}
	public function dongtis(){
		$this->checklogin();
		$uid=$_REQUEST['uid'];
		$types=$_REQUEST['types'];
		/*var_dump($uid."---".$types);
		exit;*/
		if($types==1){
			$a['ustatus']=1;
			$dongtis=M("userinfo")->where("uid = '".$uid."'")->save($a);
		}else if($types==2){
			$a['ustatus']=0;
			$dongtis=M("userinfo")->where("uid = '".$uid."'")->save($a);
		}
		if($dongtis){
			$this->success("操作成功!");
		}else{
			$this->error('操作失败,请重试!');
		}
	}
}
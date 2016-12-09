<?php
// 本类由系统自动生成，仅供测试用途
namespace Ucenter\Controller;
use Ucenter\Controller\CommonController;
class TradeController extends CommonController{
	public function tradelist()
    {
    	$tq=C('DB_PREFIX');
    	$userinfo=M('userinfo');
    	$order = M('order');
		$journal = D('journal');
    	//默认查询全部
		$otype = M("userinfo")->where("uid=".$_SESSION['cuid'])->getField("otype");
		if($otype == 2)
		{
			$t = 4;
		}else if($otype == 4)
		{
			$t = 1;
		}
		//过滤搜索
		$huilist = $userinfo->where("otype = ".$t)->select();
		$this->assign("huilist",$huilist);
		$where = "";
		//获取用户名，生产模糊条件
		$username = $_GET['jusername'];
		//获取订单时间
		$starttime = date('Y-m-d',strtotime($_GET["starttime"]));
		$endtime = date('Y-m-d',strtotime($_GET["endtime"]));
		//获取订单状态
		$ostaus = $_GET['ostaus'];
		$oid = $_GET['oid'];
		if($oid)
		{
			$oids = getDownuids($oid);
			$where['uid'] = array("in",implode(',',$oids));
			$sea['oid'] = $oid;
		}
		if($username){
			$where['jusername'] = array('like','%'.$_GET["jusername"].'%');
			$sea['jusername'] = $_GET["jusername"];
		}
		if($_GET["starttime"] && $_GET["endtime"]){
			$starttime = strtotime($starttime." 00:00:00");
			$endtime = strtotime($endtime." 23:59:59");
			$where['jtime'] = array('between',array($starttime,$endtime));
			$sea['starttime'] = $_GET["starttime"];
			$sea['endtime'] = $_GET["endtime"];
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
		$this->assign("sea",$sea);
		$count = $journal->order('jtime desc')->where($where)->count();				
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
		$tlist = $journal->order('jtime desc')->limit($start,$end)->where($where)->select();
		foreach($tlist as $key=>$val)
		{
			$ooid = M("userinfo")->where("uid=".$val['uid'])->getField('oid');
			$tlist[$key]['oid']=M("userinfo")->where("uid=".$ooid)->getField('username');
		}
		$hlist = $journal->order('jtime desc')->where($where)->select();
		foreach($hlist as $key=>$val)
		{
			$sums = $val['juprice']*$val['number'];
			$sumbuymoney+=$sums;
			$sumploss += $val['jploss'];
			$sumfee += $val['jfee'];
		}
		
		$show = $page->show();	
		$this->assign('sumbuymoney',$sumbuymoney);
		$this->assign('sumploss',$sumploss);
		$this->assign('sumfee',$sumfee);
		$this->assign('ordlist',$tlist);
		$this->assign('show',$show);
		$this->display();
    }
	public function recharge(){
		//读出提现和充值列表
		$balance = D('balance');
		$tq=C('DB_PREFIX');
		$user = M("userinfo");
		//查询多条记录
       	$field = $tq.'userinfo.username as username,'.$tq.'balance.uid as uid,'.$tq.'balance.bpid as bpid,'.$tq.'balance.bptype as bptype,'.$tq.'balance.bptime as bptime,'.$tq.'balance.bpprice as bpprice,'.$tq.'balance.remarks as remarks,'.$tq.'balance.isverified as isverified,'.$tq.'accountinfo.balance as balance,'.$tq.'balance.cltime as cltime';
		$otype = M("userinfo")->where("uid=".$_SESSION['cuid'])->getField("otype");
		if($otype == 2)
		{
			$t = 4;
		}else if($otype == 4)
		{
			$t = 1;
		}
		//过滤搜索
		$huilist = $user->where("otype = ".$t)->select();
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
		}else{
			$oids = getDownuids($_SESSION['cuid']);
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
		$count = $balance->join($tq.'userinfo on '.$tq.'balance.uid='.$tq.'userinfo.uid','left')->join($tq.'accountinfo on '.$tq.'balance.uid='.$tq.'accountinfo.uid','left')->field($field)->where($where)->order($tq.'balance.bptime desc')->count();
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
		$rechargelist = $balance->join($tq.'userinfo on '.$tq.'balance.uid='.$tq.'userinfo.uid','left')->join($tq.'accountinfo on '.$tq.'balance.uid='.$tq.'accountinfo.uid','left')->field($field)->where($where)->limit($start,$end)->order($tq.'balance.bptime desc')->select();
		foreach($rechargelist as $k => $v){
			$rechargelist[$k]['bptime'] = date("Y-m-d H:m",$rechargelist[$k]['bptime']);
			if($rechargelist[$k]['cltime']==""){
				$rechargelist[$k]['cltime']="";
			}else{
				$rechargelist[$k]['cltime'] = date("Y-m-d H:m",$rechargelist[$k]['cltime']);	
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
    public function delord(){
    	header("Content-type: text/html; charset=utf-8");
    	$orderno=I('get.orderno');
    	if ($orderno) {
    		$order=M('order');
	    	$data['display']=1;
	    	if ($order->where('orderno='.$orderno)->save($data)) {
	    		redirect(U('Trade/tradelist'),1, '删除用户成功...');
	    	}else{
	    		redirect(U('Trade/tradelist'),1, '删除用户失败...');
	    	}
    	}
    	redirect(U('Trade/tradelist'),1, '用户订单不存在...');
    }

}
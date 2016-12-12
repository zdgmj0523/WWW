<?php
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
	function islogin(){
		
	  $uid=$_SESSION['userid'];
	  return $uid;
	  
	}
	function getDownuids($uid,$type=1){
		$oid=M('userinfo')->field('uid,username')->where('oid='.$uid." and otype!=0")->select();//查询当前用户下级所有非客户的uid
        for($i=0; $i<count($oid); $i++ ) {
			$oid[$i]=$oid[$i]['uid'];
		}
		
		if(!empty($oid)){//如果有,继续查询下级所有非客户的uid
			$oid1=M('userinfo')->where('oid in('.implode(',',$oid).') and ustatus=0 and otype!=0 and vertus = 1')->select();
			for($i=0; $i<count($oid1); $i++ ) {
				$oid1[$i]=$oid1[$i]['uid'];
			}
		}
		
		if(!empty($oid))
		{
			if(!empty($oid1))
			{
				$olds = array_merge(array_merge($oid,$oid1),array($uid));
			}else{
				$olds = array_merge($oid,array($uid));
			}
		}else{
			$olds = array($uid);
		}
		
		$us = M('userinfo')->where('oid in('.implode(',',array_filter($olds)).') and ustatus=0 and otype=0 and vertus = 1')->select();
		foreach ($us as $key => $value) {
    		$arruid[]=$value['uid'];
    	}
		if($arruid){
			$arruid = array_merge($arruid,$olds);
		}else{
			$arruid = $olds;
		}
	
		return $arruid;
	}
?>
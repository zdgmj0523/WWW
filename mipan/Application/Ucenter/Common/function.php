<?php
function getDownuids($uid,$type=1){
		$oid=M('userinfo')->field('uid,username')->where('oid='.$uid." and otype!=0")->select();//��ѯ��ǰ�û��¼����зǿͻ���uid
        for($i=0; $i<count($oid); $i++ ) {
			$oid[$i]=$oid[$i]['uid'];
		}
		
		if(!empty($oid)){//�����,������ѯ�¼����зǿͻ���uid
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

<?php

namespace Admin\Controller;
use Think\Controller;
class SuperController extends Controller {
	
	//管理员列表
    public function slist()
    {
    	//判断用户是否登陆
		$user= A('Admin/User');
		$user->checklogin();
		
		$users = D('userinfo');
		//分页
		$count = $users->where('otype=3')->count();
        $pagecount = 20;
        $page = new \Think\Page($count , $pagecount);
        $page->parameter = $row; //此处的row是数组，为了传递查询条件
        $page->setConfig('first','首页');
        $page->setConfig('prev','&#8249;');
        $page->setConfig('next','&#8250;');
        $page->setConfig('last','尾页');
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
        $show = $page->show();
		//查询用户和账户信息
		$ulist = $users->where('otype=3')->order(	'uid desc')->limit($page->firstRow.','.$page->listRows)->select();
		
		$this->assign('page',$show);
		$this->assign('ulist',$ulist);
		$this->display();
		
	}
	//添加管理员
	public function sadd()
	{
		//判断用户是否登陆
		$user= A('Admin/User');
		$user->checklogin();
		//实例化userinfo表
		$users = D('userinfo');
		if(IS_POST){
			$data['utime'] = time();
			$data['username'] = I('post.username');
			$data['otype']= 3;
			$data['upwd']= md5(I('post.upwd'));
			$data['utel']=I('post.utel');
			// var_dump($data);die;
			$result = $users->add($data);
			if($result){
				$data1['uid'] = $result;
				M("accountinfo")->add($data1);
				$this->success('添加管理员成功',U('Super/slist'));
			}else{
				$this->error('添加失败');
			}
		}else{
			$this->display();	
		}
	}
	//基本设置
	public function esystem(){
		//判断用户是否登陆
		$user= A('Admin/User');
		$user->checklogin();
		$config = D('webconfig');
		$isopen = I('post.isopen');
		$webname = I('post.webname');
		$notice = I('post.notice');
		$where = "id=1";
		if($isopen!=""){
			if($isopen==0){
				$config->where($where)->setField('isopen','1');
				
				$this->ajaxReturn("开启成功");		
			}else{
				$config->where($where)->setField('isopen','0');
				$this->ajaxReturn("关闭成功");
			}		
		}elseif($webname!=""){
			$result = $config->where($where)->setField('webname',$webname);
			if($result){
				$this->ajaxReturn("修改成功");
			}else{
				$this->ajaxReturn("修改失败");
			}
		}elseif($notice!=""){
			$result = $config->where($where)->setField('notice',$notice);
			if($result){
				$this->ajaxReturn("修改成功");
			}else{
				$this->ajaxReturn("修改失败");
			}
		}else{
			$conf = $config->where($where)->find();		
			$this->assign('conf',$conf);
		}
		$this->display();
	}
	//修改管理员
	public function sedit()
    {
    	//判断用户是否登陆
		$user= A('Admin/User');
		$user->checklogin();
		
		$users = D('userinfo');
		if(IS_POST){
			if($users->create()){
				$uid = I('post.uid'); 
				$data['otype'] = I('post.otype'); 
				$data['ustatus'] = I('post.ustatus');
				$data['oid'] = I('post.oid');
				$data['utime'] = I('post.utime');
				$data['username'] = I('post.username');
				$data['upwd'] = md5(I('post.upwd').$data['utime']);
				$data['utel'] = I('post.utel');
				$result = $users->where('uid='.$uid)->save($data);
				if($result === FALSE){
					$this->error("管理员修改失败！");
				}else{
					$this->success("管理员修改成功",U('Super/slist'));
				}
			}else{
				$this->error($users->getError());
			}
		}else{
			//根据修改管理员的id读取数据
			$uid = I('get.uid');
			$ult = $users->where('uid='.$uid)->find();			
			$this->assign('ult',$ult);
			$this->display();
		}
	}
	//删除管理员
	public function sdel()
    {
    	$user = D('userinfo');
		//单个删除
		$uid = I('get.uid');
		$result = $user->where('uid='.$uid)->delete();
		if($result!==FALSE){
			$this->success("成功删除管理员！",U("Super/slist"));
		}else{
			$this->error('删除失败！');
		}
	}
	//备份数据
	public function backupdb()
	{
		//判断用户是否登录
		$user=A('Admin/User');//实例化其他模块中的方法
		$user->checklogin();
		
		$users=D('userinfo');//获取用户信息
		$username=$users->field('username')->find();
		
		
		
		
		mysql_query("set names 'utf8'");
		$mysql = "set charset utf8;\r\n";
		$q1 = mysql_query("show tables");
		while ($t = mysql_fetch_array($q1))
		{
		$table = $t[0];
		$q2 = mysql_query("show create table `$table`");
		$sql = mysql_fetch_array($q2);
		$mysql .= $sql['Create Table'] . ";\r\n";
		$q3 = mysql_query("select * from `$table`");
		while ($data = mysql_fetch_assoc($q3))
			{
				$keys = array_keys($data);
				$keys = array_map('addslashes', $keys);
				$keys = join('`,`', $keys);
				$keys = "`" . $keys . "`";
				$vals = array_values($data);
				$vals = array_map('addslashes', $vals);
				$vals = join("','", $vals);
				$vals = "'" . $vals . "'";
				$mysql .= "insert into `$table`($keys) values($vals);\r\n";
			} 
		} 
 
		$filename = APP_PATH.'backup/'.date('Y-m-d_H-i-s').".sql"; //存放路径，默认存放到项目最外层
		echo $filename;
		$fp = fopen($filename, 'w');
		fputs($fp, $mysql);
		fclose($fp);
		//echo "数据备份成功";
				//$this->display();
	}
	//还原数据
	public function restoredb()
	{
		
		
		
	}
	
	
	
	
	
	
	
	
	
	
	
}
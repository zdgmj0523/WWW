<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends Controller
{
    var $reapalNotice_url = "";
    var $reapalReturn_url = "";

    public function _initialize(){    
   
    $this->reapalNotice_url="http://" . $_SERVER['HTTP_HOST'] . "/index.php/Home/User/payReapalNotice";
    $this->reapalReturn_url = "http://" . $_SERVER['HTTP_HOST'] . "/index.php/Home/User/payReapalReturn";
    }
	public function veropenid()
	{
		var_dump($_POST);die;
	}
	public function get_real_ip()
	{
		$ip=false;
		if(!empty($_SERVER["HTTP_CLIENT_IP"])){
		  $ip = $_SERVER["HTTP_CLIENT_IP"];
		}
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		  $ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
		  if($ip){
		   array_unshift($ips, $ip); $ip = FALSE;
		  }
		  for($i = 0; $i < count($ips); $i++){
		   if (!eregi ("^(10|172\.16|192\.168)\.", $ips[$i])){
			$ip = $ips[$i];
			break;
		   }
		  }
		}
		return($ip ? $ip : $_SERVER['REMOTE_ADDR']);
	}
	public function is_mobile() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $mobile_agents = Array("240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi","android","applewebkit/525","applewebkit/532","asus","audio","au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu","cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ","fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi","htc","huawei","hutchison","inno","ipad","ipaq","ipod","jbrowser","kddi","kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo","mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-","moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia","nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-","playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo","samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank","sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit","tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin","vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce","wireless","xda","xde","zte");
        $is_mobile = false;
        foreach ($mobile_agents as $device) {
            if (stristr($user_agent, $device)) {
                $is_mobile = true;
                break;
            }
        }
        return $is_mobile;
    }
	public function login()
    { 
		if(!$this->is_mobile()){
			echo "<script>";
			echo "alert('请在微信客户端打开!');";
			echo "window.close();";
			echo "</script>";
			die;
		}
        header("Content-type: text/html; charset=utf-8");
		$wxopenid = $_SESSION['wxopenid'];
		$map123['lastlog'] = time();
		$map123['upip'] =  md5($this->get_real_ip().time());
		M('userinfo')->where('username ="'.I('post.username').'"')->save($map123);
		if(I('post.username') && I('post.password')){
				// 实例化Login对象
				$user = D('userinfo');
				$where = array();
				$where['username'] = I('post.username');
				$result = $user->where($where)->field('uid,username,upwd,utime,openid,vertus,otype')->find();
				if ($result['upwd'] === md5(I('post.password') )) {
					if($result['vertus'] != 1)
					{
						$this->error('对不起,你还没通过平台审核，不能登录!');
					}
					// 存储session
					session('uid', $result['uid']);          // 当前用户id
					session('husername', $result['username']);   // 当前用户昵称
					session('uotype', $result['otype']);   // 当前用户昵称
					session('upip', $map123['upip']);   // 当前用户昵称
					$where['uid'] = session('uid');
					if(!$result['openid'])
					{
						$map['openid'] = $_SESSION['wxopenid'];
						$user->where($where)->save($map);
					}
					// 更新用户登录信息
					
					$this->redirect('Index/index');
				} else {
					$this->error('登录失败,用户名或密码不正确!');
				}
		}
        $userinfo = M('userinfo');
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($user_agent, 'MicroMessenger') === false) {
             //非微信浏览器禁止浏览
            //echo "不是微信";
            $this->assign('is_weixin',0);
            $this->display(); 
        } else {
            //做跳转，拿到openid,第一步跳转链接，
            // if ($_GET['openid']=='') {
            //     $this->assign('is_weixin',1);
            //     $wechat=M('wechat')->find();
            //     $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$wechat['appid']."&redirect_uri=http://mipan.fxicc.com/Extend/weixin.php&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
            //     echo "请在浏览器打开";die;
            //     // echo "<script language='javascript'>window.location='".$url."'</script>";
            // }else{
                $this->assign('is_weixin',0);
                //echo $_GET['openid'];
                //这里做一个判断，客户没有注册，则直接去注册页面，否则去登录页面。
                $openid['openid']=$_GET['openid'];
                $openid['nickname']=$_GET['nickname'];
                $openid['address']=$_GET['address'];
                $openid['portrait']=$_GET['portrait'];
                $openid['utime']=$_GET['subscribe_time'];//时间
                $openid['username']= substr($openid['openid'], -5).time();//登录名
                $openid['usertype']='1';
                $openid['wxtype']='1';
                $userinfoid=$userinfo->where("openid='".$openid['openid']."'")->find();
                //有数据在判断看是否有密码，有账号，没有的话跳转到初始页面，让输入密码，这里是修改方法。
                if ($userinfoid) {
                    if ($userinfoid['upwd']) {
                          //传用户名过去，做隐藏，然后直接登录
                          $this->assign('username',$userinfoid['username']);
                       }else{
                          //注册初始密码页面
                          $this->redirect('User/reg', array('openid' => $openid['openid']), 1, '请设置初始密码...');  
                       }  
                }
                else{
                 //没查询到，就跳转到注册页面，让输入初始密码。生成一个账号。这里是添加账号方法后，赋值跳转到登录页面。
                  if($userinfo->add($openid)){
                      //初始密码页面
                      $this->redirect('User/reg', array('openid' => $openid['openid']), 1, '请设置初始密码...'); 
                      
                    }
                
                 }
                
               // }
              $this->display(); 
           }
            
    }
         public function reapal()
    {
        
        // if ($this->payConfig['reapal']['enable'] == 0) {
        //     exit("对不起，该支付方式被关闭，暂时不能使用!");
        // }
   
 
        header("Content-type: text/html; charset=UTF-8");
        require_once 'Dinpay.Key.php';
        $priKey = openssl_get_privatekey($priKey);
        /////////////////////////////////初始化提交参数//////////////////////////////////////
       
        $merchant_code =   '1118010687';
        $service_type = "direct_pay";
        $interface_version = "V3.0";
        $pay_type = "";
        $sign_type = "RSA-S";
        $input_charset = "UTF-8";
        $notify_url = $this->reapalNotice_url;
        $order_no = "PB" . time() . mt_rand(1000, 999999);
        //订单id[商户网站]
        $order_time = date("Y-m-d H:i:s", time());
        //订单创建时间
        $nummoney = number_format($_POST['money'], 2, ".", "");
        //冲值金额
        $fee = 0;
        //手续费率后台设定
        $feemoney = number_format($fee * $nummoney / 100, 2, ".", "");
 
        $order_amount = $nummoney;
        //最终冲值金额
        $name = mb_convert_encoding("帐户充值", "UTF-8", "UTF-8");
        $product_name = $name;
        //iconv( "UTF-8", "gb2312//IGNORE" ,$this->glo['web_name']."帐户充值");//产品名称
        $product_code = "";
        $product_desc = "";
        $product_num = "";
        $show_url = "";
        $client_ip = "";
        $bank_code = "";
        $redo_flag = "";
        $extend_param = "";
        $extra_return_param = "";
        $return_url = $this->reapalReturn_url;
 
        $signStr = "";
        if ($bank_code != "") {
            $signStr = $signStr . "bank_code=" . $bank_code . "&";
        }
        if ($client_ip != "") {
            $signStr = $signStr . "client_ip=" . $client_ip . "&";
        }
        if ($extend_param != "") {
            $signStr = $signStr . "extend_param=" . $extend_param . "&";
        }
        if ($extra_return_param != "") {
            $signStr = $signStr . "extra_return_param=" . $extra_return_param . "&";
        }
        $signStr = $signStr . "input_charset=" . $input_charset . "&";
        $signStr = $signStr . "interface_version=" . $interface_version . "&";
        $signStr = $signStr . "merchant_code=" . $merchant_code . "&";
        $signStr = $signStr . "notify_url=" . $notify_url . "&";
        $signStr = $signStr . "order_amount=" . $order_amount . "&";
        $signStr = $signStr . "order_no=" . $order_no . "&";
        $signStr = $signStr . "order_time=" . $order_time . "&";
        if ($pay_type != "") {
            $signStr = $signStr . "pay_type=" . $pay_type . "&";
        }
        if ($product_code != "") {
            $signStr = $signStr . "product_code=" . $product_code . "&";
        }
        if ($product_desc != "") {
            $signStr = $signStr . "product_desc=" . $product_desc . "&";
        }
        $signStr = $signStr . "product_name=" . $product_name . "&";
        if ($product_num != "") {
            $signStr = $signStr . "product_num=" . $product_num . "&";
        }
        if ($redo_flag != "") {
            $signStr = $signStr . "redo_flag=" . $redo_flag . "&";
        }
        if ($return_url != "") {
            $signStr = $signStr . "return_url=" . $return_url . "&";
        }
        if ($show_url != "") {
            $signStr = $signStr . "service_type=" . $service_type . "&";
            $signStr = $signStr . "show_url=" . $show_url;
        } else {
            $signStr = $signStr . "service_type=" . $service_type;
        }
        openssl_sign($signStr, $sign_info, $priKey, OPENSSL_ALGO_MD5);

        $sign = base64_encode($sign_info);
 
        $submitdata['sign'] = $sign;
        //接口版本
        $submitdata['merchant_code'] = $merchant_code;
        //接口版本
        $submitdata['bank_code'] = $bank_code;
        //接口版本
        $submitdata['order_no'] = $order_no;
        //接口版本
        $submitdata['order_amount'] = $order_amount;
        //接口版本
        $submitdata['service_type'] = $service_type;
        //接口版本
        $submitdata['input_charset'] = $input_charset;
        //接口版本
        $submitdata['notify_url'] = $notify_url;
        //接口版本
        $submitdata['interface_version'] = $interface_version;
        //接口版本
        $submitdata['sign_type'] = $sign_type;
        //接口版本
        $submitdata['order_time'] = $order_time;
        //接口版本
        $submitdata['product_name'] = $product_name;
        //接口版本
        $submitdata['client_ip'] = $client_ip;
        //接口版本
        $submitdata['extend_param'] = $extend_param;
        //接口版本
        $submitdata['extra_return_param'] = $extra_return_param;
        //接口版本
        $submitdata['pay_type'] = $pay_type;
        //接口版本
        $submitdata['product_code'] = $product_code;
        //接口版本
        $submitdata['product_num'] = $product_num;
        //接口版本
        $submitdata['return_url'] = $return_url;
        //接口版本
        $submitdata['product_desc'] = $product_desc;
        //接口版本
        $submitdata['show_url'] = $show_url;
        //接口版本
        $submitdata['redo_flag'] = $redo_flag;
        //接口版本
        $uid = $_SESSION['uid'];
        $data['bptime'] = date(time());
        $data['bptype'] ='充值';
        $data['uid'] =$uid;
        $data['balanceno'] =$order_no;
        $data['remarks'] ="开始充值";
        $data['bpprice']=I('post.money');
        $data['isverified']=0;
        
        M('balance')->add($data);
  
        $this->create($submitdata, "https://pay.dinpay.com/gateway?input_charset=UTF-8");
        //智付接收地址
    }
		
		//微信支付接口
         public function weixinpay()
		{	$time=time();
			$money = $_POST['money'];
			$uid = $_SESSION['uid'];
			$data['bptime'] = date($time);
			$data['bptype'] ='充值';
			$data['uid'] =$uid;
			$data['balanceno'] =date($time).$time;
			$data['remarks'] ="开始充值";
			$data['bpprice']=$money;
			$data['isverified']=0;
			
			$id=M('balance')->add($data);
			
			
			//$out_trade_no=time();
        // 组合url
		//var_dump(U('Weixin/index'));exit;
			$url=U('Weixin/index/',array('out_trade_no'=>$data['balanceno']));
			// 前往支付
			redirect($url);
			exit;
			
			
			
			
			Header("Location: ".U('weixin/start','id='.$id));
			/*
			
			
			$submitdata['trade_no'] = $data['balanceno'];
			$submitdata['price']    = $money;
			$submitdata['order_id']    = $data['balanceno'];
			
			$this->create($submitdata, "http://mipan.fxicc.com/Extend/weipay/jsapi.php");
			//微信接收地址*/
		}
	private function create($data, $submitUrl)
    {
        $inputstr = "";
        foreach ($data as $key => $v) {
            $inputstr .= '<input type="hidden"  id="' . $key . '" name="' . $key . '" value="' . $v . '"/>';
        }
        $form = '<form action="' . $submitUrl . '" name="pay" id="pay" method="POST">';
        $form .= $inputstr;
        $form .= '</form>';
        $html = '
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml"><head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>请不要关闭页面,支付跳转中.....</title>
        </head><body>
        ';
        $html .= $form;
        $html .= '
        <script type="text/javascript">
            document.getElementById("pay").submit();
        </script>';
        $html .= '</body></html>';
        $this->Mheader('utf-8');
        echo $html;
		
        exit;

    }

 private function create1($data, $submitUrl)
    {
        $inputstr = "";
        foreach ($data as $key => $v) {
            $inputstr .= '<input type="hidden"  id="' . $key . '" name="' . $key . '" value="' . $v . '"/>';
        }
        $form = '<form action="' . $submitUrl . '" name="pay" id="pay" method="POST">';
        $form .= $inputstr;
        $form .= '</form>';
        $html = '
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml"><head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>请不要关闭页面,支付跳转中.....</title>
        </head><body>
        ';
        $html .= $form;
        $html .= '
        <script type="text/javascript">
            document.getElementById("pay").submit();
        </script>';
        $html .= '</body></html>';
        $this->Mheader('utf-8');
        echo $html;
		
        exit;
    }

private function Mheader($type){
    header("Content-Type:text/html;charset={$type}"); 
}

        private function createnid($type, $static)
    {
        return md5("XXXXX@@#\$%" . $type . $static);
    }


       //注册页面
   public function reg()
    {
        
        $openid=I('get.openid'); 
        $oid = I('get.oid');
        $uid = I('get.uid');
        $otype = I('get.otype');
        $this->assign('openid',$openid);
        $this->assign('oid',$oid);
        $this->assign('uid',$uid);
        $this->assign('otype',$otype);
        $this->display();

    }
	public function yanpho()
	{
		$pho = $_POST['pho'];
		$res = M("userinfo")->where("utel = ".$pho)->select();
		if($res)
		{
			echo 1;exit;
		}else{
			echo 0;exit;
		}
	}
    //注册
    public function register()
    {
        if(IS_POST)
        {
            $user = D('userinfo');
            //检查用户名
            header("Content-type: text/html; charset=utf-8");
            //检查手机验证码
            $code = session("regcode");
            $verify = I('post.code');
            if ($code == $verify) {
                /*
                *推广链接时需要在注册时添加一个获取oid的方法，添加进去，作为上线的记录。
                */                 
                $data['username'] = I('post.username');
                $data['utel'] = I('post.utel');
                $data['utime'] = date(time());
                $data['upwd'] = md5(I('post.upwd'));
                $data['oid']=I('post.oid');
                $data['otype']=I('post.otype');
				$newusername = M("userinfo")->where("uid =".$data['oid'])->getField("username");
				$data['managername']=$newusername;
				if(I('post.otype') >= 1)
				{
					$data['vertus']=3;
				}else{
					$data['vertus']=1;
				}
                if(I('post.uid')){
					$data['rid']=I('post.uid');
                }
				$uname = $user->where('username='."'$data[username]'")->find();
				if($uname){
					// $this->ajaxReturn(3);
					// echo 3;exit;
					$this->error('用户名重复！');
				}else{
					//插入数据库
	                if ($uid = $user->add($data)) {
	                    //添加对应的金额表
	                    $acc['uid']=$uid;
	                    $aid = M('accountinfo')->add($acc);
						M("managerinfo")->add($acc);
	                    // $this->ajaxReturn(1);
	                    // echo 1;exit;
						$this->error('注册成功','login');
	                } else {
	                    // $this->ajaxReturn(2);
						 // echo 2;exit;
						 $this->error('注册失败');
	                }
				}
            }else{
               	// $this->ajaxReturn(0);
				// echo 0;exit;
				$this->error('验证码错误');
            }

        }else{
        	$oid = I('get.oid');
        	$com = M('userinfo')->field('comname,uid')->where('uid='.$oid)->find();			
			$this->assign('com',$com);
			$this->display();        	
        }

    }
    //设置初始密码，密码后台可以修改。这里需要创建资金表，创建详细信息表。
    public function myreg(){
        $userinfo=M('userinfo');
        $openid=I('post.openid');
        $user=$userinfo->where("openid='".$openid."'")->find();
        $data['uid']=$user['uid'];
        $data['utime'] = date(time());
        $data['upwd'] = md5(I('post.upwd') . date(time()));
        $data['wxtype']='0';
        if($userinfo->save($data)){
              $brok['uid']=$user['uid'];
              $brok['brokerid']=I('post.brokerid');
              M('managerinfo')->add($brok);
              $accid['uid']=$user['uid'];
              M('accountinfo')->add($accid);
            $this->redirect('User/login');
        }else{
            $this->error('设置失败，请联系管理员');
        }
        
        
    }
    public function mescontent()
    {

        $CheckCode = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
        return $CheckCode;

    }

    //短信验证
    public function smsverify()
    {
		$code = $this->mescontent();
		session("regcode",$code);
		include_once('sms.php');
    }
	//图形验证
	Public function imgverify(){
		$Verify = new \Think\Verify();  
		$Verify->fontSize = 18;  
		$Verify->length   = 4;  
		$Verify->useNoise = false;  
		$Verify->codeSet = '0123456789';  
		$Verify->imageW = 130;  
		$Verify->imageH = 50;  
		ob_end_clean();  
		$Verify->entry();	
	}
	public function tuyan(){
		$Verify = new \Think\Verify();  
		$t = I("post.t");
		echo $Verify->check($t);die;
	}
    //会员中心
    public function memberinfo()
    {
		$tq=C('DB_PREFIX');
        $this->userlogin();
        $uid = $_SESSION['uid'];
        $result = M('accountinfo')->where('uid=' . $uid)->find();
		$exper=M('experienceinfo')->join($tq.'experience on '.$tq.'experienceinfo.exid='.$tq.'experience.eid')->where($tq.'experienceinfo.uid='.$uid.' and '.$endtime.' < '.$tq.'experience.endtime and '.$tq.'experienceinfo.getstyle=0')->count();	
		$suer = M('userinfo')->where('uid='. $uid)->find();
		$suer['exper']= $exper;
        $this->assign('result', $result);
        $this->assign('suer', $suer);
        $this->display();
    }

    //修改密码
    public function edituser()
    {
        $this->userlogin();
        if (IS_POST) {
			$uid = $_SESSION['uid'];
			$ppp = M('accountinfo')->where("uid=".$uid)->getField('pwd');
			if(I('post.newpwd') == I('post.mypwd')){
				if(md5(I('post.newpwd')) != $ppp )
				{
					$edit = M('accountinfo');
					$uid = $_SESSION['uid'];
					$map['pwd'] = md5(I('post.newpwd'));
					$edituser = $edit->where("uid=".$uid)->save($map);
					if ($edituser) {
						redirect(U('User/memberinfo'), 1, '密码修改成功...');
					} else {
						$this->error('密码修改失败，请重新修改');
					}
				}else{
					$this->error('您输入的密码和原密码一致，请重新修改');
				}
			}else{
				$this->error('两次密码不一致，请重新修改');
			}
        }
        $this->display();
    }

    //退出登录
    public function logout()
    {
        // 清楚所有session
		$wxopenid = $_SESSION['wxopenid'];
		$username = $_SESSION['husername'];
        session(null);
		// session("wxopenid",$wxopenid);
		// session("husername",$username);
        $this->redirect('Index/login');

    }

    //账户提现
    public function cash()
    {
        $this->userlogin();
		$account = D('accountinfo');
		$balance = D('balance');
		$bankinfo = D('bankinfo');
		$bournal = D('bournal');
		$uid = $_SESSION['uid'];
		$username = $_SESSION['husername'];
		if(IS_POST){
			//交易密码
			$bpwd = $account->field('pwd,balance')->where('uid='.$uid)->find();
			if(!$bpwd['pwd'])
			{
				$this->ajaxReturn("33");
			}
			$pwd = I('post.pwd');
			$banknumber = I('post.banknumber');
			$bankname = I('post.bankname');
			$province = I('post.province');
			$city = I('post.city');
			$branch = I('post.branch');
			$busername = I('post.busername');
			$bpprice = I('post.bpprice');
			$shibpprice = I('post.bpprice1');
			if($bpwd['balance']-$bpprice < 0){
				$this->ajaxReturn("133");
			}
			if($bpwd['pwd']==md5($pwd)){
				if(strlen($banknumber)==16||strlen($banknumber)==19){
					$detailed = A('Home/Detailed');
					//提现表
					$balances['bptype'] = '提现';
					$balances['bptime'] = date(time());
					$balances['bpprice'] = $bpprice;
					$balances['shibpprice'] = $shibpprice;
					$balances['uid'] = $uid;
					$balances['isverified'] = 0;
					//提现记录
					$bournals['btype'] = '提现';
					$bournals['btime'] = date(time());
					$bournals['bprice'] = $bpprice;
					$bournals['uid'] = $uid;
					$bournals['username'] = $username;
					$bournals['isverified'] = 0;
					$bournals['bno'] = $detailed->build_order_no();
					$bournals['balance'] = $bpwd['balance']-$bpprice;
					//银行卡信息，添加或修改
					$banks['bankname'] = $bankname;
					$banks['province'] = $province;
					$banks['city'] = $city;
					$banks['branch'] = $branch;
					$banks['banknumber'] = $banknumber;
					$banks['busername'] = $busername;
					//插入提现记录
					$balance_result = $balance->add($balances);
					$bournal_result = $bournal->add($bournals);
					//查询银行卡表所有用户id数组
					$uidcount = $bankinfo->where('uid='.$uid)->count();
					//判断uid是否已经存在银行卡表内，存在插入数据，不存在修改数据
					if($uidcount==1){
						//查询用户银行卡表的bid
						$bid = $bankinfo->field('bid')->where('uid='.$uid)->find();
						$bankinfo->where('bid='.$bid['bid'])->save($banks);
					}else{
						$banks['uid'] = $uid;
						$bankinfo->add($banks);
					}
					if($balance_result){
						$accounts['balance'] = $bpwd['balance']-$bpprice;
						$account->where('uid='.$uid)->save($accounts);
						$this->ajaxReturn();
					}else{
						$this->ajaxReturn("0");
					}
				}else{
					$this->ajaxReturn("10");
				}
			}else{
				$this->ajaxReturn("-99");
			}
		}else{
			//账户余额
			$totle = $account->field('balance')->where('uid='.$uid)->find();
			//银行信息
			$binfo = $bankinfo->where('uid='.$uid)->find();
			
			$this->assign('binfo',$binfo);
			$this->assign('totle',$totle);
	        $this->display();	
		}
    }
    //账户充值
     public function payReapalReturn(){
         $this->redirect('Index/index'); 
    }
    public function payReapalNotice(){
        $uid = $_SESSION['uid'];
         // $pay_config = C('pay_config');
         if (empty($_POST)) {
            //判断提交来的数组是否为空
            return false;
        } else {
                 
       
         
            $merchant_code = md5($_POST["merchant_code"].'Singer2016');
            $merchant_code2 = '1118010687';
            $interface_version = $_POST["interface_version"];
            $sign_type = $_POST["sign_type"];
            $dinpaySign = base64_decode($_POST["sign"]);
            $notify_type = $_POST["notify_type"];
            $notify_id = $_POST["notify_id"];
            $order_no = $_POST["order_no"];
            $order_time = $_POST["order_time"];
            $order_amount = $_POST["order_amount"];
            $trade_status = $_POST["trade_status"];
            $trade_time = $_POST["trade_time"];
            $trade_no = $_POST["trade_no"];
            $bank_seq_no = $_POST["bank_seq_no"];
            $extra_return_param = $_POST["extra_return_param"];
            if ($trade_status=="SUCCESS" && $merchant_code=md5($merchant_code2.'Singer2016')) {
                //验签成功（Signature correct）
                    $parameter = array(
                "order_no"     => $order_no, //商户订单编号；
                "order_amount"     => $order_amount,    //交易金额；
                "trade_status"     => $trade_status, //交易状态
            );
             if(!checkorderstatus($order_no)){
                    orderhandle($parameter);
                    recommend($order_no,$order_amount);
                      echo "SUCCESS";
                    
                }    
        } else {
                  echo "Signature Error";
            }
    
    }
   }
    public function recharge()
    {

 

        $this->userlogin();
        $uid = $_SESSION['uid'];
        $result = M('accountinfo')->where('uid='. $uid)->find();
        $suer = M('userinfo')->where('uid='.$uid)->find();
        $this->assign('result', $result);
        $this->assign('suer', $suer);
        $this->assign('style','1');
        if (IS_POST) {
             $date['bpprice']=I('post.tfee1');
             $date['bpno']=$this->build_order_no();
             $date['uid']=$uid;
             $date['bptype']='充值';
             $date['bptime']=date(time());
             $date['remarks']='开始充值';
             $balanceid=M('balance')->add($date);
             if ($balanceid) {
                $balc=M('balance')->where('bpid='.$balanceid)->find();
                $this->assign('balc',$balc);
             }
             $this->assign('style','2');
        }
        $this->display();
    }
    //处理支付后的结果，加钱
    public function notify(){
         // $orderno=I('get.order_id');
		 file_put_contents("123456.txt",'123456');
		 file_put_contents("456789.txt",$_REQUEST);die;
         $balance=M('balance')->where('bpno='.$orderno)->find();
     
         //判断订单是否存在，并且判断是否是同一个人操作
         if ($balance&&$balance['uid']==$_SESSION['uid']) {
            $date['bpno']=$balance['bpno'];
            $date['remarks']='充值成功';
            $style=M('balance')->where('uid='.$balance['uid'])->save($date);
            //修改客户的帐号余额
            if ($style) {
                //查询订单金额
                $prict=M('balance')->where('uid='.$balance['uid'])->find();
                //先取出用户帐号的余额。
                $userprice=M('accountinfo')->where('uid='.$balance['uid'])->find();
                $mydate['balance']=$prict['bpprice']+$userprice['balance'];
                M('accountinfo')->where('uid='.$balance['uid'])->save($mydate);
            }
         }
         $this->redirect('User/memberinfo');   
    }
    public function notify1(){
		$abc=array(
		'1'=>'aslgjldsjfgsdjlgjsdjgsdlfg',
		'12'=>'4654654654654654',
		);
		
		post('http://mipan.fxicc.com/index.php/Home/User/notify.html',$abc);
		  
    }

    public function reorder(){
        $balanceno="ZF".date("Ymdhis",time());
        $amount=$_POST['tfee1'];
        $product_name="智付充值";
        $this->assign("balanceno",$balanceno);
        $this->assign("amount",$amount);
        $this->assign("product_name",$product_name);
        $this->display();
    }


    //获取用户收入排行
    public function ranking(){
        $this->userlogin();
        $order=M('order');
        //$userinfo=M('userinfo')->select();
        $tq=C('DB_PREFIX');
       // foreach ($userinfo as $k => $v) {
        $list=$order->field('sum('.$tq.'order.ploss) as pric,'.$tq.'order.uid')->group($tq.'order.uid')->order('sum('.$tq.'order.ploss) desc')->limit(10)->select();
        $lists=array();
        foreach ($list as $k => $v) {
           $lists[$k]=$v;
           $username=M('userinfo')->field('username','portrait')->where('uid='.$v['uid'])->find();
           $lists[$k]['name']=$username['username'];
           $lists[$k]['portrait']=$username['portrait'];
        }
        $this->assign('lists',$lists);
        $this->display();
    }


    public function recommend(){
		$this->userlogin();
	    $uid = $_SESSION['uid'];	
		$user=M("userinfo")->where("rid=$uid")->select();
		$this->assign("list",$user);

		$this->display();

    }


    //体验卷列表
    public function experiencelist()
    {
        $this->userlogin();
        $uid = $_SESSION['uid'];
        $tq = C('DB_PREFIX');
        $endtime = date(time());
         $list = M('experienceinfo')->join($tq . 'experience on ' . $tq . 'experienceinfo.exid=' . $tq . 'experience.eid')->where($tq . 'experienceinfo.uid=' . $uid . ' and ' . $endtime . ' < ' . $tq . 'experience.endtime and ' . $tq . 'experienceinfo.getstyle=0')->select();
       // $list=M('experience')->join($tq.'experienceinfo on'.$tq.'experienceinfo.exid=' . $tq . 'experience.eid')->select();


        $this->assign('list', $list);
        $this->display();
    }


      //体验卷列表
    public function alist()
    {
        $this->userlogin();
        $uid = $_SESSION['uid'];
        $tq = C('DB_PREFIX');
        $endtime = date(time());
        $alist = M('experience')->where(  $endtime . ' < ' . $tq . 'experience.endtime')->select();
        $this->assign('alist', $alist);
        $this->display();
    }





    //体验卷详情页
    public function experienceid()
    {
        $this->userlogin();
        $eid = I('eid');
        $expid = M('experience')->where('eid=' . $eid)->find();
        $this->assign('expid', $expid);
        $this->display();
    }

    public function userlogin()
    {
        //判断用户是否已经登录
        if (!isset($_SESSION['uid'])) {
            $this->redirect('User/login');
        }
    }
    public function img(){
        $this->userlogin();
        $hostlink= $_SERVER['HTTP_HOST'];
		$otype = M("userinfo")->where("uid = ".session('uid'))->getField("otype");
        $url=  "http://".$hostlink.U('User/reg')."?uid=".session('uid')."&otype=".$otype."&oid=".session('uid');
        $this->assign('url', $url);
        $this->display();
    }

    //随机生成订单编号
    function build_order_no(){
        return date(time()).substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 3);
    }
	
	function changeimg(){
		$path = "/Public/Uploads/headimg/";
		$tmp = $_FILES['file']['tmp_name'];
		$size = $_FILES['file']['size'];
		if($size > 10000000)
		{
			$this->error("图片大小超过2M了！");
		}
		$time = time();
		$t = move_uploaded_file($tmp,'.'.$path.$time.'.png');
		$newpath = 'http://'.$_SERVER['HTTP_HOST'].$path.$time.'.png';
		$uid = session('uid');
		$map['portrait'] = $newpath;
		$s = M("userinfo")->where("uid = ".$uid)->save($map);
		if($t && $s)
		{
			$this->success("更新成功!");
		}else{
			$this->error("更新失败!");
		}
	}
    public function outpwd()
	{
		$this->display();
	} 
	//短信验证
    public function outsmsverify()
    {
		$code = $this->mescontent();
		session("regcode",$code);
		include_once('sms.php');
    }
    public function chanoutpwd()
	{
		$pho = $_REQUEST['pho'];
		$pwd = $_REQUEST['pwd'];
		$code = $_REQUEST['code'];
		if($code != $_SESSION['regcode'])
		{
			echo 3;exit;
		}
		$map['upwd'] = md5($pwd);
		if(M("userinfo")->where("utel = ".$pho)->save($map)){
			echo 1;exit;
		}else{
			echo 2;exit;
		}
		
	}
}
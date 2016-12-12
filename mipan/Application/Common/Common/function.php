<?php

function get_url() {
    $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
    $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
    $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
    return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
 }
 


/**
 * 使用curl获取远程数据
 * @param  string $url url连接
 * @return string      获取到的数据
 */
function curl_get_contents($url){
	
    $ch=curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);                //设置访问的url地址
    curl_setopt($ch,CURLOPT_HEADER,1);                //是否显示头部信息
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);               //设置超时
    curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);   //用户访问代理 User-Agent
    curl_setopt($ch, CURLOPT_REFERER,_REFERER_);        //设置 referer
    curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);          //跟踪301
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        //返回结果
    $r=curl_exec($ch);
    curl_close($ch);
	var_dump($r);exit;
    return $r;
}

function getJson($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$output = curl_exec($ch);
	curl_close($ch);
	return json_decode($output, true);
}

/****************************
 * /*  手机短信接口（www.ussns.com）
 * /* 参数：$mob        手机号码
 * /*        $content    短信内容
 *****************************/
function sendsms($mob, $content)
{
    $msgconfig = C("SMS");
	$username=$msgconfig['user2'];
	$pwd=$msgconfig['pwd'];
    $type = 0;// type=0 短信接口
    if ($type == 0) {
        /////////////////////////////////////////短信接口 开始/////////////////////////////////////////////////////////////
		$post_data = array(
			'username' => $username,
			'pwd' => $pwd,
			'msg' => urlencode($content),//短信内容 编码处理
			'phone' => $mob,//发送手机号，多号码用半角逗号","分割
		);
		$smsapi = 'www.ussns.com/Api/send';//API地址
		header("Content-type:text/html;charset=utf-8");
		$postdata = http_build_query($post_data);
		$options = array(
			'http' => array(
				'method' => 'POST',
				'header' => 'Content-type:application/x-www-form-urlencoded',
				'content' => $postdata,
				'timeout' => 15 * 60 // 超时时间（单位:s）
			)
		);
		$context = stream_context_create($options);
		$result = file_get_contents('http://'.$smsapi, false, $context);
		if($result == '888'){
			return true;//echo('恭喜：发送成功！');
		}else{
			return false;//echo('错误：发送失败！');
		}
        /////////////////////////////////////////短信接口 结束/////////////////////////////////////////////////////////////
    }else{
        return false;
    }
}	
<?php
	$interval=2;//每隔一定时间运行
	$i = 0;
	do{
		$url = "http://mipan.fxicc.com/Home/Getdata/index";
		$ch = curl_init();//初始化一个curl会话
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    // 要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_exec($ch);
		curl_close($ch);
		echo $i++.',';
		sleep($interval);
	}while(true);
?>
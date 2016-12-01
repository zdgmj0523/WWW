<?php
	/*
		异步通知demo
		传输方式:get
		是否需要验签：是
	*/
	//接收参数
	$result 			= 		$_GET['result'];											//支付结果
	$other_order 		= 		$_GET['other_order'];										//平台订单号
	$order_no 		= 			$_GET['order_no'];											//商户订单号
	$pay_type 			= 		$_GET['pay_type'];											//支付类型
	$pay_amt 			= 		$_GET['pay_amt'];											//支付金额
	$goods_name			=		$_GET['goods_name'];										//商品名
	$custom				=		$_GET['custom'];
	$sign 				= 		$_GET['sign'];												//加密验签
	$key				=		'';															//商户接入时由平台分配
	//拼装验签
	$custom_str = 'order_no='.$order_no.'&order_amt='.$pay_amt.'&key='.$key;
	$sign = md5($custom_str);
	if($new_sign == $sign){
		//验证成功
		//合作方业务逻辑
		echo 'ok';
	}else{
		echo 'fail';
	}
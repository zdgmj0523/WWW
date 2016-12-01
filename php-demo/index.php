<?php	
	$order_no = '';
	$goods_name = 'test';
	$order_amt = 0.1;
	$return_url = '';
	$notify_url = '';
	$custom = '1';
	$key = '';
	$mchid = '';
	$custom_str = 'order_no='.$order_no.'&order_amt='.$order_amt.'&key='.$key;
	$sign = md5($custom_str);
	$url = 'http://www.huayuinfinite.com/pay/wechat_wap/index.php?order_no='.$order_no.'&goods_name='.$goods_name.'&order_amt='.$order_amt.'&notify_url='.$notify_url.'&custom='.$custom.'&mchid='.$mchid.'&sign='.$sign;
	echo '<script>window.location.href="'.$url.'"</script>';
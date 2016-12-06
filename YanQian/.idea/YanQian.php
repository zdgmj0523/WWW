<?php
/**
 * Created by PhpStorm.
 * User: XFJA_DG
 * Date: 2016/11/22
 * Time: 14:50
 */
date_default_timezone_set('PRC');


$post_data = array(
    'transDate'=>"2016-12-02",
    'transTime'=>"22:05:49",
    'merchno'=>"678510148160047",
    'merchName'=>"成都彩利虹",
    'customerno'=>"",
    'amount'=>"900.00",
    'traceno'=>"49745720161202100543",
    'payType'=>"2",
    'orderno'=>"900003226614",
    'channelOrderno'=>"4006882001201612021572275872",
    'channelTraceno'=>"102510008010201612026228619466",
    'openId'=>"weixin://wxpay/bizpayurl?pr=agBHMSg",
    'status'=>"1",

);
$temp="";
ksort($post_data);//对数组进行排序
//遍历数组进行字符串的拼接
foreach ($post_data as $x=>$x_value){
    if ($x != 'signature'&& $x_value != null && $x_value != "null"){
        $temp = $temp.$x."=".iconv('UTF-8','GBK//IGNORE',$x_value)."&";
    }
}
echo 'GBK：'.$temp.'C3B283E75B28D5017CE12BE7BD5B6A05'.'<br>';

$md5=md5($temp.'C3B283E75B28D5017CE12BE7BD5B6A05');
echo '签名数据结果：'.$md5."<br>";
if (strcasecmp($md5,"CB1A243ECC63BDC05547A33EB6BB5AE6")==0 ){
    echo "验签结果：正确";
}else{
    echo "验签结果：错误";
}
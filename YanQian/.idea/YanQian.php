<?php
/**
 * Created by PhpStorm.
 * User: XFJA_DG
 * Date: 2016/11/22
 * Time: 14:50
 */
date_default_timezone_set('PRC');


$post_data = array(
    'transDate'=>"2016-12-06",
    'transTime'=>"23:07:20",
    'merchno'=>"678510148160010",
    'merchName'=>"成都荣祥加园水产品有限公司",
//    'customerno'=>"",
    'amount'=>"10.00",
    'traceno'=>"a98f696e",
    'payType'=>"2",
    'orderno'=>"900003439082",
    'channelOrderno'=>"8021800594678161206026866619",
//    'channelTraceno'=>"",
    'openId'=>"weixin://wxpay/bizpayurl?pr=HzVmViO",
    'status'=>"1",

);
$temp='';
ksort($post_data);//对数组进行排序
//遍历数组进行字符串的拼接
foreach ($post_data as $x=>$x_value){
    if ($x != 'signature'&& $x_value != null){
        $temp = $temp.$x."=".iconv('UTF-8','GBK//IGNORE',$x_value)."&";
    }
}
echo $temp.'F113B1C0909DAA7A12A5A1B5CB1A09F0'."<br>";
$md5=md5($temp.'F113B1C0909DAA7A12A5A1B5CB1A09F0');
echo '签名数据结果：'.$md5."<br>";
if (strcasecmp($md5,"30265633CEFC1D25A6644B8BC80C3E24")==0 ){
    echo "验签结果：正确";
}else{
    echo "验签结果：错误";
}
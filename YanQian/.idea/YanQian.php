<?php
/**
 * Created by PhpStorm.
 * User: XFJA_DG
 * Date: 2016/11/22
 * Time: 14:50
 */
date_default_timezone_set('PRC');


$post_data = array(
    'transDate'=>"2016-11-22",
    'transTime'=>"14:08:52",
    'merchno'=>"678430148160002",
    'merchName'=>"长沙辉扬百货贸易有限公司",
    'customerno'=>"null",
    'amount'=>"0.10",
    'traceno'=>"161122140848000002",
    'payType'=>"2",
    'orderno'=>"900002701749",
    'channelOrderno'=>"4006102001201611220483856687",
    'channelTraceno'=>"100580025142201611223145862133",
    'openId'=>"weixin://wxpay/bizpayurl?pr=MvVaveY",
    'status'=>"1"
);
$temp="";
ksort($post_data);//对数组进行排序
//遍历数组进行字符串的拼接
foreach ($post_data as $x=>$x_value){
    if ($x != 'signature'&& $x_value != null && $x_value != "null"){
        $temp = $temp.$x."=".iconv('UTF-8','GBK//IGNORE',$x_value)."&";
    }
}
echo 'GBK：'.$temp.'42D65AEC115AEA1372297427A21101C3'.'<br>';

$md5=md5($temp.'42D65AEC115AEA1372297427A21101C3');
echo '签名数据结果：'.$md5."<br>";
if (strcasecmp($md5,"465698DC4FC893F2C84AC95CBC45E2EC")==0 ){
    echo "验签结果：正确";
}else{
    echo "验签结果：错误";
}
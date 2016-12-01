<?php
/**
 * Created by PhpStorm.
 * User: XFJA_DG
 * Date: 2016/11/22
 * Time: 14:50
 */
date_default_timezone_set('PRC');


$post_data = array(
//    'transDate'=>"2016-11-28",
//    'transTime'=>"15:08:38",
    'merchno'=>"001440189990001",
//    'merchName'=>"",
//    'customerno'=>"null",
    'amount'=>"0.01",
    'traceno'=>"20161201103208",
//    'payType'=>"2",
//    'orderno'=>"900003001622",
//    'channelOrderno'=>"4004352001201611281083817976",
//    'channelTraceno'=>"102590008008201611286219349425",
//    'openId'=>"",
//    'status'=>"1"
    'channel'=>'1',
    'notifyUrl' => 'localhost:8080/A/index.jsp',
    'returnUrl' => 'localhost:8080/A/index.jsp',
    'settleType' => '2',

);
$temp="";
ksort($post_data);//对数组进行排序
//遍历数组进行字符串的拼接
foreach ($post_data as $x=>$x_value){
    if ($x != 'signature'&& $x_value != null && $x_value != "null"){
        $temp = $temp.$x."=".iconv('UTF-8','GBK//IGNORE',$x_value)."&";
    }
}
echo 'GBK：'.$temp.'28480E19DD2B42DA011D1A65DD22ECAA'.'<br>';

$md5=md5($temp.'28480E19DD2B42DA011D1A65DD22ECAA');
echo '签名数据结果：'.$md5."<br>";
if (strcasecmp($md5,"BF06CA2925642E814D924C08CB8E3352")==0 ){
    echo "验签结果：正确";
}else{
    echo "验签结果：错误";
}
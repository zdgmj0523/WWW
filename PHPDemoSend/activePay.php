<?php
/**
 * Created by PhpStorm.
 * User: XFJA_DG
 * Date: 2016/11/17
 * Time: 15:17
 */
date_default_timezone_set('PRC');
require_once "General.php";
$amount = $_POST['num'];
$authno = $_POST['authno'];
$settleType = $_POST['settleType'];
$url = 'http://112.74.230.8:8081/posp-api/activePay';//主扫接口
$fee=$amount*Generals::rate;//手续费
//手续费不能低于一份
if ($fee<0.01){
    $fee = 0.01;
}
$post_data = array(
    "amount"=>$amount,
    'settleType'=>$settleType,
    'fee'=>$fee,
    'authno'=>$authno,
    'merchno'=>Generals::merchno,
    'traceno'=>Generals::traceno.date('ymdhis',time()),//自定义流水号
    'notifyUrl'=>Generals::notifyUrl,
    'certno'=>Generals::certno,
    'mobile'=>Generals::mobile,
    'accountno'=>Generals::accountno,
    'accountName'=>Generals::accountName,
    'bankno'=>Generals::bankno,
    'bankName'=>Generals::bankName,
    'bankType'=>Generals::bankType,
    'goodsName'=>'中信测试',
    'remark'=>"remark"
);
$temp='';
ksort($post_data);//对数组进行排序
//遍历数组进行字符串的拼接
foreach ($post_data as $x=>$x_value){
    if ($x_value != null){
        $temp = $temp.$x."=".iconv('UTF-8','GBK//IGNORE',$x_value)."&";
    }
}
$md5=md5($temp.Generals::signature);
$reveiveData = $temp.'signature'.'='.$md5;
//print  $reveiveData;
$curl = curl_init();
//设置抓取的url
curl_setopt($curl, CURLOPT_URL, $url);
//设置头文件的信息作为数据流输出
curl_setopt($curl, CURLOPT_HEADER, false);
//设置获取的信息以文件流的形式返回，而不是直接输出。
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//设置post方式提交
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $reveiveData);
//执行命令
$data = curl_exec($curl);
//关闭URL请求
curl_close($curl);
//return iconv('GB2312', 'UTF-8', $data);
//显示获得的数据
echo iconv('GBK//IGNORE', 'UTF-8', $data);
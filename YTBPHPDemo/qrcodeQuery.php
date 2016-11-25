<?php
/**
 * Created by PhpStorm.
 * User: 交易查询
 * Date: 2016/10/17
 * Time: 15:57
 */
require_once "General.php";//导入头文件
date_default_timezone_set('PRC');

$merchno = $_POST["mer"];
$traceno = $_POST["tra"];
$refno = $_POST["ref"];
$url = "http://120.25.96.46:8081/posp-api/qrcodeQuery";

$post_data = array(
    "merchno"=>$merchno,
    "traceno"=>$traceno,
    "refno"=>$refno
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
//echo $temp.Generals::signature;
$reveiveData = $temp.'signature'.'='.$md5;
//echo  $reveiveData;
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
//显示获得的数据
echo iconv('GBK//IGNORE', 'UTF-8', $data);

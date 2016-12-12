<?php
/**
 * Created by PhpStorm.
 * User: XFJA
 * Date: 2016/12/7
 * Time: 16:05
 */
//参与加密的参数
$post_data = array(
    'cardno' => $_POST['cardno'],
    'traceno' => $_POST['traceno'],
    'amount'=> $_POST['amount'],
    'accountno' => $_POST['accountno'],
    'mobile' => $_POST['mobile'],
    'bankno' => $_POST['bankno'],
);
$temp='';
//ksort($post_data);//对数组进行排序
//遍历数组进行字符串的拼接
foreach ($post_data as $x=>$x_value){
    $temp = $temp.$x."=".$x_value."&";
}
//iconv('UTF-8','GBK//IGNORE',$_POST['accountName'])
$md5=md5($temp.'key='.$_POST['merchKey']);
//上传的参数
$postData=array(
    'cardno' => $_POST['cardno'],
    'traceno' => $_POST['traceno'],
    'amount'=> $_POST['amount'],
    'accountno' => $_POST['accountno'],
    'mobile' => $_POST['mobile'],
    'bankno' => $_POST['bankno'],
    'accountName' => $_POST['accountName'],
    'bankName' => $_POST['bankName'],
    'bankType' => $_POST['bankType'],
    'remark' => $_POST['remark'],
);
$temp="";
foreach ($postData as $x=>$x_value){
    if ($x_value != null){
        $temp = $temp.$x."=".iconv('UTF-8','GBK//IGNORE',$x_value)."&";
    }
}
$reveiveData=$temp.'signature'.'='.$md5;
$url = "http://112.74.230.8:8083/posp-settle/virtPay.do";
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

<?php
/**
 * Created by PhpStorm.
 * User: XFJA
 * Date: 2016/12/7
 * Time: 16:05
 */
$md5=md5('cardno='.$_POST['cardno'].'&key='.$_POST['merchKey']);
$reveiveData='cardno='. $_POST['cardno'].'&signature'.'='.$md5;
$url = "http://112.74.230.8:8083/posp-settle/balance.do";
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
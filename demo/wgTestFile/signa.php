<?php
/**
 * Created by PhpStorm.
 * User: XFJA
 * Date: 2016/12/9
 * Time: 11:58
 */
$signa=$_POST["sig"];
$file  = fopen("signa.log","w");//要写入文件的文件名
fwrite($file, $signa);//写入字符串
fclose($file);

$page="index.php";
echo "<script>window.location = \"".$page."\";</script>";
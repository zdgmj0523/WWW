<?php
/**
 * Created by PhpStorm.
 * User: XFJA
 * Date: 2016/12/7
 * Time: 12:45
 */
$signa=$_POST["sig"];
$file  = fopen("signature.log","w");//要写入文件的文件名
fwrite($file, $signa);//写入字符串
fclose($file);

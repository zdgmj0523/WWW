<?php
/**
 * Created by PhpStorm.
 * User: xiaodu
 * Date: 2016/10/19
 * Time: 15:02
 */
date_default_timezone_set('PRC');
require_once "Generals.php";//导入头文件
$file  = fopen("myLog.log","a");//要写入文件的文件名
$str = "          ********************************************************"."\n          *                 [".date('Y-m-d H:i:s')."]                *"."\n*******************************************************************************\n";
fwrite($file, $str);//写入字符串

$data = file_get_contents('php://input');//接受post原数据
$post_data = array();
foreach ($_POST as $key=>$value){
    $post_data = array_merge($post_data,array(iconv('GBK//IGNORE','UTF-8',$key)=>iconv('GBK//IGNORE','UTF-8',$value)));
}
foreach ($post_data as $x=>$x_value){
    fwrite($file,$x."=>".$x_value."\n");
}

fwrite($file,"--------------------------------------------------------------\n");
$temp='';
ksort($post_data);//对数组进行排序
//遍历数组进行字符串的拼接
foreach ($post_data as $x=>$x_value){
    if ($x != 'signature'&& $x_value != null && $x_value != "null" ){
        $temp = $temp.$x."=".iconv('UTF-8','GBK//IGNORE',$x_value)."&";
    }
}
$md5=md5($temp.Generals::signature);
fwrite($file,"验签数据：".$temp.Generals::signature."\n");
fwrite($file,"加密结果：".$md5."\n");
if (strcasecmp($md5,$_POST['signature'])==0 ){
    echo "success";
    fwrite($file,"验签结果：正确\n");
}else{
    echo "fails";
    fwrite($file,"验签结果：错误\n");
}
fclose($file);

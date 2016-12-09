<?php
/**
 * Created by PhpStorm.
 * User: xiaodu
 * Date: 2016/10/19
 * Time: 15:02
 */
date_default_timezone_set('PRC');
$file  = fopen("myLog.log","w");//要写入文件的文件名
$str = "          ********************************************************".'<br>'."\n          *                 [".date('Y-m-d H:i:s')."]                *".'<br>'."\n*******************************************************************************.".'<br>'."\n";
fwrite($file, $str);//写入字符串
$data = file_get_contents('php://input');//接受post原数据
$post_data = array();
foreach ($_POST as $key=>$value){
    $post_data = array_merge($post_data,array(iconv('GBK//IGNORE','UTF-8',$key)=>iconv('GBK//IGNORE','UTF-8',$value)));
}
fwrite($file,"渠道订单号：".$post_data['orderno'].'<br>'."\n");
fwrite($file,"--------------------------------------------------------------".'<br>'."\n");
foreach ($post_data as $x=>$x_value){
    fwrite($file,$x."=>".$x_value.'<br>'."\n");
}
fwrite($file,"--------------------------------------------------------------".'<br>'."\n");
$temp='';
ksort($post_data);//对数组进行排序
//遍历数组进行字符串的拼接
foreach ($post_data as $x=>$x_value){
    if ($x != 'signature'&& $x_value != null){
        $temp = $temp.$x."=".$x_value."&";
    }
}
$sigFile=fopen("signature.log","r");
$datademo = fread($sigFile, filesize("signature.log"));
fclose($sigFile);
fwrite($file,$temp.$datademo.'<br>'."\n");
$md5=strtoupper(md5(iconv('UTF-8','GBK//IGNORE',$temp.$datademo)));
fwrite($file,"验签加密数据：".$md5.'<br>'."\n");

if ($md5 == $_POST['signature'] ){
    echo "success";
    fwrite($file,"验签结果：正确\n");
}else{
    echo "fails";
    fwrite($file,"验签结果：错误\n");
}
fclose($file);
?>


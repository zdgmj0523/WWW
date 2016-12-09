<?php
/**
 * Created by PhpStorm.
 * User: XFJA
 * Date: 2016/12/7
 * Time: 16:05
 */
$post_data = array(
    'cardno' => $_POST['cardno'],
    'traceno' => $_POST['traceno']
);
$temp='';
//ksort($post_data);//对数组进行排序
//遍历数组进行字符串的拼接
foreach ($post_data as $x=>$x_value){
    $temp = $temp.$x."=".$x_value."&";
}
//iconv('UTF-8','GBK//IGNORE',$_POST['accountName'])
$md5=md5($temp.'key='.$_POST['merchKey']);
$post_datas=array_merge($post_data,array('signature'=>$md5));
?>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=GBK">
    <title></title>
</head>
<body onload="document.form.submit();">
<form name="form" method="post" action="http://120.25.96.46:8083/posp-settle/virtOrder.do" target="_self">
    <input  type="hidden" name="cardno" value="<?php echo $_POST['cardno']; ?>" />
    <input  type="hidden" name="traceno" value="<?php echo $_POST['traceno']; ?>" />
    <input  type="hidden" name="signature" value="<?php echo $post_datas['signature']; ?>" />
</form>

</body>
</html>
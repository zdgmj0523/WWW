<?php
/**
 * Created by PhpStorm.
 * User: XFJA
 * Date: 2016/12/7
 * Time: 16:05
 */
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
$post_datas=array_merge($post_data,array('signature'=>$md5));
?>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=GBK">
    <title></title>
</head>
<body onload="document.form.submit();">
<form name="form" method="post" action="http://112.74.230.8:8083/posp-settle/virtPay.do" target="_self">
    <input  type="hidden" name="cardno" value="<?php echo $_POST['cardno']; ?>" />
    <input  type="hidden" name="traceno" value="<?php echo $_POST['traceno']; ?>" />
    <input  type="hidden" name="amount" value="<?php echo $_POST['amount']; ?>" />
    <input  type="hidden" name="accountno" value="<?php echo $_POST['accountno']; ?>" />
    <input  type="hidden" name="accountName" value="<?php echo urlencode($_POST['accountName']); ?>" />
    <input  type="hidden" name="bankno" value="<?php echo $_POST['bankno']; ?>" />
    <input  type="hidden" name="mobile" value="<?php echo $_POST['mobile']; ?>" />
    <input  type="hidden" name="bankName" value="<?php echo urlencode($_POST['bankName']); ?>" />
    <input  type="hidden" name="bankType" value="<?php echo urlencode($_POST['bankType']); ?>" />
    <input  type="hidden" name="remark" value="<?php echo urlencode($_POST['remark']); ?>" />
    <input  type="hidden" name="signature" value="<?php echo $post_datas['signature']; ?>" />
</form>

</body>
</html>

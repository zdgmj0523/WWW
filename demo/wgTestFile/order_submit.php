<?php
/**
 * Created by PhpStorm.
 * User: XFJA_DG
 * Date: 2016/11/16
 * Time: 12:05
 */
$post_data = array(
    'merchno' => $_POST['merchno'],
    'merchKey' => $_POST['merchKey'],
    'traceno'=> $_POST['traceno'],
    'amount' => $_POST['amount'],
    'channel' => $_POST['channel'],
    'bankCode' => $_POST['bankCode'],
    'settleType' => $_POST['settleType'],
    'notifyUrl' => $_POST['notifyUrl'],
    'returnUrl' => $_POST['returnUrl'],
);
$temp='';
ksort($post_data);//对数组进行排序
//遍历数组进行字符串的拼接
foreach ($post_data as $x=>$x_value){
    if ($x_value != null){
        $temp = $temp.$x."=".iconv('UTF-8','GBK//IGNORE',$x_value)."&";
    }
}
$md5=md5($temp.$_POST['merchKey']);
$reveiveData = $temp.'signature'.'='.$md5;
//echo $reveiveData;
$post_datas=array_merge($post_data,array('signature'=>$md5));
?>


<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=GBK">
    <script>
        function submit() {
            document.form1.submit();
            document.form2.submit();
        }
    </script>
</head>
<body onload="submit()">
<form name="form1" method="post" action="signa.php" target="_self">
    <input type="hidden" name="sig" value="<?php echo $_POST['merchKey']; ?>" />
</form>
<form name="form2" method="post" action="http://112.74.230.8:8081/posp-api/gateway.do?m=order" target="_blank">
    <input type="hidden" name="amount" value="<?php echo $post_datas['amount']; ?>" />
    <input type="hidden" name="bankCode" value="<?php echo $post_datas['bankCode']; ?>" />
    <input type="hidden" name="channel" value="<?php echo $post_datas['channel']; ?>" />
    <input type="hidden" name="merchKey" value="<?php echo $post_datas['merchKey']; ?>" />
    <input type="hidden" name="merchno" value="<?php echo $post_datas['merchno']; ?>" />
    <input type="hidden" name="traceno" value="<?php echo $post_datas['traceno']; ?>" />
    <input type="hidden" name="notifyUrl" value="<?php echo $post_datas['notifyUrl']; ?>" />
    <input type="hidden" name="returnUrl" value="<?php echo $post_datas['returnUrl']; ?>" />
    <input type="hidden" name="settleType" value="<?php echo $post_datas['settleType']; ?>" />
    <input type="hidden" name="signature" value="<?php echo $post_datas['signature']; ?>" />
</form>
</body>
</html>
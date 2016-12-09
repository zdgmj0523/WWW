<?php
/**
 * Created by PhpStorm.
 * User: XFJA
 * Date: 2016/12/7
 * Time: 16:05
 */
$md5=md5('cardno='.$_POST['cardno'].'&key='.$_POST['merchKey']);
?>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=GBK">
    <title></title>
</head>
<body onload="document.form.submit();">
<form name="form" method="post" action="http://112.74.230.8:8083/posp-settle/balance.do" target="_self">
    <input   name="cardno" value="<?php echo $_POST['cardno']; ?>" />
    <input   name="signature" value="<?php echo $md5; ?>" />
</form>

</body>
</html>
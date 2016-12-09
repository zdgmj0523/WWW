<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>支付示例</title>
    <script type="text/javascript" src="index.js"></script>
</head>
<body >
    <?php $datademo?>
<div >

</div>

</body>
</html>

<?php
$file  = fopen("log.txt","r");//要写入文件的文件名
$datademo = "";
//$datademo = fread($file, filesize("log.txt"));
//fclose($file);

ignore_user_abort(false);//当用户关闭页面时服务停止
set_time_limit(0);  //设置执行时间，单位是秒。0表示不限制。
date_default_timezone_set('Asia/Shanghai');//设置时区
$datademos='';
while(TRUE) {
    //这里是需要定时执行的任务
    $datademo = fread($file, filesize("log.txt"));
    $datademos = $datademos.$datademo;
    fclose($file);
    sleep(1);//暂停时间（单位为秒）

}

echo $datademo;
?>

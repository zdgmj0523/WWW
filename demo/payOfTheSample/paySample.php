<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>支付示例</title>
    <script type="application/javascript" src="../js/jquery-3.1.1.min.js"></script>
    <script type="application/javascript" src="../js/jquery.qrcode.min.js"></script>
    <script type="text/javascript" src="../js/demo.js"></script>
    <style>
        #first,#header,#main{text-align: center;}
    </style>
</head>
<body>
<div id="first" >
    <h2>支付示例</h2>
    <a>商户号：</a><input id="mer" placeholder="商户号" type="text" value="678110154110001"><br><br>
    <a>密钥：</a><input id="sig" placeholder="密钥" type="text" style="width: 200px" value="0123456789ABCDEF0123456789ABCDEF"><br><br>
    <a>通知地址：</a><input id="notify" placeholder="通知地址" type="text" style="width: 350px" value="http://yanpenghou.vicp.cc/demo/payBack.php">
</div>
<br>
<div id="header">
    <select id="settleType">
        <option value="0">t0</option>
        <option value="1">t1</option>
    </select>
    <select id="payType">
        <option value="2">微信支付</option>
        <option value="1">支付宝支付</option>
    </select>
    <input id="num" placeholder="请输入金额" type="text">
    <input id="authno" placeholder="主扫授权号" type="text">
    <input type="button" id="zs" value="主扫">
    <input type="button" id="ewm" value="二维码支付">
    <input type="button" id="wap" value="wap支付">
    <input type="button" id="gzh" value="公众号">
</div>
<br>
<div id="main" style="height: 300px">
    <div id="leftDiv"  style=" position:absolute;left: 30%;width: 300px;height: 250px">
        <p id="merchno"></p>
        <p id="refno"></p>
        <p id="traceno"></p>
        <p id="message"></p>
        <p id="respCode"></p>
        <p id="remark"></p>
    </div>
    <br>
    <div id="QrCode" style="position:relative;left: 60%;width: 200px; background-color:blue;"></div>
</div>
</body>
</html>

<?php

?>
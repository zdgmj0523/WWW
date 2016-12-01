<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>订单查询</title>
    <script type="application/javascript" src="../js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="../js/demo.js"></script>
    <style>
        #bottom{text-align: center;}
    </style>
</head>
<body>
<div id="bottom" style="text-align: center">
    <h2>订单查询</h2>
    <div id="bomHeard">
        商户号:<input id="merc" type="text">
        密钥：<input id="sign" type="text"><br><br>
        商户订单号:<input id="tra" type="text">
        渠道订单号:<input id="ref" type="text">
    </div></br>
    <input id="search" type="button" value="交易查询">
    <br><br>
    <div id="bottomInfo">
    </div>
</div>
</body>
</html>
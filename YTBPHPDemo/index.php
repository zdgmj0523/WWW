<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>支付示例</title>

    <script type="application/javascript" src="js/jquery-3.1.1.min.js"></script>
    <script type="application/javascript" src="js/jquery.qrcode.min.js"></script>
    <script type="text/javascript" src="js/demo.js"></script>
    <style>
        #header, #main, #QrCode,#butto,#bottom {
            text-align: center;
        }
    </style>
</head>
<body>
<a style='text-align: center;text-decoration:none' href="accountWithdrawal/account.php" target="_blank"><h3>虚拟账户提现</h3>
</a>
<a style="text-align: center"><h4>支付示例</h4></a>
<div id="header">
    <select id="payType">
        <option value="2">微信支付</option>
        <option value="1">支付宝支付</option>
    </select>
    <input id="num" placeholder="请输入金额" type="text">
    <input id="authno" placeholder="主扫授权号" type="text">
    <input type="button" id="zs" value="主扫">
    <input type="button" id="ewm" value="二维码支付">
    <input type="button" id="gzh" value="公众号">
</div>

<div id="main" style="height: 500px">
    </br>
    <p id="merchno"></p>
    <p id="refno"></p>
    <p id="traceno"></p>
    <p id="message"></p>
    <p id="respCode"></p>
    <p id="remark"></p>
    <div id="QrCode" style="position:relative;left: 42%;width: 200px; background-color:blue;"></div>
</div>
<div id="butto" style="text-align: center">
    <input id="butto" type="button" value="查看交易通知数据" onclick="infomation()">
</div>
<div id="aa" style="left: 100px"></div>
<br><br>
<div id="bottom" style="text-align: center">
    <a style="text-align: center"><h4>交易查询</h4></a>
    <div id="bomHeard">
        商户号:<input id="mer" type="text">
        商户订单号:<input id="tra" type="text">
        渠道订单号:<input id="ref" type="text">
    </div>
    </br>
    <input id="search" type="button" value="交易查询">
    <div id="bottomInfo">
    </div>
</div>
</body>
</html>
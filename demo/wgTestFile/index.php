<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>支付示例</title>
    <script type="application/javascript" src="../js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="../js/WGdemo.js"></script>
</head>
<body onload="ceshi()">
<form name="form" method="post" action="order_submit.php" target="_self">
    <table width="500"  border="1" cellspacing="0" cellpadding="0">
        <tr>
            <th colspan="2" scope="col">网关订单测试</th>
        </tr>
        <tr>
            <td>商户编号</td>
            <td><input name="merchno" type="text" value="678110154110001"></td>
        </tr>
        <tr>
            <td>商户秘钥</td>
            <td><input name="merchKey" type="text" value="0123456789ABCDEF0123456789ABCDEF"></td>
        </tr>
        <tr>
            <td>订单号</td>
            <td><input id="ord" name="traceno" type="text" ></td>
        </tr>
        <tr>
            <td>交易金额</td>
            <td><input name="amount" type="text" value="0.01"></td>
        </tr>
        <tr>
            <td>渠道类型</td>
            <td>
                <select id="channel" name="channel">
                    <option value="1">收银台</option>
                    <option value="2">直联银行</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>银行信息</td>
            <td>
                <select id="bankCode" name="bankCode">
                    <option value="">请选择</option>
                    <option value="3001">招商银行(借记卡)</option>
                    <option value="4001">招商银行(信用卡)</option>
                    <option value="3020">交通银行(借记卡)</option>
                    <option value="4020">交通银行(信用卡)</option>
                    <option value="3036">广发银行(借记卡)</option>
                    <option value="4036">广发银行(信用卡)</option>
                    <option value="3002">工商银行(借记卡)</option>
                    <option value="3006">民生银行(借记卡)</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>结算类型</td>
            <td>
                <select id="settleType" name="settleType">
                    <option value="2">T+1结算</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>通知地址</td>
            <td><input name="notifyUrl" type="text" value="http://home.jinkavip.com/wgTestFile/PayBack.php"></td>
        </tr>
        <tr>
            <td>返回地址</td>
            <td><input name="returnUrl" type="text" value="http://gf-info.cn:8085/test/gateway_return.jsp"></td>
        </tr>
        <tr>
            <td colspan="2"><div align="center">
                    <input type="submit" name="Submit" value="提交" ">
                </div></td>
        </tr>
    </table>
</form>

<form name="search">
    <table width="500"  border="1" cellspacing="0" cellpadding="0">
        <tr>
            <th colspan="2" scope="col">线上交易查询</th>
        </tr>
        <tr>
            <td align="center">商户编号:</td>
            <td><input name="merchn" type="text" value="678110154110001"></td>
            <br>
        </tr>
        <tr>
            <td align="center">商户订单号:</td>
            <td><input type="text" name="ordern"></td>
        </tr>
        <tr>
            <td colspan="2"><div align="center">
                    <input type="button" name="search" value="查询">
                </div></td>
        </tr>
    </table>
</form>
    <div id = "box"></div>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>虚拟账户提现示例</title>
    <script type="application/javascript" src="../js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="../js/WGdemo.js"></script>
</head>
<body onload="traceno()">
<form name="form" method="post" action="account_order.php" target="_blank">
    <table width="600"  border="1" cellspacing="0" cellpadding="0">
        <tr>
            <th colspan="2" scope="col">虚拟账户提现</th>
        </tr>
        <tr>
            <td>虚拟账号</td>
            <td><input name="cardno" type="text" value="678011010100000001"></td>
        </tr>
        <tr>
            <td>商户流水号</td>
            <td><input id="traceno" name="traceno" type="text" value=""></td>
        </tr>
        <tr>
            <td>商户秘钥</td>
            <td><input name="merchKey" type="text" style="width: 200px" value="2LNuUvB9EI2D6OXPcbeYhyqt8RJKWVe1"></td>
        </tr>
        <tr>
            <td>代付金额</td>
            <td><input name="amount" type="text" value="0.01"></td>
        </tr>
        <tr>
            <td>结算账号</td>
            <td><input name="accountno" type="text" value="6214830161709315" ></td>
        </tr>
        <tr>
            <td>结算户名</td>
            <td><input name="accountName" type="text" value="赵笃刚"></td>
        </tr>
        <tr>
            <td>手机号码</td>
            <td><input name="mobile" type="text" value="18510083054"></td>
        </tr>
        <tr>
            <td>联行号</td>
            <td><input name="bankno" type="text" value="308100005842"></td>
        </tr>
        <tr>
            <td>支行名称</td>
            <td><input name="bankName" type="text" value="招商银行股份有限公司北京西客站支行"></td>
        </tr>
        <tr>
            <td>银行类型</td>
            <td><input name="bankType" type="text" value="招商银行股份有限公司"></td>
        </tr>
        <tr>
            <td>备注信息</td>
            <td><input name="remark" type="text" value="测试信息"></td>
        </tr>
        <tr>
            <td colspan="2"><div align="center">
                    <input type="submit" name="Submit" value="提交" >
                </div></td>
        </tr>
    </table>
</form>
<br>
<form name="form" method="post" action="virtOrder.php" target="_blank">
    <table width="600"  border="1" cellspacing="0" cellpadding="0">
        <tr>
            <th colspan="2" scope="col">代付查询</th>
        </tr>
        <tr>
            <td>虚拟账号</td>
            <td><input name="cardno" type="text" value="678011010100000001"></td>
        </tr>
        <tr>
            <td>商户流水号</td>
            <td><input name="traceno" type="text" value=""></td>
        </tr>
        <tr>
            <td>商户秘钥</td>
            <td><input name="merchKey" type="text" style="width: 200px" value="2LNuUvB9EI2D6OXPcbeYhyqt8RJKWVe1"></td>
        </tr>
        <tr>
            <td colspan="2"><div align="center">
                    <input type="submit" name="Submit" value="提交" >
                </div></td>
        </tr>
    </table>
</form>
<br>
<form name="form" method="post" action="balance.php" target="_blank">
    <table width="600"  border="1" cellspacing="0" cellpadding="0">
        <tr>
            <th colspan="2" scope="col">代付查询</th>
        </tr>
        <tr>
            <td>虚拟账号</td>
            <td><input name="cardno" type="text" value="678011010100000001"></td>
        </tr>
        <tr>
            <td>商户秘钥</td>
            <td><input name="merchKey" type="text" style="width: 200px" value="2LNuUvB9EI2D6OXPcbeYhyqt8RJKWVe1"></td>
        </tr>
        <tr>
            <td colspan="2"><div align="center">
                    <input type="submit" name="Submit" value="提交" >
                </div></td>
        </tr>
    </table>
</form>
</body>
</html>
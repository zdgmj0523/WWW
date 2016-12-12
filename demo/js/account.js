/**
 * Created by XFJA on 2016/12/12.
 */
function traceno() {
    var date = new Date();
    var result = date.getFullYear()+''+(date.getMonth()+1)+''+date.getDate()+''+date.getHours()+''+date.getMinutes()+''+date.getSeconds();
    // alert(result);
    document.getElementById("traceno").value='CeShi_'+result;
}

function buttonClick() {
    var datas = 'cardno='+$('form input[name="cardno"]').val()+'&traceno='+$('form input[name="traceno"]').val()+'&merchKey='+$('form input[name="merchKey"]').val()+'&amount='+$('form input[name="amount"]').val()+'&accountno='+$('form input[name="accountno"]').val()+'&accountName='+$('form input[name="accountName"]').val()+'&mobile='+$('form input[name="mobile"]').val()+'&bankno='+$('form input[name="bankno"]').val()+'&bankName='+$('form input[name="bankName"]').val()+'&bankType='+$('form input[name="bankType"]').val()+'&remark='+$('form input[name="remark"]').val();
    $.ajax({
        type:"POST",
        url:"account_order.php",
        data:datas,
        dataType:"json",
        success:function (response,xhr) {
            if(response.respCode != '72'){
                $('#message').html("返回信息："+response.message);
            }else {
                $('#message').html("返回信息："+response.message+"-可能代付成功");
            }
            $('#respCode').html("相应码："+response.respCode);
            if (response.payStatus != null){
                switch(response.payStatus) {
                    case '2':
                        $('#VpayStatus').html("提现状态：2-代付成功");
                        break;
                    case '3':
                        $('#VpayStatus').html("提现状态：3-代付异常");
                        break;
                    default:
                        $('#VpayStatus').html("提现状态：4-代付失败");
                }
            }
            if (response.transStatus != null){
                switch(response.transStatus){
                    case '2':
                        $('#VtransStatus').html("代付状态：2-提现成功");
                        break;
                    case '3':
                        $('#VtransStatus').html("代付状态：3-提现异常");
                        break;
                    default:
                        $('#VtransStatus').html("代付状态：4-提现失败");
                }
            }
        }
    });
}
function VbuttonClick() {
    var datas = 'cardno='+$('form input[name="cardno"]').val()+'&traceno='+$('form input[name="traceno"]').val()+'&merchKey='+$('form input[name="merchKey"]').val();
    $.ajax({
        type:"POST",
        url:"virtOrder.php",
        data:datas,
        dataType:"json",
        success:function (response,xhr) {
            $('#Vmessage').html("返回信息："+response.message);
            $('#VrespCode').html("相应码："+response.respCode);
            if (response.payStatus != null){
                switch(response.payStatus) {
                    case '2':
                        $('#VpayStatus').html("提现状态：2-代付成功");
                        break;
                    case '3':
                        $('#VpayStatus').html("提现状态：3-代付异常");
                        break;
                    default:
                        $('#VpayStatus').html("提现状态：4-代付失败");
                }
            }
            if (response.transStatus != null){
                switch(response.transStatus){
                    case '2':
                        $('#VtransStatus').html("代付状态：2-提现成功");
                        break;
                    case '3':
                        $('#VtransStatus').html("代付状态：3-提现异常");
                        break;
                    default:
                        $('#VtransStatus').html("代付状态：4-提现失败");
                }
            }
        }
    });
}
function BbuttonClick() {
    var datas = 'cardno='+$('form input[name="cardno"]').val()+'&merchKey='+$('form input[name="merchKey"]').val();
    $.ajax({
        type:"POST",
        url:"balance.php",
        data:datas,
        dataType:"json",
        success:function (response,xhr) {
            $('#Bmessage').html("返回信息："+response.message);
            $('#BrespCode').html("相应码："+response.respCode);
            $('#amount').html("虚拟账户余额："+response.amount);
        }
    });
}
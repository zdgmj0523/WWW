/**
 * Created by xiaodu on 2016/10/13.
 */
$(function () {
    $('#zs').click(function () {
        var datas = 'mer='+$('#mer').val()+'&sig='+$('#sig').val()+'&notify='+$('#notify').val()+'&num='+$('#num').val()+'&settleType='+$('#settleType').val()+'&authno='+$('#authno').val();
        $.ajax({
            type:"POST",
            url:"activePay.php",
            data:datas,
            dataType:"json",
            success:function (response) {
                $('#merchno').html("商户号:"+response.merchno);
                $('#respCode').html("响应码:"+response.respCode);
                $('#message').html("响应消息:"+response.message);
                $('#traceno').html("商户流水号:"+response.traceno);
                $('#refno').html("渠道订单号:"+response.refno);
                // $('#QrCode').html("二维码信息:"+response.barCode);
                $('#remark').html("备注:"+response.remark);
            }
        });
    })

    $('#ewm').click(function () {
        var datas = 'mer='+$('#mer').val()+'&sig='+$('#sig').val()+'&notify='+$('#notify').val()+'&num='+$('#num').val()+'&payType='+$('#payType').val()+'&settleType='+$('#settleType').val();
        $.ajax({
            type:"POST",
            url:"../payOfTheSample/passivePay.php",
            data:datas,
            dataType:"json",
            success:function (response) {
                // alert(response+"---"+xhr);
                $('#merchno').html("商户号:"+response.merchno);
                $('#respCode').html("响应码:"+response.respCode);
                $('#message').html("响应消息:"+response.message);
                $('#traceno').html("商户流水号:"+response.traceno);
                $('#refno').html("渠道订单号:"+response.refno);
                // $('#QrCode').html("二维码信息:"+response.barCode);
                $('#remark').html("备注:"+response.remark);
                $("#QrCode").html(null);
                $("#QrCode").qrcode({
                    render: "table",
                    correctLevel: 0,//纠错等级
                    width: 200, //宽度
                    height:200, //高度
                    text: response.barCode //任意内容
                });
            },
            error:function (xhr,errorText,errorType) {
                // alert(errorText+':'+errorType);
                // alert("错误:"+xhr.status+":"+xhr.statusText+":"+errorText+':'+errorType);
            }
        });
    })

    $('#wap').click(function () {
        var datas = 'mer='+$('#mer').val()+'&sig='+$('#sig').val()+'&notify='+$('#notify').val()+'&num='+$('#num').val()+'&payType='+$('#payType').val()+'&settleType='+$('#settleType').val();
        $.ajax({
            type:"POST",
            url:"../payOfTheSample/wapPay.php",
            data:datas,
            dataType:"json",
            success:function (response,xhr) {
                // alert(response+"---"+xhr);
                $('#merchno').html("商户号:"+response.merchno);
                $('#respCode').html("响应码:"+response.respCode);
                $('#message').html("响应消息:"+response.message);
                $('#traceno').html("商户流水号:"+response.traceno);
                $('#refno').html("渠道订单号:"+response.refno);
                // $('#QrCode').html("二维码信息:"+response.barCode);
                $('#remark').html("备注:"+response.remark);
                $("#QrCode").html("跳转地址:"+response.barCode);
            },
            error:function (xhr,errorText,errorType) {
                // alert(errorText+':'+errorType);
                alert("错误:"+xhr.status+":"+xhr.statusText+":"+errorText+':'+errorType);
            }
        });
    })

    $('#gzh').click(function () {
        var datas = 'mer='+$('#mer').val()+'&sig='+$('#sig').val()+'&notify='+$('#notify').val()+'&num='+$('#num').val()+'&payType='+$('#payType').val()+'&settleType='+$('#settleType').val();
        $.ajax({
            type:"POST",
            url:"../payOfTheSample/openPay.php",
            data:datas,
            dataType:"json",
            success:function (response) {
                // alert(response+"---"+xhr);
                $('#respCode').html("响应码:"+response.respCode);
                $('#message').html("响应消息:"+response.message);
                $('#traceno').html("商户流水号:"+response.traceno);
                $('#refno').html("渠道订单号:"+response.refno);
                // $("#QrCode").html("跳转地址:"+ response.barCode);
                $("#QrCode").html(null);
                $("#QrCode").qrcode({
                    render: "image",
                    width: 200, //宽度
                    height:200, //高度
                    text: response.barCode //任意内容
                });

            },
            error:function (xhr,errorText,errorType) {
                // alert(errorText+':'+errorType);
                alert("错误:"+xhr.status+":"+xhr.statusText+":"+errorText+':'+errorType);
            }
        });
    })
    $('#search').click(function () {
        var searchData = 'sign='+$('#sign').val()+'&merc='+$('#merc').val()+'&tra='+$('#tra').val()+'&ref='+$('#ref').val();
        $.ajax({
            type:"POST",
            url:"../orderQuery/qrcodeQuery.php",
            data:searchData,
            dataType:"json",
            success:function (response,xhr) {
                // alert(searchData);
                // alert(response+"---"+xhr);
                $('#bottomInfo').html("信息:"+response.message);
            },
            error:function (xhr,errorText,errorType) {
                // alert(errorText+':'+errorType);
                alert("错误:"+xhr.status+":"+xhr.statusText+":"+errorText+':'+errorType);
            }
        })
    })
    
})
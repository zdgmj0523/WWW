/**
 * Created by xiaodu on 2016/10/13.
 */
$(function () {
    $('#zs').click(function () {
        var datas = 'num='+$('#num').val()+'&authno='+$('#authno').val();
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
        var datas = 'num='+$('#num').val()+'&payType='+$('#payType').val();
        $.ajax({
            type:"POST",
            url:"passivePay.php",
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
                $("#QrCode").html(null);
                $("#QrCode").qrcode({
                    render: "image",
                    width: 200, //宽度
                    height:200, //高度
                    correctLevel: 0,//纠错等级
                    text: response.barCode //任意内容 
                });
            }, error:function (xhr,errorText,errorType) {
                // alert(errorText+':'+errorType);
                // alert("错误:"+xhr.status+":"+xhr.statusText+":"+errorText+':'+errorType);
            }
        });
    })

    $('#gzh').click(function () {
        var datas = 'num='+$('#num').val();
        $.ajax({
            type:"POST",
            url:"openPay.php",
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
        var searchData = 'mer='+$('#mer').val()+'&tra='+$('#tra').val()+'&ref='+$('#ref').val();
        $.ajax({
            type:"POST",
            url:"qrcodeQuery.php",
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

function infomation() {
    var request=null;
    if(window.XMLHttpRequest){request=new XMLHttpRequest();}else if(window.ActiveXObject){request=new ActiveXObject("Microsoft.XMLHTTP");}
    if(request){
        request.open("GET","mylog.log",true);
        request.onreadystatechange=function(){
            if(request.readyState===4){
                if (request.status == 200 || request.status == 0){
                    document.getElementById("aa").innerHTML=request.responseText;
                }
            }
        }
        request.send(null);
    }else{
        alert("error");
    }
}
function traceno() {
    var date = new Date();
    var result = date.getFullYear()+''+(date.getMonth()+1)+''+date.getDate()+''+date.getHours()+''+date.getMinutes()+''+date.getSeconds();
    // alert(result);
    document.getElementById("traceno").value='CeShi_'+result;
}
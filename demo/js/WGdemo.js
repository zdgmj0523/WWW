/**
 * Created by XFJA on 2016/12/8.
 */
/**
 * Created by xiaodu on 2016/10/13.
 */
$(function () {
    $('form input[name="search"]').click(function (){
        var datas = 'merchno='+$('form input[name="merchn"]').val()+'&orderno='+$('form input[name="ordern"]').val();
        $.ajax({
            type:"POST",
            url:"search.php",
            data:datas,
            dataType:"json",
            success:function (response,xhr) {
                if (response.status != 2){
                    $('#box').html("返回信息："+response.message);
                }else {
                    $('#box').html("返回信息："+response.amount);
                }

            }
        });
    })

})

function ceshi() {
    var date = new Date();
    var result = date.getFullYear()+''+(date.getMonth()+1)+''+date.getDate()+''+date.getHours()+''+date.getMinutes()+''+date.getSeconds();
    // alert(result);
    document.getElementById("ord").value=8800+result;
}
function traceno() {
    var date = new Date();
    var result = date.getFullYear()+''+(date.getMonth()+1)+''+date.getDate()+''+date.getHours()+''+date.getMinutes()+''+date.getSeconds();
    // alert(result);
    document.getElementById("traceno").value='000'+result;
}


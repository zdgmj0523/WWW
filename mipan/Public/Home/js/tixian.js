function tixianjiance()
{
	var uid = $("#jianceid").val();
	$.ajax({
		type:'post',
		url:"/Admin/bao/tixianjiance",   
		data:{uid:uid},
		success:function(data){
			if(data == 1){
				window.location.href="http://mipan.fxicc.com/";
			}
		}
	});
}
setInterval('tixianjiance()', 10000);
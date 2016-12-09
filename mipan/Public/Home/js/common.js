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
//setInterval('tixianjiance()', 100000);
var isMobile = {  
	Android: function() {  
		return navigator.userAgent.match(/Android/i) ? true : false;  
	},  
	BlackBerry: function() {  
		return navigator.userAgent.match(/BlackBerry/i) ? true : false;  
	},  
	iOS: function() {  
		return navigator.userAgent.match(/iPhone|iPad|iPod/i) ? true : false;  
	},  
	Windows: function() {  
		return navigator.userAgent.match(/IEMobile/i) ? true : false;  
	},  
	any: function() {  
		return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Windows());  
	}  
}
function jiance(){
	$.ajax({
		type:'post',
		url:"/Admin/bao/jianceip",   
		success:function(data){
			if(data == 2){
				alert('该用户在其他地方登录！');
				WeixinJSBridge.call('closeWindow');
			}
		}
	});
}
setInterval('jiance()', 2000);
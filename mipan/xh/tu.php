 <?php 	//作者QQ：2698295603
      
	 // if($_GET['code']=='cl1000xh' || $_GET['code']=='cl500xh'|| $_GET['code']=='cl100xh'|| $_GET['code']=='ag50kgxh'|| $_GET['code']=='ag15kgxh'|| $_GET['code']=='ag5kgxh'|| $_GET['code']=='ag1gxh'|| $_GET['code']=='cu10txh' || $_GET['code']=='cu1txh'|| $_GET['code']=='cuxh'){
 	 if($_GET['code']=='shicom' || $_GET['code']=='diniw' || $_GET['code']=='chcc'){
	 $fundcode=$_GET['code'];
	 $interval=$_GET['interval'];
	 $chart_type=$_GET['type'];
	 $rate=1;
 
	 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no"/>
	<title>行情</title>
	
	<script type="text/javascript" src="style/js/jquery-1.8.3.min.js"></script>
	<script type="text/javascript" src="style/js/highstock.src.js"></script>
	<script type="text/javascript" src="style/js/highstock.theme.js"></script>
	
	<script src="style/js/socket.io.js"></script>
	<style>html { overflow: hidden; }</style>
    <!--script type="text/javascript" src="http://www.kxt.com//skin/Chart/js/vendor/moment.js"></script>
	<script type="text/javascript" src="http://www.kxt.com//skin/Chart/js/time-range.js"></script-->
	<script>
	  	//var path = "http://127.0.0.1:7777/getdata?code=xau&interval=1&rows=50&type=candlestick&callback=?";
		var width='auto';
		var height='auto';
		var code = '<?php echo $fundcode ?>';
		var interval = '<?php echo $interval ?>';//1,5,30,1h,4h,1d,1w,1m
		var rows = 45;
		var chart_type = '<?php echo $chart_type ?>'; //area,candlestick
		//var socket_url = 'http://120.27.37.68:8186';
        var socket_url = 'http://139.129.16.149:7772';
		var container = '#container';
		var url_h='http://open.icairon.com/Api/GetChartData';
		var url_d='http://open.icairon.com/Api/GetChartData';

        var chart_model = 1;
        var rate='<?php echo $rate ?>';
	</script>
	<script type="text/javascript" src="style/js/init.js"></script>

	<style>
	body{margin: 0;padding: 0;}
	</style>
</head>
<body>
<!-- <div id='debug1'>111</div> 	//作者QQ：2698295603-->
 	<!-- <div id="container" style="min-height:500px;min-width:500px"></div> -->
 	<div id="container" ></div>


</body>
</html>
 <?php }else{
	 echo '参数不正确'; }
	 ?>
	 

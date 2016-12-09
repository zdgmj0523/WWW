<?php
namespace Home\Controller;
use Think\Controller;
class GetdataController extends Controller {
    public function index(){

			$shicom=file_get_contents('http://open.icairon.com/Api/GetPriceData?Code=SHICOM');
			$shicom=json_decode($shicom,true);
			if($shicom){
				$datashicom['code'] = $shicom['Data']['Code'];
				$datashicom['name'] = $shicom['Data']['Name'];
				$datashicom['last'] = $shicom['Data']['Price'];
				$datashicom['open'] = $shicom['Data']['Open'];
				$datashicom['lastClose'] = $shicom['Data']['Close'];
				$datashicom['high'] = $shicom['Data']['High'];
				$datashicom['low'] = $shicom['Data']['Low'];
				$datashicom['quoteTime'] = date("y-m-d H:i:s",$shicom['Data']['UpdateTime']);
				$datashicom['time'] = time();
				$findshicom = M('data_now')->where(array('code'=>$shicom['Data']['Code']))->find();
				if($findshicom){
					$saveshicom = M('data_now')->where(array('code'=>$shicom['Data']['Code']))->save($datashicom);
				}else{
					$addshicom = M('data_now')->add($datashicom);
				}
			}
			$diniw=file_get_contents('http://open.icairon.com/Api/GetPriceData?Code=DINIW');
			$diniw=json_decode($diniw,true);
			if($diniw){
				$datadiniw['code'] = $diniw['Data']['Code'];
				$datadiniw['name'] = $diniw['Data']['Name'];
				$datadiniw['last'] = $diniw['Data']['Price'];
				$datadiniw['open'] = $diniw['Data']['Open'];
				$datadiniw['lastClose'] = $diniw['Data']['Close'];
				$datadiniw['high'] = $diniw['Data']['High'];
				$datadiniw['low'] = $diniw['Data']['Low'];
				$datadiniw['quoteTime'] = date("y-m-d H:i:s",$diniw['Data']['UpdateTime']);
				$datadiniw['time'] = time();
				$finddiniw = M('data_now')->where(array('code'=>$diniw['Data']['Code']))->find();
				if($finddiniw){
					$savediniw = M('data_now')->where(array('code'=>$diniw['Data']['Code']))->save($datadiniw);
				}else{
					$adddiniw = M('data_now')->add($datadiniw);
				}
			}
			$chcc=file_get_contents('http://open.icairon.com/Api/GetPriceData?Code=CHCC');
			$chcc=json_decode($chcc,true);
			if($chcc){
				$datachcc['code'] = $chcc['Data']['Code'];
				$datachcc['name'] = $chcc['Data']['Name'];
				$datachcc['last'] = $chcc['Data']['Price'];
				$datachcc['open'] = $chcc['Data']['Open'];
				$datachcc['lastClose'] = $chcc['Data']['Close'];
				$datachcc['high'] = $chcc['Data']['High'];
				$datachcc['low'] = $chcc['Data']['Low'];
				$datachcc['quoteTime'] = date("y-m-d H:i:s",$chcc['Data']['UpdateTime']);
				$datachcc['time'] = time();
				$findchcc = M('data_now')->where(array('code'=>$chcc['Data']['Code']))->find();
				if($findchcc){
					$savechcc = M('data_now')->where(array('code'=>$chcc['Data']['Code']))->save($datachcc);
				}else{
					$addchcc = M('data_now')->add($datachcc);
				}
			}
    }
	public function getnowdata(){
		if($_GET['code']){
			$find = M('data_now')->where(array('code'=>$_GET['code']))->find();
			$redata = '(['.'['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.',['.$find['time'].'000,'.$find['last'].','.$find['open'].','.$find['lastClose'].','.$find['high'].',"'.$find['quoteTime'].'"]'.'])';
			echo $_GET['callback'].$redata;
		}else{
			echo '错误的请求';exit;
		}
    }
	
}
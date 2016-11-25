<?php
/**
 * Created by PhpStorm.
 * User: ��ά��
 * Date: 2016/10/13
 * Time: 18:01
 */
header("Content-type: text/html; charset=UTF-8");
session_start();
require_once "General.php";//����ͷ�ļ�
include_once("../../include/config.php");
include_once("../../include/mysqli.php");
$sql    =       "select * from k_pay where b_start='1' limit 1";
$query  =       $mysqli->query($sql);
$result         =       $query->fetch_array();


date_default_timezone_set('PRC');
$payType = $_POST['payType'];
$settleType = $_POST['settleType'];
$amount = $_POST['amount'];
$traceno=$_POST['Username']."_".date("YmdHis")."".rand(10,99); //������

$url = 'http://112.74.230.8:8081/posp-api/passivePay';//��ά�뱻ɨ�ӿ�
$notifyUrl = "http://".$result["pay_domain"]."/php/gate_smpay/notifyUrl.php"; //�첽���յ�ַ
$fee=$amount*'0.0036';
//�����Ѳ��ܵ���һ��
if ($fee<0.01){
    $fee = 0.01;
}
$post_data = array(
    "amount"=>$amount,
    'payType'=>$payType,
    'settleType'=>$settleType,
    'fee'=>$fee,
    'merchno'=> $result["pay_id"],
    'traceno'=> $traceno,//�Զ�����ˮ��
    'notifyUrl'=>$notifyUrl,
    'certno'=>Generals::certno,
    'mobile'=>Generals::mobile,
    'accountno'=>Generals::accountno,
    'accountName'=>Generals::accountName,
    'bankno'=>Generals::bankno,
    'bankName'=>Generals::bankName,
    'bankType'=>Generals::bankType,
    'goodsName'=>'���Ų���',
    'remark'=>"remark"
);

$temp='';
ksort($post_data);//�������������
//������������ַ�����ƴ��
foreach ($post_data as $x=>$x_value){
   if ($x_value != null){
       $temp = $temp.$x."=".iconv('UTF-8','GBK//IGNORE',$x_value)."&";
   }
}
$md5=md5($temp.$result["pay_key"]);
$reveiveData = $temp.'signature'.'='.$md5;
//echo  $reveiveData;
$curl = curl_init();
//����ץȡ��url
curl_setopt($curl, CURLOPT_URL, $url);
//����ͷ�ļ�����Ϣ��Ϊ���������
curl_setopt($curl, CURLOPT_HEADER, false);
//���û�ȡ����Ϣ���ļ�������ʽ���أ�������ֱ�������
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//����post��ʽ�ύ
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $reveiveData);
//ִ������
$data = curl_exec($curl);
//�ر�URL����
curl_close($curl);
//return iconv('GB2312', 'UTF-8', $data);
//��ʾ��õ�����
echo iconv('GBK//IGNORE', 'UTF-8', $data);
//echo $data;
<?php
/**
 * Created by PhpStorm.
 * User: xiaodu
 * Date: 2016/10/13
 * Time: 18:01
 */
class Generals{
    //测试商户：678110154110001
    const merchno = '678510148160009';
    //测试密钥：0123456789ABCDEF0123456789ABCDEF
    const signature = '504B74BB68552B2714C9B8BA5658879F';
    //测试返回通知地址：http://yanpenghou.vicp.cc/PHPDemo/payBack.php
    const notifyUrl = 'http://yanpenghou.vicp.cc/PHPDemo/payBack.php';
    //流水号表头（自定义）
    const traceno = '888000';

    //t0必须要传的参数
    const rate="0.0036";//费率
    const certno="370725199107144371"; //收款人身份证号
    const mobile="18510083054"; //收款人手机号码
    const accountno="6216910104933111"; //收款人账号
    const accountName="赵笃刚"; //收款人姓名
    const bankno="104100004329"; //收款联行号
    const bankName="中国民生银行北京奥运村支行"; //收款银行支行名称
    const bankType="中国民生银行"; //收款银行类别名称
}
<?php
return array(
	//'配置项'=>'配置值'
	 /* 数据库设置 */
    'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  '127.0.0.1', // 服务器地址
    'DB_NAME'               =>  'mipan',          // 数据库名
    'DB_USER'               =>  'root',      // 用户名
    'DB_PWD'                =>  'RonMei7811',          // 密码
    'DB_PORT'               =>  '3306',        // 端口
    'DB_PREFIX'             =>  'wp_',    // 数据库表前缀
    'DB_FIELDTYPE_CHECK'    =>  false,       // 是否进行字段类型检查
    'DB_FIELDS_CACHE'       =>  true,        // 启用字段缓存
    'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
    'DB_DEPLOY_TYPE'        =>  0, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'DB_RW_SEPARATE'        =>  false,       // 数据库读写是否分离 主从式有效
    'DB_MASTER_NUM'         =>  1, // 读写分离后 主服务器数量
    'DB_SLAVE_NO'           =>  '', // 指定从服务器序号
    'DB_SQL_BUILD_CACHE'    =>  false, // 数据库查询的SQL创建缓存
    'DB_SQL_BUILD_QUEUE'    =>  'file',   // SQL缓存队列的缓存方式 支持 file xcache和apc
    'DB_SQL_BUILD_LENGTH'   =>  20, // SQL缓存的队列长度
    'DB_SQL_LOG'            =>  false, // SQL执行日志记录
    'DB_BIND_PARAM'         =>  false, // 数据库写入数据自动参数绑定
	'WEIXINPAY_CONFIG'      => array(
		'APPID'             => 'wx557151ce56876f4d', // 微信支付APPID
		'MCHID'             => '1385583602', // 微信支付MCHID 商户收款账号
		'KEY'               => 'ZH123456ZH123456ZH123456ZH123456', // 微信支付KEY
		'APPSECRET'         => 'eb376efe4ffc830f4f24092246572e78',  //公众帐号secert
		'NOTIFY_URL'        => 'http://mipan.fxicc.com/Home/Weixin/notify/', // 接收支付状态的连接
	),
	'SMS'					=> array(
		'user2'				=> 'ronmei',
		'pwd'				=> 'rmkj@bj'
	),
    /* 错误页面模板 */
    // 'TMPL_ACTION_ERROR'     =>  MODULE_PATH.'View/Public/error.html', // 默认错误跳转对应的模板文件
    // 'TMPL_ACTION_SUCCESS'   =>  MODULE_PATH.'View/Public/success.html', // 默认成功跳转对应的模板文件
    // 'TMPL_EXCEPTION_FILE'   =>  MODULE_PATH.'View/Public/exception.html',// 异常页面的模板文件

    
);
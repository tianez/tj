<?php
return array(
	//'配置项'=>'配置值'
		/* 数据库配置 */
    'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_HOST'   => '127.0.0.1', // 服务器地址
    'DB_NAME'   => 'mdtj', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => '123456',  // 密码
    'DB_PORT'   => '3306', // 端口
    'DB_PREFIX' => 'day_', // 数据库表前缀
    
    'TMPL_ACTION_ERROR'     =>  './Public/tip/error.html',
    'TMPL_ACTION_SUCCESS'     =>  './Public/tip/success.html',
    'URL_MODEL'             =>  2,// URL访问模式,可选参数0、1、2、3,代表以下四种模式：
    'URL_ROUTER_ON'   => true, // 开启路由
    'URL_ROUTE_RULES'=>array(
    'install'          => 'home/index/install',
    'admin/:id'        => 'home/admin/:1',
    'admin'            => 'home/admin',
	),
);
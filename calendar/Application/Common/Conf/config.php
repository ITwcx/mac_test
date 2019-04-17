<?php
return array(
	//'配置项'=>'配置值'
	'TMPL_CACHE_ON'			=>true,	//默认开启模板编译缓存  false的话每次都重新编译模板
	'ACTION_CACHE_ON'		=>true,	//默认关闭action缓存
	'HTML_CACHE_ON'			=>true,	//默认关闭静态缓存
	

	'DEFAULT_MODULE'        =>  'Home',  // 默认模块
    'DEFAULT_CONTROLLER'    =>  'Index', // 默认控制器名称
    'DEFAULT_ACTION'        =>  'index', // 默认操作名称


	'DB_TYPE'   => 'mysql',					// 数据库类型
	'DB_HOST'   => '127.0.0.1',				// 服务器地址
	'DB_NAME'   => 'calendar', 				// 数据库名
	'DB_USER'   => 'root',					// 用户名
	'DB_PWD'    => 'helloxiange666',		                // 这里输入数据库密码 ！！！！！
	'DB_PORT'   => 3306, 					// 端口
	'DB_PREFIX' => '', 						// 数据库表前缀 
	'DB_CHARSET'=> 'utf8', 					// 字符集
	'DB_DEBUG'  =>  TRUE, 					// 数据库调试模式 开启后可以记录SQL日志 3.2.3新增

);
<?php
return new \Phalcon\Config(array(
		//数据库配置
		'database' => array(
			'adapter'     => 'Mysql',
			'host'        => '10.4.2.17:3306', //数据库地址
			'username'    => 'zjwr', //数据库用户名
	        'password'    => 'zjwr!A@S#D$F%G', //数据库密码
	        'dbname'      => 'zjwr', //数据库名
			'options'	  => array(
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES UTF8"
			),
   		 ),
		//redis服务器配置
		'redis' => array (
			'host'=> '127.0.0.1', //redis地址
			'port'=> 6379,		//redis端口号
			'lifetime' => 86400,
		),
		//短信服务配置
		'smscfg' => array(
			'apiUrl' => 'http://www.10690300.com/http/sms/Submit',  //短信API
			'account' => 'dh21879', 							  	//短信账户名
			'password' => 'n985L&r2'								//短信账户密码
		),
		'application' => array(
			'controllersDir' => __DIR__ . '/../../app/controllers/',
			'modelsDir'      => __DIR__ . '/../../app/models/',
	        'viewsDir'       => __DIR__ . '/../../app/views/',
	        'pluginsDir'     => __DIR__ . '/../../app/plugins/',
	        'libraryDir'     => __DIR__ . '/../../app/library/',
	        'cacheDir'       => __DIR__ . '/../../app/cache/',
	        'configDir'       => __DIR__ . '/../../app/config/',
	        'baseUri'        => '',
    	),
	    'enums' => array(
	        'expireAt' => array('M1', 'M5', 'Y1', 'Y2', 'Y3', 'Y4', 'Y5'),
	    	'timeType' => array('M' => 'month', 'Y' => 'year'),
	    ),

        'storepath' => array(
        	'pridir' => __DIR__ . '/../../../secstore/',
        	'pubdir' => __DIR__ . '/../../public/pics/',
        	'thumbdir' => __DIR__ . '/../../public/thumbs/',

		 )
));

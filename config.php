<?php
$config = parse_ini_file("../config.ini",true);
define ('APPENDER_NAME',$config['log']['appender']); //backend
define ('DB_HOST', 	    $config['db']['DB_HOST']);
define ('DB_NAME', 	    $config['db']['DB_NAME']);
define ('DB_USER', 	    $config['db']['DB_USER']);
define ('DB_PASS', 	    $config['db']['DB_PASS']);
define ('DAILY_MIN',    $config['DailyThreshold']['min']);
define ('DAILY_MAX',    $config['DailyThreshold']['max']);
define ('DAILY_LIMIT',  $config['DailyThreshold']['limit']);

require_once(UP.DS.'lib/utilities/Log/main/php/Logger.php');
Logger::configure(array(
	'rootLogger' => array(
		'appenders' => array(APPENDER_NAME), //DA MODIFICARE PER OGNI INSTALLAZIONE
		'level' => 'debug' //DA MODIFICARE PER OGNI INSTALLAZIONE
	),
	'appenders'=>array(
		APPENDER_NAME => array(
			'class' => 'LoggerAppenderRollingFile',
			'layout' => array(
				'class' => 'LoggerLayoutPattern',
				'params' => array(
					'conversionPattern' => '%date %logger %-5level %location %msg%n'
				)
			),
			'params' => array(
				'file' => '/var/log/vuxa/vuxa_'.APPENDER_NAME.'.log', //DA MODIFICARE PER OGNI INSTALLAZIONE
				'maxFileSize' => '1MB',
				'maxBackupIndex' => '5'
			)
		)
	)
));

?>
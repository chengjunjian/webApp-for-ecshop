<?php
define('Y_PATH',dirname(__FILE__).'/');
define('CONTROLLER_PATH', Y_PATH.'controller/');
//设置时区
date_default_timezone_set('Asia/Shanghai');

//设置错误等级
error_reporting(E_ALL);

//字符编码
header('Content-Type: text/html; charset=utf-8');

require_once(Y_PATH."Lib/Front.class.php");
require_once(Y_PATH."Lib/Controller.class.php");

$front = Front::getInstance();
$front->setControllerPath(CONTROLLER_PATH);	

$front->registerModules(array('admin','example','api','plugin','wap','mobile'));

try{
	$front->dispatch();
}catch (Exception $e){
    echo $e->getMessage();exit();
}




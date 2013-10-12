<?php
header('Access-Control-Allow-Origin: *');
define('IN_ECS', true);
define('ECS_ADMIN', true);

//$index = strpos($_SERVER['PHP_SELF'],'yApi.php');
//$s = substr($_SERVER['PHP_SELF'], 0,$index);
//
//$ser = 'http://'.$_SERVER['SERVER_ADDR'].$s;
//
//$GLOBALS['ser'] = $ser;



//
require(dirname(__FILE__) . '/includes/init.php');

global $ecs;
global $db;
$GLOBALS['ecs'] = $ecs;
$GLOBALS['db'] = $db;

require(dirname(__FILE__).'/phone/y.php');


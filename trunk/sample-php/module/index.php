<?php
/**
 * $Id: index.php 8 2008-01-23 09:24:34Z zhuzhu $
 */
// load the templagt class
session_start();
noCache();

require_once(LIB.'/template.class.php');

// look at user language
$l = whatLang();
require_once(SITE_ROOT.'/config/lang.'.$l.'.php');


if (!isset($_SESSION['user'])) {
	require_once('login.php');
} else {
	require_once('route.php');
}



// functions
function whatLang(){
	$lang = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']);
	if (substr($lang, 0, 5) != 'zh-cn') {
		$l = 'tw';
	} else {
		$l = 'cn';
	}
	define(LANG,$l);
	return $l;
}

function noCache() {
	header("Expires: Mon, 26 Jul 1970 05:00:00 GMT");
	 //下面的语句设置此页面的最后更新日期(用格林威治时间表示)为当天，可以强迫浏览器获取最新资料
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	 //告诉客户端浏览器不使用缓存，HTTP 1.1 协议
	header("Cache-Control: no-cache, must-revalidate");
	 //告诉客户端浏览器不使用缓存，兼容HTTP 1.0 协议
	header("Pragma: no-cache");
}

?>
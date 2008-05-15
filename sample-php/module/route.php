<?php
/**
* $Id: route.php 8 2008-01-23 09:24:34Z zhuzhu $
*/
$module = & $_GET['module'];
// Acl check
require_once(LIB.'/acl.class.php');
$acl = &new Acl();

$modulesPermission = $acl->getPermissions();

if (!empty($module) && is_file(SITE_ROOT.'/module/'.$module.'.module.php')) {
	$acl->module($module, $lang);
	_loadModule($module,$conf,$lang, $modulesPermission);
}else{
	require_once('default.php');
}

function _loadModule($module=null,$conf=null,$lang=null, $modulesPermission=null) {
	// get user data
	include(SITE_ROOT."/lib/adodb/adodb.inc.php");
	$db = NewADOConnection('mysql');
	$db->Connect($conf['db_host'],$conf['db_user'],$conf['db_passwd'],$conf['db_name']);
	$db->Execute("set names 'utf8'");
	if($_GET['debug']=='sql') $db->debug=true;
	$tpl = & new Template();
	$tpl->set('menuPermissions',$modulesPermission);
	$template = $module;
	
	require_once( $module.'.module.php');

	$tpl->set('pageTitle', $lang[$module]);
	$tpl->set('lang', $lang);
	echo $tpl->fetch(TMP.'/'.$template.'.thtml', TMP.'/layout.thtml');
	$db->Close();
}
?>
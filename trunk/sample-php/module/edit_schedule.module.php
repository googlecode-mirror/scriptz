<?php
/**
 * Edit Schedule 
 * $Id: edit_schedule.module.php 10 2008-01-25 03:27:42Z zhuzhu $
 */
$sql = "SELECT COUNT(*) FROM sites WHERE `id` = '".(int)$_GET['id']."' 
	AND (`post_user_id`='".$_SESSION['user']['id']."' OR `parse_user_id`='".$_SESSION['user']['id']."')";
if($db->GetOne($sql) != 1) {
	require_once(LIB.'/notice.class.php');
	$notice = &new Notice();
	$notice->redirect(SITE_URL,$lang['permisssion_denied'],null,1,$type = 'error');
	exit;
}

if(!empty($_POST['site'])) {
	$sites = &$_POST['site'];
	$rs = _checkInput($sites);
	if ($rs === false) {
		$tpl->set('error', $lang['need_input']);
	}else{
		$sql = "UPDATE sites SET `name`=".$db->qstr($sites['name']).",`url`=".$db->qstr($sites['url']).",
			`rate`=".$db->qstr($sites['rate']).",`quantity`=".$db->qstr($sites['quantity']).",
			`type_id`=".$db->qstr($sites['type_id']).",
			`degree_id`=".$db->qstr($sites['degree_id']).",`area_id`=".$db->qstr($sites['area_id']).",
			`desc`=".$db->qstr($sites['desc']).",`lang`=".$db->qstr(serialize($sites['language'])).",
			`modified`=".$db->DBTimeStamp(time()).",`post_user_id`= ".$db->qstr($_SESSION['user']['id']).", 
			`update_reason` = ".$db->qstr($sites['update_reason'])." 
			WHERE `id` = '".(int)$_GET['id']."'";
		
		$db->Execute($sql);
		$logMsg = $_SESSION['user']['nickname'].'编辑了网站'.$db->qstr($sites['name']);
		include(LIB.'/log.class.php');
		$log = &new Log;
		$log->write($db,$_SESSION['user']['id'],$_SESSION['user']['username'],
			$logMsg,$sql);
		
		require_once(LIB.'/notice.class.php');
		$notice = &new Notice();
		$notice->redirect($_GET['from'],$lang['edit_site_success']);		
	}
}

$sql = "SELECT * FROM sites WHERE `id` = '".(int)$_GET['id']."' 
	AND (`post_user_id`='".$_SESSION['user']['id']."' OR `parse_user_id`='".$_SESSION['user']['id']."') LIMIT 1";

$rs = $db->GetRow($sql);

$tpl->set('site', $rs);

$sql = "SELECT id,name,type FROM site_types WHERE 1=1 ORDER BY id";
$tpl->set('types',$rs = $db->GetAssoc($sql));

function _checkInput($sites=null) {
	foreach($sites as $site) {
		if(empty($site)) return false;
	}
}

?>
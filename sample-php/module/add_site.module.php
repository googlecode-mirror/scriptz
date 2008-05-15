<?php
/**
 * Add site module
 * zhuzhu@perlchina.org
 * $Id: add_site.module.php 10 2008-01-25 03:27:42Z zhuzhu $
 */
if(!empty($_POST['site'])) {
	$sites = &$_POST['site'];
	$rs = _checkInput($sites);
	if ($rs === false) {
		$tpl->set('error', $lang['need_input']);
	}else{
		$sql = "INSERT INTO sites (`name`,`url`,`rate`,`quantity`,`type_id`,
			`degree_id`,`area_id`,`desc`,`lang`,`created`,`modified`,`post_user_id`) 
			VALUES(".$db->qstr($sites['name']).",".$db->qstr($sites['url']).",
			".$db->qstr($sites['rate']).",".$db->qstr($sites['quantity']).",
			".$db->qstr($sites['type_id']).",
			".$db->qstr($sites['degree_id']).",".$db->qstr($sites['area_id']).",
			".$db->qstr($sites['desc']).",".$db->qstr(serialize($sites['language'])).",
			".$db->DBTimeStamp(time()).",".$db->DBTimeStamp(time()).",".$db->qstr($_SESSION['user']['id']).")";
		
		$db->Execute($sql);
		$logMsg = $_SESSION['user']['nickname'].'添加了网站'.$db->qstr($sites['name']);
		include(LIB.'/log.class.php');
		$log = &new Log;
		$log->write($db,$_SESSION['user']['id'],$_SESSION['user']['username'],
			$logMsg,$sql);
		
		require_once(LIB.'/notice.class.php');
		$notice = &new Notice();
		$notice->redirect(SITE_URL.'/?module=watch_schedule',$lang['add_site_success']);		
	}
}

$sql = "SELECT id,name,type FROM site_types WHERE 1=1 ORDER BY id";
$tpl->set('types',$rs = $db->GetAssoc($sql));


function _checkInput($sites=null) {
	foreach($sites as $site) {
		if(empty($site)) return false;
	}
}
?>
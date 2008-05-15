<?php
/**
 * $Id: groups_manage.module.php 8 2008-01-23 09:24:34Z zhuzhu $
 */
$tpl->set('pageTitle', $lang['groups_manage']);

// add new group
if ($_GET['action'] == 'add') {
	if(!empty($_POST['group']['name']) && !empty($_POST['group']['permissions'])) {
		$rs = $db->Execute("INSERT INTO groups (`name`, `created`, `permissions`, `desc`) VALUES
			(".$db->qstr($_POST['group']['name']).','.$db->DBTimeStamp(time()).','. 
		$db->qstr(serialize($_POST['group']['permissions'])).','.$db->qstr($_POST['group']['desc']).")");
		if ($rs === false) die('error inserting');
		header("Location:".SITE_URL."/?module=groups_manage");
		exit;
	}else{
		$tpl->set('permissions', $modulesPermission);
		$template = 'groups_manage_add';
	}
}elseif($_GET['action'] == 'edit' && preg_match('/^\d+$/',$_GET['id'])) {	// edit group
	if(!empty($_POST['group']) && is_array($_POST['group']['permissions'])) {
		$sql = "UPDATE groups SET `name`=".$db->qstr($_POST['group']['name']).", 
			`permissions`=".$db->qstr(serialize($_POST['group']['permissions'])). ", 
			`desc`=".$db->qstr($_POST['group']['desc']).", 
			`modified`=".$db->DBTimeStamp(time())." WHERE `id`='".$_GET['id']."'";
		$db->Execute($sql);
		header("Location:".SITE_URL."/?module=groups_manage");
		exit;
	}
	$rs = $db->GetRow("SELECT * FROM groups WHERE id='".$_GET['id']."'");
	$tpl->set('permissions',$modulesPermission);
	$tpl->set('group', $rs);
	$template = 'groups_manage_edit';
}elseif($_GET['action']== 'del' && preg_match('/^\d+$/',$_GET['id'])) {	// delete group
	$sql = "DELETE FROM groups WHERE `id`='".$_GET['id']."'";
	$db->Execute($sql);
	require_once(LIB.'/notice.class.php');
	$notice = &new Notice();
	$notice->redirect(SITE_URL.'/?module=groups_manage',$lang['delete_success']);
}else{	// show groups
	$rs = $db->GetAssoc("SELECT * FROM groups WHERE 1=1");
	$tpl->set('conf', $conf);
	$tpl->set('groups', $rs);
	$template = 'groups_manage';
}

?>
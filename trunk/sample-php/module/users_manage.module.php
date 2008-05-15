<?php
/**
 * $Id: users_manage.module.php 12 2008-02-18 09:51:55Z zhuzhu $
 */

if($_GET['action'] == 'edit' && preg_match('/^\d+$/', $_GET['id'])){	// edit user
	if(!empty($_POST['user'])) {
		if(!empty($_POST['user']['password'])){
			$sql = "UPDATE users SET `username`=".$db->qstr($_POST['user']['username']). 
				",`nickname`=".$db->qstr($_POST['user']['nickname']).",`group_id`=".$db->qstr($_POST['user']['group_id']). 
				", `email`=".$db->qstr($_POST['user']['email']).",
				`password`='".md5($_POST['user']['password'])."',`modified`=".$db->DBTimeStamp(time())." WHERE `id`='".$_GET['id']."'";
		}else{
			$sql = "UPDATE users SET `username`=".$db->qstr($_POST['user']['username']). 
				",`nickname`=".$db->qstr($_POST['user']['nickname']).",`group_id`=".$db->qstr($_POST['user']['group_id']). 
				", `email`=".$db->qstr($_POST['user']['email']).",`modified`=".$db->DBTimeStamp(time())." WHERE `id`='".$_GET['id']."'";
		}
		$db->Execute($sql);
		header("Location:".SITE_URL."/?module=users_manage");
		exit;
	}else{
		$rs = $db->GetRow("SELECT * FROM users WHERE `id`='".$_GET['id']."'"); 
		$tpl->set('user', $rs);
		$rs0 = $db->GetAssoc("SELECT id,name FROM groups WHERE 1=1");
		$tpl->set('groups', $rs0);
		$template = 'users_manage_edit';
	}
}elseif($_GET['action'] == 'add'){	// add new user
	if(!empty($_POST['user'])){
		$sql="INSERT INTO users (`username`,`password`,`nickname`,`group_id`,`email`,`created`) VALUES 
			(".$db->qstr($_POST['user']['username']).",".$db->qstr(md5('123456')).",
			".$db->qstr($_POST['user']['nickname']).",	
			".$db->qstr($_POST['user']['group_id']).",".$db->qstr($_POST['user']['email']).",
			".$db->DBTimeStamp(time()).")";
		$db->Execute($sql);
		require_once(LIB.'/notice.class.php');
		$notice = &new Notice();
		$notice->redirect(SITE_URL.'/?module=users_manage',$lang['add_user_success']);
	}else{
		$rs0 = $db->GetAssoc("SELECT id,name FROM groups WHERE 1=1");
		$tpl->set('groups', $rs0);
		$template = 'users_manage_add';
	}
}elseif($_GET['action'] == 'del' && preg_match('/^\d+$/', $_GET['id'])){	// delete user
	$sql = "DELETE FROM users WHERE id='".$_GET['id']."'";
	$db->Execute($sql);
	require_once(LIB.'/notice.class.php');
	$notice = &new Notice();
	$notice->redirect(SITE_URL.'/?module=users_manage',$lang['delete_success']);
}else {	// show users list
	$rs = $db->GetAssoc("SELECT users.*,groups.name FROM users LEFT JOIN groups ON users.group_id=groups.id WHERE 1=1");
	$tpl->set('users', $rs);
	$template = 'users_manage';
}


?>
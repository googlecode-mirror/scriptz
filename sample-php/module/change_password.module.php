<?php
/**
 * Change user password
 * $Id: change_password.module.php 8 2008-01-23 09:24:34Z zhuzhu $
 */
if(!empty($_POST['user'])) {
	$password = &$_POST['user']['password'];
	if(strlen($password)<3 || strlen($password)>15) {
		$tpl->set('error', $lang['please_set_right_password']);
	}else{
		$sql = "UPDATE users SET `password`=".$db->qstr(md5($password))." WHERE id=". 
			$db->qstr($_SESSION['user']['id']);
		$db->Execute($sql);
		$tpl->set('success', $lang['change_password_success']);
	}
}
?>
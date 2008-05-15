<?php
  /**
   * $Id: login.php 12 2008-02-18 09:51:55Z zhuzhu $
   */
$tpl = & new Template();
$tpl->set('pageTitle', $lang['login']);
$tpl->set('lang', $lang);
if(!isset($_POST['login']) || empty($_POST['login']) ||
				    !is_array($_POST['login'])) {

} else {
	if(empty($_POST['login']['username']) || 
		empty($_POST['login']['password'])) {
		$tpl->set('error', $lang['need_input']);
	}else{
		include(LIB."/adodb/adodb.inc.php");
		$db = NewADOConnection('mysql');
		$db->Connect($conf['db_host'],$conf['db_user'], 
			$conf['db_passwd'], $conf['db_name']) or die("can't connect database");
		$db->Execute("set names 'utf8'");
		$result = $db->GetRow("SELECT users.*,groups.permissions FROM users LEFT JOIN groups ON users.group_id=groups.id WHERE 
			users.username='".$_POST['login']['username']."' AND 
			users.password='".md5($_POST['login']['password'])."'");
		if ($result === false) die ('fatal error');
		if (!empty($result)) {
			// reg user info
			$_SESSION['user'] = array(
				'id' => $result['id'],
				'username' => $result['username'],
				'nickname' => $result['nickname'],
				'group_id' => $result['group_id'],
				'permissions' => $result['permissions'],
				'last_login_time' => $result['last_login_time'],
				'last_login_ip' => $result['last_login_ip'],
				);
			// record to database
			$ip = (getenv(HTTP_X_FORWARDED_FOR))?getenv(HTTP_X_FORWARDED_FOR):getenv(REMOTE_ADDR);
			$sql = "UPDATE users SET last_login_time=".$db->DBTimeSTamp(time()).", last_login_ip= 
			".$db->qstr($ip)." WHERE id='".$result['id']."'";
			$db->Execute($sql);
			$db->Close();
			
			require_once(LIB.'/notice.class.php');
			$notice = &new Notice();
			$notice->redirect('',$lang['login_success']);
		}else{
			$tpl->set('error', $lang['password_wrong']);
		}
	}

}

echo $tpl->fetch(TMP.'/login.thtml');	
?>
<?php
/**
 * Acl Check Class
 * zhuzhu@perlchina.org
 * $Id: acl.class.php 8 2008-01-23 09:24:34Z zhuzhu $
 *
 */
class Acl {
	/**
	 * check user permissions
	 *
	 */
	public function module($module = null, $lang = null)
	{
		$permissions = unserialize($_SESSION['user']['permissions']);
		if(in_array($module, $permissions) === false && $_GET['module'] != 'logout') {
			require_once(LIB.'/notice.class.php');
			$notice = &new Notice();
			$notice->redirect(SITE_URL,$lang['permisssion_denied'],null,1,$type = 'error');
			exit;
		}
	}

	/**
	 * List all modules permission 
	 *
	 * @return array
	 */
	public function getPermissions() {
		$handle = opendir(SITE_ROOT.'/module/');
		while($file = readdir($handle)) {
			if(preg_match('/.module.php$/', $file) && $file!='logout.module.php'){
				$permission[] = substr($file, 0, -11);
			}
		}
		return $permission;
	}
}

?>
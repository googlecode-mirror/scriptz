<?php
/**
 * $Id: logout.module.php 8 2008-01-23 09:24:34Z zhuzhu $
 */
 
unset($_SESSION['user']);
require_once(LIB.'/notice.class.php');
$notice = &new Notice();
$notice->redirect(SITE_URL,$lang['logout_success']);
?>
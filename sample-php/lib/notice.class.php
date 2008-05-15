<?php
/**
 * $Id: notice.class.php 8 2008-01-23 09:24:34Z zhuzhu $
 */
class Notice {

	public function redirect($url='', $msg='', $pageTitle='', 
		$time='1',$type='success')
	{
		require_once(LIB.'/template.class.php');
		$tpl = & new Template();
		$tpl->set('pageTitle', $pageTitle);
		$tpl->set('msg', $msg);
		$tpl->set('url', $url);
		$tpl->set('type', $type);
		$tpl->set('time', $time);
		echo $tpl->fetch(TMP.'/redirect.thtml');
		
		exit;
	}

} 
 
?>
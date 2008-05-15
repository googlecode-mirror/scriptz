<?php
  /**
   * $Id: core.php 17 2008-02-22 07:38:19Z zhuzhu $
   */

$conf = array(
	 'name' => 'dna system',
	 'mail' => 'zhuzhu@perlchina.org',
	 'version' => 'version 1.0',
	 'db_host' => 'localhost',
	 'db_name' => 'book_dnasys',
	 'db_user' => 'root',
	 'db_passwd' => '123456'
	 );
	 
// set global var	 
define("DB_HOST", $conf['db_host']);
define("DB_NAME", $conf['db_name']);
define("DB_USER", $conf['db_user']);
define("DB_PASSWD", $conf['db_passwd']);

//error_reporting(0);
	 
$ip = (getenv(HTTP_X_FORWARDED_FOR))?getenv(HTTP_X_FORWARDED_FOR):
			getenv(REMOTE_ADDR);
if(!preg_match('/^192.168.1/', $ip)){			
	define("SITE_URL", 'http://dnasys249.isoshu.com');
}else{
	define("SITE_URL", 'http://dnasys249.isoshu.com');
}
define("HTDOCS", SITE_ROOT.'/htdocs');
define("TMP", SITE_ROOT.'/tpl');
define("IMG", SITE_URL.'/img');
define("LIB", SITE_ROOT.'/lib');
define("CSS", SITE_URL.'/css');
define("PAGE_ROW", '20');
define("PROOF_USERS_NUM", '3');

define('SYS_INFO', $conf['name'].'('.$conf['version'].'), power by dna php framework.');
?>
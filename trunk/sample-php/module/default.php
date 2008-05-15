<?php
/**
* $Id: default.php 12 2008-02-18 09:51:55Z zhuzhu $
*/
$tpl = & new Template();
$tpl->set('menuPermissions', $modulesPermission);
$tpl->set('pageTitle', $lang['manage_index']);

include(SITE_ROOT."/lib/adodb/adodb.inc.php");
$db = NewADOConnection('mysql');
$db->Connect($conf['db_host'],$conf['db_user'],$conf['db_passwd'],$conf['db_name']);
if($_GET['debug']=='sql') $db->debug=true;

switch($_SESSION['user']['group_id']){
	case 6:
		$sql = "SELECT COUNT(*) FROM sites WHERE sites.parse_user_id=".$_SESSION['user']['id']." AND  
			(status=5 OR status=6) AND end_time<".$db->DBTimeStamp(time());
		$sites['parser_defer'] = $db->GetOne($sql);
		$sql = "SELECT COUNT(*) FROM sites WHERE sites.parse_user_id=".$_SESSION['user']['id']." AND status = 5";
		$sites['parser_wait'] = $db->GetOne($sql);
		break;
		
	case 9:	
		$sql = "SELECT COUNT(*) FROM sites LEFT JOIN proofs ON sites.id=proofs.site_id WHERE 
			sites.status=8 AND proofs.proof_user_id is NULL";
		$sites['wait_proof'] = $db->GetOne($sql);
		break;
		
	case 4: 
		$sql = "SELECT COUNT(*) FROM sites WHERE status=10";
		$sites['finish_sites'] = $db->GetOne($sql);
		break;
		
	default:

		// count total sites
		$sql = "SELECT COUNT(*) FROM sites";
		$sites['total'] = $db->GetOne($sql);
		
		// count past sites
		$sql = "SELECT COUNT(*) FROM sites WHERE (status=5 OR status=6) AND end_time<".$db->DBTimeStamp(time());
		$sites['past'] = $db->GetOne($sql);
		
		// count wait examine sites
		$sql = "SELECT COUNT(*) FROM sites WHERE status=1";
		$sites['wait_examine'] = $db->GetOne($sql);
		
		// count wait parse sites
		$sql = "SELECT COUNT(*) FROM sites WHERE status=5";
		$sites['wait_parse'] = $db->GetOne($sql);
		
		// count need defer sites
		$sql = "SELECT COUNT(*) FROM sites WHERE defer_time!='0000-00-00 00:00:00' AND status<8";
		$sites['need_defer'] = $db->GetOne($sql);
		
		// count finish sites
		$sql = "SELECT COUNT(*) FROM sites WHERE status>7";
		$sites['finish'] = $db->GetOne($sql);
		
		break;
}


$db->Close();

$tpl->set('sites', $sites);
$tpl->set('lang', $lang);
echo $tpl->fetch(TMP.'/default.thtml', TMP.'/layout.thtml');

?>
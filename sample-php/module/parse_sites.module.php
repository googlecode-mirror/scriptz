<?php
/**
 * $Id: parse_sites.module.php 12 2008-02-18 09:51:55Z zhuzhu $
 */

if($_GET['action']=='view' && preg_match('/^\d+$/',$_GET['id'])){
	$template = 'view_schedule';
	include_once('view_schedule.module.php');	
}elseif($_GET['action']=='edit' && preg_match('/^\d+$/',$_GET['id'])){
	$id = (int) $_GET['id'];
	if(!empty($_POST['site'])){
		$sql = "SELECT status FROM sites WHERE `id`='".$id."'";
		$status = $db->GetOne($sql);
		if($status>7  && $status<2){
			require_once(LIB.'/notice.class.php');
			$notice = &new Notice();
			$notice->redirect(SITE_URL,$lang['permisssion_denied'],null,1,$type = 'error');
			exit;
		}elseif($_POST['site']['status'] == '6'){  // 4 is not pass		
			if($_POST['site']['defer_reason']=='') {
				require_once(LIB.'/notice.class.php');
				$notice = &new Notice();
				$notice->redirect('',$lang['please_input_reason'],null,1,$type = 'error');
				exit;
			}
			$upReason = $db->qstr($_POST['site']['defer_reason']);
			$upStatus = '6';
			$upDeferTime = $db->qstr($_POST['site']['defer_time']);
		}elseif($_POST['site']['status'] == '8') {	// 5 is pass
			if(empty($_POST['site']['code'])) {
				require_once(LIB.'/notice.class.php');
				$notice = &new Notice();
				$notice->redirect('',$lang['need_input'],null,1,$type = 'error');
				exit;
			}
			$upStatus = '8';
			$upReason = $db->qstr('');
			$upDeferTime = $db->qstr('');
			$upCode = $db->qstr($_POST['site']['code']);
			
			
			// up code 
			$sql = "SELECT COUNT(*) FROM codes WHERE site_id =".$id;
			if($db->GetOne($sql)>0){
				$sqlUpCode = "UPDATE codes SET code=".$upCode.",modified=".$db->DBTimeStamp(time())." WHERE site_id=".$id;
				$db->Execute($sqlUpCode);
			}else {
				$sqlUpCode = "INSERT INTO codes (site_id,code,created,parse_user_id) VALUES 
					(".$id.", ".$upCode.", ".$db->DBTimeStamp(time()).",'".$_SESSION['user']['id']."' )" ;
				$db->Execute($sqlUpCode);
			}
			
		}
		$sql = "UPDATE sites SET status='".$upStatus."', 
			defer_reason=".$upReason.",defer_time=".$upDeferTime.",defer_reason=".$upReason." 
			WHERE `id`='".$id."'";
		
		$db->Execute($sql);
		
		$sql0 = "SELECT name FROM sites WHERE `id`='".$id."'";
		$siteName = $db->GetOne($sql0);
		
		if($upStatus == '8'){
			$sql = "SELECT nickname FROM users WHERE id=".$upParseUserId;
			$parse_user = $db->GetOne($sql);
			$logMsg = $_SESSION['user']['nickname'].'安排了网站解析任务'.$db->qstr($siteName).'给'.$parse_user;
		}elseif($upStatus == '6'){
			$logMsg = $_SESSION['user']['nickname'].'提交网站'.$db->qstr($siteName).'需要延期到'.$upDeferTime;
		}
		
		include(LIB.'/log.class.php');
		$log = &new Log;
		$log->write($db,$_SESSION['user']['id'],$_SESSION['user']['username'],
			$logMsg,$sql);
					
		require_once(LIB.'/notice.class.php');
		$notice = &new Notice();
		$notice->redirect($_GET['from'],$lang['edit_site_success']);	
	}else{
		$sql = "SELECT sites.*,type.name as type_name,degree.name as degree_name,
			area.name as area_name,codes.code FROM sites LEFT JOIN site_types as type ON 
			sites.type_id=type.id LEFT JOIN site_types as degree ON
			sites.degree_id=degree.id LEFT JOIN site_types as area ON 
			sites.area_id=area.id LEFT JOIN codes ON codes.site_id=sites.id WHERE sites.id='".$id."'";
		$rs = $db->GetRow($sql);
		$tpl->set('site', $rs);
		$sql = "SELECT * FROM users WHERE group_id = 6";
		$rs = $db->GetAssoc($sql);
		$tpl->set('parse_users', $rs);
		$template = 'parse_sites_edit';
	}	
}else{
	
	switch($_GET['type']){
		case 'past':
			$sqlWhere = "sites.parse_user_id=".$_SESSION['user']['id']." AND  (status=5 OR status=6) AND end_time<".$db->DBTimeStamp(time());
			$pageName = $lang['status_7'];
			break;
		case 'wait_parse':
			$sqlWhere = "sites.parse_user_id=".$_SESSION['user']['id']." AND status = 5";
			$pageName = $lang['wait_parse'];
			break;
		default:
			$sqlWhere = "sites.parse_user_id=".$_SESSION['user']['id']." AND status > 4";
			break;
	}
	
	$sql = "SELECT count(*) FROM sites WHERE ".$sqlWhere;
	$tpl->set('totalRows',$rsNum = $db->GetOne($sql));
	$rsPer = PAGE_ROW;
	
	include_once(LIB.'/pagination.class.php');
	$pagination = &new Pagination ($currentPage=$_GET['page'], $rsNum, $rsPer, 
		SITE_URL.'/?module=parse_sites');
	
	$currentPage = $pagination->current_page - 1;
	
	$startRow = $rsPer*$currentPage;
	
	$sql = "SELECT sites.*,users.nickname as post_user FROM sites LEFT JOIN users ON sites.post_user_id=users.id 
		WHERE ".$sqlWhere." ORDER BY end_time DESC LIMIT ".$startRow.",
			".$rsPer;
			
	$rs = $db->GetAssoc($sql);
	$tpl->set('pageName', $pageName);
	$tpl->set('pagers', $pagination->pagers());
	$tpl->set('totalPages', $pagination->tot_pages);
	$tpl->set('sites', $rs);
}
?>
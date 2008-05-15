<?php
/**
 * $Id: examine_schedule.module.php 11 2008-02-15 02:18:15Z zhuzhu $
 */

if($_GET['action']=='view') {
	$template = 'view_schedule';
	include_once('view_schedule.module.php');
}elseif(!empty($_GET['id'])&&$_GET['action']=='edit') {
	$id = (int) $_GET['id'];
	if(!empty($_POST['site'])){
		$sql = "SELECT status FROM sites WHERE `id`='".$id."'";
		$status = $db->GetOne($sql);
		if($status>3 ){
			require_once(LIB.'/notice.class.php');
			$notice = &new Notice();
			$notice->redirect(SITE_URL,$lang['permisssion_denied'],null,1,$type = 'error');
			exit;
		}elseif($_POST['site']['status'] == '3'){  // 3 is not pass		
			if($_POST['site']['not_pass_reason']=='') {
				require_once(LIB.'/notice.class.php');
				$notice = &new Notice();
				$notice->redirect('',$lang['please_input_reason'],null,1,$type = 'error');
				exit;
			}
			$upReason = $db->qstr($_POST['site']['not_pass_reason']);
			$upStatus = '3';
		}elseif($_POST['site']['status'] == '2') {	// 2 is pass
			$upStatus = '2';
			$upReason = $db->qstr('');
		}
		$sql = "UPDATE sites SET examine_time=".$db->DBTimeStamp(time()).",status='".$upStatus."',not_pass_reason=".$upReason." WHERE `id`='".$id."'";
		$db->Execute($sql);
		
		$sql0 = "SELECT name FROM sites WHERE `id`='".$id."'";
		$siteName = $db->GetOne($sql0);
		
		if($upStatus == '2'){
			$logMsg = $_SESSION['user']['nickname'].'通过审核了网站'.$db->qstr($siteName);
		}elseif($upStatus == '3'){
			$logMsg = $_SESSION['user']['nickname'].'取消审核了网站'.$db->qstr($siteName);
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
			area.name as area_name FROM sites LEFT JOIN site_types as type ON 
			sites.type_id=type.id LEFT JOIN site_types as degree ON
			sites.degree_id=degree.id LEFT JOIN site_types as area ON
			sites.area_id=area.id WHERE sites.id='".$id."'";
		$rs = $db->GetRow($sql);
		$tpl->set('site', $rs);
		$template = 'edit_examine_schedule';
	}
}else{
	
	$sql = "SELECT count(*) FROM sites WHERE 1=1 ORDER BY id DESC,status ASC";
	
	$tpl->set('totalRows',$rsNum = $db->GetOne($sql));
	$rsPer = PAGE_ROW;
	
	include_once(LIB.'/pagination.class.php');
	$pagination = &new Pagination ($currentPage=$_GET['page'], $rsNum, $rsPer, 
		SITE_URL.'/?module=examine_schedule');
	
	$currentPage = $pagination->current_page - 1;
	
	$startRow = $rsPer*$currentPage;
	
	$sql = "SELECT sites.*,site_types.name as type_name,degree.name as degree_name,
		users.nickname,postuser.nickname as post_user,parse_users.nickname as parse_user
		 FROM sites 
		LEFT JOIN site_types ON sites.type_id=site_types.id 
		LEFT JOIN site_types as degree ON sites.degree_id=degree.id 
		LEFT JOIN users ON sites.parse_user_id=users.id 
		LEFT JOIN users as postuser ON sites.post_user_id=postuser.id 
		 LEFT JOIN users as parse_users ON 
		 sites.parse_user_id = parse_users.id 
		 WHERE 1=1 ORDER BY modified DESC LIMIT ".$startRow.",
		".$rsPer;
	$rs=$db->GetAssoc($sql);
	
	$tpl->set('pagers', $pagination->pagers());
	
	$tpl->set('totalPages', $pagination->tot_pages);
	$tpl->set('schedules', $rs);

}
?>
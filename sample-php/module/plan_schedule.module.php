<?php
/**
 * $Id: plan_schedule.module.php 12 2008-02-18 09:51:55Z zhuzhu $
 * 
 */
if($_GET['action']=='view') {	// 查看详情
	$template = 'view_schedule';
	include_once('view_schedule.module.php');
}elseif($_GET['action']=='edit'){	// 安排计划
	$id = (int) $_GET['id'];
	if(!empty($_POST['site'])){
		$sql = "SELECT status FROM sites WHERE `id`='".$id."'";
		$status = $db->GetOne($sql);
		if($status>7  && $status<2){
			require_once(LIB.'/notice.class.php');
			$notice = &new Notice();
			$notice->redirect(SITE_URL,$lang['permisssion_denied'],null,1,$type = 'error');
			exit;
		}elseif($_POST['site']['status'] == '4'){  // 3 is not pass		
			if($_POST['site']['not_feasibility_reason']=='') {
				require_once(LIB.'/notice.class.php');
				$notice = &new Notice();
				$notice->redirect('',$lang['please_input_reason'],null,1,$type = 'error');
				exit;
			}
			$upReason = $db->qstr($_POST['site']['not_feasibility_reason']);
			$upStartTime = '';
			$upEndTime = '';
			$upParseUserId = '';
			$upStatus = '4';
		}elseif($_POST['site']['status'] == '5') {	// 2 is pass
			if(empty($_POST['site']['parse_user_id'])||empty($_POST['site']['end_time'])) {
				require_once(LIB.'/notice.class.php');
				$notice = &new Notice();
				$notice->redirect('',$lang['need_input'],null,1,$type = 'error');
				exit;
			}
			$upStatus = '5';
			$upStartTime = $_POST['site']['start_time'];
			$upReason = $db->qstr('');
			$upEndTime = $_POST['site']['end_time'].' 00:00:00';
			$upParseUserId = $_POST['site']['parse_user_id'];
		}
		$sql = "UPDATE sites SET parse_user_id=".$db->qstr($upParseUserId).", 
			start_time=".$db->qstr($upStartTime).", end_time=".$db->qstr($upEndTime).", 
			status='".$upStatus."',not_feasibility_reason=".$upReason." 
			WHERE `id`='".$id."'";
		
		$db->Execute($sql);
		
		$sql0 = "SELECT name FROM sites WHERE `id`='".$id."'";
		$siteName = $db->GetOne($sql0);
		
		if($upStatus == '5'){
			$sql = "SELECT nickname FROM users WHERE id=".$upParseUserId;
			$parse_user = $db->GetOne($sql);
			$logMsg = $_SESSION['user']['nickname'].'安排了网站解析任务'.$db->qstr($siteName).'给'.$parse_user;
		}elseif($upStatus == '4'){
			$logMsg = $_SESSION['user']['nickname'].'取消安排了网站解析任务'.$db->qstr($siteName);
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
		$sql = "SELECT * FROM users WHERE group_id = 6";
		$rs = $db->GetAssoc($sql);
		$tpl->set('parse_users', $rs);
		$template = 'plan_schedule_edit';
	}
		
}else{		// 计划列表
	$sql = "SELECT COUNT(*) FROM sites WHERE status!=1 AND status!=3";
	$tpl->set('totalRows',$rsNum = $db->GetOne($sql));	
	$rsPer = PAGE_ROW;
	
	include_once(LIB.'/pagination.class.php');
	$pagination = &new Pagination ($currentPage=$_GET['page'], $rsNum, $rsPer, 
		SITE_URL.'/?module=plan_schedule');

	$currentPage = $pagination->current_page - 1;
	$startRow = $rsPer*$currentPage;
	
	$sql = "SELECT sites.*,type.name as type_name,degree.name as degree_name, post_users.nickname as post_user,
		parse_users.nickname as parse_user ,
		area.name as area_name,codes.id as code_id, codes.code FROM sites LEFT JOIN site_types as type ON 
		sites.type_id=type.id LEFT JOIN site_types as degree ON
		sites.degree_id=degree.id LEFT JOIN site_types as area ON
		sites.area_id=area.id LEFT JOIN codes ON sites.id=codes.site_id LEFT JOIN users as post_users ON 
		sites.post_user_id=post_users.id LEFT JOIN users as parse_users ON sites.parse_user_id =parse_users.id 
		WHERE sites.status!=1 AND sites.status!=3";
	
	$rs = $db->GetAssoc($sql);
	$tpl->set('pagers', $pagination->pagers());
	$tpl->set('totalPages', $pagination->tot_pages);
	
	$tpl->set('schedules', $rs);
}

?>
<?php
/**
 * $Id: watch_all_schedule.module.php 12 2008-02-18 09:51:55Z zhuzhu $
 */
if($_GET['action']=='view') {
	$template = 'view_schedule';
	include_once('view_schedule.module.php');
}else{

	switch($_GET['type']) {
		case 'past':
			$sqlWhere = "(status=5 OR status=6) AND end_time<".$db->DBTimeStamp(time());
			$pageName = $lang['status_7'];
			break;
		case 'wait_examine':
			$sqlWhere = "status=1";
			$pageName = $lang['status_1'];
			break;
		case 'wait_parse':
			$sqlWhere = "status=5";
			$pageName = $lang['wait_parse'];
			break;
		case 'in_parse':
			$sqlWhere = "status=5";
			$pageName = $lang['status_5'];
			break;
		case 'need_defer':
			$sqlWhere = "defer_time!='0000-00-00 00:00:00' AND status<8";
			$pageName = $lang['status_6'];
			break;
		case 'finish';
			$sqlWhere = "status>7";
			$pageName = $lang['finish'];
			break;
		default:
			$sqlWhere = "1=1";
			break;
	}
	
	$tpl->set('pageName', $pageName);
	
	$sql = "SELECT count(*) FROM sites WHERE ".$sqlWhere." ORDER BY id DESC,status ASC";
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
			 WHERE ".$sqlWhere." ORDER BY modified DESC LIMIT ".$startRow.",
			".$rsPer;
	$rs=$db->GetAssoc($sql);
	
	$tpl->set('pagers', $pagination->pagers());
	
	$tpl->set('totalPages', $pagination->tot_pages);
	$tpl->set('schedules', $rs);
}
?>
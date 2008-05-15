<?php
/**
 * $Id: watch_schedule.module.php 12 2008-02-18 09:51:55Z zhuzhu $
 */



if($_GET['action']=='edit'){  // hi, this a bug fix from bad reading project document ;(
	$template = 'edit_schedule';
	include_once('edit_schedule.module.php');
	
}elseif($_GET['action']=='view'){
	$template = 'view_schedule';
	include_once('view_schedule.module.php');
}else{

	$sql = "SELECT count(*) FROM sites WHERE `post_user_id`='".$_SESSION['user']['id']."' 
		OR `parse_user_id`='".$_SESSION['user']['id']."' ORDER BY id DESC";
	
	$tpl->set('totalRows',$rsNum = $db->GetOne($sql));
	$rsPer = PAGE_ROW;
	
	include_once(LIB.'/pagination.class.php');
	$pagination = &new Pagination ($currentPage=$_GET['page'], $rsNum, $rsPer, 
		SITE_URL.'/?module=watch_schedule');
	
	$currentPage = $pagination->current_page - 1;
	
	$startRow = $rsPer*$currentPage;
	
	$sql = "SELECT sites.*,site_types.name as type_name,degree.name as degree_name,users.nickname as parse_user
		 FROM sites 
		LEFT JOIN site_types ON sites.type_id=site_types.id 
		LEFT JOIN site_types as degree ON sites.degree_id=degree.id 
		LEFT JOIN users ON sites.parse_user_id=users.id  WHERE 
		`post_user_id`='".$_SESSION['user']['id']."' 
		OR `parse_user_id`='".$_SESSION['user']['id']."' ORDER BY id DESC LIMIT ".$startRow.",
		".$rsPer;
	$rs=$db->GetAssoc($sql);
	
	$tpl->set('pagers', $pagination->pagers());
	
	$tpl->set('totalPages', $pagination->tot_pages);
	$tpl->set('schedules', $rs);

}
?>
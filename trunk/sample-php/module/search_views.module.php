<?php
/**
 * $Id: search_views.module.php 12 2008-02-18 09:51:55Z zhuzhu $
 */
$sql = "SELECT COUNT(*) FROM sites WHERE status='10'";
$tpl->set('totalRows',$rsNum = $db->GetOne($sql));
$rsPer = PAGE_ROW;
	
include_once(LIB.'/pagination.class.php');
$pagination = &new Pagination ($currentPage=$_GET['page'], $rsNum, $rsPer, 
	SITE_URL.'/?module=proof_schedule');

$currentPage = $pagination->current_page - 1;
$startRow = $rsPer*$currentPage;
	
$sql = "SELECT site.name,site.desc,site.url,site.rate,site.quantity,type.name AS type_name,degree.name AS degree_name,
	area.name AS area_name FROM sites AS site LEFT JOIN site_types AS 
	type ON site.type_id=type.id LEFT JOIN site_types AS degree ON site.degree_id=degree.id 
	LEFT JOIN site_types AS area ON site.area_id=area.id WHERE site.status='10' ORDER BY site.id 
	DESC  LIMIT ".$startRow.",".$rsPer;

$rs = $db->GetAssoc($sql);
$tpl->set('pagers', $pagination->pagers());
$tpl->set('totalPages', $pagination->tot_pages);
$tpl->set('schedules', $rs);
?>
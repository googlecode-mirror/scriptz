<?php
/**
 * $Id: system_logs.module.php 11 2008-02-15 02:18:15Z zhuzhu $
 */

$sql = 'SELECT count(*) FROM logs WHERE 1=1 ORDER BY id DESC';

$tpl->set('totalRows',$rsNum = $db->GetOne($sql));
$rsPer = PAGE_ROW;

include_once(LIB.'/pagination.class.php');
$pagination = &new Pagination ($currentPage=$_GET['page'], $rsNum, $rsPer, 
	SITE_URL.'/?module=system_logs');

$currentPage = $pagination->current_page - 1;

$startRow = $rsPer*$currentPage;
$sql = "SELECT * FROM logs WHERE 1=1 ORDER BY id DESC LIMIT ".$startRow.",".$rsPer;
$rs=$db->GetAssoc($sql);

$tpl->set('pagers', $pagination->pagers());

$tpl->set('totalPages', $pagination->tot_pages);
$tpl->set('logs', $rs);
?>
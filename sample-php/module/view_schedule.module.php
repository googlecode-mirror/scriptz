<?php
/**
 * $Id: view_schedule.module.php 10 2008-01-25 03:27:42Z zhuzhu $
 */
$id = (int) $_GET['id'];
$sql = "SELECT sites.*,type.name as type_name,degree.name as degree_name,
	area.name as area_name FROM sites LEFT JOIN site_types as type ON 
	sites.type_id=type.id LEFT JOIN site_types as degree ON
	sites.degree_id=degree.id LEFT JOIN site_types as area ON
	sites.area_id=area.id WHERE sites.id='".$id."'";
$rs = $db->GetRow($sql);

$tpl->set('site', $rs);
?>
<?php
/**
 * $Id: site_type_manage.module.php 8 2008-01-23 09:24:34Z zhuzhu $
 */
$type = (int)$_GET[type];
if (empty($type)) $type=1;
if($_GET['action'] == "add"){
	if(!empty($_POST['attribute'])){
		$sql = "insert into site_types(`name`,`type`) values (".$db->qstr($_POST['attribute']['name']).",'".$_POST['type']."')";
		$db->Execute($sql);
		require_once(LIB.'/notice.class.php');
		$notice = &new Notice();
		$notice->redirect(SITE_URL.'/?module=site_type_manage&type='.$_POST[type],$lang['add_type_success']);
	}else{
		$rs = $db->GetRow("select * from  site_types where type = $type ");
		$tpl->set('attribute', $rs);
		$template = 'site_type_manage_add';
	}


}else if($_GET['action'] == 'edit' && preg_match('/^\d+$/', $_GET['id'])){
	if(!empty($_POST['attribute'])){
		$sql = "update site_types set `name` = ".$db->qstr($_POST['attribute']['name'])." where id =".$_GET['id'];
		$db->Execute($sql);
		header("Location:".SITE_URL."/?module=site_type_manage&type=".$_POST[type]."");
		exit;
	}else{
		$rs = $db->GetRow("select * from  site_types where type = $type and id=$_GET[id]");
		$tpl->set('attribute', $rs);
		$template = 'site_type_manage_edit';
	}

}else if($_GET['action'] == 'del' && preg_match('/^\d+$/', $_GET['id'])){
	$sql = "delete from site_types where id =".$_GET['id'];
	$db->Execute($sql);
		header("Location:".SITE_URL."/?module=site_type_manage&type=$type");
		exit;
}else{
	$rs = $db->GetAssoc("select * from  site_types where type = $type order by id desc");
	$tpl->set('attributes', $rs);
	$template = 'site_type_manage';
}

?>
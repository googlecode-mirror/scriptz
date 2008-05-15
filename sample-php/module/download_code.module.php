<?php
/**
 * $Id: download_code.module.php 12 2008-02-18 09:51:55Z zhuzhu $
 * 
 */

$id = (int)$_GET['id'];
$sql = "SELECT code FROM codes WHERE id ='".$id."'";
echo $db->GetOne($sql);exit;
?>
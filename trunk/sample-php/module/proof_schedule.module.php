<?php
/**
 * $Id: proof_schedule.module.php 12 2008-02-18 09:51:55Z zhuzhu $
 * Proof user module
 */
if($_GET['action']=='view' && preg_match('/^\d+$/',$_GET['id'])){
	$template = 'view_schedule';
	include_once('view_schedule.module.php');	
}elseif($_GET['action'] == 'edit' && preg_match('/^\d+$/', $_GET['id'])){	// edit user
	$id = (int) $_GET['id'];
	if(!empty($_POST['schedule'])){
		if($_POST['schedule']['status'] == '1'){
			$upPass = '1';
			$upReason = '';
		}elseif($_POST['schedule']['status'] == '0'){
			if(empty($_POST['schedule']['reason'])){
				require_once(LIB.'/notice.class.php');
				$notice = &new Notice();
				$notice->redirect($_GET['from'],$lang['need_input'],null,1,$type = 'error');
			}
			$upPass = '0';
			$upReason = $_POST['schedule']['reason'];
		}else{
			require_once(LIB.'/notice.class.php');
			$notice = &new Notice();
			$notice->redirect($_GET['from'],$lang['need_input'],null,1,$type = 'error');
		}
		
		$sql = "SELECT count(*) FROM proofs WHERE site_id='".$id."' AND proof_user_id ='".$_SESSION['user']['id']."'";
		$rsCount = $db->GetOne($sql);
		if($rsCount>0){
			$sql = "UPDATE proofs SET pass=".$db->qstr($upPass).", not_pass_reason=".$db->qstr($upReason).", 
				modified=".$db->DBTimeStamp(time())." WHERE site_id='".$id."' AND proof_user_id='".$_SESSION['user']['id']."' 
				LIMIT 1";
		}else{
			$sql = "INSERT INTO proofs (site_id,proof_user_id,pass,not_pass_reason,created,modified) 
				VALUE(".$db->qstr($id).", ".$db->qstr($_SESSION['user']['id']).", ".$db->qstr($upPass).", 
				".$db->qstr($upReason).", ".$db->DBTimeStamp(time()).",".$db->DBTimeStamp(time()).")";
		}
		$db->Execute($sql);
		
		$sqlProof = "SELECT count(*) FROM proofs WHERE site_id='".$id."' AND pass='1'";
		$proofNum = $db->GetOne($sqlProof);
		if($proofNum >= PROOF_USERS_NUM) {
			$sqlSite = "UPDATE sites SET status='10',modified=".$db->DBTimeStamp(time())." WHERE id='".$id."' LIMIT 1";
		}elseif($proofNum == PROOF_USERS_NUM-1){	
			$sqlSite = "UPDATE sites SET status='11',modified=".$db->DBTimeStamp(time())." WHERE id='".$id."' LIMIT 1";
		}else{
			$sqlSite = "UPDATE sites SET status='9',modified=".$db->DBTimeStamp(time())." WHERE id='".$id."' LIMIT 1";
		}
		$db->Execute($sqlSite);

		$sql0 = "SELECT name FROM sites WHERE `id`='".$id."'";
		$siteName = $db->GetOne($sql0);
		
		if($upPass == '1'){
			$logMsg = $_SESSION['user']['nickname'].'验收并通过网站'.$db->qstr($siteName).'的解析';
		}elseif($upPass == '0'){
			$logMsg = $_SESSION['user']['nickname'].'验收没有通过网站'.$db->qstr($siteName).'的解析';
		}
		
		include(LIB.'/log.class.php');
		$log = &new Log;
		$log->write($db,$_SESSION['user']['id'],$_SESSION['user']['username'],
			$logMsg,$sql);
					
		require_once(LIB.'/notice.class.php');
		$notice = &new Notice();
		$notice->redirect($_GET['from'],$lang['edit_site_success']);	
				
	}else{
		$sql = "SELECT * FROM sites WHERE id='".$id."' LIMIT 1";
		$rs = $db->GetRow($sql);
		$tpl->set('schedule', $rs);
		$sql = "SELECT * FROM proofs WHERE site_id='".$id."' AND proof_user_id='".$_SESSION['user']['id']."'";
		$rs = $db->GetRow($sql);
//		print_r($rs);
		$tpl->set('proof', $rs);
		$template = 'proof_schedule_edit';
	}
} else {
	if ($_GET['type'] == 'wait_proof') {
		$sql = "SELECT COUNT(*) FROM sites LEFT JOIN proofs ON sites.id=proofs.site_id WHERE 
			sites.status=8 AND proofs.proof_user_id is NULL";
	}else{
		$sql = "SELECT count(*) FROM sites WHERE status > '7' ORDER BY id DESC";
	}
	$tpl->set('totalRows',$rsNum = $db->GetOne($sql));	
	$rsPer = PAGE_ROW;
	
	include_once(LIB.'/pagination.class.php');
	$pagination = &new Pagination ($currentPage=$_GET['page'], $rsNum, $rsPer, 
		SITE_URL.'/?module=proof_schedule');

	$currentPage = $pagination->current_page - 1;
	$startRow = $rsPer*$currentPage;
		
	if ($_GET['type'] == 'wait_proof') {
		$sql = "SELECT sites.id,sites.name,sites.url FROM sites LEFT JOIN proofs ON sites.id=proofs.site_id WHERE 
			sites.status=8 AND proofs.proof_user_id is NULL";
	}else{
		$sql = "SELECT id,name,url FROM sites WHERE status > '7' ORDER BY id DESC  LIMIT ".$startRow.",
				".$rsPer;
	}
	
	$rs = $db->GetAssoc($sql);
	$tpl->set('pagers', $pagination->pagers());
	$tpl->set('totalPages', $pagination->tot_pages);
	$tpl->set('schedules', $rs);
}

?>
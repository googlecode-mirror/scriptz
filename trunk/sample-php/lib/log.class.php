<?php
/**
 * Log Classs
 * $Id: log.class.php 8 2008-01-23 09:24:34Z zhuzhu $
 *
 */
class Log {
	
	/**
	 * write system logs 
	 *
	 * @param string $msg
	 * @param string $sql
	 */
	public function write($db, $user_id=null,$user_name=null,$msg=null,$sql_sentence=null) 
	{
		$ip = (getenv(HTTP_X_FORWARDED_FOR))?getenv(HTTP_X_FORWARDED_FOR):
			getenv(REMOTE_ADDR);
		$sql = "INSERT INTO logs (`user_id`,`user_name`,`desc`,`time`,`ip`,`sql`) 
			VALUES(".$db->qstr($user_id).",".$db->qstr($user_name).",
			".$db->qstr($msg).",".$db->DBTimeStamp(time()).",
			".$db->qstr($ip).",".$db->qstr($sql_sentence).")";
		$db->Execute($sql);
		$db->Close();
	}
}

?>
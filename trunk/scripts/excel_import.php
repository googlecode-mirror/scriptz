<?php
/**
 * $Id$
 * zhuzhu@perlchina.org
 * 
 * version 2.0
 * Lisence: MIT
 * Use:
 *  set your temp DataBase to $tmpDatabase
 *  set your temp Table to $tmpTable
 *  set your XSL file path to $fileurl
 *  set your XSL style fields list to $fieldList, and use blank space
 *  	to split it
 *  enjoy it!	
 */
	//error_reporting(E_ERROR);
	$tmpDatabase = 'zhu_test_cnc';
	$tmpTable = 'cartoon_main_book_info';
	$fileurl = 'cnc.xls';
	$fieldList = "BOOK_ID ISBN BOOKNAME_CN BOOKNAME_OTHER BOOKNAME_ORIGINAL BOOK_TAG BOOK_HIT IS_END AUTHOR PUBLISH_COMPANY GRADE TYPE_BACKGROUND TYPE_RENT_BOOK TYPE_VOLUME TYPE_AUTHOR TYPE_AGE TYPE_READER_AGE TYPE_COUNTRY TYPE_INVESTIGATE TYPE_RECOMMEND TYPE_ROMAN TYPE_ZHUYIN TYPE_ZISHU BOOK_COVER_L BOOK_COVER_M BOOK_COVER_S UPDATE_DATE LANG BOOK_INTRODUCE BOOK_VOLUMES BOOK_VOLUME_NO ROLE_TOP BOOK_COVER_BACK BOOK_COVER_f order_id seo_title seo_desc seo_keywords order_rent_book";
	// set data end 
	
	// set MySQL host, user and password
	$conn=mysql_connect("127.0.0.1","root","123456");

	mysql_select_db($tmpDatabase,$conn);
	
	require_once('excel_reader.php');
	$data = new Spreadsheet_Excel_Reader();
	$data -> setOutputEncoding('utf-8');
	$data -> read($fileurl);
	$fieldArray = explode(' ', $fieldList);
	$numrows = $data -> sheets[0]['numRows'];
	for ($i = 1; $i <= $numrows;$i++) {		
		$n = '1';
		foreach($fieldArray as $field)
		{
			${$field} =$data->sheets[0]['cells'][$i][$n];
			$n = $n + 1;
		}
		mysql_query("set names 'utf8'");
		$fieldQueryList = implode(',', $fieldArray);
		$m = 0;
		foreach($fieldArray as $field)
		{
			$inputString[$m] = '\''.addslashes(strip_tags(trim(${$field}))).'\'';
			// $inputString[$m] = '\''.addslashes(${$field}).'\'';
			$m = $m + 1;
		}
		$fieldQueryString = implode(',', $inputString);
		$sql = "INSERT INTO $tmpDatabase.$tmpTable ($fieldQueryList) VALUES ($fieldQueryString)";
		$result=mysql_query($sql) ;
		if(!$result) echo mysql_error()."<br><br>";
	}
?>

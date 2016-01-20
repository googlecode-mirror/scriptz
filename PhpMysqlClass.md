# MySQL数据库操作类 #

## 简单操作类 ##
```
<?php
class sql_db{
	/*开始MYSQL数据库的类*/
	var $db_link; //数据库连接点
	var $db_res; //获得的结果集
	var $res_num; //获得结果总数
	var $db_host='localhost'; //服务器
	var $db_dbname='dbname'; //数据库
	var $db_user='root'; //用户名
	var $db_pass='root'; //密码
	
	//连接数据库
	function sql_db(){
		  $this->db_link=@mysql_connect($this->db_host,$this->db_user,$this->db_pass);	
		mysql_query("set names 'utf8'");
		@mysql_select_db($this->db_dbname,$this->db_link);
		//return $this->db_link;	
	}
	
	//执行SQL语句
	function sql_query($sql,$return = false){
		$this->db_res=@mysql_query($sql,$this->db_link);
		if($return==true){
		return @mysql_affected_rows($this->db_link); //返回影响数目
		}else{ //否则就返回取得的数组
			if(!$this->db_res){
			return false;
			}else{ //如果有数据
			$this->res_num=@mysql_num_rows($this->db_res);
			for($i=0;$i<$this->res_num;$i++){ //循环输出
				  $array[$i]=@mysql_fetch_array($this->db_res);
			}				 
			@mysql_free_result($this->db_res); //释放内存
			return $array; //返回得到的数组
			}
		}
	}
	function get_row($sql,$output='ARRAY_A') {
		$row=$this->sql_query($sql);
		return $row[0];
	}
	function get_results($sql,$output='ARRAY_A') {
		$res=$this->sql_query($sql);
		return $res;
	}
	//关闭数据库
	function sql_close(){
		@mysql_close($this->db_link);
	}
	
}
?>
```
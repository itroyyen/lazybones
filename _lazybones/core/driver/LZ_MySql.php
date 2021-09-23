<?php
/**************************************************************************
 * Lazybones web development framework for PHP 5.1.2 or newer
 *
 * @author      顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @copyright   Copyright (c) 2010, 顏宏育(Hong Yu Yan)
 **************************************************************************/

/**
 * 實作MySQL資料庫操作使用mysql函式庫
 * 
 * @author 顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @package core.db
 */
class LZ_MySql implements LZ_IDbDriver {
	
	protected $_link;
	protected $_encoding = 'utf8';
	protected $_lastSql = '';

	public function __construct() {
		$dbconf = app::conf()->getActiveDbConfig();
		$this->connect($dbconf['DBNAME'],$dbconf['USER'],$dbconf['PASSWORD'],$dbconf['HOST']);
		$this->setEncoding($dbconf['ENCODING']);
	}

	/**
	 * 建立連線
	 * @param string $dbname 資料庫名稱
	 * @param string $user 使用者名稱
	 * @param string $pswd 使用者密碼
	 * @param string $host 主機
	 */
	public function connect($dbname,$user,$pswd,$host){
		$this->_link = mysql_connect($host, $user, $pswd) or die("mysql_connect() failed.<br>");
		mysql_select_db($dbname, $this->_link) or die("mysql_select_db() failed.<br>");
	}

	/**
	 * 開始交易模式
	 * TODO: 等待實作
	 */
	public function beginTransaction(){

	}

	/**
	 * 結束交易模式
	 * TODO: 等待實作
	 */
	public function endTransaction(){}

	/**
	 * 查詢
	 * @param string $sql
	 * @return mixed 結果資源
	 */
	public function query($sql){
		$this->_lastSql = $sql;
		mysql_query("SET NAMES '".$this->_encoding."'",$this->_link);
		$res = mysql_query($sql,$this->_link);
		if (!$res) {
			$this->_displayErrorMsg();
			return false;
		}
		return $res;
	}

	/**
	 * 影響列數
	 * @return int
	 */
	public function affectedRows(){
		return mysql_affected_rows();
	}

	/**
	 * 關閉連線
	 */
	public function close(&$res){
		mysql_close($res);
	}

	/**
	 * 最後新增ID
	 * @return int
	 */
	public function lastInsertId(){
		return mysql_insert_id($this->_link);
	}

	/**
	 * 回傳搜尋一列結果 - 陣列格式，失敗傳回false
	 * @param mixed $res 結果資源
	 * @param int 類型 0 = associative array , 1 = numeric array , 2 = both
	 * @return array
	 */
	public function fetch($res,$type = 0){
		if (gettype($res) == "resource"){
			switch($type){
				case 0:
					return mysql_fetch_assoc($res);
					break;
				case 1:
					return mysql_fetch_row($res);
					break;
				case 2:
					return mysql_fetch_array($res);
					break;
			}
			
		}else{
			return false;
		}
	}

	/**
	 * 回傳搜尋一列結果 - 物件格式，失敗傳回false
	 * @param mixed $res 結果資源
	 * @param string 類別名稱
	 * @return object
	 */
	public function fetchObject($res,$className = null){
		if (gettype($res) == "resource"){
			if(null === $className){
				return mysql_fetch_object($res);
			}else{
				return mysql_fetch_object($res,$className);
			}
		}else{
			return false;
		}
	}

	/**
	 * 取得結果總數
	 * @param mixed $res 結果資源
	 * @return int
	 */
	public function count($res){
		if (gettype($res) == "resource"){
			return mysql_num_rows($res);
		}else{
			return false;
		}
	}

	/**
	 * 跳脫字元格式化
	 * @param string $str 原始字串
	 * @return string
	 */
	public function escape($str){
		return mysql_escape_string($str);
	}

	/**
	 * 設定編碼方式
	 * @param string $encoding
	 */
	public function setEncoding($encoding){
		$this->_encoding = $encoding;
	}

	/**
	 * 取得最後查詢SQL
	 * @return string
	 */
	public function getLastSql(){
		return $this->_lastSql;
	}

	/**
	 * 顯示錯誤訊息
	 */
	private function _displayErrorMsg(){
		if (app::conf()->DEBUG_MOD){
			$Message = mysql_error();
			$Number	= mysql_errno();
			$SQL = $this->_lastSql;
			$errMsg = <<<EOD
			<table style='border-width:1px; border-color:rgb(153,153,153); border-style:dashed;' border='0' cellpadding='8' width='100%' bgcolor='#FFFFCC'>
					<tr>
							<td width='977'>
									<font face='Courier New' size='4' color='#990000'><b>SQL Query Error !!</b></font>
									<hr style='border-top-width:1px; border-top-color:rgb(153,153,153); border-top-style:dashed; ' size='1' width='98%'>

						<b><span style='font-size:12pt;'><font color='#333333'>Error Message
									:</font><font color='#0000CC'> </font></span></b><font size='2' color='#003300'>$Message</font>
										<hr style='border-top-width:1px; border-top-color:rgb(153,153,153); border-top-style:dashed; ' size='1' width='98%'>
						<b><span style='font-size:12pt;'><font color='#333333'>Error Number&nbsp;:</font><font color='#0000CC'>
									</font></span></b><font size='2' color='#003300'>$Number</font>
									<hr style='border-top-width:1px; border-top-color:rgb(153,153,153); border-top-style:dashed; ' size='1' width='98%'>
						<b><span style='font-size:12pt;'><font color='#333333'>Query SQL :&nbsp;:</font><font color='#0000CC'>
									</font></span></b><font size='2' color='#003300'>$SQL</font>
							</td>
					</tr>
			</table>
EOD;
		}else{
			$errMsg = "mysql_query() error.<br>";
		}
		app::throwError($errMsg);
	}
}

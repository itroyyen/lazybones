<?php
/**************************************************************************
 * Lazybones web development framework for PHP 5.1.2 or newer
 *
 * @author      顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @copyright   Copyright (c) 2010, 顏宏育(Hong Yu Yan)
 **************************************************************************/

/**
 * 定義資料庫操作介面
 * 
 * @author 顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @package core.interface
 */
interface LZ_IDbDriver {
	/**
	 * 建立連線
	 * @param string $dbname 資料庫名稱
	 * @param string $user 使用者名稱
	 * @param string $pswd 使用者密碼
	 * @param string $host 主機
	 */
	public function connect($dbname,$user,$pswd,$host);

	/**
	 * 開始交易模式
	 */
	public function beginTransaction();

	/**
	 * 結束交易模式
	 */
	public function endTransaction();

	/**
	 * 查詢
	 * @param string $sql
	 * @return mixed 結果資源
	 */
	public function query($sql);

	/**
	 * 影響列數
	 * @return int
	 */
	public function affectedRows();

	/**
	 * 關閉連線
	 */
	public function close(&$res);

	/**
	 * 最後新增ID
	 * @return int
	 */
	public function lastInsertId();

	/**
	 * 回傳搜尋一列結果 - 陣列格式，失敗傳回false
	 * @param mixed $res 結果資源
	 * @param int 類型 0 = associative array , 1 = numeric array , 2 = both
	 * @return array
	 */
	public function fetch($res,$type = 0);

	/**
	 * 回傳搜尋一列結果 - 物件格式，失敗傳回false
	 * @param mixed $res 結果資源
	 * @param string 類別名稱
	 * @return object
	 */
	public function fetchObject($res,$className = null);

	/**
	 * 取得結果總數
	 * @param mixed $res 結果資源
	 * @return int
	 */
	public function count($res);

	/**
	 * 跳脫字元格式化
	 * @param string $str 原始字串
	 * @return string
	 */
	public function escape($str);

	/**
	 * 設定編碼方式
	 * @param string $encoding
	 */
	public function setEncoding($encoding);

	/**
	 * 取得最後查詢SQL
	 * @return string
	 */
	public function getLastSql();
}
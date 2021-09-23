<?php
/**************************************************************************
 * Lazybones web development framework for PHP 5.1.2 or newer
 *
 * @author      顏宏育(Hong Yu Yan) <royyam0812@gmail.com>
 * @copyright   Copyright (c) 2010, 顏宏育(Hong Yu Yan)
 **************************************************************************/

/**
 * 安全過濾
 *
 * @author 顏宏育(Hong Yu Yan) <royyam0812@gmail.com>
 * @package core
 */
class LZ_Security{

	/**
	 * 取得經過XSS清除的 $_GET
	 * @return array
	 */
	public function cleanQuery($allowHtmlContent = false){
		return $this->_cleanArray($_GET);
	}

	/**
	 * 取得經過XSS清除的 $_POST
	 * @return array
	 */
	public function cleanPost($allowHtmlContent = false){
		return $this->_cleanArray($_POST);
	}

	/**
	 * 取得經過XSS清除的 $_REQUEST
	 * @return array
	 */
	public function cleanRequest($allowHtmlContent = false){
		return $this->_cleanArray($_REQUEST);
	}

	/**
	 * 內部清除
	 * @param array $vars 陣列資料
	 * @return array
	 */
	private  function _cleanArray(&$vars){
		$rtval = array();
		foreach($vars as $k => $var){
			$rtval[$k] = $allowHtmlContent ? $this->cleanHtmlContent($var) : strip_tags($var);
		}
		return $rtval;
	}

	/**
	 * 過濾HTML內容中隱含的安全問題
	 * @param string $str
	 */
	public function cleanHtmlContent($str){
		
	}

	/**
	 * 清除XSS
	 * @param mixed $var
	 */
	public function xssClean($var){
		return self::_xssClean($var);
	}

	/**
	 * 內部清除XSS
	 * @param mixed $var
	 */
	private static function _xssClean($var){
		
	}
}
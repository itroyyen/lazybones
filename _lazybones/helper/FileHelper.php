<?php
/**************************************************************************
 * Lazybones web development framework for PHP 5.1.2 or newer
 *
 * @author      顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @copyright   Copyright (c) 2010, 顏宏育(Hong Yu Yan)
 **************************************************************************/

/**
 * 檔案處理助手
 *
 * @author 顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @package helper
 */
class FileHelper extends LZ_Helper {

	private static $_instance;

	/**
	 * 取得單一實例
	 * @return FileHelper
	 */
	public static function getInstance(){
		if(null === self::$_instance) self::$_instance = new FileHelper();
		return self::$_instance;
	}

	/**
	 * 建立目錄
	 * @param string $path 要建立的目錄路徑
	 * @param bool $autoCreate 如果指定目錄的父目錄不存在，是否自動建立
	 */
	public function createDir($path,$autoCreate = true){
		if ($autoCreate == true){
			$arrPath = '';
			$i = 0;
			$pathTemp = $path;
			while (!is_dir($pathTemp) && $pathTemp !=''){
				$arrPath[$i] = $pathTemp;
				$pathTemp = dirname($pathTemp);
				$i++;
			}

			if (is_array($arrPath)){
				for ($i=count($arrPath)-1; $i>=0; $i--){
					mkdir($arrPath[$i]);
				}
			}
		}else{
			if (!is_dir($path)) mkdir($path);
		}
	}

	/**
	 * 刪除檔案
	 * @param string $strPath 要刪除的檔案路徑
	 * @return bool 是否成功
	 */
	public function delete($path){
		if (file_exists($path)){
			if (is_file($path)){
				unlink($path);
				return true;
			}
		}
		
		return false;
	}

	/**
	 * 取得格式化的檔案大小字串
	 * e.g. 傳入 2048 傳回 2 KB
	 * 
	 * @param int $byte 檔案位元組
	 * @param int $length = 2 小數點後的位數
	 * @return string
	 */
	public function sizeToString ($byte,$length = 2){
		$sizeArr = array('KB' => 1024,'MB' => 1048576,'GB' => 1073741824);
		$byte = intval($byte);
		foreach($sizeArr as $k => $size){
			if ($byte >= $size){
				return round($byte / $size , $length)." $k";
			}
		}
		return $byte." Byte";
	}
}
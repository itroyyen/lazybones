<?php
/**************************************************************************
 * Lazybones web development framework for PHP 5.1.2 or newer
 *
 * @author      顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @copyright   Copyright (c) 2010, 顏宏育(Hong Yu Yan)
 **************************************************************************/

/**
 * 檔案上傳助手
 *
 * @author 顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @package helper
 */
class UploadHelper extends LZ_Helper {

	public $allowContentTypes;
	public $allowExtensions;
	private $_err = 0;

	private static $_instance;

	public function  __construct(){
		parent::__construct();
		$this->loadLanguageFile();
	}
	
	/**
	 * 取得單一實例
	 * @return UploadHelper
	 */
	public static function getInstance(){
		if(null === self::$_instance) self::$_instance = new UploadHelper();
		return self::$_instance;
	}

	/**
	 * 設定允許大小
	 * @param string $size  e.g. 50M
	 */
	public function setAllowSize($size){
		ini_set('upload_max_filesize',$size);
		ini_set('post_max_size',$size);
	}

	/**
	 * 上傳檔案並儲存到指定路徑
	 * @param string $field
	 * @param string $path 存放路徑(不含檔名)
	 * @param string $newName 新名稱
	 * @param string $autoRename 若檔案存在是否自動重新命名
	 * @return string 儲存後的完整檔案路徑，若失敗傳回false
	 */
	public function save($field,$path,$newName = null,$autoRename = true){
		if(!isset($_FILES[$field])){
			$this->_err = 23;
			return false;
		}
		$file = $_FILES[$field];

		if(UPLOAD_ERR_OK === $file['error']){
			if(0 === $file['error'] && $file['size'] > 0){
				$ext = strtolower(substr(strrchr($file['name'], '.'), 1));
				if(isset($this->allowExtensions)){
					if(!in_array($ext, $this->allowExtensions)){
						$this->_err = 21;
						return false;
					}
				}

				if(isset($this->allowContentTypes)){
					app::output()->getContentType($file['name']);
					if(!in_array($ext, $this->allowContentTypes)){
						$this->_err = 22;
						return false;
					}
				}
				
				$name = $file['name'];
				
				if(null !== $newName) $name = $newName.'.'.$ext;
				$filename = $path.$name;
				
				if($autoRename){
					$finfo = pathinfo($filename);
					$i = 1;
					while(file_exists($filename)){
						$filename = $path.$finfo['filename']." ($i).".$ext;
						++$i;
					}
				}
				
				move_uploaded_file($file['tmp_name'], $filename);
				$this->_err = 0;
				return $filename;
			}
		}else {
			$this->_err = $file['error'];
			return false;
		}
	}

	/**
	 * 取得最後錯誤訊息
	 * @return string
	 */
	public function getLastMessage(){
		$err = $this->_err;
		if(null !== $this->_err){
			if(isset($this->lang[$err])){
				return $this->lang[$err];
			}else{
				return $this->lang[20];
			}
		}
	}

	/**
	 * 取得最後錯誤代號
	 * @return int
	 */
	public function getLastError(){
		return $this->_err;
	}

	/**
	 * 是否發生錯誤
	 * @return bool
	 */
	public function hasError(){
		if($this->_err === 0){
			return false;
		}else {
			return true;
		}
	}
}
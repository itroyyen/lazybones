<?php
/**************************************************************************
 * Lazybones web development framework for PHP 5.1.2 or newer
 *
 * @author      顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @copyright   Copyright (c) 2010, 顏宏育(Hong Yu Yan)
 **************************************************************************/

/**
 * Helper基底類別
 *
 * @author 顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @package core
 */
class LZ_Helper {

	public $name;
	protected  $conf;
	protected $lang;

	public function  __construct() {
		$this->name = stripSuffix(get_class($this), 'Helper');
	}

	/**
	 * 載入config檔
	 * @param string $name 設定檔名稱，自動加上 .conf.PHP_EXT
	 * @param string $folder 檔資料夾
	 */
	protected function loadConfig($name = null,$folder = null){
		if(null === $name) $name = $this->name;
		$this->conf = app::loadConfig(app::RES_HELPER.'/'.$name);
	}

	/**
	 * 載入語系檔
	 * @param string $name 語系檔名稱 e.g. 'name' or 'folder/name'
	 */
	protected function loadLanguageFile($name = null){
		if(null === $name) $name = 'helper/'.$this->name;
		$this->lang = app::loadFrameworkLanguageFile($name);
	}
}
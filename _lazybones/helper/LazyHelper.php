<?php
/**************************************************************************
 * Lazybones web development framework for PHP 5.1.2 or newer
 *
 * @author      顏宏育(Hong Yu Yan) <royyam0812@gmail.com>
 * @copyright   Copyright (c) 2010, 顏宏育(Hong Yu Yan)
 **************************************************************************/

/**
 * 開發輔助助手
 *
 * @author 顏宏育(Hong Yu Yan) <royyam0812@gmail.com>
 * @package helper
 */
class LazyHelper extends LZ_Helper {

	private static $_instance;

	/**
	 * 取得單一實例
	 * @return LazyHelper
	 */
	public static function getInstance(){
		if(null === self::$_instance) self::$_instance = new LazyHelper();
		return self::$_instance;
	}
	
	public function formToModel($model){
		
	}
}
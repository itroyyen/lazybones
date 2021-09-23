<?php
/**
 * Description of Helper
 *
 * @author YourName <YourEmail@lazy.com>
 */
class YourNameHelper  extends LZ_Helper {

	private static $_instance;

	protected function  __construct() {
		parent::__construct();
	}

	/**
	 * @return YourNameHelper
	 */
	public static function getInstance(){
		if(null !== self::$_instance) self::$_instance = new YourNameHelper();
		return self::$_instance;
	}
}
<?php
/**************************************************************************
 * Lazybones web development framework for PHP 5.1.2 or newer
 *
 * @author      顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @copyright   Copyright (c) 2010, 顏宏育(Hong Yu Yan)
 **************************************************************************/

/**
 * 文字處理助手
 *
 * @author 顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @package helper
 */
class TextHelper  extends LZ_Helper {

	private static $_instance;

	private static $_randomTemplate = 'abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';

	/**
	 * 取得單一實例
	 * @return TextHelper
	 */
	public static function getInstance(){
		if(null === self::$_instance) self::$_instance = new TextHelper();
		return self::$_instance;
	}
	
	/**
	 * 取得亂數字串
	 * @param int $length 亂數碼長度
	 * @param string $template 亂數碼使用字串
	 * @return string
	 */
	public function random($length,$template = null){
		if(null === $template) $template = self::$_randomTemplate;
		srand((double)microtime()*1000000);
		$rtval = '';
		$len = strlen($template) - 1;
		for ($i=0;$i<$length;$i++){
			$rtval .= substr($template,rand(0,$len),1);
		}
		return($rtval);
	}

	/**
	 * 依照指定的間距切開字串為陣列
	 * 若欄位指定不足，將依照最後一個欄位而定
	 *
	 * @param string $str 原始字串
	 * @param string $format 間距格式如 "3" 或 "3,6,3"
	 * @return array
	 */
	public function chunk($str,$format){
		$cfmt = explode(',',$format);
		$cfmt_top = count($cfmt)-1;
		$strlen = strlen($str);
		$formatIdx = $getcnt = 0;
		$tmp = '';
		$rtval = array();
		$cnt = intval($cfmt[$formatIdx]);

		for ($i=0;$i<$strlen;$i++){
			$char = substr($str,$i,1);
			$tmp .= $char;
			$getcnt++;
			if($getcnt == $cnt){
				$rtval[] = $tmp;
				$getcnt = 0;
				$tmp = '';
				if($formatIdx != $cfmt_top) $formatIdx++;
				$cnt = intval($cfmt[$formatIdx]);
			}
		}

		return $rtval;
	}
}
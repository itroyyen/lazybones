<?php
/**************************************************************************
 * Lazybones web development framework for PHP 5.1.2 or newer
 *
 * @author      顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @copyright   Copyright (c) 2010, 顏宏育(Hong Yu Yan)
 **************************************************************************/

/**
 * 定義視圖介面
 *
 * @author 顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @package core.interface
 */
interface LZ_IView{

	const EVENT_BEFORE_RENDER = 'LZ_View.before_render';
	const EVENT_AFTER_RENDER = 'LZ_View.after_render';
	const EVENT_PREPROCESS = 'LZ_View.preprocess';
	
	/**
	 * 賦予變數並呈現視圖<br>
	 * <b>[注意]</b><br>
	 * 實作時必須觸發 EVENT_BEFORE_RENDER 與 EVENT_AFTER_RENDER 兩個事件
	 * @param mixed $v
	 */
	public function render($v = null);

	/**
	 * 賦予變數並取得視圖結果
	 * @param string $v
	 * @return string
	 */
	public function result($v = null);

	/**
	 * 設定視圖路徑
	 * @param string $name 視圖名稱 e.g. 'name' or 'folder/name'
	 */
	public function setPath($name);

	/**
	 * 取得視圖路徑
	 * @return string
	 */
	public function getPath();

	/**
	 * 指派變數
	 * @param mixed $arrVarOrName 變數名或陣列
	 * @param mixed $value 值
	 */
	public function assign($arrVarOrName,$value = null);

	/**
	 * 設定是否顯示PHP NOTICE等級錯誤報告，預設為不顯示
	 * @param bool $isShow
	 */
	public function showNotice($isShow = true);

	/**
	 * 設定是否總是編譯樣板，預設為只在樣板改變後才編譯
	 * @param bool $isShow
	 */
	public function alwaysCompile($isAlways = true);
}
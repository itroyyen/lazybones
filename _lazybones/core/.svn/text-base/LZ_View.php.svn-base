<?php
/**************************************************************************
 * Lazybones web development framework for PHP 5.1.2 or newer
 *
 * @author      顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @copyright   Copyright (c) 2010, 顏宏育(Hong Yu Yan)
 **************************************************************************/

/**
 * 視圖 - Basic模式
 * 
 * @author 顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @package core
 */
class LZ_View implements LZ_IView {

	/**
	 * 視圖路徑
	 * @var string
	 */
	protected $_path;

	/**
	 * 子目錄
	 * @var string
	 */
	protected $_folder;

	/**
	 * 視圖名稱
	 * @var string
	 */
	protected $_name;

	/**
	 * 是否顯示PHP NOTICE等級錯誤報告
	 * @var bool
	 */
	protected $_isShowNotice = false;

	/**
	 * 是否總是編譯樣板
	 * @var bool
	 */
	protected $_isAlwaysCompile = false;
	
	/**
	 * 記錄視圖變數
	 * @var array
	 */
	protected $_v;

	public function  __construct() {
		$this->_isAlwaysCompile = app::conf()->VIEW_ALWAYS_COMPILE;
	}

	/**
	 * 賦予變數並呈現視圖
	 * @param mixed $v
	 */
	public function render($v = null){
		if(null !== $v && null !== $this->_v){
			if(is_array($v)) $v = array_merge($this->_v, $v);
		}elseif(null === $v && null !== $this->_v){
			$v = $this->_v;
		}

		//觸發視圖渲染前事件
		app::event()->trigger(self::EVENT_BEFORE_RENDER, array($this,$v,$this->_path));

		if(!$this->_isShowNotice ) error_reporting(ERROR_REPORTING_SETTING & ~E_NOTICE);

		if(file_exists($this->_path)){
			include $this->getCompiled();
		}else{
			app::throwError(app::ft('','LZ_View : 視圖檔案([:0])不存在',array($this->_path)));
		}

		if(!$this->_isShowNotice ) error_reporting( ERROR_REPORTING_SETTING );
		
		//觸發視圖渲染後事件
		app::event()->trigger(self::EVENT_AFTER_RENDER, array($this,$v,$this->_path));
	}

	/**
	 * 賦予變數並取得視圖結果
	 * @param string $v
	 * @return string
	 */
	public function result($v = null){
		app::output()->pauseBuffer();
		ob_start();
		$this->render($v);
		$content = ob_get_contents();
		ob_end_clean();
		app::output()->startBuffer();
		return $content;
	}

	/**
	 * 設定視圖路徑
	 * @param string $name 視圖名稱 e.g. 'name' or 'folder/name'
	 */
	public function setPath($name){
		$folder = dirname(($name = trim($name, '/')));
		$name = basename($name);
		if('.' !== $folder) $this->_folder = $folder;
		$this->_name = $name;
		$this->_path = app::conf()->VIEW_PATH  . ($folder === '.' ? '' : $folder.'/') . $name . VIEW_EXT;
	}

	/**
	 * 取得視圖路徑
	 * @return string
	 */
	public function getPath(){
		return $this->_path;
	}

	/**
	 * 指派變數
	 * @param mixed $arrVarOrName 變數名或陣列
	 * @param mixed $value 值
	 */
	public function assign($arrVarOrName,$value = null){
		if(is_array($arrVarOrName)){
			if(is_array($this->_v)){
				$this->_v = array_merge($this->_v, $arrVarOrName);
			}else{
				$this->_v = $arrVarOrName;
			}
		}else{
			$this->_v[$arrVarOrName] = $value;
		}
	}

	/**
	 * 設定是否顯示PHP NOTICE等級錯誤報告，預設為不顯示
	 * @param bool $isShow
	 */
	public function showNotice($isShow = true){
		$this->_isShowNotice = $isShow;
	}

	/**
	 * 取得編譯後的樣板檔名
	 * @return string
	 */
	protected function getCompiled(){
		$viewcFile = app::conf()->VIEWC_PATH.($this->_folder === '' ? '' : $this->_folder . '/') .$this->_name. PHP_EXT;

		if(file_exists($viewcFile)){
			$view_mtime = filemtime($this->_path);
			$view_c_mtime = filemtime($viewcFile);
			if($view_mtime !== $view_c_mtime || $this->_isAlwaysCompile){
				$content = file_get_contents($this->_path);
				app::event()->trigger(LZ_IView::EVENT_PREPROCESS,array(&$content));
				file_put_contents($viewcFile, $content);
				touch($viewcFile,$view_mtime);
			}
		}else {
			if('' !== $this->_folder){
				$path = app::conf()->VIEWC_PATH;
				$folders = explode('/', $this->_folder);
				foreach ($folders as $folder){
					$path .= $folder.'/';
					if(!is_dir($path)) mkdir($path);
				}
			}
			$content = file_get_contents($this->_path);
			app::event()->trigger(LZ_IView::EVENT_PREPROCESS,array(&$content));
			file_put_contents($viewcFile, $content);
			$view_mtime = filemtime($this->_path);
			touch($viewcFile,$view_mtime);
		}

		return $viewcFile;
	}

	/**
	 * 設定是否總是編譯樣板，預設為只在樣板改變後才編譯
	 * @param bool $isShow
	 */
	public function alwaysCompile($isAlways = true){
		$this->_isAlwaysCompile = $isAlways;
	}
}
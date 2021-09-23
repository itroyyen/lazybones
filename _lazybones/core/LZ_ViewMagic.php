<?php
/**************************************************************************
 * Lazybones web development framework for PHP 5.1.2 or newer
 *
 * @author      顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @copyright   Copyright (c) 2010, 顏宏育(Hong Yu Yan)
 **************************************************************************/

/**
 * 視圖 - Magic模式
 * 具備樣板功能/樣板經過預先編譯
 *
 * @author 顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @package core
 */
class LZ_ViewMagic extends LZ_View {

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
			app::throwError(app::ft('','LZ_ViewMagic : 視圖檔案([:0])不存在',array($this->_path)));
		}

		if(!$this->_isShowNotice ) error_reporting( ERROR_REPORTING_SETTING );

		//觸發視圖渲染後事件
		app::event()->trigger(self::EVENT_AFTER_RENDER, array($this,$v,$this->_path));
	}

	/**
	 * @return LZ_ViewMagicCompiler
	 */
	protected function compiler(){
		if(null === $this->_compiler){
			app::loadCore('LZ_ViewMagicCompiler');
			$this->_compiler = LZ_ViewMagicCompiler::getInstance();
		}
		return $this->_compiler;
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
				$this->compiler()->compile($this->_path,$viewcFile);
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
			$this->compiler()->compile($this->_path,$viewcFile);
			$view_mtime = filemtime($this->_path);
			touch($viewcFile,$view_mtime);
		}

		return $viewcFile;
	}
}
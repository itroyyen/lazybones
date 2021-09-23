<?php
/**************************************************************************
 * Lazybones web development framework for PHP 5.1.2 or newer
 *
 * @author      顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @copyright   Copyright (c) 2010, 顏宏育(Hong Yu Yan)
 **************************************************************************/

/**
 * 佈局
 *
 * @author 顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @package core
 */
class LZ_Layout {

	private $_views;
	private $_layouts;

	/**
	 * 呈現佈局組件
	 * @param string 佈局名稱
	 * @param string $component 組件名稱
	 * @param array $args 附加參數
	 */
	public function render($name,$component,$args = null){

		if(!isset($this->_views[$name])){
			$this->_views[$name] = app::loadView('_layout/' . $name . '/' . $component);
		}

		if(!isset($this->_layouts[$name])){
			$this->_layouts[$name] = $this->_loadLayout($name);
		}

		if(null !== $args){
			$result = call_user_func_array(array($this->_layouts[$name],$component), $args);
		}else{
			$result = call_user_func(array($this->_layouts[$name],$component));
		}

		$this->_views[$name]->render($result);
		
	}

	/**
	 * 取得佈局組件渲染結果
	 * @param string 佈局名稱
	 * @param string $component 組件名稱
	 * @param array $args 附加參數
	 */
	public function result($name,$component,$args = null){
		app::output()->pauseBuffer();
		ob_start();
		$this->render($name,$component,$args);
		$content = ob_get_contents();
		ob_end_clean();
		app::output()->startBuffer();
		return $content;
	}

	/**
	 * 內部載入layout
	 * @param string $name 佈局名稱
	 * @return Object
	 */
	private function _loadLayout($name){
		list($name,$path) = getClassPath($name, app::conf()->LAYOUT_PATH,'Layout');
		app::load($name , $path, false,'layout');
		return new $name;
	}
}
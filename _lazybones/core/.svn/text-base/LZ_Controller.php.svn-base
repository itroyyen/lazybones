<?php
/**************************************************************************
 * Lazybones web development framework for PHP 5.1.2 or newer
 *
 * @author      顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @copyright   Copyright (c) 2010, 顏宏育(Hong Yu Yan)
 **************************************************************************/

/**
 * 控制器
 *
 * @author 顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @package core
 */
class LZ_Controller
{
	/**
	 * @var LZ_IView
	 */
	private $_view;

	/**
	 * 由網址參數傳入的資料
	 * @var string
	 */
	protected  $v;

	/**
	 * 匹配的Route規則
	 * @var string
	 */
	public  $rule = '';

	/**
	 * 控制器名稱
	 * @var string
	 */
	public  $name;

	/**
	 * 預設執行的Action
	 * @var string
	 */
	protected  $defaultAction = 'Index';

	/**
	 * 目前的Action
	 * @var string
	 */
	protected  $currentAction ;

	/**
	 * 覆寫此成員可設定拒絕自動路由執行的Action
	 * e.g. array('index','Hello'); 或 array('*');全部拒絕
	 * @var array
	 */
	protected  $denyAutoRoute;

	/**
	 * 語系資料
	 */
	protected $lang;

	const EVENT_BEFORE_ACTION = 'LZ_Controller.before_action';
	const EVENT_AFTER_ACTION = 'LZ_Controller.after_action';
	
	function __construct(){
		$this->name = stripSuffix(get_class($this), 'Controller');
	}

	/**
	 * 取得目前執行中的Action
	 * @return string
	 */
	public function currentAction(){
		return $this->currentAction;
	}

	/**
	 * 取得預設Action
	 * @return string
	 */
	public function defaultAction(){
		return $this->defaultAction;
	}

	/**
	 * 執行Controller本身的Action
	 * @param string $action Action名稱
	 * @param mixed $params 額外參數
	 */
	public function  executeAction($action,$params = null){
		$this->currentAction = $action;
		$actionName = $action;
		$action = 'Action_'.$action;
		
		if(!is_callable(array($this, $action))) return 404;

		if('!AUTO_ROUTE' === $this->rule){
			if(isset($this->denyAutoRoute[0])){
				if(in_array('*',$this->denyAutoRoute)) return 404;
				if(in_array($action,$this->denyAutoRoute)) return 404;
			}
		}
		
		$result = app::event()->trigger(self::EVENT_BEFORE_ACTION, array($this,$actionName));
		
		if(null !== $result) return $result;

		if(null === $params){
			$result = $this->$action();
			if(null !== $result) return $result;
		}else{
			$result = call_user_func_array(array($this,$action), $params);
			if(null !== $result) return $result;
		}

		$result = app::event()->trigger(self::EVENT_AFTER_ACTION, array($this,$actionName));

		if(null !== $result) return $result;

	}

	/**
	 * 將資料加入到 $var 中
	 * @param array $v
	 */
	public function init($v,$rule){
		if(null === $this->v) $this->v = array();
		if(null === $v) $v = array();
		$this->v = array_merge($v,$this->v);
		$this->rule = $rule;
	}

	/**
	 * 控制器視圖
	 * 第一次呼叫時可指定路徑
	 * @param $name   視圖名稱 不指定則為目前的Action
	 * @param $folder 視圖子目錄 不指定則為目前的Controler
	 * @return LZ_IView
	 */
	protected function view($name = null,$folder = null){
		if(null === $this->_view){
			if(empty($name)) $name = $this->currentAction;
			if(empty($folder)) $folder = $this->name;
			$this->_view = app::loadView("{$folder}/{$name}");
		}
		return $this->_view;
	}

	/**
	 * 取得目前資訊
	 * @return array
	 */
	protected  function getInfo(){
		$info = array();
		$info['class'] = get_class($this);
		$info['v'] = $this->v;
		$info['defaultAction'] = $this->defaultAction;
		$info['currentAction'] = $this->currentAction;
		$info['rule'] = $this->rule;
		return $rtval;
	}

	/**
	 * 顯示目前資訊
	 */
	protected function displayInfo(){
		$class = get_class($this);
		echo '<h3>Controller Info :</h3>';
		echo "class : <b>[{$class}]</b> action : <b>[{$this->currentAction}]</b><br />";
		echo "match rule : <b>[{$this->rule}]</b><br />";
		echo "default action : <b>[{$this->defaultAction}]</b>";
		echo '<h3>v :</h3>';
		echo '<pre>';
		print_r($this->v);
		echo '</pre>';
	}

	/**
	 * 載入語系檔
	 * @param string $name 語系檔名稱 e.g. 'name' or 'folder/name'
	 * @param string $folder 資料夾
	 */
	protected function loadLanguage($name = null){
		if(null === $name) $name = 'controller/'.$this->name;
		$this->lang = app::loadLanguageFile($name);
	}

	/**
	 * 設定ContentType
	 * @param string $type 類型 e.g. xml 或 jpg 或 png 等等，參閱 /_lazybones/data/content_type.php
	 * @param string $charset 編碼
	 */
	protected function setContentType($type = 'html', $charset='utf-8'){
		$contentType = app::output()->getContentType('name.'.$type);
		if(null === $charset || '' === $charset){
			app::conf()->CONTENT_TYPE = $contentType;
		}else{
			app::conf()->CONTENT_TYPE = "{$contentType}; charset={$charset}";
		}
    }
}
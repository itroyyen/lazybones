<?php
/**************************************************************************
 * Lazybones web development framework for PHP 5.1.2 or newer
 *
 * @author      顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @copyright   Copyright (c) 2010, 顏宏育(Hong Yu Yan)
 **************************************************************************/

/**
 * 應用程式物件，為應用程式及框架之主要核心
 *
 * @author 顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @package core
 */
class app{

	/**
	 * 應用程式組態
	 * @var AppConfig
	 */
	protected static $_conf;

	/**
	 * 是否已經執行過
	 * @var bool
	 */
	private static $_isRan = false;

	/**
	 * 核心鍵值定義
	 * @var array
	 */
	private static $_coreKeys;

	/**
	 * 核心定義
	 * @var array
	 */
	private static $_core;

	/**
	 * 核心實體
	 * @var array
	 */
	private static $_instances;

	/*
	 * 已裝載的核心、函式庫、助手
	 * @var array
	 */
	private static $_loaded = array(
		'core' => array() ,
		'project' => array(),
		'helper' => array() ,
		'library' => array(),
		'model' => array(),
		'controller' =>array(),
		'appclass' =>array(),
		'applibrary' =>array(),
		'layout' =>array(),
		'unknow' => array()
	);

	/**
	 * 是否已裝載核心
	 * @var array
	 */
	private static $_coreLoaded = array(
		'model' => false,
		'view' => false,
		'helper' => false,
		'layout' => false,
		'db' => null
	);

	/**
	 * Content-Type 若未指定則輸入 cont()->DEFAULT_CONTENT_TYPE
	 * @var string
	 */
	public static $contentType;

	private static $langs;

	const EVENT_APP_RUN = 'app.run';
	const EVENT_APP_END = 'app.end';

	const RES_HELPER = '_helper';
	const RES_CONTROLLER = '_controler';
	const RES_VIEW = '_view';
	const RES_CUSTOM_CORE = '_custom_core';

	private function  __construct(){}

	/**
	 * 執行應用程式
	 *
	 * @param array $routeConfig 路由設定
	 */
	public static function run(&$routeConfig) {

		if(self::$_isRan === true) return;
		self::$_isRan = true;

		self::$_coreKeys = include(self::conf()->FRAMEWORK_PATH . 'data/core_keys' . PHP_EXT);

		self::loadLibrary('Lazybones');
		self::loadCoreByKey('Controller');

		$router = self::router();

		self::event()->addListener(self::EVENT_APP_RUN, array('AppProject','__AppRun__'), 0);
		self::event()->addListener(self::EVENT_APP_END, array('AppProject','__AppEnd__'), 0);

		self::output()->startBuffer();
		self::output()->sendHeader('X-Powered-By: '.FRAMEWORK_NAME);

		self::event()->trigger(self::EVENT_APP_RUN);

		$router->execute($routeConfig,self::conf()->AUTO_ROUTE);

		if(null !== self::conf()->CONTENT_TYPE){
			self::output()->addHeader('Content-Type: '.self::conf()->CONTENT_TYPE);
		}

		self::output()->dump();

		self::event()->trigger(self::EVENT_APP_END);
	}

	/**
	 * 內部取得核心實體
	 * @param string $key CoreKey
	 * @param string $name 核心名稱
	 * @param string $mode 模式
	 * @return object
	 */
	private static function _getCoreInstance($key,$name,$mode = null){
		if(!isset(self::$_instances[$name])){
			self::loadCoreByKey($key,false,$mode);
			self::$_instances[$name] = new $name();
		}
		return self::$_instances[$name];
	}

	/**
	 * 取得組態物件
	 * @return AppConfig
	 */
	public static function conf($class = null){
		if(null === self::$_conf){
			self::$_conf = new AppConfig();
		}
		return self::$_conf;
	}

	/**
	 * 取得組態物件
	 * @return LZ_Router
	 */
	public static function router(){ return self::_getCoreInstance('Router','LZ_Router'); }

	/**
	 * 取得輸出物件
	 * @return LZ_Output
	 */
	public static function output() { return self::_getCoreInstance('Output','LZ_Output'); }

	/**
	 * 取得驗證器物件
	 * @return LZ_Validator
	 */
	public static function validator() { return self::_getCoreInstance('Validator','LZ_Validator'); }

	/**
	 * 取得ACL物件
	 * @return LZ_Acl
	 */
	public static function acl() { return self::_getCoreInstance('ACL','LZ_Acl'); }

	/**
	 * 取得驗證器物件
	 * @return LZ_Event
	 */
	public static function event() { return self::_getCoreInstance('Event','LZ_Event'); }

	/**
	 * 取得資料庫操作物件
	 * @return LZ_IDbDriver
	 */
	public static function db(){
		if(null === self::$_coreLoaded['db']){
			$driver = self::conf()->DB[self::conf()->DB_MOD]['DRIVER'];
			self::$_instances[$driver] = self::loadCoreByKey('Database',true,$driver);
			self::$_coreLoaded['db'] = $driver;
		}else{
			$driver = self::$_coreLoaded['db'];
		}
		return self::$_instances[$driver];
	}

	/**
	 * 取得核心佈局物件
	 * @return LZ_Layout
	 */
	public static function layout(){
		return self::_getCoreInstance('Layout','LZ_Layout');
	}


	/**
	 * 載入與類別，檔名與類別名稱相同
	 * @param string $className 類別名稱
	 * @param string $path 路徑
	 * @param bool $createObj 是否自動建立物件
	 * @return object
	 */
	public static function load($className, $path , $createObj = false , $type = 'unknow'){
		if(in_array($className, self::$_loaded[$type])){
			if($createObj) return new $className;
			return;
		}else{
			self::$_loaded[$type][] = $className;
		}
		$loadPath = $path . $className . PHP_EXT;
		if(!file_exists($loadPath)) return false;
		require $loadPath;
		if($createObj) return new $className;
	}

	/**
	 * 依據鍵值載入核心類別，將一併載入依賴類別
	 * @param string $coreName 核心類別名稱，定義於data/core_define.php
	 * @param bool $createObj 是否自動建立物件
	 * @param string $mode 模式
	 * @return object
	 */
	public static function loadCore($coreName , $createObj = false){
		$path = self::conf()->FRAMEWORK_PATH . 'core/';
		$fileName = $coreName . PHP_EXT;;
		if(file_exists($path.$fileName)){
			return self::load($coreName, $path , $createObj ,'core');
		}else{
			if(null === self::$_core ) self::$_core = include(self::conf()->FRAMEWORK_PATH.'data/core_define'. PHP_EXT);
			if(!isset(self::$_core[$coreName])) self::throwError(self::ft('核心 "[:core]" 尚未定義',null,$coreName));
			$path = self::conf()->FRAMEWORK_PATH . 'core/' . self::$_core[$coreName] . '/';
			return self::load($coreName, $path , $createObj ,'core');
		}
	}

	/**
	 * 依據鍵值載入核心類別，將一併載入依賴類別
	 * @param string $coreKey 核心類別索引碼，定義於data/core_keys.php
	 * @param bool $createObj 是否自動建立物件
	 * @param string $mode 模式
	 * @return object
	 */
	public static function loadCoreByKey($coreKey , $createObj = false , $mode = null){
		$coreData = self::$_coreKeys[$coreKey];
		return self::_loadCoreByKey($coreData,$createObj ,$mode);
	}

	/**
	 * 內部依據鍵值載入核心類別
	 * @param array $coreData 定義資料
	 * @param bool $createObj 是否自動建立物件
	 * @param string $mode 模式
	 * @return object
	 */
	private static function _loadCoreByKey(&$coreData , &$createObj , &$mode){
		//載入介面
		if(isset($coreData['interfaces'])){
			foreach($coreData['interfaces'] as $interface){
				$path = isset($require[1]) ? $interface[1].'/' : '';
				self::load($interface[0], self::conf()->FRAMEWORK_PATH . 'core/' . $path ,false ,'core' );
			}
		}

		//載入依賴核心
		if(isset($coreData['dependences'])){
			foreach($coreData['dependences'] as $dependence){
				self::loadCoreByKey($dependence);
			}
		}

		//載入相關需求
		if(isset($coreData['requires'])){
			foreach($coreData['requires'] as $require){
				$path = isset($require[1]) ? $require[1].'/' : '';
				self::load($require[0], self::conf()->FRAMEWORK_PATH . 'core/' . $path ,false ,'core' );
			}
		}

		//依照模式載入 e.g. DB driver
		if(isset($coreData['modes'][$mode])){
			$modeData = $coreData['modes'][$mode];
			return self::_loadCoreByKey($modeData,$mode,$createObj);
		}

		//建立實體
		if(isset($coreData['class'])){
			if($createObj) return new $coreData['class'];
		}
	}

	/**
	 * 載入模型
	 * @param string $name 模型名稱
	 * @return object
	 */
	public static function loadModel($name, $createObj = false){
		if(!self::$_coreLoaded['model']){
			self::loadCoreByKey('Model');
			self::$_coreLoaded['model'] = true;
		}
		return self::load($name.'Model', self::conf()->MODEL_PATH , $createObj ,'model');
	}

	/**
	 * 載入控制器
	 * e.g. loadController('Main') 載入 MainController
	 * e.g. loadController('subpath/Main') 載入 subpath/MainController
	 * @param string $name 控制器名稱或路徑
	 * @return object
	 */
	public static function loadController($name, $createObj = false){
		list($name,$path) = getClassPath($name, self::conf()->CONTROLLER_PATH,'Controller');
		return self::load($name , $path, $createObj,'controller');
	}

	/**
	 * 載入視圖
	 * @param string $name 視圖名稱或路徑
	 * @return LZ_IView
	 */
	public static function loadView($name){
		$cls = self::$_coreKeys['View']['modes'][self::conf()->VIEW_MODE]['class'];
		if(!self::$_coreLoaded['view']){
			self::loadCoreByKey('View',false,self::conf()->VIEW_MODE);
			self::$_coreLoaded['view'] = true;
		}
		$view = new $cls();
		$view->setPath($name);
		return $view;
	}

	/**
	 * 載入助手
	 * @param string $name 助手名稱
	 * @return object
	 */
	public static function loadHelper($name, $createObj = false){
		if(!self::$_coreLoaded['helper']){
			self::loadCore('LZ_Helper');
			self::$_coreLoaded['helper'] = true;
		}
		$path = self::conf()->FRAMEWORK_PATH . 'helper/';
		return self::load($name.'Helper', $path, $createObj , 'helper');
	}

	/**
	 * 載入函式庫
	 * @param string $libName
	 */
	public static function loadLibrary($libName){
		if(in_array($libName, self::$_loaded['library'])){
			return;
		}else{
			self::$_loaded['library'][] = $libName;
		}
		require self::conf()->FRAMEWORK_PATH . '/library/' . $libName . PHP_EXT;
	}

	/**
	 * 載入應用程式自定類別
	 * @param string $clsName
	 * @param bool $creatObject
	 * @return object
	 */
	public static function loadAppClass($clsName,$creatObject = false){
		return self::load($clsName, self::conf()->APP_CLS_PATH ,$creatObject ,'appclass');
	}

	/**
	 * 載入應用程式自定函式庫
	 * @param string $libName
	 */
	public static function loadAppLibrary($libName){
		if(in_array($libName, self::$_loaded['applibrary'])){
			return;
		}else{
			self::$_loaded['applibrary'][] = $libName;
		}
		require self::conf()->APP_LIB_PATH. $libName . PHP_EXT;
	}

	/**
	 * 載入語系檔
	 * @param string $name 語系檔名稱 e.g. 'name' or 'folder/name'
	 * @return array
	 */
	public static function loadLanguageFile($name){
		return self::_loadLanguageFile($name, false);
	}

	/**
	 * 載入框架語系檔
	 * @param string $name 語系檔名稱 e.g. 'name' or 'folder/name'
	 * @return array
	 */
	public static function loadFrameworkLanguageFile($name){
		return self::_loadLanguageFile($name, true);
	}

	/**
	 * 內部載入語系檔
	 * @param string $name e.g. 'name' or 'folder/name'
	 * @param bool $framework 是否為框架語系檔
	 * @return array
	 */
	private static function _loadLanguageFile($name,$framework){

		if($framework){
			$path = self::conf()->FRAMEWORK_PATH . 'language/'. self::conf()->LANGUAGE . '/' .$name . PHP_EXT;
		}else{
			$path = self::conf()->LANGUAGE_PATH . self::conf()->LANGUAGE . '/' . $name . PHP_EXT;
		}

		if(!file_exists($path)){
			self::throwError(self::ft('app : 語系檔 "[:0]" 不存在',null,$path));
		}else{
			return include($path);
		}
	}

	/**
	 *
	 * @param string $name 自動附加 .conf.php
	 * @param string $resourceType 資源類別
	 * @param string $folder 子資料夾
	 */
	public static function loadConfig($name){
		$path = self::conf()->CONFIG_PATH . $name . '.conf' . PHP_EXT;
		if(!file_exists($path)){
			self::throwError(self::ft('app : 組態檔 "[:0]" 不存在',null,$path));
		}else{
			return include($path);
		}
	}

	/**
	 * 輸出錯誤訊息並終止執行
	 * @param string $errmsg 錯誤訊息
	 * @param bool 是否自動結束
	 */
	public static function throwError($errmsg,$exit = true){
		throw new ErrorException($errmsg);
		if($exit) exit;
	}

	/**
	 * 重新導向URL
	 * @param string|array $target URL/控制器
	 * @param bool $exit = true 是否離開應用程式
	 */
	public static function  redirect($target , $exit = true , $code = 302){
		self::router()->redirect($target,$exit,$code);
	}

	/**
	 * 重新導向基於getUrl()的URL，即結合 redirect() 與 getUrl()
	 * @param $path 路徑
	 * @param string $uriParam URL附加參數
	 * @param bool $exit = true 是否離開應用程式
	 */
	public static function  redirectUrl($path, $urlParam = null, $exit = true , $code = 302){
		self::router()->redirect(self::getUrl($path, $urlParam),$exit,$code);
	}

	/**
	 * 顯示已設定的HTTP 404頁面
	 * @param $statusCode 狀態碼
	 * @param bool $exit = true 是否離開應用程式
	 */
	public static function  statusPage($statusCode = 404,$exit = true){
		self::router()->statusPage($statusCode,$exit);
	}

	/**
	 * 產生URL - 簡短
	 * @param string|array $path 路徑
	 * @param string $uriParam URL附加參數
	 * @return string
	 */
	public static function getUrl($path,$urlParam = null){
		return self::_getUrl(self::conf()->BASE_SHORT_URL,$path, $urlParam, false);
	}

	/**
	 * 產生URL - 完整
	 * @param string|array $path 路徑
	 * @param string $uriParam URL附加參數
	 * @return string
	 */
	public static function getUrl2($path,$urlParam = null){
		return self::_getUrl(self::conf()->BASE_URL,$path, $urlParam, true);
	}

	/**
	 * 內部產生URL
	 * @param string|array $path 路徑
	 * @param string $uriParam URL附加參數
	 * @param bool $useBaseUrl 是否使用 BASE_URL，若為false則使用 /SUBFOLDER/
	 * @return string
	 */
	private static function _getUrl($prefix,$path,$urlParam = null){
		if(is_array($path)) $path = join('/',$path);
		$path = ltrim($path,'/');
		$url = '';

		if('' === $path){
			$path = ($prefix === self::conf()->BASE_URL) ? self::conf()->BASE_URL : self::conf()->BASE_SHORT_URL;
		}else{
			$url = self::conf()->ENTRY_FILE !== '' ? $prefix.self::$_conf->ENTRY_FILE . '/' : $prefix.'';
		}
		
		$parts = explode('/', $path);
		$cnt = count($parts);
		for($i=0;$i<$cnt;++$i){
			$parts[$i] = urlencode($parts[$i]);
		}

		$url .= join('/',$parts);
		
		if(is_array($urlParam)){
			$params = array();
			foreach($urlParam as $k => $v){
				$params[] = $k.'='.urlencode($v);
			}
			$url .= '?'.join('&',$params);
		}
		return $url;
	}

	/**
	 * 內部載入語系字串
	 * @param string $name 語系檔名稱 e.g. 'name' or 'folder/name'
	 * @param bool $framework 是否為框架語系
	 */
	private static function _loadLanguage($name,$framework){
		if($framework){
			$filename = self::conf()->FRAMEWORK_PATH.'language/'.self::conf()->LANGUAGE.'/'.$name.PHP_EXT;
		}else{
			$filename = self::conf()->LANGUAGE_PATH.self::conf()->LANGUAGE.'/'.$name.PHP_EXT;
		}

		if(file_exists($filename)){
			self::$langs[$name] = include ($filename);
			return true;
		}else{
			self::$langs[$name] = 0;
			return false;
		}
	}

	/**
	 * 取得框架語系字串
	 * @param string $name 語系檔名稱 e.g. 'name' or 'folder/name' 若傳入空字串，則為預設 'language'
	 * @param string $message 語言訊息
	 * @param mixed $params 參數
	 * @return string
	 */
	public static function ft($name,$message,$params = null){
		return self::_t(true,&$name,&$message,$params);
	}

	/**
	 * 內部取得語系字串
	 * @param bool $framework 是否為框架語系
	 * @param string $message 語言訊息
	 * @param string $name 語系檔名稱 e.g. 'name' or 'folder/name' 若傳入空字串，則為預設 'language'
	 * @param mixed $params 參數
	 * @return string
	 */
	private static function _t($framework,$name,$message,$params = null){
		if(empty($name)) $name = 'language';

		if(isset(self::$langs[$name])){
			if(0 !== self::$langs[$name]){
				if(isset(self::$langs[$name][$message])) $message = self::$langs[$name][$message];
			}
		}else{
			if(self::_loadLanguage($name,$framework)){
				if(isset(self::$langs[$name][$message])) $message = self::$langs[$name][$message];
			}
		}

		if(null !== $params){
			if(is_array($params)){
				foreach($params as $k => $v){
					$replace['[:'.$k.']'] = $v;
				}
			}else{
				$replace = array('[:0]',$params);
			}
			return strtr($message,$replace);
		}else{
			return $message;
		}

	}

	/**
	 * 取得語系字串
	 * @param string $name 語系檔名稱 e.g. 'name' or 'folder/name' 若傳入空字串，則為預設 'language'
	 * @param string $message 語言訊息
	 * @param mixed $params 參數
	 * @return string
	 */
	public static function t($name,$message,$params = null){
		return self::_t(false,&$name,&$message,&$params);
	}
}
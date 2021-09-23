<?php
/**************************************************************************
 * Lazybones web development framework for PHP 5.1.2 or newer
 *
 * @author      顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @copyright   Copyright (c) 2010, 顏宏育(Hong Yu Yan)
 **************************************************************************/

/**
 * URL路由器
 *
 * @author 顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @package core
 */
class LZ_Router {

	/**
	 * 路由設定
	 * @var array
	 */
	private $_routes;

	public function  __construct() {}

	/**
	 * 執行Route處理
	 * @param array $routeArr 路由設定
	 * @param bool $autoRoute 是否啟用自動路由
	 */
	public function execute($routeArr,$autoRoute) {
		$this->_routes = $routeArr;

		$match = $this->matchRoute($routeArr);

		$route = ($match != null) ? $match['route'] : null;
		$vars = ($match != null) ? $match['vars'] : array();
		$matchRule = ($match != null) ? $match['rule'] : '';
		
		if(null === $route) {
			if($autoRoute == true) {
				$this->_executeAutoRoute();
			}else{
				$this->statusPage(404);
			}
			return;
		}elseif(is_string($route)){
			$this->redirect($route, true);
			return;
		}

		$ctrlName = $route[0];
		$methodName = (isset($route[1])) ? $route[1] : '';
		$filters = (isset($route[2])) ? $route[2] : null;
		
		$filterResult = true;
		if($filters != null) $filterResult = $this->_executeFilter($filters,$vars);
		
		if(true === $filterResult){
			$this->runController($ctrlName, $methodName, $vars, $matchRule);
		}elseif(is_int($filterResult)){
			$this->statusPage($filterResult);
		}elseif(is_string($filterResult)){
			$this->redirect($filterResult);
		}elseif(is_array($filterResult)){
			$methodName = (isset($filterResult[1])) ? $filterResult[1] : '';
			$this->runController($filterResult[0], $methodName, $vars, $matchRule);
		}else{
			$this->statusPage(404);
		}
	}

	/**
	 * URL路由匹配
	 * @param array $routeArr 路由設定
	 * @return array 匹配的路由
	 */
	public function matchRoute($routeArr) {

		$type = strtolower($_SERVER['REQUEST_METHOD']);
		$pinfo = $this->getPathInfo();
		$pinfo = trim($pinfo,'/');
		$pinfoKey = '/'.$pinfo;
		$vars = null;
		
		if(app::conf()->ENTRY_FILE != ''){
			$pinfoKey = stripPrefix($pinfoKey, '/'.app::conf()->ENTRY_FILE);
			if(false  === $pinfoKey) $pinfoKey = '/';
		}
		
		if('/' === $pinfoKey){
			if(isset($routeArr[$type]['/'])) {
				return array('route' => $routeArr['*']['/'],'vars' => $vars ,'rule' => $pinfoKey);
			}elseif(isset($routeArr['*']['/'])) {
				return array('route' => $routeArr['*']['/'],'vars' => $vars ,'rule' => $pinfoKey);
			}else {
				app::throwError(app::ft('','LZ_Router : 預設頁面未定義'));
			}
		}

		if(isset($routeArr[$type][$pinfoKey])) {
			return array('route' => $routeArr[$type][$pinfoKey],'vars' => $vars,'rule' => $pinfoKey);
		}else {
			$routeType = isset($routeArr[$type]) ? $routeArr[$type] : array();
		}

		if(isset($routeArr['*'][$pinfoKey])) {
			return array('route' => $routeArr['*'][$pinfoKey],'vars' => $vars ,'rule' => $pinfoKey);
		}else {
			$routeAny = isset($routeArr['*']) ? $routeArr['*'] : array();
		}

		$uparts = explode('/', $pinfo);
		$utop = count($uparts)-1;

		$match = null;
		$matchRule = '';

		$routes = array_merge($routeAny,$routeType);

		foreach($routes as $ruleKey => $route) {
			$rparts = explode('/', trim($ruleKey,'/'));
			$rtop = count($rparts)-1;
			if($rtop != $utop) {
				if($rparts[$rtop] != '*') {
					continue;
				}else {
					if( ($utop < $rtop) && ($utop != $rtop-1) ) {
						continue;
					}
				}
			}

			$i = 0;
			$addIdx = 0;
			
			if($rparts[0] !== $uparts[0]) {
				$first = substr($rparts[$i],0,1);
				if($first == '*') {
					$this->_pushVars($vars, $addIdx, $addIdx, $uparts[$i]);
					$match = $routes[$ruleKey];
					$matchRule = $ruleKey;
				}elseif($first == ':') {
					$this->_pushVars($vars, $addIdx, substr($rparts[$i],1) , $uparts[$i]);
					$match = $routes[$ruleKey];
					$matchRule = $ruleKey;
				}else {
					continue;
				}
			}else{
				$match = $routes[$ruleKey];
				$matchRule = $ruleKey;
			}

			while(true) {
				$i++;
				if(isset($uparts[$i]) && isset($rparts[$i])) {
					//對等
					if($uparts[$i] != $rparts[$i]) {
						$first = substr($rparts[$i],0,1);
						if($first == '*') { //不定參數
							$this->_pushVars($vars, $addIdx, $addIdx , $uparts[$i]);
						}elseif($first == ':') { //指定參數
							$this->_pushVars($vars, $addIdx, substr($rparts[$i],1) , $uparts[$i]);
						}else {
							$match = null;
							break;
						}
					}
				}else {
					if($rparts[$rtop] != '*' && $rtop != $utop) {
						$match = null;
						break;
					}
					if(isset($uparts[$i])) { //不定參數
						$this->_pushVars($vars, $addIdx,$addIdx , $uparts[$i]);
					}else { //不定參數
						if($rparts[$rtop] == '*' && $rtop == $utop && isset($uparts[$i])){
							$this->_pushVars($vars, $addIdx,$addIdx , $uparts[$i]);
						}else{
							break;
						}
					}
				}
			}
			if($match != null) break;
		}

		if(null === $match) $matchRule = '';

		return array('route' => $match,'vars' => $vars,'rule' => $matchRule);

	}

	/**
	 * 取得 PATH_INFO
	 * @return string
	 */
	public function getPathInfo() {
		$pinfo = $_SERVER['REQUEST_URI'];
		if(!empty(app::conf()->SUBFOLDER)) $pinfo = stripPrefix($pinfo,'/'.stripPrefix(app::conf()->SUBFOLDER, '/'));
		if(!empty(app::conf()->ENTRY_FILE)) $pinfo = stripPrefix($pinfo, app::conf()->ENTRY_FILE);
		return $pinfo;
	}

	/**
	 *　顯示已設定的HTTP 404頁面
	 * @param $statusCode 狀態碼
	 * @param bool $exit = true 是否離開應用程式
	 */
	public function statusPage($statusCode = 404,$exit = true) {
		
		app::output()->statusHeader($statusCode);
		$statusRoute = isset ($this->_routes['!'.$statusCode]) ? $this->_routes['!'.$statusCode] : null;

		if(null !== $statusRoute ) {
			if(is_array($statusRoute)){
				$methodName = isset($statusRoute[1]) ? $statusRoute[1] : '';
				$this->runController($statusRoute[0], $methodName, null , '!STATUS_'.$statusCode.'_ROUTTE' ,false);
			}elseif(is_string($statusRoute)){
				$this->redirect($statusRoute);
			}
		}elseif(intval($statusCode) === 404){
			require app::conf()->FRAMEWORK_PATH . 'data/default_http_404' . PHP_EXT;
		}else{
			app::throwError(app::ft('','LZ_Router : HTTP狀態頁([:0])面未定義',array($statusCode)));
		}
		
		if($exit) exit;
	}

	/**
	 * 重新導向URL
	 * 若 $exit = true 且已啟動Session 將自動儲存 Session內容
	 * @param string $target URL/控制器
	 * @param bool $exit = true 是否離開應用程式
	 */
	public function  redirect($target , $exit=true , $code=302) {
		if($exit) endSession();
		if(is_string($target)){
			app::output()->statusHeader($code);
			app::output()->sendHeader('Location: '.$target, true, $code);
		}else{
			if(!isset($target[1])) $target[1] = null;
			if(!isset($target[2])) $target[2] = null;
			$this->runController($target[0], $target[1], $target[2], '!Controller_REDIRECT');
		}
		if($exit) exit;
	}

	/**
	 * 載入並執行 Controller
	 * @param string $ctrlName 控制器名稱
	 * @param string $methodName 方法名稱
	 * @param array $vars 變數
	 * @param string $matchRule 匹配規則
	 * @param bool $statusPage 發生錯誤是否執行顯示Error404
	 */
	public function runController($ctrlName,$methodName,$vars = null ,$matchRule = '' ,$statusPage = true) {
		
		if($ctrlName != '') {
			if(!$this->_isControllerExists($ctrlName)){
				$this->statusPage(404);
			}
		}elseif($statusPage) {
			$this->statusPage(404);
		}
		
		$result = null;

		$control = app::loadController($ctrlName,true);
		
		if('' === $methodName) $methodName = $control->defaultAction();
		
		if($methodName !== $control->defaultAction()){
			if(!is_callable(array($control, 'Action_'.$methodName))){
				$uparts = explode('/', trim($this->getPathInfo(), '/'));
				if(isset($uparts[1])){
					if(null === $vars){
						$vars = array($uparts[1]);
					}else{
						$vars = array_merge(array($uparts[1]), $vars);
					}
				}
				$methodName = $control->defaultAction();
			}
		}
		
		$control->init($vars,$matchRule);
		
		$result = $control->executeAction($methodName,null);

		if(null !== $result){
			if(is_int($result)){
				$this->statusPage($result);
			}elseif(is_string($result)){
				$this->redirect($result);
			}elseif(is_array($result)){
				$methodName = (isset($result[1])) ? $result[1] : '';
				$this->runController($result[0], $methodName, $vars, '!CONTROLLER_REDIRECT');
			}
		}
	}

	/**
	 *　執行自動路由
	 */
	private function _executeAutoRoute() {
		$pathInfo = $this->getPathInfo();
		$uparts = explode('/', trim($pathInfo, '/'));
		$ctrlName = $methodName = '';
		if(isset($uparts[0])) $ctrlName = $this->_nameFormat($uparts[0]);
		if(isset($uparts[1])) $methodName = $this->_nameFormat($uparts[1]);
		//if($methodName == '') $methodName = 'index';
		$vars = array();
		$ucount = count($uparts);
		$addIdx = 1;
		for($i = 2;$i<$ucount;$i++){
			$this->_pushVars($vars, $addIdx, $addIdx, $uparts[$i]);
		}
		$this->runController($ctrlName, $methodName, $vars , '!AUTO_ROUTE');
	}

	/**
	 * 判斷控制器是否存在
	 * @param string $controlName 控制器名稱
	 * @return bool
	 */
	private function _isControllerExists($ctrlName) {
		if($ctrlName == '') return false;
		$ctrlerPath = app::conf()->CONTROLLER_PATH.$ctrlName . 'Controller' . PHP_EXT;
		return file_exists($ctrlerPath);
	}

	/**
	 * 轉換名稱格式
	 * e.g. hello_world 轉成 HelloWorld
	 * @param string $oldName
	 */
	private function _nameFormat($oldName) {
		$oldName = ucwords(str_replace('_', ' ', $oldName));
		return str_replace(' ', '', $oldName);
	}

	/**
	 * 將變數增加到$vars中
	 * @param array $vars $vars5陣列
	 * @param string $key 索引key
	 * @param mixed $value 值
	 */
	private function _pushVars(&$vars,&$addIdx,$key,$value) {
		$vars[$key] = urldecode($value);
		$addIdx++;
	}

	/**
	 * 執行過路器
	 * @param array $filters 過濾器設定
	 * @param array $vars 變數列表
	 * @return mixed 進一步處理
	 */
	private function _executeFilter($filters ,$vars){
		foreach ($filters as $name  => $filter){
			$type = $filter[0];
			$param = $filter[1];
			$name = ltrim($name,':');
			if($name == '*'){
				//全部參數過濾器
				switch ($type){
					case 'function':
						$result = $param($vars);
						if(true !== $result) return $result;
						break;
					case 'regex':
						foreach($vars as $k => $var){
							if (!preg_match($param,$vars[$k])) return false;
						}
						return $rtval;
						break;
				}
			}else{
				
				//單一參數過濾器
				switch ($type){
					case 'function':
						$result = $param($name,$vars[$name]);
						if(true !== $result) return $result;
						break;
					case 'regex':
						if (!preg_match($param,$vars[$name])) return false;
						break;
				}
				
			}
		}
		return true;
	}
}
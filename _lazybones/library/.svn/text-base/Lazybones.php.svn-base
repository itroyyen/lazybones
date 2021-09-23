<?php
/**************************************************************************
 * Lazybones web development framework for PHP 5.1.2 or newer
 *
 * @author      顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @copyright   Copyright (c) 2010, 顏宏育(Hong Yu Yan)
 **************************************************************************/

/**
 * 框架通用函式庫
 * 
 * @author 顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @package functions
 */

/**
 * 框架版本號
 */
define('FRAMEWORK_VERSION', '0.2.4');// 框架版本號

/**
 * 框架名稱
 */
define('FRAMEWORK_NAME', 'Lazybones');// 框架名稱

//--------------------------------------------------------------------------
//使之支援autoload
if (version_compare(PHP_VERSION, '5.1.2', '>=')) {
	spl_autoload_register('__provide_spl_autoload');
}
//--------------------------------------------------------------------------

/**
 * 取代訊息字串
 * e.g.
 * rt('[:label] 需要輸入值',array('label' => '姓名'))
 * rt('[:0] 需要輸入值','label')
 * rt('歡迎回來 [:0] ，您的帳號是：[:1]',array('王小明','user_account'))
 * @param string $template
 * @param array $params
 * @return string
 */
function rt($template,$params = null){
	if(null !== $params){
		if(is_array($params)){
			foreach($params as $k => $v){
				$replace['[:'.$k.']'] = $v;
			}
		}else{
			$replace = array('[:0]',$params);
		}
		return strtr($template,$replace);
	}else{
		return $template;
	}
}

/**
 * 取得語系字串
 * @param string $name 語系檔名稱 e.g. 'name' or 'folder/name' 若傳入空字串，則為預設 'language'
 * @param string $message 語言訊息
 * @param mixed $params 參數
 * @return string
 */
function t($name,$message,$params = null){
	return app::t(&$name,&$message,&$params);
}

/**
 * 取得語系字串 - 不指定語系檔名稱，使用預設 'language'
 * @param string $message 語言訊息
 * @param mixed $params 參數
 * @return string
 */
function tt($message,$params = null){
	return app::t('',&$message,&$params);
}

/**
 * 提供自動載入Helper/Model/Core/App class/Controller功能
 */
function __provide_spl_autoload($class) {
	if(isSuffix($class,'Model')){
		app::loadModel(stripSuffix($class, 'Model'));
	}elseif(isSuffix($class,'Helper')){
		app::loadHelper(stripSuffix($class, 'Helper'));
	}elseif(isSuffix($class,'Controller')){
		app::loadController(stripSuffix($class, 'Controller'));
	}elseif(isPrefix($class, 'LZ_')){
		app::loadCore($class);
	}else{
		$filename = app::conf()->APP_CLS_PATH . $class . PHP_EXT;
		if(file_exists($filename)) app::loadAppClass($class);
	}
}

/**
 * 取得類別路徑
 * @param string $className 原始路徑名稱
 * @param string $path 路徑
 * @param string $appendName 附加名稱
 * @return array
 */
function getClassPath($className,$path,$appendName = ''){
	if(strpos($className, '/') !== false){
		$parts = explode('/', $className);
		$className = array_pop($parts);
		$path .= join('/',$parts).'/';
	}
	return array($className.$appendName,$path);
}

/**
 * 去除後綴
 * @param string $str
 * @param string $suffix
 * @return string
 */
function stripSuffix($str,$suffix){
	$pos = strrpos($str, $suffix);
	return substr($str, 0,false ===  $pos ? strlen($str) : $pos);
}

/**
 * 去除前綴
 * @param string $str
 * @param string $prefix
 * @return string
 */
function stripPrefix($str,$prefix){
	$pos = strpos($str, $prefix);
	return ($pos === 0) ? substr($str, strlen($prefix)) : $str;
}

/**
 * 判斷字串是否為後綴
 * @param string $str 被判斷的字串
 * @param string $suffix 後綴字
 * @param bool $ignoreCase 是否忽略大小寫
 * @return bool
 */
function isSuffix($str,$suffix,$ignoreCase = false){
	if($ignoreCase){
		$pos = strripos($str, $suffix);
	}else{
		$pos = strrpos($str, $suffix);
	}
	return ($pos === (strlen($str) - strlen($suffix))) && $pos !== false ? true:false;
}

/**
 * 判斷字串是否為前綴
 * @param string $str 被判斷的字串
 * @param string $prefix 前綴字
 * @param bool $ignoreCase 是否忽略大小寫
 * @return bool
 */
function isPrefix($str,$prefix,$ignoreCase = false){
	if($ignoreCase){
		return stripos($str, $prefix) === 0 ? true:false;
	}else{
		return strpos($str, $prefix) === 0 ? true:false;
	}
}

 /**
  * 轉換名稱格式
  * e.g. hello_world 轉成 HelloWorld
  * 若 firstLowe=true 則轉為 helloWorld
  * @param string $str 要被轉換的字串
  * @param string $firstLower 首字是否為小寫
  * @return string
  */
function nameToUpper($str,$firstLower = false){
	$str = ucwords(str_replace('_', ' ', $str));
	$str = str_replace(' ', '', $str);
	if($firstLower) $str = strtolower(substr($str,0,1)) . substr($str,1);
	return $str;
}

/**
 * 根據傳入物件回傳新物件
 * @param object $obj
 * @return object
 */
function newInstance($obj){
	if(is_object($obj)){
		$cls = get_class($obj);
		return new $cls;
	}else{
		app::throwError(app::ft('','參數類型不是物件'));
	}
}

/**
 * 名稱轉換為表名稱 e.g. HelloWorld >> hello_world
 * @param string $name
 * @return string
 */
function nameToUnderline($name){
	$part = splitUpper($name);
	return  strtolower(join('_',$part));
}

/**
 * 取得前綴後接的字串，若前綴不符則回傳null
 * e.g. splitPrefix('findByName','findBy') 回傳 'Name'
 * @param string $str 被尋找的字串
 * @param string $prefix 前綴
 * @param bool $ignoreCase 是否忽略大小寫
 * @return string
 */
 function splitPrefix($str,$prefix,$ignoreCase = false){
 	if($ignoreCase){
 		$pos = stripos($str,$prefix);
 	}else{
 		$pos = strpos($str,$prefix);
 	}

 	if(false === $pos){
 		return null;
 	}else{
 		return substr($str,strlen($prefix));
 	}
 }

 /**
  * 已大寫字母分隔字串
  * e.g.
  * splitUpper('helloWorld');       回傳 array('hello','World');
  * splitUpper('helloWorld',false); 回傳 array('World');
  * @param string $str 要被分割的字串
  * @param bool $allowFirstLower 是否允許首字開頭小寫
  * @return array
  */
 function splitUpper($str,$allowFirstLower = true){
 	$arr = str_split($str);
 	$cnt = count($arr);
 	$active = false;
 	$rtval = array();
 	$temp = '';
 	for($i=0;$i<$cnt;$i++){
 		if( ord($arr[$i]) >= 65 &&  ord($arr[$i]) <= 90){
 			$active = true;
 			if($temp != ''){
 				$rtval[] = $temp;
 				$temp = '';
 			}
 		}

 		if($allowFirstLower){
 			$temp .= $arr[$i];
 		}elseif($active){
 			$temp .= $arr[$i];
 		}
 	}

 	if($temp != '') $rtval[] = $temp;
 	return $rtval;
 }

/**
 * 輸出URL - 簡短
 * @param string|array $path 路徑
 * @param string $uriParam URL附加參數
 * @return string
 */
function url($path = '',$urlParam = null){
	echo app::getUrl(&$path,&$urlParam);
}

/**
 * 輸出URL - 完整
 * @param string|array $path 路徑
 * @param string $uriParam URL附加參數
 * @return string
 */
function url2($path = '',$urlParam = null){
	echo app::getUrl2(&$path,&$urlParam);
}

/**
 * 取得URL - 簡短
 * @param string|array $path 路徑
 * @param string $uriParam URL附加參數
 * @return string
 */
function getUrl($path = '',$urlParam = null){
	return app::getUrl(&$path,&$urlParam);
}

/**
 * 取得URL - 完整
 * @param string|array $path 路徑
 * @param string $uriParam URL附加參數
 * @return string
 */
function getUrl2($path = '',$urlParam = null){
	return app::getUrl2(&$path,&$urlParam);
}

/**
 * 呈現佈局
 * @param string $name 佈局名稱或路徑
 * @param string $component 組件名稱
 * @param array $args 附加參數
 */
function layout($name,$component,$args = null){
	app::layout()->render($name,$component,$args);
}

/**
 * 將字串內容依據指定格式取代為 config 中的設定值
 * e.g. '{:BASE_URL}name' 成為 '/name'
 * @param <type> $str
 * @return <type>
 */
function replaceConfigVar($str){
	$pattern = '/\{\:([a-zA-z0-9_]*)\}/';
	preg_match_all($pattern,$str,$matchs);
	$conf = app::conf();
	foreach($matchs[1] as $name){
		$str = str_replace('{:'.$name.'}', $conf->$name, $str);
	}
	return $str;
}

/**
 * 計時函數
 * @param double $beginTime 開始時間，若不傳入則自動使用常數 _PAGE_BEGIN_TIME_
 */
function elapsedTime($beginTime =  null){
	if(null === $beginTime) $beginTime = _PAGE_BEGIN_TIME_;
	$sec = ($endTime = microtime(true)) -  $beginTime;
	$ms = round((double)$sec * 1000,2);
	$secPer =  round((double)(1 / $sec),2);
	$sec =  round($sec,4);
	$memPeak = round(memory_get_peak_usage() / 1024 / 1024,4) ;
	$mem =round(memory_get_usage() /1024 / 1024,4) ;
	return "<p> [$ms ms] / [$sec sec] / memory usage : now [{$mem} MB] / peak [{$memPeak} MB] </p>";
}

/**
 * 啟動Session 本函數可重複呼叫而不會產生錯誤
 */
function beginSession(){
	if(!isset($_SESSION)) session_start();
}

/**
 * 結束 Session 並儲存內容
 */
function endSession(){
	if(isset($_SESSION)) session_write_close();
}
<?php
/**************************************************************************
 * Lazybones web development framework for PHP 5.1.2 or newer
 *
 * @author      顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @copyright   Copyright (c) 2010, 顏宏育(Hong Yu Yan)
 **************************************************************************/

/**
 * 資料驗證器
 * 
 * @author 顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @package core
 */
class LZ_Validator{

	private $_errMessages;
	private $_handlers;
	private $_errorTemplate;
	private $_hasError = false;
	private $_inTransaction = false;
	private $_rollbacks = array();
	private $_callbacks = array();
	private static $_rollbackData;


	public function  __construct() {
		$this->_errorTemplate = app::loadFrameworkLanguageFile('validator');
		$this->addRollback(array('upper','lower','trim','ltrim','rtrim','account','account_lower'));
		$this->addRollback('upload',array($this,'_rollback_delete_file'));
		$this->addRollback('upload_img',array($this,'_rollback_delete_file'));
		$this->addRollback('upload_img_crop',array($this,'_rollback_delete_file'));
	}

	/**
	 * 檢查資料
	 * @param mixed $value 值
	 * @param string|array $rules 驗證規則
	 * @return bool
	 */
	public function check($value,$rules){
		return $this->_validate(&$value,$rules, null,false);
	}

	/**
	 * 驗證資料並記錄錯誤訊息
	 * @param mixed $value 值
	 * @param string|array $rules 驗證規則
	 * @param string $label 錯誤訊息用的標籤
	 * @return bool
	 */
	public function validate($value,$rules,$label){
		$result = $this->_validate(&$value,$rules, $label);
		return $result;
	}
	
	/**
	 * 內部執行驗證
	 * @param mixed $value 值
	 * @param string|array $rules 驗證規則
	 * @param string $label 標籤
	 * @param array $params 參數
	 * @param bool $storeError 是否記錄錯誤訊息
	 * @return mixed
	 */
	private function _validate($value,$rules,$label = null,$storeError = true){
		$oldValue = $value;
		$rtval = true;
		if(is_string($rules)) $rules = $this->_ruleConvert($rules);
		$params = null;
		
		foreach($rules as $k => $v){
			if(is_int($k)){
				$method = $v;
				$handler = '_rule_'.$v;
			}else{
				$method = $k;
				$handler = '_rule_'.$k;
				if(!is_array($v)) $v = array($v);
				$params = $v;
			}

			if($this->_inTransaction){
				if(in_array($method, $this->_rollbacks)){
					if(!isset(self::$_rollbackData[$method])) self::$_rollbackData[$method] = array();
					self::$_rollbackData[$method][] = array('oldValue' => $oldValue,'value' => &$value ,'params' => $params);
				}
			}

			$args = array(&$value);

			if(null !== $params){
				if(!is_array($params)) $params = array($params);
				$args = array_merge($args, $params);
			}

			if(!isset($args)){
				$result = call_user_func(array($this,$handler));
			}else{
				$result = call_user_func_array(array($this,$handler), $args);
			}

			if(null === $result){
				$rtval = true;
			}else{
				if($storeError){
					if(isset($this->_errorTemplate[$method])){
						if(null !== $label )$result['label'] = $label;
						$this->_hasError = true;
						$this->_errMessages[] = rt($this->_errorTemplate[$method],$result);
					}else{
						$this->rollback();
						app::throwError(app::ft('','LZ_Validator : 規則 "[:method]" 的錯誤訊息樣板未定義',array('method' => $method)));
					}
				}
				$rtval = false;
				break;
			}
		}
		return $rtval;
	}


	/**
	 * 內部規則轉換
	 * @param string $ruleString
	 */
	private function _ruleConvert($ruleString){
		$parts = explode('|', $ruleString);
		$cnt = count($parts);
		$rules = array();
		for($i=0;$i<$cnt;++$i){
			$pos = strpos($parts[$i], '(');
			if($pos !== false){
				$name = substr($parts[$i], 0,$pos);
				$argStr = substr($parts[$i], $pos+1,strlen($parts[$i])-$pos-2);
				$args = explode(',',$argStr);
				$rules[$name] = $args;
			}else{
				$rules[] = $parts[$i];
			}
		}
		return $rules;
	}

	/**
	 * 是否有錯誤訊息
	 * @return bool
	 */
	public function hasError(){
		return $this->_hasError;
	}

	/**
	 * 取得驗證失敗的訊息
	 * @param bool $reset 是否自動執行rest()
	 * @return array
	 */
	public function getError($reset = true){
		$rtval = $this->_errMessages;
		if($reset ) $this->rest();
		return $rtval;
	}

	/**
	 * 清除已經記錄的訊息
	 */
	public function rest(){
		$this->_errMessages = null;
		$this->_hasError = false;
		self::$_rollbackData = null;
	}

	/**
	 * 載入自訂錯誤訊息樣板
	 * @param string $name 名稱
	 */
	public function loadCustomTemplate($name = 'validator'){
		$template = app::loadLanguageFile(app::RES_CUSTOM_CORE.'/validator');
		$this->_errorTemplate = array_merge($this->_errorTemplate, $template);
	}

	/**
	 * 設定錯誤訊息樣板
	 */
	public function setErrorTemplate($name,$template){
		$this->_errorTemplatep[$name] = $template;
	}

	/**
	 * 加入自訂驗證處理器
	 * 回呼處理可傳入以下任一種:<br>
	 * [1] '函數名稱'<br>
	 * [2] array('類別名稱','方法名稱')<br>
	 * [3] array($object,'方法名稱')<br>
	 * 
	 * @param string $name 規則名稱
	 * @param mixed $handler 規則處理器
	 * @param string $errmsgTemplate 錯誤訊息樣板
	 * @param bool $isModifier 是否兼具修飾器功能，true代表於交易模式下將自動復原資料
	 * @param mixed $callback 回呼處理，進一步對修飾器資料復原做處理
	 */
	public function addRule($name,$handler,$errmsgTemplate = null,$isModifier = false,$callback = null){
		$this->_handlers[$name] = $handler;
		if(null !== $errmsgTemplate) $this->_errorTemplate[$name] = $errmsgTemplate;
		if($isModifier) $this->addRollback($name,$callback);
	}

	/**
	 * 開始資料驗證的交易模式，期間驗證資料若被修飾器更動過，將於結束交易模式後復原
	 */
	public function beginTransaction(){
		$this->_inTransaction = true;
	}

	/**
	 * 結束資料驗證的交易模式，若驗證資料有誤將自動復原被修飾器更動過的資料
	 */
	public function endTransaction(){
		$this->_inTransaction = false;
		if($this->hasError()) $this->rollback();
	}

	/**
	 * 加入回滾處理
	 * 回呼處理可傳入以下任一種:<br>
	 * [1] '函數名稱'<br>
	 * [2] array('類別名稱','方法名稱')<br>
	 * [3] array($object,'方法名稱')<br>
	 * 
	 * @param string|array $name 規則名稱，若不需包含 $callback 可用陣列指定
	 * @param mixed $callback 回呼處理
	 */
	protected function addRollback($name,$callback = null){
		if(is_array($name)){
			foreach($name as $v){
				$this->_rollbacks[] = $v;
			}
		}else{
			$this->_rollbacks[] = $name;
			if(null !== $callback) $this->_callbacks[$name] = $callback;
		}
	}

	/**
	 * 資料回滾
	 */
	protected function rollback(){
		if(is_array(self::$_rollbackData)){
			foreach(self::$_rollbackData as $method => $datas){
				foreach($datas as $data){
					if(isset($this->_callbacks[$method])){
						call_user_func_array($this->_callbacks[$method], array($data));
					}
					$data['value'] = $data['oldValue'];
				}
			}
			self::$_rollbackData = null;
		}
	}

	/**
	 * 提供自訂驗證處理器執行
	 */
	public function  __call($name,  $args) {
		$name = stripPrefix($name, '_rule_');
		if(isset($this->_handlers[$name])){
			if(null === $args){
				return call_user_func($this->_handlers[$name]);
			}else{
				return call_user_func_array($this->_handlers[$name], $args);
			}
		}else{
			$cls = get_class($this);
			app::throwError(app::ft('','LZ_Validator : 呼叫未定義的方法成員 [:class]::[:method]()',array('class' => $cls,'method' => $name)));
		}
	}

	//--------------------------------------------------------------------------------
	// 驗證規則處理器，失敗傳回false或array代表參數內容
	//--------------------------------------------------------------------------------
	
	/**
	 * 不得為空字串及null
	 */
	protected function _rule_require($val){
		if('' === $val || null === $val) return false;
	}

	/**
	 * 不得為空值
	 */
	protected function _rule_not_empty($val){
		if(empty($val)) return false;
	}

	/**
	 * 必須為數值格式
	 */
	protected function _rule_num($val){
		if(!is_numeric($val)) return false;
	}

	/**
	 * 不得為0
	 */
	protected function _rule_not_zero($val){
		if(0 === intval($val)) return false;
	}

	/**
	 * 數值必須大於 $min
	 */
	protected function _rule_min($val,$min){
		if(intval($val) < intval($min)) return array('min' => $min);
	}

	/**
	 * 數值必須小於 $max
	 */
	protected function _rule_max($val,$max){
		if(intval($val) > intval($max)) return array('max' => $max);
	}

	/**
	 * 數值必須介於 $min 與 $max 之間
	 */
	protected function _rule_range($val,$min,$max){
		if(intval($val) < intval($min) || intval($val) > intval($max)){
			return array('min' => $min,'max' => $max);
		}
	}

	/**
	 * 字串長度必須介於 $min 與 $max 之間
	 */
	protected function _rule_len($val,$min,$max){
		if(strlen($val) < intval($min) || strlen($val) > intval($max) ){
			return array('min' => $min,'max' => $max);
		}
	}

	/**
	 * 字串長度必須大於 $min
	 */
	protected function _rule_min_len($val,$min){
		if(strlen($val) < intval($min)) return false;
	}

	/**
	 * 字串長度必須小於 $max
	 */
	protected function _rule_max_len($val,$max){
		if(strlen($val) > intval($min)) return false;
	}

	/**
	 * 必須符合正規表達式規則
	 */
	protected function _rule_regex($val,$pattern){
		if(!preg_match($pattern, $val)) return false;
	}

	/**
	 * 必須為英文字母
	 */
	protected function _rule_alpha($val){
		if(!ctype_alpha($val)) return false;
	}

	/**
	 * 必須為英數
	 */
	protected function _rule_alpha_num($val){
		if(!ctype_alnum($val)) return false;
	}

	/**
	 * 必須為正確的email格式
	 */
	protected function _rule_email($val){
		$pattern = "/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])(([a-z0-9-])*([a-z0-9]))+".
		"(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i";
		if(!preg_match($pattern, $val)) return false;
	}

	/**
	 * 必須為正確的IP address格式
	 */
	protected function _rule_ip($val){
		$parts = explode('.', $val);
		if(count($parts) == 4){
			foreach($parts as $part){
				$v = intval($part);
				if($v < 0 || $v > 255) return false;
			}
		}else{
			return false;
		}
	}

	/**
	 * 必須為正確的URL格式
	 */
	protected function _rule_url($val){
		$pattern = "/^".
		"(?:ftp|https?):\/\/(?:(?:(?:[\w\.\-\+!$&'\(\)*\+,;=]|%[0-9a-f]{2})+:)*".
		"(?:[\w\.\-\+%!$&'\(\)*\+,;=]|%[0-9a-f]{2})+@)?".
		"(?:(?:[a-z0-9\-\.]|%[0-9a-f]{2})+|(?:\[(?:[0-9a-f]{0,4}:)*(?:[0-9a-f]{0,4})\]))".
		"(?::[0-9]+)?(?:[\/|\?](?:[\w#!:\.\?\+=&@!$'~*,;\/\(\)\[\]\-]|%[0-9a-f]{2})*)?".
		"$/xi";
		if(!preg_match($pattern, $val)) return false;
	}

	/**
	 * 必須位於 $list 陣列值中，若為失敗則以 $alias 代替值顯示
	 */
	protected function _rule_list($val,$list,$alias = null){
		$idx = array_search($val,$arr2);
		if(false === $idx){
			if(null === $alias){
				return array('list' => join(' / ',$list));
			}else{
				return array('list' => join(' / ',$alias));
			}
		}
	}

	//--------------------------------------------------------------------------------
	// 修飾規則
	//--------------------------------------------------------------------------------
	/**
	 * 字串轉為大寫
	 */
	protected function _rule_upper($val){
		$val = strtoupper($val);
	}

	/**
	 * 字串轉為小寫
	 */
	protected function _rule_lower($val){
		$val = strtolower($val);
	}

	/**
	 * 字串去除頭尾空白，或去除頭尾指定的 $char
	 */
	protected function _rule_trim($val,$char = ' '){
		$val = trim($val, $charlist);
	}

	/**
	 * 字串去除左方空白，或去除左方指定的 $char
	 */
	protected function _rule_ltrim($val,$charlist = ' '){
		$val = ltrim($val, $charlist);
	}

	/**
	 * 字串去除右方空白，或去除右方指定的 $char
	 */
	protected function _rule_rtrim($val,$charlist = ' '){
		$val = rtrim($val, $charlist);
	}
	
	//--------------------------------------------------------------------------------
	// 雙功能規則(修飾規則 + 驗證規則)
	//--------------------------------------------------------------------------------

	/**
	 * 內部上傳檔案
	 */
	private function _upload($filed,$path,$require,$name,$autoRename,$accept = null){
		$upload = UploadHelper::getInstance();
		if(null !== $accept){
			$upload->allowExtensions = $accept;
		}
		$rtval['filename'] = $upload->save($filed,$path,$name,$autoRename);
		$rtval['err'] = null;
		if($upload->hasError()){
			if($require){
				$rtval['err'] =  array('message' => $upload->getLastMessage());
			}elseif($upload->getLastError() !== 4){
				$rtval['err'] = array('message' => $upload->getLastMessage());
			}
		}
		return $rtval;
	}

	/**
	 * 傳入HTML Form欄位名稱，上傳檔案到 $path 路徑下，並以 $name 所指定的名稱重新命名<br />
	 * 若檔案已存在，且 $autoNewName 為 true 時則自動重新命名，反之則覆蓋<br />
	 * 若 $require為 flase，則若未上傳檔案將不出現錯誤訊息
	 */
	protected function _rule_upload($val,$path,$require = true,$name = null,$autoRename = true){
		$result = $this->_upload($val,$path,$require,$name,$autoRename);
		$val = false === $result['filename'] ? '' : $result['filename'];
		if(null !== $result['err']) return $rtval['err'];
	}

	/**
	 * 傳入HTML Form欄位名稱，上傳檔案到 $path 路徑下，並以 $name 所指定的名稱重新命名，依照$width及$height等比例縮小圖片<br />
	 * 若檔案已存在，且 $autoRename 為 true 時則自動重新命名，反之則覆蓋<br />
	 * 若 $require為 flase，則若未上傳檔案將不出現錯誤訊息
	 */
	protected function _rule_upload_img($val,$path,$width,$height,$require = true,$name = null,$autoRename = true ){
		$result = $this->_upload($val,$path,$require,$name,$autoRename,array('jpg','jpeg','png','gif'));
		$val = false === $result['filename'] ? '' : $result['filename'];
		if(null !== $result['err']) return $rtval['err'];
		
		if(false !== $result['filename']){
			$image = new ImageHelper();
			$rtval = $image->reszie($result['filename'], $result['filename'],$width,$height);
			if(!$rtval){
				return array('message' => app::ft('', '圖片無法重新調整大小'));
			}
		}
	}

	/**
	 * 傳入HTML Form欄位名稱，上傳檔案到 $path 路徑下，並以 $name 所指定的名稱重新命名，且從$x1,$y1 剪裁 $width寬,$height 高的圖片
	 * 若檔案已存在，且 $autoRename 為 true 時則自動重新命名，反之則覆蓋<br />
	 * 若 $require為 flase，則若未上傳檔案將不出現錯誤訊息
	 */
	protected function _rule_upload_img_crop($val,$path,$x1,$y1,$width,$height,$require = true,$name = null,$autoRename = true){
		$result = $this->_upload($val,$path,$require,$name,$autoRename,array('jpg','jpeg','png','gif'));
		$val = false === $result['filename'] ? '' : $result['filename'];
		if(null !== $result['err']) return $rtval['err'];

		if(false !== $result['filename']){
			$image = new ImageHelper();
			$rtval = $image->crop($result['filename'], $result['filename'],$x1,$y1,$width,$height);
			if(!$rtval){
				return array('message' => app::ft('', '圖片無法剪裁'));
			}
		}
	}

	/**
	 * 傳入HTML Form欄位名稱，上傳檔案到 $path 路徑下，並以 $name 所指定的名稱重新命名，且依據$x1,$y1 與$x2,$y2剪裁圖片
	 * 若檔案已存在，且 $autoRename 為 true 時則自動重新命名，反之則覆蓋<br />
	 * 若 $require為 flase，則若未上傳檔案將不出現錯誤訊息
	 */
	protected function _rule_upload_img_crop2($val,$path,$x1,$y1,$x2,$y2,$require = true,$name = null,$autoRename = true){
		$result = $this->_upload($val,$path,$require,$name,$autoRename,array('jpg','jpeg','png','gif'));
		$val = false === $result['filename'] ? '' : $result['filename'];
		if(null !== $result['err']) return $rtval['err'];

		if(false !== $result['filename']){
			$image = new ImageHelper();
			$rtval = $image->crop2($result['filename'], $result['filename'],$x1,$y1,$x2,$y2);
			if(!$rtval){
				return array('message' => app::ft('', '圖片無法剪裁'));
			}
		}
	}

	/**
	 * 用於帳號驗證及處理，必須為英數及底線組成，自動將傳入值去除頭尾空白
	 */
	protected function _rule_account($val){
		$val = trim($val);
		$pattern = '/^[a-zA-Z0-9_]*$/i';
		if(!preg_match($pattern, $val)) return false;
	}

	/**
	 * 用於帳號驗證及處理，必須為英數及底線組成，自動將傳入值去除頭尾空白及轉換為小寫
	 */
	protected function _rule_account_lower($val){
		$val = strtolower(trim($val));
		$pattern = '/^[a-z0-9_]*$/i';
		if(!preg_match($pattern, $val)) return false;
	}

	//--------------------------------------------------------------------------------
	// 回滾處理
	//--------------------------------------------------------------------------------

	/**
	 * 提供 upload/upload_img/img_crop 刪除已上傳檔案回滾處理
	 */
	private function _rollback_delete_file($data){
		if(file_exists($data['value'])){
			unlink($data['value']);
		}
	}
}
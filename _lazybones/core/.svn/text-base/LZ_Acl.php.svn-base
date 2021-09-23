<?php
/**************************************************************************
 * Lazybones web development framework for PHP 5.1.2 or newer
 *
 * @author      顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @copyright   Copyright (c) 2010, 顏宏育(Hong Yu Yan)
 **************************************************************************/

/**
 * Acl (Access Control List)
 * 提供簡易的方式驗證訪客身分，並引導至適合頁面
 *
 * @author 顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @package core
 */
class LZ_Acl {

	private $_handler;
	public $name;
	private  $_conf;

	/**
	 * 掌握事件的進行
	 * 若為true則匹配成功後，其他 LZ_Controller::EVENT_BEFORE_ACTION 與 EVENT_AFTER_ACTION 將不再被執行
	 * @var bool
	 */
	public $holdEvent = true;

    public function  __construct() {}

	/**
	 * 開始執行
	 * @param mixed $authHandler 權限控制者
	 * @param string $configName 設定檔名稱
	 */
	public function execute($authHandler,$configName = 'acl'){
		$this->_conf = app::loadConfig($configName);
		$this->_handler = $authHandler;
		app::event()->addListener('LZ_Controller.before_action', array($this,'__beforeContrllerAction'),0);
	}

	/**
	 * 監聽控制器執行Action前事件
	 * @param LZ_Controller $controller
	 * @param string $action
	 */
	public function __beforeContrllerAction($controller,$action){
		$resCtrl = $controller->name;
		$resFull = "$resCtrl.$action";

		$auth = call_user_func($this->_handler);
		
		if(!$this->_isAlwaysAllow($resCtrl, $resFull)){
			if(null === $auth){
				if(!isset($this->_conf['auth'])) app::throwError(app::ft('core_msg','LZ_Acl : 無法確認身分，請檢查組態檔'));
				$auth = $this->_conf['auth'];
			}elseif(is_array($auth)){
				$mergeFnc = 'array_merge_recursive';
				if(isset($auth['replace'])){
					if(false === $auth['recursive']) $mergeFnc = 'array_merge';
				}
				$this->_conf = $mergeFnc($this->_conf,$auth);
				$auth = $auth['auth'];
			}
			return $this->_verify($auth,$resCtrl, $resFull);
		}
	}

	/**
	 * 內部判斷是否為AlwaysAllow
	 * @param string $resCtrl 資源 - 控制器
	 * @param string $resFull 資源 - 完整
	 * @return bool
	 */
	private function _isAlwaysAllow($resCtrl,$resFull){
		if(isset($this->_conf['alwaysAllow'])){
			$always = $this->_conf['alwaysAllow'];
			if(in_array($resCtrl, $always)){
				return true;
			}elseif(in_array($resFull, $always)){
				return true;
			}
		}
		return false;
	}

	/**
	 * 內部執行檢驗
	 * @param string $auth 權限
	 * @param string $resCtrl 資源 - 控制器
	 * @param string $resFull 資源 - 完整
	 * @return mixed
	 */
	private function _verify($auth,$resCtrl,$resFull){
		
		$denyRoutes = array('*' => '{:BASE_URL}');
		$default = 'allow';
		$isAllow = false;
		
		if(isset($this->_conf['default']))   $default = $this->_conf['default'];
		if(isset($this->_conf['denyRoute'])) $denyRoutes = $this->_conf['denyRoute'];
		if('allow' === $default){
			$rules = $this->_conf['deny'];
			$isAllow = true;
		}else{
			$rules = $this->_conf['allow'];
			$isAllow = false;
		}

		if(isset($rules[$auth])){
			$rule = $rules[$auth];
			if(is_array($rule)){
				
				if(in_array($resFull, $rule)){
					$isAllow = !$isAllow;
				}elseif(in_array($resCtrl, $rule)){
					$isAllow = !$isAllow;
				}elseif(in_array('*', $rule)){
					if(!$this->_isAlwaysAllow($resCtrl, $resFull)){
						$isAllow = !$isAllow;
					}
				}
			}elseif('*' === $rule){
				$isAllow = !$isAllow;
			}
		}

		if(!$isAllow) {
			if(isset($denyRoutes[$resFull])){
				$route = $denyRoutes[$resFull];
			}elseif(isset($denyRoutes[$resCtrl])){
				$route = $denyRoutes[$resCtrl];
			}elseif(isset($denyRoutes['*'])){
				$route = $denyRoutes['*'];
			}else{
				$route = 404;
			}
			return $route;
		}
	}
}
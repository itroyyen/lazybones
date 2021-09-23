<?php
/**************************************************************************
 * Lazybones web development framework for PHP 5.1.2 or newer
 *
 * @author      顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @copyright   Copyright (c) 2010, 顏宏育(Hong Yu Yan)
 **************************************************************************/

/**
 * 組態檔基底類別 -  儲存及設置全站組態
 * 
 * @author 顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @package core
 */
class LZ_Config{
	/**
	 * 網站路徑 - e.g. C:/website/
	 * @var string
	 */
	public $BASE_PATH;

	/**
	 * 子目錄 - 若無為空字串 e.g. sub_folder/
	 * @var stirng
	 */
	public $SUBFOLDER;

	/**
	 * Javascript路徑 - e.g. C:/website/js/
	 * @var string
	 */
	public $JS_PATH;

	/**
	 * CSS路徑 - e.g. C:/website/css/
	 * @var string
	 */
	public $CSS_PATH;

	/**
	 * 應用程式路徑 - e.g. C:/website/_app/
	 * @var string
	 */
	public $APP_PATH;

	/**
	 * 組態檔路徑 - e.g. C:/website/_config/
	 * @var string
	 */
	public $CONFIG_PATH;

	/**
	 * Controller路徑 - e.g. C:/website/_app/Controller/
	 * @var string
	 */
	public $CONTROLLER_PATH;

	/**
	 * Model路徑 - e.g. C:/website/_app/model/
	 * @var string
	 */
	public $MODEL_PATH;

	/**
	 * Application 自定類別路徑 - e.g. C:/website/_app/class/
	 * @var string
	 */
	public $APP_CLS_PATH;

	/**
	 * Application 自定函式庫路徑 - e.g. C:/website/_app/library/
	 * @var string
	 */
	public $APP_LIB_PATH;

	/**
	 * View 路徑 - e.g. C:/website/_app/view/default/
	 * @var string
	 */
	public $VIEW_PATH;

	/**
	 * View Compiled 路徑 - e.g. C:/website/_app/view_c/default/
	 * @var string
	 */
	public $VIEWC_PATH;

	/**
	 * View 模式
	 * @var string
	 */
	public $VIEW_MODE;

	/**
	 * View 模式
	 * @var bool
	 */
	public $VIEW_ALWAYS_COMPILE = false;

	/**
	 * Layout 路徑 - e.g. C:/website/_app/layout/
	 * @var string
	 */
	public $LAYOUT_PATH;

	/**
	 * Framework路徑 - e.g. C:/website/_lazybones/
	 * @var string
	 */
	public $FRAMEWORK_PATH;

	/**
	 * 網站網址 - e.g. http://domain.com/
	 * @var string
	 */
	public $BASE_URL;

	/**
	 * Javascript 網址 - e.g. http://domain.com/js/
	 * @var string
	 */
	public $JS_URL;

	/**
	 * CSS 網址 - e.g. http://domain.com/css/
	 * @var string
	 */
	public $CSS_URL;

	/**
	 * Images 網址 - e.g. http://domain.com/images/
	 * @var string
	 */
	public $IMG_URL;

	/**
	 * 網站短網址 - e.g. /
	 * @var string
	 */
	public $BASE_SHORT_URL;
	
	/**
	 * Javascript 短網址 - e.g. /js/
	 * @var string
	 */
	public $JS_SHORT_URL;

	/**
	 * CSS 短網址 - e.g. /css/
	 * @var string
	 */
	public $CSS_SHORT_URL;

	/**
	 * Images 短網址 - e.g. /images/
	 * @var string
	 */
	public $IMG_SHORT_URL;

	/**
	 * 是否啟動自動路由
	 * @var boolean
	 */
	public $AUTO_ROUTE;

	/**
	 * 資料庫設定
	 * @var array
	 */
	public $DB;

	/**
	 * 資料表附加前綴 e.g. 'LAZY_'
	 * @var array
	 */
	public $TABLE_PREFIX = '';

	/**
	 * 資料庫模式
	 * @var string
	 */
	public $DB_MOD;

	/**
	 * 是否啟用除錯模式
	 * @var bool
	 */
	public $DEBUG_MOD = false;

	/**
	 * 入口檔案路徑，預設為index.php
	 * @var string
	 */
	public $ENTRY_FILE;

	/**
	 * 預設輸出Content-Tpype
	 * @var string
	 */
	public $CONTENT_TYPE;

	/**
	 * 語系
	 * @var string
	 */
	public $LANGUAGE;

	/**
	 * 語系 e.g. C:/website/_app/language/
	 * @var string
	 */
	public $LANGUAGE_PATH;

	/**
	 * 主題
	 * @var string
	 */
	public $THEME;

	/**
	 * 內部記錄 View 路徑 - e.g. C:/website/_app/view/
	 * @var string
	 */
	private $_viewPath;

	/**
	 * 內部記錄 View Compiled 路徑 - e.g. C:/website/_app/view_c/
	 * @var string
	 */
	private $_viewcPath;

	/**
	 * 內部記錄關聯設定
	 * @var string
	 */
	private $_db_rel;

	/**
	 * 設置組態
	 * @param array $config
	 */
	public function setConfig($config){
		foreach($config as $k => $v){
			$this->{$k} = $v;
		}
		
		$this->_viewPath = $this->VIEW_PATH;
		$this->_viewcPath = $this->VIEWC_PATH;

		if(!empty($this->THEME)){
			$this->VIEW_PATH = $this->_viewPath . $this->THEME . '/';
			$this->VIEWC_PATH = $this->_viewcPath . $this->THEME . '/';
		}
	}

	/**
	 * 取得目前啟用的資料庫設定
	 * @param bool $withRelation 是否包含關聯設定
	 * @return array
	 */
	public function getActiveDbConfig($withRelation = false){
		$mod = $this->DB_MOD;
		if($withRelation){
			if(!isset($this->DB[$mod]['RELATION'])){
				$this->DB[$mod]['RELATION'] = app::loadConfig('db_rel');
			}
		}
		return $this->DB[$mod];
	}

	/**
	 * 更改主題名稱
	 * @param string $theme 主題名稱
	 */
	public function changeTheme($theme){
		$this->THEME = $theme;
		$theme = ('' !== $theme) ? $theme.'/' : '';
		$this->VIEW_PATH = $this->_viewPath . $theme;
		$this->VIEWC_PATH = $this->_viewcPath . $theme;
	}

	/**
	 * 更改語系名稱
	 * @param string $language 語系名稱
	 */
	public function changelanguage($language){
		$this->LANGUAGE = $language;
	}
}
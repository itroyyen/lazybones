<?php
/****************************************************************
 * 主要組態設定
 ****************************************************************/

/*----------------------------------------------------------------
 * 一般設定
 *--------------------------------------------------------------*/
 define('PHP_EXT' , '.php');    // PHP  副檔名
 define('VIEW_EXT', '.php');    // View 副檔名

// 是否啟動自動路由
$config['AUTO_ROUTE'] = true;

// 是否啟用除錯模式
$config['DEBUG_MOD'] = true;

// 入口檔案名稱 e.g. index.php
$config['ENTRY_FILE'] = '';

// 視圖模式 ( Basic or Magic )
$config['VIEW_MODE'] = 'Magic';

// 視圖模式 ( Basic or Magic )
$config['VIEW_MODE'] = 'Magic';

//是否總是編譯視圖 - 視圖是否總是編譯的預設值(建議於開發階段設定為 true)
$config['VIEW_ALWAYS_COMPILE'] = false;

// 預設 Content-Type
$config['CONTENT_TYPE'] = 'text/html; charset=utf-8';

// 預設語系
$config['LANGUAGE'] = 'zh_tw';

// 預設視圖主題
$config['THEME'] = 'default';

/*----------------------------------------------------------------
 * 實際檔案路徑設定
 *--------------------------------------------------------------*/
//Site absolute path (Site base path)
$config['BASE_PATH'] = str_replace('/_config', '', str_replace('\\', '/', dirname(__FILE__))) . '/';

//Subfolder path
$config['SUBFOLDER'] = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\','/',$config['BASE_PATH']));

//Framework path
$config['FRAMEWORK_PATH'] = $config['BASE_PATH'].'_lazybones/';

//Javascript path
$config['JS_PATH'] = $config['BASE_PATH'].'js/';

//CSS path
$config['CSS_PATH'] = $config['BASE_PATH'].'css/';

//app path
$config['APP_PATH'] = $config['BASE_PATH'].'_app/';

//Configuration Path
$config['CONFIG_PATH'] = $config['BASE_PATH'].'_config/';

//App Class path
$config['APP_CLS_PATH'] = $config['APP_PATH'].'class/';

//App Library path
$config['APP_LIB_PATH'] = $config['APP_PATH'].'library/';

//Controller path
$config['CONTROLLER_PATH'] = $config['APP_PATH'].'controller/';

//Language Path
$config['LANGUAGE_PATH'] = $config['APP_PATH'].'language/';

//Model path
$config['MODEL_PATH'] = $config['APP_PATH'].'model/';

//View path
$config['VIEW_PATH'] = $config['APP_PATH'].'view/';

//View Compiled path
$config['VIEWC_PATH'] = $config['APP_PATH'].'view_c/';

//Layout path
$config['LAYOUT_PATH'] = $config['APP_PATH'].'layout/';

/*-----------------------------------------------------------------------------------------------------------
 * URL 設定
 *-----------------------------------------------------------------------------------------------------------*/
$subfolder = (!empty($config['SUBFOLDER']) ? ltrim($config['SUBFOLDER'],'/') : '');

//Site URL
$config['BASE_URL'] ='http://'.$_SERVER['HTTP_HOST'].'/'.$subfolder;

//Javascript URL
$config['JS_URL'] = $config['BASE_URL'].'js/';

//CSS URL
$config['CSS_URL'] = $config['BASE_URL'].'css/';

//Images URL
$config['IMG_URL'] = $config['BASE_URL'].'images/';

//Site short URL
$config['BASE_SHORT_URL'] = '/'.$subfolder;

//Javascript short URL
$config['JS_SHORT_URL'] = $config['BASE_SHORT_URL'].'js/';

//CSS short URL
$config['CSS_SHORT_URL'] = $config['BASE_SHORT_URL'].'css/';

//Images short URL
$config['IMG_SHORT_URL'] = $config['BASE_SHORT_URL'].'images/';

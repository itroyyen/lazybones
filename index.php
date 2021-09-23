<?php
//************************************************************************
//PHP debug tools
if($_SERVER['HTTP_HOST'] === 'lazybones'){
	require './debug/lib/debug.php';
}
//************************************************************************
// 開始時間
define('_PAGE_BEGIN_TIME_', microtime(true));

//************************************************************************
// 錯誤報告等級定義
define('ERROR_REPORTING_SETTING', E_ALL | E_STRICT); //此常數位必須定義常數
error_reporting(ERROR_REPORTING_SETTING);

//************************************************************************
// 設置時區
//date_default_timezone_set('Asia/Taipei');

//************************************************************************
// 載入主要設定
require '_config/main.conf.php';

//************************************************************************
// 載入app
require $config['FRAMEWORK_PATH'].'app.php';

//************************************************************************
// 載入資料庫設定，若無需資料庫可移除
require $config['CONFIG_PATH'].'db.conf.php';

//************************************************************************
// 載入URL路由設定
require $config['CONFIG_PATH'].'route.conf.php';

//************************************************************************
//載入相關類別
require $config['FRAMEWORK_PATH'].'core/LZ_Config.php';
require $config['APP_PATH'].'AppConfig.php';
require $config['APP_PATH'].'AppProject.php';

//************************************************************************
// 啟動應用程式
app::conf()->setConfig($config);
app::run($route);

//************************************************************************
// 呼叫函數取得程式執行後時間
echo elapsedTime();
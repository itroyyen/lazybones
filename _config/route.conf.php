<?php
/**
[設定說明 - 基本格式]
$route['RuquestType']['Rule'] = array('Controller','Action'[,array(...)]);

RuquestType
請求的方法，大小寫敏感，一律使用小寫，可設定=> 不限定 , get => GET方法 , post => POST方法 或 其他如 : delete

Controller
導向的控制器

Action
導向的控制器動作

array(...) 可選項
自訂參數過濾器

[優先權]
1. RuquestType : 具體指定Request方法 >(不限定Request方法)
2. Rule        : 依照設定的順序
3. 路由規則 > 自動路由

[Rule格式說明]
符號  *    代表不定參數
符號  :var   代表將該值傳入控制器的$var欄位，可在控制器中以$this->v['var']取得，var為可自訂的變數關鍵字
符號  /      為分隔符號，Rule最後不加/

[自訂參數過濾器說明]

格式為 : 第三個陣列參數傳入如下
array(
	'變數名' => array('過濾器類型', '內容'),
	'*' => array('過濾器類型', '內容'),
)

變數名為 * 代表為全部參數過濾器
若為不定參數，則變數名則設為 ':num' 如 ':0'

過濾器類型可設定為 :
function 傳入函數名稱進行驗證，傳回true代表通過，反之則失敗
regex    以正則表達式進行驗證

若使用函數過濾器，將根據回傳值進行處理
回傳true，則代表通過驗證，
若回傳[數值]則顯示狀態頁面，如：404則顯示Error404頁面
若回傳[陣列]則交由另一個Controller處理，如：array('controller','action') 省略action則代表預設action
若回傳[字串]則重新導向所指的URL，如：'http://www.google.com.tw' 或 '{:BASE_URL}main/'
*/
//route規則最後不要加斜線，有具體指定RequestMothod優先權較高，例如get大於*
//指定格式為 $route['RequestMothod']['規則'] = 路由設定陣列
//[首頁] -------------------------------------
// $URL = '/'
$route['*']['/'] = array('Main');

$route['*']['/test_router/:param'] = array(
	'Post',
	'View',
	array(
	'*' => array('function', 'my_filter') )
);

function my_filter($vars){
	$value = strtolower($vars['param']);
	switch($value){
		case 'google':
			//重新導向URL
			return 'http://www.google.com.tw';
		case 'news':
			//重新導向URL
			return app::getUrl('news/view');
		case 'main':
			//重新導向到MainController的Test動作
			return array('Main','Test');
			break;
		case 'error':
			//顯示HTTP 404錯誤頁面
			return 404;
		default :
			//不做任何處理
			return true;
	}
}




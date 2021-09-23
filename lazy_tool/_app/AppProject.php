<?php
/**
 * 專案類別
 * 
 * @package project
 */
class AppProject {

	/**
	 * [特殊方法成員]
	 * 於應用程式開始執行時呼叫此方法
	 */
	public static function __AppRun__(){
		// 註冊View編譯預處理
		app::event()->addListener('LZ_View.preprocess', array('AppProject','__commonResourcePreprocess'));
	}

	/**
	 * [特殊方法成員]
	 * 於應用程式結束執行前呼叫此方法
	 */
	public static function __AppEnd__(){
		
	}

	/**
	 * ACL auth 處理器
	 * @return string|array
	 */
	public static function __authHandler(){
		
	}

	/**
	 * 通用 View 編譯預處理
	 * 可將編譯後的視圖檔案中 img/script/link 標籤的路徑調整為正確路徑
	 * 
	 * @param string $content 可透過修改此參數改變編譯後的視圖檔案內容
	 */
	public static function __commonResourcePreprocess($content){
		$path= app::conf()->VIEWC_PATH.'file.txt';
		$backLevel = 0;

		while(true){
			$path = dirname($path).'/';
			++$backLevel;
			if($path == app::conf()->BASE_PATH || $backLevel > 20) break;
		}

		$patterns = array('/<link .*href="(.*)".*>/U','/<img .*src="(.*)".*>/U','/<script .*src="(.*)".*>/U');
		foreach($patterns as $pattern){
			if (preg_match_all($pattern, $content, $matchs)) {
				foreach($matchs[1] as $search){
					$replace = $search;
					for($i=0;$i<$backLevel;++$i){
						$replace = stripPrefix($replace, '../');
					}
					$replace = app::conf()->BASE_SHORT_URL.$replace;
					$content = str_replace($search, $replace, $content);
				}
			}
		}
	}
}
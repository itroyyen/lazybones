<?php
/**
 * Lazybones Project
 *
 * @author 顏宏育(Hong Yu Yan) <royyam0812@gmail.com>
 * @package project
 */
class AppProject {

	/**
	 * [特殊方法成員]
	 * 於應用程式開始執行時呼叫此方法
	 */
	public static function __AppRun__(){
		/**/
		//ACL
		//app::acl()->execute(array('AppProject','__authHandler'));

		//LZ_View預處理
		//app::event()->addListener('LZ_View.preprocess', array('AppProject','__commonResourcePreprocess'));

		//app::conf()->PUBLIC_PATH = app::conf()->BASE_PATH . 'public/';
		/**/
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
		return 'vip';
	}

	/**
	 * 通用 LZ_ViewMagicCompiler 預處理
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


	public static function _getMainView(){
		//return $mainView;
	}

	public static function _getTestUriList(){
		$filePath = app::conf()->BASE_PATH.'url_list.txt';
		$outlink  = file_get_contents($filePath);
		$lines = explode("\n", $outlink);
		$rtval = '';
		foreach($lines as $line){
			$line = trim($line);
			if($line == '') continue;
			if(preg_match('/^\[.*\]$/i', $line)){
				$rtval .= "<h3>{$line}</h3>";
			}else{
				if($line == '/'){
					$rtval .= "<a href='{$line}'>HOME</a><br />";
				}else{
					$part = explode('=', $line);
					if(isset($part[1])){
						$rtval .= "<a href='".$part[1]."'>".$part[1]."</a> : ".$part[0]."<br />";
					}else{
						$rtval .= "<a href='".$part[0]."'>".$part[0]."</a><br />";
					}
				}
			}
		}
		return $rtval;
	}
}
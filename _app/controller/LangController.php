<?php
/**
 * Description of Controller
 *
 * @author YourName <YourEmail@lazy.com>
 */
class LangController extends LZ_Controller{

	/**
	 * 可設定拒絕自動路由執行的Action，若不需要可刪除
	 * e.g. array('Index','Hello'); 或 array('*');全部拒絕
	 * @var array
	 */
	protected  $denyAutoRoute = array();

	/**
	 * 預設執行的Action，若不需要可刪除
	 * @var string
	 */
	protected  $defaultAction = 'Index';

	public function Action_Index(){
		if(isset($_COOKIE['LANG'])){
			app::conf()->changelanguage($_COOKIE['LANG']);
		}
		
		$v['title'] = tt('I18n 國際化測試 ([:lang])',array('lang' => app::conf()->LANGUAGE));
		$this->view()->render($v);
	}

	public function Action_SetLang(){
		setcookie('LANG', $this->v[0], time()+86400*360, '/');
		app::redirect('/lang/');
	}
}
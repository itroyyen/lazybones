<?php
class TestController extends LZ_Controller{

	/**
	 * 索引
	 */
	public function Action_Index(){
		
		t('test','title');

	}

	/**
	 * 自訂帳號驗證處理器
	 */
	public function _validator_rule_my_account($val){
		$pattern = '/^[a-z0-9_\.]{4,20}$/i';
		if(!preg_match($pattern, $val)) return false;
	}

	public function Action_TestValidator(){
		session_start();
		
		$validator = app::validator();
		$name = null;
		$filed = 'file';
		
		$uploadParam = array(
			app::conf()->BASE_PATH.'public/', //檔案上傳存放路徑
			10,//
			10,//高
			50,//
			50,//
			true, //是否必須上傳
			'new_name', //新名稱
			true //若檔案存在是否自動重新命名
		);

		//開始交易模式
		$validator->beginTransaction();

		//$validator->validate($name, 'require|len(3,10)', '姓名');
		$validator->validate(&$filed,array('upload_img_crop' => $uploadParam), '大頭照');
		
		//結束交易模式
		$validator->endTransaction();

		if($validator->hasError()){
			$_SESSION['message'] = $validator->getError();
		}else{
			$_SESSION['message'] = '成功上傳';
		}
		
		session_write_close();
		app::redirect(app::getUrl('test'));
		
	}

	/**
	 * 錯誤訊息頁面
	 */
	public function Action_Error404(){
		echo '這是 [' . get_class($this) . '] ERROR 404 Page!!';
	}

	/**
	 * Say Hello 頁面
	 */
	public function Action_SayHello(){
		echo '這是 [' . get_class($this) . '] SayHello!!';
	}

}
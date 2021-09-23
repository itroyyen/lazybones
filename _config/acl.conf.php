<?php
/**********************************************************************
 * 使用方式
 * 1. 於 AppProject 中的特殊成員 __AppRun__ 中，將作用於全域
 *	  app::acl()->execute(array('AppProject','__authHandler')); // authHandler 名稱可自定
 *
 * 2. 於 Controller中覆蓋建構子，將作用於個別Controller
 *	  app::acl()->execute(array($this,'__authHandler')); // authHandler 名稱可自定
 **********************************************************************/

/**********************************************************************
 * Simple - 只從config檔設定(基本結構)
 **********************************************************************/
// in config
return array(
	'auth' => 'anonymous', //預設的身分，亦可不設定，由authHandler決定
	'default' => 'allow',  //預設允許(allow)或拒絕(deny)  --- (allow亦即全部允許，除非拒絕)
	'deny' => array(       //因此增加deny設定
		'anonymous' => array(// anonymous身分 拒絕 Vip 中的任何動作 及 Forum的Post動作
			'Vip',
			'Forum.post'
		),
		'vip' => array( // vip身分 拒絕 DbTest的Index動作
			'DbTest.Index'
		),
		'baduser' => array( // baduser身分 拒絕所有動作
			'*'
		),
		'member' => array( //member身分 拒絕 Game/Vip/ 中的任何動作
			'Game',
			'Vip'
		)
	),
	'alwaysAllow' => array ( //最高優先權，不論如何一定允許，例如顯示錯誤訊息及登入功能必須一定允許
		'Error',      // 總是允許 Error控制器中的任何動作
		'Auth'        // 總是允許 Auth控制器中的任何動作
	),
	'denyRoute' => array( // 拒絕時引導路由
		'*' => array('Error','VipDeny'),//預設路由，當沒有匹配路由規則時，使用此路由
		'Vip' => app::getUrl('/'),//當被拒絕為控制器Vip下的所有動作時 轉往指定網址 (首頁)
		'Forum.Post' => array('Error','VipDeny'),//當被拒絕為控制器Forum的Post動作時，具體指定控制器動作的優先權高於只有指定控制器
		'Game.StartGame' => 'http://someurl/', // 轉往指定網址
		'Game.TesGame' => app::getUrl('someurl'),// 轉往以網站基礎的網址
		'Test.index' => 'http://www.google.com.tw/',
		'Test.SayHello' => 404 //呈現HTTP 404狀態頁面
	)
);
/**********************************************************************
 * Advance 動態產生權限 - 例如從資料庫中讀取
 **********************************************************************/
// in config
return array(
	'default' => 'allow',
	'deny' => array(
		'anonymous' => array('*')
	),
	'alwaysAllow' => array(
		'Error',
		'Auth'
	),
	'denyRoute' => array(
		'*' => array('Error','VipDeny'),
		'Main' => app::getUrl('someurl'),
		'Main.MyTest' => array('Error','VipDeny'),
		'Game.StartGame' => 'http://someurl/',
		'Game.TesGame' => app::getUrl('someurl'),
		'Test.index' => 'http://www.google.com.tw/',
		'Test.SayHello' => 404
	)
);
//-------------------------------------------------------------------
// authHandler 必須回傳一個如同Simple中結構的陣列
//-------------------------------------------------------------------
//陣列中的權限資料將可由任何方式自行處理，並動態產生，例如：從資料庫讀取
return array(
	'auth' => 'member',
	'deny' => array(
		'member' => array(
			'Game',
			'Test',
			'Main'
		)
	)
);

//如果要針對帳號做個別權限控管，那麼可以回傳這樣的格式
return array(
	'auth' => '使用者帳號',
	'deny' => array(
		'使用者帳號' => array(
			'Game',
			'Test',
			'Main'
		)
	)
);

//或是default為deny
return array(
	'auth' => '使用者帳號',
	'allow' => array(
		'使用者帳號' => array(
			'Game',
			'Test',
			'Main'
		)
	)
);
//或是甚至可以從回傳的陣列中加入新的denyRoute
return array(
	'auth' => '使用者帳號',
	'denyRoute' => array(
		'Game.Start' => app::getUrl('someurl')
	)
);

//若要覆蓋config中的設定，例如重新指定denyRoute，則需指定或
return array(
	'auth' => '使用者帳號',
	'denyRoute' => array (
		'Game.Start' => app::getUrl('someurl')
	),
	'recursive' => true
);
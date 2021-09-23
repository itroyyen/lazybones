<?php
/**************************************************************************
 * 關連設定
 **************************************************************************/
//User 有多個 Post 依據 Post.id_user 關聯
$dbrel['User']['has_many']['Post'] = array('Post' , 'id_user');

//User 有一個 UserInfo 依據 UserInfo.id_user 關聯
$dbrel['User']['has_one']['UserInfo'] = array('UserInfo' , 'id_user');

//UserInfo 有一個 User 依據 UserInfo.id_user 關聯
$dbrel['UserInfo']['has_one']['User'] = array('UserInfo' , 'id_user');

//Post 屬於 User 依據 Post.id_user 關聯
$dbrel['Post']['belongs_to']['User'] =  array('Post' , 'id_user');

//Post 屬於 PostCategory 依據 Post.id_post_category 關聯
$dbrel['Post']['belongs_to']['PostCategory'] =  array('Post' , 'id_post_category');

//PostCategory 有多個 Post 依據 Post.id_post_category 關聯
$dbrel['PostCategory']['has_many']['Post'] = array('Post' , 'id_post_category');


//多對多 使用者與興趣，一個使用者有多個興趣，每個興趣也屬於多個使用者
//User 有多個 Interest 依據 UserHasInterest 資料表 關聯
//User     的主鍵對應 UserHasInterest.id_user
//Interest 的主鍵對應 UserHasInterest.id_interest
$dbrel['User']['many_many']['Interest'] = array(
	'user_has_interest',
	array(
		'User' => 'id_user' ,
		'Interest' => 'id_interest'
	)
);

$dbrel['Interest']['many_many']['User'] = array(
	'user_has_interest',
	array(
		'User' => 'id_user' ,
		'Interest' => 'id_interest'
		)
);

//N_M_Table也可以用Model形式表示，亦即 user_has_interest 與 UserHasInterest 通用


//回傳設定
return $dbrel;
<?php
/**
 * 驗證器內建規則，錯誤訊息樣板
 */
return array(
	'require' => '[:label] 必須輸入值',
	'not_empty' => '[:label] 不能為空值',
	'not_zero' => '[:label] 不能為0',
	'num' => '[:label] 必須為數字',
	'int' => '[:label] 必須為數值',
	'bool' => '[:label] 必須為布林值',
	'float' => '[:label] 必須為浮點數',
	'double' => '[:label] 必須為雙倍精確浮點數',
	'min' => '[:label] 必須大於 [:min]',
	'max' => '[:label] 必須小於 [:max]',
	'range' => '[:label] 必須介於 [:min] 到 [:max] 之間',
	'min_len' => '[:label] 長度必須大於 [:min]',
	'max_len' => '[:label] 長度必須小於 [:max]',
	'len' => '[:label] 長度必須介於 [:min] 到 [:max] 之間',
	'regex' => '[:label] 格式錯誤',
	'alpha' => '[:label] 必須由英文字母組成',
	'alpha_num' => '[:label] 必須由英文字母與數字組成',
	'email' => '[:label] 必須是E-Mail格式',
	'ip' => '[:label] 必須是IP address格式',
	'url' => '[:label] 必須是正確URL格式',
	'account' => '[:label] 必須由英文字母或數字及底線組成',
	'account_lower' => '[:label] 必須由英文字母或數字及底線組成',
	'list' => '[:label] 必須為 [:list] 其中一項',
	'upload' => '[:label] 上傳時發生錯誤訊息：[:message]',
	'upload_img' => '[:label] 上傳時發生錯誤訊息：[:message]',
	'upload_img_crop' => '[:label] 上傳時發生錯誤訊息：[:message]',
	'upload_img_crop2' => '[:label] 上傳時發生錯誤訊息：[:message]'
);
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $v['title'];?></title>
</head>

<body>
<h1><?php echo $v['title'];?></h1>
<h2><?php echo tt('你最愛的水果是什麼？');?></h2>
<ol>
	<li><?php echo tt('蘋果');?></li>
	<li><?php echo tt('香蕉');?></li>
	<li><?php echo tt('鳳梨');?></li>
</ol>
<a href="<?php echo url('lang/set_lang/zh_tw');?>">中文</a> | <a href="<?php echo url('lang/set_lang/en');?>">Englih</a>
</body>
</html>

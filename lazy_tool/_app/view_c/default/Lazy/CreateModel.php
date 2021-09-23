<?php
$_stack_lv_=0;
$_stack_ = array();
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>歡迎使用 <?php echo FRAMEWORK_NAME;?> 懶人工具</title>
<link href="/lazy_tool/css/style.css" rel="stylesheet" type="text/css" />
</head>
<body>

<h1>建立模型</h1>

<a href="<?php echo url('/');?>">懶人工具首頁</a> | <a href="<?php echo url('create_model');?>">建立模型</a>

<ul>
<?php if($v['tables']): ?>
	<?php
$_stack_[$_stack_lv_++] = $v;
if(isset($v['tables'])):
foreach($v['tables'] as $v): ?>
		<li><a href="<?php echo url('create_model/'.$v);?>"><?php echo $v;?></a></li>
	<?php endforeach;
endif;
$v=$_stack_[--$_stack_lv_];
?>

<?php else:?>
<a href="<?php echo url('create_model/'.$v['tbname'].'/save');?>">儲存</a>
<textarea style="width:100%;" rows="20"><?php echo $v['result'];?></textarea>
<?php endif; ?>
</ul>
</body>
</html>
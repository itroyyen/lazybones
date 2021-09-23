<?php
$_stack_lv_=0;
$_stack_ = array();
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>資料驗證器測試</title>
</head>

<body>
<?php if(is_array($v['message'])): ?>
錯誤訊息如下：
<ul>
	<?php
$_stack_[$_stack_lv_++] = $v;
if(isset($v['message'])):
foreach($v['message'] as $v): ?>
	<li><?php echo $v;?></li>
	<?php endforeach;
endif;
$v=$_stack_[--$_stack_lv_];
?>
</ul>
<?php else:?>
<div><?php echo $v['message'];?></div>
<?php endif; ?>

<form action="<?php echo url('test/test_validator');?>" method="post" enctype="multipart/form-data" name="form1" id="form1">
	<label>
		<input type="file" name="file" id="file" />
	</label>
	<input type="submit" name="button" id="button" value="送出" />
</form>
</body>
</html>
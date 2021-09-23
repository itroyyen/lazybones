<?php
$_stack_lv_=0;
$_stack_ = array();
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>無標題文件</title>
</head>
<link href="/css/style.css" rel="stylesheet" type="text/css">
<body>
<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
<p>此頁面是由<?php echo FRAMEWORK_NAME;?>動態產生的預設的展示頁，目前啟用的視圖模式：[<?php echo $GLOBALS['config']['VIEW_MODE'];?>]</p>
<p>此頁面是由<?php echo FRAMEWORK_NAME;?>動態產生的預設的展示頁，目前啟用的視圖模式：[<?php echo app::conf()->VIEW_MODE;?>]</p>
<?php if($v->name == false || $name == 1): ?>
AAA
<?php elseif($v['name'] === false): ?>
BBB
<?php endif; ?>

<?php
$_stack_[$_stack_lv_++] = $v;
if(isset($v['list'])):
$i=0;
foreach($v['list'] as $v):
$i++;
?>
<?php echo $v;?><br />
<?php endforeach;
endif;
$v=$_stack_[--$_stack_lv_];
?>
<br />
<?php
$_stack_[$_stack_lv_++] = $v;
if(isset($v['list'])):
$j=0;
foreach($v['list'] as $v): ?>
<?php echo $v;?><br />
<?php endforeach;
endif;
$v=$_stack_[--$_stack_lv_];
?>
<br />
<?php
$_stack_[$_stack_lv_++] = $v;
if(isset($v['list'])):
$j=0;
foreach($v['list'] as $v):
$j++;
;
$m = $j%2;
?>
<?php echo $m;?><br />
<?php endforeach;
endif;
$v=$_stack_[--$_stack_lv_];
?>
<br />
這是引入的樣板！！<?php echo $v['name'];?>
我的名字是：<?php echo $v['name'];?>
<?php echo $GLOBALS['config']['VIEW_MODE'];?>

<img src="/images/web_icon_0002.gif" width="12" height="12">
<?php echo $v['name'];?>

<?php if($v['error']): ?>
<p>錯誤訊息為：</p>
<ul>
	<?php
$_stack_[$_stack_lv_++] = $v;
if(isset($v['error'])):
foreach($v['error'] as $v): ?>
	<li><?php echo $v;?></li>
	<?php endforeach;
endif;
$v=$_stack_[--$_stack_lv_];
?>
</ul>
<?php endif; ?>

<?php if($v['list']): ?>
<ul>
	<?php
$_stack_[$_stack_lv_++] = $v;
if(isset($v['list'])):
foreach($v['list'] as $v): ?>
	<li><?php echo $v['text'];?>
		<ul>
			<?php
$_stack_[$_stack_lv_++] = $v;
if(isset($v['list'])):
foreach($v['list'] as $v): ?>
				<?php if(($v['name']) == 'Good'): ?>
				<li><b><?php echo ($v['name']);?></b></li>
				<?php else:?>
				<li><?php echo $v['name'];?></li>
				<?php endif; ?>
			<?php endforeach;
endif;
$v=$_stack_[--$_stack_lv_];
?>
		</ul>
	</li>
	<?php endforeach;
endif;
$v=$_stack_[--$_stack_lv_];
?>
</ul>
<?php endif; ?>

</body>
</html>

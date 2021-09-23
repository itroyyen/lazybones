<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>無標題文件</title>
</head>
<link href="../../../css/style.css" rel="stylesheet" type="text/css">
<body>
<script type="text/javascript" src="../../../js/jquery-1.3.2.min.js"></script>
<p>此頁面是由{FRAMEWORK_NAME}動態產生的預設的展示頁，目前啟用的視圖模式：[{$GLOBALS:config:VIEW_MODE}]</p>
<p>此頁面是由{FRAMEWORK_NAME}動態產生的預設的展示頁，目前啟用的視圖模式：[{app@conf().VIEW_MODE}]</p>
<!-- #if(.name == false || $name == 1) -->
AAA
<!-- #elseif(:name === false) -->
BBB
<!-- #endif -->

<!-- #begin(:list){$i=0}{$i++} -->
{$v}<br />
<!-- #end -->
<br />
<!-- #begin(:list){$j=0} -->
{$v}<br />
<!-- #end -->
<br />
<!-- #begin(:list){$j=0}{$j++;$m = $j%2} -->
{$m}<br />
<!-- #end -->
<br />
<!-- #include(test) -->
我的名字是：{:name}
{$GLOBALS:config:VIEW_MODE}
<!-- #begin_hide -->
<div>
ANNNNNNNNNNNNN
</div>
<!-- #end_hide -->
<img src="../../../images/web_icon_0002.gif" width="12" height="12">
{:name}

<!-- #if(:error) -->
<p>錯誤訊息為：</p>
<ul>
	<!-- #begin(:error) -->
	<li>{$v}</li>
	<!-- #end -->
</ul>
<!-- #endif -->

<!-- #if(:list) -->
<ul>
	<!-- #begin(:list) -->
	<li>{:text}
		<ul>
			<!-- #begin(:list) -->
				<!-- #if((:name) == 'Good') -->
				<li><b>{(:name)}</b></li>
				<!-- #else -->
				<li>{:name}</li>
				<!-- #endif -->
			<!-- #end -->
		</ul>
	</li>
	<!-- #end -->
</ul>
<!-- #endif -->

</body>
</html>

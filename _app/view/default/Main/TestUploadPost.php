<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>測試上傳檔案 - 接受上傳</title>
</head>

<body>
<!-- #if(!:error) -->
<div>上傳成功</div>
上傳的檔案為：
{:filename}<br />
<!-- #else -->
<div>上傳失敗</div>

<ul>
	<!-- #begin(:error) -->
	<li>{$v}</li>
	<!-- #end -->
</ul>
<!-- #endif -->



</body>
</html>

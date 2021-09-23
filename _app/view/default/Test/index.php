<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>資料驗證器測試</title>
</head>

<body>
<!-- #if(is_array(:message)) -->
錯誤訊息如下：
<ul>
	<!-- #begin(:message) -->
	<li>{$v}</li>
	<!-- #end -->
</ul>
<!-- #else -->
<div>{:message}</div>
<!-- #endif -->

<form action="{url('test/test_validator')}" method="post" enctype="multipart/form-data" name="form1" id="form1">
	<label>
		<input type="file" name="file" id="file" />
	</label>
	<input type="submit" name="button" id="button" value="送出" />
</form>
</body>
</html>
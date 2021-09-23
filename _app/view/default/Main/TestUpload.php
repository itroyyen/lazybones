<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>測試檔案上傳</title>
</head>

<body>

<p>選擇上傳檔案：</p>
<form id="form1" name="form1" enctype="multipart/form-data" method="post" action="{{url('main/test_upload_post')}}">
				<label>
								<input type="file" name="file" id="file" />
				</label>
				<label>
								<input type="submit" name="button" id="button" value="上傳" />
				</label>
</form>
<p>&nbsp;</p>
</body>
</html>

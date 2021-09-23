<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>歡迎使用 <?php echo FRAMEWORK_NAME;?></title>

<style type="text/css">
body,h1{
	font-family: Verdana, Geneva, sans-serif
}
body {
	background-color: #fff;
	margin: 40px;
	font-size: 14px;
	color: #515257;
}

a {
	color: #069;
}

a:hover {
	color: #F73E00;
}

h1 {
	font-size: 24px;
	color: #555;
	background-color: #F2F2F2;
	padding: 10px;
	border: 1px solid #D6D6D6;
}

code {
	font-family: "Courier New", Courier, monospace;
	font-size: 12px;
	background-color: #F5F5F5;
	border: 1px dashed #CCC;
	color: #036;
	display: block;
	padding: 10px;
}
</style>

</head>
<body>
<h1>歡迎使用 <?php echo FRAMEWORK_NAME;?>&nbsp;<?php echo FRAMEWORK_VERSION;?></h1>
<p>此頁面是由<?php echo FRAMEWORK_NAME;?>動態產生的預設的展示頁，目前啟用的視圖模式：[<?php echo $GLOBALS['config']['VIEW_MODE'];?>]</p>



<p>目前已實現的特色有：</p>
<ul>
	<li>核心迷你，執行效率高</li>
	<li>操作簡便，不需複雜設定組態檔</li>
	<li>MVC structure</li>
	<li>Scalability
		<ul>
			<li>Helper / Library / Application / Config</li>
			<li>可擴充支援任何資料庫 (目前為MySQL)</li>
			<li>可擴充並使用第三方樣板引擎</li>
			<li>驗證器可擴充驗證規則</li>
		</ul>
	</li>
	<li>驗證器具備修飾器功能，並可於發生驗證失敗時進行修飾資料復原</li>
	<li>可自由選擇原生PHP視圖(Basic模式)、方便的內建樣板引擎視圖(Magic模式)，或自行擴充視圖模式並使用任何樣板引擎</li>
	<li>ACL (Access Control List)</li>
	<li>Database ORM</li>
	<li>Model fluent-interface</li>
	<li>Event<br />可透過事件加強核心功能或創造新功能，例如：
		<ul>
			<li>自製更強大的ACL</li>
			<li>自製的Helper可擁有自己的事件</li>
			<li>擴充視圖Magic模式的功能</li>
			<li>加強個別應用程式的彈性</li>
		</ul>
	</li>
	<li>URL Router (Custom / Auto)</li>
	<li>Data Validator (Custom  / Model) </li>
	<li>I18n Internationalization</li>
	<li>Theme support</li>
</ul>

<h3>high-performance</h3>
<p>核心迷你，執行效率高</p>
<h3>MVC structure</h3>
<p>核心迷你，執行效率高</p>


<p>您可以從以下路徑找到視圖檔，並試著編輯這個頁面</p>
<code>/main/my_test/</code>

<p>或者您可以從以下路徑找到控制器檔案</p>
<code>/main/my_test/</code>

</body>
</html>
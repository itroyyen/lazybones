<html>
<head>
<title>歡迎使用 {FRAMEWORK_NAME}</title>

<style type="text/css">

body {
 background-color: #fff;
 margin: 40px;
 font-family: Lucida Grande, Verdana, Sans-serif;
 font-size: 14px;
 color: #4F5155;
}

a {
 color: #003399;
 background-color: transparent;
 font-weight: normal;
}

h1 {
 color: #444;
 background-color: transparent;
 border-bottom: 1px solid #D0D0D0;
 font-size: 16px;
 font-weight: bold;
 margin: 24px 0 2px 0;
 padding: 5px 0 6px 0;
}

code {
 font-family: Monaco, Verdana, Sans-serif;
 font-size: 12px;
 background-color: #f9f9f9;
 border: 1px solid #D0D0D0;
 color: #002166;
 display: block;
 margin: 14px 0 14px 0;
 padding: 12px 10px 12px 10px;
}

</style>
<script type="text/javascript" src="../../../js/jquery-1.3.2.min.js"></script>
<link href="../../../css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<!-- #include(test) -->
{asdf}
{$GLOBALS:config:VIEW_MODE}
<!-- #begin_hide -->
<div>
ANNNNNNNNNNNNN
</div>
<!-- #end_hide -->
<img src="../../../images/web_icon_0002.gif" width="12" height="12">

<h1>歡迎使用 {FRAMEWORK_NAME}{FRAMEWORK_NAME}&nbsp;{FRAMEWORK_VERSION} -- {trim("some \"text\"...中文字")} 
</h1>
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
我的名字是：{:name}
<p>此頁面是由{FRAMEWORK_NAME}動態產生的預設的展示頁</p>
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
			<li>可擴充資料驗證器規則</li>
		</ul>
	</li>
	<li>可自由選擇原生PHP視圖(Basic模式)、方便的內建樣板引擎視圖(Magic模式)，或自行擴充並使用任何樣板引擎</li>
	<li>ACL (Access Control List)</li>
	<li>Database ORM</li>
	<li>Model fluent-interface</li>
	<li>Event 可透過事件的加強核心功能或創造新功能，例如：
		<ul>
			<li>自製更強大的ACL</li>
			<li>自製的Helper可擁有自己的事件</li>
			<li>擴充視圖Magic模式的功能</li>
			<li>加強個別應用程式的彈性</li>
		</ul>
	</li>
	<li>URL Router (Custom / Auto)</li>
	<li>Data Validator (Form / Model) </li>
</ul>


<p>您可以從以下路徑找到視圖檔，並試著編輯這個頁面</p>

<code>{url('/main/my_test/')}</code>

<p>或者您可以從以下路徑找到控制器檔案</p>
<code>{url('/main/my_test/')}</code>

</body>
</html>
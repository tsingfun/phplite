<?php
define('MONITOR_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
include MONITOR_PATH.'/global/base.php';

$req_json = $_REQUEST['req_json']; //POST GET都支持
if (!empty($req_json)) {
	//请求
	// UTF8 -> GBK
	$req_json_gbk = mult_iconv("UTF-8", "GBK", $req_json);
	
	$rsp_json = monitor_base::request_server(array("req_json" => $req_json_gbk));
	$rsp_json = mult_iconv("GBK", "UTF-8", $rsp_json);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Wind 交易监控系统1.0 _ JSON请求测试页面</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link type="text/css" rel="stylesheet" href="/statics/css/global.css?<?php echo time();?>"/>
	<style type="text/css">
		div.main { padding:20px; }
		div.title {padding:5px 0; font-weight:bold; color:#FF5300; font-size:15px; font-family: Ubuntu,Arial,sans-serif;}
		textarea{ border: solid 1px #555;border-radius: 2px; margin: 0;padding: 5px;
    }
	</style>
	<script type="text/javascript">
		function test_json() 
		{
			document.getElementById('req_json').value='{"MsgCode":1}';
		}
	</script>
</head>

<body>
<div class="main">
	<form action="" action="post">
		<div class="title">请求JSON <a style="margin-left:8px;">(<?php echo monitor_base::load_config('monitor_server_uri')?>)</a></div>
		<textarea rows="16" cols="150" name="req_json" id="req_json"><?php echo $req_json; ?></textarea>
		<br/><br/>
		<input type="submit" value="请求"/>
		
		<div style="display:inline; position: absolute;margin-top: 8px;">
			<a style="margin-left: 20px; " href="/json_test.php">清空</a>
			<a style="margin-left: 20px; cursor:pointer;" onclick="javascript:test_json();">测试JSON</a>
		</div>
	</form>
	<br/><br/>
	<div class="title">响应JSON</div>
	<textarea rows="16" cols="150"><?php echo $rsp_json; ?></textarea>
</div>
</body>
</html>

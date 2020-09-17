<?php
/**
 *  monitor.php 实时监控
 *
 * @copyright			(C) 2017 Wind Information
 * @author				qpzhou
 * @lastmodify			2017-9-15
 */

define('MONITOR_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR);

include MONITOR_PATH.'/global/base.php';

$req_json = $_REQUEST['req_json']; //POST GET都支持
if (empty($req_json)) {
	echo '{"ErrDesc":"JSON is empty!"}';
	exit;
}

$req_data = json2array($req_json);
$msgcode = $req_data['MsgCode'];
if (empty($msgcode)) {
	echo '{"ErrDesc":"MsgCode is empty!"}';
	exit;
}

// 请求记录文件
$fp = fopen(MONITOR_PATH.'/mock/json/req/'.$msgcode.'.json', "w");
fwrite($fp, $req_json);
fclose($fp);

// 读本地JSON文件，mock返回结果
$rsp_json_file = MONITOR_PATH.'/mock/json/rsp/'.$msgcode.'.json';
$fp = @fopen($rsp_json_file, "r") or showmessage("请求响应失败！[MsgCode:".$msgcode."]");
$rsp_json = fread($fp, filesize($rsp_json_file));
fclose($fp);

// GBK -> UTF8 此处不转码，真正server数据是GBK的
//$rsp_json = mult_iconv("GBK", "UTF-8", $rsp_json);

echo $rsp_json;

?>
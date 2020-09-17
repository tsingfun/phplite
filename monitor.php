<?php
/**
 *  monitor.php 实时监控
 *
 * @copyright			(C) 2017 Wind Information
 * @author				qpzhou
 * @lastmodify			2017-9-15
 */

define('MONITOR_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);

include MONITOR_PATH.'/global/base.php';

$mon = new monitor;
if(!isset($_GET['method'])) {
	$mon->init(); //初始页面
	exit;
}
else {
	if (method_exists($mon, $_GET['method']))
		$mon->$_GET['method']();
	else
		showmessage("不支持的请求！");
}

class monitor {
	
	public function init() {
		//请求Monitor.Server获取监控列表数据
		$data_ew = monitor_base::req_func(MSG_CODE_SEARCH_ERR_WARN);
		$data_notice = monitor_base::req_func(MSG_CODE_SEARCH_NOTICE);
		
		include template('monitor');
	}
	
	// 数量
	public function ewnum() {

		$ew_num_json = monitor_base::req_func(MSG_CODE_SEARCH_ERR_WARN_NUM, null, false);
		echo $ew_num_json;
	}
	
	// 详情
	public function ewdetail() {
		
		$req_data['IP'] = $_GET['IP'];
		$req_data['AppName'] = $_GET['AppName'];
		$req_data['AlarmType'] = intval($_GET['AlarmType']);
		$req_data['Template'] = intval($_GET['Template']);
		
		$data_ewdetail = monitor_base::req_func(MSG_CODE_SEARCH_ERR_WARN_DETAIL, $req_data);
		
		include template('monitor_ewdetail');
	}
	
	// 消警确认界面
	public function ewcomfirm() {
		include template('monitor_ewcomfirm');
	}
	
	// 消警
	public function ewclear() {
	
		$req_data['IP'] = $_GET['IP'];
		$req_data['AppName'] = $_GET['AppName'];
		$req_data['AlarmType'] = intval($_GET['AlarmType']);
		$req_data['Template'] = intval($_GET['Template']);
		$req_data['Remark'] = $_GET['Remark'];
		$req_data['UserId'] = get_cookie('UserId'); //操作者
		
		$ret_json = monitor_base::req_func(MSG_CODE_CLEAR_ERR_WARN, $req_data, false);
		echo $ret_json;
	}
}

?>
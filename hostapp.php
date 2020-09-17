<?php
/**
 *  hostapp.php 主机应用管理
 *
 * @copyright			(C) 2017 Wind Information
 * @author				qpzhou
 * @lastmodify			2017-9-18
 */

define('MONITOR_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);

include MONITOR_PATH.'/global/base.php';

$ha = new hostapp;
if(!isset($_GET['method'])) {
	$ha->init(); //初始页面
	exit;
}
else {
	if (method_exists($ha, $_GET['method']))
		$ha->$_GET['method']();
	else
		showmessage("不支持的请求！");
}

class hostapp {
	//主机应用列表 - 主机列表
	public function init() {

		$data_host = monitor_base::req_func(MSG_CODE_SEARCH_HOST_LIST);
		
		include template('host_list');
	}
	
	//主机应用列表 - 应用列表
	public function hostapplist() {
		
		$IP = $_GET['IP'];
		$req_data['HostIP'] = $IP;
		
		if (!empty($IP))		
			$data_app = monitor_base::req_func(MSG_CODE_SEARCH_HOST_APP_LIST, $req_data);
		
		include template('host_app_list');
	}
	
	//主机应用列表 - 启动、停止...
	public function appoper() {
		$req_data['OperType'] = $_GET['OperType'];
		$req_data['HostIP'] = $_GET['IP'];
		$req_data['AppName'] = $_GET['AppName'];		
		
		$ret_json = monitor_base::req_func(MSG_CODE_SEARCH_HOST_APP_OPER, $req_data, false);
		echo $ret_json;
	}
	
	// ------------------------------------------------
	// 主机管理 - 列表
	public function host() {
		
		$data_host = monitor_base::req_func(MSG_CODE_SEARCH_HOST_MANAGELIST);
		
		include template('host');
	}
	
	//主机管理 - 弹窗页
	public function hostoper() {
		$oper = $_GET['oper']; //add or mod
		include template('host_oper');
	}
	//主机管理 - 增删改
	public function hostoperapply() {
		$req_data['OperType'] = $_GET['OperType'];//Add Mod Del
		$req_data['HostIP'] = $_GET['HostIP'];
		$req_data['HostName'] = $_GET['HostName'];
		$req_data['HostRemark'] = $_GET['HostRemark'];
		$req_data['UserId'] = get_cookie('UserId');//操作者
		
		$ret_json = monitor_base::req_func(MSG_CODE_HOST_MANAGE_APPLY, $req_data, false);
		echo $ret_json;
	}
	
	// ------------------------------------------------
	//应用管理 - 列表
	public function app() {
		$CurAppTypeName = $_POST['AppName'];
		$data_app_typelist = monitor_base::req_func(MSG_CODE_SEARCH_APP_TYPENAMELIST);
		
		// 初始化时，默认先加载第一个应用类型
		if ($CurAppTypeName == null)
			$CurAppTypeName = $data_app_typelist['AppTypeList'][0];
		
		// 请求应用列表
		$req_data['AppName'] = $CurAppTypeName;
		$data_app = monitor_base::req_func(MSG_CODE_SEARCH_APP_MANAGELIST, $req_data);
		
		// 加载组别列表数据
		$req_data['AppName'] = $CurAppTypeName;
		$data_appgroup = monitor_base::req_func(MSG_CODE_APP_GROUP_GET, $req_data);
		
		include template('app');
	}
	//应用管理 - 组别管理弹窗页
	public function app_group_manage() {
		$oper = $_GET['oper'];
		$AppName = $_GET['AppName'];
		
		// 加载组别列表数据
		$req_data['AppName'] = $AppName;
		$data_appgroup = monitor_base::req_func(MSG_CODE_APP_GROUP_GET, $req_data);
		
		include template('app_group_manage');
	}
	//应用管理 - 组别管理应用
	public function app_group_apply() {
		$req_data['OperType'] = $_GET['OperType'];//Add Del
		$req_data['AppName'] = $_GET['AppName'];
		$req_data['AppGroup'] = $_GET['AppGroup'];
		
		$ret_json = monitor_base::req_func(MSG_CODE_APP_GROUP_MANAGE, $req_data, false);
		echo $ret_json;
	}
	//应用管理 - 设置组别
	public function app_group_set() {
		$req_data['HostIP'] = $_GET['HostIP'];
		$req_data['AppName'] = $_GET['AppName'];
		$req_data['AppGroup'] = $_GET['AppGroup'];
		
		$ret_json = monitor_base::req_func(MSG_CODE_SET_APP_2_GROUP, $req_data, false);
		echo $ret_json;
	}
	
	// ------------------------------------------------
	public function pb() {
		$CurHostIP = $_POST['HostIP'];
		
		// 请求应用IP列表
		$req_data['AppName'] = 'PB.Manager';
		$data_apptype_hostlist = monitor_base::req_func(MSG_CODE_SEARCH_APP_MANAGELIST, $req_data);
		
		// 初始化时，默认先加载第一个IP
		if ($CurHostIP == null)
			$CurHostIP = $data_apptype_hostlist['AppData'][0]['HostIP'];
		
		// 请求PB监控信息
		$req_data2['HostIP'] = $CurHostIP;
		$req_data2['AppName'] = 'PB.Manager';
		$data_pb = monitor_base::req_func(MSG_CODE_MONITOR_PB, $req_data2);
		
		include template('pb');
	}
	
	// ------------------------------------------------
	public function ems() {		
		//获取EMS Manager对象
		$req_data['Type'] = 10;
		$data_manager = monitor_base::req_func(MSG_CODE_MONITOR_EMS, $req_data);
		
		//获取EMS引擎列表
		$req_data['Type'] = 3;
		$data_enginelist = monitor_base::req_func(MSG_CODE_MONITOR_EMS, $req_data);
		
		if (is_string($data_enginelist['Result']))
			showmessage($data_enginelist['Result']);
		
		include template('ems');
	}
	
	public function ems_mgr_stop() {
		//启动/停止Manager
		$req_data['Type'] = intval($_GET['Type']);
	
		$ret_json = monitor_base::req_func(MSG_CODE_MONITOR_EMS, $req_data, false);
		echo $ret_json;
	}
	
	public function ems_inst() {
		$EngineID = intval($_REQUEST['EngineID']);
		$AlgoOrderID = $_REQUEST['AlgoOrderID'];
		//获取引擎算法单列表
		$req_data['Type'] = 4;
		$req_data['EngineID'] = $EngineID;
		if (!empty($AlgoOrderID))
			$req_data['AlgoOrderID'] = $AlgoOrderID;
		$data_instlist = monitor_base::req_func(MSG_CODE_MONITOR_EMS, $req_data);
		
		include template('ems_inst');
	}
	
	public function ems_inst_stop() {
		//停止引擎算法单
		$req_data['Type'] = 5;
		$req_data['EngineID'] = intval($_GET['EngineID']);
		$req_data['AlgoOrderID'] = $_GET['AlgoOrderID'];
		
		$ret_json = monitor_base::req_func(MSG_CODE_MONITOR_EMS, $req_data, false);
		echo $ret_json;
	}
	
	public function ems_quo() {
		$EngineID = intval($_REQUEST['EngineID']);
		//获取行情信息
		$req_data['Type'] = 7;
		$req_data['EngineID'] = $EngineID;
		
		$MarketId = $_REQUEST['MarketId'];		
		if (!empty($MarketId) && $MarketId != '==All==')
			$req_data['MarketId'] = $MarketId;
		
		$Code = $_REQUEST['Code'];
		if (!empty($Code))
			$req_data['Code'] = $Code;
		$data_quolist = monitor_base::req_func(MSG_CODE_MONITOR_EMS, $req_data);
		
		//获取市场时间
		$req_data['Type'] = 6;
		$req_data['EngineID'] = intval($_REQUEST['EngineID']);
		$data_markettimelist = monitor_base::req_func(MSG_CODE_MONITOR_EMS, $req_data);
		
		//获取行情源信息
		$req_data['Type'] = 11;
		$req_data['EngineID'] = intval($_REQUEST['EngineID']);
		$data_quosrclist = monitor_base::req_func(MSG_CODE_MONITOR_EMS, $req_data);
		
		include template('ems_quo');
	}
	
	public function ems_error() {
		//获取错误报告
		$req_data['Type'] = 8;
		$EngineID = intval($_GET['EngineID']);
		$req_data['EngineID'] = $EngineID;
		$data_reportlist = monitor_base::req_func(MSG_CODE_MONITOR_EMS, $req_data);
		
		include template('ems_error');
	}
	
	public function ems_error_detail() {
		//获取错误详细日志
		$req_data['Type'] = 9;
		$req_data['EngineID'] = intval($_GET['EngineID']);
		$req_data['AlarmType'] = intval($_GET['AlarmType']);
		$data_errdetaillist = monitor_base::req_func(MSG_CODE_MONITOR_EMS, $req_data);
		
		include template('ems_error_detail');
	}
}


// 市场状态字符串
function get_market_status($char) {
	switch ($char) {
		case 0:		return "开市";
		case 1:		return "闭市";
		default:	return "未知状态";
	}
}

// 算法单市场状态字符串
function get_ems_order_status($char) {
	switch ($char) {
		//case 'U':   return "UNREPORTED";//UNREPORTED
		//case 'R':   return "REPORTING";//REPORTING
		case 'N':	return "初始化";		//INIT
		case 'G':	return "初始化中";	//INITIALIZING
		case 'I':	return "已初始化";	//INITED
		case 'S':	return "启动中";		//STARTING
		case 'W':	return "执行中";		//WORKING
		case 'A':	return "暂停中";		//PAUSING
		case 'P':	return "已暂停";		//PAUSED
		case 'M':	return "恢复中";		//RUSUMING
		case 'T':	return "停止中";		//STOPING
		case 'C':	return "已取消";		//CANCELED
		case 'J':	return "已拒绝";		//REJECTED
		case 'F':	return "失败";		//FAILED
		case 'O':	return "成功";		//OK
		default:	return "未知状态";
	}
}

// 行情的市场状态字符串
function get_quo_status($char) {
	switch($char)
	{
		case '0' : return "[0]首日上市";
		case '1' : return "[1]增发新股";
		case '2' : return "[2]上网定价发行";
		case '3' : return "[3]上网竞价发行";
		case 'A' : return "[A]交易节休市";
		case 'B' : return "[B]整天停牌";
		case 'C' : return "[C]全天收市";
		case 'D' : return "[D]暂停交易";
		case 'd' : return "[d]集合竞价阶段结束到连续竞价阶段开始之前的时段";
		case 'E' : return "[E]启动交易盘";
		case 'F' : return "[F]盘前处理";
		case 'G' : return "[G]不可恢复交易的熔断阶段（上交所的N）";
		case 'H' : return "[H]放假";
		case 'I' : return "[I]开市集合竞价";
		case 'J' : return "[J]盘中集合竞价";
		case 'K' : return "[K]开市订单簿平衡前期";
		case 'L' : return "[L]盘中订单簿平衡前期";
		case 'M' : return "[M]开市订单簿平衡";
		case 'N' : return "[N]盘中订单簿平衡";
		case 'O' : return "[O]连续撮合";
		case 'P' : return "[P]休市";
		case 'Q' : return "[Q]波动性中断";
		case 'q' : return "[q]可恢复交易的熔断时段(上交所的M)";
		case 'R' : return "[R]交易间";
		case 'S' : return "[S]非交易服务支持";
		case 'T' : return "[T]固定价格集合竞价";
		case 'U' : return "[U]盘后处理";
		case 'V' : return "[V]结束交易";
		case 'W' : return "[W]暂停";
		case 'X' : return "[X]停牌";
		case 'Y' : return "[Y]新增产品";
		case 'Z' : return "[Z]可删除的产品";
		case 'v' : return "[v]市场波动调节机制(港交所）";
		default : return "未知状态";
	}
}

?>
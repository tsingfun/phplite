<?php
/**
 *  paramset.php 参数设置
 *
 * @copyright			(C) 2017 Wind Information
 * @author				qpzhou
 * @lastmodify			2017-9-18
 */

define('MONITOR_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);

include MONITOR_PATH.'/global/base.php';

$ps = new paramset;
if(!isset($_GET['method'])) {
	$ps->init(); //初始页面
	exit;
}
else {
	if (method_exists($ps, $_GET['method']))
		$ps->$_GET['method']();
	else
		showmessage("不支持的请求！");
}

class paramset {
	
	public function init() {
		self::load_appgroup_param($_GET['AppName']);
	}
	
	public function req_group_param(){
		self::load_appgroup_param($_POST['AppName'], $_POST['AppGroup']);
	}
	
	public function req_param_by_ip(){
		self::load_appgroup_param($_GET['AppName'], $_GET['AppGroup'], $_GET['HostIP']);
	}
	
	// 参数数据的请求JSON
	private function paramdata_req_json($AppName, $AppGroup, $type, $HostIP){
		$json = '';
		
		if ($type == 'yw') { //请求全部运维相关数据
			$json .='{"AppName":"'.$AppName.'", 
					  "AppGroup":"'.$AppGroup.'",
					  "HostIP":"'.$HostIP.'",
					  "WtsStock.ini":{
							"OEM":{"ID":null,"ERRINFO":null},
							"MD5":{"ENABLE":null,"ERRINFO":null},
							"宏汇处理方式":{"账号权限管理":null},
							"EMS":null,
							"ETF过滤":null
					   },
					  "ModulesMd5.json":null,
					  "AccountAuthority.json":null
					 }';
		}
		else if ($type == 'yy') { //请求全部运营相关数据
			$json .='{"AppName":"'.$AppName.'", 
					  "AppGroup":"'.$AppGroup.'",
					  "HostIP":"'.$HostIP.'",
					  "WtsStock.ini":{
							"宏汇处理方式":{"黑白名单限制模式":null,"黑白名单匹配模式":null},"ETF过滤":null
					   },
					  "BlackList.txt":null,
					  "WhiteList.txt":null
					}';
		}
		else if ($type == 'emsmgr') { //请求全部EMS.Manager相关数据
			$json .='{"AppName":"'.$AppName.'",
					  "AppGroup":"'.$AppGroup.'",
					  "HostIP":"'.$HostIP.'",
					  "ems_manager.ini":{
							"services":{"backuptime":null, "exittime":null}
					   }
					}';
		}
		else if ($type == 'emseng') { //请求全部EMS.Engine相关数据
			$json .='{"AppName":"'.$AppName.'",
					  "AppGroup":"'.$AppGroup.'",
					  "HostIP":"'.$HostIP.'",
					  "ems_engine.ini":{
							"SYS":{"exittime":null},
					  		"TDFAPI":{"SrcSeq":null}';
			
			//行情源key，采取两次请求的方式
			$sections = explode(";", self::TDFAPI_SrcSeq($AppName, $AppGroup, $type, $HostIP));
			foreach($sections as $sec){
				if (!empty($sec))
					$json .= ',"'. $sec . '":null';
			}
			
			$json .= '}}';
		}
		else if ($type == 'pbquo') { //请求全部EMS.Quotation相关数据
			$json .='{"AppName":"'.$AppName.'",
					  "AppGroup":"'.$AppGroup.'",
					  "HostIP":"'.$HostIP.'",
					  "PB_Quotation.ini":{
					  		"TDFAPI":{"SrcSeq":null}';
			
			//行情源key，采取两次请求的方式
			$sections = explode(";", self::TDFAPI_SrcSeq($AppName, $AppGroup, $type, $HostIP));
			foreach($sections as $sec){
				if (!empty($sec))
					$json .= ',"'. $sec . '":null';
			}
			
			$json .= '}}';
		}
		
		return $json;
	}
	
	//请求行情源TDFAPI SrcSeq值
	private function TDFAPI_SrcSeq($AppName, $AppGroup, $type, $HostIP) {
		if ($type == 'emseng')
			$iniName = 'ems_engine.ini';
		else if ($type == 'pbquo')
			$iniName = 'PB_Quotation.ini';
		
		//请求TDFAPI SrcSeq值
		$json ='{"AppName":"'.$AppName.'",
				  "AppGroup":"'.$AppGroup.'",
				  "HostIP":"'.$HostIP.'",
				  "' . $iniName . '":{
				  		"TDFAPI":{"SrcSeq":null}
				  	}
				  }';
		
		$data_param = monitor_base::req_json_func(MSG_CODE_SETTINGS_GET, $json);
		// 提取TDFAPI SrcSeq值
		if (isset($data_param[$iniName]['TDFAPI']['SrcSeq']))
			return $data_param[$iniName]['TDFAPI']['SrcSeq'];
	}
	
	//加载应用组别参数
	private function load_appgroup_param($AppName, $CurAppGroup = null, $HostIP = null){
		
		$type = $_GET['type']; //运维:yw or 运营:yy
		
		// 加载组别列表数据
		$req_data['AppName'] = $AppName;
		$data_appgroup = monitor_base::req_func(MSG_CODE_APP_GROUP_GET, $req_data);
		
		// 初始化时，默认先加载第一个组别的参数
		if ($CurAppGroup == null)
			$CurAppGroup = $data_appgroup['AppGroupList'][0];
		
		//请求应用类型下的IP列表
		$req_data_iplist['AppName'] = $AppName;
		$data_iplist = monitor_base::req_func(MSG_CODE_SEARCH_APP_MANAGELIST, $req_data_iplist);
		
		//组装请求json，请求参数
		$req_json = self::paramdata_req_json($AppName, $CurAppGroup, $type, $HostIP);
		$data_param = monitor_base::req_json_func(MSG_CODE_SETTINGS_GET, $req_json);
		
		//-----------------特殊处理：ETF列表、黑白名单---------------------
		$data_param['ETFList'] = array();
		for ($i = 0; $i < intval($data_param['WtsStock.ini']['ETF过滤']['ETF数量']); $i++){
			array_push($data_param['ETFList'], array("market" => self::get_market_name($data_param['WtsStock.ini']['ETF过滤']['市场'.$i]),
					"code" => $data_param['WtsStock.ini']['ETF过滤']['代码'.$i])
			);
		}
		
		$data_param['BlackList'] = array();
		$data_param['WhiteList'] = array();
		
		$arr_blacklist = explode("\r\n", $data_param['BlackList.txt']);
		$arr_whitelist = explode("\r\n", $data_param['WhiteList.txt']);
		
		foreach($arr_blacklist as $key){
			$arr_key = explode("\n", $key);
			foreach($arr_key as $key2){
				if (empty($key2) || substr($key2, 0, 1) == ';')	//注释行,跳过
					continue;
				array_push($data_param['BlackList'], $key2);
			}
		}
		foreach($arr_whitelist as $key){
			$arr_key = explode("\n", $key);
			foreach($arr_key as $key2){
				if (empty($key2) || substr($key2, 0, 1) == ';')	//注释行,跳过
					continue;
				array_push($data_param['WhiteList'], $key2);
			}
		}
		// --------------------------------------------------------------
		
		//----------------------特殊处理：行情源--------------------------
		$data_param['QuoList'] = array();
		foreach ($data_param as $key => $value){
			
			if (isset($value['TDFAPI'])){ //行情源
				$arr_quosrc = explode(";", $value['TDFAPI']['SrcSeq']);
				
				foreach ($arr_quosrc as $quosrc) {
					$server_num = $value[$quosrc]['ServerNum'];
					
					for ($i = 0; $i < $server_num; $i++){
						array_push($data_param['QuoList'], array("QuoSrc" => $quosrc,
								"IP" => $value[$quosrc]['IP'.$i],
								"Port" => $value[$quosrc]['Port'.$i],
								"User" => $value[$quosrc]['User'.$i],
								"Pwd" => $value[$quosrc]['Password'.$i],
								"MarketFilter" => $value[$quosrc]['MarketFilter'])
						);
					}
				}
			}
		}
		// --------------------------------------------------------------
		
		include template('paramset');
	}
	
	// 根据市场代码获取市场名称
	private function get_market_name($market_code){
		switch ($market_code){
			case '0':
				return '深圳';
			case '1':
				return '上海';
		}
		return '';
	}
	
	// 权限设置
	public function trade() {
		
		$oper = $_GET['oper']; //add or mod
		$tableid = $_GET['tableid'];
		include template('paramset_trade');
	}
	
	// OEM设置
	public function oemmd5() {
		
		$oper = $_GET['oper']; //add or mod
		$tableid = $_GET['tableid'];
		include template('paramset_oemmd5');
	}
	
	// ETF代码设置
	public function etf() {
	
		$oper = $_GET['oper']; //add or mod
		$tableid = $_GET['tableid'];
		include template('paramset_etf');
	}
	
	// 黑白名单设置
	public function bwlist() {
	
		$oper = $_GET['oper']; //add or mod
		$tableid = $_GET['tableid'];
		include template('paramset_bwlist');
	}
	
	// 行情源配置
	public function quosrc() {
	
		$oper = $_GET['oper']; //add or mod
		$tableid = $_GET['tableid'];
		include template('paramset_quosrc');
	}
	
	// 提交设置
	public function commit() {
		
		$ret_json = monitor_base::req_json_func(MSG_CODE_SETTINGS_SET, $_POST['json'], false);
		echo $ret_json;
	}
}

?>
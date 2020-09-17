<?php
/**
 *  settings.php 系统设置
 *
 * @copyright			(C) 2017 Wind Information
 * @author				qpzhou
 * @lastmodify			2017-9-18
 */

define('MONITOR_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);

include MONITOR_PATH.'/global/base.php';

$sett = new settings;
if(!isset($_GET['method'])) {
	$sett->init(); //初始页面
	exit;
}
else {
	if (method_exists($sett, $_GET['method']))
		$sett->$_GET['method']();
	else
		showmessage("不支持的请求！");
}

class settings {
	
	public function init() {

		$data_sett = monitor_base::req_func(MSG_CODE_SYS_SETTINGS_GET);
		
		include template('settings');
	}
	
	public function save() {
	
		$req_data['RefreshPeroid'] = intval($_POST['RefreshPeroid']);
		$req_data['IsSendEmail'] = $_POST['IsSendEmail']=='1' ? 1 : 0;
		$req_data['EmailServerID'] = $_POST['EmailServerID'];
		$req_data['EmailServerPort'] = intval($_POST['EmailServerPort']);
		$req_data['SendFrom'] = $_POST['SendFrom'];
		$req_data['AddrList1'] = $_POST['AddrList1'];
		$req_data['Upgrade2'] = intval($_POST['Upgrade2']);
		$req_data['AddrList2'] = $_POST['AddrList2'];
		$req_data['Upgrade3'] = intval($_POST['Upgrade3']);
		$req_data['AddrList3'] = $_POST['AddrList3'];
		$req_data['UserId'] = get_cookie('UserId'); //操作者
		
		$ret_json = monitor_base::req_func(MSG_CODE_SYS_SETTINGS_SAVE, $req_data, false);
		echo $ret_json;
	}
	
	public function send_test_email() {
		$req_data['SMTPServer'] = $_POST['SMTPServer'];
		$req_data['SMTPPort'] = intval($_POST['SMTPPort']);
		$req_data['Sender'] = $_POST['Sender'];
		$req_data['Reciever'] = $_POST['Reciever'];
		$req_data['Tittle'] = $_POST['Tittle'];
		$req_data['Content'] = $_POST['Content'];
		
		$ret_json = monitor_base::req_func(MSG_CODE_SEND_EMAIL_TEST, $req_data, false);
		echo $ret_json;
	}
}

?>
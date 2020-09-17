<?php
/**
 *  login.php 登录入口、登录逻辑处理
 *
 * @copyright			(C) 2017 Wind Information
 * @author				qpzhou
 * @lastmodify			2017-9-13
 */

define('MONITOR_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);

include MONITOR_PATH.'/global/base.php';

$login = new login;
if(!isset($_GET['method'])) {
	$login->init(); //初始登录页面
	exit;
}
else {
	if (method_exists($login, $_GET['method']))
		$login->$_GET['method']();
	else
		showmessage("不支持的请求！");
}

class login {
	
	public function init() {		
		include template('login');
	}
	
	public function dologin() {
		$req_data['UserId'] = $_POST['UserId'];
		$req_data['password'] = md5($_POST['password']);
		
		//验证码的验证
		session_start();
		$VerifyCode = $_POST['VerifyCode'];		
		if($VerifyCode != $_SESSION['VerifyCode']){
			showmessage('验证码输入有误！');
		}
		
		$ret = monitor_base::req_func(MSG_CODE_LOGIN, $req_data);
		if ($ret['Result'] == '1') {
			
			session_destroy();//将session去掉，以每次都能取新的session值
			
			// 登录成功
			set_cookie("LoginStatus", 'OK');
			// 用户ID
			set_cookie("UserId", $_POST['UserId'], 7*24*60); // 默认记住一周
			// 是否记住用户名
			set_cookie("saveInfo", $_POST['saveInfo'], 7*24*60); // 默认记住一周
			
			// 角色权限数据
			set_cookie("RoleMenuData", $ret['RoleMenuData']);
			
			// 获取系统设置中的刷新时间
			$data_sett = monitor_base::req_func(MSG_CODE_SYS_SETTINGS_GET);
			if (isset($data_sett['RefreshPeroid']))
				$RefreshPeroid = intval($data_sett['RefreshPeroid']);
			else
				$RefreshPeroid = 3;
			set_cookie("RefreshPeroid", $RefreshPeroid);
			
			header("Location: index.php");
		} else {
			if (!isset($ret['RspInfo']))
				showmessage('服务器连接异常，请稍后再试！');
			else 
				showmessage($ret['RspInfo']);
		}
	}
	
	public function logout() {
		if (get_cookie('saveInfo') != '1')
			set_cookie('UserId'); //清除
		set_cookie('LoginStatus'); //清除登录状态
		set_cookie('RoleMenuData'); //清除
		
		header("Location: login.php");
	}
}

?>
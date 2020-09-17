<?php
/**
 *  user.php 用户相关功能
 *
 * @copyright			(C) 2017 Wind Information
 * @author				qpzhou
 * @lastmodify			2017-9-18
 */

define('MONITOR_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);

include MONITOR_PATH.'/global/base.php';

$user = new user;
if(!isset($_GET['method'])) {
	$user->init(); //初始页面
	exit;
}
else {
	if (method_exists($user, $_GET['method']))
		$user->$_GET['method']();
	else
		showmessage("不支持的请求！");
}

class user {
	
	public function init() {
		//用户列表
		$data_userlist = monitor_base::req_func(MSG_CODE_SEARCH_USERLIST);
		//角色列表
		$data_rolelist = monitor_base::req_func(MSG_CODE_SEARCH_USER_ROLELIST);
		
		include template('user');
	}
	
	// 用户管理 - 弹窗页
	public function useroper() {
		$oper = $_GET['oper']; //add or mod
		$tableid = $_GET['tableid'];
		
		$data_rolelist = monitor_base::req_func(MSG_CODE_SEARCH_USER_ROLELIST);
		
		include template('user_user');
	}
	// 用户管理 - 增删改
	public function useroperapply() {
		$req_data['OperType'] = $_GET['OperType'];//Add Mod Del
		$req_data['UserID'] = $_GET['UserID'];
		$req_data['UserName'] = $_GET['UserName'];
		$req_data['UserAddr'] = $_GET['UserAddr'];
		$req_data['Tel'] = $_GET['Tel'];
		$req_data['Email'] = $_GET['Email'];
		$req_data['UserRoleName'] = $_GET['UserRoleName'];
		
		$ret_json = monitor_base::req_func(MSG_CODE_USER_MANAGE_APPLY, $req_data, false);
		echo $ret_json;
	}
	
	// 角色管理 - 弹窗页
	public function roleoper() {
		$oper = $_GET['oper']; //add or mod
		$tableid = $_GET['tableid'];
		include template('user_role');
	}
	// 角色管理 - 增删改
	public function roleoperapply() {
		$req_data['OperType'] = $_GET['OperType'];//Add Mod Del
		$req_data['RoleID'] = intval($_GET['RoleID']);
		$req_data['RoleName'] = $_GET['RoleName'];
		$req_data['RoleRemark'] = $_GET['RoleRemark'];
	
		$ret_json = monitor_base::req_func(MSG_CODE_USER_ROLE_MANAGE_APPLY, $req_data, false);
		echo $ret_json;
	}
	// 角色管理 - 角色权限查看
	public function rolemenu() {
		
		//获取角色菜单
		$roleid = $_GET['RoleID'];
		$rolename = $_GET['RoleName'];
		if (isset($roleid)) {
			
			//加载菜单列表
			$data_menulist = monitor_base::get_menu_list();
			
			$req_data['RoleID'] = intval($roleid);
			$data_rolemenu = monitor_base::req_func(MSG_CODE_ROLE_MENU_GET, $req_data);
			
			$arr_rolemenu = array();
			if (isset($data_rolemenu['RoleMenuData']))
				$arr_rolemenu = explode(",", $data_rolemenu['RoleMenuData']);
		}
		
		include template('user_roleauth');
	}
	// 角色管理 - 角色权限提交
	public function rolemenuapply() {
	
		$req_data['RoleID'] = intval($_POST['RoleID']);
		$req_data['RoleMenuData'] = $_POST['RoleMenuData'];

		$ret_json = monitor_base::req_func(MSG_CODE_ROLE_MENU_SET, $req_data, false);
		echo $ret_json;
	}
	
	
	public function changepwd() {
		include template('user_changepwd');
	}
	
	public function dochangepwd() {
		$UserId = get_cookie('UserId', '');
		if (empty($UserId)) {
			header("Location: login.php");
			exit;
		}
		$req_data['UserId'] = $UserId;
		$req_data['OldPwd'] = md5($_POST['OldPwd']);
		$req_data['NewPwd'] = md5($_POST['NewPwd']);
	
		$ret = monitor_base::req_func(MSG_CODE_CHANGE_PWD, $req_data);
		if ($ret['Result'] == '1')
			include template('user_changepwd_succ');
		else
			showmessage($ret['RspInfo']);
	}
	
	//操作日志
	public function oplogs() {
		$CurLogTypeName = $_POST['LogTypeName'];
		$data_log_typelist = monitor_base::req_func(MSG_CODE_SEARCH_OPERLOG_TYPELIST);
		
		// 初始化时，默认先加载第一个日志类型
		if ($CurLogTypeName == null)
			$CurLogTypeName = $data_log_typelist['LogTypeList'][0];
		
		// 请求日志列表
		$req_data['LogTypeName'] = $CurLogTypeName;
		$data_logs = monitor_base::req_func(MSG_CODE_SEARCH_OPERLOGS, $req_data);
		
		include template('oplogs');
	}
}

?>
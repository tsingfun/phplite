<?php
/**
 *  forget.php 找回密码相关功能
 *
 * @copyright			(C) 2017 Wind Information
 * @author				qpzhou
 * @lastmodify			2017-9-19
 */

define('MONITOR_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);

include MONITOR_PATH.'/global/base.php';

$f = new forget;
if(!isset($_GET['method'])) {
	$f->init(); //初始页面
	exit;
}
else {
	if (method_exists($f, $_GET['method']))
		$f->$_GET['method']();
	else
		showmessage("不支持的请求！");
}

class forget {
	
	public function init() {
				
		include template('user_forget');
	}
	
	public function sendemail() {
		$UserId = $_POST['UserId'];
		$email = $_POST['email'];
		
		$req_data['UserId'] = $UserId;
		$req_data['email'] = $email;
		
		//url地址
		$pageURL = 'http';
		if ($_SERVER["HTTPS"] == "on")
			$pageURL .= "s";
		$pageURL .= "://".$_SERVER["HTTP_HOST"];
		
		//邮件内容
		$expire_time = time() + 3600;
		$req_data['Content'] = new_addslashes('Wind交易监控系统找回密码链接：' . $pageURL.'/forget.php?method=findpwd&f='.base64_encode($UserId) .'&w='.base64_encode($expire_time) );

		$ret = monitor_base::req_func(MSG_CODE_SEND_EMAIL_RESETPWD, $req_data);
		if ($ret['Result'] == '1') {			
			include template('user_forget_succ');
		} else {
			if (!isset($ret['RspInfo']))
				showmessage('服务器连接异常，请稍后再试！');
			else
				showmessage($ret['RspInfo']);
		}
	}
	
	//处理找回密码url
	public function findpwd() {
		$UserId = base64_decode($_GET['f']);
		$expire_time = base64_decode($_GET['w']);
		if (empty($UserId) || $UserId == false || empty($expire_time) || $expire_time == false) {
			showmessage('找回密码：URL异常！');
		}
		
		if (time() > $expire_time) {
			showmessage('找回密码：URL已失效！');
		}
		
		include template('user_forget_changepwd');
	}
	
	//密码重置
	public function dochangepwd() {
		$req_data['UserId'] = $_POST['UserId'];
		$req_data['OldPwd'] = 'reset';
		$req_data['NewPwd'] = md5($_POST['NewPwd']);

		$ret = monitor_base::req_func(MSG_CODE_CHANGE_PWD, $req_data);
		if ($ret['Result'] == '1')
			include template('user_forget_changepwd_succ');
		else
			showmessage($ret['RspInfo']);
	}
}

?>
<?php
/**
 *  nologin.php 无需登录验证的页面集合
 *
 * @copyright			(C) 2017 Wind Information
 * @author				qpzhou
 * @lastmodify			2017-9-19
 */
$nologin = array(
		'login.php',		// 登录页面（不可删除）
		'server_mock.php',	// mock请求
		'json_test.php',	// json请求测试
		'forget.php',		// 找回密码
);

function is_no_need_login($page) {
	global $nologin;
	return in_array($page, $nologin);
}

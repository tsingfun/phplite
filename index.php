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

// 获取子菜单列表
function get_child_menu($menuid, $menu_arr) {
	//$arr = array_filter($menu_arr, function($item){ return $item['ParentMenuID'] == $menuid; }); //todo:$menuid在回调函数里面竟然为null??
	$arr = array();
	foreach ($menu_arr as $r) {
		if ($r['ParentMenuID'] == $menuid)
			array_push($arr, $r);
	}
	return $arr;
}

// 获取菜单的url地址
function get_menu_url($menuid = '', $default = '') {
	static $urls = array();
	if (!isset($menuid))
		return $default;

	if (isset($urls[$menuid]))
		return $urls[$menuid];

	$path = MONITOR_PATH.'/global/menu_url.php';
	if (file_exists($path)) {
		$urls = include $path;
	}

	if (isset($urls[$menuid]))
		return $urls[$menuid];
	else
		return $default;
}

//角色权限菜单
$arr_rolemenu = array();
$roleMenuData = get_cookie("RoleMenuData");
if (isset($roleMenuData))
	$arr_rolemenu = explode(",", $roleMenuData);

// 是否需要报警？（有“实时监控”菜单权限的需要）
$isneed_alarm = strpos($roleMenuData, '1001') !== false;
// 是否是运营角色？（有“WTS运营”菜单权限的）
$isyy_role = strpos($roleMenuData, '2001') !== false;

//加载菜单列表
$all_menulist = monitor_base::get_menu_list();

//过滤掉无权限的菜单
$data_menulist = array();
if (isset($all_menulist['MenuData'])) {
	foreach ($all_menulist['MenuData'] as $r) {
		if (in_array($r['MenuID'], $arr_rolemenu))
			array_push($data_menulist, $r);
	}
}

//一级菜单
$first_menu = array_filter($data_menulist, function($item){ return $item['MenuLevel'] === 1; });

include template('index');

?>
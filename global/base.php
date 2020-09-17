<?php
/**
 *  base.php 框架入口文件
 *
 * @copyright			(C) 2017 Wind Information
 * @author				qpzhou
 * @lastmodify			2017-9-13
 */
define('IN_MONITOR', true);

if(!defined('MONITOR_PATH')) define('MONITOR_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR);

include MONITOR_PATH.'/global/global.func.php';
include MONITOR_PATH.'/global/msgcode.php';
include MONITOR_PATH.'/global/nologin.php';

//系统开始时间
//define('SYS_START_TIME', microtime());
//define('SYS_TIME', time());

monitor_base::load_config('errorlog') ? set_error_handler('my_error_handler') : error_reporting(E_ERROR | E_WARNING | E_PARSE);
//设置本地时差
function_exists('date_default_timezone_set') && date_default_timezone_set(monitor_base::load_config('timezone'));

//输出页面字符集
header('Content-type: text/html; charset=utf-8');

//登录判断
$curfile = basename($_SERVER['PHP_SELF']);
if (!is_no_need_login($curfile)) {
	$LoginStatus = get_cookie('LoginStatus');
	if (empty($LoginStatus)) {
		header("Location: login.php");
		exit;
	}
}

class monitor_base {
	/**
	 * 加载配置文件
	 * @param string $key  要获取的配置荐
	 * @param string $default  默认配置。当获取配置项目失败时该值发生作用。
	 */
	public static function load_config($key = '', $default = '') {
		static $configs = array();
		if (!isset($key))
			return $default;
		
		if (isset($configs[$key]))
			return $configs[$key];
		
		$path = MONITOR_PATH.'config.php';
		if (file_exists($path)) {
			$configs = include $path;
		}
		
		if (isset($configs[$key]))
			return $configs[$key];
		else
			return $default;
	}
	
	/**
	 * 请求Monitor.Server数据（请求数据为数组）
	 * @param number $msgcode 请求类型
	 * @param obj $reqdata 请求数据
	 * @param bool $trans2array 响应JSON是否转换成数组(默认转数组)
	 * @param number $timeout 请求超时时间
	 */
	public static function req_func($msgcode, $req_data = null, $trans2array = true, $timeout = 10) {
		
		// 生成请求Json
		$req_data['MsgCode'] = $msgcode;
		$req_data['ClientIP'] = ip();
		$req_json = array2json($req_data);
		//echo $req_json;
		
		//请求后台
		$rsp_json = self::request_server(array("req_json" => $req_json), $timeout);
		
		//sleep(1);
		if ($trans2array)
			return json2array($rsp_json);
		else
			return mult_iconv("GBK", "UTF-8", $rsp_json);
	}
	
	/**
	 * 请求Monitor.Server数据（请求数据为元素JSON）
	 * @param number $msgcode 请求类型
	 * @param obj $reqdata 请求数据
	 * @param bool $trans2array 响应JSON是否转换成数组(默认转数组)
	 * @param number $timeout 请求超时时间
	 */
	public static function req_json_func($msgcode, $req_json = null, $trans2array = true, $timeout = 10) {

		$req_json = '{"MsgCode":'.$msgcode.',"ClientIP":"'.ip().'",'.substr($req_json, 1);
		// UTF8 -> GBK
		$req_json = mult_iconv("UTF-8", "GBK", $req_json);
		
		//请求后台
		$rsp_json = self::request_server(array("req_json" => $req_json), $timeout);

		if ($trans2array)
			return json2array($rsp_json);
		else
			return mult_iconv("GBK", "UTF-8", $rsp_json);
	}
	
	
	/**
	 * 请求url，返回结果
	 * @param unknown $url
	 * @return mixed
	 */
	public static function request_server($post_data, $timeout = 10) {		
		$curl = curl_init();
		
		curl_setopt($curl, CURLOPT_URL, self::load_config('monitor_server_uri'));
		curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
		// 设置获取的信息以文件流的形式返回，而不是直接输出。
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		// post数据
		curl_setopt($curl, CURLOPT_POST, 1);
		// post的变量
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post_data));
		
		$data = curl_exec($curl);
		//关闭URL请求
		curl_close($curl);
		
		return $data;
	}
	
	//获取菜单列表:todo缓存无效??
	public static function get_menu_list() {
		static $data_menulist = array();
		if (!isset($data_menulist['MenuData']))
			$data_menulist = monitor_base::req_func(MSG_CODE_SEARCH_MENULIST);
		
		return $data_menulist;
	}
}

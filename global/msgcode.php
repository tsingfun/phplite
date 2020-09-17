<?php
/**
 *  msgcode.php 功能类型定义
 *
 * @copyright			(C) 2017 Wind Information
 * @author				qpzhou
 * @lastmodify			2017-9-15
 */

// ----------MsgCode定义---------------
// 监控信息相关
define('MSG_CODE_SEARCH_ERR_WARN', 			1);		// 2.5.1	查询错误、警告报告
define('MSG_CODE_SEARCH_ERR_WARN_NUM', 		100);	// 			查询错误、警告数量
define('MSG_CODE_SEARCH_ERR_WARN_DETAIL',	2);		// 2.5.2	查询错误、警告详情
define('MSG_CODE_CLEAR_ERR_WARN',			3);		// 2.5.3	消除错误、警告
define('MSG_CODE_SEARCH_NOTICE',			4);		// 2.5.4	查询通知消息

// 主机应用相关
define('MSG_CODE_SEARCH_HOST_MANAGELIST',	5);		// 2.5.5	查询主机管理列表
define('MSG_CODE_SEARCH_HOST_LIST',			6);		// 2.5.6	查询主机列表（主机应用列表功能）
define('MSG_CODE_HOST_MANAGE_APPLY',		7);		// 2.5.7	主机管理（增删改）
define('MSG_CODE_SEARCH_APP_TYPENAMELIST',	8);		// 2.5.8	查询应用类型列表
define('MSG_CODE_SEARCH_APP_MANAGELIST',	9);		// 2.5.9	查询应用管理列表
define('MSG_CODE_SEARCH_HOST_APP_LIST',		10);	// 2.5.10	查询应用列表（主机应用列表功能）
define('MSG_CODE_SEARCH_HOST_APP_OPER',		11);	// 2.5.11	操作应用（启动、停止、自启动、守护）
define('MSG_CODE_APP_GROUP_GET',			12);	// 2.5.12	查询应用组别
define('MSG_CODE_APP_GROUP_MANAGE',			13);	// 2.5.13	管理应用组别（增删）
define('MSG_CODE_SET_APP_2_GROUP',			14);	// 2.5.14	设置应用组别

define('MSG_CODE_SEARCH_OPERLOGS',			15);	// 2.5.15	查询操作日志
define('MSG_CODE_SEARCH_USERLIST',			16);	// 2.5.16	查询用户
define('MSG_CODE_USER_MANAGE_APPLY',		17);	// 2.5.17	管理用户（增删改）
define('MSG_CODE_SEARCH_USER_ROLELIST',		18);	// 2.5.18	查询角色
define('MSG_CODE_USER_ROLE_MANAGE_APPLY',	19);	// 2.5.19	管理角色（增删改）
define('MSG_CODE_SEARCH_MENULIST',			20);	// 2.5.20	查询菜单列表
define('MSG_CODE_ROLE_MENU_GET',			21);	// 2.5.21	查询角色权限
define('MSG_CODE_ROLE_MENU_SET',			22);	// 2.5.22	设置角色权限
define('MSG_CODE_SYS_SETTINGS_GET',			23);	// 2.5.23	查询系统设置
define('MSG_CODE_SYS_SETTINGS_SAVE',		24);	// 2.5.24	保存系统设置
define('MSG_CODE_MONITOR_EMS',				25);	// 2.5.25	EMS监控
define('MSG_CODE_MONITOR_PB',				26);	// 2.5.26	PB监控

define('MSG_CODE_SETTINGS_GET',				27);	// 2.5.27	远程参数获取（WTS、EMS、PB等）
define('MSG_CODE_SETTINGS_SET',				28);	// 2.5.28	远程参数设置（WTS、EMS、PB等）


define('MSG_CODE_CHANGE_PWD', 				101);	// 			修改密码
define('MSG_CODE_LOGIN', 					102);	// 			登录验证
define('MSG_CODE_SEARCH_OPERLOG_TYPELIST', 	103);	// 			查询操作日志类型列表
define('MSG_CODE_SEND_EMAIL_TEST',			104);	// 			发送测试邮件
define('MSG_CODE_SEND_EMAIL_RESETPWD',		105);	// 			发送找回密码邮件

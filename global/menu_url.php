<?php
return array(
		'1001' => '/monitor.php',	// 实时监控
		
		// 参数设置
		'1011' => '/paramset.php?AppName=WtsStock&type=yw',	// WTS
		'1012' => '/paramset.php?AppName=wind.ems.manager&type=emsmgr',	// EMS.Manager
		'1013' => '/paramset.php?AppName=wind.ems.engine&type=emseng',	// EMS.Engine
		'1014' => '/paramset.php?AppName=PB.Quotation&type=pbquo',	// PB.Quotation
		
		'1020' => '/hostapp.php',	// 主机应用列表
		'1021' => '/hostapp.php?method=ems',	// EMS
		'1022' => '/hostapp.php?method=pb',	// PB
		
		// 系统管理
		'1031' => '/hostapp.php?method=host',	// 主机管理
		'1032' => '/hostapp.php?method=app',	// 应用管理
		'1033' => '/user.php?method=oplogs',	// 操作日志
		'1034' => '/user.php',	// 用户权限
		'1035' => '/settings.php',	// 系统设置
		
		// 运营
		'2001' => '/paramset.php?AppName=WtsStock&type=yy',	// WTS运营
);
?>
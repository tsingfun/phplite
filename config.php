<?php
return array(

'monitor_server_uri' => "http://10.100.2.86:5583/windmonitorserver",	//Monitor.Server HTTP URI
#'monitor_server_uri' => "http://127.0.0.1/mock/server_mock.php",
'errorlog' => 0,	//1、保存错误日志到 logs/error.log | 0、在页面直接显示
'cookie_pre' => 'TradeMonitor_',
'cookie_timeout' => 300,	//SESSION超时时间（分钟）
'timezone' => 'Etc/GMT-8', //网站时区（只对php 5.1以上版本有效），Etc/GMT-8 实际表示的是 GMT+8

);
?>
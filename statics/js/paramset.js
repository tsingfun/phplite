// 表格点击选中行（新增行时调用）
function refresh_table_selectable(){
    $('table.selectable tr').click(function () {
        var trSeq = $(this).parent().find('tr').index($(this));
        if (trSeq > 0){
        	$(this).parent().find('tr').removeClass('selected');
        	$(this).addClass('selected');
        }
    })
}
// 刷新表格的序号（删除行时调用）
function refresh_table_no(tableid){
	$('#' + tableid + ' tr:nth-child(n+2)').each(function(){
		var trSeq = $(this).parent().find('tr').index($(this));
		$(this).children('td').eq(0).text(trSeq);
	});
}

// 修改后，Tab标*星号
function tab_mark(tabid){
	if (tab_is_marked(tabid)) return;
	$('#' + tabid).html($('#' + tabid).html() + '<font class="tab_mark">*</font>');
}
function tab_is_marked(tabid){
	return $('#' + tabid).html().indexOf('*') >= 0;
}


// 初始化
$().ready( function(){

	$('.ip').ipaddress();

	/*tab切换*/
    $(".tab_title li a").click( function(){
        var id = $(this).attr("id");
        $("#activTab").val(id);
        $(".menu").hide();
        $("#"+ id +"Box").show();
        $(".tab_title > ul > li").removeClass("tab_title_l").addClass("tab_title_l_down");
        $(this).parent().removeClass("tab_title_l_down").addClass("tab_title_l").addClass(id);
    });
	
 	// 表格点击选中行
    refresh_table_selectable();
    
    // -----------------------------控件内容改变Tab标*星号-----------------------------
    //每次输入都会触发
    //$("#oem_id").on('input',function(e){  alert($(this).val());  });
    
    // 内容改变，失去光标时触发
    $('#oem_id').change(function(){  tab_mark('oem');  });
    $('#oem_errinfo').change(function(){  tab_mark('oem');  });
    $('#md5_enable').change(function(){  tab_mark('oem');  });
    $('#md5_errinfo').change(function(){  tab_mark('oem');  });
    
    $('#trade_enable').change(function(){  tab_mark('trade');  });
    
    //$('#ems_ip').change(function(){  tab_mark('ems');  });
    $('#ems_ip_octet_1').change(function(){  tab_mark('ems');  });
    $('#ems_ip_octet_2').change(function(){  tab_mark('ems');  });
    $('#ems_ip_octet_3').change(function(){  tab_mark('ems');  });
    $('#ems_ip_octet_4').change(function(){  tab_mark('ems');  });
    $('#ems_port').change(function(){  tab_mark('ems');  });
    $('#ems_timeout').change(function(){  tab_mark('ems');  });
    
    // 运营
    $('#etf_enable').change(function(){  tab_mark('etf');  });
    
    $('#bwlist_mode').change(function(){  tab_mark('bwlist');  });
    $('#bwlist_match').change(function(){  tab_mark('bwlist');  });
    
    // EMS.Manager    
    $('#emsmgr_backuptime').change(function(){  tab_mark('emsmgr');  });
    $('#emsmgr_restarttime').change(function(){  tab_mark('emsmgr');  });
    // EMS.Engine    
    $('#emseng_backuptime').change(function(){  tab_mark('emseng');  });
    $('#emseng_restarttime').change(function(){  tab_mark('emseng');  });
    // -----------------------------------------------------------------------------
    
    //----------------------------------载入配置弹出层-------------------------------
    $('#load_from').mouseenter(function(e){
    	//获取鼠标位置函数
    	var x = e.pageX || e.originalEvent.x || e.originalEvent.layerX || 0;
    	var y = e.pageY || e.originalEvent.y || e.originalEvent.layerY || 0;
    	$('#load_div').css({ top: y-2 ,left: x+10 });
    	
        $('#load_div').fadeIn(1);
    }).mouseleave(function(e){
    	//鼠标在弹出层范围内，不隐藏
    	var x = e.pageX || e.originalEvent.x || e.originalEvent.layerX || 0;
    	var y = e.pageY || e.originalEvent.y || e.originalEvent.layerY || 0;
    	
    	var t = $('#load_div').offset().top;
    	var l = $('#load_div').offset().left;
    	var w = $('#load_div').width();
    	var h = $('#load_div').height();
    	//alert('x:'+x+',y:'+y+'   t:'+t+',l:'+l+',w:'+w+',h:'+h);
    	if (x<l || x>(l+w) || y<t || y>(t+h))
    		$('#load_div').fadeOut(500);
    });
    
    $('#load_div').mouseenter(function(e){    	
        $('#load_div').stop().fadeIn(1);
    }).mouseleave(function(){
    	$(this).fadeOut(500);
    });
    //----------------------------------------------------------------------------
    
});


// --------OEM设置：许可客户端MD5列表管理-----------
// 提交时生成JSON
function gen_oem_json(ret_json){
	if(typeof(ret_json['WtsStock.ini'])=='undefined')
		ret_json['WtsStock.ini'] = {};
	
	ret_json['WtsStock.ini']['OEM'] = {};
	ret_json['WtsStock.ini']['OEM']['ID'] = $('#oem_id').val();
	ret_json['WtsStock.ini']['OEM']['ERRINFO'] = $('#oem_errinfo').val();
	
	ret_json['WtsStock.ini']['MD5'] = {};
	ret_json['WtsStock.ini']['MD5']['ENABLE'] = $('#md5_enable').is(':checked') ? '1' : '0';
	ret_json['WtsStock.ini']['MD5']['ERRINFO'] = $('#md5_errinfo').val();
	
	ret_json['ModulesMd5.json'] = {};
	ret_json['ModulesMd5.json']['Md5Info'] = [];
	$('#table_md5 tr:nth-child(n+2)').each(function(){
		var Md5Info = {};
		Md5Info['apptype'] = $(this).children('td').eq(1).text();
		Md5Info['md5'] = $(this).children('td').eq(2).text();
		Md5Info['remark'] = $(this).children('td').eq(3).text();
		
		ret_json['ModulesMd5.json']['Md5Info'].push(Md5Info);
	});
}
function md5_add(tableid){
	parent.layer.open({
		type: 2,
		title: 'MD5码 - 添加',
		maxmin: false,
		area: ['510px', '260px'],
		anim:false,
		content: '/paramset.php?method=oemmd5&oper=add&tableid=' + tableid,
  	});
}
function md5_mod(tableid){
	//检查是否有选中行
	var trObj = $('#'+tableid).find('tr.selected');
	if (trObj.length <= 0){
		parent.layer.msg('请选中一行进行修改！', {icon: 7});
		return;
	}
	parent.layer.open({
		type: 2,
		title: 'MD5码 - 修改',
		maxmin: false,
		area: ['510px', '260px'],
		anim:false,
		content: '/paramset.php?method=oemmd5&oper=mod&tableid=' + tableid,
  	});
}
function md5_del(tableid){
	var trObj = $('#'+tableid).find('tr.selected');
	if (trObj.length <= 0){
		parent.layer.msg('请选中一行进行删除！', {icon: 7});
		return;
	}
	
	//询问框
	parent.layer.confirm('终端类型：' + trObj.children('td').eq(1).text()
			+ '<br/>MD5码：' + trObj.children('td').eq(2).text()
			+ '<br/>备注：' + trObj.children('td').eq(3).text(),
	{
	  btn: ['确认','取消'], //按钮
	  title: 'MD5码 - 删除确认'
	}, function(){
	   	//ok
		trObj.remove();
		refresh_table_no(tableid);
		parent.layer.msg('删除成功！', {icon: 1});
		tab_mark('oem');
	}, function(){	
	  	//cancel
	});
}
function md5_callback(tableid, oper, terminalType, md5code, remark){
	
	if (oper=='add'){
		var rowNum = $('#' + tableid).find('tr').length;
		var newRow = '<tr><td>' + rowNum + '</td><td>' + terminalType + '</td><td>' + md5code + '</td><td>' + remark + '</td></tr>';
		$('#' + tableid + ' tr:last').after(newRow);
		
		// 表格点击选中行
	    refresh_table_selectable();
	}
	else if (oper=='mod'){
		var trObj = $('#'+tableid).find('tr.selected');
		trObj.children('td').eq(1).text(terminalType);
		trObj.children('td').eq(2).text(md5code);
		trObj.children('td').eq(3).text(remark);
		
	} else {
		parent.layer.msg('不支持的操作类型！', {icon: 2});
		return;
	}
	
	tab_mark('oem');
}



//--------交易权限管理-----------
//提交时生成JSON
function gen_trade_json(ret_json){
	if(typeof(ret_json['WtsStock.ini'])=='undefined')
		ret_json['WtsStock.ini'] = {};
	
	if(typeof(ret_json['WtsStock.ini']['宏汇处理方式'])=='undefined')
		ret_json['WtsStock.ini']['宏汇处理方式'] = {};
	
	ret_json['WtsStock.ini']['宏汇处理方式']['账号权限管理'] = $('#trade_enable').is(':checked') ? '1' : '0';
	
	ret_json['AccountAuthority.json'] = {};
	ret_json['AccountAuthority.json']['AccountAuthority'] = [];
	$('#table_trade tr:nth-child(n+2)').each(function(){
		var AccountInfo = {};
		AccountInfo['accountcode'] = $(this).children('td').eq(0).text();
		AccountInfo['ip'] = $(this).children('td').eq(1).text();
		AccountInfo['mac'] = $(this).children('td').eq(2).text();
		AccountInfo['tuneposition'] = $(this).children('td').eq(3).text() == '启用' ? 1 : 0;
		AccountInfo['orderconfirm'] = $(this).children('td').eq(4).text() == '启用' ? 1 : 0;
		AccountInfo['filewithdraw'] = $(this).children('td').eq(5).text() == '启用' ? 1 : 0;
		AccountInfo['orderspeed'] = parseInt($(this).children('td').eq(6).text());
		AccountInfo['withdrawspeed'] = parseInt($(this).children('td').eq(7).text());
		AccountInfo['withdrawdaily'] = parseInt($(this).children('td').eq(8).text());
		AccountInfo['searchspeed'] = parseInt($(this).children('td').eq(9).text());
		
		ret_json['AccountAuthority.json']['AccountAuthority'].push(AccountInfo);
	});
}
function trade_add(tableid){
	parent.layer.open({
		type: 2,
		title: '交易权限 - 添加',
		maxmin: false,
		area: ['600px', '530px'],
		anim:false,
		content: '/paramset.php?method=trade&oper=add&tableid=' + tableid,
  	});
}
function trade_mod(tableid){
	//检查是否有选中行
	var trObj = $('#'+tableid).find('tr.selected');
	if (trObj.length <= 0){
		parent.layer.msg('请选中一行进行修改！', {icon: 7});
		return;
	}
	parent.layer.open({
		type: 2,
		title: '交易权限 - 修改',
		maxmin: false,
		area: ['600px', '530px'],
		anim:false,
		content: '/paramset.php?method=trade&oper=mod&tableid=' + tableid,
  	});
}
function trade_del(tableid){
	var trObj = $('#'+tableid).find('tr.selected');
	if (trObj.length <= 0){
		parent.layer.msg('请选中一行进行删除！', {icon: 7});
		return;
	}
	
	//询问框
	parent.layer.confirm('资金账户：' + trObj.children('td').eq(0).text()
			+ '<br/>IP地址：' + trObj.children('td').eq(1).text()
			+ '<br/>MAC地址：' + trObj.children('td').eq(2).text()
			+ '<br/>调仓：' + trObj.children('td').eq(3).text()
			+ '<br/>自动下单确认：' + trObj.children('td').eq(4).text()
			+ '<br/>撤单(文件)：' + trObj.children('td').eq(5).text()
			+ '<br/>下单流速上限：' + trObj.children('td').eq(6).text()
			+ '<br/>撤单流速上限：' + trObj.children('td').eq(7).text()
			+ '<br/>日撤单数上限：' + trObj.children('td').eq(8).text()
			+ '<br/>查询流速上限：' + trObj.children('td').eq(9).text(),
	{
	  btn: ['确认','取消'], //按钮
	  title: '交易权限 - 删除确认'
	}, function(){
	   	//ok
		trObj.remove();
		parent.layer.msg('删除成功！', {icon: 1});
		tab_mark('trade');
	}, function(){	
	  	//cancel
	});
}
function trade_callback(tableid, oper, accountcode, ip, mac, 
		tuneposition, orderconfirm, filewithdraw, orderspeed, withdrawspeed, withdrawdaily, searchspeed){
	
	if (oper=='add'){
		//检查：  资金账户,IP地址,MAC地址 联合主键
		var bDupl = false;
		$('#' + tableid + ' tr:nth-child(n+2)').each(function(){
			if ( accountcode == $(this).children('td').eq(0).text() &&
				 ip == $(this).children('td').eq(1).text() &&
				 mac == $(this).children('td').eq(2).text() ) {
				
				parent.layer.alert('资金账户：' + $(this).children('td').eq(0).text()
						+ '<br/>IP地址：' + $(this).children('td').eq(1).text()
						+ '<br/>MAC地址：' + $(this).children('td').eq(2).text()
						+ '<br/>三者相同的已经存在，请不要重复添加！', {icon: 2});
				bDupl = true;
				return false; //=break
			}
		});
		if (bDupl) return;
		
		var newRow = '<tr>' + 
			'<td>' + accountcode + '</td>' + 
			'<td>' + ip + '</td>' + 
			'<td>' + mac + '</td>' + 
			'<td>' + tuneposition + '</td>' + 
			'<td>' + orderconfirm + '</td>' + 
			'<td>' + filewithdraw + '</td>' + 
			'<td>' + orderspeed + '</td>' + 
			'<td>' + withdrawspeed + '</td>' + 
			'<td>' + withdrawdaily + '</td>' + 
			'<td>' + searchspeed + '</td>' + 
		'</tr>';
		$('#' + tableid + ' tr:last').after(newRow);
		
		// 表格点击选中行
	    refresh_table_selectable();
	}
	else if (oper=='mod'){
		var trObj = $('#'+tableid).find('tr.selected');
		trObj.children('td').eq(0).text(accountcode);
		trObj.children('td').eq(1).text(ip);
		trObj.children('td').eq(2).text(mac);
		trObj.children('td').eq(3).text(tuneposition);
		trObj.children('td').eq(4).text(orderconfirm);
		trObj.children('td').eq(5).text(filewithdraw);
		trObj.children('td').eq(6).text(orderspeed);
		trObj.children('td').eq(7).text(withdrawspeed);
		trObj.children('td').eq(8).text(withdrawdaily);
		trObj.children('td').eq(9).text(searchspeed);
		
	} else {
		parent.layer.msg('不支持的操作类型！', {icon: 2});
		return;
	}
	
	tab_mark('trade');
}


//--------ETF过滤-----------
function gen_etf_json(ret_json){
	if(typeof(ret_json['WtsStock.ini'])=='undefined')
		ret_json['WtsStock.ini'] = {};
	
	ret_json['WtsStock.ini']['ETF过滤'] = {};
	ret_json['WtsStock.ini']['ETF过滤']['过滤标志'] = $('#etf_enable').is(':checked') ? '1' : '0';
	
	var i = 0;
	$('#table_etf tr:nth-child(n+2)').each(function(){
		var market = $(this).children('td').eq(0).text() == '上海' ? '1' : '0';
		ret_json['WtsStock.ini']['ETF过滤']['市场' + i] = market;
		ret_json['WtsStock.ini']['ETF过滤']['代码' + i] = $(this).children('td').eq(1).text();
		i++;
	});
	ret_json['WtsStock.ini']['ETF过滤']['ETF数量'] = i.toString();
}
function etf_add(tableid){
	parent.layer.open({
		type: 2,
		title: 'ETF代码 - 添加',
		maxmin: false,
		area: ['490px', '240px'],
		anim:false,
		content: '/paramset.php?method=etf&oper=add&tableid=' + tableid,
  	});
}
function etf_mod(tableid){
	//检查是否有选中行
	var trObj = $('#'+tableid).find('tr.selected');
	if (trObj.length <= 0){
		parent.layer.msg('请选中一行进行修改！', {icon: 7});
		return;
	}
	parent.layer.open({
		type: 2,
		title: 'ETF代码 - 修改',
		maxmin: false,
		area: ['490px', '240px'],
		anim:false,
		content: '/paramset.php?method=etf&oper=mod&tableid=' + tableid,
  	});
}
function etf_del(tableid){
	var trObj = $('#'+tableid).find('tr.selected');
	if (trObj.length <= 0){
		parent.layer.msg('请选中一行进行删除！', {icon: 7});
		return;
	}
	
	//询问框
	parent.layer.confirm('市场：' + trObj.children('td').eq(0).text()
			+ '<br/>代码：' + trObj.children('td').eq(1).text(),
	{
	  btn: ['确认','取消'], //按钮
	  title: 'ETF代码 - 删除确认'
	}, function(){
	   	//ok
		trObj.remove();
		parent.layer.msg('删除成功！', {icon: 1});
		
		tab_mark('etf');
	}, function(){
	  	//cancel
	});
}
function etf_callback(tableid, oper, market, code){
	
	if (oper=='add'){
		var newRow = '<tr><td>' + market + '</td><td>' + code + '</td></tr>';
		$('#' + tableid + ' tr:last').after(newRow);
		
		// 表格点击选中行
	    refresh_table_selectable();
	}
	else if (oper=='mod'){
		var trObj = $('#'+tableid).find('tr.selected');
		trObj.children('td').eq(0).text(market);
		trObj.children('td').eq(1).text(code);
		
	} else {
		parent.layer.msg('不支持的操作类型！', {icon: 2});
		return;
	}
	
	tab_mark('etf');
}



//--------黑白名单管理-----------
function gen_bwlist_json(ret_json){
	if(typeof(ret_json['WtsStock.ini'])=='undefined')
		ret_json['WtsStock.ini'] = {};
	
	if(typeof(ret_json['WtsStock.ini']['宏汇处理方式'])=='undefined')
		ret_json['WtsStock.ini']['宏汇处理方式'] = {};
	
	ret_json['WtsStock.ini']['宏汇处理方式']['黑白名单限制模式'] = $('#bwlist_mode').val().toString();
	ret_json['WtsStock.ini']['宏汇处理方式']['黑白名单匹配模式'] = $('#bwlist_match').val().toString();
	
	var blackStr = ';================================黑名单清单================================\n';
	$('#table_blacklist tr:nth-child(n+2)').each(function(){
		blackStr += $(this).children('td').eq(0).text();
		blackStr += '\n';
	});
	ret_json['BlackList.txt'] = blackStr;
	
	var whiteStr = ';================================白名单清单================================\n';
	$('#table_whitelist tr:nth-child(n+2)').each(function(){
		whiteStr += $(this).children('td').eq(0).text();
		whiteStr += '\n';
	});
	ret_json['WhiteList.txt'] = whiteStr;
}
function bwlist_add(tableid){
	var title = tableid.indexOf('black') >= 0 ? '黑名单' : '白名单';
	parent.layer.open({
		type: 2,
		title: title + ' - 添加',
		maxmin: false,
		area: ['510px', '180px'],
		anim:false,
		content: '/paramset.php?method=bwlist&oper=add&tableid=' + tableid,
  	});
}
function bwlist_mod(tableid){
	var title = tableid.indexOf('black') >= 0 ? '黑名单' : '白名单';
	//检查是否有选中行
	var trObj = $('#'+tableid).find('tr.selected');
	if (trObj.length <= 0){
		parent.layer.msg('请选中一行进行修改！', {icon: 7});
		return;
	}
	parent.layer.open({
		type: 2,
		title: title + ' - 修改',
		maxmin: false,
		area: ['510px', '180px'],
		anim:false,
		content: '/paramset.php?method=bwlist&oper=mod&tableid=' + tableid,
  	});
}
function bwlist_del(tableid){
	var title = tableid.indexOf('black') >= 0 ? '黑名单' : '白名单';
	var trObj = $('#'+tableid).find('tr.selected');
	if (trObj.length <= 0){
		parent.layer.msg('请选中一行进行删除！', {icon: 7});
		return;
	}
	
	//询问框
	parent.layer.confirm('资金账号：' + trObj.children('td').eq(0).text(),
	{
	  btn: ['确认','取消'], //按钮
	  title: title + ' - 删除确认'
	}, function(){
	   	//ok
		trObj.remove();
		parent.layer.msg('删除成功！', {icon: 1});
		
		tab_mark('bwlist');
	}, function(){
	  	//cancel
	});
}
function bwlist_callback(tableid, oper, account_code){
	
	if (oper=='add'){
		var newRow = '<tr><td>' + account_code + '</td></tr>';
		$('#' + tableid + ' tr:last').after(newRow);
		
		// 表格点击选中行
	    refresh_table_selectable();
	}
	else if (oper=='mod'){
		var trObj = $('#'+tableid).find('tr.selected');
		trObj.children('td').eq(0).text(account_code);
		
	} else {
		parent.layer.msg('不支持的操作类型！', {icon: 2});
		return;
	}
	
	tab_mark('bwlist');
}



//--------EMS、PB、行情源管理-----------
//生成EMS.Manager JSON
function gen_emsmgr_json(ret_json){
	ret_json['ems_manager.ini'] = {};
	ret_json['ems_manager.ini']['services'] = {};
	ret_json['ems_manager.ini']['services']['backuptime'] = $('#emsmgr_backuptime').val();
	ret_json['ems_manager.ini']['services']['exittime'] = $('#emsmgr_restarttime').val();
}
//生成EMS.Engine JSON
function gen_emseng_json(ret_json){
	ret_json['ems_engine.ini'] = {};
	ret_json['ems_engine.ini']['SYS'] = {};
	ret_json['ems_engine.ini']['SYS']['exittime'] = $('#emseng_restarttime').val();
	
	// 行情源配置生成
	var SrcArr = new Array();
	$('#table_emseng tr:nth-child(n+2)').each(function(){
		var src = $(this).children('td').eq(1).text();
		if (SrcArr.indexOf(src) < 0)
			SrcArr.push(src);
	});
	
	var SrcSeq = '';
	for(var i = 0; i < SrcArr.length; i++){
		var src = SrcArr[i];
		SrcSeq += (src + ';');
		
		ret_json['ems_engine.ini'][src] = {};
		
		var server_num = 0;
		var market_filter = '';
		$('#table_emseng tr:nth-child(n+2)').each(function(){			
			if (src == $(this).children('td').eq(1).text()) {
				ret_json['ems_engine.ini'][src]['IP' + server_num] = $(this).children('td').eq(2).text();
				ret_json['ems_engine.ini'][src]['Port' + server_num] = $(this).children('td').eq(3).text();
				ret_json['ems_engine.ini'][src]['User' + server_num] = $(this).children('td').eq(4).text();
				ret_json['ems_engine.ini'][src]['Password' + server_num] = $(this).children('td').eq(5).text();
				
				if (server_num == 0) //取第一个
					market_filter = $(this).children('td').eq(6).text();
				server_num++;
			}
		});
		
		ret_json['ems_engine.ini'][src]['MarketFilter'] = market_filter;
		ret_json['ems_engine.ini'][src]['ServerNum'] = server_num.toString();
	}

	ret_json['ems_engine.ini']['TDFAPI'] = {};
	ret_json['ems_engine.ini']['TDFAPI']['SrcSeq'] = SrcSeq;
}
//生成PB.Quotation JSON
function gen_pbquo_json(ret_json){
	ret_json['PB_Quotation.ini'] = {};
	
	// 行情源配置生成
	var SrcArr = new Array();
	$('#table_pbquo tr:nth-child(n+2)').each(function(){
		var src = $(this).children('td').eq(1).text();
		if (SrcArr.indexOf(src) < 0)
			SrcArr.push(src);
	});
	
	var SrcSeq = '';
	for(var i = 0; i < SrcArr.length; i++){
		var src = SrcArr[i];
		SrcSeq += (src + ';');
		
		ret_json['PB_Quotation.ini'][src] = {};
		
		var server_num = 0;
		var market_filter = '';
		$('#table_pbquo tr:nth-child(n+2)').each(function(){			
			if (src == $(this).children('td').eq(1).text()) {
				ret_json['PB_Quotation.ini'][src]['IP' + server_num] = $(this).children('td').eq(2).text();
				ret_json['PB_Quotation.ini'][src]['Port' + server_num] = $(this).children('td').eq(3).text();
				ret_json['PB_Quotation.ini'][src]['User' + server_num] = $(this).children('td').eq(4).text();
				ret_json['PB_Quotation.ini'][src]['Password' + server_num] = $(this).children('td').eq(5).text();
				
				if (server_num == 0) //取第一个
					market_filter = $(this).children('td').eq(6).text();
				server_num++;
			}
		});
		
		ret_json['PB_Quotation.ini'][src]['MarketFilter'] = market_filter;
		ret_json['PB_Quotation.ini'][src]['ServerNum'] = server_num.toString();
	}

	ret_json['PB_Quotation.ini']['TDFAPI'] = {};
	ret_json['PB_Quotation.ini']['TDFAPI']['SrcSeq'] = SrcSeq;
}
function quosrc_add(tableid){
	parent.layer.open({
		type: 2,
		title: '行情源 - 添加',
		maxmin: false,
		area: ['560px', '380px'],
		anim:false,
		content: '/paramset.php?method=quosrc&oper=add&tableid=' + tableid,
	});
}
function quosrc_mod(tableid){
	//检查是否有选中行
	var trObj = $('#'+tableid).find('tr.selected');
	if (trObj.length <= 0){
		parent.layer.msg('请选中一行进行修改！', {icon: 7});
		return;
	}
	parent.layer.open({
		type: 2,
		title: '行情源 - 修改',
		maxmin: false,
		area: ['560px', '380px'],
		anim:false,
		content: '/paramset.php?method=quosrc&oper=mod&tableid=' + tableid,
	});
}
function quosrc_del(tableid){
	var trObj = $('#'+tableid).find('tr.selected');
	if (trObj.length <= 0){
		parent.layer.msg('请选中一行进行删除！', {icon: 7});
		return;
	}
	
	//询问框
	parent.layer.confirm('目标服务器：' + trObj.children('td').eq(1).text()
			+ '<br/>IP：' + trObj.children('td').eq(2).text()
			+ '<br/>端口：' + trObj.children('td').eq(3).text()
			+ '<br/>用户：' + trObj.children('td').eq(4).text()
			+ '<br/>市场列表：' + trObj.children('td').eq(6).text(),
	{
	  btn: ['确认','取消'], //按钮
	  title: '行情源 - 删除确认'
	}, function(){
	   	//ok
		trObj.remove();
		refresh_table_no(tableid);
		parent.layer.msg('删除成功！', {icon: 1});
		tab_mark(tableid.replace('table_',''));
	}, function(){	
	  	//cancel
	});
}
function quosrc_callback(tableid, oper, QuoSrc, IP, Port, User, Pwd, MarketFilter){	
	if (oper=='add'){
		//检查：  目标服务器,IP,端口 联合主键
		bError = false;
		$('#' + tableid + ' tr:nth-child(n+2)').each(function(){
			if ( QuoSrc == $(this).children('td').eq(1).text() &&
				IP == $(this).children('td').eq(2).text() &&
				Port == $(this).children('td').eq(3).text() ) {
				
				parent.layer.alert('目标服务器：' + QuoSrc
						+ '<br/>IP：' + IP
						+ '<br/>端口：' + Port
						+ '<br/>三者相同的已经存在，请不要重复添加！', {icon: 2});
				bError = true;
				return false; //=break
			}
		});
		if (bError) return;
		
		var rowNum = $('#' + tableid).find('tr').length;
		var newRow = '<tr>' + 
			'<td>' + rowNum + '</td>' + 
			'<td>' + QuoSrc + '</td>' + 
			'<td>' + IP + '</td>' + 
			'<td>' + Port + '</td>' + 
			'<td>' + User + '</td>' + 
			'<td>' + Pwd + '</td>' + 
			'<td>' + MarketFilter + '</td>' + 
		'</tr>';
		$('#' + tableid + ' tr:last').after(newRow);
		
		// 表格点击选中行
	    refresh_table_selectable();
	}
	else if (oper=='mod'){
		var trObj = $('#'+tableid).find('tr.selected');
		trObj.children('td').eq(1).text(QuoSrc);
		trObj.children('td').eq(2).text(IP);
		trObj.children('td').eq(3).text(Port);
		trObj.children('td').eq(4).text(User);
		trObj.children('td').eq(5).text(Pwd);
		trObj.children('td').eq(6).text(MarketFilter);
		
	} else {
		parent.layer.msg('不支持的操作类型！', {icon: 2});
		return;
	}
	
	tab_mark(tableid.replace('table_',''));
}




//***************************提交**************************
function dosubmit(ret_json) {
	// 提交参数
   	$.ajax({
	    url:'/paramset.php?method=commit',
	    data:{'json':JSON.stringify(ret_json)},
	    type:'POST',
	    async:true,    //或false,是否异步
	    timeout:10000,    //超时时间
	    dataType:'json',    //返回的数据格式：json/xml/html/script/jsonp/text
	    success:function(data, textStatus, jqXHR){
	    	if (data['Result'] == '1'){
	    		
	    		//------ 执行成功 --------
	    		parent.layer.alert('参数设置成功！', {
	    			icon: 1, 
	    			end:function(){
	    				//window.location.reload(); // 刷新页面
						document.wtsForm.submit();
	    			}
	    		});
	    		
	    	} else {
	    		
	    		//------ 执行失败 --------
	    		parent.layer.alert('发生错误：' + data['RspInfo'], {icon: 2});
	    	}
	    },
	    error:function(xhr, textStatus) {
	    	parent.layer.alert('失败[' + textStatus + ']', {icon: 2});
	    },
	    complete:function(){
	    	
	    }
	})
}
//参数设置提交
function paramSubmit(AppName, AppGroup){
	var ret_json = {};
	ret_json['AppName'] = AppName;
	ret_json['AppGroup'] = AppGroup;
	ret_json['WriteStamp'] = WriteStamp;
	ret_json['UserId'] = curUserId;
	
	var bChanged = false;
	var bCheckPass = true;
	$('#menuTitle li a').each(function(){
		if (tab_is_marked(this.id)){
			bChanged = true;
			//JSON生成
			switch(this.id){
			//运维
			case 'oem':
				//非空check
				if ($('#oem_errinfo').val()=='') {
					parent.layer.msg('[OEM设置] 警告信息不能为空！', {icon: 7});
					$('#oem_errinfo').focus();
					bCheckPass = false;
					return false;
				}
				gen_oem_json(ret_json);
				break;
			case 'trade':
				gen_trade_json(ret_json);
				break;
			case 'ems':
				//非空check
				if ($('#ems_ip').val()==''){
					parent.layer.msg('[EMS算法服务器设置] IP地址不能为空！', {icon: 7});
					//$('#ems_ip').focus();
					bCheckPass = false;
					return false;
				}
				if ($('#ems_port').val()==''){
					parent.layer.msg('[EMS算法服务器设置] 端口不能为空！', {icon: 7});
					$('#ems_port').focus();
					bCheckPass = false;
					return false;
				}
				if ($('#ems_timeout').val()==''){
					parent.layer.msg('[EMS算法服务器设置] 最大超时时间不能为空！', {icon: 7});
					$('#ems_timeout').focus();
					bCheckPass = false;
					return false;
				}
				if(typeof(ret_json['WtsStock.ini'])=='undefined')
					ret_json['WtsStock.ini'] = {};
				ret_json['WtsStock.ini']['EMS'] = {};
				ret_json['WtsStock.ini']['EMS']['IpAddress'] = $('#ems_ip').val();
				ret_json['WtsStock.ini']['EMS']['port'] = $('#ems_port').val();
				ret_json['WtsStock.ini']['EMS']['timeout'] = $('#ems_timeout').val();
				break;
			
			//运营
			case 'etf':
				gen_etf_json(ret_json);
				break;
			case 'bwlist':
				gen_bwlist_json(ret_json);
				break;
				
			//EMS.Manager
			case 'emsmgr':
				//非空check
				if ($('#emsmgr_backuptime').val()==''){
					parent.layer.msg('[EMS.Manager] 数据库备份时间不能为空！', {icon: 7});
					$('#emsmgr_backuptime').focus();
					bCheckPass = false;
					return false;
				}
				if ($('#emsmgr_restarttime').val()==''){
					parent.layer.msg('[EMS.Manager] 重启时间不能为空！', {icon: 7});
					$('#emsmgr_restarttime').focus();
					bCheckPass = false;
					return false;
				}
				gen_emsmgr_json(ret_json);
				break;
			//EMS.Engine
			case 'emseng':
				//非空check
				if ($('#emseng_restarttime').val()==''){
					parent.layer.msg('[EMS.Engine] 重启时间不能为空！', {icon: 7});
					$('#emseng_restarttime').focus();
					bCheckPass = false;
					return false;
				}
				gen_emseng_json(ret_json);
				break;
			//PB.Quotation
			case 'pbquo':
				gen_pbquo_json(ret_json);
				break;
			}
		}
	});
	
	if (!bChanged){
		parent.layer.msg('参数没有进行任何修改！', {icon: 7});
		return;
	}
	if (!bCheckPass)
		return;
	//alert(JSON.stringify(ret_json));return;
	
	//---盘中提交询问框---
	if (MarkertOpen) {
		parent.layer.confirm('确定要在盘中进行参数设置？', {
		  btn: ['是','否'], //按钮
		  title: '<font color=red>盘中参数设置警告</font>'
		}, function(){
		   	//ok
			//---重启询问框---
			if (NeedRestart) {
				parent.layer.confirm(appTypeName + ' 参数设置需重启才能生效，是否继续？', {
					  btn: ['是','否'], //按钮
					  title: '应用重启确认'
				}, function(){
					//ok,提交
					dosubmit(ret_json);					
				}, function(){
				  	//cancel
				});
			} else {
				//提交
				dosubmit(ret_json);
			}
			
		}, function(){
		  	//cancel
		});
	} else {
		//---重启询问框---
		if (NeedRestart) {
			parent.layer.confirm(appTypeName + ' 参数设置需重启才能生效，是否继续？', {
				  btn: ['是','否'], //按钮
				  title: '应用重启确认'
			}, function(){
				//ok,提交
				dosubmit(ret_json);				
			}, function(){
			  	//cancel
			});
		} else {
			//提交
			dosubmit(ret_json);
		}
	}
	
}
//*********************************************************
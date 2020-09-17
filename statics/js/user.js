// 表格点击选中行（新增行时调用）
function refresh_table_selectable(){
    $('table.selectable tr').click(function () {
    	if ($(this).parent().find('tr').children('td').eq(0).html() == '暂无数据') return;
        var trSeq = $(this).parent().find('tr').index($(this));
        if (trSeq > 0){
        	$(this).parent().find('tr').removeClass('selected');
        	$(this).addClass('selected');
        	
        	// 角色列表双击显示角色权限
        	if ($(this).parent().parent().attr('id') == 'table_role'){
	        	document.getElementById("roleFrame").src = '/user.php?method=rolemenu&RoleID='
	        		+ $(this).children('td').eq(0).text() + '&RoleName=' + $(this).children('td').eq(1).text();
        	}
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


// 初始化
$().ready( function(){
	
	// 表格点击选中行
	refresh_table_selectable();
    
});


// ---------------------用户--------------------
function user_oper_apply(tableid, oper_type, UserID, UserName, UserAddr, Tel, Email, UserRoleName) {
	$.ajax({
	    url:'/user.php?method=useroperapply&OperType=' + oper_type,
	    data:{'UserID':UserID, 'UserName':UserName, 'UserAddr':UserAddr, 'Tel':Tel, 'Email':Email, 'UserRoleName':UserRoleName},
	    type:'GET',
	    async:true,    //或false,是否异步
	    timeout:10000,    //超时时间
	    dataType:'json',    //返回的数据格式：json/xml/html/script/jsonp/text
	    success:function(data, textStatus, jqXHR){
	    	if (data['Result'] == '1'){
	    		
	    		//------ 执行成功 --------
	    		if (oper_type=='Add'){
	    			var rowNum = $('#' + tableid).find('tr').length;
	    			var newRow = '<tr>' + 
	    				'<td>' + rowNum + '</td>' + 
	    				'<td>' + UserID + '</td>' + 
	    				'<td>' + UserName + '</td>' + 
	    				'<td class="left">' + UserAddr + '</td>' + 
	    				'<td>' + Tel + '</td>' + 
	    				'<td class="left">' + Email + '</td>' + 
	    				'<td>' + UserRoleName + '</td>' + 
	    			'</tr>';
	    			$('#' + tableid + ' tr:last').after(newRow);
	    			
	    			// 表格点击选中行
	    		    refresh_table_selectable();
	    		}
	    		else if (oper_type=='Mod'){
	    			var trObj = $('#'+tableid).find('tr.selected');
	    			trObj.children('td').eq(1).text(UserID);
	    			trObj.children('td').eq(2).text(UserName);
	    			trObj.children('td').eq(3).text(UserAddr);
	    			trObj.children('td').eq(4).text(Tel);
	    			trObj.children('td').eq(5).text(Email);
	    			trObj.children('td').eq(6).text(UserRoleName);
	    		} 
	    		else if (oper_type=='Del'){
	    			var trObj = $('#'+tableid).find('tr.selected');
	    			trObj.remove();
	    			refresh_table_no(tableid);
	    		}
	    		else {
	    			parent.layer.msg('不支持的操作类型！', {icon: 2});
	    			return;
	    		}
	    		
	    		var msg = '';
	    		switch(oper_type) {
	    		case 'Add':
	    			msg = '用户添加成功';
	    			break;
	    		case 'Mod':
	    			msg = '用户修改成功';
	    			break;
	    		case 'Del':
	    			msg = '用户删除成功';
	    			break;
	    		}
	    		parent.layer.msg(msg, {	icon: 1 });
	    		
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
function user_add(tableid){
	parent.layer.open({
		type: 2,
		title: '用户 - 添加',
		maxmin: false,
		area: ['500px', '380px'],
		content: '/user.php?method=useroper&oper=Add&tableid=' + tableid,
  	});
}
function user_mod(tableid){
	//检查是否有选中行
	var trObj = $('#'+tableid).find('tr.selected');
	if (trObj.length <= 0){
		parent.layer.msg('请选中一行进行修改！', {icon: 7});
		return;
	}
	parent.layer.open({
		type: 2,
		title: '用户 - 修改',
		maxmin: false,
		area: ['500px', '380px'],
		content: '/user.php?method=useroper&oper=Mod&tableid=' + tableid,
  	});
}
function user_del(tableid){
	var trObj = $('#'+tableid).find('tr.selected');
	if (trObj.length <= 0){
		parent.layer.msg('请选中一行进行删除！', {icon: 7});
		return;
	}
	
	var UserID = trObj.children('td').eq(1).text();
	var UserName = trObj.children('td').eq(2).text();
	var UserAddr = trObj.children('td').eq(3).text();
	var Tel = trObj.children('td').eq(4).text();
	var Email = trObj.children('td').eq(5).text();
	var UserRoleName = trObj.children('td').eq(6).text();
	
	//询问框
	parent.layer.confirm('用户名：' + UserID
			+ '<br/>用户姓名：' + UserName,
	{
	  btn: ['确认','取消'], //按钮
	  title: '用户 - 删除确认'
	}, function(){
	   	//ok
	   	// 提交
		user_oper_apply(tableid, 'Del', UserID, UserName, UserAddr, Tel, Email, UserRoleName);

	}, function(){	
	  	//cancel
	});
}


// ---------------------角色--------------------
function role_oper_apply(tableid, oper_type, RoleID, RoleName, RoleRemark) {
	$.ajax({
	    url:'/user.php?method=roleoperapply&OperType=' + oper_type,
	    data:{'RoleID':RoleID, 'RoleName':RoleName, 'RoleRemark':RoleRemark},
	    type:'GET',
	    async:true,    //或false,是否异步
	    timeout:10000,    //超时时间
	    dataType:'json',    //返回的数据格式：json/xml/html/script/jsonp/text
	    success:function(data, textStatus, jqXHR){
	    	if (data['Result'] == '1'){
	    		
	    		//------ 执行成功 --------
	    		if (oper_type=='Add'){
	    			var newRow = '<tr>' + 
	    				'<td>' + RoleID + '</td>' + 
	    				'<td class="left">' + RoleName + '</td>' + 
	    				'<td class="left">' + RoleRemark + '</td>' + 
	    			'</tr>';
	    			$('#' + tableid + ' tr:last').after(newRow);
	    			
	    			// 表格点击选中行
	    		    refresh_table_selectable();
	    		}
	    		else if (oper_type=='Mod'){
	    			var trObj = $('#'+tableid).find('tr.selected');
	    			trObj.children('td').eq(0).text(RoleID);
	    			trObj.children('td').eq(1).text(RoleName);
	    			trObj.children('td').eq(2).text(RoleRemark);
	    		} 
	    		else if (oper_type=='Del'){
	    			var trObj = $('#'+tableid).find('tr.selected');
	    			trObj.remove();
	    		}
	    		else {
	    			parent.layer.msg('不支持的操作类型！', {icon: 2});
	    			return;
	    		}
	    		
	    		var msg = '';
	    		switch(oper_type) {
	    		case 'Add':
	    			msg = '用户角色添加成功';
	    			break;
	    		case 'Mod':
	    			msg = '用户角色修改成功';
	    			break;
	    		case 'Del':
	    			msg = '用户角色删除成功';
	    			break;
	    		}
	    		parent.layer.msg(msg, {	icon: 1 });
	    		
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
function role_add(tableid){
	parent.layer.open({
		type: 2,
		title: '角色 - 添加',
		maxmin: false,
		area: ['500px', '260px'],
		content: '/user.php?method=roleoper&oper=Add&tableid=' + tableid,
  	});
}
function role_mod(tableid){
	//检查是否有选中行
	var trObj = $('#'+tableid).find('tr.selected');
	if (trObj.length <= 0){
		parent.layer.msg('请选中一行进行修改！', {icon: 7});
		return;
	}
	parent.layer.open({
		type: 2,
		title: '角色 - 修改',
		maxmin: false,
		area: ['500px', '260px'],
		content: '/user.php?method=roleoper&oper=Mod&tableid=' + tableid,
  	});
}
function role_del(tableid){
	var trObj = $('#'+tableid).find('tr.selected');
	if (trObj.length <= 0){
		parent.layer.msg('请选中一行进行删除！', {icon: 7});
		return;
	}
	
	var RoleID = trObj.children('td').eq(0).text();
	var RoleName = trObj.children('td').eq(1).text();
	var RoleRemark = trObj.children('td').eq(2).text();
	
	//询问框
	parent.layer.confirm('角色ID：' + RoleID
			+ '<br/>角色名称：' + RoleName
			+ '<br/>备注：' + RoleRemark,
	{
	  btn: ['确认','取消'], //按钮
	  title: '角色 - 删除确认'
	}, function(){
	   	//ok
	   	// 提交
		role_oper_apply(tableid, 'Del', RoleID, RoleName, RoleRemark);

	}, function(){	
	  	//cancel
	});
}
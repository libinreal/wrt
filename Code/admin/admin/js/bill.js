var Bill = {
	order_arr: [
		"bill_id",
		"bill_type",
		"bill_num",
		"due_date",
		"bill_amount",
		"customer_name",
		"drawer",
		"acceptor",
		"check_status",
		"status",
		"operate"
	],
	bill_status: {},
	limit: 0,
	offset: 8,
	total_page: 0,
	current_page: 1,
	url: "BillModel.php",
	entity: "bill",
	bill_type: "",

	getList: function(search){
		if(typeof(search) === "undefined"){
			serach = false;
		}else{
			var condition = {};
			var search_type = $('#search_form select[name=search_type]').val();
			var search_value = $('#search_form input[name=search_value]').val();
			var status = $('#search_form select[name=status]').val();
			var due_date1 = $('#search_form input[name=due_date1]').val();
			var due_date2 = $('#search_form input[name=due_date2]').val();
			if(search_value != ''){
				condition.like = {};
				condition.like[search_type] = search_value
			}
			if(status != ''){
				condition.status = status;
			}
			if(due_date1 != ''){
				condition.due_date1 = due_date1;
			}
			if(due_date2 != ''){
				condition.due_date2 = due_date2;
			}
		}
		if(search != false){
			var params = {"params":{"where":condition,"limit":this.limit, "offset":this.offset}};
		}else{
			var params = {"params":{"limit":this.limit, "offset":this.offset}};
		}
		strJson = createJson("page", this.entity, params);
		var that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				that.total_page = Math.ceil(obj.content.total/that.offset);
				if(obj.content.total == 0){
					var row = "<tr><td colspan='20'>"+createWarn("无数据")+"</td></tr>";
					$("#bill_list>tbody").html(row);
					$("#paginate").html('');
				}else{
					$("#paginate").html(createPaginate(that.url, obj.content.total, that.current_page, that.limit, that.offset));
					var row = "";
					$.each(obj.content.data,function(key, value){
						row += "<tr>";
						for(var i=0;i<that.order_arr.length;i++){
							if(that.order_arr[i] == "operate"){
								var edit = createLink("demo_template.php?section=bill_manage&act=info&id="+value.bill_id, "编辑");
								edit += createLink("demo_template.php?section=bill_manage&act=view&id="+value.bill_id, "查看");
								edit += createLink("demo_template.php?section=bill_manage&act=generate_note&bill_id="+value.bill_id, "生成票据采购额");
								if(obj.content.data[key].status == 0){
									edit += createLink("demo_template.php?section=bill_manage&act=repay&id="+value.bill_id, "还票");
								}
								row += createTd(edit);
								continue;
							}
							if(value[that.order_arr[i]] != null){
								if(that.order_arr[i] == "status"){
									row += createTd(that.bill_status[value["status"]]);
								}else{
									row += createTd(subString(value[that.order_arr[i]],10,true));
								}
							}else{
								row += createTd(createWarn('无数据'));
							}
						}
						row += "</tr>";
						$("#bill_list>tbody").html(row);
					});
				}
			}
			$('#message_area').html('');
		}, "json");
	},

	getInitCreate: function(){
		// 票据类型
		TypeMode.getBillType("bill_type");
		TypeMode.getBillType("bill_status");
		TypeMode.getUsers("customer_id");
		// 调用收付款列表
		TypeMode.getUsers("pay_user_id");
		TypeMode.getAdminUsers("receive_user_id");
		TypeMode.getUserBanks("pay_bank_id", $("#pay_user_id").val());
		TypeMode.getAdminUserBanks("receive_bank_id", $("#receive_user_id").val());
		TypeMode.getUserBanksAccounts("pay_account", $("#pay_user_id").val(), $("#pay_bank_id").val());
		TypeMode.getAdminUserBanksAccounts("receive_bank_id", $("#receive_user_id").val(), $("#receive_bank_id").val());
	},

	getCreate: function(){
		if($("#bill_edit_form").valid() === false){
			return false;
		}
		var form_data = $("#bill_edit_form").FormtoJson();
		strJson = createJson("create", this.entity, form_data);
		that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
			}else if(obj.error == 0){
				redirectToUrl("demo_template.php?section=bill_manage&act=list");
			}
		}, "json");
	},

	getEdit: function(){
		var id = getQueryStringByName('id');
		if(id===""||!validateNumber(id)){
			return false;
		}
		var params = {"bill_id":id};
		strJson = createJson("editInit", this.entity, params);
		var that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				// 初始化列表
				$.each(obj.content.init,function(key, value){
					var row = "";
					if(key == "payers"){
						$.each(value, function(k, v){
							row += appendOption(v.user_id, v.user_name)
						});
						$("select[name=pay_user_id]").append(row);
					}else if(key == "receivers"){
						$.each(value, function(k, v){
							row += appendOption(v.user_id, v.user_name)
						});
						$("select[name=receive_user_id]").append(row);						
					}else{
						if($("select[name="+key+"]").length){
							$.each(value, function(k, v){
								row += appendOption(k, v)
							});
							$("select[name="+key+"]").append(row);
						}
					}
				});
				// 绑定数据
				// 调用收付款列表
				$.each(obj.content.info, function(key, value){
					if($("input[name="+key+"]").length){
						var o = "input[name="+key+"]";
						if($(o).attr("type") == "text"){
							$(o).val(value);
						}
						if($(o).attr("type") == "radio"){
							$("input[name="+key+"][value="+value+"]").attr("checked","1");
						}
					}
					if($("textarea[name="+key+"]").length){
						$("textarea[name="+key+"]").text(value);
					}
					if($("select[name="+key+"]").length){
						$("select[name="+key+"]>option[value="+value+"]").attr("selected","selected");
					}
				});
				TypeMode.getUsers("customer_id", obj.content.info.customer_id);
				TypeMode.getUserBanks("pay_bank_id", obj.content.info.pay_user_id, obj.content.info.pay_bank_id);
				TypeMode.getUserBanksAccounts("pay_account", obj.content.info.pay_user_id, obj.content.info.pay_bank_id, obj.content.info.pay_account);
				TypeMode.getAdminUserBanks("receive_bank_id", obj.content.info.receive_user_id, obj.content.info.pay_bank_id);
				TypeMode.getAdminUserBanksAccounts("receive_bank_id", obj.content.info.receive_user_id, obj.content.info.receive_bank_id);
			}
			$('#message_area').html('');
		},"json");
	},

	getView: function(){
		var id = getQueryStringByName('id');
		if(id===""||!validateNumber(id)){
			return false;
		}
		var params = {"bill_id":id};
		strJson = createJson("editInit", this.entity, params);
		var that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				// 初始化列表
				TypeMode.getUsers("customer_id");
				$.each(obj.content.init,function(key, value){
					var row = "";
					if(key == "payers"){
						$.each(value, function(k, v){
							row += appendOption(v.user_id, v.user_name)
						});
						$("select[name=pay_user_id]").append(row);
					}else if(key == "receivers"){
						$.each(value, function(k, v){
							row += appendOption(v.user_id, v.user_name)
						});
						$("select[name=receive_user_id]").append(row);						
					}else{
						if($("select[name="+key+"]").length){
							$.each(value, function(k, v){
								row += appendOption(k, v)
							});
							$("select[name="+key+"]").append(row);
						}
					}
				});
				// 绑定数据
				// 调用收付款列表
				$.each(obj.content.info, function(key, value){
					if($("#"+key).length){
						$("#"+key).text(value)
					}
					if($("select[name="+key+"]").length){
						$("select[name="+key+"]>option[value="+value+"]").attr("selected","selected");
					}
				});
				TypeMode.getUserBanks("pay_bank_id", obj.content.info.pay_user_id);
				TypeMode.getUserBanksAccounts("pay_account", obj.content.info.pay_user_id, obj.content.info.pay_bank_id);
				TypeMode.getAdminUserBanks("receive_bank_id", obj.content.info.receive_user_id);
				TypeMode.getAdminUserBanksAccounts("receive_bank_id", obj.content.info.receive_user_id, obj.content.info.receive_bank_id);
			}
			$('#message_area').html('');
		},"json");
	},

	putUpdate: function(){
		if($("#bill_edit_form").valid() === false){
			return false;
		}
		var form_data = $("#bill_edit_form").FormtoJson();
		var id = getQueryStringByName('id');
		if(id===""||!validateNumber(id)){
			return false;
		}
		form_data.bill_id = id;
		strJson = createJson("update", this.entity, form_data);
		that = this
		$.post(this.url, strJson, function(obj){
			console.log(obj);
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
			}else if(obj.error == 0){
				$('#message_area').html(createTip(obj.message));
				redirectToUrl("demo_template.php?section=bill_manage&act=list");
			}
		}, "json");
	}
}
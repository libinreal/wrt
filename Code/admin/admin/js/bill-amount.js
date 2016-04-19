var BillAmount = {
	order_arr: [
		"bill_amount_log_id",
		"create_by",
		"create_time",
		"customer_name",
		"amount",
		"amount_type",
		"review_status",
		"operate"
	],
	note_type: {
		0:"商票",
		1:"现金",
		2:"承兑"
	},
	limit: 0,
	offset: 20,
	total_page: 0,
	current_page: 1,
	url: "BillAmountModel.php",
	entity: "bill_amount_log",

	getList: function(search){
		if(typeof(search) === "undefined"){
			serach = false;
		}else{
			var condition = {};
			var user_name = $('#search_form input[name=user_name]').val();
			var due_date1 = $('#search_form input[name=due_date1]').val();
			var due_date2 = $('#search_form input[name=due_date2]').val();
			var amount_type = $('#search_form select[name=amount_type] option:selected').val();
			if(user_name != ''){
				condition.like = {"user_name":user_name};
			}
			if(due_date1 != ''){
				condition.due_date1 = due_date1;
			}
			if(due_date2 != ''){
				condition.due_date2 = due_date2;
			}
			if(amount_type != ''){
				condition.amount_type = amount_type;
			}
		}
		if(search != false){
			if(search == "search"){
				this.limit = 0;
			}
			var params = {"params":{"where":condition, "limit":this.limit, "offset":this.offset}};
		}else{
			var params = {"params":{"limit":this.limit, "offset":this.offset}};
		}
		var strJson = createJson("page", this.entity, params);
		var that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				that.total_page = Math.ceil(obj.content.total/that.offset);
				if(obj.content.total == 0){
					var row = "<tr><td colspan='20'>"+createWarn("无数据")+"</td></tr>";
					$("#bill_amount_list>tbody").html(row);
					$("#paginate").html('');
				}else{
					$("#paginate").html(createPaginate(that.url, obj.content.total, that.current_page, that.limit, that.offset));
					var row = "";
					$.each(obj.content.data,function(key, value){
						row += "<tr>";
						for(var i=0;i<that.order_arr.length;i++){
							if(that.order_arr[i] == "amount_type"){
								row += createTd(that.note_type[value.amount_type]);
								continue;
							}
							if(that.order_arr[i] == "operate"){
								if(value.amount_type == 1 || value.amount_type == 2){
									var edit = "";
									var edit = createLink("demo_template.php?section=bill_manage&act=generate_cash_view&log_id="+value.bill_amount_log_id, "详情");
								}else{
									var edit = "";
									var edit = createLink("demo_template.php?section=bill_manage&act=generate_view&log_id="+value.bill_amount_log_id, "详情");
								}
								row += createTd(edit);
								continue;
							}
							if(value[that.order_arr[i]] != null){
								row += createTd(subString(value[that.order_arr[i]],10,true));
							}else{
								row += createTd(createWarn('无数据'));
							}
						}
						row += "</tr>";
					});
					$("#bill_amount_list>tbody").html(row);
				}
			}
		}, "json");
	},

	getCreateAction: function(){
		if($("#bill_purchase_form").valid() === false){
			return false;
		}
		if($("select[name=user_id]").length>0){
			$("input[name=user_name]").val($("#user_id option:selected").text());
		}
		if($("input[name=amount_rate]").length>0){
			$("input[name=amount_rate]").val($("#discount_rate").val());
		}
		var form_data = $("#bill_purchase_form").FormtoJson();
		strJson = createJson("create", this.entity, form_data);
		that = this
		console.log(strJson);
		$.post(this.url, strJson, function(obj){
			console.log(obj);
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$('#message_area').html(createTip(obj.message));
				redirectToUrl("demo_template.php?section=bill_manage&act=order_list");
			}
		}, "json");
	},

	// 采购额度生成单（商票）初始化
	getAddInitAction: function(){
		var bill_id = getQueryStringByName('bill_id');
		if(bill_id===""||!validateNumber(bill_id)){
			return false;
		}
		$("#bill_id").text(bill_id);
		$("input[name=bill_id]").val(bill_id);
		var params = {"bill_id":bill_id, "type": 0};
		strJson = createJson("addInit", this.entity, params);
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$.each(obj.content.info, function(k, v){
					if(k == "discount_rate"){
						$("#"+k).val(parseInt(v));
					}else{
						$("input[name="+k+"]").val(v);
						$("#"+k).text(v);
					}
				});
				var bill_amount = parseFloat(obj.content.info.bill_amount);
				var discount_rate = parseFloat($("#discount_rate").val());
				$("#amount").val(bill_amount*discount_rate/100);
				$("#operate_button").html(createButton('BillAmount.getCreateAction()', '添加'));
			}
			
		}, "json");
	},

	// 采购额度生成单（现金）初始化
	getAddInitCashAction: function(){
		var params = {"bill_id":0, "type": 1};
		strJson = createJson("addInit", this.entity, params);
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				var row = "";
				$.each(obj.content.init.amount_type, function(k, v){
					row += appendOption(k, v);
				});
				$("#amount_type").html(row);
				var row = "";
				$.each(obj.content.init.customer, function(k, v){
					row += appendOption(v.user_id, v.user_name);
				});
				$("#user_id").html(row);
				$("#operate_button").html(createButton('BillAmount.getCreateAction()', '添加'));
			}
		}, "json");
	},

	getUpdateAction: function(type){
		var log_id = getQueryStringByName('log_id');
		if($("#bill_purchase_form").valid() === false){
			return false;
		}
		var form_data = $("#bill_purchase_form").FormtoJson();
		form_data.bill_amount_log_id = log_id;
		//form_data.user_name = $("#user_id>option[selected]").text();
		strJson = createJson("update", this.entity, form_data);
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
			}else{
				$('#message_area').html(createTip(obj.message));
				if(type == "note"){
					redirectToUrl("demo_template.php?section=bill_manage&act=generate_view&log_id="+log_id);
				}else if(type == "cash"){
					redirectToUrl("demo_template.php?section=bill_manage&act=generate_cash_view&log_id="+log_id);
				}
			}
		}, "json");
	},

	// 编辑初始化
	getEditInitAction: function(type){
		var log_id = getQueryStringByName('log_id');
		if(log_id == "" || !validateNumber(log_id)){
			return false;
		}
		var params = {"bill_amount_log_id": log_id};
		strJson = createJson("editInit", this.entity, params);
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				// 初始化
				if(obj.content.init.length != ""){
					var row = "";
					$.each(obj.content.init.amount_type, function(k, v){
						row += appendOption(k, v);
					});
					$("#amount_type").html(row);
					var row = "";
					$.each(obj.content.init.customer, function(k, v){
						row += appendOption(v.user_id, v.user_name);
					});
					$("#user_id").html(row);
				}
				$.each(obj.content.info, function(k, v){
					if($("select[name="+k+"]").length > 0){
						$("select[name="+k+"]>option[value="+v+"]").attr("selected", "selected");
					}
					if($("input[name="+k+"]").length > 0){
						$("input[name="+k+"]").val(v);
					}
					if($("textarea[name="+k+"]").length > 0){
						$("textarea[name="+k+"]").text(v);
					}
					if($("td#"+k).length > 0){
						$("td#"+k).text(v);
					}
				});
				$("#operate_button").html(createButton('BillAmount.getUpdateAction()', '保存'));
			}
		}, "json");
	},

	// 额度生成详情
	getView: function(type){
		var log_id = getQueryStringByName('log_id');
		if(log_id == "" || !validateNumber(log_id)){
			return false;
		}
		var params = {"bill_amount_log_id": log_id};
		strJson = createJson("find", this.entity, params);
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				// 初始化
				var row = "";
				var operate_button = "";
				$.each(obj.content, function(k, v){
					if($("select[name="+k+"]").length > 0){
						$("select[name="+k+"]>option[value="+v+"]").attr("selected", "selected");
					}
					if($("td#"+k).length > 0){
						if(k == "review_status"){
							if(v == 0){
								v = "未审核";
								if(type == 1){
									operate_button = createButton('redirectToUrl("demo_template.php?section=bill_manage&act=generate_edit&log_id='+log_id+'")', '编辑');
								}else{
									operate_button = createButton('redirectToUrl("demo_template.php?section=bill_manage&act=generate_note_edit&log_id='+log_id+'")', '编辑');
								}
								if(obj.content.is_review == 1){
									operate_button = operate_button + createButton('BillAmount.checkReview(2)', '审核不通过');
									operate_button = operate_button + createButton('BillAmount.checkReview(1)', '审核通过');
								}
							}else if(v == 1){
								v = "已通过";
							}else if(v == 2){
								v = "未通过";
							}
						}
						$("td#"+k).text(v);
					}
				});
				$("#operate_button span").html(operate_button);
			}
		}, "json");
	},

	//审核单据
	checkReview: function(status){
		var log_id = getQueryStringByName('log_id');
		if(log_id == "" || !validateNumber(log_id)){
			return false;
		}
		var params = {"bill_amount_log_id":log_id, "review_status":status};
		var strJson = createJson("review", this.entity, params);
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				console.log(obj)
				$("#operate_button span").html("");
				if(status == 1){
					$("td#review_status").text("已通过");
				}else if(status == 2){
					$("td#review_status").text("未通过");
				}
			}
		}, "json");
	}
}
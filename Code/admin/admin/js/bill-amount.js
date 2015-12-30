var BillAmount = {
	order_arr: [
		"bill_amount_log_id",
		"audit_status",
		"create_by",
		"create_time",
		"modify_by",
		"modify_time",
		"audit_time",
		"audit_by",
		"user_id",
		"amount",
		"amount_type",
		"bill_date",
		"operate"
	],
	limit: 0,
	offset: 8,
	total_page: 0,
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
			if(user_name != ''){
				condition.like = {"user_name":user_name};
			}
			if(due_date1 != ''){
				condition.due_date1 = due_date1;
			}
			if(due_date2 != ''){
				condition.due_date2 = due_date2;
			}
		}
		if(search != false){
			var params = {"params":{"where":condition, "limit":this.limit, "offset":this.offset}};
		}else{
			var params = {"params":{"limit":this.limit, "offset":this.offset}};
		}
		strJson = createJson("page", this.entity, params);
		that = this
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
					$("#paginate").html(createPaginate(that.url, obj.content.total, that.limit, that.offset));
					var row = "";
					$.each(obj.content.data,function(key, value){
						row += "<tr>";
						for(var i=0;i<that.order_arr.length;i++){
							if(that.order_arr[i] == "operate"){
								if(value.amount_type == 0){
									var edit = createLink("demo_template.php?section=bill_manage&act=generate_note&log_id="+value.bill_amount_log_id, "详情");
								}else if(value.amount_type == 1 || value.amount_type == 2){
									var edit = createLink("demo_template.php?section=bill_manage&act=generate&log_id="+value.bill_amount_log_id, "详情");
								}else{
									var edit = createTd(createWarn('无数据'));
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
						$("#bill_amount_list>tbody").html(row);
					});
				}
			}
			$('#message_area').html('');
		}, "json");
	},

	getCreateAction: function(){
		if($("#bill_purchase_form").valid() === false){
			return false;
		}
		var form_data = $("#bill_purchase_form").FormtoJson();
		strJson = createJson("create", this.entity, form_data);
		that = this
		console.log(strJson);
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$('#message_area').html(createError(obj.message));
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
		var params = {"bill_id":bill_id};
		strJson = createJson("addInit", this.entity, params);
		$.post(this.url, strJson, function(obj){ 
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$.each(obj.content.info, function(k, v){
					if(k == "discount_rate"){
						$("#"+k).val(v);
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
			$('#message_area').html('');
		}, "json");
	},

	getUpdateAction: function(){
		var log_id = getQueryStringByName('log_id');
		if($("#bill_purchase_form").valid() === false){
			return false;
		}
		var form_data = $("#bill_purchase_form").FormtoJson();
		form_data.bill_amount_log_id = log_id;
		form_data.user_name = $("#user_id>option[selected]").text();
		strJson = createJson("update", this.entity, form_data);
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
			}else{
				$('#message_area').html(createError(obj.message));
			}
		}, "json");
	}
}
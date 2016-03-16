var BillAjust = {
	order_arr: [
		"user_id",
		"custom_no",
		"customer_name",
		"user_name",
		"email",
		"mobile",
		"operate"
	],
	bill_status: {},
	limit: 0,
	offset: 8,
	total_page: 0,
	current_page: 1,
	url: "BillAdjustModel.php",
	entity: "bill_adjust",

	getList: function(search){
		if(typeof(search) === "undefined"){
			serach = false;
		}else{
			var condition = {};
			var user_name = $('#search_form input[name=user_name]').val();
			var customer_name = $('#search_form input[name=customer_name]').val();
			var mobile = $('#search_form input[name=mobile]').val();
			condition.like = {};
			if(user_name != ''){
				condition.like['user_name'] = user_name;
			}
			if(customer_name != ''){
				condition.like['customer_name'] = customer_name;
			}
			if(mobile != ''){
				condition.like['mobile'] = mobile;
			}
		}
		if(search != false){
			if(search == "search"){
				this.limit = 0;
			}
			var params = {"params":{"where":condition,"limit":this.limit, "offset":this.offset}};
		}else{
			var params = {"params":{"limit":this.limit, "offset":this.offset}};
		}
		var strJson = createJson("page", this.entity, params);
		var that = this
		console.log(strJson)
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				that.total_page = Math.ceil(obj.content.total/that.offset);
				if(obj.content.total == 0){
					var row = "<tr><td colspan='20'>"+createWarn("无数据")+"</td></tr>";
					$("#user_list>tbody").html(row);
					$("#paginate").html('');
				}else{
					$("#paginate").html(createPaginate(that.url, obj.content.total, that.current_page, that.limit, that.offset));
					var row = "";
					console.log(obj.content.data)
					$.each(obj.content.data,function(key, value){
						row += "<tr>";
						for(var i=0;i<that.order_arr.length;i++){
							if(that.order_arr[i] == "operate"){
								var edit = createLink("demo_template.php?section=bill_manage&act=assign_note&id="+value.user_id, "分配商票采购额度");
								edit += createLink("demo_template.php?section=bill_manage&act=assign_cash&id="+value.user_id, "分配现金采购额度");
								edit += createLink("demo_template.php?section=bill_manage&act=adjust&id="+value.user_id, "额度调整");
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
					$("#user_list>tbody").html(row);
				}
			}
			
		}, "json");
	},

	getAddInitAction: function(){
		var id = getQueryStringByName('id');
		if(id===""||!validateNumber(id)){
			return false;
		}
		params = {"user_id":id}
		strJson = createJson("addInit", this.entity, params);
		that = this
		console.log(strJson)
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				// 初始化
				var from_order = 0;
				var from_row = "";
				var from_contract_id = $('select[name=from_contract_id]').val();
				var to_order = 0;
				var to_row = "";
				var	to_contract_id = $('select[name=to_contract_id]').val();
				$.each(obj.content.init.contracts, function(k, v){
					if(from_contract_id != "" && from_contract_id == v.contract_id){
						from_row += appendOption(v.contract_id, v.contract_name, 1);
						from_order = k;
					}else{
						from_row += appendOption(v.contract_id, v.contract_name);
					}
					if(to_contract_id != "" && to_contract_id == v.contract_id){
						to_row += appendOption(v.contract_id, v.contract_name, 1);
						to_order = k;
					}else{
						to_row += appendOption(v.contract_id, v.contract_name);
					}
				});
				$("select[name=from_contract_id]").html(from_row);
				$("select[name=to_contract_id]").html(to_row);
				var row = "";
				var from_type = $("#from_type").val();
				$.each(obj.content.init.type, function(k, v){
					if(from_type != "" && from_type == k){
						row += appendOption(k, v, 1);
					}else{
						row += appendOption(k, v);
					}
				});
				$("#from_type").html(row);
				$("#to_type").html(row);
				//
				if(from_type == 0 || from_type == null){
					$("#from_amount_valid").text(obj.content.init.contracts[from_order].bill_amount_valid);
				}else{
					$("#from_amount_valid").text(obj.content.init.contracts[from_order].cash_amount_valid);
				}
				if(from_type == 0 || from_type == null){
					$("#to_amount_valid").text(obj.content.init.contracts[to_order].bill_amount_valid);
				}else{
					$("#to_amount_valid").text(obj.content.init.contracts[to_order].cash_amount_valid);
				}
				$("#from_adjust_amount").val(0);
				$("#to_adjust_amount").val(0);
			}
			
		}, "json");
	},

	getCreateAction: function(){
		if($('select[name=from_contract_id]').val() == $('select[name=to_contract_id]').val()){
			$('#message_area').html(createError('合同编号重复'));
			return false;
		}
		if($("#adjust_form").valid() == false){
        	return false;
    	}
		// 表单数据
		var form_data = $("#adjust_form").FormtoJson();
		strJson = createJson("create", this.entity, form_data);
		that = this
		console.log(strJson);
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$('#message_area').html(createTip(obj.message));
				return false;
			}
			
		}, "json");	
	}
}
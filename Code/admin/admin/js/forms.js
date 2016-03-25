var Forms = {
	customer_arr: [
		"order_sn",
		"goods_sn",
		"goods_name",
		"attr",
		"goods_number_arr_buyer",
		"goods_price_arr_buyer",
		"order_amount_arr_buyer",
		"remark"
	],
	supplier_arr: [
		"order_sn",
		"goods_sn",
		"goods_name",
		"attr",
		"goods_number_arr_saler",
		"goods_price_arr_saler",
		"order_amount_arr_saler",
		"remark"
	],
	project_arr: [
		"goods_name",
		"goods_sn",
		"order_sn",
		"goods_price_arr_buyer",
		"goods_number_arr_buyer",
		"order_amount_arr_buyer",
		"purchase_sn",
		"suppliers_name",
		"goods_price_arr_saler",
		"goods_number_arr_saler",
		"order_amount_arr_saler",
		"differential"
	],
	limit: 0,
	offset: 8,
	total_page: 0,
	current_page: 1,
	url: "StatementsModel.php",
	entity: "order_info",

	getCustomer: function(search){
		if(typeof(search) === "undefined"){
			serach = false;
		}else{
			var condition = {};
			var contract_name = $('#search_form input[name=contract_name]').val();
			var contract_sn= $('#search_form input[name=contract_sn]').val();
			var due_date1 = $('#search_form input[name=due_date1]').val();
			var due_date2 = $('#search_form input[name=due_date2]').val();
			if(contract_name != ''){
				condition.like = {};
				condition.like['contract_name'] = contract_name
			}
			if(contract_sn != ''){
				condition.like = {};
				condition.like['contract_sn'] = contract_sn
			}
			if(due_date1 != ''){
				condition.due_date1 = due_date1;
			}
			if(due_date2 != ''){
				condition.due_date2 = due_date2;
			}
		}
		if(search != false){
			if(search == 'search'){
				this.limit = 0;
			}
			var params = {"params":{"where":condition,"limit":this.limit, "offset":this.offset}};
		}else{
			var params = {"params":{"limit":this.limit, "offset":this.offset}};
		}
		strJson = createJson("customPage", this.entity, params);
		var that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				that.total_page = Math.ceil(obj.content.total/that.offset);
				if(obj.content.total == 0){
					var row = "<tr><td colspan='20'>"+createWarn("无数据")+"</td></tr>";
					$("#list>tbody").html(row);
					$("#list>tfoot").html('');
					$("#paginate").html('');
					return false;
				}else{
					$("#paginate").html(createPaginate(that.url, obj.content.total, that.current_page, that.limit, that.offset));
					var row = "";
					$.each(obj.content, function(key, value){
						if($("#"+key).length == 1){
							$("#"+key).html(value);	
						}
					});
					$.each(obj.content.data,function(key, value){
						row += "<tr>";
						for(var i=0;i<that.customer_arr.length;i++){
							if(value[that.customer_arr[i]] != null){
								if(key == "goods_number_arr_buyer"){
									row += createTd(value[that.project_arr[i]] + value.unit);
								}else if(key == "goods_number_arr_saler"){
									row += createTd(value[that.project_arr[i]] + value.unit);
								}else{
									row += createTd(subString(value[that.customer_arr[i]],20,true));
								}
							}else{
								row += createTd(createWarn('无数据'));
							}
						}
						row += "</tr>";
						$("#list>tbody").html(row);
					});
				}
			}
			
		}, "json");
	},

	getSupplier: function(search){
		if(typeof(search) === "undefined"){
			serach = false;
		}else{
			var condition = {};
			var customer_name = $('#search_form input[name=customer_name]').val();
			var due_date1 = $('#search_form input[name=due_date1]').val();
			var due_date2 = $('#search_form input[name=due_date2]').val();
			if(customer_name != ''){
				condition.like = {};
				condition.like['customer_name'] = customer_name
			}
			if(due_date1 != ''){
				condition.due_date1 = due_date1;
			}
			if(due_date2 != ''){
				condition.due_date2 = due_date2;
			}
		}
		if(search != false){
			if(search == 'search'){
				this.limit = 0;
			}
			var params = {"params":{"where":condition,"limit":this.limit, "offset":this.offset}};
		}else{
			var params = {"params":{"limit":this.limit, "offset":this.offset}};
		}
		strJson = createJson("suppliersPage", this.entity, params);
		var that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				that.total_page = Math.ceil(obj.content.total/that.offset);
				if(obj.content.total == 0){
					var row = "<tr><td colspan='20'>"+createWarn("无数据")+"</td></tr>";
					$("#list>tbody").html(row);
					$("#list>tfoot").hide()
					$("#paginate").html('');
					return false;
				}else{
					$("#paginate").html(createPaginate(that.url, obj.content.total, that.current_page, that.limit, that.offset));
					var row = "";
					$("#list>tfoot").show();
					$.each(obj.content, function(key, value){
						if($("#"+key).length == 1){
							$("#"+key).html(value);	
						}
					});
					$.each(obj.content.data,function(key, value){
						row += "<tr>";
						for(var i=0;i<that.supplier_arr.length;i++){
							if(value[that.supplier_arr[i]] != null){
								if(key == "goods_number_arr_buyer"){
									row += createTd(value[that.project_arr[i]] + value.unit);
								}else if(key == "goods_number_arr_saler"){
									row += createTd(value[that.project_arr[i]] + value.unit);
								}else{
									row += createTd(subString(value[that.supplier_arr[i]],20,true));
								}
							}else{
								row += createTd(createWarn('无数据'));
							}
						}
						row += "</tr>";
						$("#list>tbody").html(row);
					});
				}
			}
			
		}, "json");
	},

	getProject: function(search){
		if(typeof(search) === "undefined"){
			serach = false;
		}else{
			var condition = {};
			var contract_name = $('#search_form input[name=contract_name]').val();
			var due_date1 = $('#search_form input[name=due_date1]').val();
			var due_date2 = $('#search_form input[name=due_date2]').val();
			if(contract_name != ''){
				condition.like = {};
				condition.like['contract_name'] = contract_name
			}
			if(due_date1 != ''){
				condition.due_date1 = due_date1;
			}
			if(due_date2 != ''){
				condition.due_date2 = due_date2;
			}
		}
		if(search != false){
			if(search == 'search'){
				this.limit = 0;
			}
			var params = {"params":{"where":condition,"limit":this.limit, "offset":this.offset}};
		}else{
			var params = {"params":{"limit":this.limit, "offset":this.offset}};
		}
		strJson = createJson("contractPage", this.entity, params);
		var that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				that.total_page = Math.ceil(obj.content.total/that.offset);
				if(obj.content.total == 0){
					var row = "<tr><td colspan='20'>"+createWarn("无数据")+"</td></tr>";
					$("#list>tbody").html(row);
					$("#list>tfoot").hide()
					$("#paginate").html('');
					return false;
				}else{
					$("#paginate").html(createPaginate(that.url, obj.content.total, that.current_page, that.limit, that.offset));
					var row = "";
					$("#list>tfoot").show();
					$.each(obj.content, function(key, value){
						if($("#"+key).length == 1){
							$("#"+key).html(value);	
						}
					});
					$.each(obj.content.data,function(key, value){
						row += "<tr>";
						for(var i=0;i<that.project_arr.length;i++){
							if(value[that.project_arr[i]] != null){
								if(key == "goods_number_arr_buyer"){
									row += createTd(value[that.project_arr[i]] + value.unit);
								}else if(key == "goods_number_arr_saler"){
									row += createTd(value[that.project_arr[i]] + value.unit);
								}else{
									row += createTd(value[that.project_arr[i]]);
								}
							}else{
								row += createTd(createWarn('无数据'));
							}
						}
						row += "</tr>";
						$("#list>tbody").html(row);
					});
				}
			}
			
		}, "json");
	},

	customPageExport: function(){
		var act = "customPageExport";
		var params = {"limit":this.limit, "offset":this.offset};
		var formData = $("#search_form").FormtoJson();
		$.each(formData, function(k, v){
			if(v != ""){
				params[k] = v;
			}
		});
		var getData = "?act="+encodeURI(act)+"&params="+encodeURI(JSON.stringify(params));
		window.open(this.url + getData, '_blank')
	},

	suppliersPageExport: function(){
		var act = "suppliersPageExport";
		var params = {"limit":this.limit, "offset":this.offset};
		var formData = $("#search_form").FormtoJson();
		$.each(formData, function(k, v){
			if(v != ""){
				params[k] = v;
			}
		});
		var getData = "?act="+encodeURI(act)+"&params="+encodeURI(JSON.stringify(params));
		window.open(this.url + getData, '_blank')		
	},

	contractPageExport: function(){
		var act = "contractPageExport";
		var params = {"limit":this.limit, "offset":this.offset};
		var formData = $("#search_form").FormtoJson();
		$.each(formData, function(k, v){
			if(v != ""){
				params[k] = v;
			}
		});
		var getData = "?act="+encodeURI(act)+"&params="+encodeURI(JSON.stringify(params));
		window.open(this.url + getData, '_blank')
	}
}
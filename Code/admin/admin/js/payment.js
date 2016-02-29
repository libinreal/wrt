var Payment = {
	order_arr: [
		"order_pay_id",
		"suppliers_name",
		"create_time",
		"order_total",
		"pay_status",
		"operate"
	],
	goods_arr: [
		"order_sn",
		"goods_sn",
		"goods_name",
		"attributes",
		"goods_price",
		"goods_number",
		"shipping_fee",
		"order_amount"
	],
	payment_status: [],
	limit: 0,
	offset: 8,
	total_page: 0,
	current_page: 1,
	url: "OrderPayModel.php",
	entity: "OrderPay",

	getList: function(search){
		if(typeof(search) === "undefined"){
			serach = false;
		}else{
			var condition = {};
			condition.like = {};
			var order_sn = $('#search_form input[name=order_sn]').val();
			var pay_status = $('#search_form select[name=pay_status]').val();
			var suppliers_name = $('#search_form input[name=suppliers_name]').val();
			var contract_sn = $('#search_form select[name=contract_sn]').val();
			var purchase_order_sn = $('#search_form input[name=purchase_order_sn]').val();
			if(order_sn != ''){
				condition.like['order_sn'] = order_sn
			}
			if(pay_status != ''){
				condition.like['pay_status'] = pay_status
			}
			if(suppliers_name != ''){
				condition.like['suppliers_name'] = suppliers_name
			}
			if(contract_sn != ''){
				condition.like['contract_sn'] = contract_sn
			}
			if(purchase_order_sn != ''){
				condition.like['purchase_order_sn'] = purchase_order_sn
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
					$("#payment_order_list>tbody").html(row);
					$("#paginate").html('');
				}else{
					$("#paginate").html(createPaginate(that.url, obj.content.total, that.current_page, that.limit, that.offset));
					var row = "";
					$.each(obj.content.data,function(key, value){
						row += "<tr>";
						for(var i=0;i<that.order_arr.length;i++){
							if(that.order_arr[i] == "operate"){
								var edit = createLink("demo_template.php?section=payment&act=detail&id="+value.order_pay_id, "详细");
								row += createTd(edit);
								continue;
							}
							if(value[that.order_arr[i]] != null){
								if(that.order_arr[i] == "pay_status"){
									row += createTd(that.payment_status[value[that.order_arr[i]]]);
								}else{
									row += createTd(value[that.order_arr[i]]);
								}
							}else{
								row += createTd(createWarn('无数据'));
							}
						}
						row += "</tr>";
					});
					$("#payment_order_list>tbody").html(row);
				}
			}
			$('#message_area').html('');
		}, "json");
	},

	getDetail: function(){
		var order_pay_id = getQueryStringByName("id");
		var params = {"order_pay_id":order_pay_id};
		strJson = createJson("find", this.entity, params);
		that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				var row = "";
				$.each(obj.content.file_0,function(key, value){
					row += "<tr>";
					row += createTd(value.upload_name);
					var edit = createButton("Supplier.delUpload(this,"+value.upload_id+")", "删除");
					edit += "<input type='hidden' name='upload_id[]' value='"+value.upload_id+"'>";
					row += createTd(edit);
				});
				$("#left_list>tbody").html(row);
				row ="";
				$.each(obj.content.file_1,function(key, value){
					row += "<tr>";
					row += createTd(value.upload_name);
					var edit = createButton("Supplier.delUpload(this,"+value.upload_id+")", "删除");
					edit += "<input type='hidden' name='upload_id[]' value='"+value.upload_id+"'>";
					row += createTd(edit);
				});
				$("#right_list>tbody").html(row);
				$.each(obj.content.data, function(k,v){
					if($("#"+k).length){
						$("#"+k).text(v);
					}
				});
				if(obj.content.data.goods_list.length == 0){
					var row = "<tr><td colspan='20'>"+createWarn("无数据")+"</td></tr>";
					$("#detail_list>tbody").html(row);
				}else{
					var row = "";
					$.each(obj.content.data.goods_list, function(key, value){
						row += "<tr>";
						for(var i=0;i<that.goods_arr.length;i++){
							if(value[that.goods_arr[i]] != null){
								row += createTd(value[that.goods_arr[i]]);
							}else{
								row += createTd(createWarn('无数据'));
							}
						}
						row += "</tr>";
					});
					$("#detail_list>tbody").html(row);
					// 按钮状态更新
					var button = '';
					$.each(obj.content.buttons, function(k, v){
						button += '<input type="button" class="button" onclick="Payment.changeButtonStatus(this)" value="'+v+'" >';
					});
					$("#handle_button>span").html(button);
				}
			}
			$('#message_area').html('');
		}, "json");
	},

	setHandle: function(){
		var params = {"params":{"limit":this.limit, "offset":this.offset}};
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
					$("#>tbody").html(row);
					$("#paginate").html('');
				}else{
					$("#paginate").html(createPaginate(that.url, obj.content.total, that.limit, that.offset));
				}
			}
			$('#message_area').html('');
		}, "json");
	},

	changeButtonStatus: function(handle){
		var order_pay_id = getQueryStringByName('id');
		if(order_pay_id===""||!validateNumber(order_pay_id)){
			return false;
		}
		var button_name = $(handle).val();
		var params = {"order_pay_id":order_pay_id, "button": button_name};

		var strJson = createJson("uPayStat", "uPayStat", params);
		var that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$('#message_area').html(createTip(obj.message));
				that.updateButtonStatus();
			}
		}, "json");
	},

	updateButtonStatus: function(){
		var order_pay_id = getQueryStringByName("id");
		var params = {"order_pay_id":order_pay_id};
		strJson = createJson("find", this.entity, params);
		that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				// 按钮状态更新
				var button = '';
				$.each(obj.content.buttons, function(k, v){
					button += '<input type="button" class="button" onclick="Payment.changeButtonStatus(this)" value="'+v+'" >';
				});
				$("#handle_button>span").html(button);
			}
			$('#message_area').html('');
		}, "json");
	}
}
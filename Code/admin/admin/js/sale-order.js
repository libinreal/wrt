var SaleOrder = {
	order_arr: [
		"order_sn",
		"customer_name",
		"customer_id",
		"contract_sn",
		"contract_name",
		"add_time",
		"best_time",
		"goods_amount",
		"order_status",
		"operate"
	],
	order_detail_arr: [
		"goods_sn",
		"goods_name",
		"attr",
		"goods_number",
		"send_number",
		"remain_number",
		"goods_price",
		"subtotal",
		"suppliers_name",
		"add_time",
		"operate"
	],
	suborder_arr: [
		"add_time",
		"contract_name",
		"order_sn",
		"goods_name",
		"cat_name",
		"attr",
		"goods_price",
		"goods_number",
		"order_status",
		"operate"
	],
	check_button: {
		"sale_check":{
			"status": "",
			"name": "销售验签",
			"func": ""
		},
		"purchase_check":{
			"status": "",
			"name": "采购验签",
			"func": ""
		},
		"send_check":{
			"status": "2,4",
			"name": "发货验签",
			"func": ""
		},
		"receive_check":{
			"status": "6,8",
			"name": "到货验签",
			"func": ""
		},
		"confirm_check":{
			"status": "",
			"name": "对账验签",
			"func": ""
		},
		"cancel_check":{
			"status": "2,3,4,5,6,7,8,9",
			"name": "取消验签",
			"func": ""
		},
		"revoke_order":{
			"status": "",
			"name": "撤销订单",
			"func": ""
		},
		"purchase_order":{
			"status": "3",
			"name": "采购下单",
			"func": ""
		},
		"send_change_price":{
			"status": "1",
			"name": "发货改价",
			"func": ""
		},
		"receive_change_price":{
			"status": "5",
			"name": "到货改价",
			"func": ""
		}
	},
	limit: 0,
	offset: 8,
	current_page: 1,
	total_page: 0,
	url: "OrderInfoModel.php",
	entity: "order_info",

	getList: function(search){
		if(typeof(search) === "undefined"){
			serach = false;
		}else{
			var condition = {};
			var like = {};
			if($('input[name=order_sn]').val() != '') like["order_sn"] = $('input[name=order_sn]').val();
			if($('input[name=user_name]').val() != '') like["user_name"] = $('input[name=user_name]').val();
			if($('input[name=contract_name]').val() != '') like["contract_name"] = $('input[name=contract_name]').val();
			var due_date1 = $('#search_form input[name=due_date1]').val();
			var due_date2 = $('#search_form input[name=due_date2]').val();
			condition.like = like;
			$("#search_form input[name^='due']").each(function(index, elem){
				if($(elem).val() != ''){
					condition[$(elem).attr("name")] = $(elem).val();
				}
			});
		}
		if(search != false){
			if(search == "search"){
				this.current_page = 1;
			}
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
					$("#sale_order_list>tbody").html(row);
					$("#paginate").html('');
				}else{
					$("#paginate").html(createPaginate(that.url, obj.content.total, that.current_page, that.limit, that.offset));
					var row = "";
					$.each(obj.content.data,function(key, value){
						row += "<tr>";
						for(var i=0;i<that.order_arr.length;i++){
							if(that.order_arr[i] == "operate"){
								var edit = createLink("demo_template.php?section=sale_order&act=detail&id="+value.order_id, "取消");
								edit += createLink("demo_template.php?section=sale_order&act=detail&id="+value.order_id, "详情");
								edit += createLink("demo_template.php?section=sale_order&act=suborder_list&id="+value.order_id, "子订单列表");
								row += createTd(edit);
								continue;
							}
							if(value[that.order_arr[i]] != null){
								row += createTd(value[that.order_arr[i]]);
							}else{
								row += createTd(createWarn('无数据'));
							}
						}
						row += "</tr>";
						$("#sale_order_list>tbody").html(row);
					});
				}
			}
			$('#message_area').html('');
		}, "json");
	},

	getOrderDetail: function(){
		var id = getQueryStringByName('id');
		if(id===""||!validateNumber(id)){
			return false;
		}
		var params = {"params":{"order_id":id}};
		strJson = createJson("find", this.entity, params);
		that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$.each(obj.content.info, function(k, v){
					if($("#"+k).length){
						$("#"+k).text(v);
					}
				});
				$.each(obj.content.invoice, function(k, v){
					if($("#"+k).length){
						$("#"+k).text(v);
					}
				});
				$.each(obj.content.goods, function(key, value){
					row = "<tr>";
					for(var i=0;i<that.order_detail_arr.length;i++){
						if(that.order_detail_arr[i] == "operate"){
							var edit = createLink("demo_template.php?section=sale_order&act=detail&id="+obj.content.info.order_id, "取消未处理");
							edit += createLink("demo_template.php?section=sale_order&act=split&order_id="+obj.content.info.order_id+"&goods_id="+value.goods_id, "拆单");
							edit += createLink("demo_template.php?section=sale_order&act=suborder_detail&order_id="+obj.content.info.order_id, "子订单");
							row += createTd(edit);
							continue;
						}
						if(value[that.order_detail_arr[i]] != null){
							row += createTd(value[that.order_detail_arr[i]]);
						}else{
							row += createTd(createWarn('无数据'));
						}
					}
					row += "</tr>";
					$("#sale_order_detail_list>tbody").html(row);					
				});
			}
			$('#message_area').html('');
		}, "json");
	},

	setSplitInit: function(){
		var order_id = getQueryStringByName('order_id');
		if(order_id===""||!validateNumber(order_id)){
			return false;
		}
		var goods_id = getQueryStringByName('goods_id');
		if(goods_id===""||!validateNumber(goods_id)){
			return false;
		}

		var params = {"order_id":order_id, "goods_id":goods_id};
		strJson = createJson("splitInit", this.entity, params);
		that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$.each(obj.content.good, function(k, v){
					if($("#"+k).length>0){
						$("#"+k).text(v);
					}
				});
				$("#order_id").text(order_id);
				$("#goods_id").text(goods_id);

				$.each(obj.content.order, function(k, v){
					if($("#"+k).length>0){
						$("#"+k).text(v);
					}
					if($("input[name="+k+"]").length>0){
						$("input[name="+k+"]").val(v);
					}
					if(k == "payment"){
						var row = "";
						$.each(v, function(k1, v1){
							row += appendOption(v1.pay_id, v1.pay_name);
						});
						$("select[name=pay_id]").html(row);
					}
					if($("select[name="+k+"]").length>0){
						var row = "";
						if(k == "suppliers"){
							$.each(v, function(k1, v1){
								row += appendOption(v1.suppliers_id, v1.suppliers_name);
							});
						}
						$("select[name="+k+"]").html(row);
					}
				});
				if(obj.content.order.remain_number > 0){
					$("#handle_button").append('<input type="button" class="button" value=" 确定 " onclick="SaleOrder.setSplit()">')
				}
			}
			$('#message_area').html('');
		}, "json");
	},

	setSplit: function(){
		var order_id = getQueryStringByName('order_id');
		if(order_id===""||!validateNumber(order_id)){
			return false;
		}
		var goods_id = getQueryStringByName('goods_id');
		if(goods_id===""||!validateNumber(goods_id)){
			return false;
		}
		if($("#split_form").valid() == false){
			return false;
		}
		var remain_number = parseFloat($("#remain_number").text());
		var send_number = parseFloat($("#send_number").val());
		if(send_number>remain_number){
			$('#message_area').html(createError('拆分余数不足'));
			return false;
		}
		var form_data = $("#split_form").FormtoJson();
		form_data.order_id = order_id;
		form_data.goods_id = goods_id;
		strJson = createJson("split", this.entity, form_data);
		that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$('#message_area').html(createTip(obj.message));
				return false;
			}
		}, "json");
	},

	setOrderPrice: function(){
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
					$("#bill_amount_list>tbody").html(row);
					$("#paginate").html('');
				}else{
					$("#paginate").html(createPaginate(that.url, obj.content.total, that.limit, that.offset));
					var row = "";
				}
			}
			$('#message_area').html('');
		}, "json");
	},

	getSubOrderList: function(search){
		var id = getQueryStringByName('id');
		if(id===""||!validateNumber(id)){
			return false;
		}
		if(typeof(search) === "undefined"){
			search = false;
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
			var params = {"order_id":id};
		}
		strJson = createJson("childerList", this.entity, params);
		that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				that.total_page = Math.ceil(obj.content.total/that.offset);
				if(obj.content.total == 0){
					var row = "<tr><td colspan='20'>"+createWarn("无数据")+"</td></tr>";
					$("#suborder_list>tbody").html(row);
				}else{
					var row = "";
					$.each(obj.content.data,function(key,value){
						row += "<tr>";
						for(var i=0;i<that.suborder_arr.length;i++){
							if(that.suborder_arr[i] == "operate"){
								var edit = createLink("demo_template.php?section=sale_order&act=suborder_detail&order_id="+value.order_id, "详情");
								row += createTd(edit);
								continue;
							}
							if(value[that.suborder_arr[i]] != null){
								row += createTd(subString(value[that.suborder_arr[i]],10,true));
							}else{
								row += createTd(createWarn('无数据'));
							}
						}
						row += "</tr>";
						$("#suborder_list>tbody").html(row);
					});
				}
			}
			$('#message_area').html('');
		}, "json");
	},

	getSubOrderDetail: function(){
		var order_id = getQueryStringByName('order_id');
		if(order_id===""||!validateNumber(order_id)){
			return false;
		}
		var params = {"params":{"order_id":order_id}};
		strJson = createJson("childerDetail", this.entity, params);
		that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$.each(obj.content.info, function(k, v){
					if($("#"+k).length){
						$("#"+k).text(v);
					}
				});
				$.each(obj.content.invoice, function(k, v){
					if($("#"+k).length){
						$("#"+k).text(v);
					}
				});
				$.each(obj.content.goods, function(k, v){
					if($("#"+k).length){
						$("#"+k).text(v);
					}
				});
				// 订单状态相应操作
				var button = '';
				$.each(that.check_button, function(k, v){
					if(v.status.indexOf(obj.content.info.order_status) > 0){
						button += '<input type="button" class="button" onclick="'+v.func+'" value="'+v.name+'" >';
					}
				});
				$("#handle_button").append(button);

				// 物流信息
				var count = 0;
				var k;

				for (k in obj.content.shipping) {
				    if (obj.content.shipping.hasOwnProperty(k) && k!="log") {
				        count++;
				    }
				}
				if(count == 0){
					$("#logistics_info").html('<div style="text-align:center">'+createWarn("暂无物流信息")+'</div>');
					$("#logistics_operate").html(createLink("javascript:void(0);", "新增物流", "popupLayer()"));
				}else{
					var table='<table cellpadding="0" cellspacing="0"><thead><tr>';
					table += '<td class="title text-right" width="100">物流公司：</td><td>'+obj.content.shipping.company_name+'</td>';
					table += '<td class="title text-right" width="100">物流单号：</td><td>'+obj.content.shipping.shipping_num+'</td>';
					table += '<td class="title text-right" width="100">联系电话：</td><td>'+obj.content.shipping.tel+'</td>';
					table += '<td class="title text-right" width="100">发货时间：</td><td>'+obj.content.shipping.shipping_time+'</td>';
					table += '</tr></thead>';
					if(obj.content.shipping.log.length == 0){
						table += '<tbody><tr><td colspan="20">'+createWarning("暂无动态")+'</td></tr>';
						table += '</tbody></table>';
						$("#logistics_info").html(table);
					}else{
						table += '<tbody>';
						$.each(obj.content.shipping.log, function(k, v){
							table += '<td>'+v.date+'</td><td colspan="7">'+v.content+'</td>';
						});
						table += '</tbody></table>';
						$("#logistics_info").html(table);
					}
				}
			}
			$('#message_area').html('');
		}, "json");		
	},

	addShippingInfo: function(){
		var order_id = getQueryStringByName('order_id');
		if(order_id===""||!validateNumber(order_id)){
			return false;
		}
		if($("#logistics_form").valid() == false){
			return false;
		}
		$("input[name=order_id]").val(order_id);
		var formData = $("#logistics_form").FormtoJson();
		var params = {"params":formData};
		strJson = createJson("addShippingLog", this.entity, params);
		that = this
		console.log(strJson);
		$.post(this.url, strJson, function(obj){
			console.log(obj);
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
				}
			}
			$('#message_area').html('');
		}, "json");		
	}
}
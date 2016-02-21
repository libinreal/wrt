var PurchaseOrder = {
	order_arr: [
		"order_sn",
		"goods_name",
		"goods_sn",
		"attr",
		"price",
		"number",
		"shipping",
		"add_time",
		"status",
		"operate"
	],
	order_status: {},
	suborder_status: {},
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
	price_log: [
		"goods_sn",
		"goods_name",
		"goods_attr",
		"suppliers_name",
		"suppliers_price",
		"actual_price",
		"shipping_fee",
		"financial",
		"total",
		"payment",
		"price_date"
	],
	invoice_type: {
		0: "增值税专用",
		1: "普通发票"
	},
	limit: 0,
	offset: 8,
	current_page: 1,
	total_page: 0,
	url: "PurchaseOrderModel.php",
	entity: "order_info",

	getList: function(search){
		if(typeof(search) === "undefined"){
			serach = false;
		}else{
			var condition = {};
			var like = {};
			if($('input[name=order_sn]').val() != '') like["order_sn"] = $('input[name=order_sn]').val();
			if($('input[name=suppliers_name]').val() != '') like["suppliers_name"] = $('input[name=suppliers_name]').val();
			if($('input[name=contract_name]').val() != '') like["contract_name"] = $('input[name=contract_name]').val();
			if($('select[name=status]').val() != '') condition["status"] = $('select[name=status]').val();
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
		var that = this
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
								if(value.is_cancel == "yes" && value.order_status != 3){
									var edit = "<span id='cancel_"+value.order_id+"'>"+createLink("javascript:void(0)", "取消", "PurchaseOrder.cancelOrderInit('"+value.order_id+"','"+value.order_sn+"')")+"</span>";
								}else{
									var edit = '';
								}
								edit += createLink("demo_template.php?section=purchase_order_manage&act=detail&id="+value.order_id, "订单详情");
								row += createTd(edit);
								continue;
							}
							if(value[that.order_arr[i]] != null || value[that.order_arr[i]] != ''){
								if(that.order_arr[i] == "add_time"){
									row += createTd(timestampToDate(value[that.order_arr[i]]));
								}else if(that.order_arr[i] == "status"){
									row += createTd(that.order_status[value.status] === undefined ? "未知状态" : that.order_status[value.status]);
								}else{
									row += createTd(value[that.order_arr[i]]);
								}
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
		var order_id = getQueryStringByName('id');
		if(order_id===""||!validateNumber(order_id)){
			return false;
		}
		var params = {"params":{"order_id":order_id}};
		var strJson = createJson("childerDetail", this.entity, params);
		var that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$.each(obj.content.info, function(k, v){
					if($("#"+k).length){
						$("#"+k).text(v);
					}
					if($("select[name="+k+"]").length){
						$("select[name="+k+"]>option[value="+v+"]").attr("selected","selected");
					}
					if($("."+k).length){
						$("."+k).text(v);
					}
				});
				$.each(obj.content.invoice, function(k, v){
					if($("#"+k).length){
						if(k == "inv_type"){
							$("#"+k).text(that.invoice_type[v]);	
						}else{
							$("#"+k).text(v);
						}
					}
				});
				$.each(obj.content.goods, function(k, v){
					if($("#"+k).length){
						$("#"+k).text(v);
					}
				});
				// 订单状态相应操作
				var button = '';
				if(obj.content.order_status != 3){
					$.each(obj.content.buttons, function(k, v){
						if(v == "发货改价"){
							button += '<input type="button" class="button" onclick="redirectToUrl(\'demo_template.php?section=purchase_order_manage&act=send_price&order_id='+order_id+'\')" value="'+v+'" >';
						}else if(v == "到货改价"){
							button += '<input type="button" class="button" onclick="redirectToUrl(\'demo_template.php?section=purchase_order_manage&act=receive_price&order_id='+order_id+'\')" value="'+v+'" >';
						}else{
							button += '<input type="button" class="button" onclick="PurchaseOrder.updateChilderStatus(this)" value="'+v+'" >';
						}
					});
				}
				$("#handle_button>span").html(button);

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
					$("#logistics_operate").html(createLink("javascript:void(0);", "新增物流", "PurchaseOrder.addShippingInfoInit("+order_id+")"));
				}else{
					var table='<table cellpadding="0" cellspacing="1"><thead><tr>';
					table += '<td class="title text-right" width="100">物流公司：</td><td>'+obj.content.shipping.company_name+'</td>';
					table += '<td class="title text-right" width="100">物流单号：</td><td>'+obj.content.shipping.shipping_num+'</td>';
					table += '<td class="title text-right" width="100">联系电话：</td><td>'+obj.content.shipping.tel+'</td>';
					table += '<td class="title text-right" width="100">发货时间：</td><td>'+obj.content.shipping.shipping_time+'</td>';
					table += '</tr></thead>';
					if(obj.content.shipping.log.length == 0){
						table += '<tbody></tbody></table>';
						$("#logistics_info").html(table);
					}else{
						table += '<tbody>';
						table += '<tr><td class="title">日期</td><td class="title" colspan="7">动态</td></tr>';
						$.each(obj.content.shipping.log, function(k, v){
							table += '<tr><td>'+v.date+'</td><td colspan="7">'+v.content+'</td></tr>';
						});
						table += '</tbody></table>';
						$("#logistics_info").html(table);
					}
					$("#logistics_operate").html(createLink("javascript:void(0);", "添加物流信息", "PurchaseOrder.addShippingLogInit("+order_id+",'"+obj.content.shipping.shipping_num+"')"));
				}
			}
			$('#message_area').html('');
		}, "json");		
	},

	addShippingInfoInit: function(order_id){
		$("#popupLayer").load("templates/second/addShippingInfo_purchase.html?order_id="+order_id);
		popupLayer();
		$('#message_area').html('');
	},

	addShippingLogInit: function(order_id,shipping_num){
		$("#popupLayer").load("templates/second/addShippingLog_purchase.html?order_id="+order_id+"&num="+shipping_num);
		popupLayer();
		$('#message_area').html('');
	},

	addShippingInfo: function(){
		var table='<table cellpadding="0" cellspacing="0"><thead><tr>';
		table += '<td class="title text-right" width="100">物流公司：</td><td>'+$("input[name=company_name]").val()+'</td>';
		table += '<td class="title text-right" width="100">物流单号：</td><td>'+$("input[name=shipping_num]").val()+'</td>';
		table += '<td class="title text-right" width="100">联系电话：</td><td>'+$("input[name=tel]").val()+'</td>';
		table += '<td class="title text-right" width="100">发货时间：</td><td>'+$("input[name=shipping_time]").val()+'</td>';
		table += '</tr></thead>';
		table += '<tbody><tr><td colspan="20">'+createWarn("暂无动态")+'</td></tr>';
		table += '</tbody></table>';
		$("#logistics_info").html(table);
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
		var strJson = createJson("addShippingInfo", this.entity, params);
		var shipping_num = $('input[name="shipping_num"]').val();
		that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$('#logistics_form').html('<div style="text-align:center">'+createTip(obj.message)+'<input type="button" class="button close" href="javascript:void(0)" onclick="popupLayer()" value=" 关闭 " /></div>');
				$("#logistics_operate").html(createLink("javascript:void(0);", "添加物流信息", "PurchaseOrder.addShippingLogInit("+order_id+",'"+shipping_num+"')"));
				return false;
			}
		}, "json");
		popupLayer();
	},

	addShippingLogInit: function(order_id,shipping_num){
		$("#popupLayer").load("templates/second/addShippingLog_purchase.html?order_id="+order_id+"&num="+shipping_num);
		popupLayer();
		$('#message_area').html('');
	},

	addShippingLog: function(){
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
		var strJson = createJson("addShippingLog", this.entity, params);
		var that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$('#message_area').html(createTip(obj.message));
				var table = '<tr><td>'+$("input[name=date]").val()+'</td><td colspan="7">'+$("textarea[name=log]").val()+'</td></tr>';
				$('#logistics_info>table>tbody').append(table);
				return false;
			}
		}, "json");
		popupLayer();
	},

	updateChilderStatus: function(handle){
		var order_id = getQueryStringByName('id');
		if(order_id===""||!validateNumber(order_id)){
			return false;
		}
		var button_name = $(handle).val();
		var params = {"params":{"order_id":order_id, "button":button_name}};
		var strJson = createJson("updateChilderStatus", this.entity, params);
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

	initPriceSend: function(){
		var order_id = getQueryStringByName('order_id');
		if(order_id===""||!validateNumber(order_id)){
			return false;
		}
		$("input[name=order_id]").val(order_id);
		var params = {"params":{"order_id":order_id}};
		var strJson = createJson("initPriceSend", this.entity, params);
		var that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				// 初始化列表
				var key_arr = {
					"pay_id":{id:"id",name:"name"},
					"suppers_id":{id:"suppliers_id",name:"suppliers_name"}
				}
				$.each(obj.content.form,function(key, value){
					var row = "";
					if($("select[name='"+key+"']").length>0){
						$.each(value, function(k, v){
							row += appendOption(v[key_arr[key].id], v[key_arr[key].name])
						});
						$("select[name='"+key+"']").append(row);		
					}
					if($("input[name="+key+"]").length){
						$("input[name="+key+"]").val(value);
					}
					if($("textarea[name="+key+"]").length){
						$("textarea[name="+key+"]").text(value);
					}
				});
				$.each(obj.content.info,function(key, value){
					if($("#"+key).length){
						$("#"+key).text(value);	
					}
				});
				var row = "";
				$.each(obj.content.price_log,function(key, value){
					row += "<tr>";
					for(var i=0;i<that.price_log.length;i++){
						if(value[that.price_log[i]] != null){
							row += createTd(subString(value[that.price_log[i]],10,true));
						}else{
							row += createTd(createWarn('无数据'));
						}
					}
					row += "</tr>";
					$("#price_log_list>tbody").html(row);
				});

				$('#message_area').html('');		
			}
		}, "json");
	},

	updatePriceSend: function(){
		if($("#change_price_form").valid() == false){
			return false;
		}
		var formData = $("#change_price_form").FormtoJson();
		var params = {"params":formData};
		var strJson = createJson("updatePriceSend", this.entity, params);
		var that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$('#message_area').html(createTip(obj.message));
				that.getPriceLog();
				return false;
			}
		}, "json");
	},

	getPriceLog: function(){
		var order_id = getQueryStringByName('order_id');
		if(order_id===""||!validateNumber(order_id)){
			return false;
		}
		var params = {"params":{"order_id":order_id}};
		var strJson = createJson("initPriceSend", this.entity, params);
		var that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				// 初始化列表
				var row = "";
				$.each(obj.content.price_log,function(key, value){
					row += "<tr>";
					for(var i=0;i<that.price_log.length;i++){
						if(value[that.price_log[i]] != null){
							row += createTd(subString(value[that.price_log[i]],10,true));
						}else{
							row += createTd(createWarn('无数据'));
						}
					}
					row += "</tr>";
					$("#price_log_list>tbody").html(row);
				});
				$('#message_area').html('');		
			}
		}, "json");
	},

	initPriceArr: function(){
		var order_id = getQueryStringByName('order_id');
		if(order_id===""||!validateNumber(order_id)){
			return false;
		}
		$("input[name=order_id]").val(order_id);
		var params = {"params":{"order_id":order_id}};
		var strJson = createJson("initPriceArr", this.entity, params);
		var that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				// 初始化列表
				var key_arr = {
					"pay_id":{id:"id",name:"name"},
					"suppers_id":{id:"suppliers_id",name:"suppliers_name"}
				}
				$.each(obj.content.form,function(key, value){
					var row = "";
					if($("select[name='"+key+"']").length>0){
						$.each(value, function(k, v){
							row += appendOption(v[key_arr[key].id], v[key_arr[key].name])
						});
						$("select[name='"+key+"']").append(row);		
					}
					if($("input[name="+key+"]").length){
						$("input[name="+key+"]").val(value);
					}
					if($("textarea[name="+key+"]").length){
						$("textarea[name="+key+"]").text(value);
					}
				});
				$.each(obj.content.info,function(key, value){
					if($("#"+key).length){
						$("#"+key).text(value);	
					}
				});
				$('#message_area').html('');
			}
		}, "json");		
	},

	updatePriceArr: function(){
		if($("#change_price_form").valid() == false){
			return false;
		}
		var formData = $("#change_price_form").FormtoJson();
		var params = {"params":formData};
		var strJson = createJson("updatePriceArr", this.entity, params);
		var that = this
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

	updateButtonStatus: function(){
		var order_id = getQueryStringByName('id');
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
					if($("select[name="+k+"]").length){
						console.log($("select[name="+k+"]").val());
						$("select[name="+k+"]>option[value='"+v+"']").attr("selected","selected");
					}
				});
				// 按钮状态更新
				var button = '';
				$.each(obj.content.buttons, function(k, v){
					if(v == "发货改价"){
						button += '<input type="button" class="button" onclick="redirectToUrl(\'demo_template.php?section=sale_order&act=change_send_price&order_id='+order_id+'\')" value="'+v+'" >';
					}else if(v == "到货改价"){
						button += '<input type="button" class="button" onclick="redirectToUrl(\'demo_template.php?section=sale_order&act=change_receive_price&order_id='+order_id+'\')" value="'+v+'" >';
					}else{
						button += '<input type="button" class="button" onclick="SaleOrder.updateChilderStatus(this)" value="'+v+'" >';
					}
				});
				$("#handle_button>span").html(button);
			}
			$('#message_area').html('');
		}, "json");
	}
}
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
				console.log(obj);
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
							edit += createLink("demo_template.php?section=sale_order&act=detail&id="+obj.content.info.order_id, "子订单");
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
		console.log(strJson);
		$.post(this.url, strJson, function(obj){
			console.log(obj);
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
					if($("select[name="+k+"]").length>0){
						var row = "";
						if(k == "payment"){
							$.each(v, function(k1, v1){
								row += appendOption(v1.pay_id, v1.pay_name);
							});
						}
						if(k == "suppliers"){
							$.each(v, function(k1, v1){
								row += appendOption(v1.suppliers_id, v1.suppliers_name);
							});
						}
						$("select[name="+k+"]").html(row);
					}
				});
				if(obj.content.order.remain_number > 0){
					$("#handle_button").append('<input type="button" class="button" value=" 确定 " onclick="">')
				}
			}
			$('#message_area').html('');
		}, "json");
	},

	setSplit: function(){
		if($("split_form").valid() == false){
			return false;
		}
		var params = {"params":{"limit":this.limit, "offset":this.offset}};
		strJson = createJson("split", this.entity, params);
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

	getSubOrderList: function(){
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
				}
			}
			$('#message_area').html('');
		}, "json");		
	},

	getSubOrderDetail: function(){
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
	}
}
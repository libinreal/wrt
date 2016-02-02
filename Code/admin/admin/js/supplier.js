var Supplier = {
	order_arr: [
		"order_sn",
		"goods_name",
		"goods_sn",
		"attr",
		"goods_price",
		"goods_number",
		"shipping_fee",
		"order_status",
		"operate"
	],
	shipping_order_arr: [
		"cat_name",
		"shipping_fee",
		"desc",
		"operate"
	],
	limit: 0,
	offset: 8,
	total_page: 0,
	current_page: 1,
	url: "SupplierModel.php",
	entity: "order_info",

	getList: function(search){
		if(search == undefined){
			serach = false;
		}else{
			var condition = {};
			var order_sn = $('#search_form input[name=order_sn]').val();
			var goods_name = $('#search_form input[name=goods_name]').val();
			var status = $('#search_form select[name=status]').val();
			var due_date1 = $('#search_form input[name=due_date1]').val();
			var due_date2 = $('#search_form input[name=due_date2]').val();
			condition.like = {};
			if(order_sn != ''){
				condition.like['order_sn'] = order_sn
			}
			if(goods_name != ''){
				condition.like['goods_name'] = goods_name
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
			if(search == "search"){
				this.limit = 0;
			}
			var params = {"params":{"where":condition,"limit":this.limit, "offset":this.offset}};
		}else{
			var params = {"params":{"limit":this.limit, "offset":this.offset}};
		}
		strJson = createJson("orderPage", this.entity, params);
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
					$("#main_list>tbody").html(row);
					$("#paginate").html('');
				}else{
					$("#paginate").html(createPaginate(that.url, obj.content.total, that.current_page, that.limit, that.offset));
					var row = "";
					$.each(obj.content.data,function(key, value){
						row += "<tr>";
						for(var i=0;i<that.order_arr.length;i++){
							if(that.order_arr[i] == "operate"){
								var edit = createLink("demo_template.php?section=supplier&act=order_detail&id="+value.order_id, "详情");
								row += createTd(edit);
								continue;
							}
							if(value[that.order_arr[i]] != null){
								row += createTd(subString(value[that.order_arr[i]],18,true));
							}else{
								row += createTd(createWarn('无数据'));
							}
						}
						row += "</tr>";
						$("#main_list>tbody").html(row);
					});
				}
			}
			$('#message_area').html('');
		}, "json");
	},

	getDetail: function(){
		var id = getQueryStringByName('id');
		if(id===""||!validateNumber(id)){
			return false;
		}
		var params = {"params":{"order_id":id}};
		strJson = createJson("orderDetail", this.entity, params);
		that = this
		console.log(strJson)
		$.post(this.url, strJson, function(obj){
			console.log(obj)
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$.each(obj.content.info,function(key, value){
					if($("span#"+key).length == 1){
						$("span#"+key).text(value);
					}
				});

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
					$("#logistics_operate").html(createLink("javascript:void(0);", "新增物流", "Supplier.addShippingInfoInit("+id+")"));
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
					$("#logistics_operate").html(createLink("javascript:void(0);", "添加物流信息", "Supplier.addShippingLogInit("+id+",'"+obj.content.shipping.shipping_num+"')"));
				}
			}
			$('#message_area').html('');
		},"json");
	},

	addShippingInfoInit: function(order_id){
		$("#popupLayer").load("templates/supplier/addShippingInfo.html?order_id="+order_id);
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
		var order_id = getQueryStringByName('id');
		if(order_id===""||!validateNumber(order_id)){
			return false;
		}
		if($("#logistics_form").valid() == false){
			return false;
		}
		$("input[name=order_id]").val(order_id);
		var formData = $("#logistics_form").FormtoJson();
		var params = {"params":formData};
		strJson = createJson("addShippingInfo", this.entity, params);
		var shipping_num = $('input[name="shipping_num"]').val();
		that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$("#logistics_operate").html(createLink("javascript:void(0);", "添加物流信息", "Supplier.addShippingLogInit("+order_id+",'"+shipping_num+"')"));
				$('#message_area').html(createTip(obj.message));
				return false;
			}
		}, "json");
		popupLayer();
	},

	addShippingLogInit: function(order_id,shipping_num){
		$("#popupLayer").load("templates/supplier/addShippingLog.html?order_id="+order_id+"&num="+shipping_num);
		popupLayer();
		$('#message_area').html('');
	},

	addShippingLog: function(){
		var order_id = getQueryStringByName('id');
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
	},

	updateChilderStatus: function(handle){
		var order_id = getQueryStringByName('id');
		if(order_id===""||!validateNumber(order_id)){
			return false;
		}
		var button = $(handle).text();
		var params = {"params":{"order_id":order_id, "button": button}};
		strJson = createJson("updateChilderStatus", this.entity, params);
		that = this
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
	},

	setShippingFeeInit: function(){
		var params = {};
		strJson = createJson("initcategoryShipping", this.entity, params);
		var that = this
		$.post(this.url, strJson, function(obj){
			console.log(obj)
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$("#paginate").html(createPaginate(that.url, obj.content.total, that.current_page, that.limit, that.offset));
				// 物料类别
				var row = '';
				$.each(obj.content.cat, function(k, v){
					row += appendOption(v.cat_id, v.cat_name);
				});
				$("#cat").html(row);
				$("#cat_id_edit").html(row);
					
				var row = "";
				$.each(obj.content.data,function(key, value){
					row += "<tr id='list_"+value.shipping_fee_id+"'>";
					for(var i=0;i<that.shipping_order_arr.length;i++){
						if(that.shipping_order_arr[i] == "operate"){
							var edit = createButton("Supplier.removeShippingFee("+value.shipping_fee_id+", this)", "移除");
							edit += createButton("Supplier.editShippingInit("+value.shipping_fee_id+")", "编辑");
							row += createTd(edit);
							continue;
						}
						if(value[that.shipping_order_arr[i]] != null){
							row += createTd(value[that.shipping_order_arr[i]]);
						}else{
							row += createTd(createWarn('无数据'));
						}
					}
					row += "</tr>";
				});
				$("#main_list>tbody").html(row);
				$('#message_area').html(createTip(obj.message));
				return false;
			}
		}, "json");
	},

	editShippingInit: function(id){
		popupLayer();
		var params = {"params":{"shipping_fee_id": id}};
		strJson = createJson("categoryShippingDetail", "shipping_price", params);
		var that = this;
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$('#message_area').html(createTip(obj.message));
				$.each(obj.content, function(k, v){
					if($("input[name="+k+"]").length){
						$("input[name="+k+"]").val(v);
					}
					if($("select[name="+k+"]").length){
						$("select[name="+k+"] option[value="+v+"]").attr("selected", "selected");
					}
				});
			}
		}, "json");
	},

	editShipping: function(){
		if($("#logistics_form").valid == false){
			return false;
		}
		// 表单值
		var shipping_fee_id = $("#logistics_form input[name=shipping_fee_id]").val();
		var shipping_fee = $("#logistics_form input[name=shipping_fee]").val();
		var desc = $("#logistics_form input[name=desc]").val();
		var cat_name = $("#logistics_form  #cat_id_edit option:selected").text();

		var row = "";
		row += createTd(cat_name);
		row += createTd(shipping_fee);
		row += createTd(desc);

		var edit = createButton("Supplier.removeShippingFee("+shipping_fee_id+", this)", "移除");
		edit += createButton("Supplier.editShippingInit("+shipping_fee_id+")", "编辑");
		row += createTd(edit);
		$("#list_"+shipping_fee_id).html(row);

		var formData = $("#logistics_form").FormtoJson();
		var params = {"params":formData};
		var strJson = createJson("saveCategorShipping", "shipping_price", params);
		var that = this;
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$('#message_area').html(createTip(obj.message));
				popupLayer();
				return false;
			}
		}, "json");
	},

	setShippingFee: function(){
		if($("#main_form").valid() == false){
			return false;
		}
		var goods_type_exist = false;
		$("#main_list>tbody>tr").each(function(index, elem){
			if($(elem).children(":first").text() == $("select#cat>option:selected").text()){
				goods_type_exist = true;
				return false;
			}else{
				$('#message_area').html('');
			}
		});
		if(goods_type_exist){
			$('#message_area').html(createError("物料类别已存在"));
			return false;
		}
		var row = "<tr>";
		row += "<td>"+$("#cat>option:selected").text()+"</td>";
		row += "<td>"+$("input[name=shipping_fee]").val()+"</td>";
		row += "<td>"+$("input[name=desc]").val()+"</td>";
		row += "<td></td>";
		$("#main_list>tbody").prepend(row);
		
		var formData = $("#main_form").FormtoJson();
		var params = {"params":formData};
		strJson = createJson("addCategoryShippingFee", this.entity, params);
		var that = this;
		$.post(this.url, strJson, function(obj){
			console.log(obj)
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$('#message_area').html(createTip(obj.message));
				return false;
			}
		}, "json");
	},

	removeShippingFee: function(id, handle){
		var params = {"params":{"shipping_fee_id": id}};
		strJson = createJson("removeCategoryShipping", this.entity, params);
		var that = this;
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$('#message_area').html(createTip(obj.message));
				$(handle).parent().parent().remove();
				return false;
			}
		}, "json");		
	}
}
var Price = {
	order_arr: [
		"goods_id",
		"goods_name",
		"cat_name",
		"attr_values",
		"shop_price",
		"final_price",
		"price_num",
		"price_rate",
		"price_type",
		"operate"
	],
	review_type_arr: [
		"未审核",
		'<span style="color:blue">审核已通过</span>',
		'<span style="color:red">审核未通过</span>'
	],
	limit: 0,
	offset: 20,
	total_page: 0,
	current_page: 1,
	url: "price_manage.php",
	entity: "price_adjust",

	getList: function(search){
		if(typeof(search) === "undefined"){
			var params = {"params":{"limit":this.limit, "offset":this.offset}};
		}else{
			var condition = {};
			var cat_id = $('#search_form select[name=cat_id]').val();
			var brand_id = $('#search_form select[name=brand_id]').val();
			var suppliers_id = $('#search_form select[name=suppliers_id]').val();
			var type = $('#search_form select[name=type]').val();
			if(cat_id != ''){
				condition.cat_id = cat_id;
			}
			if(brand_id != ''){
				condition.brand_id = brand_id;
			}
			if(suppliers_id != ''){
				condition.suppliers_id = suppliers_id;
			}
			condition.type = type;
			if(search == "search"){
				this.current_page = 1;
				this.limit = 0;
			}
			var attributes = [];
			$('select[name*=attr_id] :selected').each(function(index, e){
				if($(e).val() != ''){
					attributes.push({"attr_id":$(e).val(), "attr_values":$(e).text()});
				}
			});
			if(attributes.length > 0){
				condition.attributes = attributes;
			}
			var params = {"params":{"where":condition, "limit":this.limit, "offset":this.offset}};
		}
		var strJson = createJson("priceList", "goods", params);
		var that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				that.total_page = Math.ceil(obj.content.total/that.offset);
				if(obj.content.total == 0){
					var row = "<tr><td colspan='20'>"+createWarn("无数据")+"</td></tr>";
					$("#purchase_price_increase_list>tbody").html(row);
					$("#paginate").html('');
				}else{
					$("#paginate").html(createPaginate(that.url, obj.content.total, that.current_page, that.limit, that.offset));
					var row = "";
					$.each(obj.content.data,function(key, value){
						row += "<tr>";
						for(var i=0;i<that.order_arr.length;i++){
							if(that.order_arr[i] == "operate"){
								var edit = createLink("demo_template.php?section=purchase_price_manage&act=single&id="+value.goods_id, "修改价格");
								edit += createLink("javascript:void(0)", "删除", "Price.deleteGoodsPrice("+value.goods_id+")");
								row += createTd(edit);
								continue;
							}
							if(that.order_arr[i] == "attr_values"){
								var attr = "";
								$.each(value.attr_values, function(k, v){
									attr += v + "/";
								});
								row += createTd(attr);
								continue;
							}
							if(that.order_arr[i] == "price_rate"){
								if(value.price_rate == 0 || value.price_rate == ''){
									row += createTd(0);
								}else{
									row += createTd(value.price_rate+"%");
								}
								continue;
							}
							if(that.order_arr[i] == "final_price"){
								if(value.price_num == 0 || value.price_num == ''){
									row += createTd(parseFloat(value.shop_price) + value.shop_price * value.price_rate/100);
								}else{
									row += createTd(parseFloat(value.shop_price) + parseFloat(value.price_num));
								}
								continue;
							}
							if(that.order_arr[i] == "attr_values"){
								var attr = "";
								$.each(value.attr_values, function(k, v){
									attr += v + "/";
								});
								row += createTd(attr);
								continue;
							}
							if(value[that.order_arr[i]] != null){
								row += createTd(subString(value[that.order_arr[i]],20,true));
							}else{
								row += createTd(createWarn('无数据'));
							}
						}
						row += "</tr>";
					});
					$("#purchase_price_increase_list>tbody").html(row);
				}
			}
			
		}, "json");
	},
	// 获取厂家列表
	getBrandsList: function(){
		strJson = createJson("getBrands", "brand", {});
		that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				var row = '';
				$.each(obj.content, function(k,v){
					row += appendOption(v.brand_id, v.brand_name);
				});
				$("select[name=brand_id]").append(row);
			}
			
		}, "json");
	},
	// 获取物料类别列表
	getGoodsTypeList: function(){
		strJson = createJson("catList", "goods_type", {});
		that = this
		$.post("contract_manage.php", strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				var row = '';
				$.each(obj.content, function(k,v){
					if(v.list.length == 0){
						row += appendOption(v.cat_id, v.cat_name, 0, 1);
						return;
					}else{
						row += appendOption(v.cat_id, v.cat_name, 0, 0);
						$.each(v.list, function(k1, v1){
							if(v1.list.length == 0){
								row += appendOption(v1.cat_id, "&nbsp;&nbsp;&nbsp;&nbsp;"+v1.cat_name, 0, 1);
								return;
							}else{
								row += appendOption(v1.cat_id, "&nbsp;&nbsp;&nbsp;&nbsp;"+v1.cat_name, 0, 0);
								$.each(v1.list, function(k2, v2){
									if(v1.list.length == 0){
										row += appendOption(v2.cat_id, "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+v2.cat_name, 0, 1);
										return;
									}else{
										row += appendOption(v2.cat_id, "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+v2.cat_name, 0, 0);
										$.each(v2.list, function(k3, v3){
											row += appendOption(v3.cat_id, "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+v3.cat_name, 0, 1);
										});										
									}
								});								
							}
						});
					}
				});
				$("select[name=cat_id]").append(row);
			}
			
		}, "json");
	},
	// 获取供应商列表
	getSuppliers: function(){
		strJson = createJson("suppliers", "admin_user", {});
		that = this
		$.post("contract_manage.php", strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				var row = '';
				$.each(obj.content, function(k,v){
					row += appendOption(v.suppliers_id, v.suppliers_name);
				});
				$("select[name=suppliers_id]").append(row);
			}
			
		}, "json");
	},
	// 获取物料属性
	getAttributes: function(cat_id){
		cat_id = $("select[name=cat_id]").val();
		if(cat_id===""||!validateNumber(cat_id)){
			return false;
		}
		params = {"cat_id": cat_id};
		strJson = createJson("getAttributes", "attribute", params);
		that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				var row = '';
				$.each(obj.content, function(k, v){
					row += '<labe>'+v.attr_name+'：</labe>';
					row += '<select name="attr_id[]" style="width:150px;">';
					var option = '<option value="">全部</option>';
					$.each(v.attr_values, function(k1, v1){
						option += '<option value="'+v.attr_id+'">'+v1+'</option>';
					});
					row += option;
					row += '</select> &nbsp;';
				});
				$("#sub_search").html(row);
			}
			
		}, "json");
	},
	// 获取批量加价列表
	getExistBatch: function(search){
		if(typeof(search) === "undefined"){
			var params = {};
		}else{
			var params = {};
			var cat_id = $('#search_form select[name=cat_id]').val();
			var brand_id = $('#search_form select[name=brand_id]').val();
			var suppliers_id = $('#search_form select[name=suppliers_id]').val();
			if(cat_id != ''){
				params.cat_id = cat_id;
				if(brand_id != ''){
					params.brand_id = brand_id;
				}
				if(suppliers_id != ''){
					params.suppliers_id = suppliers_id;
				}
			}
		}
		var strJson = createJson("getExistBatch", "price_adjust", params);
		var that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				var row = '';
				$.each(obj.content, function(k,v){
					var id = v.cat_id + v.brand_id + v.suppliers_id;
					var brand_name = v.brand_name == '' ? '全部' : v.brand_name;
					var suppliers_name = v.suppliers_name == '' ? '全部' : v.suppliers_name;
					row += '<tr id='+id+'>';
					row += "<td class='title'>物料类别：</td>";
					row += "<td>"+v.cat_name+"</td>";
					row += "<td class='title'>厂家：</td>";
					row += "<td>"+brand_name+"</td>";
					row += "<td class='title'>供应商：</td>";
					row += "<td>"+suppliers_name+"</td>";
					row += "<td class='title'>加价幅度：</td>";
					if(v.review_status == 0){
						row += "<td><input type='text' class='number' name='price_num_"+id+"' value="+v.price_num+"></td>";	
					}else{
						row += "<td><input type='text' class='number' name='price_num_"+id+"' value="+v.price_num+" disabled></td>";
					}
					row += "<td class='title'>加价比例：</td>";
					if(v.review_status == 0){
						row += "<td><input type='text' class='number' name='price_rate_"+id+"' value="+v.price_rate+"></td>";
					}else{
						row += "<td><input type='text' class='number' name='price_rate_"+id+"' value="+v.price_rate+" disabled></td>";
					}
					row += "<td>"+that.review_type_arr[v.review_status]+"</td>";
					row += "<td>";
					if(v.review_status == 0 && v.is_review == 1){
						row += createButton('Price.checkReview(2, '+v.price_adjust_id+')', '不通过');
						row += createButton('Price.checkReview(1, '+v.price_adjust_id+')', '通过');
					}
					row	+= "<input type='button' class='button' value='删除' onclick='removeRow(this, "+v.price_adjust_id+")' />";
					row += "<input type='hidden' name='cat_id_"+id+"' value='"+v.cat_id+"' />";
					row += "<input type='hidden' name='brand_id_"+id+"' value='"+v.brand_id+"' />";
					row += "<input type='hidden' name='suppliers_id_"+id+"' value='"+v.suppliers_id+"' />";
					row += "<input type='hidden' name='price_adjust_id_"+id+"' value='"+v.price_adjust_id+"' /></td>";
					row += "</tr>";
				});
				$("#purchase_price_increase_table>tbody").html(row);
			}
			
		}, "json");		
	},
	//批量加价
	setBatch: function(){
		if($("tr#no-item").length>0){
			$('#message_area').html(createError("请添加项目"));
			return false;
		}else{
			var arr = [];
			$("#purchase_price_increase_table>tbody tr").each(function(index, elem){
				if($(elem).find("input[name*='price_num']").val() != '' || $(elem).find("input[name*='price_rate']").val() != '') {
					if($(elem).find("input[name*='price_adjust_id']").val() != ''){
						arr.push({"price_adjust_id":$(elem).find("input[name*='price_adjust_id']").val(),"cat_id":$(elem).find("input[name*='cat_id']").val(),"brand_id":$(elem).find("input[name*='brand_id']").val(),"suppliers_id":$(elem).find("input[name*='suppliers_id']").val(),"price_num":$(elem).find("input[name*='price_num']").val() == '' ? 0 : $(elem).find("input[name*='price_num']").val(),"price_rate":$(elem).find("input[name*='price_rate']").val() == '' ? 0 : $(elem).find("input[name*='price_rate']").val()})
					}else{
						arr.push({"cat_id":$(elem).find("input[name*='cat_id']").val(),"brand_id":$(elem).find("input[name*='brand_id']").val(),"suppliers_id":$(elem).find("input[name*='suppliers_id']").val(),"price_num":$(elem).find("input[name*='price_num']").val() == '' ? 0 : $(elem).find("input[name*='price_num']").val(),"price_rate":$(elem).find("input[name*='price_rate']").val()  ? 0 : $(elem).find("input[name*='price_rate']").val()})
					}
				}
			});
		}
		params = {"params": arr}
		strJson = createJson("batchPrice", this.entity, params);
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
			$('#message_area').html(createTip('更新成功'));
			Price.getExistBatch('search');
		}, "json");
	},
	//删除批量加价中一条数据
	deleteSingleBatch: function(id){
		if(id===""||!validateNumber(id)){
			return false;
		}
		var params = {"price_adjust_id": id};
		strJson = createJson("deletePrice", "goods", params);
		that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$('#message_area').html(createTip('删除成功'));
				return false;
			}
		}, "json");
	},

	setSingle: function(){
		var id = getQueryStringByName('id');
		if(id===""||!validateNumber(id)){
			return false;
		}
		params = {"goods_id": id};
		strJson = createJson("singlePrice", "goods", params);
		that = this
		$.post(this.url, strJson, function(obj){
			console.log(obj);
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$.each(obj.content, function(key, value){
					if(key == "attr"){
						var attr_name = "";
						var attr_val = "";
						$.each(value, function(k, v){
							attr_name += v.attr_name + "/";
							attr_val += v.attr_value + "/";
						});
						$("#attr").html(attr_name + " : " + attr_val);
						return;
					}
					if($("#"+key).length > 0){
						$("#"+key).html(value);
					}
					if($("input[name="+key+"]").length >0){
						$("input[name="+key+"]").val(value);
					}
				});
				$("#final_price").text(parseFloat(obj.content.shop_price)+parseFloat(obj.content.price_num));
			}
			
		}, "json");
	},

	editSingle: function(){
		var id = getQueryStringByName('id');
		if(id===""||!validateNumber(id)){
			return false;
		}
		if($("#increase_form").valid() == false){
			return false;
		}
		var formdata = $("#increase_form").FormtoJson();
		var params = {"goods_id":id, "params":formdata};
		strJson = createJson("setPrice", "goods", params);
		that = this
		console.log(strJson);
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				redirectToUrl("price_manage.php?act=list");
				return false;
			}
		}, "json");
	},

	//删除批量加价中一条数据
	deleteGoodsPrice: function(id){
		if(id===""||!validateNumber(id)){
			return false;
		}
		var params = {"goods_id": id};
		var strJson = createJson("deleteGoodsPrice", "goods", params);
		var that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$('#message_area').html(createTip('删除成功'));
				that.getList();
				return false;
			}
		}, "json");
	},

	//审核票据
	checkReview: function(status, id){
		if(id == "" || !validateNumber(id)){
			return false;
		}
		var params = {"price_adjust_id":id, "review_status":status};
		var strJson = createJson("review", "price_adjust", params);
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				Price.getExistBatch('search');
			}
		}, "json");
	}
}
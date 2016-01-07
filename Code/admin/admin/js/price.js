var Price = {
	order_arr: [

	],
	limit: 0,
	offset: 8,
	total_page: 0,
	url: "price_manage.php",
	entity: "price_adjust",

	getList: function(){
		if(typeof(search) === "undefined"){
			var params = {"params":{"limit":this.limit, "offset":this.offset}};
		}else{
			var condition = {};
			var search_type = $('#contract_search_form select[name=search_type]').val();
			var search_value = $('#contract_search_form input[name=search_value]').val();
			var contract_status = $('#contract_search_form select[name=contract_status]').val();
			var start_time = $('#contract_search_form input[name=start_time]').val();
			var end_time = $('#contract_search_form input[name=end_time]').val();
			if(search_value != ''){
				condition.like = {"search_type":search_type, "search_value":search_value};
			}
			if(contract_status != ''){
				condition.contract_status = contract_status;
			}
			if(start_time != ''){
				condition.start_time = start_time;
			}
			if(end_time != ''){
				condition.end_time = end_time;
			}
			if(search == "search"){
				this.current_page = 1;
				this.limit = 0;
			}
			var params = {"params":{"where":condition, "limit":this.limit, "offset":this.offset}};
		}
		strJson = createJson("priceList", this.entity, params);
		that = this
		console.log(strJson);
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
				$("select[name=brand_id]").html(row);
			}
			$('#message_area').html('');
		}, "json");
	},
	// 获取物料类别列表
	getGoodsTypeList: function(){
		strJson = createJson("getAttributes", "attribute", {});
		that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				console.log(obj)
				var row = '';
				$.each(obj.content, function(k,v){
					row += appendOption(v.brand_id, v.brand_name);
				});
				$("select[name=brand_id]").html(row);
			}
			$('#message_area').html('');
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
				arr.push({"cat_id":$(elem).find("input[name*='cat_id']").val(),"brand_id":$(elem).find("input[name*='brand_id']").val(),"suppliers_id":$(elem).find("input[name*='suppliers_id']").val(),"price_num":$(elem).find("input[name*='price_num']").val(),"price_rate":$(elem).find("input[name*='price_rate']").val()})
			});
		}
		params = {"params": arr}
		strJson = createJson("batchPrice", this.entity, params);
		that = this
		console.log(strJson);
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
	//编辑批量加价
	editBatch: function(){
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
	//删除批量加价中一条数据
	deleteSingleBatch: function(){
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

	setSingle: function(){
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

	editSingle: function(){
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
}
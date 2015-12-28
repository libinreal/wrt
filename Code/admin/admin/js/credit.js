var Credit = {
	order_arr: [
		"credit_num",
		"customer_num",
		"customer_name",
		"amount_all",
		"amount_remain",
		"credit_status",
		"start_time",
		"end_time",
		"registration_name",
		"create_type",
		"operate"
	],
	limit: 0,
	offset: 8,
	total_page: 0,
	url: "credit_manage.php",
	entity: "bank_credit",

	getList: function(){
		var params = {"params":{"limit":this.limit, "offset":this.offset}};
		strJson = createJson("creditList", this.entity, params);
		that = this
//		console.log(strJson);
		$.post(this.url, strJson, function(obj){
			if(obj.error){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				that.total_page = Math.ceil(obj.content.total/that.offset);
				if(obj.content.total == 0){
					var row = "<tr><td colspan='20'>"+createWarn("无数据")+"</td></tr>";
					$("#credit_list>tbody").html(row);
				}else{
					$("#paginate").html(createPaginate(that.url, obj.content.total, that.limit, that.offset));
					var row = "";
					$.each(obj.content.data,function(key, value){
						row += "<tr>";
						for(var i=0;i<that.order_arr.length;i++){
							$.each(value, function(k, v){
								if(k == that.order_arr[i]){
									if(v == null){
										row += createTd(v);
									}else{
										row += createTd(subString(v,10,true));
									}
								}
							});
							if(that.order_arr[i] == "operate"){
								var edit = createLink("demo_template.php?section=credit_manage&act=info&id="+value.credit_id, "详情");
								row += createTd(edit);
							}
						}
						row += "</tr>";
						$("#credit_list>tbody").html(row);
					});
				}
			}
			$('#message_area').html('');
		},"json");
	},

	getCreditInfo: function(){
		var id = getQueryStringByName('id');
		if(id===""||!validateNumber(id)){
			return false;
		}
		var params = {"credit_id": id, "params":{}};
		strJson = createJson("creditInfo", this.entity, params);
		that = this
//		console.log(strJson);
		$.post(this.url, strJson, function(obj){
			if(obj.error){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$.each(obj.content, function(k,v){
					if(k == "remark"){
						$("#"+k+" input").val(v);
					}else{
						$("#"+k).text(v);
					}
				});
			}
			$('#message_area').html('');
		},"json");
	},

	getUpdateCreditRemark: function(){
		var id = getQueryStringByName('id');
		if(id===""||!validateNumber(id)){
			return false;
		}
		var remark = $("#remark input").val();
		var params = {"credit_id": id, "params":{"remark": remark}};
		strJson = createJson("creditRemark", this.entity, params);
		that = this
		console.log(strJson)
		$.post(this.url, strJson, function(obj){
			console.log(obj);
			if(obj.error){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$('#message_area').html(createTip('保存成功'));
			}
		});
	}
}
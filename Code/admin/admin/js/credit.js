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
		"operate"
	],
	limit: 0,
	offset: 8,
	total_page: 0,
	current_page: 1,
	url: "credit_manage.php",
	entity: "bank_credit",

	getList: function(){
		var params = {"params":{"limit":this.limit, "offset":this.offset}};
		strJson = createJson("creditList", this.entity, params);
		that = this
		console.log(strJson);
		$.post(this.url, strJson, function(obj){
			if(obj.error){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				that.total_page = Math.ceil(obj.content.total/that.offset);
				if(obj.content.total == 0){
					var row = "<tr><td colspan='20'>"+createWarn("无数据")+"</td></tr>";
					$("#credit_list>tbody").html(row);
					$("#paginate").html('');
				}else{
					$("#paginate").html(createPaginate(that.url, obj.content.total, that.current_page, that.limit, that.offset));
					var row = "";
					$.each(obj.content.data,function(key, value){
						row += "<tr>";
						for(var i=0;i<that.order_arr.length;i++){
							if(value[that.order_arr[i]] != null){
								row += createTd(subString(value[that.order_arr[i]],10,true));
							}else{
								if(that.order_arr[i] == "operate"){
									var edit = createLink(that.url+"?act=detail&id="+value.credit_id, "详情");
									row += createTd(edit);
								}else{
									row += createTd(createWarn('无数据'));
								}
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
		$.post(this.url, strJson, function(obj){
			console.log(obj)
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
				redirectToUrl("credit_manage.php?act=list");
			}
		});
	},

	// 授信文件上传
	getUploadFile: function(){
		if($("#xml_field").valid()===false){
			return false;
		}
		var filename = $("#xml_field option:selected").val();
		var strJson = createJson("importCredit", filename, {});
		var that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$('#message_area').html(createTip('更新成功'));
				that.getList();
			}
		},"json");
	},

	// 授信文件选择
	getOptionXml: function(){
		var strJson = createJson("optionXml", "xmlList", {});
		var that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				var row = "";
				$.each(obj.content.files, function(k,v){
					row += appendOption(v, v);
				});
				$("#xml_field").append(row);
			}
		},"json");
	}
}
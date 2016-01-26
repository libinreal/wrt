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
			}
		});
	},

	// 授信文件上传
	getUploadFile: function(){
		if($("#file_form").valid()===false){
			return false;
		}
		var strJson = createJson("importCredit", "credit_file", {}, "object");
		that = this
        $.ajaxFileUpload({
            url:this.url,
            fileElementId:'credit_file',//file标签的id
            dataType: 'json',//返回数据的类型  
            data:strJson,//一同上传的数据
            success: function (data, status) {
				if(data.error){
					$('#message_area').html(createError(data.message));
				}else{
					$('#message_area').html(createTip('上传成功'));
					that.getList();
				}
            },
            error: function (data, status, e) {
                console.log(e);
            }
        });
	}
}
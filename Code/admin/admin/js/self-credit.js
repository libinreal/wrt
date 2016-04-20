var SelfCredit = {
	order_arr: [
		"apply_id",
		"user_name",
		"create_date",
		"apply_amount",
		"contract_name",
		"check_amount",
		"apply_remark",
		"check_remark",
		"status",
		"operate"
	],
	limit: 0,
	offset: 20,
	total_page: 0,
	current_page: 1,
	url: "applyCredit_manage.php",
	entity: "apply_credit",

	getList: function(search, check_status){
		if(!search){
			serach = false;
		}else{
			var condition = {};
			var user_name = $('#search_form input[name=user_name]').val();
			var contract_name = $('#search_form input[name=contract_name]').val();
			var status = $('#search_form select[name=status]').val();
			var start_time = $('#search_form input[name=start_time]').val();
			var end_time = $('#search_form input[name=end_time]').val();
			condition.like = {};
			if(user_name != ''){
				condition.like['user_name'] = user_name
			}
			if(contract_name != ''){
				condition.like['contract_name'] = contract_name
			}
			if(status != ''){
				condition.state = status;
			}
			if(start_time != ''){
				condition.start_time = start_time;
			}
			if(end_time != ''){
				condition.end_time = end_time;
			}
		}
		if(search != false){
			var params = {"params":{"where":condition,"limit":this.limit, "offset":this.offset}};
		}else{
			var params = {"params":{"limit":this.limit, "offset":this.offset}};
		}
		params.flag = check_status;
		var strJson = createJson("applyCreditList", this.entity, params);
		var that = this
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
								if(check_status == 4){
									var edit = createButton("SelfCredit.deleteRecord("+value.apply_id+")", "移除");
								}else{
									var edit = createLink("applyCredit_manage.php?act=detail&id="+value.apply_id, "详情");
								}
								row += createTd(edit);
								continue;
							}
							if(that.order_arr[i] == "apply_id"){
								row += createTd(value[that.order_arr[i]] + '<input type="checkbox" name="apply_id[]" value='+value[that.order_arr[i]]+' />');
								continue;
							}
							if(value[that.order_arr[i]] != null){
								row += createTd(subString(value[that.order_arr[i]],10,true));
							}else{
								row += createTd(createWarn('无数据'));
							}
						}
						row += "</tr>";
						$("#main_list>tbody").html(row);
					});
				}
			}
			
		}, "json");
	},

	removeTrash: function(){
		var params = {};
		var apply_id = [];
		var formData = $("#main_form").FormtoJson();
		if(formData.apply_id === undefined){
			$('#message_area').html(createError('请选择ID'));
			return false;
		}else{
			
		}
		if(formData.apply_id.length == 1){
			params.apply_id = parseInt(formData.apply_id[0]);			
		}else{
			params.apply_id = formData.apply_id;
		}
		params.flag = 1;
		var strJson = createJson("applyCreditStatus", this.entity, params);
		var that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				that.getList(false, 1);
				$('#message_area').html(createTip('移除成功'));
			}
		}, "json");
	},

	getDetail: function(){
		var id = getQueryStringByName('id');
		if(id===""||!validateNumber(id)){
			return false;
		}
		var params = {"apply_id":id};
		var strJson = createJson("applyCreditSingle", this.entity, params);
		var that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$.each(obj.content, function(key, value){
					if($("td#"+key).length){
						$("td#"+key).text(value);
					}
					if($("select[name="+key+"]").length){
						$("select[name="+key+"]>option[value="+value+"]").attr("selected","selected");
					}
					if($("input[name="+key+"]").length){
						if(obj.content.status==2||obj.content.status==3){
							$("input[name="+key+"]").attr("disabled","disabled")	
						}
						if(key == 'check_amount'){
							$("input[name="+key+"]").val(obj.content.apply_amount);
							return;
						}
						$("input[name="+key+"]").val(value);
					}
					if($("textarea[name="+key+"]").length){
						if(obj.content.status==2||obj.content.status==3){
							$("textarea[name="+key+"]").attr("disabled","disabled")	
						}
						$("textarea[name="+key+"]").text(value);
					}
					if($("img#"+key).length){
						if(value != ''){
							$("img#"+key).attr("src",value);
						}
						$("div#img").text(obj.content.img);
					}
				});
				if(obj.content.status==2||obj.content.status==3){
					$("#handle_button span").html('')
				}
			}
			
		},"json");	
	},

	setStatus: function(status){
		var id = getQueryStringByName('id');
		if(id===""||!validateNumber(id)){
			return false;
		}
		var formData = $("#main_form").FormtoJson();
		var params = {};

		params.apply_id = id;
		params.flag = 2;

		params.params = formData
		params.params.status = status;
		params.params.apply_id = id;

		strJson = createJson("applyCreditStatus", this.entity, params);
		var that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$('#message_area').html(createTip(obj.message));
				if($("select[name=status]").length){
					$("select[name=status]>option[value="+status+"]").attr("selected","selected");
				}
				$("#handle_button span").html('');
				return false;
			}
			
		},"json");		
	},

	deleteRecord: function(apply_id){
		var params = {};
		if(apply_id){
			params.apply_id = apply_id;	
		}else{
			var apply_id = [];
			var formData = $("#main_form").FormtoJson();
			if(formData.apply_id === undefined){
				$('#message_area').html(createError('请选择ID'));
				return false;
			}else{
				
			}
			if(formData.apply_id.length == 1){
				params.apply_id = parseInt(formData.apply_id[0]);			
			}else{
				params.apply_id = formData.apply_id;
			}
		}
		params.flag = 1;
		var strJson = createJson("applyCreditDelete", this.entity, params);
		var that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$('#message_area').html(createTip('移除成功'));
			}
			that.getList(false, 0);
		}, "json");
	}
}
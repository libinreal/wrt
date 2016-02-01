var Bank = {
	order_arr: [
		"bank_id",
		"bank_num",
		"bank_name",
		"bank_tel",
		"bank_fax",
		"bank_addr",
		"operate"
	],
	limit: 0,
	offset: 8,
	total_page: 0,
	current_page: 1,
	url: "bank_manage.php",
	entity: "bank",

	getList: function(){
		var params = {};
		var strJson = createJson("bankList", this.entity, params);
		var that = this
		$.post(this.url, strJson, function(obj){
			console.log(obj)
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				if(obj.content.length == 0){
					var row = "<tr><td colspan='20'>"+createWarn("无数据")+"</td></tr>";
					$("#bank_list>tbody").html(row);
				}else{
					var row = "";
					$.each(obj.content,function(key, value){
						row += "<tr>";
						for(var i=0;i<that.order_arr.length;i++){
							if(that.order_arr[i] == "operate"){
								var edit = createLink("demo_template.php?section=bank&act=edit&id="+value.bank_id, "编辑");
								row += createTd(edit);
								continue;
							}
							if(value[that.order_arr[i]] != null){
								if(that.order_arr[i] == "status"){
									row += createTd(that.bill_status[value["status"]]);
								}else{
									row += createTd(value[that.order_arr[i]]);
								}
							}else{
								row += createTd(createWarn('无数据'));
							}
						}
						row += "</tr>";
						$("#bank_list>tbody").html(row);
					});
				}
			}
			$('#message_area').html('');
		}, "json");
	},

	getCreate: function(){
		if($("#bank_edit_form").valid() === false){
			return false;
		}
		var form_data = $("#bank_edit_form").FormtoJson();
		var params = {};
		params.flag = 0;
		params.params = form_data;
		strJson = createJson("bankAdd", this.entity, params);
		that = this
		console.log(strJson)
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
			}else if(obj.error == 0){
				redirectToUrl("demo_template.php?section=bank&act=list");
			}
		}, "json");
	},

	getEditInit: function(){
		var id = getQueryStringByName('id');
		if(id===""||!validateNumber(id)){
			return false;
		}
		var params = {"bank_id":parseInt(id)};
		strJson = createJson("bankDetail", this.entity, params);
		var that = this
		$.post(this.url, strJson, function(obj){
			console.log(obj)
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$.each(obj.content, function(key, value){
					if($("input[name="+key+"]").length){
						var o = "input[name="+key+"]";
						if($(o).attr("type") == "text"){
							$(o).val(value);
						}
					}
					if($("#"+key).length){
						$("#"+key).text(value);	
					}
				});
			}
			$('#message_area').html('');
		},"json");
	},

	getEdit: function(){
		if($("#bank_edit_form").valid() === false){
			return false;
		}
		var form_data = $("#bank_edit_form").FormtoJson();
		var id = getQueryStringByName('id');
		if(id===""||!validateNumber(id)){
			return false;
		}
		var params = {};
		params.bank_id = id;
		params.flag = 1;
		params.params = form_data;
		strJson = createJson("bankAdd", this.entity, params);
		that = this
		console.log(strJson)
		$.post(this.url, strJson, function(obj){
			console.log(obj);
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
			}else if(obj.error == 0){
				$('#message_area').html(createTip(obj.message));
				redirectToUrl("demo_template.php?section=bank&act=list");
			}
		}, "json");
	}
}
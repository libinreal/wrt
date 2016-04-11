var BillRepay = {
	order_arr: [
		"bill_repay_log_id",
		"create_by",
		"create_time",
		"customer_name",
		"repay_amount",
		"operate"
	],
	limit: 0,
	offset: 20,
	total_page: 0,
	current_page: 1,
	url: "BillRepayModel.php",
	entity: "bill_repay_log",

	getList: function(search){
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
			if(search == "search"){
				this.limit = 0;
			}
			var params = {"params":{"where":condition, "limit":this.limit, "offset":this.offset}};
		}else{
			var params = {"params":{"limit":this.limit, "offset":this.offset}};
		}
		strJson = createJson("page", this.entity, params);
		var that = this;
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				that.total_page = Math.ceil(obj.content.total/that.offset);
				if(obj.content.total == 0){
					var row = "<tr><td colspan='20'>"+createWarn("无数据")+"</td></tr>";
					$("#bill_repay_list>tbody").html(row);
					$("#paginate").html('');
				}else{
					$("#paginate").html(createPaginate(that.url, obj.content.total, that.current_page, that.limit, that.offset));
					var row = "";
					$.each(obj.content.data,function(key, value){
						row += "<tr>";
						for(var i=0;i<that.order_arr.length;i++){
							if(that.order_arr[i] == "operate"){
								var edit = createLink("demo_template.php?section=bill_manage&act=repay_view&id="+value.bill_repay_log_id, "详情");
								row += createTd(edit);
								continue;
							}
							if(value[that.order_arr[i]] != null){
								row += createTd(subString(value[that.order_arr[i]],10,true));
							}else{
								row += createTd(createWarn('无数据'));
							}
						}
						row += "</tr>";
						$("#bill_repay_list>tbody").html(row);
					});
				}
			}
			
		}, "json");
	},

	getAddInit: function(){
		var id = getQueryStringByName('id');
		if(id===""||!validateNumber(id)){
			return false;
		}
		$("#bill_id").text(id);
		$("input[name=bill_id]").val(id);
		var params = {"bill_id":id};
		strJson = createJson("addInit", this.entity, params);
		that = this
		$.post(this.url, strJson, function(obj){
			console.log(obj);
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				// 初始化
				TypeMode.getBillRepayType("bill_repay_type");

				$.each(obj.content.info, function(k, v){
					if($("input[name="+k+"]").length > 0){
						$("input[name="+k+"]").val(v);
					}else{
						$("#"+k).text(v);
					}
				});
				$("input[name=repay_amount]").val(obj.content.info.need_repay);
			}
			
		}, "json");
	},

	getCreateAction: function(){
		var id = getQueryStringByName('id');
		if(id===""||!validateNumber(id)){
			return false;
		}
		if($("#bill_repay_form").valid() === false){
			return false;
		}
		var form_data = $("#bill_repay_form").FormtoJson();
		strJson = createJson("create", this.entity, form_data);
		console.log(strJson)
		var that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$('#message_area').html(createTip(obj.message));
				redirectToUrl("demo_template.php?section=bill_manage&act=list");
			}
		}, "json");		
	},

	getEditInit: function(){
		var id = getQueryStringByName('id');
		if(id===""||!validateNumber(id)){
			return false;
		}
		var params = {"bill_repay_log_id":id};
		strJson = createJson("editInit", this.entity, params);
		that = this
		$.post(this.url, strJson, function(obj){
			console.log(obj)
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$.each(obj.content.info, function(k, v){
					if($("td#"+k).length > 0){
						$("td#"+k).text(v);
					}
				});
			}
			
		}, "json");
	}
}
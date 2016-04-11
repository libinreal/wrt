var BillAssign = {
	limit: 0,
	offset: 20,
	total_page: 0,
	current_page: 1,
	url: "BillAssignModel.php",
	entity: "bill_assign_log",

	getList: function(type){
		var id = getQueryStringByName('id');
		if(id===""||!validateNumber(id)){
			return false;
		}
		params = {"params":{"where":{"customer_id":id,"type":type},"limit":this.limit,"offset":this.offset}};
		strJson = createJson("page", this.entity, params);
		var that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				//客户信息
				$.each(obj.content.info, function(k, v){
					$("#"+k).text(v);
				});
				// 绑定数据
				if(obj.content.total == 0){
					var row = "<tr><td colspan='20'>"+createWarn("无数据")+"</td></tr>";
					$("#bill_purchase_assign_list>tbody").html(row);
					return false;
				}else{
					that.total_page = Math.ceil(obj.content.total/that.offset);
					$("#paginate").html(createPaginate(that.url, obj.content.total, that.current_page, that.limit, that.offset));
					var row = "";
					$.each(obj.content.data,function(key, value){
						row += "<tr>";
						row += "<td class='title'>合同号：</td><td>"+value.contract_num+"</td>";
						if(type == 0){
							row += "<td class='title'>已分配采购额度：</td><td>"+value.bill_amount_history+"</td>";
							row += "<td class='title'>现有采购额度：</td><td id='bill_amount_valid"+value.contract_id+"'>"+value.bill_amount_valid+"</td>";
							row += "<td class='title'>本次分配额度：</td><td><input type='text'name='assign_amount"+value.contract_id+"' data-rule-number='true' data-number-msg='' onchange='checktotal()' /><input type='hidden' name='contract_id[]' value='"+value.contract_id+"' /></td>";
							row += "<td class='title'>现有总采购额度：</td><td id='total"+value.contract_id+"'>"+value.bill_amount_valid+"</td>";
						}else if(type == 1){
							row += "<td class='title'>已分配现金采购额度：</td><td>"+value.cash_amount_history+"</td>";
							row += "<td class='title'>现有现金采购额度：</td><td id='bill_amount_valid"+value.contract_id+"'>"+value.cash_amount_valid+"</td>";
							row += "<td class='title'>本次分配现金额度：</td><td><input type='text'name='assign_amount"+value.contract_id+"' data-rule-number='true' data-number-msg='' onchange='checktotal()' /><input type='hidden' name='contract_id[]' value='"+value.contract_id+"' /></td>";
							row += "<td class='title'>现有总采购额度：</td><td id='total"+value.contract_id+"'>"+value.cash_amount_valid+"</td>";						
						}
						row += "</tr>";
					});
					$("#bill_purchase_assign_list>tbody").html(row);
				}
			}
			$('#message_area').html(createTip(obj.message));
		}, "json");
	},

	getCreateMulti: function(type){
		if($("#bill_purchase_form").valid() == false){
			return false;
		}
		var amount_valid = parseFloat($("#amount_valid").text());
		var total = 0;
		var contract_id = "";
		var assign_amount = "";
		$("#bill_purchase_form input[type=text]").each(function(index, element){
			if($(element).val() != ""&&isNumeric($(element).val())){
				total = total + parseFloat($(element).val());
				assign_amount += $(element).val() + ",";
				contract_id += $(element).next("input").val() + ",";
			}
		});
		if(total > amount_valid){
			$('#message_area').html(createError('客户可用总额不足分配'));
			return false;
		};
		contract_id = contract_id.substring(0, contract_id.length-1);
		assign_amount = assign_amount.substring(0, assign_amount.length-1);
		
		var params = {"type":type, "contract_id":contract_id, "assign_amount":assign_amount};
		strJson = createJson("createMulti", this.entity, params);
		that = this
		console.log(strJson)
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$('#message_area').html(createTip(obj.message));
			}
		}, "json");
	}
}
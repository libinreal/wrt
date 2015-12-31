var BillAssign = {
	limit: 0,
	offset: 8,
	total_page: 0,
	url: "BillAssignModel.php",
	entity: "bill_assign_log",

	getList: function(type){
		var id = getQueryStringByName('id');
		if(id===""||!validateNumber(id)){
			return false;
		}
		params = {"params":{"where":{"customer_id":id,"type":type},"limit":this.limit,"offset":this.offset}};
		strJson = createJson("page", this.entity, params);
		that = this
		console.log(strJson);
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				console.log(obj);
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
					$("#paginate").html(createPaginate(that.url, obj.content.total, that.limit, that.offset));
					var row = "";
					$.each(obj.content.data,function(key, value){
						row += "<tr>";
						row += "<td class='title'>合同号：</td><td>"+value.contract_id+"</td>";
						row += "<td class='title'>已分配现金采购额度：</td><td>"+value.bill_amount_history+"</td>";
						row += "<td class='title'>现有现金采购额度：</td><td>"+value.bill_amount_valid+"</td>";
						row += "<td class='title'>本次分配现金额度：</td><td><input type='hidden' name='contract_id' value='"+value.contract_id+"' /><input type='text' data-rule-number='true' data-number-msg='' onchange='checktotal()' /></td>";
						row += "<td class='title'>现有总采购额度：</td><td>"+value.bill_amount_valid+"</td>";
						row += "</tr>";
					});
					$("#bill_purchase_assign_list>tbody").html(row);
				}
			}
			$('#message_area').html('');
		}, "json");
	},

	getCreateMulti: function(type){
		if($("#bill_purchase_form").valid() == false){
			return false
		}
		var amount_valid = parseFloat($("#amount_valid").text());
		var total = 0;
		$("#bill_purchase_form input").each(function(index, element){
			if($(element).val() != ""&&isNumeric($(element).val())){
				total = total + parseFloat($(element).val());
			}
		});
		if(total > amount_valid){
			$('#message_area').html(createError('客户可用总额不足分配'));
			return false;
		};
	}
}
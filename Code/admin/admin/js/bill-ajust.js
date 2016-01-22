var BillAjust = {
	limit: 0,
	offset: 8,
	total_page: 0,
	url: "BillAdjustModel.php",
	entity: "bill_adjust",

	getAddInitAction: function(){
		var id = getQueryStringByName('id');
		if(id===""||!validateNumber(id)){
			return false;
		}
		params = {"user_id":id}
		strJson = createJson("addInit", this.entity, params);
		that = this
		console.log(strJson)
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				// 初始化
				var from_order = 0;
				var from_row = "";
				var from_contract_id = $('select[name=from_contract_id]').val();
				var to_order = 0;
				var to_row = "";
				var	to_contract_id = $('select[name=to_contract_id]').val();
				$.each(obj.content.init.contracts, function(k, v){
					if(from_contract_id != "" && from_contract_id == v.contract_id){
						from_row += appendOption(v.contract_id, v.contract_id, 1);
						from_order = k;
					}else{
						from_row += appendOption(v.contract_id, v.contract_id);
					}
					if(to_contract_id != "" && to_contract_id == v.contract_id){
						to_row += appendOption(v.contract_id, v.contract_id, 1);
						to_order = k;
					}else{
						to_row += appendOption(v.contract_id, v.contract_id);
					}
				});
				$("select[name=from_contract_id]").html(from_row);
				$("select[name=to_contract_id]").html(to_row);
				var row = "";
				var from_type = $("#from_type").val();
				$.each(obj.content.init.type, function(k, v){
					if(from_type != "" && from_type == k){
						row += appendOption(k, v, 1);
					}else{
						row += appendOption(k, v);
					}
				});
				$("#from_type").html(row);
				$("#to_type").html(row);
				//
				if(from_type == 0 || from_type == null){
					$("#from_amount_valid").text(obj.content.init.contracts[from_order].bill_amount_valid);
				}else{
					$("#from_amount_valid").text(obj.content.init.contracts[from_order].cash_amount_valid);
				}
				if(from_type == 0 || from_type == null){
					$("#to_amount_valid").text(obj.content.init.contracts[to_order].bill_amount_valid);
				}else{
					$("#to_amount_valid").text(obj.content.init.contracts[to_order].cash_amount_valid);
				}
				$("#from_ajust_amount").val(0);
				$("#to_ajust_amount").val(0);
			}
			$('#message_area').html('');
		}, "json");
	},

	getCreateAction: function(){
		if($('select[name=from_contract_id]').val() == $('select[name=to_contract_id]').val()){
			$('#message_area').html(createError('合同编号重复'));
			return false;
		}
		if($("#adjust_form").valid() == false){
        	return false;
    	}
		// 表单数据
		var form_data = $("#adjust_form").FormtoJson();
		strJson = createJson("create", this.entity, form_data);
		that = this
		console.log(strJson);
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$('#message_area').html(createTip(obj.message));
				return false;
			}
			$('#message_area').html('');
		}, "json");	
	}
}
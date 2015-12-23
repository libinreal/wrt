var Contract = {
	order_arr: [
		'contract_id',
		'contract_name',
		'contract_type',
		'companyName',
		'start_time',
		'end_time',
		'registration',
		'contract_status',
		'total',
		'valid',
		'bill_amount_valid',
		'cash_amount_valid',
		'operate'
	],
	limit: 1,
	offset: 1,
	url: "contract_manage.php",

	getList: function(){
		var params = {"params":{"limit":this.limit, "offset":this.offset}};
		strJson = makeJson("contList", "contract", params);
		that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error){
				console.log(obj.message);
			}else{
				$("#paginate").html(paginate(that.url, obj.content.total, that.limit, that.offset));
				var row = "";
				$.each(obj.content.data,function(key, value){
					row += "<tr>";
					for(var i=0;i<that.order_arr.length;i++){
						$.each(value, function(k, v){
							if(k == that.order_arr[i]){
								row += td(v);
							}
						});
						if(that.order_arr[i] == "total" || that.order_arr[i] == "valid" || that.order_arr[i] == "bill_amount_valid" || that.order_arr[i] == "cash_amount_valid"){
							row += td("");
						}
						if(that.order_arr[i] == "operate"){
							var edit = linkStr("demo_template.php?section=contract_manage&act=info&id="+value.contract_id, "编辑");
							edit += linkStr("demo_template.php?section=contract_manage&act=bind&id="+value.contract_id, "绑定供应商");
							row += td(edit);
						}
					}
					row += "</tr>";
					$("#contract_list>tbody").html(row);
				});
			}
		}, 'json');
	},

	getEdit: function(){
		id = getQueryStringByName('id');
		var params = {"contract_id": id, "params":{}};
		strJson = makeJson("singleCont", "contract", params);
		that = this
		$.post(this.url, strJson, function(obj){
			$.each(obj.content, function(key, value){
				if($("input[name="+key+"]").length){
					var o = "input[name="+key+"]";
					if($(o).attr("type") == "text"){
						$(o).val(value);
					}
					if($(o).attr("type") == "radio"){
						$("input[name="+key+"][value="+value+"]").attr("checked","1")
					}
				}
				if($("textarea[name="+key+"]").length){
					$("textarea[name="+key+"]").text(value)
				}
				if($("select[name="+key+"]").length){
					$("select[name="+key+"]>option[value="+value+"]").attr("selected","selected")	
				}
			});
		}, "json");
	}
}
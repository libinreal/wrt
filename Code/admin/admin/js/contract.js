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
		strJson = createJson("contList", "contract", params);
		that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error){
				console.log(obj.message);
			}else{
				$("#paginate").html(createPaginate(that.url, obj.content.total, that.limit, that.offset));
				var row = "";
				$.each(obj.content.data,function(key, value){
					row += "<tr>";
					for(var i=0;i<that.order_arr.length;i++){
						$.each(value, function(k, v){
							if(k == that.order_arr[i]){
								row += createTd(v);
							}
						});
						if(that.order_arr[i] == "total" || that.order_arr[i] == "valid" || that.order_arr[i] == "bill_amount_valid" || that.order_arr[i] == "cash_amount_valid"){
							row += createTd("");
						}
						if(that.order_arr[i] == "operate"){
							var edit = createLink("demo_template.php?section=contract_manage&act=info&id="+value.contract_id, "编辑");
							edit += createLink("demo_template.php?section=contract_manage&act=bind&id="+value.contract_id, "绑定供应商");
							row += createTd(edit);
						}
					}
					row += "</tr>";
					$("#contract_list>tbody").html(row);
				});
			}
		}, 'json');
	},

	getEdit: function(){
		var id = getQueryStringByName('id');
		var params = {"contract_id": id, "params":{}};
		var strJson = createJson("singleCont", "contract", params);
		var that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error){
				console.log(obj.message);
				return false;
			}
			$.each(obj.content.data, function(key, value){
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
				if(key == "attachment"){
					$("#filename").show();
					$("#filename>span").text(value);
				}
			});
			$('#handle_button').html(createButton('Contract.getUpdate()', '保存'));
			var str = "";
			if(obj.content.cat.length <= 0){
				$('#contract_material').html(createWarn("无物料类型"));
				return false;
			}
			for(var i=0; i<obj.content.cat.length; i++){
				str += "<div class='checkbox'>"+createCheckbox('cat_id', obj.content.cat[i].cat_id, obj.content.cat[i].cat_name, 1)+"</div>";
			}
			$('#contract_material_list').html(str);
		}, "json");
	},

	getUpdate: function(){
		var id = getQueryStringByName('id');
		var form_data = $("#contract_content").FormtoJson();
		var params = {"contract_id": id, "params":form_data};
		var strJson = createJson("contUp", "contract", params);
		console.log(strJson);
		return false;
		$.post(this.url, strJson, function(obj){
			console.log('test')
			console.log(obj);
		});
	},

	getUpdateMaterial: function(){
		var id = getQueryStringByName('id');
		var form_data = $("#contract_material").FormtoJson();
//		var params = 
		console.log(form_data);
		$.each(form_data, function(k,v){

		});
	},

	getUploadFile: function(){
		var id = getQueryStringByName('id');
		var params = {"contract_id": id, "params":{}};
		var strJson = createJson("uploadify", "contract_attachment_name", params);
        $.ajaxFileUpload({
            url:this.url,
            fileElementId:'contract_attachment_name',//file标签的id
            dataType: 'json',//返回数据的类型  
            data:strJson,//一同上传的数据  
            success: function (data, status) {
				if(data.error){
					$('#contract_attachment_form>span[data-name=error]').html(createError(data.message));
				}else{
					$('#contract_attachment_form>span[data-name=error]').html('');
					$('#filename').text(data.content);
				}
            },
            error: function (data, status, e) {
                alert(e);
            }
        });  		
	}
}
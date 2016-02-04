var Contract = {
	order_arr: [
		'contract_num',
		'contract_name',
		'contract_type',
		'companyName',
		'start_time',
		'end_time',
		'registration',
		'contract_status',
		'total_amount',
		'total_amount_valid',
		'bill_amount_valid',
		'cash_amount_valid',
		'operate'
	],
	contract_supplier_order: [
		'companyName',
		'contract_name',
		'contract_num',
		'suppliers_name',
		'region_name'
	],
	limit: 0,
	offset: 8,
	total_page: 0,
	current_page: 1,
	url: "contract_manage.php",

	getList: function(search){
		if(typeof(search) === "undefined"){
			var params = {"params":{"limit":this.limit, "offset":this.offset}};
		}else{
			var condition = {};
			var search_type = $('#contract_search_form select[name=search_type]').val();
			var search_value = $('#contract_search_form input[name=search_value]').val();
			var contract_status = $('#contract_search_form select[name=contract_status]').val();
			var start_time = $('#contract_search_form input[name=start_time]').val();
			var end_time = $('#contract_search_form input[name=end_time]').val();
			if(search_value != ''){
				condition.like = {"search_type":search_type, "search_value":search_value};
			}
			if(contract_status != ''){
				condition.contract_status = contract_status;
			}
			if(start_time != ''){
				condition.start_time = start_time;
			}
			if(end_time != ''){
				condition.end_time = end_time;
			}
			if(search == "search"){
				this.current_page = 1;
				this.limit = 0;
			}
			var params = {"params":{"where":condition, "limit":this.limit, "offset":this.offset}};
		}
		strJson = createJson("contList", "contract", params);
		that = this
		$.post(this.url, strJson, function(obj){
			console.log(obj);
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				that.total_page = Math.ceil(obj.content.total/that.offset);
				if(obj.content.total == 0){
					var row = "<tr><td colspan='20'>"+createWarn("无数据")+"</td></tr>";
					$("#contract_list>tbody").html(row);
					$("#paginate").html('');
				}else{
					$("#paginate").html(createPaginate(that.url, obj.content.total, that.current_page, that.limit, that.offset));
					var row = "";
					$.each(obj.content.data,function(key, value){
						row += "<tr>";
						for(var i=0;i<that.order_arr.length;i++){
							if(that.order_arr[i] == "total_amount"){
								row += createTd(parseFloat(value.bill_amount_history) + parseFloat(value.cash_amount_history));
								continue;	
							}
							if(that.order_arr[i] == "total_amount_valid"){
								row += createTd(parseFloat(value.bill_amount_valid) + parseFloat(value.cash_amount_valid));
								continue;								
							}
							$.each(value, function(k, v){
								if(k == that.order_arr[i]){
									if(v == null){
										row += createTd(v);
									}else{
										row += createTd(subString(v,10,true));
									}
								}
							});
							if(that.order_arr[i] == "operate"){
								var edit = createLink(that.url+"?act=contractView&id="+value.contract_id, "查看");
								edit += createLink(that.url+"?act=contractEdit&id="+value.contract_id, "编辑");
								edit += createLink(that.url+"?act=supplierSet&id="+value.contract_id, "绑定供应商");
								row += createTd(edit);
							}
						}
						row += "</tr>";
						$("#contract_list>tbody").html(row);
					});
				}
			}
			$('#message_area').html('');
		},"json");
	},

	getView: function(){
		var id = getQueryStringByName('id');
		if(id===""||!validateNumber(id)){
			return false;
		}
		var params = {"contract_id": id, "params":{}};
		var strJson = createJson("singleCont", "contract", params);
		var that = this;
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}
			$.each(obj.content.data, function(key, value){
				if(key == "bank_id"){
					that.getOrgList(value);
				}
				if(key == "customer_id"){
					that.getUserList(value);
				}
				if(key == "contract_type"){
					if(value == 1){
						$("td#rate").html(obj.content.data.rate)
					}else if(value == 2){
						$("#rate").html(createWarn("费率不可用"))
					}
				}
				if($("td#"+key).length && key != "rate"){
					$("td#"+key).text(value);
				}
				if($("select[name="+key+"]").length){
					$("select[name="+key+"]>option[value="+value+"]").attr("selected","selected");
				}
				if($("input[name="+key+"]").length){
					var o = "input[name="+key+"]";
					if($(o).attr("type") == "radio"){
						$("input[name="+key+"][value="+value+"]").attr("checked","1");
					}
				}
				if(key == "attachment"){
					if(value != ""){
						$("td#attachment_file").html(createLink("image.php?act=view&url="+value, "查看文件", "javascript:void(0)","blank")+createLink("image.php?act=download&url="+value, "下载", "javascript:void(0)","blank"));
					}else{
						$("td#attachment_file").html(createWarn("无附件"));
					}
				}
				if(key == "is_control" && value == 1){
					$('#is_goods_type_visible').html(createLink("javascript:void(0);", "查看物料类型", "popupLayer()"));
				}else if(key == "is_control" && value == 0){
					$('#is_goods_type_visible').html(createWarn("物料类型不可用"));
				}
			});
			$('#handle_button').html(createButton('redirectToUrl("contract_manage.php?act=contractList")', '返回列表') + createButton('Contract.getUpdate()', '保存'));
			var row = "";
			if(obj.content.cat.length <= 0){
				var goods_type = false;
				$('#goods_type_list').html('<div style="text-align:center">'+createWarn('无物料类型')+'</div>');
			}else{
				var row = '<ul>';
				for(var i=0; i<obj.content.cat.length; i++){
					row += "<li>"+createCheckbox('goods_type', obj.content.cat[i].cat_id, obj.content.cat[i].cat_name, 1)+"</li>";
				}
				row += "</ul>";
				$('#goods_type_list').html(row);
			}
			$('#message_area').html('');
		}, "json");
	},

	getEdit: function(){
		var id = getQueryStringByName('id');
		if(id===""||!validateNumber(id)){
			return false;
		}
		var params = {"contract_id": id, "params":{}};
		var strJson = createJson("singleCont", "contract", params);
		var that = this;
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}
			$.each(obj.content.data, function(key, value){
				if(key == "bank_id"){
					that.getOrgList(value);
				}
				if(key == "customer_id"){
					that.getUserList(value, obj.content.data.user_id);
				}
				if(key == "contract_type"){
					if(value == 1){
						$('#is_rate_visible>input').attr('disabled', false);
					}else if(value == 2){
						$('#is_rate_visible>input').attr('disabled', true);
					}
				}
				if($("input[name="+key+"]").length){
					var o = "input[name="+key+"]";
					if($(o).attr("type") == "text"){
						$(o).val(value);
					}
					if($(o).attr("type") == "radio"){
						$("input[name="+key+"][value="+value+"]").attr("checked","1");
					}
				}
				if($("textarea[name="+key+"]").length){
					$("textarea[name="+key+"]").text(value);
				}
				if($("select[name="+key+"]").length){
					$("select[name="+key+"]>option[value="+value+"]").attr("selected","selected");
				}
				if(key == "attachment" && value != ""){
					$("#attachment_name").parent().show();
					$("#attachment_name").text(value);
				}
				if(key == "is_control" && value == 1){
					$('#is_goods_type_visible').html(createLink("javascript:void(0);", "查看物料类型", "popupLayer()"));
				}else if(key == "is_control" && value == 0){
					$('#is_goods_type_visible').html(createWarn("物料类型不可用"));
				}
				if(key == "attachment"){
					$("#filename").show();
					$("#filename>span").text(value);
				}
			});
			$('#handle_button').html(createButton('redirectToUrl("contract_manage.php?act=contractList")', '返回列表') + createButton('Contract.getUpdate()', '保存'));
			var row = "";
			if(obj.content.cat.length <= 0){
				var goods_type = false;
			}else{
				var row = "<ul>";
				for(var i=0; i<obj.content.cat.length; i++){
					row += "<li>"+createCheckbox('goods_type', obj.content.cat[i].cat_id, obj.content.cat[i].cat_name, 1)+"</li>";
				}
				row += "</ul>";
				$('#goods_type_list').html(row);
				var goods_type = obj.content.cat;
			}
			that.getGoodsType(goods_type);
			$('#message_area').html('');
		}, "json");
	},
	// 合同更新
	getUpdate: function(){
		if($("#contract_content").valid() === false){
			return false;
		}
		var id = getQueryStringByName('id');
		if(id===""||!validateNumber(id)){
			return false;
		}
		var form_data = $("#contract_content").FormtoJson();
		var good_type = $('#goods_type_form').FormtoJson();
		$.each(good_type, function(k,v){
			form_data[k] = v;
		});
		form_data.attachment = $('#attachment_name').text();
		var params = {"contract_id": id, "params":form_data};
		var strJson = createJson("contUp", "contract", params);
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
			}else{
				$('#message_area').html(createTip('保存成功'));
				redirectToUrl("contract_manage.php?act=contractList");
			}
		}, "json");
	},
	// 合同文件上传
	getUploadFile: function(){
		if($("#attachment_form").valid()===false){
			return false;
		}
		var params = {};
		var strJson = createJson("uploadify", "attachment_file", params, "object");
		console.log(strJson);
        $.ajaxFileUpload({
            url:this.url,
            fileElementId:'attachment_file',//file标签的id
            dataType: 'json',//返回数据的类型  
            data:strJson,//一同上传的数据
            success: function (data, status) {
				if(data.error){
					$('#message_area').html(createError(data.message));
				}else{
					$('#message_area').html(createTip('上传成功'));
					$('#attachment_name').parent().show();
					$('#attachment_name').text(data.content);
				}
            },
            error: function (data, status, e) {
                console.log(e);
            }
        });  		
	},

	getGoodsType: function(goods_type_bind){
		var params = {"params":{}};
		var strJson = createJson("catList", "goods_type", params);
		var goods_type_arr = [];
		if(goods_type_bind){
			$.each(goods_type_bind, function(k, v){
				goods_type_arr.push(v.cat_id);
			})
		}else{
			goods_type_arr = false;
		}
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				if(obj.content.length <= 0){
					$('#goods_type_form').html(createWarn("无物料类型"));
				}else{
					
					var row = "<ul>";
					$.each(obj.content, function(k,v){
						if(goods_type_arr){
							if(goods_type_arr.indexOf(v.cat_id) == -1){
								row += "<li>"+createCheckbox('goods_type', v.cat_id, v.cat_name, 0)+"</li>";
							}else{
								row += "<li>"+createCheckbox('goods_type', v.cat_id, v.cat_name, 1)+"</li>";
							}
						}else{
							row += "<li>"+createCheckbox('goods_type', v.cat_id, v.cat_name, 0)+"</li>";
						}
						if(v.list.length == 0){
							return;
						}else{
							$.each(v.list, function(k1, v1){
								if(goods_type_arr){
									if(goods_type_arr.indexOf(v1.cat_id) == -1){
										row += "<li>"+createCheckbox('goods_type', v1.cat_id, v1.cat_name, 0)+"</li>";
									}else{
										row += "<li>"+createCheckbox('goods_type', v1.cat_id, v1.cat_name, 1)+"</li>";
									}
								}else{
									row += "<li>"+createCheckbox('goods_type', v1.cat_id, v1.cat_name, 0)+"</li>";
								}
								if(v1.list.length == 0){
									return;
								}else{
									$.each(v1.list, function(k2, v2){
										if(goods_type_arr){
											if(goods_type_arr.indexOf(v2.cat_id) == -1){
												row += "<li>"+createCheckbox('goods_type', v2.cat_id, v2.cat_name, 0)+"</li>";
											}else{
												row += "<li>"+createCheckbox('goods_type', v2.cat_id, v2.cat_name, 1)+"</li>";
											}
										}else{
											row += "<li>"+createCheckbox('goods_type', v2.cat_id, v2.cat_name, 0)+"</li>";
										}
										if(v1.list.length == 0){
											return;
										}else{
											$.each(v2.list, function(k3, v3){
												if(goods_type_arr){
													if(goods_type_arr.indexOf(v3.cat_id) == -1){
														row += "<li>"+createCheckbox('goods_type', v3.cat_id, v3.cat_name, 0)+"</li>";
													}else{
														row += "<li>"+createCheckbox('goods_type', v3.cat_id, v3.cat_name, 1)+"</li>";
													}
												}else{
													row += "<li>"+createCheckbox('goods_type', v3.cat_id, v3.cat_name, 0)+"</li>";
												}
											});								
										}
									});								
								}
							});
						}
					});
					row += "</ul>";
					$('#goods_type_list').html(row);
				}
			}
			$('#message_area').html('');
		}, 'json');
	},

	putInsert: function(){
		if($("#contract_content").valid() === false){
			return false;
		}
		var form_data = $("#contract_content").FormtoJson();
		var good_type = $('#goods_type_form').FormtoJson();
		$.each(good_type, function(k,v){
			form_data[k] = v;
		});
		form_data.attachment = $('#attachment_name').text();
		var params = {"params":form_data};
		var strJson = createJson("contIn", "contract", params);
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				redirectToUrl("contract_manage.php?act=contractList");
			}
		}, "json");
	},

	getContSupsList: function(search){
		var condition = {};
		if(typeof(search) === "undefined"){
			serach = false;
		}else{
			var customer_id = $('#search_form select[name=customer_id]').val();
			var contract_id = $('#search_form select[name=contract_id]').val();
			if(contract_id != ''){
				condition["contract_id"] = contract_id;
			}
			var region_id = $('#search_form select[name=region_id]').val();
			if(customer_id != ''){
				condition["customer_id"] = customer_id;
			}
			if(customer_id != '' && contract_id != ''){
				condition["contract_id"] = contract_id;
			}
			if(region_id != ''){
				condition["region_id"] = region_id;
			}
			if(search == "search"){
				this.limit = 0;
			}
		}
		var params = {"params":{"where":condition, "limit":this.limit, "offset":this.offset}};
		strJson = createJson("contSupsList", "contract_suppliers", params);
		that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				that.total_page = Math.ceil(obj.content.total/that.offset);
				if(obj.content.total == 0){
					$("#paginate").html('');
					var row = "<tr><td colspan='20'>"+createWarn("无数据")+"</td></tr>";
					$("#list>tbody").html(row);
				}else{
					$("#paginate").html(createPaginate(that.url, obj.content.total, that.current_page, that.limit, that.offset));
					var row = "";
					$.each(obj.content.data,function(key, value){
						row += "<tr>";
						for(var i=0;i<that.contract_supplier_order.length;i++){
							$.each(value, function(k, v){
								if(k == that.contract_supplier_order[i]){
									if(k == "contract_name"){
										row += createTd(v, that.url+"?act=supplierSet&id="+value.contract_id);
									}else{
										row += createTd(v);
									}
								}
							});
						}
						row += "</tr>";
						$("#list>tbody").html(row);
					});
				}
			}
			$('#message_area').html('');
		}, "json");
	},

	getUserList: function(customer_id, user_id){
		var params = {"params":{}};
		var strJson = createJson("userList", "users", params);
		var that = this;
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				var row = '';
				if(customer_id){
					if(user_id){
						that.getKidUser(customer_id, user_id);	
					}else{
						that.getKidUser(customer_id);	
					}
				}else{
					that.getKidUser(obj.content[0].user_id);
				}
				$.each(obj.content, function(k,v){
					if(typeof(customer_id) !== 'undefined' && v.user_id == customer_id){
						row += appendOption(v.user_id, v.companyName, 1, v.user_name);
					}else{
						row += appendOption(v.user_id, v.companyName, 0, v.user_name);
					}
				});
				$("select[name=customer_id]").append(row);
			}
		}, "json");
		$('#message_area').html('');
	},

	getKidUser: function(parent_id, user_id){
		var params = {"parent_id":parent_id};
		var strJson = createJson("kidUser", "users", params);
		var that = this;
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				var row = '';
				if(obj.content.length == 0){
					$("input[name=user_name]").val('');
					$("select[name=user_id]").html('');
					return false;
				}
				$("input[name=user_name]").val(obj.content[0].user_name);
				$.each(obj.content, function(k,v){
					if(typeof(user_id) !== 'undefined' && v.user_id == user_id){
						row += appendOption(v.user_id, v.user_name, 1);
						$("input[name=user_name]").val(v.user_name);
					}else{
						row += appendOption(v.user_id, v.user_name, 0);
					}
				});
				$("select[name=user_id]").html(row);
			}
		}, "json");
		$('#message_area').html('');
	},

	//下游客户的采购合同列表
	getBuyCont: function(){
		var customer_id = $("select[name=customer_id]").val();
		if(customer_id == ''){
			$("select[name=contract_id]").html('<option value="">全部</option>');
			return false;
		}
		var params = {"customer_id" : customer_id};
		strJson = createJson("buyCont", "contract", params);
		that = this;
		console.log(strJson);
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				var row = '<option value="">全部</option>';
				if(obj.content.length > 0){
					$.each(obj.content, function(k,v){
						row += appendOption(v.contract_id, v.contract_name);
					});
				}
				$("select[name=contract_id]").html(row);
			}
		}, "json");
		$('#message_area').html('');
	},

	getRegionList: function(){
		var params = {"params":{}};
		strJson = createJson("regionList", "region", params);
		that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				var row = '';
				$.each(obj.content, function(k,v){
					row += appendOption(v.region_id, v.region_name);
				});
				$("select[name=region_id]").append(row);
			}
		}, "json");
		$('#message_area').html('');
	},

	getSuppliers: function(contract_id, search){
		if(typeof(contract_id) === "undefined"){
			var condition = {};
		}else{
			var condition = {"contract_id":contract_id};
		}
		if(search === true){
			var region_id = $("select[name=region_id]").val();
			if(region_id != 0){
				condition["region_id"] = region_id;
			}
		}
		strJson = createJson("suppliers", "admin_user", condition);
		that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				var row = '';
				$.each(obj.content, function(k,v){
					row += appendOption(v.suppliers_id, v.suppliers_name);
				});
				$("#liOptionms2side__sx").html(row);
			}
		}, "json");
		$('#message_area').html('');
	},

	getContIdSupsList: function(contract_id, search){
		var contract_id = getQueryStringByName('id');
		if(contract_id===""||!validateNumber(contract_id)){
			if($('select[name=contract_id]').val() != ''){
				contract_id = $('select[name=contract_id]').val();
			}
		}
		var condition = {};
		condition["contract_id"] = contract_id;
		if(search === true){
			var region_id = $("select[name=region_id]").val();
			var customer_id = $("select[name=customer_id]").val();
			var contract_id = $("select[name=contract_id]").val();
			if(region_id != 0){
				condition["region_id"] = region_id;
			}
			if(customer_id != ''){
				condition["customer_id"] = customer_id;
				if(contract_id != ''){
					condition["contract_id"] = contract_id;
				}
			}
		}
		condition["flag"] = 1;
		strJson = createJson("suppliers", "admin_user", condition);
		console.log(strJson);
		that = this
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				that.total_page = Math.ceil(obj.content.total/that.offset);
				var row = '';
				$.each(obj.content, function(k,v){
					row += appendOption(v.suppliers_id, v.suppliers_name);
				});
				$("select#liOptionms2side__dx").html(row);
			}
			$('#message_area').html('');
		}, "json");
	},

	setContInSups: function(contract_id){
		var contract_id = getQueryStringByName('id');
		if(contract_id===""||!validateNumber(contract_id)){
			if($('select[name=contract_id]').val() != ''){
				contract_id = $('select[name=contract_id]').val();
			}else{
				$('#message_area').html(createError('合同无效'));
				return false;
			}
		}
		var ContInSupsId = new Array();
		$.each($('#liOptionms2side__dx option'), function(){
			ContInSupsId.push($(this).val());
		});
		var params = {"contract_id":contract_id, "params":{"suppliers_id":ContInSupsId}};
		strJson = createJson("contInSups", "contract_suppliers", params);
		console.log(strJson);
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				$('#message_area').html(createTip("保存成功"));
				redirectToUrl("contract_manage.php?act=supplierList");
			}
		}, "json");
	},

	getOrgList: function(bank_id){
		var params = {};
		strJson = createJson("orgList", "bank", params);
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				var row = '';
				$.each(obj.content,function(k,v){
					if(typeof(bank_id) !== "undefined" && v.bank_id == bank_id){
						row += appendOption(v.bank_id, v.bank_name, 1);
					}else{
						row += appendOption(v.bank_id, v.bank_name);
					}
				});
				$('select[name=bank_id]').html(row);
			}
		}, "json");
	}
}
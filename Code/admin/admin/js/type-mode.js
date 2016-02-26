// admin_users 为收款方
// users 为付款方

var TypeMode = {
	url: "TypeModel.php",

	getBillType: function(select_id){
		var strJson = createJson("bill_type", "bill_type", {});
		that = this;
		$.post(that.url, strJson, function(object){
			if(object.error == -1){
				$('#message_area').html(createError(object.message));
				return false;
			}else{
				var row = "";
				$.each(object.content, function(k, v){
					row += appendOption(k, v);
				})
				$('#'+select_id).html(row);
			}
			$('#message_area').html('');
		},"json");
	},

	getBillRepayType: function(select_id){
		var strJson = createJson("bill_repay_type", "bill_repay_type", {});
		that = this;
		$.post(that.url, strJson, function(object){
			if(object.error == -1){
				$('#message_area').html(createError(object.message));
				return false;
			}else{
				var row = "";
				$.each(object.content, function(k, v){
					row += appendOption(k, v);
				})
				$('#'+select_id).html(row);
			}
			$('#message_area').html('');
		},"json");
	},

	getBillStatus: function(select_id, func){
		var strJson = createJson("bill_status", "bill_status", {});
		that = this;
		$.ajax({
			url: that.url,
			data: strJson,
			method: "POST",
			dataType: "json",
			success: function(object){
				if(object.error == -1){
					$('#message_area').html(createError(object.message));
					return false;
				}else{
					var row = "";
					$.each(object.content, function(k, v){
						row += appendOption(k, v);
					})
					$('#'+select_id).append(row);
				}
				$('#message_area').html('');
			},
			complete: function(){
				if(func){
					window[func]();
				}
			}
		});
		return false;
	},

	getUsers: function(select_id, check_id){
		var strJson = createJson("users", "users", {});
		that = this;
		$.post(that.url, strJson, function(object){
			if(object.error == -1){
				$('#message_area').html(createError(object.message));
				return false;
			}else{
				var row = "";
				$.each(object.content, function(k, v){
					if(check_id == v.user_id){
						row += appendOption(v.user_id, v.companyName, 1);
					}else{
						row += appendOption(v.user_id, v.companyName);
					}
				})
				$('#'+select_id).html(row);
			}
			$('#message_area').html('');
		},"json");
	},

	getUserList: function(){
		var order_arr = ["user_id","user_name","companyName","user_nickname","user_email","user_phone","operate"];
		var strJson = createJson("users", "users", {});
		that = this;
		$.post(that.url, strJson, function(object){
			if(object.error == -1){
				$('#message_area').html(createError(object.message));
				return false;
			}else{
				console.log(object);
				$("#paginate").html(createPaginate(that.url, object.content.total, that.limit, that.offset));
				var row = "";
				$.each(object.content,function(key, value){
					row += "<tr>";
					for(var i=0;i<order_arr.length;i++){
						if(order_arr[i] == "operate"){
							var edit = createLink("demo_template.php?section=bill_manage&act=assign_note&id="+value.user_id, "分配商票采购额度");
							edit += createLink("demo_template.php?section=bill_manage&act=assign_cash&id="+value.user_id, "分配现金采购额度");
							edit += createLink("demo_template.php?section=bill_manage&act=adjust&id="+value.user_id, "额度调整");
							row += createTd(edit);
							continue;
						}
						if(value[order_arr[i]] != null){
							row += createTd(subString(value[order_arr[i]],16,true));
						}else{
							row += createTd(createWarn('无数据'));
						}
					}
					row += "</tr>";
					$("#user_list>tbody").html(row);
				});
			}
			$('#message_area').html('');
		},"json");
	},

	getUserBanks: function(select_id, user_id, check_id){
		if(user_id == ''){
			return false;
		}
		var strJson = createJson("user_banks", "user_banks", {"user_id":user_id});
		console.log(strJson);
		that = this
		$.post(that.url, strJson, function(object){
			if(object.error == -1){
				$('#message_area').html(createError(object.message));
				return false;
			}else{
				var row = appendOption("", "全部");
				$.each(object.content, function(k, v){
					if(v.bank_id == check_id){
						row += appendOption(v.bank_id, v.bank_name, 1);
					}else{
						row += appendOption(v.bank_id, v.bank_name);
					}
				})
				$("#"+select_id).html(row);
			}
			$('#message_area').html('');
		},"json");
	},

	getUserBanksAccounts: function(select_id, user_id, bank_id, check_id){
		if(user_id == ''){
			return false;
		}
		var strJson = createJson("user_bank_accounts", "user_bank_accounts", {"user_id":user_id, "bank_id":bank_id});
		that = this;
		$.post(that.url, strJson, function(object){
			if(object.error == -1){
				$('#message_area').html(createError(object.message));
				return false;
			}else{
				var row = appendOption("", "全部");
				$.each(object.content, function(k, v){
					if(check_id == v.account){
						row += appendOption(v.account, v.account, 1);
					}else{
						row += appendOption(v.account, v.account);
					}
				})
				$('#'+select_id).html(row);
			}
			$('#message_area').html('');
		}, "json");
	},

	getAdminUsers: function(select_id){
		var strJson = createJson("admin_users", "admin_users", {});
		that = this;
		$.post(that.url, strJson, function(object){
			if(object.error == -1){
				$('#message_area').html(createError(object.message));
				return false;
			}else{
				var row = "";
				$.each(object.content, function(k, v){
					row += appendOption(v.user_id, v.user_name);
				})
				$('#'+select_id).html(row);
			}
			$('#message_area').html('');
		},"json");
	},

	getAdminUserBanks: function(select_id, user_id, check_id){
		if(user_id == ''){
			return false;
		}
		var strJson = createJson("admin_user_banks", "admin_user_banks", {"user_id":user_id});
		var that = this;
		$.post(that.url, strJson, function(object){
			if(object.error == -1){
				$('#message_area').html(createError(object.message));
				return false;
			}else{
				var row = appendOption("", "选择银行");
				$.each(object.content, function(k, v){
					if(k == check_id){
						row += appendOption(k, v, true);
					}else{
						row += appendOption(k, v);
					}
				})
				$('#'+select_id).html(row);
			}
			$('#message_area').html('');
		},"json");
	},

	getAdminUserBanksAccounts: function(select_id, user_id, bank_id){
		if(user_id == '' || bank_id == ''){
			return false;
		}
		var strJson = createJson("admin_user_bank_accounts", "admin_user_bank_accounts", {"user_id":user_id, "bank_id":bank_id});
		that = this;
		$.post(that.url, strJson, function(object){
			if(object.error == -1){
				$('#message_area').html(createError(object.message));
				return false;
			}else{
				var row = appendOption("", "选择账户");
				$.each(object.content, function(k, v){
					row += appendOption(k, v);
				})
				$('#'+select_id).html(row);
			}
			$('#message_area').html('');
		},"json");
	},

	getOrderStatus: function(select_id, func){
		var strJson = createJson("order_status", "order_status", {});
		var that = this;
		$.post(that.url, strJson, function(object){
			if(object.error == -1){
				$('#message_area').html(createError(object.message));
				return false;
			}else{
				var row = '';
				$.each(object.content, function(k, v){
					row += appendOption(k, v);
				})
				$('#'+select_id).append(row);
			}
			$('#message_area').html('');
		},"json").done(function(){
			if(func){
				window[func]();
			}			
		});
	},

	getPurchaseOrderStatus: function(select_id, func){
		var strJson = createJson("purchase_status", "purchase_status", {});
		var that = this;
		$.post(that.url, strJson, function(object){
			if(object.error == -1){
				$('#message_area').html(createError(object.message));
				return false;
			}else{
				var row = '';
				$.each(object.content, function(k, v){
					row += appendOption(k, v);
				})
				$('#'+select_id).append(row);
			}
			$('#message_area').html('');
		},"json").done(function(){
			if(func){
				window[func]();
			}			
		});
	},

	getChilderOrderStatus: function(select_id, func){
		var strJson = createJson("childer_order_status", "childer_order_status", {});
		var that = this;
		$.ajax({
			url: that.url,
			data: strJson,
			method: "POST",
			dataType: 'json',
			success: function(object){
				if(object.error == -1){
					$('#message_area').html(createError(object.message));
					return false;
				}else{
					var row = '';
					$.each(object.content, function(k, v){
						row += appendOption(k, v);
					})
					$('#'+select_id).append(row);
				}
				$('#message_area').html('');
			},
			complete: function(){
				if(func){
					window[func]();
				}
			}
		});
	},

	getPurchasePayStatus: function(select_id, func){
		var strJson = createJson("purchase_pay_status", "purchase_pay_status", {});
		var that = this;
		$.post(that.url, strJson, function(object){
			if(object.error == -1){
				$('#message_area').html(createError(object.message));
				return false;
			}else{
				var row = '';
				$.each(object.content, function(k, v){
					row += appendOption(k, v);
				})
				$('#'+select_id).append(row);
			}
			$('#message_area').html('');
		},"json").done(function(){
			if(func){
				window[func]();
			}
		});
	},

	getPurchaseOrderPayStatus: function(select_id, func){
		var strJson = createJson("purchase_order_pay_status", "purchase_order_pay_status", {});
		var that = this;
		$.post(that.url, strJson, function(object){
			if(object.error == -1){
				$('#message_area').html(createError(object.message));
				return false;
			}else{
				var row = '';
				$.each(object.content, function(k, v){
					row += appendOption(k, v);
				})
				$('#'+select_id).append(row);
			}
			$('#message_area').html('');
		},"json").done(function(){
			if(func){
				window[func]();
			}
		});
	}	
}
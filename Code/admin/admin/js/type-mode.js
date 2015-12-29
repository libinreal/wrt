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

	getUsers: function(select_id){
		var strJson = createJson("users", "users", {});
		that = this;
		$.post(that.url, strJson, function(object){
			if(object.error == -1){
				$('#message_area').html(createError(object.message));
				return false;
			}else{
				var row = "";
				$.each(object.content, function(k, v){
					row += appendOption(v.user_id, v.companyName);
				})
				$('#'+select_id).html(row);
			}
			$('#message_area').html('');
		},"json");
	},

	getUserBanks: function(select_id, user_id){
		var strJson = createJson("user_banks", "user_banks", {"user_id":user_id});
		$.post(that.url, strJson, function(object){
			if(object.error == -1){
				$('#message_area').html(createError(object.message));
				return false;
			}else{
				var row = appendOption("", "全部");
				$.each(object.content, function(k, v){
					row += appendOption(v.bank_id, v.bank_name);
				})
				$("#"+select_id).html(row);
			}
			$('#message_area').html('');
		},"json");
	},

	getUserBanksAccounts: function(select_id, user_id, bank_id){
		var strJson = createJson("user_bank_accounts", "user_bank_accounts", {"user_id":user_id, "bank_id":bank_id});
		that = this;
		$.post(that.url, strJson, function(object){
			if(object.error == -1){
				$('#message_area').html(createError(object.message));
				return false;
			}else{
				var row = appendOption("", "全部");
				$.each(object.content, function(k, v){
					row += appendOption(v.account, v.account);
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
					row += appendOption(k, v);
				})
				$('#'+select_id).html(row);
			}
		},"json");
	},

	getAdminUserBanks: function(select_id, user_id){
		var strJson = createJson("admin_user_banks", "admin_user_banks", {"user_id":user_id});
		that = this;
		$.post(that.url, strJson, function(object){
			if(object.error == -1){
				$('#message_area').html(createError(object.message));
				return false;
			}else{
				var row = appendOption("", "选择银行");
				$.each(object.content, function(k, v){
					row += appendOption(k, v);
				})
				$('#'+select_id).html(row);
			}
		},"json");		
	},

	getAdminUserBanksAccounts: function(select_id, user_id, bank_id){
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
		},"json");
	}
}
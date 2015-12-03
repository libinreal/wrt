define(function(require) {
	var $ = require('jquery'),
		config = require('./config'),
		Ajax = require('../base/ajax'),
		Storage = require('../base/storage'),
		Tools = require('../base/tools');

	require('../base/common');
	require('../module/validator/local/zh_CN');

	//validator config
	$.validator.config({
		focusCleanup: true,
		msgWrapper: 'div',
		msgMaker: function(opt) {
			return '<span class="' + opt.type + '">' + opt.msg + '</span>';
		}
	});

	//回填用户相关数据
	config.afterCheckAccount = function(account){
		$('#username').text(account.account || '');
		$('#mobile').text(account.telephone || '');
		$('input[name="contacts"]').val(account.contacts || '');
		$('input[name="email"]').val(account.email || '');
		$('input[name="fax"]').val(account.fax || '');
		$('input[name="gender"]').each(function() {
			if ($(this).val() == account.gender) {
				$(this).prop('checked', true);
				return;
			}
		});
		$('input[name="officePhone"]').val(account.officePhone || '');
		$('input[name="companyName"]').val(account.companyName || '');
		$('input[name="companyAddress"]').val(account.companyAddress || '');
		$('input[name="position"]').val(account.position || '');
		$('input[name="secondContacts"]').val(account.secondContacts || '');
		$('input[name="secondPhone"]').val(account.secondPhone || '');
		$('input[name="weixin"]').val(account.weixin || '');
		$('input[name="qq"]').val(account.qq || '');
	}

	//验证提交表单
	$('#updateInfoForm').validator({
		fields: {
			"email": {
				rule: "email;"
			},
			"contacts": {
				rule: "联系人: required;length[1~10]"
			},
			"secondContacts": {
				rule: "length[1~10]"
			},
			"secondPhone": {
				rule: "mobile"
			},
			"qq": {
				rule: "QQ: qq"
			},
			"weixin": {
				rule: "微信: length[1~20];"
			},
			"companyName": {
				rule: "单位名称: required;length[1~40]"
			},
			"companyAddress": {
				rule: "单位地址: required;length[1~150]"
			},
			"officePhone": {
				rule: "办公电话: tel|mobile"
			},
			"fax": {
				rule: "办公传真: tel"
			},
			"position": {
				rule: "职位: required;length[1~20]"
			},
			"department": {
				rule: "所在部门: required;length[1~50]"
			}
		},
		//验证成功
		valid: function() {
			Ajax.submit({
				url: config.iModifyInfo,
				data: $('#updateInfoForm'),
			}, function(data) {
				if (!data || data.code != 0) {
					Tools.showAlert(data.message);
					return;
				}
				updateUserInfo($('#updateInfoForm').serializeArray());
				location.href = "my-info.html";
			});
		}
	});

	//更新用户信息
	function updateUserInfo(arr) {
		if (!arr || !arr.length || !user) {
			return;
		}
		for (i in user) {
			for (k in arr) {
				if (i == arr[k].name) {
					log(user[i] = arr[k].value);
				}
			}
		}
		Storage.set(Storage.ACCOUNT, user);
	}
});
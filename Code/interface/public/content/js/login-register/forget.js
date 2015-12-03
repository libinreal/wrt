define(function(require) {
	var $ = require('jquery'),
		msg = require('./messages'),
		rules = require('./rules'),
		Tools = require('../base/tools'),
		Storage = require('../base/storage'),
		config = require('./config'),
		Ajax = require('../base/ajax'),
		Cookie = require('../base/cookie');

	require('../base/common');

	var mobile = $('input[name="mobile"]'),
		verifyCode = $('input[name="verifyCode"]'),
		timer = undefined;//短信计时

	// 第一步、短信发送
	$('#sendMsg').click(function(e) {
		if ($(this).hasClass('disable-btn')) {
			return;
		}

		var _this = $(this);
		if (rules.empty.test(mobile.val())) {
			Tools.showAlert(msg.phoneEmpty);
			return;
		}
		if (!rules.mobile.test(mobile.val())) {
			Tools.showAlert(msg.phoneErr);
			return;
		}
		$(this).addClass('disable-btn');

		Ajax.custom({
			url: config.iForget,
            data: {
            	step: 1,
                mobile: mobile.val()
            },
            type: 'POST'
		}, function(data) {
			if(data.code != 0){
				_this.removeClass('disable-btn');
				Tools.showAlert(data.message);
				return;
			}

			changeBtnState(_this);
		},function(jqxhr, textStatus, errorThrown){
			log('找回密码第一步失败 ' + textStatus);
			_this.removeClass('disable-btn');
		});
	});

	// 确认第二步
	mobile.keyup(checkSubmit).blur(checkSubmit);
	verifyCode.keyup(checkSubmit).blur(checkSubmit);

	// 第二部、提交表单
	$('#forget-form').submit(function(e) {
		e.preventDefault();
		if (!validateForm()) {
			return;
		}
		var that = $(this);

		Ajax.submit({
			url: config.iForget,
			data: {
				step: 2,
				mobile: mobile.val(),
				vcode: verifyCode.val()
			},
			type: 'POST'
		}, function(data) {
			if (data.code != 0) {
				if (data.code == 204) {
					Tools.showAlert(msg.phoneNotRigester);
					return;
				}
				Tools.showAlert(data.message);
				return;
			}
			Tools.showAlert(msg.sendSmsSuccess,0,function(){
				//成功之后，清除form，停止计时，还原按钮状态，关闭弹窗
				that[0].reset();
				if(timer){
					clearInterval(timer);
				}
				$('#sendMsg').removeClass('disable-btn').prop('disabled',false);
				$('input[type="submit"]').addClass('disable-btn').prop('disabled',true);
				$('.modal').hide();
				$('#panelBg_').hide();
			});
		});
	});


	//短信计时
	function changeBtnState(obj) {
		var second = 60;
		var text = obj.val();
		obj.prop('disabled', true);
		timer = setInterval(function() {
			obj.val(text + '(' + (second--) + ')');
			if (second < 0) {
				obj.prop('disabled', false);
				obj.val(text);
				obj.removeClass('disable-btn');
				clearInterval(timer);
			}
		}, 1000);
	}

	//表单验证
	function validateForm() {
		if (rules.empty.test(mobile.val())) {
			Tools.showAlert(msg.phoneEmpty);
			return false;
		}
		if (!rules.mobile.test(mobile.val())) {
			Tools.showAlert(msg.phoneErr);
			return false;
		}
		if (rules.empty.test(verifyCode.val())) {
			Tools.showAlert(msg.vcEmpty);
			return false;
		}
		if (!rules.vcode.test(verifyCode.val())) {
			Tools.showAlert(msg.vcodeTypeErr);
			return false;
		}
		return true;
	}

	//验证提交按钮状态
	function checkSubmit(){
		$('input[type="submit"]').addClass('disable-btn').prop('disabled',true);
		if (!rules.mobile.test(mobile.val())) {
			return;
		}
		if (!rules.vcode.test(verifyCode.val())) {
			return;
		}

		$('input[type="submit"]').removeClass('disable-btn').prop('disabled',false);
	}
});
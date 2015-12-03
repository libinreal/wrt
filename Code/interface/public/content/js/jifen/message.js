define(function(require, exports, module) {

	var msg = require('../base/messages');

	//错误提示
	msg.phoneEmpty = '手机号码为空';
	msg.phoneError = '手机号码格式错误';
	msg.passwordEmpty = '密码不能为空';
	msg.passwordError = '密码格式错误';

    module.exports = msg;

});


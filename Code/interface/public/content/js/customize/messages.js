define(function(require, exports, module) {

	var msg = require('../base/messages');

	//错误提示
	msg.notEmpty = '不能为空';
	msg.phoneErr = '手机号码格式错误';
	msg.pwEmpty = '密码不能为空';
	msg.pwErr = '密码应不少于6位，请重新输入';
	msg.pwConfirmErr = '密码不一致，请重新输入';
	msg.usernameEmpty = '用户名不能为空';
	msg.usernameErr = '用户名应不少于3位，请重新输入';
	msg.vcodeErr = '验证码错误';
	msg.loginErrTip = '用户名或密码错误！';
	msg.infofail = '申请提交失败，请检查信息是否完整！'

    module.exports = msg;

});
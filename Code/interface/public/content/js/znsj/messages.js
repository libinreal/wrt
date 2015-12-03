define(function(require, exports, module) {

	var msg = require('../base/messages');

	//错误提示
	msg.unEmpty = '用户名不能为空';
	msg.pwEmpty = '密码不能为空';
	msg.vcEmpty = '验证码不能为空';
	msg.phoneErr = '手机号码格式错误';
	msg.phoneEmpty = '手机号不能为空!';
	msg.pwErr = '密码不应少于6位，请重新输入';
	msg.pwConfirmErr = '密码不一致，请重新输入';
	msg.usernameEmpty = '用户名不能为空';
	msg.usernameErr = '用户名应不少于3位，请重新输入';
	msg.vcodeErr = '验证码错误';
	msg.vcodeTypeErr = '验证码格式错误';
	msg.loginErrTip = '用户名或密码错误！';
	msg.sendSmsSuccess = '密码已发送至您的手机，请注意查收！';
	msg.phoneNotRigester = '您输入的手机号码尚未注册，请确定后再试。';
    module.exports = msg;

});


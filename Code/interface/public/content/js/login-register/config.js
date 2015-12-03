define(function(require, exports, module) {

	var config = require('../base/config');

	config.iLogin = config.server + '/account/login'; //登陆
	config.iForget = config.server + '/account/findpwd'; //找回密码
	config.iRegister = config.server + '/account/register'; //注册
	config.iVerifyUname = config.server + '/account/checkname'; //用户名唯一验证
	config.iVerifyMobile = config.server + '/account/checkmobile'; //手机号唯一验证
	config.iSendMsg = config.server + '/account/createVcode'; //获取验证码接口

	config.index = 'index.html'; //首页
	config.register = '../register.html'; //注册
	
	module.exports = config;

});
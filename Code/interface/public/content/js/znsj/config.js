define(function(require, exports, module) {

	var config = require('../base/config');

	config.help = config.server + '/helpcenter/gethelps';

	config.article = config.server + '/helpcenter/viewhelp';
	config.login = config.server + '/account/login';

	module.exports = config;
});
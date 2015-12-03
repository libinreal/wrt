define(function(require, exports, module) {

	var config = require('../base/config');
	var suffix = '.js';
	suffix = '';

	config.service = config.server + '/helpcenter/gethelps?catType=3001';
	config.guide = config.server + '/helpcenter/gethelps?catType=3002';
	config.newhand = config.server + '/helpcenter/gethelps?catType=3003';
	config.about = config.server + '/helpcenter/gethelps?catType=3004';

	config.article = config.server + '/helpcenter/viewhelp';
	config.appointment = config.server + '/helpcenter/appointment';//电话预约
	config.omplaint = config.server + '/helpcenter/omplaint';//投诉建议


	module.exports = config;
});
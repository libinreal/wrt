define(function(require, exports, module) {

	var config = require('../base/config');

	config.list = config.server + 'list';
	config.detail = config.server + 'detail';
	config.save = config.server + 'save';

    module.exports = config;

});


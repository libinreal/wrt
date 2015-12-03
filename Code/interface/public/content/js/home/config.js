define(function(require, exports, module) {

	var config = require('../base/config');

	config.list = config.server + '/list';

    module.exports = config;

});


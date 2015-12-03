define(function(require, exports, module) {

	var config = require('../base/config');

	config.pageSize = 10;

	config.newsHome = config.server + '/wrnews/gethomenews';
	config.newsList = config.server + '/wrnews/getclassifynews';
	config.newsDetail = config.server + '/wrnews/view';
	config.newsTop = config.server + '/wrnews/gettopnews';

    module.exports = config;

});
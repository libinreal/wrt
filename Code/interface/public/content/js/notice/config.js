define(function(require, exports, module) {

	var config = require('../base/config');

	config.newsHome = config.server + '/wrnews/gethomenews';
	config.newsList = config.server + '/notice/getannouncelist';
	config.newsDetail = config.server + '/notice/view';
	config.newsTop = config.server + '/notice/gettopnews';

    module.exports = config;

});
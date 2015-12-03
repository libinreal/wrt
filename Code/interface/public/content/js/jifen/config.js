define(function(require, exports, module) {

    var config = require('../base/config');

    config.pagesize = 12;

    config.getCategorys = config.server + '/gift/getcategorys';
    config.getList = config.server + '/gift/getlist';
    config.getDetail = config.server + '/gift/getdetail';
    config.exchange = config.server + '/gift/exchange';
 	config.iMyAddress = config.server+'/address/getlist';//收货地址列表
 	config.iSaveAddress = config.server+'/address/save';//保存收货地址
    module.exports = config;

});

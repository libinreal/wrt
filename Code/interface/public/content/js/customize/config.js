define(function(require, exports, module) {

    var config = require('../base/config');

    config.pageSize = 9;

    config.getapplys = config.server + '/customize/search'; //定制查询接口
    config.sendapply = config.server + '/customize/create'; //申请接口
    config.append = config.server + '/customize/append'; //追加定制
    config.getdetail = config.server + '/customize/get'; //详情接口
    config.getsort = config.server + '/goods/getAllCategory'; //分类接口
    config.goodsdetail = config.server + '/goods/detail'; //获取商品详情
    config.createProject = config.server + '/customize/createProject'; //定制申请信息
    module.exports = config;

});

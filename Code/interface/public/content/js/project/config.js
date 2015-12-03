define(function(require, exports, module) {

    var config = require('../base/config');

    config.getpnewest = config.server + '/prjnews/getpnewest';
    config.getbnewest = config.server + '/prjnews/getbnewest';
    config.getbiddingList = config.server + '/prjnews/getbiddingList';
    config.getSqLinks = config.server + '/prjnews/getlinks?type=sq';
    config.getZbLinks = config.server + '/prjnews/getlinks?type=zb';
    config.iCategoryList = config.server + '/prjnews/getrecomm';
    config.iPriceLine = config.server + '/prjnews/getpriceline';
    config.getprjtotal = config.server + '/prjnews/getprjtotal'; //获得全国工程招标信息数统计
    config.mycreamt = config.server + '/credit/mycreamt'; //
    config.getcged = config.server + '/credit/getcged'; //采购额度
    config.getpriceline = config.server + '/prjnews/getpriceline'; //价格走势数据


    //地图坐标
    config.coordinate = [{
        id: 2,
        name: '北京市',
        x: 1170,
        y: 573
    }, {
        id: 3,
        name: '湖南省',
        x: 1074,
        y: 953
    }, {
        id: 4,
        name: '福建省',
        x: 1247,
        y: 1020
    }, {
        id: 5,
        name: '甘肃省',
        x: 828,
        y: 704
    }, {
        id: 6,
        name: '广东省',
        x: 1084,
        y: 1108
    }, {
        id: 7,
        name: '广西壮族自治区',
        x: 950,
        y: 1116
    }, {
        id: 8,
        name: '贵州省',
        x: 902,
        y: 1001
    }, {
        id: 9,
        name: '海南省',
        x: 1003,
        y: 1197
    }, {
        id: 10,
        name: '河北省',
        x: 1116,
        y: 636
    }, {
        id: 11,
        name: '河南省',
        x: 1092,
        y: 747
    }, {
        id: 12,
        name: '黑龙江省',
        x: 1444,
        y: 352
    }, {
        id: 13,
        name: '湖北省',
        x: 1112,
        y: 880
    }, {
        id: 14,
        name: '湖南省',
        x: 1073,
        y: 953
    }, {
        id: 15,
        name: '吉林省',
        x: 1411,
        y: 429
    }, {
        id: 16,
        name: '江苏省',
        x: 1233,
        y: 834
    }, {
        id: 17,
        name: '江西省',
        x: 1156,
        y: 941
    }, {
        id: 18,
        name: '辽宁省',
        x: 1359,
        y: 503
    }, {
        id: 19,
        name: '内蒙古自治区',
        x: 1042,
        y: 538
    }, {
        id: 20,
        name: '宁夏回族自治区',
        x: 893,
        y: 622
    }, {
        id: 21,
        name: '青海省',
        x: 1116,
        y: 636
    }, {
        id: 22,
        name: '山东省',
        x: 1184,
        y: 683
    }, {
        id: 23,
        name: '山西省',
        x: 1064,
        y: 642
    }, {
        id: 24,
        name: '陕西省',
        x: 965,
        y: 762
    }, {
        id: 25,
        name: '上海市',
        x: 1310,
        y: 848
    }, {
        id: 26,
        name: '四川省',
        x: 833,
        y: 878
    }, {
        id: 27,
        name: '天津市',
        x: 1190,
        y: 599
    }, {
        id: 28,
        name: '西藏自治区',
        x: 481,
        y: 910
    }, {
        id: 29,
        name: '新疆维吾尔族自治区',
        x: 386,
        y: 428
    }, {
        id: 30,
        name: '云南省',
        x: 796,
        y: 1050
    }, {
        id: 31,
        name: '浙江省',
        x: 1271,
        y: 892
    }, {
        id: 32,
        name: '重庆市',
        x: 898,
        y: 892
    }, {
        id: 33,
        name: '香港特别行政区',
        x: 1109,
        y: 1110
    }, {
        id: 34,
        name: '澳门特别行政区',
        x: 1092,
        y: 1117
    }, {
        id: 35,
        name: '台湾省',
        x: 1308,
        y: 1032
    }];


    module.exports = config;

});

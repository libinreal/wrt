define(function(require, exports, module) {

	var config = require('../base/config');
    config.getdetail = config.server + '/order/getdetail';//我的订单-详情

    config.someurl = config.server + '';//合同验签


    //订单状态，0：已提交，1：确认中 ，2：验收中，3、对账中，4、完成
    config.ORDERSTATUS = {
    	'0': '已提交',
    	'1': '确认中',
    	'2': '验收中',
    	'3': '对账中',
    	'4': '完成',
    };

    //订单中发票类型
    config.INVTYPE = {
    	'0': '增值税专用发票',
    	'1': '普通发票'
    }

    //订单中的支付机构
    config.PAYENUM = {
    	'01': '浙商银行',
    	'02': '工商银行',
    	'03': '招商银行',
    	'04': '浦发银行',
    	'05': '中信银行',
    	'06': '中交银行',
    	'07': '华夏银行',
    };

    //订单状态值，0：已提交，1：确认中 ，2：验收中，3、对账中，4、完成，5、已取消
    config.ORDERENUM = {
        'YTJ': 0,
        'QRZ': 1,
        'YSZ': 2,
        'DZZ': 3,
        'WC': 4,
        'YQX': 5
    };

    //子订单状态
    config.SUBORDERSTATUS = {
        '-1': '已保存',
        '0': '已提交',
        '1': '待支付',
        '2': '确认支付',
        '3': '待验收',
        '4': '确认验收',
        '5': '完成',
    };

    //子订单状态值
    config.SUBORDERENUM = {
        'YTJ': 0,
        'DZF': 1,
        'QRZF': 2,
        'DYS': 3,
        'QRYS': 4,
        'WC': 5
    };

	module.exports = config;
});
define(function(require, exports, module) {

	var config = require('../base/config');

	config.createEvaluation = config.server + '/credit/createEvaluation';
	config.getevaluationlist = config.server + '/credit/getevaluationlist';
	config.getevaluationInfo = config.server + '/credit/getevaluationInfo';
	config.applyxyed = config.server + '/credit/applyxyed';
	config.getintrinfo = config.server + '/credit/getintrinfo';//了解更多
	config.getallapply = config.server + '/credit/getallapply';//在线申请
	
	config.mycreamt = config.server + '/credit/mycreamt'; //信用额度登记机构
	config.getcged = config.server + '/credit/getcged'; //采购额度

    module.exports = config;

});
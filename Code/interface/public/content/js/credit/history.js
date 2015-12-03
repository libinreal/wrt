define(function(require) {
    var $ = require('jquery'),
        template = require('template'),
        config = require('./config'),
        Ajax = require('../base/ajax');

    require('../base/common');
    require('scrollable');

    //模板帮助方法，确定申请状态
    //资料已提交=1，受理成功=2，审核通过=3，成功（评测结果）=4，失败（评测结果）=5
    template.helper('$checkStatus', function(content) {
        if(content == 1){
            return '<span>资料已提交</span>';
        }else if(content == 2){
            return '受理成功';
        }else if(content == 3){
            return '审核通过';
        }else if(content == 4){
            return '成功';
        }else{
            return '失败';
        }
    });

    //获取评测记录
    Ajax.paging({
        url: config.getevaluationlist,
        renderFor: 'zj-history-tmpl',
        renderEle: '#zj-history'
    },function(response){
    });

    //获取广告
    config.getAd(config.ADENUM.XYPC);
});
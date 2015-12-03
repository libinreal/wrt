define(function(require) {
    var $ = require('jquery'),
        template = require('template'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools'),
        Storage = require('../base/storage');

    require('../base/common');

    var account0,
        id = Tools.getQueryValue('id');

    //模板帮助方法，确定公司性质
    //央企=1  国企=2  股份制=3 私企=4
    template.helper('$checkNatural', function(content) {
        if (content == 1) {
            return '央企';
        } else if (content == 2) {
            return '国企';
        } else if (content == 3) {
            return '股份制';
        } else {
            return '私企';
        }
    });

    //回填用户相关数据
    config.afterCheckAccount = function(account1) {
        account = account1;
        setUserinfo();
    }

    //详情
    Ajax.detail({
        url: config.getevaluationInfo,
        data: {
            id: id
        }
    }, function(response) {
        setUserinfo();
    });

    //设置用户信息
    function setUserinfo() {
        if (!account) {
            return;
        }
        $('#account').text(account.account);
        $('#contacts').text(account.contacts);
        $('#telephone').text(account.telephone);
        $('#secondContacts').text(account.secondContacts || '--');
        $('#secondPhone').text(account.secondPhone || '--');
        $('#companyName').text(account.companyName || '--');
        $('#companyAddress').text(account.companyAddress || '--');
    }
});

define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Storage = require('../base/storage'),
        Tools = require('../base/tools');

    require('../base/common');

    //回填用户相关数据
    var tempIcon;
    config.afterCheckAccount = function(account) {
        tempIcon = account.icon;
        $('.user-icon').attr('src', config.getUserIcon(account.icon))
    }

    //信用额度登记机构
    Ajax.custom({
        url: config.mycreamt
    }, function(response) {
        var data = response.body || {},
            banks = [];
        if (response.code != 0) {
            banks = [];
        } else {
            banks = response.body.banks;
        }

        if (tempIcon) {

            data.icon = config.getUserIcon(tempIcon); //用户头像
        }
        var result = template.render('zj-summary-tmpl', data);
        $('#zj-summary').html(result);

        //处理银行数据
        var tempBanks = [];
        for (var i in config.BANKS) {
            var flag = false;
            for (var j = 0; j < banks.length; j++) {
                if (banks[j].bankFnum == i) {
                    flag = true;
                    break;
                }
            }
            if (flag) {
                tempBanks.push(banks[j]);
            } else {
                tempBanks.push({
                    'bankFnum': i,
                    'bankName': config.BANKS[i]
                });
            }
        }

        var result = template.render('zj-banks-tmpl', {
            'list': tempBanks
        });
        $('#zj-banks').html(result);
    });

    //采购额度
    Ajax.custom({
        url: config.getcged
    }, function(response) {
        if (response.code != 0) {
            return;
        }

        var result = template.render('zj-cged-tmpl', {
            'list': response.body || []
        });
        $('#zj-cged').html(result);
    });

});

define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Storage = require('../base/storage'),
        Tools = require('../base/tools');

    require('../base/common');

    // 模板帮助方法，
    // 1配送中，2已签收，3已下单
    template.helper('$getStatus', function(content) {
        if (content == 1) {
            return '配送中';
        } else if (content == 2) {
            return '已签收';
        } else {
            return '已下单';
        }
    });

    var isA = true,
        optionA = {
            url: config.iSeeCreditsExchangeOrder,
            data: {
                size: config.pageSize,
                createAt: config.beginTime
            },
            renderFor: 'zj-exchange-tmpl',
            renderEle: '#exchange'
        },
        optionB = {
            url: config.iSeeCreditsGetLog,
            data: {
                size: config.pageSize,
                createAt: config.beginTime
            },
            renderFor: 'zj-record-tmpl',
            renderEle: '#record'
        };

    //回填用户相关数据
    config.afterCheckAccount = function(account) {
        $('#account').text(account.companyName);
        $('#credit-level').text(account.creditLevel);
        $('#custom-no').text(account.customNo);
        $('#credits').text(account.credits);
        $('#custom-icon').attr('src', config.getUserIcon(account.icon));
    }

    config.paging = function() {
        Ajax.paging(isA ? optionA : optionB, function(response) {
            if (response.body.length == 0) {
                return;
            }
            if (isA) {
                optionA.beginTime = response.body[0].createAt;
                optionA.endTime = response.body[response.body.length - 1].createAt;
            } else {
                optionB.beginTime = response.body[0].createAt;
                optionB.endTime = response.body[response.body.length - 1].createAt;
            }
        });
    };
    config.paging();

    $('.nav-tabs a').click(function(e) {
        e.preventDefault();
        var name = $(this).attr('href');
        var target = $(name);

        $('.nav-tabs a').each(function() {
            $(this).removeClass('active');
            target.hide();
        });
        $('.tab-item').hide();
        $(this).addClass('active');
        target.show();

        isA = name == '#exchange';
        config.beginTime = isA ? optionA.beginTime : optionB.beginTime;
        config.endTime = isA ? optionA.endTime : optionB.endTime
        //if (target.children().length == 0) {
            config.paging();
        //}
    });

});

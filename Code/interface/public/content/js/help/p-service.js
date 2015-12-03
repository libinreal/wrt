define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools');

    require('../base/common');
    require('../module/validator/local/zh_CN');

    var flag = false;//是否已初始化时间

    // 打开弹出框
    $('.open-dialog-btn').on('click', function(e) {
        e.preventDefault();

        initTime();
        $('.panel-bg').show();
        $('#' + $(this).attr('for')).show();
    })
    // 关闭弹出框
    $('.dialog-close').click(function(e) {
        e.preventDefault();
        $('.panel-bg').hide();
        $('.dialog').hide();
    });

    $('#appointment-form').on('change', 'select', function() {
        var str = $('#day').val() + ' ' + $('#hour').val() + ':' + $('#minute').val() + ':0';
        var date = Tools.strToDate(str);
        var abc = date.getTime().toString();
        abc = abc.substring(0, 10);

        $('input[name="time"]').val(abc);
        $('#appointment-form').validator('hideMsg','input[name="time"]');
    });

    //radio
    $('input[name="type1"]').click(function(){
        var sel = $('input[name="type1"]:checked').val();
        $(this).parent().parent().next().val(sel);
        $('#appointment-form').validator('hideMsg','input[name="type"]');
    });
    //radio
    $('input[name="type2"]').click(function(){
        var sel = $('input[name="type2"]:checked').val();
        $(this).parent().parent().next().val(sel);
        $('#omplaint-form').validator('hideMsg','input[name="type"]');
    });

    //提交电话预约表单
    $('#appointment-form').validator({
        focusCleanup: true,
        msgWrapper: 'div',
        msgMaker: function(opt) {
            return '<span class="' + opt.type + '">' + opt.msg + '</span>';
        },
        rules: {},
        fields: {
            "type": {
                rule: "预约类型: required;"
            },
            "telephone": {
                rule: "手机号码: required;mobile;"
            },
            "time": {
                rule: "回电时间: required;"
            }
        },
        //验证成功
        valid: function(form) {
            Ajax.submit({
                url: config.appointment,
                data: $('#appointment-form'),
            }, function(data) {
                if (!data || data.code != 0) {
                    return;
                }

                Tools.showAlert('您的电话预约已提交', 0, function() {
                    $('#appointment-form')[0].reset();
                    $('.panel-bg').hide();
                    $('.dialog').hide();
                });
            });
        }
    });


    //提交投诉建议表单
    $('#omplaint-form').validator({
        focusCleanup: true,
        msgWrapper: 'div',
        msgMaker: function(opt) {
            return '<span class="' + opt.type + '">' + opt.msg + '</span>';
        },
        rules: {},
        fields: {
            "type": {
                rule: "投诉建议类型: required;"
            },
            "content": {
                rule: "具体内容: required;length[5~200];"
            },
            "orderNo": {
                rule: "订单号: required;"
            }
        },
        //验证成功
        valid: function(form) {
            Ajax.submit({
                url: config.omplaint,
                data: $('#omplaint-form'),
            }, function(data) {
                if (!data || data.code != 0) {
                    Tools.showToast(data.message || '操作失败');
                    return;
                }
                log(data)
                Tools.showAlert('您的投诉建议已提交', 0, function() {
                    $('#omplaint-form')[0].reset();
                    $('.panel-bg').hide();
                    $('.dialog').hide();
                });
            });
        }
    });

    function initTime() {
        if(flag)
            return;

        var date = new Date();
        var result = '';

        for (var i = 0; i < 10; i++) {
            date.setDate(date.getDate() + 1);
            var y = date.getFullYear(),
                m = date.getMonth() + 1,
                d = date.getDate(),
                h = date.getHours(),
                m0 = date.getMinutes();
            var str = y + '-' + (m < 10 ? '0' + m : m) + '-' + (d < 10 ? '0' + d : d);
            var val = y + ' ' + (m < 10 ? '0' + m : m) + ' ' + (d < 10 ? '0' + d : d);
            result += '<option value="' + str + '">' + str + '</option>'
        }

        $('#day').html(result);
    }
});

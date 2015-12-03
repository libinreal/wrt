define(function(require) {
    var $ = require('jquery'),
        msg = require('./messages'),
        config = require('./config'),
        Tools = require('../base/tools'),
        Ajax = require('../base/ajax');

    require('../base/common');
    require('../module/validator/local/zh_CN');

    var id = Tools.getQueryValue("id"),
        goodsId = Tools.getQueryValue("goodsId"),
        flag = false, //是否追加定制
        tempProvince;//省市数据

    /* 遮罩层和省份的显示与隐藏start */
    //所在区域选择显示省份
    $("#area").click(function() {
        if ($(this).prop('readonly')) {
            return;
        }
        $('#modal-province').addClass('in').show();
        if ($("#zj-province").children().length > 0) {
            return;
        }
        config.getProvince();
    });
    $("#area").focus(function() {
        if ($(this).prop('readonly')) {
            return;
        }
        $('#modal-province').addClass('in').show();
        if ($("#zj-province").children().length > 0) {
            return;
        }
        config.getProvince();
    });
    var $pro_change = $(".province-change"),
        $panel_bg = $(".panel-bg");
    //确认后赋值并隐藏省份选择框
    $("#confirm_pro").click(function() {
        $('#modal-province').hide();
        $("#area").val($(".province-list button[class='active']").html());
        $("#areaId").val($(".province-list button[class='active']").attr('data-id'));
    });
    //取消按钮
    $(".modal-close").click(function() {
        $('#modal-province').hide();
    });
    $("#zj-province").on('click', 'button', function() {
        $("#zj-province button").removeClass("active");
        $(this).addClass("active");
    });
    /* 遮罩层和省份的显示与隐藏end */

    //提交申请表单
    $('#applyForm').validator({
        focusCleanup: true,
        msgWrapper: 'div',
        msgMaker: function(opt) {
            return '<span class="' + opt.type + '">' + opt.msg + '</span>';
        },
        fields: {
            "name": {
                rule: "项目名称: required;"
            },
            "address": {
                rule: "项目地址: required;"
            },
            "period": {
                rule: "项目工期: required;"
            },
            "amount": {
                rule: "项目投资额度: required;integer;"
            },
            "areaId": {
                rule: "销售区域: required;"
            },
            "contactPeople": {
                rule: "联系人: required;"
            },
            "position": {
                rule: "职位: required;"
            },
            "contactTelephone": {
                rule: "联系方式: required;[mobile|tel];"
            },
            "remark": {
                rule: "项目筹资情况: required;"
            }

        },
        //验证成功
        valid: function(form) {
            Ajax.submit({
                url: flag ? config.append : config.createProject,
                data: $("#applyForm")
            }, function(data) {
                if (data.code != 0) {
                    Tools.showAlert(data.message || msg.infofail);
                    return;
                }

                Tools.showAlert({
                    message: '您的' + $("#name").val() + '工程定制申请已提交！',
                    yesCallback: function() {
                        location.href = '../personal/my-customize.html';
                    }
                });
            });
        }
    });

});

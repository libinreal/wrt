define(function(require) {
    var $ = require('jquery'),
        msg = require('./messages'),
        config = require('./config'),
        Tools = require('../base/tools'),
        Ajax = require('../base/ajax');

    require('../base/common');
    require('../module/validator/local/zh_CN');
    require('../module/jQuery-File-Upload-9.5.7/jquery.fileupload');

    var id = Tools.getQueryValue("id"),
        goodsId = Tools.getQueryValue("goodsId"),
        categorys = [], //分类接口数据
        flag = false, //是否追加定制
        tempProvince;//省市数据

    //获取定制详细信息
    if (id != "") {
        Ajax.custom({
            url: config.getdetail,
            data: {
                id: id
            }
        }, function(data) {
            if (data.code != 0) {
                return;
            }
            var d = data.body;
            //追加定制仅数量 ，地址，电话，联系人，留言可修改
            flag = true;
            $("#applyId").val(d.id);
            $('#categoryNo').attr('disabled', 'true');
            $('#categoryNo').parent().addClass('disabled');
            $('#categoryNo-children').val(d.categoryNo).attr('disabled', 'true');
            $('#categoryNo-children').parent().addClass('disabled');
            $("#goodsName").val(d.goodsName).attr("readonly", "true");
            $("#goodsUnit").val(d.goodsUnit).attr("readonly", "true");
            $("#goodsSpec").val(d.goodsSpec).attr("readonly", "true");
            $("#goodsModel").val(d.goodsModel).attr("readonly", "true");
            $("#goodsPrice").val(d.goodsPrice).attr("readonly", "true");
            $("#area").val(d.area).attr("readonly", "true");
            $("#areaId").val('0'); //设置默认值通过验证
            if (d.originalImg) {
                $("#goodsImg").attr("src", config.absImg(d.originalImg)).attr("readOnly", "true");
                $('input[name="originalImg"]').val(d.originalImg);
            }
            $('#picurl').hide();
            $('#validDateEnum').val(d.validDateEnum).attr('disabled', 'true');

            //动态设置下拉框
            setCategory(d.categoryNo);
        });
    } else if (goodsId) {
        Ajax.custom({
            url: config.goodsdetail,
            data: {
                id: goodsId
            }
        }, function(data) {
            if (data.code != 0) {
                return;
            }
            var d = data.body.goodsDetail;
            //加载该商品的销售区域，分类，商品名，规格，型号，单位，价格
            // $('#categoryNo').attr('disabled', 'true');
            $('#categoryNo-children').val(d.categoryNo);
            $("#goodsName").val(d.name);
            $("#goodsUnit").val(d.unit);
            // $("#goodsSpec").val(d.goodsSpec);
            // $("#goodsModel").val(d.goodsModel);
            $("#goodsPrice").val(d.price);
            $("#area").val(d.salesArea);
            $("#areaId").val('0'); //设置默认值通过验证
            if (d.thumb) {
                $("#goodsImg").attr("src", config.absImg(d.thumb));
                $('input[name="originalImg"]').val(d.thumb);
                $('input[name="goodsImg"]').val(d.thumb);
            }

            if (data.body.nav && data.body.nav.length > 2) {
                //动态设置下拉框，需要商品的导航有至少两级数据
                setCategory(data.body.nav[1].code);
            }
            config.getProvince('','',function(response){
                if(response.code != 0){
                    return;
                }
                tempProvince = response.body;
                for (var i = tempProvince.length - 1; i >= 0; i--) {
                    if(tempProvince[i].name == d.salesArea){
                        $("#areaId").val(tempProvince[i].id);
                        break;
                    }
                };
            });
        });
    }

    //获取分类信息
    Ajax.custom({
        url: config.getsort
    }, function(response) {
        categorys = response.body;
        setCategory();
    });
    //绑定切换事件，拉取二级分类数据
    $("#categoryNo").on('change', function() {
        var code = $(this).val();
        $.each(categorys, function() {
            if (this.code == code) {
                var result = template.render('zj-sort-tmpl', {
                    'list': this.children
                });
                $('#categoryNo-children').html(result);
            }
        });
    });

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
            "goodsName": {
                rule: "商品名: required;"
            },
            "originalImg": {
                rule: "商品图片: required;"
            },
            "goodsNum": {
                rule: "数量: required;digits;"
            },
            "goodsUnit": {
                rule: "单位: required;"
            },
            "goodsPrice": {
                rule: "价格: required;number;"
            },
            "areaId": {
                rule: "销售区域: required;"
            },
            "address": {
                rule: "地址: required;"
            },
            "telephone": {
                rule: "联系电话: required;[mobile|tel];"
            },
            "contacts": {
                rule: "联系人: required;"
            },
            "validDateEnum": {
                rule: "有效期: required;"
            }
        },
        //验证成功
        valid: function(form) {
            Ajax.submit({
                url: flag ? config.append : config.sendapply,
                data: $("#applyForm")
            }, function(data) {
                if (data.code != 0) {
                    Tools.showAlert(data.message || msg.infofail);
                    return;
                }

                Tools.showAlert({
                    message: '您的 ' + $("#goodsName").val() + ' 定制申请已提交！',
                    yesCallback: function() {
                        location.href = 'index.html';
                    }
                });
            });
        }
    });

    //图片上传接口
    $('input[type="file"]').fileupload({
        url: config.upload,
        dataType: "JSON",
        acceptFileTypes: /(\.|\/)(jpe?g|png)$/i,
        maxFileSize: 5000000,
        done: function(e, data) {
            $(this).next().attr("src", config.absImg(data.result.body[0]));
            $(this).parent().next().val(data.result.body[0]);
            $('input[name="goodsImg"]').val('');
            $('#applyForm').validator('hideMsg', $(this).parent().next());
        },
        process: function(e, data) {
            for (var i = 0, l = data.processQueue.length; i < l; i++) {
                if (data.processQueue[i].action == 'validate') {
                    data.messages.acceptFileTypes = '上传文件格式不支持.';
                }
            }
            data.messages.maxFileSize = '上传文件太大，限制' + data.maxFileSize / 1000 + 'K以内.';
        },
        processalways: function(e, data) {
            var index = data.index,
                file = data.files[index];
            if (file.error) {
                alert(file.error);
            }
        },
        fail: function(e) {}
    });

    //初始化并赋值动态下拉框
    var a = 0,
        b = 0,
        c = undefined;

    function setCategory(code) {
        if (code) {
            c = code;
        }
        for (var i = 0; i < categorys.length; i++) {
            for (var j = 0; j < categorys[i].children.length; j++) {
                if (categorys[i].children[j].code == c) {
                    a = i;
                    b = j;
                    break;
                }
            }
        }

        if (categorys && categorys.length > 0) {
            var result = template.render('zj-sort-tmpl', {
                'list': categorys
            });
            $('#categoryNo').html(result);
            if (a >= 0) {
                $('#categoryNo').next().val(categorys[a].code).trigger('change');

                var result = template.render('zj-sort-tmpl', {
                    'list': categorys[a].children
                });
                $('#categoryNo-children').html(result);
                if (b >= 0) {
                    $('#categoryNo-children').next().val(categorys[a].children[b].code).trigger('change');
                }
            }
        }
    }

});

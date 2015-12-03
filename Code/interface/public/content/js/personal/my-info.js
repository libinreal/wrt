define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools'),
        Storage = require('../base/storage');

    require('../base/common');
    require('fileupload');

    // 模板帮助方法，确定会员类型
    template.helper('$getRoleName', function(content) {
        if (!content)
            return '&nbsp;';

        return config.ROLE[content] || '--';
    });

    // 模板帮助方法，确定会员权限
    template.helper('$checkPermisson', function(content) {
        if (!content)
            return '&nbsp;';

        return config.ROLEPERMISSION[content] || '--';
    });

    //回填用户相关数据
    config.afterCheckAccount = function(account) {
        $('.user-icon').attr('src', config.getUserIcon(account.icon))
        $('#user-data').append(template.render('user-tmpl', account));
    }

    //upload icon
    $('input:file').fileupload({
        url: config.upload,
        dataType: "JSON",
        acceptFileTypes: /(\.|\/)(jpe?g|png)$/i,
        maxFileSize: 5000000,
        done: function(e, data) {
            $('.user-icon').attr('src', config.absImg(data.result.body[0]));
            $('input[name="icon"]').val(data.result.body[0]);
        },
        process: function(e, data) {
            for (var i = 0, l = data.processQueue.length; i < l; i++) {
                if (data.processQueue[i].action == 'validate') {
                    data.messages.acceptFileTypes = '上传文件格式不支持.';
                }
            }
            data.messages.maxFileSize = '上传文件太大，限制' + data.maxFileSize / 1000 + 'K以内.';
        }
    });

    //default icon
    $('#icons').on('click', 'img', function(e) {
        e.preventDefault();
        $('.user-icon').attr('src', $(this).attr('src'));
        $('input[name="icon"]').val($(this).attr('data-icon'));
    });

    //保存头像
    $('#uploadIconForm').submit(function(e) {
        e.preventDefault();
        Ajax.custom({
            url: config.iModifyIcon,
            type: 'POST',
            data: {
                icon: $('input[name="icon"]').val()
            }
        }, function(response) {
            if (response.code != 0) {
                Tools.showToast(response.message || '保存头像失败！');
                return;
            }

            Tools.showToast('保存头像成功！');
        });
    });
});

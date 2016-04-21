define(function(require) {
    var template = require('template'),
        config = require('./config'),
        Tools = require('./tools');

    template.openTag = "<!--[";
    template.closeTag = "]-->";

    // 模板帮助方法，绝对化图片地址
    template.helper('$absImg', function(content, defaultValue) {
        return config.absImg(content);
    });

    // 模板帮助方法，获取用户头像
    template.helper('$getUserIcon', function(content) {
        return config.getUserIcon(content);
    });

    // 模板帮助方法，转换时间戳成字符串
    template.helper('$formatDate', function(content, type, defaultValue) {
        if (content) {
            if (content.length == 10)
                content = content + '000';
            return Tools.formatDate(content, type);
        } else {
            return defaultValue || '--';
        }
    });

    // 模板帮助方法，转换房源你的标签
    template.helper('$convertTag', function(content) {
        if (content) {
            var result = '';
            var arr = content.split(',');
            for (var i in arr) {
                if (/^\s*$/.test(arr[i])) {
                    continue;
                }
                result += '<span>' + arr[i] + '</span>';
            }
            return result;
        } else {
            return '--';
        }
    });

    //模板帮助方法，编码url参数
    template.helper('$encodeUrl', function(content) {
        return encodeURIComponent(content);
    });

    //模板帮助方法，格式化货币
    template.helper('$formatCurrency1', function(content, defaultValue, unit) {
        if (!content) {
            return defaultValue || '--';
        }
        content = content + '';//转字符串

        var prefix,subfix,idx = content.indexOf('.');
        if(idx > 0){
            prefix = content.substring(0, idx);
            subfix = content.substring(idx, content.length);
        }else{
            prefix = content;
            subfix = '';
        }

        var mod = prefix.toString().length % 3;
        var sup = '';
        if (mod == 1) {
            sup = '00';
        } else if (mod == 2) {
            sup = '0';
        }

        prefix = sup + prefix;
        prefix = prefix.replace(/(\d{3})/g, '$1,');
        prefix = prefix.substring(0, prefix.length - 1);
        if (sup.length > 0) {
            prefix = prefix.replace(sup, '');
        }
        if(subfix){
            if(subfix.length == 2){
                subfix += '0';
            }else if(subfix.length == 1){
                subfix += '00';
            }
            subfix = subfix.substring(0,3);
        }

        return prefix + subfix;
        // return content + (unit || '');
    });

    //模板帮助方法，\r\n替换换行
    template.helper('$convertRN', function(content) {
        if (!content) {
            return '--';
        }
        return content.replace(/\r\n/gi, '<br/>');
    });

    //模板帮助方法，根据序列值添加样式名
    template.helper('$addClassByIdx', function(i, v, className) {
        if (i == v) {
            return className || '';
        }
    });

    //模板帮助方法，截取内容长度添加省略号
    template.helper('$ellipsis', function(content, length) {
        var v = content.replace(/[^\x00-\xff]/g, '__').length;
        if (v / 2 > length) {
            return content.substring(0, length) + '...';
        }
        return content;
    });

    //模板帮助方法，拼接新闻URL
    template.helper('$newsUrl', function(id) {
        var type = Tools.getQueryValue('type');
        if (type) {
            return 'detail.html?id=' + id + '&type=' + type;
        } else {
            return 'detail.html?id=' + id + '&type=brand';
        }
    });

    //模板帮助方法，确定公告类型文字
    //商城公告(2002)，定制专区公告(2003)，积分公告(2004)
    template.helper('$noticeType', function(content) {
        if (content == 2002) {
            return '商城公告';
        } else if (content == 2003) {
            return '定制专区';
        } else if (content == 2004) {
            return '积分公告';
        } else {
            return '未知';
        }
    });

    //模板帮助方法，格式化货币
    template.helper('$formatCurrency', function(content, i) {
        if (!content) {
            return '--';
        }
        content = content + '';//转字符串

        //1200.55->1200<span class="c-red f-s">.55</span>
        var p, f, idx = content.indexOf('.');
        if (idx > 0) {
            p = content.substring(0, idx);
            f = content.substr(idx, 3);
        } else {
            p = content;
            f = '.00';
        }
        return p + '<span data="'+ p+f +'" class="f-s">' + f + '</span>';
    });

});

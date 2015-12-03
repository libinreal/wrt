//全局配置
var config = {};
config.server = 'http://61.155.169.177:9988';
// config.server = 'http://127.0.0.1:9901';
config.image = 'http://61.155.169.177:9988';
config.server1 = '/api';
config.notice = config.server + '/notice/getannouncelist';
config.ad = config.server + '/global/get';
config.newsHome = config.server + '/wrnews/gethomenews';
config.newsList = config.server + '/wrnews/getclassifynews';
config.newsTop = config.server + '/wrnews/gettopnews';
config.newsDetail = config.server + '/wrnews/view';

config.noticeList = config.server + '/notice/getannouncelist';
config.noticeDetail = config.server + '/notice/view';
config.noticeTop = config.server + '/notice/gettopnews';

config.helpList = config.server + '/helpcenter/gethelps';
config.helpDetail = config.server + '/helpcenter/viewhelp';

config.appointment = config.server + '/helpcenter/appointment';//电话预约
config.omplaint = config.server + '/helpcenter/omplaint';//投诉建议

config.getprjtotal = config.server + '/prjnews/getprjtotal'; //获得全国工程招标信息数统计

config.nodata = '暂无数据。';

config.position = {//广告位置
    '1001': 3,
    '1002': 4,
    '1003': 5
};
// 广告枚举
// 2：物融新闻-首页
// 3：物融新闻-媒体声音
// 4: 物融新闻-品牌新闻 
// 5: 物融新闻-市场活动
// 11：公告-商城公告
// 12: 公告-定制专区
// 13: 公告-积分公告
// 14: 公告广告
config.ADENUM = {
    WRXW: 2,
    MTSY: 3,
    PPXW: 4,
    SCHD: 5,
    SCGG: 11,
    DZGG: 12,
    JFGG: 13,
    GGGG: 14
};

// 模版标签
template.openTag = "<!--[";
template.closeTag = "]-->";

// 模板帮助方法，绝对化图片地址
template.helper('$absImg', function(content) {
    if (!content) {
        return config.image + 'content/images/common/blank.png';
    }
    if (content && content.indexOf('http://') == 0) {
        return content;
    }
    return config.image + content;
});

// 模板帮助方法，转换时间戳成字符串
template.helper('$formatDate', function(content, type, defaultValue) {
    if (content) {
        if (content.length == 10)
            content = content + '000';
        return formatDate(content, type || 5);
    } else {
        return defaultValue || '--';
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

function formatDate(content, type) {
    var pattern = "yyyy-MM-dd hh:mm:ss";
    switch (type) {
        case 1:
            pattern = "yyyy年M月d日";
            break;
        case 2:
            pattern = "hh:mm";
            break;
        case 3:
            pattern = "yyyy.M.d";
            break;
        case 4:
            pattern = "yyyy-MM-dd hh:mm";
            break;
        case 5:
            pattern = "yyyy/MM/dd";
            break;
        case 6:
            pattern = "yyyy-MM-dd";
            break;
        default:
            pattern = !!type ? type : pattern;
            break;
    }
    if (isNaN(content) || content == null) {
        return content;
    } else if (typeof(content) == 'object') {
        var y = dd.getFullYear(),
            m = dd.getMonth() + 1,
            d = dd
            .getDate();
        if (m < 10) {
            m = '0' + m;
        }
        var yearMonthDay = y + "-" + m + "-" + d;
        var parts = yearMonthDay.match(/(\d+)/g);
        var date = new Date(parts[0], parts[1] - 1, parts[2]);
        return format(date, pattern);
    } else {
        var date = new Date(parseInt(content));
        return format(date, pattern);
    }
};

function format(date, pattern) {
    var that = date;
    var o = {
        "M+": that.getMonth() + 1,
        "d+": that.getDate(),
        "h+": that.getHours(),
        "m+": that.getMinutes(),
        "s+": that.getSeconds(),
        "q+": Math.floor((that.getMonth() + 3) / 3),
        "S": that.getMilliseconds()
    };
    if (/(y+)/.test(pattern)) {
        pattern = pattern.replace(RegExp.$1, (that.getFullYear() + "")
            .substr(4 - RegExp.$1.length));
    }
    for (var k in o) {
        if (new RegExp("(" + k + ")").test(pattern)) {
            pattern = pattern.replace(RegExp.$1, RegExp.$1.length == 1 ? o[k] : ("00" + o[k]).substr(("" + o[k]).length));
        }
    }
    return pattern;
};

function getQueryValue(key) {
    var q = location.search,
        keyValuePairs = new Array();

    if (q.length > 1) {
        var idx = q.indexOf('?');
        q = q.substring(idx + 1, q.length);
    } else {
        q = null;
    }

    if (q) {
        for (var i = 0; i < q.split("&").length; i++) {
            keyValuePairs[i] = q.split("&")[i];
        }
    }

    for (var j = 0; j < keyValuePairs.length; j++) {
        if (keyValuePairs[j].split("=")[0] == key) {
            // 这里需要解码，url传递中文时location.href获取的是编码后的值
            // 但FireFox下的url编码有问题
            return decodeURI(keyValuePairs[j].split("=")[1]);

        }
    }
    return '';
};

function log(msg) {
    if (typeof console != undefined) {
        console.log(msg);
    }
}

//获取公告
//物融公告(2001)，商城公告(2002)，定制专区公告(2003)，积分公告(2004)
function getNotice(type, size, renderFor, renderEle) {
    renderFor = renderFor || 'zj-notice-tmpl';
    renderEle = renderEle || '#zj-notice';
    size = size || 6;

    $.ajax({
        url: config.notice,
        data: {
            type: type,
            size: size
        },
        dataType: 'JSON',
        type: 'GET'
    }).then(function(response, textStatus, jqXHR) {
        if (response.code != 0) {
            log('获取公告接口失败');
            return;
        }

        var result = template.render(renderFor, {
            'list': response.body || []
        });
        $(renderEle).html(result || config.nodata);

    }, function(jqXHR, textStatus, errorThrown) {
        log(textStatus)
    });
};

//获取广告
//position 查看接口文档
function getAd(position, callback, renderFor, renderEle) {
    renderFor = renderFor || 'zj-ad-tmpl';
    renderEle = renderEle || '#zj-ad';

    $.ajax({
        url: config.ad,
        data: {
            position: position
        },
        dataType: 'JSON',
        type: 'GET'
    }).then(function(response, textStatus, jqXHR) {
        if (response.code != 0) {
            return;
        }
        var result = template.render(renderFor, {
            'list': response.body || []
        });
        $(renderEle).html(result);

        if (typeof callback == 'function') {
            callback(response);
        }
    }, function(jqXHR, textStatus, errorThrown) {
        log(textStatus)
    });
};

//初始化滚动图片,每个页面限一个
function initScroll(opt, mode) {
    var nav = $('.subscript');
    var len = $('.scroller').children().length;
    if (len == 0) {
        return;
    }
    $('.scroller').css({
        width: $('.scroller').width() * len
    });
    var res = '';
    for (var i = 0; i < len; i++) {
        if (i == 0) {
            res += '<span class="active"></span>';
        } else {
            res += '<span></span>';
        }
    }
    nav.html(res);
    previewScroll = new iScroll($('.slider')[0], {
        snap: true,
        momentum: false,
        hScrollbar: false,
        onScrollEnd: function() {
            var cur = $('#img-' + this.currPageX);
            cur.attr('src', cur.attr('data-src'));
            nav.find('span').removeClass('active');
            nav.find('span').eq(this.currPageX).addClass('active');
            $(nav.find('span')[this.currPageX]).addClass('active');
        }
    });

    $('#img-0').attr('src', $('#img-0').attr('data-src'));

};

//地图坐标
config.coordinate = [{
    id: 2,
    name: '北京市',
    x: 1170,
    y: 573
}, {
    id: 3,
    name: '湖南省',
    x: 1074,
    y: 953
}, {
    id: 4,
    name: '福建省',
    x: 1247,
    y: 1020
}, {
    id: 5,
    name: '甘肃省',
    x: 828,
    y: 704
}, {
    id: 6,
    name: '广东省',
    x: 1084,
    y: 1108
}, {
    id: 7,
    name: '广西壮族自治区',
    x: 950,
    y: 1116
}, {
    id: 8,
    name: '贵州省',
    x: 902,
    y: 1001
}, {
    id: 9,
    name: '海南省',
    x: 1003,
    y: 1197
}, {
    id: 10,
    name: '河北省',
    x: 1116,
    y: 636
}, {
    id: 11,
    name: '河南省',
    x: 1092,
    y: 747
}, {
    id: 12,
    name: '黑龙江省',
    x: 1444,
    y: 352
}, {
    id: 13,
    name: '湖北省',
    x: 1112,
    y: 880
}, {
    id: 14,
    name: '湖南省',
    x: 1073,
    y: 953
}, {
    id: 15,
    name: '吉林省',
    x: 1411,
    y: 429
}, {
    id: 16,
    name: '江苏省',
    x: 1233,
    y: 834
}, {
    id: 17,
    name: '江西省',
    x: 1156,
    y: 941
}, {
    id: 18,
    name: '辽宁省',
    x: 1359,
    y: 503
}, {
    id: 19,
    name: '内蒙古自治区',
    x: 1042,
    y: 538
}, {
    id: 20,
    name: '宁夏回族自治区',
    x: 893,
    y: 622
}, {
    id: 21,
    name: '青海省',
    x: 1116,
    y: 636
}, {
    id: 22,
    name: '山东省',
    x: 1184,
    y: 683
}, {
    id: 23,
    name: '山西省',
    x: 1064,
    y: 642
}, {
    id: 24,
    name: '陕西省',
    x: 965,
    y: 762
}, {
    id: 25,
    name: '上海市',
    x: 1310,
    y: 848
}, {
    id: 26,
    name: '四川省',
    x: 833,
    y: 878
}, {
    id: 27,
    name: '天津市',
    x: 1190,
    y: 599
}, {
    id: 28,
    name: '西藏自治区',
    x: 481,
    y: 910
}, {
    id: 29,
    name: '新疆维吾尔族自治区',
    x: 386,
    y: 428
}, {
    id: 30,
    name: '云南省',
    x: 796,
    y: 1050
}, {
    id: 31,
    name: '浙江省',
    x: 1271,
    y: 892
}, {
    id: 32,
    name: '重庆市',
    x: 898,
    y: 892
}, {
    id: 33,
    name: '香港特别行政区',
    x: 1109,
    y: 1110
}, {
    id: 34,
    name: '澳门特别行政区',
    x: 1092,
    y: 1117
}, {
    id: 35,
    name: '台湾省',
    x: 1308,
    y: 1032
}];

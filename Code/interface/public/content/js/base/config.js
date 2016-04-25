//debug
var log = function(m) {
    if (typeof console != 'undefined') {
//        console.log(m);
    }
};

define(function() {
    var config = {
        begin: 0, //当前第几页，从0开始
        beginTime: undefined, //第一条数据的时间戳
        endTime: undefined, //最后一条数据的时间戳
        pageSize: 10, //默认分页大小
        forward: undefined, //分页方向，0向后1向前，默认空
        server: location.protocol + '//' + location.host,
        image: location.protocol + '//' + location.host + '/'
        // image: 'http://61.155.169.178:9988/' //测试服务器上图片
        // image: 'localhost:9901/' //测试服务器上图片
    };

    //公用接口
    config.ad = config.server + '/global/get'; //广告
    config.notice = config.server + '/notice/getannouncelist'; //公告
    config.province = config.server + '/global/getProvinceList'; //全国省市
    config.upload = config.server + '/global/upload'; //上传文件
    config.homeNews = config.server + '/wrnews/getclassifynews'; //首页的新闻
    config.getCategorys = config.server + '/goods/getAllCategory'; //商品的分类
    config.contracts = config.server + '/goods/getcontracts'; //获取合同列表
    config.addFavorite = config.server + '/favorites/save'; //新增收藏
    config.deleteFavorite = config.server + '/favorites/delete'; //取消收藏
    config.logout = config.server + '/account/logout'; //取消收藏
    config.getaccount = config.server + '/account/get'; //获取用户信息

    config.historylist = config.server + '/goods/historylist'; //历史浏览记录

    config.index = "../index.html"; //首页配置
    config.nodata = '<div class="nodata">暂无数据。</div>';

    //绝对化图片地址
    config.absImg = function(content) {

        if (!content) {
            // 测试时使用相对
            return '../content/images/common/d300x300-1.png';
        }
        if (content && content.indexOf('http://') == 0) {
            return content;
        }
        return config.image + content;
    };

    //获取用户头像
    config.getUserIcon = function(content) {
        var a = config.ICON[content];

        return a || config.absImg(content);
    }

    //银行编码对应表
    config.BANKS = {
        '01': '浙商银行',
        '02': '工商银行',
        '03': '招商银行',
        '04': '浦发银行',
        '05': '中信银行',
        '06': '中交物融',
        '07': '华夏银行'
    }

    //用户头像对应表
    config.ICON = {
        'icon01': '../content/images/personal/icon_head1.png',
        'icon02': '../content/images/personal/icon_head2.png',
        'icon03': '../content/images/personal/icon_head3.png',
        'icon04': '../content/images/personal/icon_head4.png',
        'icon05': '../content/images/personal/icon_head5.png'
    }

    //0网站注册会员 1交易会员 2交易下单会员
    config.ROLE = {
        '0': '网站注册会员',
        '1': '交易会员',
        '2': '交易下单会员'
    };

    //用户权限
    config.ROLEPERMISSION = {
        '0': '无下单权限',
        '1': '无下单权限',
        '2': '有下单权限'
    };

    // 广告枚举
    // 1：定制专区-首页
    // 2：物融新闻-首页
    // 3：物融新闻-媒体声音
    // 4: 物融新闻-品牌新闻 
    // 5: 物融新闻-市场活动
    // 6: 积分商城-首页
    // 7: 基建商城-首页
    // 8：信用池-首页
    // 9：信用池-信用评测
    // 10：信用池-了解更多
    // 11：公告-商城公告
    // 12: 公告-定制专区
    // 13: 公告-积分公告
    // 14: 公告广告
    // 15: 智能数据
    // 16: 移动物融
    // 24: 移动物融顶部右侧
    config.ADENUM = {
        DZZQ: 1,
        WRXW: 2,
        MTSY: 3,
        PPXW: 4,
        SCHD: 5,
        JFSC: 6,
        JJSC: 7,
        XYC: 8,
        XYPC: 9,
        LJGD: 10,
        SCGG: 11,
        DZGG: 12,
        JFGG: 13,
        GGGG: 14,
        ZNSJ: 15,
        YDWR: 16,
        YDWR2: 24
    };

    //公告枚举，物融公告(2001)，商城公告(2002)，定制专区公告(2003)，积分公告(2004)
    config.NOTICEENUM = {
        WRXW: 2001,
        JJSC: 2002,
        DZZQ: 2003,
        JFSC: 2004
    };

    return config;
});

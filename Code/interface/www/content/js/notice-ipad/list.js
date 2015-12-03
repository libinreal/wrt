(function() {

    var myScroll,
        pullDownEl, pullDownOffset,
        pullUpEl, pullUpOffset,
        generatedCount = 0,
        createAt,
        size = 11,
        type = getQueryValue('type'),
        page = 0,
        previewScroll,
        position = config.ADENUM.GGGG; //广告位置;

    document.addEventListener('touchmove', function(e) {
        e.preventDefault();
    }, false);

    document.addEventListener('DOMContentLoaded', loaded, false);

    // $('#menu a').click(function(e) {
    //     e.preventDefault();

    //     config.init($(this).attr('data-type'));
    // });

    //初始化，并获取数据
    //@param t 公告分类 2002=商城公告,2003=定制专区公告,2004=积分公告
    config.init = function(t) {
        type = t;

        //确认type
        if (type == '2002') {
            position = config.ADENUM.SCGG;
        } else if (type == '2003') {
            position = config.ADENUM.DZGG;
        } else if (type == '2004') {
            position = config.ADENUM.JFGG;
        }
        localStorage.setItem('NOTICETYPE', type);

        // 计算菜单状态
        if(type){
            $('#menu a').removeClass('active');
            $('#menu a').each(function() {
                if ($(this).attr('href').indexOf(type) >= 0) {
                    $(this).addClass('active');
                }
            });
        }
        size = 11;
        createAt = undefined;

        getList();
        getAd(position);
        getRec();
    };
    config.init(type);

    //获取公告列表数据
    function getList() {
        $.ajax({
            url: config.noticeList,
            data: {
                type: type,
                size: size,
                createAt: createAt
            },
            dataType: 'JSON',
            type: 'GET'
        }).then(function(response, textStatus, jqXHR) {
            if (response.code != 0) {
                log('获取公告列表接口失败');
                return;
            }

            var data = response.body || [];
            var result = template.render('zj-list-tmpl', {
                'list': data,
                'type': type
            });
            if (createAt) {
                $('#thelist').append(result);
            } else {
                $('#thelist').html(result);
            }

            if (response.body && response.body.length > 0) {
                //记录分页时间戳
                createAt = response.body[response.body.length - 1].createAt;
                page++;
            }

            myScroll.refresh();
        }, function(jqXHR, textStatus, errorThrown) {
            log(textStatus)
        });
    };

    //获取置顶公告
    function getRec() {
        $.ajax({
            url: config.noticeTop,
            data: {
                type: type
            },
            dataType: 'JSON',
            type: 'GET'
        }).then(function(response, textStatus, jqXHR) {
            if (response.code != 0) {
                log('获取置顶公告接口失败');
                return;
            }

            var d = response.body,
                result;
            //接口返回值可能为空数组，这里过滤掉
            if (d && d.length != 0) {
                d.type = type;
                result = template.render('zj-rec-tmpl', d);
                $('#rec-detail').html(result);
                initScroll();
            } else {
                $('#rec-detail').html('');
            }
        }, function(jqXHR, textStatus, errorThrown) {
            log(textStatus)
        });
    }

    function pullDownAction() {
        createAt = undefined;
        size = 11;
        getList();
    }

    function pullUpAction() {
        size = 5;
        getList();
    }

    function loaded() {
        pullDownEl = document.getElementById('pullDown');
        pullDownOffset = pullDownEl.offsetHeight;
        pullUpEl = document.getElementById('pullUp');
        pullUpOffset = pullUpEl.offsetHeight;

        myScroll = new iScroll('wrapper', {
            useTransition: true,
            topOffset: pullDownOffset,
            onRefresh: function() {
                if (pullDownEl.className.match('loading')) {
                    pullDownEl.className = '';
                    pullDownEl.querySelector('.pullDownLabel').innerHTML = 'Pull down to refresh...';
                } else if (pullUpEl.className.match('loading')) {
                    pullUpEl.className = '';
                    pullUpEl.querySelector('.pullUpLabel').innerHTML = 'Pull up to load more...';
                }
            },
            onScrollMove: function() {
                if (this.y > 5 && !pullDownEl.className.match('flip')) {
                    pullDownEl.className = 'flip';
                    pullDownEl.querySelector('.pullDownLabel').innerHTML = 'Release to refresh...';
                    this.minScrollY = 0;
                } else if (this.y < 5 && pullDownEl.className.match('flip')) {
                    pullDownEl.className = '';
                    pullDownEl.querySelector('.pullDownLabel').innerHTML = 'Pull down to refresh...';
                    this.minScrollY = -pullDownOffset;
                } else if (this.y < (this.maxScrollY - 5) && !pullUpEl.className.match('flip')) {
                    pullUpEl.className = 'flip';
                    pullUpEl.querySelector('.pullUpLabel').innerHTML = 'Release to refresh...';
                    this.maxScrollY = this.maxScrollY;
                } else if (this.y > (this.maxScrollY + 5) && pullUpEl.className.match('flip')) {
                    pullUpEl.className = '';
                    pullUpEl.querySelector('.pullUpLabel').innerHTML = 'Pull up to load more...';
                    this.maxScrollY = pullUpOffset;
                }
            },
            onScrollEnd: function() {
                if (pullDownEl.className.match('flip')) {
                    pullDownEl.className = 'loading';
                    pullDownEl.querySelector('.pullDownLabel').innerHTML = 'Loading...';
                    pullDownAction();
                } else if (pullUpEl.className.match('flip')) {
                    pullUpEl.className = 'loading';
                    pullUpEl.querySelector('.pullUpLabel').innerHTML = 'Loading...';
                    pullUpAction();
                }
            }
        });
    }

})();

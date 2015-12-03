(function() {
    var type = getQueryValue('type') || '3001',
        id;

    // 接口地址
    var interfaces = {
        newhand: config.newhand,
        guide: config.guide,
        service: config.service,
        about: config.about
    };

    //初始化，并获取数据
    //@param t 分类（售后服务(3001)新手教学(3002)采购指南(3003)关于我们(3004)。）
    //@param i 编号
    config.init = function(t, i) {
        type = t;
        id = i;

        // 获取导航列表
        $.ajax({
            url: config.helpList,
            data: {
                catType: type
            }
        }).then(function(result) {
            if (result.code != 0) {
                return;
            }
            if (!result.body || !result.body.length) {
                return;
            }
            //关于我们中添加物融新闻链接
            if(type == '3004'){
                $('#link-to-news').show();
            }
            $('#menu').html(template.render('navs-tmpl', {
                list: result.body || []
            }));

            $('#menu a').on('click', function(e) {
                e.preventDefault();
                $('#menu a').removeClass('active');
                $(this).addClass("active");
                seeArticle($(this).attr('data-id'));
            });

            // 默认文章
            if (location.hash) {
                var idx = location.hash.replace('#', '');
                var cur = $('#menu a:eq(' + idx + ')');
                cur.addClass('active');
                seeArticle(cur.attr('data-id'));
            } else if(id){
                $('#menu a').each(function(){
                    if($(this).attr('data-id') == id){
                        $(this).addClass('active');
                    }
                })
                seeArticle(id);
            }else{
                $('#menu').find('a:first').addClass("active");
                seeArticle(result.body[0].id);
            }
        });
    };
    config.init(type, id);

    // 查看文章
    var detailContainer = $('#news-detail')

    function seeArticle(id) {
        if (!id) return;

        detailContainer.html('数据加载中...');
        $.ajax({
            url: config.helpDetail,
            data: {
                id: id
            },
            type: 'GET'
        }).then(function(result) {
            if (!result.body) {
                return;
            }

            detailContainer.html(result.body.content || config.nodata);
        }, function() {
            detailContainer.html('加载失败，请重新加载');
        });
    }

})();

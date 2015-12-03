    getData();

    // var map = new SpryMap({
    //     id: "worldMap",
    //     height: 600,
    //     width: 920,
    //     startX: 600,
    //     startY: 600,
    //     cssClass: "mappy"
    // });

    //获取数据
    function getData(type) {
        $.ajax({
            url: config.getprjtotal,
            data: {
                type: type
            },
            success: function(response) {
                if (response.code != 0) {
                    return;
                }

                var result = template.render('zj-list-tmpl', {
                    'list': response.body
                });
                $('#zj-list').html(result);
                window.scrollTo(500,400);

                var w = 966 - 100,
                    h = 728 - 100,
                    ow = 109,
                    oh = 95;
                $('.bidding-tooltip').each(function() {
                    var that = $(this),
                        areaId = $(this).attr('data-id');

                    for (var i = 0; i < config.coordinate.length; i++) {
                        var d = config.coordinate[i];
                        if (d.id == areaId) {
                            that.css({
                                left: d.x - ow,
                                top: d.y - oh
                            });
                            break;
                        }
                    }
                });
            }
        });
    }

    $('#zj-list').on('click', '.bidding-tooltip', function(e) {
        e.preventDefault();

        loadURL('zjwr-' + $(this).attr('data-id'));
    });

    // 通知iPhone UIWebView 加载url对应的资源
    function loadURL(url) {
        var iFrame;
        iFrame = document.createElement("iframe");
        iFrame.setAttribute("src", url);
        iFrame.setAttribute("style", "display:none;");
        iFrame.setAttribute("height", "0px");
        iFrame.setAttribute("width", "0px");
        iFrame.setAttribute("frameborder", "0");
        document.body.appendChild(iFrame);
        iFrame.parentNode.removeChild(iFrame);
        iFrame = null;
    };

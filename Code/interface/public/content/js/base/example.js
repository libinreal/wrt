//示例
//这里展示Ajax四种常用方法

Ajax.detail({
    url: config.showDetail,
    data: {
        id: id
    }
}, function(data) {
    if (data.code != 'OK') {
        return;
    }

    // 加载完详细信息之后在查找评论
    setTimeout(function() {
        config.getList();
    }, 500);
});

// 评论数据
config.getList = function() {
    Ajax.pageing({
        url: config.commentList,
        data: {
            column: 'SHOW',
            ccid: id,
            begin: config.cpage,
            count: config.pagesize
        }
    }, function(data) {
        $('.delComment').click(function() {
            delComment($(this).attr('data-id'));
        });
    });
};

// 提交评论
commentForm.submit(function(e) {
    e.preventDefault();

    var txt = commentForm.find('input[type="text"]').val();

    if (!uid) {
        config.redirectLogin('show/house-detail?id=' + id);
        return;
    }
    if (/^\s*$/.test(txt)) {
        Tools.showTip('爷，评论内容不能为空', 5000);
        return;
    }
    if (txt.length < 10) {
        Tools.showTip('爷，评论内容至少10个字', 5000);
        return;
    }

    Ajax.submit({
        url: config.commentCommit,
        data: $(this)
    }, function(data) {
        if (data.code != 'OK') {
            Tools.showTip('爷，服务器异常，请稍后再试～', 5000);
        }
        commentForm[0].reset();
        config.cpage = 0;
        config.getList();
    });
});



//comment delete
function delComment(id) {
    Ajax.custom({
        url: config.deleteComment,
        data: {
            'commentId': id
        },
    }, function(result) {
        config.cpage = 0;
        config.getList();
    });
}

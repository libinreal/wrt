<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>物融新闻-物融通平台</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/news.css" />
</head>
<body>

<?php include '../com/header.php'; ?>

<div class="section content-left-bg">
	<div class="page-vertical clearfix">
		<div class="content-left">
			<a href="list.html?type=1002">品牌新闻</a>
			<a href="list.html?type=1003">市场活动</a>
			<a href="list.html?type=1001">媒体声音</a>
			<a href="../help/common-page.html?type=about">关于我们</a>
		</div>
		<div class="content-right">
			<div class="breadcrumbs">
				<a href="../">首页</a> &gt; <a href="index.html">物融新闻</a> &gt; <a id="type-name" href="list.html?type=">...</a> &gt; <span id="title">新闻详情</span>
				<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
			</div>
			<div class="content-detail" id="zj-detail"></div>
		</div>
	</div>
</div><!--//section-->

<?php include '../com/footer.php'; ?>

<script id="zj-detail-tmpl" type="text/html">
    <div class="new_detail_title"><!--[= title]--></div>
	<div class="new_detail_time">
		<span>时间：<!--[= $formatDate(createAt,'yyyy-MM-dd')]--></span>
		<span class="spanjuli">阅读：<!--[= browserNum]--> 次</span>
	</div>
	<!--<div class="new_detail_img">
		<img src="">
	</div>-->
	<div class="new_detail_detail">
		<!--[== content]-->
	</div>
	<div class="news-nearlink clearfix" style="padding-top: 20px;">
		<!--[if(lastArticle && lastArticle.id){]-->
		<div class="fl">
			上一篇：<a href="detail.html?id=<!--[= lastArticle.id]-->" title="<!--[= lastArticle.title]-->"><!--[= lastArticle.title]--></a>
		</div>
		<!--[}]-->
		<!--[if(nextArticle && nextArticle.id){]-->
		<div class="fr">
			下一篇：<a href="detail.html?id=<!--[= nextArticle.id]-->" title="<!--[= nextArticle.title]-->"><!--[= nextArticle.title]--></a>
		</div>
		<!--[}]-->
	</div>
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
seajs.use('../content/js/news/detail');
</script>

</body>
</html>
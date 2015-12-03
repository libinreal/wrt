<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>公告-中交物融平台</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/news.css" />
</head>
<body>

<?php include '../com/header.php'; ?>

<div class="section content-left-bg">
	<div class="page-vertical clearfix">
		<div class="content-left">
			<a href="list.html?type=2002">商城公告</a>
			<a href="list.html?type=2003">定制专区公告</a>
			<a href="list.html?type=2004">积分公告</a>
		</div>
		<div class="content-right">
			<div class="breadcrumbs">
				<a href="../">首页</a> &gt; <a href="list.html">公告</a><span id="zj-sep" style="display: none;"> &gt; </span><span id="type-name"></span>
				<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
			</div>
			<div id="rec-detail" class="clearfix"></div>
			<div class="list clearfix" style="margin-bottom: 40px;">
				<div class="list-ti2">
					<ul id="zj-list"></ul>
				</div>
				<div class="list-img2" id="zj-ad"></div>
			</div>
		</div>
	</div>
</div><!--//section-->

<?php include '../com/footer.php'; ?>

<script id="zj-rec-tmpl" type="text/html">
    <div class="slider">
		<div class="scroller">
			<!--[for(i = 0; i< fileUrl.length; i++){]-->
			<div class="item">
				<img id="img-<!--[= i]-->" src="<!--[= $absImg(fileUrl[i])]-->" alt="" />
			</div>
			<!--[}]-->
		</div>
		<div class="subscript"></div>
	</div>
	<div class="intro clearfix">
		<div class="title">
			<!--[=title]--><span><!--[= $formatDate(createAt,'yyyy-MM-dd')]--></span>
		</div>
		<div class="jieshao"><!--[= brief]--></div>
		<div class="detail">
			<span><a href="<!--[= $newsUrl(id)]-->">详情 ></a></span>
		</div>
	</div>
</script>
<script id="zj-list-tmpl" type="text/html">
    <!--[for(i = 0; i < list.length; i ++) {]-->
	<li>
		<a href="<!--[= $newsUrl(list[i].id)]-->">
			<span class="title"><!--[= list[i].title]--></span>
		    <span class="date"><!--[= $formatDate(list[i].createAt,'MM/dd')]--></span>
		</a>
	</li>
    <!--[}]-->
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
seajs.use('../content/js/notice/list');
</script>

</body>
</html>

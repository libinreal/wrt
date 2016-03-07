<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>公告-物融通平台</title>
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
				<a href="../">首页</a> &gt; <a href="list.html">公告</a> &gt; <a id="type-name" href="list.html?type=">...</a> &gt; <span id="title">...</span>
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
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
seajs.use('../content/js/notice/detail');
</script>

</body>
</html>
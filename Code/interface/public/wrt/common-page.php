<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>智能数据</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/help.css" />
</head>
<body>

<?php include '../com/header.php'; ?>

<div class="section content-left-bg common-service news-index">
	<div class="content page-vertical clearfix">
		<div class="content-left">
			<ul id="navList"></ul>
		</div>
		<div class="content-right">
			<div class="breadcrumbs">
				<a href="../">首页</a> &gt; <a href="index.html">物融通</a> &gt; <span id="type-name"></span>
				<a href="javascript:history.go(-1)" class="return">返回 &gt;</a>
			</div>
			<div class="content-detail" id="zj-detail"></div>
		</div>
	</div>
</div>
<!--//section-->

<?php include '../com/footer.php'; ?>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script id="navs-tmpl" type="text/html">
<!--[for(i=0;i<list.length;i++){]-->
	<li>
		<a href="#<!--[= i]-->" data-id="<!--[=list[i].id]-->"><span><!--[=list[i].title]--></span></a>
	</li>
<!--[}]-->
</script>
<script>
	seajs.use('../content/js/znsj/common-page');
</script>

</body>
</html>
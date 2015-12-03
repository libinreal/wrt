<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>信用评测-信用池</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/credit.css" />
</head>
<body>

	<?php include '../com/header.php'; ?>

	<div class="section credit content-left-bg">
		<div class="page-vertical clearfix">
			<div class="content-left">
				<a href="history.html" class="active">信用评测</a>
				<a href="manage.html">信用管理</a>
				<a href="additional.html">信用追加</a>
				<a href="more.html">了解更多</a>
			</div>
			<div class="content-right">
				<div class="breadcrumbs">
					<a href="../">首页</a> &gt; <a href="index.html">信用池</a> &gt; <span id="type-name" >信用评测</span>
					<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
				</div>
				<div class="credit-bg credit-history">
					<div class="credit-banner scroll-img">
						<div class="items" id="zj-ad"></div>
						<div class="navi"></div>
					</div>
					<div class="credit-title">
						<h3>我的评测记录</h3>
					</div>
					<table class="credit-history-list" id="zj-history"></table>
					<div class="options credit-options">
						<a href="apply.html" class="button btn-secondary big">申请信用评测</a>
					</div>
				</div>
			</div>
		</div>
	</div><!--//section-->

	<?php include '../com/footer.php'; ?>

	<script id="zj-history-tmpl" type="text/html">
	    <!--[for(i = 0; i < list.length; i ++) {]-->
		<tr>
			<td class="time"><!--[= $formatDate(list[i].createAt,'yyyy-MM-dd hh:mm:ss')]--></td>
			<td class="status"><!--[== $checkStatus(list[i].status)]--></td>
			<td class="options"><a href="status.html?id=<!--[= list[i].id]-->" class="button btn-gray">查看进度</a></td>
		</tr>
	    <!--[}]-->
	</script>

	<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
	<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
	<script>
		seajs.use('../content/js/credit/history');
	</script>
</body>
</html>
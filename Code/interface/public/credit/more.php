<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>了解更多-信用池</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/credit.css" />
</head>
<body>

	<?php include '../com/header.php'; ?>

	<div class="section content-left-bg">
		<div class="page-vertical clearfix">
			<div class="content-left">
				<a href="history.html">信用评测</a>
				<a href="manage.html">信用管理</a>
				<a href="additional.html">信用追加</a>
				<a href="more.html" class="active">了解更多</a>
			</div>
			<div class="content-right">
				<div class="breadcrumbs">
					<a href="../">首页</a> &gt; <a href="index.html">信用池</a> &gt; <span id="type-name">了解更多</span>
					<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
				</div>
				<div class="credit-bg credit-more">
					<!-- <div class="credit-banner scroll-img">
						<div class="items" id="zj-ad"></div>
						<div class="navi"></div>
					</div> -->
					<div class="credit-title">
						<h3>信用池的介绍</h3>
					</div>
					<div class="credit-more-content" id="zj-content"></div>
				</div>
			</div>
		</div>
	</div><!--//section-->

	<?php include '../com/footer.php'; ?>

	<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
	<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
	<script>
		seajs.use('../content/js/credit/more');
	</script>
</body>
</html>
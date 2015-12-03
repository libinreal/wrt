<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>搜索-基建商城</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/mall.css" />
</head>
<body>

<?php include '../com/header.php';?>

<div class="section">
	<div class="page-vertical">
		<div class="general-list cate-normal">
			<ul class="clearfix" id="zj-list"></ul>
		</div>
	</div>
</div><!--//section-->

<?php include '../com/footer.php';?>

<script id="zj-list-tmpl" type="text/html">
	<!--[for(i = 0; i < list.length; i++){]-->
	<li>
		<div class="hist">
			<div class="title"><a href="detail.html?id=<!--[= list[i].id]-->" title="<!--[= list[i].name]-->"><!--[= list[i].name]--></a></div>
			<div class="bgimg"><a href="detail.html?id=<!--[= list[i].id]-->"><img src="<!--[= $absImg(list[i].thumb)]-->" /></a></div>
			<div class="price clearfix">
				<div class="vip">
					<span class="price-label">交易单价</span><span 
					class="price-value"><!--[== $formatCurrency(list[i].vipPrice)]--></span><span 
					class="price-unit">信用B</span>
				</div>
				<div class="default">
					<span class="price-label factory">挂牌单价</span><span 
					class="price-value"><!--[== $formatCurrency(list[i].price)]--></span><span 
					class="price-unit">信用B</span>
				</div>
			</div>
			<div class="operate clearfix">
				<a class="operate-shoucang <!--[= (list[i].hasFavorites == '1' ? 'active' : '')]-->" data-id="<!--[= list[i].id]-->" href="#"><span>收藏</span></a>
				<a class="operate-cart" data-id="<!--[= list[i].id]-->" href="#"><span>加入购物车</span></a>
			</div>
		</div>
	</li>
	<!--[}]-->
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/mall/search-normal');
</script>
</body>
</html>
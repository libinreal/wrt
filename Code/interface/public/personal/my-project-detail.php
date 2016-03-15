<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<!-- <title>我的项目-个人中心</title> -->
	<title>我的订单-个人中心</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/personal.css" />
</head>
<body>

<?php include '../com/header.php'; ?>

<div class="section content-left-bg">
	<div class="page-vertical clearfix">
	<?php include '../com/nav-left.php'; ?>
		<div class="content-right">
			<div class="breadcrumbs">
				<!-- <a href="../">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <span id="type-name" ><a href="my-project.html">我的项目</a></span> &gt; <span id="type-name" >查看工程订单详情</span> -->
				<a href="../mall/">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <span id="type-name" ><a href="my-order.html">我的订单</a></span> &gt; <span id="type-name" >查看工程订单详情</span>
				<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
			</div>				
			<div class="gray-box project-detail" id="zj-detail"></div>
		</div>
	</div>
</div>
<!--//section-->

<?php include '../com/footer.php'; ?>
<script id="zj-detail-tmpl" type="text/html">
	<div class="project-pre">
		<div class="project-t order-t">
			<div class="project-l order-l">项目名：<span><!--[= prjName]--></span></div>					
		</div>
		<ul class="project-m order-m clearfix">
			<li>公司名称：<span><!--[= companyName]--></span></li>
			<li>合同编号：<span><!--[= prjNo]--></span></li>
			<li class="m-l">合同金额：<span class="c-red"><!--[= $formatCurrency1(price)]--></span><span>&nbsp;信用B</span></li>
			<li class="m-r">合同有效截止日：<span><!--[= $formatDate(deadline,'yyyy-MM-dd')]--></span></li>										
		</ul>				
	</div>
	<div class="bg-next">
		<!--[for(i = 0; i < prjs.length; i ++) {]-->
		<div class="project-order">
			<ul class="detail-b1 clearfix">
				<li class="b1-l">合同编号：<span><!--[= prjs[i].conChildFnum]--></span></li>
				<li class="b1-c">单位名称：<span><!--[= prjs[i].prjName]--></span></li>
				<li class="b1-l">合同金额：<span><em class="c-red"><!--[= $formatCurrency1(prjs[i].conCount)]--></em>&nbsp;信用B</span></li>
				<li class="b1-c">交货时间：<span><!--[= $formatDate(prjs[i].endDate, 'yyyy-MM-dd')]--></span></li>
				<a class="button btn-secondary btn-addcart" data-id="<!--[= prjNo]-->" href="../mall/cart.html">立即下单</a>
			</ul>
			<div class="detail-b2">
				<!--[for(j = 0; j < prjs[i].goodsItem.length; j ++) {]-->
				<div class="detail-b2-item clearfix" data-id="<!--[= prjs[i].goodsItem[j].id]-->">
					<div class="detail-b2-thumb"><img src="<!--[= $absImg(prjs[i].goodsItem[j].thumb)]-->" /></div>
					<div class="detail-b2-content">
						<p>商品名称：<span><!--[= prjs[i].goodsItem[j].goodsName]--></span></p>
						<ul class="clearfix">
						<li class="b2-c">商品价格：<span class="c-red"><!--[= $formatCurrency1(prjs[i].goodsItem[j].goodsPrice)]--></span>&nbsp;信用B</li>
						<!--[for(z = 0; z < prjs[i].goodsItem[j].attr.length; z ++) {]-->
						<li class="b2-c"><!--[= prjs[i].goodsItem[j].attr[z].name]-->：<span><!--[= prjs[i].goodsItem[j].attr[z].value]--></span></li>
						<!--[}]-->
						</ul>
					</div>
				</div>
				<!--[}]-->
			</div>
		</div>
		<!--[}]-->
	</div>
</script>
<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/personal/my-project-detail');
</script>

</body>
</html>
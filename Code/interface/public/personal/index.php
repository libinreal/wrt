<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>个人中心</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/personal.css" />
	<link rel="stylesheet" href="../content/css/personal-index.css" />
</head>
<body>

<?php include "../com/header.php"; ?>

<div class="section content-left-bg">
	<div class="page-vertical clearfix">
		<?php include '../com/nav-left.php'; ?>
		<div class="content-right"style="padding-top: 15px;">
			<div class="gray-box clearfix" id="zj-summary"></div>
			<div class="contract-list gray-box">
				<div class="contract-list-header clearfix">
					<div style="line-height:50px;color:#f00;font-size:16px;text-indent:1em;">&nbsp;最新公告</div>
				</div>
				<div class="list-ti2"><ul id="note-list"></ul></div>
			</div>
			
			<div class="contract-list gray-box">
				<div class="contract-list-header clearfix">
					<div style="line-height:50px;font-size:16px;text-indent:1em;">合同管理</div>
				</div>
				<div id="contract-list"></div>
			</div>
			<div class="product-tuijian gray-box">
			<div class="tuijian-title"><span>相关商品推荐</span></div>
				<ul class="clearfix" id="zj-list"></ul>
			</div>
		</div>
	</div>
</div>
<!--//section-->

<?php include "../com/footer.php"; ?>

<script id="zj-summary-tmpl" type="text/html">
	<div class="m-c-i-left">
		<div class="m-c-i-info clearfix">
			<div class="show-icon">
				<a href="my-info.html"><img class="user-icon" alt="" src="<!--[= $getUserIcon(icon)]-->" /></a>
			</div>
			<div class="m-c-i-text">
				<div><span class="gray-span">公司名称：</span><!--[= companyName || '--']--></div>
				<div><span class="gray-span">用户名：</span><!--[= account || '--']--></div>
				<div title="<!--[= companyAddress]-->"><span class="gray-span">用户地址：</span><!--[= companyAddress || '--']--></div>
			</div>
		</div>
	</div>
	<table width="100%" cellpadding="0" cellspacing="0" class="m-c-i-center">
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td class="gray-span">采购额度：</td>
			<td style="color:#F00"><!--[= $formatCurrency1(billAmountValid)]-->RMB</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td class="gray-span">现金额度：</td>
			<td style="color:#F00"><!--[= $formatCurrency1(cashAmountValid)]-->RMB</td>
		</tr>
	</table>
</script>
<script id="zj-list-tmpl" type="text/html">
	<!--[for(i = 0; i < list.length; i ++) {]-->
		<li class="product-border">
			<div class="product-name"><a href="../mall/detail.html?id=<!--[= list[i].id]-->" title="<!--[= list[i].name]-->"><!--[= list[i].name || '--']--></a></div>
			<div class="product-img0">
				<a href="../mall/detail.html?id=<!--[= list[i].id]-->" title="<!--[= list[i].name]-->"><img alt="" src="<!--[= $absImg(list[i].thumb)]-->"/></a>
			</div>
			<div class="product-infos">
				<div class="product-price clearfix">
					<div class="vip">
						<span class="price-label">交易单价</span><span 
						class="price-value"><!--[== $formatCurrency(list[i].vipPrice)]--></span><span 
						class="price-unit">&nbsp;信用B</span>
					</div>
					<div class="default">
						<span class="price-label factory">挂牌单价</span><span 
						class="price-value"><!--[== $formatCurrency(list[i].price)]--></span><span 
						class="price-unit">&nbsp;信用B</span>
					</div>
				</div>
				<div class="product-operate">
					<button class="operate-shoucang <!--[= (list[i].hasFavorites == '1' ? 'active' : '')]-->" data-id="<!--[= list[i].id]-->"><span>收藏</span></button>
					<button class="shop-product operate-cart" data-id="<!--[= list[i].id]-->">加入购物车</button>
				</div>
			</div>
		</li>
	<!--[}]-->
</script>
<script id="contract-list-tmpl" type="text/html">
    <!--[for(i = 0; i < list.length; i ++) {]-->
    <table class="index-contract-index">
    <thead>
    <tr>
    <td>项目名称:&nbsp;&nbsp;<!--[= list[i].name || '--']--></td>
    <td><a href="my-contract-detail.html?contract_id=<!--[= list[i].id || '--']-->">查看合同详情</a></td>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>公司名称:&nbsp;&nbsp;<!--[= list[i].userName || '--']--></td>
    <td>合同编号:&nbsp;&nbsp;<!--[= list[i].num || '--']--></td>
    </tr>
    <tr>
    <td>合同金额:&nbsp;&nbsp;<!--[= list[i].amount || '--']--></td>
    <td>合同有效期:&nbsp;&nbsp;<!--[= $formatDate(list[i].startTime, 1) || '--']--> 至 <!--[= $formatDate(list[i].endTime, 1) || '--']--></td>
    </tr>
    </tbody>
    </table>
    <!--[}]-->
</script>
<script id="note-list-tmpl" type="text/html">
    <!--[for(i = 0; i < list.length; i ++) {]-->
	<table width="95%" style="margin:0 auto; line-height:30px;">
		<tr>
		<td width="90%"><a href="../notice/<!--[= $newsUrl(list[i].id)]-->"><span class="title"><!--[= list[i].title]--></span></a></td>
		<td width="10%"><span class="date"><!--[= $formatDate(list[i].createAt,'MM/dd')]--></span></td>
		</tr>
	</table>
    <!--[}]-->
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/personal/index');
</script>

</body>
</html>
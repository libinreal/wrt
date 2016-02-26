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
			<div class="my-credit-info gray-box clearfix" id="zj-summary"></div>
			<div class="contract-list gray-box">
				<div class="contract-list-header clearfix">
					<div style="line-height:30px;">&nbsp;</div>
				</div>
				<div class="contract-list-header clearfix">
					<div style="line-height:30px;color:#f00;">&nbsp;最新公告</div>
				</div>
				<div class="list-ti2"><ul id="note-list"></ul></div>
			</div>
			
			<div class="contract-list gray-box">
				<div class="contract-list-header clearfix">
					<div style="line-height:30px;">&nbsp;合同管理</div>
				</div>
				<div id="contract-list"></div>
			</div>
			<div class="project-list gray-box" id="zj-recommend" style="min-height: 211px;">
				<!-- <div class="tuijian-title">
					<span>XXXXX项目采购推荐</span>
					<a class="button btn-gray" href="my-order.html">查看订单</a>
				</div>
				<div id="zj-recommend"></div>
				<div class="p-t-content clearfix">
					<ul>
						<li>商品名称：<span>xxxxxxx</span></li>
					</ul>
					<ul class="p-t-left">
						<li>合同编号：<span>xxxxxx</span></li>
						<li>订单金额：<span>xxxxxxxxxxxxxx</span></li>
					</ul>
					<ul class="p-t-right">
						<li>交货时间：<span>xxxxxx</span></li>
						<li>公司单位：<span>xxxxxx</span></li>
					</ul>
				</div> -->
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
				<div><span class="gray-span">信用等级：</span><!--[= cusLevel || '--']--></div>
				<div><span class="gray-span">客户号：</span><!--[= cusFnum || '--']--></div>
				<div title="<!--[= cusName]-->"><span class="gray-span">客户名：</span><!--[= cusName || '--']--></div>
			</div>
		</div>
		<div class="m-c-i-btns">
			<a class="button btn-gray"  href="../credit/more.html">了解信用B</a>
			<a class="button btn-gray"  href="my-address.html">收货地址</a>
			<a class="button btn-primary" href="javascript:;">安全等级：高</a>
		</div>
	</div>
	<table width="100%" cellpadding="0" cellspacing="0" class="m-c-i-center">
		<tr>
			<td class="gray-span">总信用额度：</td>
			<td class="money"><!--[= $formatCurrency1(creAmtTot)]-->RMB</td>
		</tr>
		<tr>
			<td class="gray-span">可用总信用额度：</td>
			<td><!--[= $formatCurrency1(lastAmtTot)]-->RMB</td>
		</tr>
		<tr>
			<td class="gray-span">已使用信用额度：</td>
			<td><!--[= $formatCurrency1(spendAmtTot)]-->RMB</td>
		</tr>
		<tr>
			<td class="gray-span">已恢复信用额度：</td>
			<td><!--[= $formatCurrency1(restoreAmtTot)]-->RMB</td>
		</tr>
	</table>
	<div class="m-c-i-right">
		<a class="button btn-yellow" href='../credit/additional.html'>追加信用额度</a>
		<a class="button btn-secondary" href='my-creditb-bill.html'>票据兑换到期提醒</a>
		<a class="button btn-cgdd" href='my-creditb-history.html'>使用流水历史记录</a>
	</div>
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
<script id="zj-recommend-tmpl" type="text/html">
	<div class="project-t">
		<div class="project-l" style="margin-left:10px;"><span style="margin-left: 0;"><!--[= prjName || '--']--></span></div>
        <!--[if (recommand && recommand != '0'){]-->
		<div class="project-r"><a class="button btn-secondary" href="my-project-detail.html?constractSn=<!--[= prjNo]-->">查看分期推荐订单详情</a></div>
        <!--[}else{]-->
		<div class="project-r"><a class="button disabled" href="javascript:;">查看分期推荐订单详情</a></div>
        <!--[}]-->
	</div>
	<ul class="project-m clearfix">
		<li>公司名称：<span><!--[= companyName || '--']--></span></li>
		<li>合同编号：<span><!--[= prjNo || '--']--></span></li>
		<li class="m-l">合同金额：<span><em class="c-red"><!--[= $formatCurrency1(price)]--></em>&nbsp;信用B</span></li>
		<li class="m-r">合同有效截止日：<span><!--[= $formatDate(deadline, 'yyyy-MM-dd')]--></span></li>										
	</ul>
</script>
<script id="contract-list-tmpl" type="text/html">
    <!--[for(i = 0; i < list.length; i ++) {]-->
    <table class="index-contract-list">
    <thead>
    <tr>
    <td>项目名称:&nbsp;&nbsp;<!--[= list[i].name || '--']--></td>
    <td><a href="">查看合同详情</a></td>
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
	<table class="index-contract-list">
		<tr>
		<td><a href="../notice/<!--[= $newsUrl(list[i].id)]-->"><span class="title"><!--[= list[i].title]--></span></a></td>
		<td><span class="date"><!--[= $formatDate(list[i].createAt,'MM/dd')]--></span></td>
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
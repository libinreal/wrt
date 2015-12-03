<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>物资验收-订单详情-个人中心</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/personal.css" />
</head>
<body>

<?php include '../com/header.php'; ?>
<div class="section page-vertical ">
	<div class="breadcrumbs">
		<a href="../">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <a href="my-order.html">我的订单</a> &gt; <span id="type-name">分批物资验收</span>
		<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
	</div>
	<div class="order" id="zj-detail"></div>
</div><!--//section-->

<?php include '../com/footer.php'; ?>

<script id="zj-detail-tmpl" type="text/html">
	<div class="order-head gray-box">
		<div class="o-h-text">
			<span>订单编号：<!--[= orderSn || '--']--></span>
			<span>项目编号：<!--[= prjNo || '--']--></span>
			<span>所属项目：<!--[= prjName || '--']--></span>
		</div>
		<div class="o-h-tip"><p>本订单中有如下物资采用分批次物流配送，请根据实际情况验收各批次的物资清单。</p></div>
	</div>

	<div class="order-detial gray-box">
		<div class="head-title clearfix">
			<span class="product-0">商品分批配送清单</span>
		</div>
		<div class="product-goodsList">
		<div class="pdt-list">
			<ul class="pdt-list-top clearfix">
				<li class="top-1">子订单编号</li>
				<li class="top-2">商品编号</li>
				<li class="top-3">商品信息</li>
				<li class="top-4">验收单价</li>
				<li class="top-5">验收数量</li>
				<li class="top-6">验收时间</li>
			</ul>
			<!--[if(orders){]-->
			<!--[for(i = 0; i < orders.length; i ++) {]-->
				<div class="pdt-list-content clearfix">
					<div class="top-1"><!--[= orders[i].orderSn]--></div>
					<div class="top-2"><!--[= orders[i].goodsCode]--></div>
					<div class="top-3 clearfix">
						<div class="product-info">
							<div class="product-img">
								<img alt="" src="<!--[= $absImg(orders[i].thumb)]-->"/>
							</div>
							<div class="product-text">
								<ul>
									<li class="txt-desc name"><!--[= orders[i].goodsName || '--']--></li>
									<div class="txt-three"><li class="txt-desc"><span>单位：</span><!--[= orders[i].goodsUnit || '--']--></li>
									<!--[if(orders[i].attr){]-->
									<!--[for(j = 0; j < orders[i].attr.length; j++){]-->
										<li class="txt-desc"><span><!--[= orders[i].attr[j].name]-->：</span><!--[= orders[i].attr[j].value]--></li>
									<!--[}]-->
									<!--[}]-->
								</ul>
							</div>
						</div>
					</div>
					<div class="top-4 c-red"><!--[== $formatCurrency(orders[i].price)]--></div>
					<div class="top-5 c-red"><!--[= $formatCurrency1(orders[i].nums)]--></div>
					<div class="top-6 c-red"><!--[= $formatDate(orders[i].vtime,'yyyy-MM-dd hh:mm:ss')]--></div>
					<div class="goods-button" style="<!--[= orders[i].status == '3' ? 'display: block;' : 'display: none;']-->"><a class="button btn-primary btn-yanshou" href="#" data-id="<!--[= orders[i].id]-->">确认验收</a></div>
					<div class="goods-button" style="<!--[= orders[i].status != '3' ? 'display: block;' : 'display: none;']-->"><a class="button disabled" href="javascript:;">已验收</a></div>
				</div>
			<!--[}]-->
			<!--[}else{]-->
			<div class="nodata">暂无数据。</div>
			<!--[}]-->
		</div>
	</div>
	</div>

	<div class="order-total gray-box">
		<div class="img"></div>
		<div class="r-my clearfix">
			<div class="div line clearfix">
				<div class="div-l">已验收数量：<span class="c-red"><!--[= typeof numsTotal == 'undefined' ? '--' : numsTotal]--></span></div>
				<div class="div-r">已验收金额：<span class="c-red"><!--[= typeof sumTotal == 'undefined' ? '--' : sumTotal]--></span><!--[= typeof sumTotal == 'undefined' ? '' : '&nbsp;信用B']--></div>
			</div>
			<div class="div clearfix">
				<div class="div-l">未验收数量：<span class="c-red"><!--[= typeof unnumsTotal == 'undefined' ? '--' : unnumsTotal]--></span></div>
				<div class="div-r">未验收金额：<span class="c-red"><!--[= typeof unsumTotal == 'undefined' ? '--' : unsumTotal]--></span><!--[= typeof unsumTotal == 'undefined' ? '' : '&nbsp;信用B']--></div>
			</div>
		</div>
	</div>
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/personal/my-order-acceptance');
</script>

</body>
</html>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>订单批次-订单详情-个人中心</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/personal.css" />
</head>
<body>

<?php include '../com/header.php'; ?>
<div class="section page-vertical ">
	<div class="breadcrumbs">
		<a href="../">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <a href="my-order.html">我的订单</a> &gt; <span id="type-name">查看订单批次</span>
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
		<div class="o-h-tip"><p>本商品分类已按照实际情况拆分为多批次订单,请选择所需订单后支付。</p></div>
	</div>

	<div class="order-detial gray-box">
		<div class="head-title">
			<span>订单批次清单</span>
		</div>
		<div class="product-goodsList">
			<div class="pdt-list">
				<ul class="pdt-list-top clearfix">
					<li class="top-1">订单批次</li>
					<li class="top-2">商品编号</li>
					<li class="top-3">商品信息</li>
					<li class="top-5">合同数量</li>
					<li class="top-4">合同单价</li>
					<li class="top-6">预计交货时间</li>
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
										<li class="txt-desc name" title="<!--[= orders[i].goodsName]-->"><!--[= orders[i].goodsName || '--']--></li>
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
						<div class="top-5 c-red"><!--[= $formatCurrency1(orders[i].nums)]--></div>
						<div class="top-4 c-red"><!--[== $formatCurrency(orders[i].price)]--></div>
						<div class="top-6 c-red"><!--[= $formatDate(orders[i].vtime,'yyyy-MM-dd hh:mm:ss')]--></div>
							<div class="goods-button s1" style="<!--[= orders[i].status == '1' ? 'display: block;' : 'display: none;']-->"><a class="button btn-primary btn-zhifu" href="../wrt/payfor.html?orderSn=<!--[= orders[i].orderSn]-->&goodsId=<!--[= $getCurGoodsId()]-->&price=<!--[= orders[i].price]-->&id=<!--[= orders[i].id]-->" data-id="<!--[= orders[i].id]-->">立即支付</a></div>
							<div class="goods-button s2" style="<!--[= orders[i].status != '1' ? 'display: block;' : 'display: none;']-->"><a class="button disabled" href="javascript:;">已支付</a><a class="button" href="my-order-logistics.html?id=<!--[= orders[i].id]-->">查看物流</a></div>
					</div>
				<!--[}]-->
				<!--[}else{]-->
				<div class="nodata">暂无数据。</div>
				<!--[}]-->
			</div>
		</div>
	</div>

	<div class="order-total clearfix gray-box">
			<div class="img"></div>
			<div class="r-my clearfix">
				<div class="div line clearfix">
					<div class="div-l">已支付数量：<span class="c-red"><!--[= typeof numsTotal == 'undefined' ? '--' : numsTotal]--></span></div>
					<div class="div-r">已支付金额：<span class="c-red"><!--[= typeof sumTotal == 'undefined' ? '--' : sumTotal]--></span><!--[= typeof sumTotal == 'undefined' ? '' : '&nbsp;信用B']--></div>
				</div>
				<div class="div clearfix">
					<div class="div-l">未支付数量：<span class="c-red"><!--[= typeof unnumsTotal == 'undefined' ? '--' : unnumsTotal]--></span></div>
					<div class="div-r">未支付金额：<span class="c-red"><!--[= typeof unsumTotal == 'undefined' ? '--' : unsumTotal]--></span><!--[= typeof unsumTotal == 'undefined' ? '' : '&nbsp;信用B']--></div>
				</div>		
			</div>
	</div>
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/personal/my-order-sub');
</script>

</body>
</html>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>物流详情-我的订单-个人中心</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/personal.css" />
</head>
<body>

<?php include '../com/header.php'; ?>
<div class="section page-vertical">
	<div class="breadcrumbs">
		<a href="../">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <a href="my-order.html">我的订单</a> &gt; <span id="type-name" >查看物流</span>
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
		<div class="o-h-tip">已完成信用B支付，商品物流配送中。</div>
	</div>

	<div class="order-info gray-box">
		<div class="head-title">
			<span>订单信息</span>
			<a class="button btn-primary btn-yanshou<!--[= (orderStatus == '3' ? '' : ' disabled')]-->" href="#"><!--[= (orderStatus == '3' ? '我要' : '已')]-->验收</a>
			<a class="button btn-cuiban<!--[= (isRemaind == '1' ? ' disabled' : '')]-->" href="#" data-id="<!--[= parentOrderId]-->"><!--[= (isRemaind == '1' ? ' 已催办' : '催办订单')]--></a>
		</div>
		<div class="info-border first">
			<div class="info-head">收货人信息</div>
			<div><span>收货人：<!--[= payer || '--']--></span></div>
			<div><span>地<em class="e1"></em>址：<!--[= address || '--']--></span></div>
			<div><span>手<em class="e1"></em>机：<!--[= mobile || '--']--></span></div>
		</div>
		<div class="info-border">
			<div class="info-head">支付信息</div>
			<div><span>支付方式：信用B支付</span></div>
			<div><span>支付机构：<!--[= $getPayOrg(payOrg) || '--']--></span></div>
		</div>
		<div class="info-border">
			<div class="info-head">发票信息</div>
			<div><span>发票类型：<!--[= $getInvType(invType) || '--']--></span></div>
			<div><span>发票抬头：<!--[= invPayee || '--']--></span></div>
			<div><span>发票内容：<!--[= invContent || '--']--></span></div>
		</div>
		<div class="order-delivery">
			<p>处理时间：<!--[= $formatDate(doTime, 'yyyy-MM-dd hh:mm:ss')]--></p>
			<p>订单状态：<!--[= $getStatus(orderStatus)]--></p>
			<p>配送信息：<span class="c-red"><!--[= lastEvent]--></span></p>
		</div>

		<div class="order-delivery-list">
			<!--[if(proclist){]-->
			<table width="100%" cellpadding="0" cellspacing="0">
			<col width="200"/>
			<col width="543"/>
			<col width="200"/>
			<!--[for(i = 0; i < proclist.length; i++){]-->
				<tr>
					<td><!--[= $formatDate(proclist[i].createAt,'yyyy-MM-dd hh:mm:ss')]--></td>
					<td><!--[= proclist[i].event]--></td>
					<td><!--[= proclist[i].whodo]--></td>
				</tr>
			<!--[}]-->
			</table>
			<!--[}else{]-->
			<div class="nodata">暂无数据。</div>	
			<!--[}]-->
		</div>
	</div>
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/personal/my-order-logistics');
</script>

</body>
</html>
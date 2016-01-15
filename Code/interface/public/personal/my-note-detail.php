<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>票据详情-个人中心</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/personal.css" />
	<style>
	.distance{
		margin-right:100px;
	}
	</style>
</head>
<body>

<?php include '../com/header.php'; ?>
<div class="section page-vertical">
	<div class="breadcrumbs">
		<a href="../">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <a href="my-note.html">我的票据</a> &gt; <span id="type-name">票据详情</span>
		<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
	</div>
	<div class="order" id="zj-detail"></div>
</div><!--//section-->

<?php include '../com/footer.php'; ?>
<script id="zj-detail-tmpl" type="text/html">
	
	<div class="order-info gray-box">
		<div class="head-title">
			<span>票据信息</span>
		</div>
		<div class="info-border first">
			<div><span class="distance">票据编号：<!--[= bill_num || '--']--></span><span class="distance">票据类型：<!--[= bill_type || '--']--></span><span class="distance">币别：<!--[= currency || '--']--></span></div>
			<div><span class="distance">票面金额：<!--[= bill_amount || '--']--></span><span class="distance">客户号：<!--[= customer_num || '--']--></span><span class="distance">合同编号：<a href="my-contract-detail.html?contract_id=<!--[= contract_id || '0']-->"><!--[= contract_num || '--']--></a></span></div>
			<div><span class="distance">付款利率：<!--[= payment_rate || '--']--></span><span class="distance">到期金额：<!--[= expire_amount || '--']--></span></div>
			<div><span class="distance">签发日期：<!--[= issuing_date || '--']--></span><span class="distance">结束日期：<!--[= due_date || '--']--></span><span class="distance">付款期限：<!--[= prompt_day || '--']--></span></div>
			<div><span class="distance">出票人：<!--[= drawer || '--']--></span><span class="distance">承兑人：<!--[= acceptor || '--']--></span></div>
			<div><span class="distance">承兑协议编号：<!--[= accept_num || '--']--></span><span class="distance">承兑日期：<!--[= accept_date || '--']--></span></div>
			<div><span class="distance">收票日：<!--[= receive_date || '--']--></span><span class="distance">交易金额：<!--[= trans_amount || '--']--></span></div>
			<div><span class="distance">销售人员：<!--[= saler || '--']--></span><span class="distance">收款组织：<!--[= receiver || '--']--></span><span class="distance">结算组织：<!--[= balance || '--']--></span></div>
			<div><span class="distance">折算比例：<!--[= discount_rate || '--']--></span><span class="distance">票据状态：<!--[= status || '--']--></span><span class="distance">累计已还金额：<!--[= has_repay || '--']--></span></div>
			<div><span class="distance">折后额度：<!--[= discount_amount || '--']--></span><span class="distance">带追索权：<!--[= is_recourse || '--']--></span></div>
			<div><span class="distance">付款账号：<!--[= pay_account || '--']--></span><span class="distance">收款账号：<!--[= receive_account || '--']--></span></div>
			<div><span class="distance">来往单位：<!--[= current_unit || '--']--></span><span class="distance">备注：<!--[= remark || '--']--></span></div>
		</div>
		
	</div>

	

</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/personal/my-note-detail');
</script>

</body>
</html>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>合同详情-个人中心</title>
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
		<a href="../">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <a href="my-contract.html">我的合同</a> &gt; <span id="type-name">合同详情</span>
		<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
	</div>
	<div class="order" id="zj-detail"></div>
</div><!--//section-->

<?php include '../com/footer.php'; ?>
<script id="zj-detail-tmpl" type="text/html">
	
	<div class="order-info gray-box">
		<div class="head-title">
			<span>合同信息</span>
		</div>
		<div class="info-border first">
			<div><span class="distance">合同编号：<!--[= num || '--']--></span><span class="distance">合同名称：<!--[= name || '--']--></span><span class="distance">合同金额：<!--[= amount || '--']--></span></div>
			<div><span class="distance">合同状态：<!--[= status || '--']--></span><span class="distance">合同类型：<!--[= type || '--']--></span><span class="distance">合同签署类型：<!--[= signType || '--']--></span></div>
			<div><span class="distance">开始时间：<!--[= startTime || '--']--></span><span class="distance">结束时间：<!--[= endTime || '--']--></span></div>
			<div><span class="distance">金融费率：<!--[= rate || '--']-->%</span><span class="distance">登记机构：<!--[= reg || '--']--></span></div>
			<div><span class="distance">合同创建人：<!--[= createBy || '--']--></span><span class="distance">合同创建时间：<!--[= createTime || '--']--></span></div>
			<div><span class="distance">备注：<!--[= remark || '--']--></span></div>
		</div>
		
	</div>

	

</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/personal/my-contract-detail');
</script>

</body>
</html>
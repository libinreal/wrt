<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>定制申请</title>
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
				<a href="../mall/">首页</a> &gt; <a href="my-customize.html">工程定制</a> &gt; <span>定制详情</span>
				<a href="javascript:history.go(-1)" class="return">返回 &gt;</a>
			</div>
			<div class="apply-form apply-3">
				<div class="clearfix" id="zj-detail"></div>
			</div>
		</div>
	</div>
</div>
<!--//section-->

<?php include '../com/footer.php'; ?>

<script id="zj-detail-tmpl" type="text/html">
	<div class="groupbox applybox clearfix">
		<div class="group clearfix">
			<label>项目名称：</label><span><!--[= name || '--']--></span>
		</div>
		<div class="group clearfix">
			<label>项目地址：</label><span><!--[= address || '--']--></span>
		</div>
		<div class="group clearfix">
			<label>项目工期：</label><span><!--[= period || '--']--></span>
		</div>
		<div class="group clearfix">
			<label>项目投资额度：</label><span><!--[= amount]--></span>
		</div>
		<div class="group clearfix">
			<label>所属区域：</label><span><!--[= area || '--']--></span>
		</div>
	</div>
	<div class="group-right apply-r-3 clearfix">
		<div class="group clearfix">
			<label>联系人：</label><span><!--[= contactPeople || '--']--></span>
		</div>
		<div class="group clearfix">
			<label>职位：</label><span><!--[= position || '--']--></span>
		</div>
		<div class="group clearfix">
			<label>联系方式：</label><span><!--[= contactTelephone || '--']--></span>
		</div>
		<div class="group clearfix">
			<label>公司名称：</label><span><!--[= companyName || '--']--></span>
		</div>
		<div class="group clearfix">
			<label>公司地址：</label><span><!--[= companyAddress || '--']--></span>
		</div>
	</div>
	<div class="group apply">
		<label class="xmczqk-label">项目筹资情况：</label><div class="xmczqk"><!--[= remark || '--']--></div>
	</div>
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/my-customize-detail');
</script>
</body>
</html>
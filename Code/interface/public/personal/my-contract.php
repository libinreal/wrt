<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>我的合同-个人中心</title>
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
				<a href="../">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <span id="type-name">我的合同</span>
				<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
			</div>	
			
			<div class="contract-list gray-box">
				<div class="contract-list-header clearfix">
					<div class="contract-list-col c2">合同编号</div>
					<div class="contract-list-col c3">合同名称</div>
					<div class="contract-list-col c4">合同金额</div>
					<div class="contract-list-col c5">开始时间</div>
					<div class="contract-list-col c6">结束时间</div>
				</div>
				<div class="contract-list-content" id="contract-list"></div>
			</div>
		</div>
	</div>
</div>
<!--//section-->

<?php include '../com/footer.php'; ?>
<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>

<script id="contract-list-tmpl" type="text/html">
    <!--[for(i = 0; i < list.length; i ++) {]-->
	<a href="#?id=<!--[= list[i].contract_id]-->" class="clearfix">
		<div class="contract-list-col c2"><!--[= list[i].contract_num || '--']--></div>
		<div class="contract-list-col c3"><!--[= list[i].contract_name || '--']--></div>
		<div class="contract-list-col c4"><!--[= list[i].contract_amount || '--']--></div>
		<div class="contract-list-col c5"><!--[= $formatDate(list[i].start_time, 1) || '--']--></div>
		<div class="contract-list-col c6"><!--[= $formatDate(list[i].end_time, 1) || '--']--></div>
	</a>
    <!--[}]-->
</script>

<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/personal/my-contract');
</script>

</body>
</html>
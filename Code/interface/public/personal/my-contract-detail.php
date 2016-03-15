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
		<a href="../mall/">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <a href="my-contract.html">我的合同</a> &gt; <span id="type-name">合同详情</span>
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
		<div class="info-border">
			<table cellpadding="0" cellspacing="1">
				<tr>
				<th>合同编号：</th><td><!--[= num || '--']--></td><th>合同名称：</th><td><!--[= name || '--']--></td><th>合同金额：</th><td><!--[= amount || '--']--></td><th>合同状态：</th><td><!--[= status || '--']--></td>
				</tr>
				<tr>
				<th>公司：</th><td>中交物融</td><th>客户：</th><td><!--[= userName || '--']--></td><th>合同类型：</th><td><!--[= type || '--']--></td><th>签署类型：</th><td><!--[= signType || '--']--></td>
				</tr>
				<tr>
				<th>开始时间：</th><td><!--[= startTime || '--']--></td><th>结束时间：</th><td><!--[= endTime || '--']--></td><th>是否控制类型：</th><td><!--[= isControl == 1 ? "是" : "否" ]--></td><td></td><td></td>
				</tr>
				<tr>
				<th>金融费率：</th><td><!--[= rate || '--']-->%</td><th>登记机构：</th><td><!--[= reg || '--']--></td><th>附件：</th><td colspan="4"><!--[= file || '--']--></td>
				</tr>
				<tr>
				<th>物料类型：</th>
				<td colspan="20">
				<!--[if(category.length == 0) {]-->
					暂无物料
				<!--[}else{]-->
				    <!--[for(i = 0; i < category.length; i ++) {]-->
				    	<span><!--[= category[i].cat_name || '--']--></span>&nbsp;,&nbsp;
				    <!--[}]-->
				<!--[}]-->
				</td>
				</tr>
				<tr>
				<th>备注：</th><td colspan="20"><!--[= remark || '--']--></td>
				</tr>
			</table>
			<div style="text-align:center">
			<a class="button btn-gray" href="javascript:history.back()">返回</a>
			</div>
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
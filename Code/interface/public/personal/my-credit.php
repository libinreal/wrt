<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>自有授信-个人中心</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/personal.css" />
	<style>
	.credit-list-col {
		width: 100px;
	}
	</style>
</head>
<body>

<?php include '../com/header.php'; ?>

<div class="section content-left-bg">
	<div class="page-vertical clearfix">
		<?php include '../com/nav-left.php'; ?>
		<div class="content-right">
			<div class="breadcrumbs">
				<a href="../">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <span id="type-name">自有授信</span>
				<a href="my-credit-add.html" class="button btn-secondary small">新增</a><a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
			</div>
			<div class="order-list-header clearfix">
				<div style="height:10px;"></div>
				<form id="search_form" class="search_form" onsubmit="return false;">
					<div style="text-align:center;">
					<label>申请人：</label><input type="text" name="apply_user">&nbsp;&nbsp;
					<label>申请合同：</label>
					<select name="contract_id" id="contract-id" style="width:100px;"></select>&nbsp;&nbsp;
					<label>申请状态：</label>
					<select name="apply_status">
						<option value="">全部</option>
						<option value="0">审核中</option>
						<option value="1">总帐户已审核</option>
						<option value="2">平台通过</option>
						<option value="3">平台不通过</option>
					</select>&nbsp;&nbsp;
					<label>日期：</label>
			        <input type="text" name="start" id="start"> 到 <input type="text" name="end" id="end">&nbsp;&nbsp;
					<button class="button" id="search_button">查询</button>
					</div>
				</form>
				<div style="height:10px;"></div>
			</div>
			<div class="contract-list gray-box">
				<div class="contract-list-header clearfix">
					<div class="contract-list-col credit-list-col c1">申请人</div>
					<div class="contract-list-col credit-list-col c2">申请时间</div>
					<div class="contract-list-col credit-list-col c3">申请金额</div>
					<div class="contract-list-col credit-list-col c4">审批金额</div>
					<div class="contract-list-col credit-list-col c5">申请备注</div>
					<div class="contract-list-col credit-list-col c5">申请通过时间</div>
					<div class="contract-list-col credit-list-col c6">审批意见</div>
					<div class="contract-list-col credit-list-col c7">申请状态</div>
					<div class="contract-list-col credit-list-col c8">操作</div>
				</div>
				<div class="contract-list-content" id="credit-list"></div>
			</div>
		</div>
	</div>
</div>
<!--//section-->

<?php include '../com/footer.php'; ?>
<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script id="contract-id-tmpl" type="text/html">
<option value="">全部</option>
<!--[for(i = 0; i < list.length; i ++) {]-->
<option value="<!--[= list[i].contract_id || '--']-->"><!--[= list[i].contract_name || '--']--></option>
<!--[}]-->
</script>
<script id="credit-list-tmpl" type="text/html">
    <!--[for(i = 0; i < list.length; i ++) {]-->
		<div class="contract-list-col credit-list-col c1"><!--[= list[i].account || '--']--></div>
		<div class="contract-list-col credit-list-col c2"><!--[= list[i].create_date || '--']--></div>
		<div class="contract-list-col credit-list-col c3"><!--[= list[i].apply_amount || '--']--></div>
		<div class="contract-list-col credit-list-col c4"><!--[= list[i].check_amount || '--']--></div>
		<div class="contract-list-col credit-list-col c3"><!--[= list[i].apply_remark || '--']--></div>		
		<div class="contract-list-col credit-list-col c5"><!--[= list[i].check_time || '--']--></div>
		<div class="contract-list-col credit-list-col c6"><!--[= list[i].check_remark || '--']--></div>
		<div class="contract-list-col credit-list-col c7"><!--[= list[i].status || '--']--></div>
		<div class="contract-list-col credit-list-col c8">
		<a href="my-credit-edit.html?apply_id=<!--[= list[i].apply_id]-->" style="display:inline;">编辑</a>&nbsp;&nbsp;
		<a href="my-credit-detail.html?apply_id=<!--[= list[i].apply_id]-->" style="display:inline;">详情</a>
		</div>
    <!--[}]-->
</script>

<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/personal/my-credit');
</script>

</body>
</html>
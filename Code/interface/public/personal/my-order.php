<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>我的订单-个人中心</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/personal.css" />
	<style>
	.order-list-col{
		width: 115px;
	}
	.c7{
		width: 150px;
	}
	#zj-list table tr:hover{
		background: #EfEfEf;
	}
	#zj-list table td{
		text-align: center;
		height: 40px;
		line-height: 40px;
		border-bottom: 1px solid #E6E6E6;
		padding: 5px 0;
	}
	#zj-list table td a{
		display: inline;
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
				<a href="../">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <span id="type-name" >我的订单</span>
				<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
			</div>
			<div class="order-list gray-box">
				<div class="order-list-header clearfix">
					<div style="height:10px;"></div>
					<form id="search_form" class="search_form" onsubmit="return false;">
						<div style="padding-left:10px;"><label class="gray-span">客户订单号：</label><input type="text" name="order_sn">&nbsp;&nbsp;&nbsp;&nbsp;
						<label class="gray-span">合同名称：</label><input type="text" name="contract_name">&nbsp;&nbsp;&nbsp;&nbsp;
						<label class="gray-span">订单状态：</label>
						<select name="order_status">
							<option value="">全部</option>
							<option value="0">已下单</option>
							<option value="1">处理中</option>
							<option value="2">验收中</option>
							<option value="3">对账中</option>
							<option value="4">已完成</option>
							<option value="5">订单取消</option>
						</select>&nbsp;&nbsp;&nbsp;&nbsp;
						<button class="button" id="search_button">查询</button>
						</div>
					</form>
					<div style="height:10px;"></div>
				</div>
				<div class="order-list-header clearfix">
					<div class="order-list-col c1">订单号</div>
					<div class="order-list-col c2">客户名称</div>
					<div class="order-list-col c3">合同名称</div>
					<div class="order-list-col c4">下单时间</div>
					<div class="order-list-col c5">下单金额</div>
					<div class="order-list-col c6">订单状态</div>
					<div class="order-list-col c7">操作</div>
				</div>
				<div class="order-list-content" id="zj-list"></div>
			</div>

		</div>
	</div>
</div>
<!--//section-->

<div id="kpsign" style="display:none">
	<OBJECT classid="CLSID:57A1AA83-D974-4A12-8475-DAAEE04D237C" CODEBASE="kpsignx.cab#Version=1,7,0,3" id="doit" VIEWASTEXT width="200" height="40">
		<PARAM NAME="_cx" VALUE="5292">
		<PARAM NAME="_cy" VALUE="1058">
		<PARAM NAME="DigitalSignature" VALUE="1">
		<PARAM NAME="SignMethod" VALUE="2">
		<PARAM NAME="UseFileToken" VALUE="0">
	</OBJECT>
</div>

<?php include '../com/footer.php'; ?>

<script id="zj-list-tmpl" type="text/html">
    <!--[for(i = 0; i < list.length; i ++) {]-->
    	<div href="javascript:void(0);" class="clearfix">
    	<table cellpadding="0">
    	<tr>
    		<td style="width: 130px;"><!--[= list[i].orderSn || '--']--></td>
    		<td style="width: 130px;"><!--[= list[i].company_name || '--']--></td>
    		<td style="width: 130px;"><!--[= list[i].prjName || '--']--></td>
    		<td style="width: 130px;"><!--[= $formatDate(list[i].createAt, 1) || '--']--></td>
    		<td style="width: 130px;"><!--[= list[i].orderAmount || '--']--></td>
    		<td style="width: 130px;"><!--[= $getStatus(list[i].status)]--></td>
    		<td style="width: 220px;">
			<a href="my-order-detail.html?id=<!--[= list[i].id]-->">订单详情</a>&nbsp;
			<a href="my-kid-order.html?id=<!--[= list[i].id]-->">子订单列表</a><br/>
		<!--[if(list[i].allowCancel == 1) {]-->
		<!--[if(list[i].cancelStatus == 1) {]-->
			<button class="button btn-gray disabled" data-id="<!--[= list[i].id]-->" data-status="<!--[= list[i].status]-->">取消中</button>
		<!--[}else{]-->
			<button class="button btn-gray" data-id="<!--[= list[i].id]-->" data-status="<!--[= list[i].status]-->">取消订单</button>
		<!--[}}]-->
    		</td>
    	</tr>
    	</table>
    	</div>
    <!--[}]-->
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/personal/my-order');
</script>

</body>
</html>
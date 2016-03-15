<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>子订单-个人中心</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/personal.css" />
	<style>
	.order-list-col{
		width: 145px;
	}
	#zj-list table{

	}
	#zj-list table td{
		text-align: center;
		width: 145px;
		height: 30px;
		line-height: 30px;
		border-bottom: 1px solid #ccc;
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
				<a href="../mall/">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <a href="my-order.html">我的订单</a> &gt; <span id="type-name" >子订单</span>
				<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
			</div>
			<div class="order-list gray-box">
				<div class="order-list-header clearfix" style="background:#ededed;">
					<div class="order-list-col c1">拆单时间</div>
					<div class="order-list-col c2">对应合同号</div>
					<div class="order-list-col c3">拆分订单号</div>
					<div class="order-list-col c7">单价</div>
					<div class="order-list-col c8">数量</div>
					<div class="order-list-col c9">订单状态</div>
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
    		<td><!--[= list[i].createAt || '--']--></td>
    		<td><!--[= list[i].contract_name || '--']--></td>
    		<td><!--[= list[i].orderSn || '--']--></td>
    		<td><!--[= list[i].goodsPrice || '--']--></td>
    		<td><!--[= list[i].nums || '--']--></td>
    		<td><!--[= $getStatus(list[i].status)]--></td>
    	</tr>
    	<tr>
    		<td colspan="4" style="text-align:left;border-right:1px solid #ccc;">
    		<span style='font-weight:bold'>物料名称：</span>
    		<!--[= list[i].name || '--']-->	
    		</td>
    		<td colspan="2" rowspan="2">
    		<span style='font-weight:bold'>订单操作：</span>
			<a href="my-kid-order-detail.html?id=<!--[= list[i].id]-->">子订单详情</a>
		<!--[if(list[i].allowCancel == 1) {]-->
		<!--[if(list[i].cancelStatus == 1) {]-->
			<button class="button btn-gray disabled" data-id="<!--[= list[i].id]-->" data-status="<!--[= list[i].status]-->">取消中</button>
		<!--[}else{]-->
			<button class="button btn-gray" data-id="<!--[= list[i].id]-->" data-status="<!--[= list[i].status]-->">取消订单</button>
		<!--[}}]-->
    		</td>
    	</tr>
    	<tr>
    		<td colspan="2" style="text-align:left;">
    		<span style='font-weight:bold;'>物料类别：</span>
    		<!--[= list[i].cat_name || '--']-->	
    		</td>
    		<td colspan="2" style="text-align:left;border-right:1px solid #ccc;">
    		<span style='font-weight:bold'>规格/型号/牌号：</span>
    		<!--[= list[i].attributes || '--']-->	
    		</td>
    	</tr>
    	<tr>
    		<td colspan="20" style="background:#ededed;">&nbsp;</td>
    	</tr>
    	</table>
    	</div>
    <!--[}]-->
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/personal/my-kid-order');
</script>

</body>
</html>
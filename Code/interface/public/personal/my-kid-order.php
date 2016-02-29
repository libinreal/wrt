<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>子订单-个人中心</title>
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
				<a href="../">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <a href="my-order.html">我的订单</a> &gt; <span id="type-name" >子订单</span>
				<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
			</div>
			<div class="order-list gray-box">
				<div class="order-list-header clearfix">
					<div class="order-list-col c1">订单编号</div>
					<div class="order-list-col c2">项目编号</div>
					<div class="order-list-col c3">项目名称</div>
					<div class="order-list-col c4">订单状态</div>
					<div class="order-list-col c5">下单时间</div>
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
	<a href="my-kid-order-detail.html?id=<!--[= list[i].id]-->" class="clearfix">
		<div class="order-list-col c1"><!--[= list[i].orderSn || '--']--></div>
		<div class="order-list-col c2"><!--[= list[i].prjNo || '--']--></div>
		<div class="order-list-col c3"><!--[= list[i].prjName || '--']--></div>
		<div class="order-list-col c4"><!--[= $getStatus(list[i].status)]--></div>
		<div class="order-list-col c5"><!--[= $formatDate(list[i].createAt, 1) || '--']--></div>
	</a>
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
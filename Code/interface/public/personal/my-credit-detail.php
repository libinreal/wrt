<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>授信详情-个人中心</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/personal.css" />
</head>
<body>

<?php include '../com/header.php'; ?>
<div class="section page-vertical">
	<div class="breadcrumbs">
		<a href="../">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <a href="my-credit.html">自有授信</a> &gt; <span id="type-name">授信详情</span>
		<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
	</div>
	<div class="order" id="zj-detail"></div>
</div><!--//section-->

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
<script id="zj-detail-tmpl" type="text/html">
	
	<div class="order-info gray-box">
		<div class="head-title">
			<span>自有授信申请信息</span>
		</div>
		<div class="info-border first">
			<div class="info-head clearfix"><span class="women">收货人信息</span><span>中交物融大宗物流配送</span></div>
			<div><span class="women">申请人：<!--[= account || '--']--></span><span>申请时间：<!--[= create_date || '--']--></span></div>
			<div><span class="women">用途：<!--[= contract_name || '--']--></span><span>申请额度：<!--[= apply_amount || '--']--></span></div>
			<div><span class="women">所属单位：<!--[= companyName || '--']--></span><span>审批额度：<!--[= check_amount || '--']--></span></div>
			<div><span class="women">申请备注：<!--[= apply_remark || '--']--></span><span>申请状态：<!--[= status || '--']--></span></div>
		</div>
		<div class="info-border">
			<div class="info-head">审批备注</div>
			<div><span><!--[= check_remark || '--']--></span></div>
		</div>
		<div class="info-border">
			<div class="info-head">申请附件</div>
			<div><span><img src="/public/apply_attachment/<!--[= img || '--']-->" /></span></div>
		</div>
	</div>
	
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/personal/my-credit-detail');
</script>

</body>
</html>
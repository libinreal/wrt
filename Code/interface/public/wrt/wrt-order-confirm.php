<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>订单验签-物融通</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/personal.css" />
	<link rel="stylesheet" href="../content/css/wrt.css" />
</head>
<body>

<?php include '../com/header.php'; ?>
<div class="section page-vertical">
	<div class="breadcrumbs">
		<a href="../">首页</a> &gt; <a href="index-in.html">物融通</a> &gt; <span id="type-name">订单验签</span>
		<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
	</div>
	<div class="order" id="zj-detail">
	</div>
</div>

<?php include '../com/footer.php'; ?>
<script id="zj-detail-tmpl" type="text/html">
	<div class="order-head gray-box">
		<div class="o-h-text">
			<span>订单编号：<!--[= orderSn || '--']--></span>
			<span>项目编号：<!--[= prjNo || '--']--></span>
			<span>所属项目：<!--[= prjName || '--']--></span>
		</div>
		<div class="o-h-tip">
			<p>请确认采购的商品名称、规格、数量等信息准确，并请确保已在系统上插入移动验签密钥设备，进行订单信息核验操作。</p>
		</div>
	</div>

	<div class="order-info gray-box">
		<div class="head-title">
			<span>需核验的订单信息</span>
		</div>
		<div class="info-border first">
			<div class="info-head">收货人信息</div>
			<div><span>收货人：<!--[= payer || '--']--></span></div>
			<div><span>地<em class="e1"></em>址：<!--[= address || '--']--></span></div>
			<div><span>手<em class="e1"></em>机：<!--[= mobile || '--']--></span></div>
			<div class="info-head" style="margin-top:20px">物融通大宗物流配送</div>
			<div><span>指定交货时间：2015-5-5</span></div>
		</div>
		<div class="info-border">
			<div class="info-head">支付信息</div>
			<div><span>支付方式：信用B支付</span></div>
			<div><span>支付机构：<!--[= $getPayOrg(payOrg) || '--']--></span></div>
		</div>
		<div class="info-border">
			<div class="info-head">发票信息</div>
			<div><span>发票类型：<!--[= $getInvType(invType) || '--']--></span></div>
			<div><span>发票抬头：<!--[= invPayee || '--']--></span></div>
			<div><span>发票内容：<!--[= invContent || '--']--></span></div>
		</div>
	</div>

	<div class="order-detial gray-box clearfix">
		<div class="head-title"><span>商品清单确认</span></div>
		<div class="pdt-detail clearfix">
			<ul class="bg-confirm clearfix">
				<li class="top-1">订单编号</li>
				<li class="top-2">商品信息</li>
				<li class="top-3">规格</li>
				<li class="top-4">单位</li>
				<li class="top-5">交易单价</li>
				<li class="top-6">订单数量</li>
			</ul>
			<!--[if(goodsList){]-->
			<!--[for(i = 0; i < goodsList.length; i ++) {]-->
			<ul class="bg-confirm clearfix">
				<li class="top-1"><!--[= goodsList[i].goodsCode]--></li>
				<li class="top-2" title="<!--[= goodsList[i].goodsName]-->"><!--[= goodsList[i].goodsName || '--']--></li>
				<li class="top-3">
				<!--[if(goodsList[i].attr){]-->
				<!--[for(j = 0; j < goodsList[i].attr.length; j++){]-->
					<span class="txt-desc"><span><!--[= goodsList[i].attr[j].name]-->：</span><!--[= goodsList[i].attr[j].value]--></span>
				<!--[}]-->
				<!--[}]-->
				</li>
				<li class="top-4"><!--[= goodsList[i].goodsUnit || '--']--></li>
				<li class="top-5"><!--[== $formatCurrency(goodsList[i].changePrice)]--></li>
				<li class="top-6"><!--[= goodsList[i].orderNums]--></li>
			</ul>
			<!--[}]-->
			<!--[}else{]-->
			<div><span class="nodata">暂无数据。</span></div>
			<!--[}]-->
		</div>
		<div class="total-creditb">订单总金额：<span class="c-red"><!--[= $formatCurrency1(typeof ordersum == 'undefined' ? '--' : ordersum)]--></span><!--[= typeof ordersum == 'undefined' ? '' : '&nbsp;信用B']--></div>
	</div>

	<div class="order-detial gray-box order-verify clearfix">
		<ul class="f-msg clearfix">
			<li class="yqts">友情提示：</li>
			<li class="yqts-content">请确认采购的商品名称、规格、数量等信息的准确，并请确保已在系统上插入移动验签密钥设备，再点击“订单确认无误”验签按键，完成订单核验操作。</li>
		</ul>
		<!--[if(orderStatus == '-1'){]-->
		<a class="button btn-primary" href="../personal/my-order-detail.html?id=1"><span>订单确认无误</span></a>
		<!--[}else{]-->
		<a class="button btn-primary disabled" href="javascript:;"><span>订单已确认</span></a>
		<!--[}]-->
	</div>
</script>

<script>
var basepath = "";
document.write('<div id="kpsign" style="display:none">');
if((navigator.userAgent.indexOf("MSIE") > -1) || ((navigator.userAgent.indexOf("rv:11") > -1 && navigator.userAgent.indexOf("Firefox")<= -1)))
{
    //alert("IE");
    if((navigator.platform =="Win32")) {
        //alert("windows 32 bit IE");
        document.write('<OBJECT id="doit" height=40 width=200 classid="CLSID:D54C3C5F-6CA8-440B-A4E5-8B43186D5DFF" codebase='+basepath+'npkoaliiCZB_x86.CAB VIEWASTEXT>')
    } else if(navigator.platform =="Win64") {
        //alert("windows 64 bit IE");
        document.write('<OBJECT id="doit" height=40 width=200 classid="CLSID:40BEE6CE-DC27-45DB-A07E-42A625547B45"  codebase='+basepath+'npkoaliiCZB_x64.CAB VIEWASTEXT>')
    }
}
document.write('<param name="DigitalSignature" value="1">');
document.write('<param name="SignMethod" value="2">');
document.write('</OBJECT>');
document.write('</div>');
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/personal/sign');
	seajs.use('../content/js/wrt/wrt-order-confirm');
</script>

</body>
</html>
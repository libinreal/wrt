<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>对账验签-物融通</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/personal.css" />
	<link rel="stylesheet" href="../content/css/wrt.css" />
</head>
<body>

<?php include '../com/header.php'; ?>
<div class="section page-vertical">
	<div class="breadcrumbs">
		<a href="../">首页</a> &gt; <a href="index-in.html">物融通</a> &gt; <span id="type-name">对账验签</span>
		<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
	</div>
	<div class="order" id="zj-detail"></div>
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
			<p>请确认采购的商品名称、规格、数量等信息准确，并请确保已插入移动验签密钥设备，再进行对账核验操作。</p>
		</div>
	</div>

	<div class="order-detial gray-box position-relative clearfix">
		<div class="head-title"><span>商品清单确认</span></div>
		<div class="pdt-detail clearfix">
			<ul class="pdt-detail-top clearfix">
				<li class="top-1">订单编号</li>
				<li class="top-2">商品信息</li>
				<li class="top-3">规格</li>
				<li class="top-4">单位</li>
				<li class="top-6">订单数量</li> 
				<li class="top-5">交易单价</li>
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
				<li class="top-6"><!--[= goodsList[i].orderNums]--></li>
				<!-- <li class="top-5"><!--[== $formatCurrency(goodsList[i].changePrice)]-->s</li> -->
				<li class="top-5">--</li>
			</ul>
			<!--[}]-->
			<!--[}else{]-->
			<div><span class="nodata">暂无数据。</span></div>
			<!--[}]-->
		</div>
		<div class="total-creditb total-line">订单总金额：<span class="c-red"><!--[= $formatCurrency1(typeof ordersum == 'undefined' ? '--' : ordersum)]--></span><!--[= typeof ordersum == 'undefined' ? '' : '&nbsp;信用B']--></div>
		<div class="position-absolute"><img src="../content/images/wrt/bill-small.png" width="254" height="236"/></div>
	</div>

	<div class="order-detial gray-box order-verify clearfix">
		<ul class="f-msg clearfix">
			<li class="yqts">友情提示：</li>
			<li class="yqts-content">请确认采购的商品名称、规格、数量等信息的准确，并请确保已在电脑上插入移动验签密钥设备，再进行商城物资采购订单对账的确认操作。</li>
		</ul>
		<a class="button btn-primary order-confirm" href="javascript:;"><span>订单对账确认无误</span></a>
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

<script id="zj-detail-tmpl" type="text/html">

</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/personal/sign');
	seajs.use('../content/js/wrt/wrt-bill-confirm');
</script>

</body>
</html>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>子订单详情-个人中心</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/personal.css" />
	<style type="text/css">
	.table_info_2{
	    margin: 0 auto;
	    background: #ccc;
	    width: 100%;
	}
	.table_info_2 th{
	    padding:8px;
	    background: #eee;
	    font-weight: bold;
	    border: 1px solid #ccc;
	}
	.table_info_2 td.title{
	    text-align: right;
	    font-weight: bold;
	    padding: 5px 1em;
	}
	.table_info_2 td{
	    background: #fff;
	    padding:5px;
	    border: 1px solid #ccc;
	    text-align: center;
	}
	.table_info_2 table{
	    width: 100%;
	}
	table.t_1{
	    position: relative;
	    top:-1px;
	}
	.table_info_2 table td{
	    text-align: center;
	    line-height: 30px;
	    background: #fff;
	}
	</style>
</head>
<body>

<?php include '../com/header.php'; ?>
<div class="section page-vertical">
	<div id="zj-detail"></div>
</div><!--//section-->
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
	        document.write('<OBJECT id="doit" height=40 width=200 classid="CLSID:40BEE6CE-DC27-45DB-A07E-42A625547B45" codebase='+basepath+'npkoaliiCZB_x64.CAB VIEWASTEXT>')
	    }
	}
	document.write('<param name="DigitalSignature" value="1">');
	document.write('<param name="SignMethod" value="2">');
	document.write('</OBJECT>');
	document.write('</div>');
	document.write('<input type="hidden" name="step" value="1" />');
</script>
<?php include '../com/footer.php'; ?>
<script id="zj-detail-tmpl" type="text/html">
	<div class="breadcrumbs">
		<a href="../">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <a href="my-order.html">我的订单</a> &gt; <a href="my-kid-order.html?id=<!--[= parentId ]-->">子订单</a> &gt; <span id="type-name">子订单详情</span>
		<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
	</div>
	<div class="order">
	<div class="order-progress gray-box clearfix">
		<div class="order-progress-bg"></div>
		<ul>
			<li class="<!--[= $checkStatus(0,childOrderStatus)]-->"><div><i>&nbsp;</i>订单提交</div></li>
			<li class="<!--[= $checkStatus(1,childOrderStatus)]-->"><div><i>&nbsp;</i>订单确认</div></li>
			<li class="<!--[= $checkStatus(6,childOrderStatus)]-->"><div><i>&nbsp;</i>物资验收</div></li>
			<li class="<!--[= $checkStatus(8,childOrderStatus)]-->"><div><i>&nbsp;</i>订单对账</div></li>
			<li class="<!--[= $checkStatus(9,childOrderStatus)]-->"><div><i>&nbsp;</i>订单完成</div></li>
		</ul>
		<div class="order-progress-statu clearfix">
			<div class="order-operate-btns">
				<!--[if(orderStatus == '-1' || orderStatus == '0') {]-->
				<div id="btn1">
					<a class="button btn-cuiban<!--[= (isRemaind == '1' ? ' disabled' : '')]-->" href="#"><!--[= (isRemaind == '1' ? ' 已催办' : '催办订单')]--></a>
					<!--[if(cancelStatus == '1'){]-->
					<a class="button btn-quxiao disabled" href="#">取消中</a>
					<!--[}else{]-->
					<a class="button btn-quxiao" href="#" data-status="<!--[= orderStatus]-->">取消订单</a>
					<!--[}]-->
					<!--[if(orderStatus == '0'){]-->
					<a class="button btn-primary disabled" href="javascript:;">已签署合同</a>
					<!--[}else{]-->
					<a class="button btn-primary" href="../wrt/wrt-order-confirm.html?id=<!--[= id]-->" target="_blank">签署订单合同</a>
					<!--[}]-->
				</div>
				<!--[}else if(orderStatus == '1') {]-->
				<div id="btn2">
					<a class="button btn-cuiban<!--[= (isRemaind == '1' ? ' disabled' : '')]-->" href="#"><!--[= (isRemaind == '1' ? ' 已催办' : '催办订单')]--></a>
					<a class="button" href="my-project-detail.html?constractSn=<!--[= prjNo]-->">查阅订单合同</a>
					<a class="button btn-queren<!--[= (isAllCheck == '1' ? '' : ' disabled')]-->" href="#" style="display: none;">订单确认完成</a>
					<!--[if(isAllCheck == '2'){]-->
					<!--[}]-->
					<a class="button btn-primary btn-special" href="my-order-acceptance.html?orderSn=<!--[= orderSn]-->&id=<!--[= id]-->">我要验收部分订单批次</a>
				</div>
				<!--[}else if(orderStatus == '2') {]-->
				<div id="btn3">
					<a class="button" href="my-project-detail.html?constractSn=<!--[= prjNo]-->">查阅电子合同</a>
					<a class="button btn-primary btn-yanshou<!--[= (isAllCheck == '1' ? '' : ' disabled')]-->" href="#" style="display: none;">完成验收</a>
				</div>
				<!--[}else if(orderStatus == '3') {]-->
				<div id="btn4">
					<a class="button" href="http://lc.talk99.cn/chat/chat/p.do?c=10033976&f=10043368&g=10048426" target="_blank">物融客服</a>
					<a class="button" href="my-project-detail.html?constractSn=<!--[= prjNo]-->">查阅电子合同</a>
					<a class="button btn-primary btn-duizhang<!--[= (isAllCheck == '1' ? '' : ' disabled')]-->" href="../wrt/wrt-bill-confirm.html?id=<!--[= id]-->">完成对账</a>
				</div>
				<!--[}else if(orderStatus == '4') {]-->
				<div id="btn5">
					<a class="button" href="my-project-detail.html?constractSn=<!--[= prjNo]-->">查阅电子合同</a>
					<a class="button btn-primary" href="http://lc.talk99.cn/chat/chat/p.do?c=10033976&f=10043368&g=10048426" target="_blank">售后服务</a>
				</div>
				<!--[}]-->
			</div>
		</div>
	</div>
	<div class="order-info gray-box">
		<div class="head-title">
			<span>订单资料</span>
		</div>
		<div class="info-border first">
			<table>
				<tbody>
				<tr>
				<th width="100">订单编号：</th><td><!--[= orderSn || '--']--></td>
				<th width="100">合同号：</th><td><!--[= contract_num || '--']--></td>
				<th width="100">合同名称：</th><td><!--[= contract_name || '--']--></td>
				</tr>
				<tr>
				<th width="100">公司名称：</th><td><!--[= createAt || '--']--></td>
				<th width="100">下单人：</th><td><!--[= createAt || '--']--></td>
				<th width="100">拆单时间：</th><td><!--[= createAt || '--']--></td>
				</tr>
				<tr>
				<th width="100">订单状态：</th><td><!--[= $getStatus(childOrderStatus)]--></td>
				<th width="100">子订单状态：</th><td colspan="3"><!--[= $getStatus(childOrderStatus)]--></td>
				</tr>
				</tbody>
			</table>
		</div>
		<div class="head-title">
			<span>开票资料</span>
		</div>
		<div class="info-border first">
			<table>
				<tbody>
				<tr>
				<th>发票类型：</th><td><!--[= $getInvType(invType)]--></td>
				<th>发票抬头：</th><td><!--[= invPayee || '--']--></td>
				<th>内容：</th><td><!--[= invContent || '--']--></td>
				</tr>
				<tr>
				<th>收货人：</th><td><!--[= consignee || '--']--></td>
				<th>收货地址：</th><td><!--[= address || '--']--></td>
				<th>手机号码：</th><td><!--[= phone || '--']--></td>
				</tr>
				<tr>
				<th>地址标签：</th><td colspan="2"><!--[= tag || '--']--></td>
				<th>备注：</th><td colspan="2"><!--[= remark || '--']--></td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>

	<div class="gray-box">
		<div class="head-title">
			<span>商品资料</span>
		</div>
		<div class="product-goodsList">
			<div class="pdt-detail">
				<table class="table_info_2">
					<tr>
				    	<th width="150" align="right">商品名称：</th><td><!--[= goods_name || '--']--></td>
				    	<th width="150" align="right">规格/型号/材质：</th><td><!--[= attributes || '--']--></td>
				    </tr>
				</table>
				<table class="table_info_2 t_1">
					<tr>
						<td rowspan="4" align="center">订单信息</td>
						<th rowspan="2">发货信息</th>
						<th>发货单价</th>
						<th>发货数量</th>
						<th>物流费用</th>
						<th>金融费用</th>
						<th>总金额</th>
					</tr>
				    <tr>
				    	<td><!--[= goodsPriceSendBuyer || '--']--></td>
				    	<td><!--[= goodsNumberSendBuyer || '--']--></td>
				    	<td><!--[= shippingFeeSendBuyer || '--']--></td>
				    	<td><!--[= financialSendRate || '--']--></td>
				    	<td><!--[= orderAmountSendBuyer || '--']--></td>
				    </tr>
				    <tr>
				    	<th rowspan="2">到货信息</th>
				    	<th>到货单价</th>
				    	<th>到货数量</th>
				    	<th>物流费用</th>
				    	<th>金融费用</th>
				    	<th>总金额</th>
				    </tr>
				    <tr>
				    	<td><!--[= goodsPriceArrBuyer || '--']--></td>
				    	<td><!--[= goodsNumberArrBuyer || '--']--></td>
				    	<td><!--[= shippingFeeArrBuyer || '--']--></td>
				    	<td><!--[= financialArrRate || '--']--></td>
				    	<td><!--[= orderAmountArrBuyer || '--']--></td>
				    </tr>
				</table>
			</div>
		</div>
	</div>
	<div class="gray-box">
		<div class="head-title">
			<span>物流动态</span>
		</div>
		<div class="product-goodsList">
			<div class="pdt-detail">
				<table class="table_info_2">
					<tr>
				    	<th align="right">物流公司：</th><td><!--[= shippingInfo.company_name || '--']--></td>
				    	<th align="right">物流编号：</th><td><!--[= shippingInfo.shipping_num || '--']--></td>
				    	<th align="right">联系电话：</th><td><!--[= shippingInfo.tel || '--']--></td>
				    </tr>
				</table>
				<table class="table_info_2 t_1">
					<thead>
					<tr>
				    	<th width="100">日期</th><th>动态</th>
				    </tr>					
					</thead>
					<tbody>
					<!--[for(i = 0; i < shippingLog.length; i ++) {]-->
					<tr>
						<td><!--[= shippingLog[i].date || '--']--></td><td><!--[= shippingLog[i].content || '--']--></td>
					</tr>
					<!--[}]-->
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div style="text-align:center;padding-bottom:15px;">
		<div id="handle-button">
		<!--[if(childOrderStatus == 1){]-->
			<a class="button change-status" href="javascript:void(0)">发货验签</a>
		<!--[}]-->
		<!--[if(childOrderStatus == 6){]-->
			<a class="button change-status" href="javascript:void(0)">到货验签</a>
		<!--[}]-->
			<a class="button" href="javascript:history.back()">返回</a>
		</div>
	</div>
	</div>
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/personal/my-kid-order-detail');
</script>

</body>
</html>
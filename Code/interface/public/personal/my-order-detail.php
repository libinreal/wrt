<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>订单详情-个人中心</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/personal.css" />
	<style type="text/css">
	.pdt-detail{

	}
	.pdt-detail table{
		
	}
	.pdt-detail table tbody td{
		border: 1px solid #ccc;
	}
	.pdt-detail table thead{
		background: #e9e9e9;
	}
	</style>
</head>
<body>

<?php include '../com/header.php'; ?>
<div class="section page-vertical">
	<div class="breadcrumbs">
		<a href="../mall/">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <a href="my-order.html">我的订单</a> &gt; <span id="type-name">订单详情</span>
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
	<div class="order-head gray-box">
		<div class="o-h-text">
			<span>订单编号：<!--[= orderSn || '--']--></span>
			<span>合同名称：<!--[= contName || '--']--></span>
		</div>
		<div class="o-h-tip">
		<!--[if(status == '0') {]-->
			<p>订单已提交审核，请进行订单合同签署流程，并保持联系方式畅通, 商城客服会与您确认订单情况, 以完成订单审核。</p>
		<!--[}else if(status == '1'){]-->
			<p>订单合同已完成签署，请确认订单详情，并“查看订单批次”完成已确认批次订单的支付操作。</p>
		<!--[}else if(status == '2'){]-->
			<p>物资验收中，请验收人员仔细核对物资价格及数量是否与订单一致，若有误差请立即与商城客服联系: 400-058-5888！</p>
		<!--[}else if(status == '3'){]-->
			<p>订单对账中。</p>
		<!--[}else if(status == '4'){]-->
			<p>订单已完成。</p>
		<!--[}]-->
		</div>
	</div>
	<div class="order-progress gray-box clearfix">
		<div class="order-progress-bg"></div>
		<ul>
			<li class="<!--[= $checkStatus(0,status)]-->"><div><i>&nbsp;</i>订单提交</div></li>
			<li class="<!--[= $checkStatus(1,status)]-->"><div><i>&nbsp;</i>订单确认</div></li>
			<li class="<!--[= $checkStatus(2,status)]-->"><div><i>&nbsp;</i>物资验收</div></li>
			<li class="<!--[= $checkStatus(3,status)]-->"><div><i>&nbsp;</i>订单对账</div></li>
			<li class="<!--[= $checkStatus(4,status)]-->"><div><i>&nbsp;</i>订单完成</div></li>
		</ul>
		<div class="order-progress-statu clearfix">
			<div class="order-progress-text">
				<table width="100%" cellspacing="0" cellpadding="0">
				<col width="9%"/>
					<tr>
						<td>处理时间：</td>
						<td><!--[= $formatDate(doTime, 'yyyy-MM-dd hh:mm:ss')]--></td>
					</tr>
					<tr>
						<td>订单状态：</td>
						<td><!--[= $getStatus(status)]--></td>
					</tr>
					<tr>
						<td>备<span class="e2"></span>注：</td>
						<td><!--[= remark || '--']--></td>
					</tr>
				</table>
			</div>
			<div class="order-operate-btns">
				<!--[if(status == '-1' || status == '0') {]-->
				<div id="btn1">

					<!--[if(cancelStatus == '1'){]-->
					<a class="button btn-quxiao disabled" href="#">取消中</a>
					<!--[}else{]-->
					<a class="button btn-quxiao" href="#" data-status="<!--[= status]-->">取消订单</a>
					<!--[}]-->
					<!--[if(status == '0'){]-->
					<a class="button btn-primary disabled" href="javascript:;">已签署合同</a>
					<!--[}else{]-->
					<a class="button btn-primary" href="../wrt/wrt-order-confirm.html?id=<!--[= id]-->" target="_blank">签署订单合同</a>
					<!--[}]-->
				</div>
				<!--[}else if(status == '1') {]-->
				<div id="btn2">

				<a class="button" href="my-contract-detail.html?contract_id=<!--[= contid ]-->">查阅订单合同</a>
				<a class="button btn-queren<!--[= (isAllCheck == '1' ? '' : ' disabled')]-->" href="#" style="display: none;">订单确认完成</a>

				</div>
				<!--[}else if(status == '2') {]-->
				<div id="btn3">
					<a class="button" href="my-project-detail.html?constractSn=<!--[= prjNo]-->">查阅电子合同</a>
					<a class="button btn-primary btn-yanshou<!--[= (isAllCheck == '1' ? '' : ' disabled')]-->" href="#" style="display: none;">完成验收</a>
				</div>
				<!--[}else if(status == '3') {]-->
				<div id="btn4">
					<a class="button" href="http://lc.talk99.cn/chat/chat/p.do?c=10033976&f=10043368&g=10048426" target="_blank">物融客服</a>
					<a class="button" href="my-project-detail.html?constractSn=<!--[= prjNo]-->">查阅电子合同</a>
					<a class="button btn-primary btn-duizhang<!--[= (isAllCheck == '1' ? '' : ' disabled')]-->" href="../wrt/wrt-bill-confirm.html?id=<!--[= id]-->">完成对账</a>
				</div>
				<!--[}else if(status == '4') {]-->
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
				<th>订单编号：</th><td><!--[= orderSn || '--']--></td>
				<th>合同号：</th><td><!--[= contNum || '--']--></td>
				<th>合同名称：</th><td><!--[= contName || '--']--></td>
				</tr>
				<tr>
				<th>公司名称：</th><td><!--[= companyName || '--']--></td>
				<th>下单人：</th><td><!--[= companyName || '--']--></td>
				<th>下单时间：</th><td><!--[= $formatDate(createAt, 'yyyy-MM-dd hh:mm:ss') || '--']--></td>
				</tr>
				<tr>
				<th>订单状态：</th><td colspan="20"><!--[= $getStatus(status) || '--']--></td>
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
				<th>发票类型：</th><td><!--[= invType || '--']--></td>
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

	<div class="order-detial gray-box">
		<div class="product-goodsList">
			<div class="pdt-detail">
			<table>
			<thead>
			<tr>
			<th style='height:35px;'>物料代码</th>
			<th>物料名称</th>
			<th>规格/型号/材质</th>
			<th>下单数量</th>
			<th>已拆单数量</th>
			<th>未拆单数量</th>
			<th>单价</th>
			<th>小计</th>
			<th>厂商</th>
			<th>操作</th>
			</tr>
			</thead>
			<tbody>
		<!--[if(goods){]-->
			<!--[for(i = 0; i < goods.length; i ++) {]-->
			<tr>
			<td><!--[= goods[i].goodsId]--></td>
			<td><!--[= goods[i].goodsName]--></td>
			<td><!--[= goods[i].attributes]--></td>
			<td><!--[= goods[i].nums]--></td>
			<td><!--[= goods[i].send_number]--></td>
			<td><!--[= goods[i].nums - goods[i].send_number]--></td>
			<td><!--[= goods[i].goodsPrice]--></td>
			<td><!--[= goods[i].totalPrice]--></td>
			<td><!--[= goods[i].suppliers || '--' ]--></td>
			<td><a href="my-kid-order.html?id=<!--[= goods[i].orderId]-->">子订单列表</a></td>
			</tr>
			<!--[}]-->
		<!--[}else{]-->
			<tr><td class="nodata" colspan="20">暂无数据。</td></tr>
		<!--[}]-->
			</tbody>
			</table>
			</div>
		</div>
	</div>
	<div style="text-align:center;padding-bottom:15px;">
		<div id="handle-button">
			<a class="button" href="javascript:history.back()">返回</a>
		</div>
	</div>
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/personal/my-order-detail');

	function changeStatus(oid){
		var xhReq = new XMLHttpRequest();
		xhReq.open("GET", "http://"+window.location.host+"/order/uchildstatus?oid="+oid, false);
		xhReq.send(null);
		var serverResponse = xhReq.responseText;
		location.reload();
	}
</script>

</body>
</html>
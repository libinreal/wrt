<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>订单详情-个人中心</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/personal.css" />
</head>
<body>

<?php include '../com/header.php'; ?>
<div class="section page-vertical">
	<div class="breadcrumbs">
		<a href="../">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <a href="my-order.html">我的订单</a> &gt; <span id="type-name">订单详情</span>
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
			<span>项目编号：<!--[= prjNo || '--']--></span>
			<span>所属项目：<!--[= prjName || '--']--></span>
		</div>
		<div class="o-h-tip">
		<!--[if(orderStatus == '-1') {]-->
			<p>订单已提交审核，请进行订单合同签署流程，并保持联系方式畅通, 商城客服会与您确认订单情况, 以完成订单审核。</p>
		<!--[}else if(orderStatus == '1'){]-->
			<p>订单合同已完成签署，请确认订单详情，并“查看订单批次”完成已确认批次订单的支付操作。</p>
		<!--[}else if(orderStatus == '2'){]-->
			<p>物资验收中，请验收人员仔细核对物资价格及数量是否与订单一致，若有误差请立即与商城客服联系: 400-058-5888！</p>
		<!--[}else if(orderStatus == '3'){]-->
			<p>订单对账中。</p>
		<!--[}else if(orderStatus == '4'){]-->
			<p>订单对账中。</p>
		<!--[}]-->
		</div>
	</div>
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
			<div class="order-progress-text">
				<table width="100%" cellspacing="0" cellpadding="0">
				<col width="9%"/>
					<tr>
						<td>处理时间：</td>
						<td><!--[= $formatDate(doTime, 'yyyy-MM-dd hh:mm:ss')]--></td>
					</tr>
					<tr>
						<td>订单状态：</td>
						<td><!--[= $getStatus(childOrderStatus)]--></td>
					</tr>
					<tr>
						<td>备<span class="e2"></span>注：</td>
						<td><!--[= remark || '--']--></td>
					</tr>
				</table>
			</div>
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
			<span>订单信息</span>
		</div>
		<div class="info-border first">
			<div class="info-head clearfix"><span class="women">收货人信息</span><span>中交物融大宗物流配送</span></div>
			<div><span class="women">收货人：<!--[= payer || '--']--></span><span>指定交货时间：2015/1/2</span></div>
			<div><span>地<em class="e1"></em>址：<!--[= address || '--']--></span></div>
			<div><span>手<em class="e1"></em>机：<!--[= mobile || '--']--></span></div>
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

	<div class="order-detial gray-box">
		<div class="head-title">
			<span>商品清单</span>
		</div>
		<div class="product-goodsList">
			<div class="pdt-detail">
				<ul class="pdt-detail-top clearfix">
					<li class="top-1">商品编号</li>
					<li class="top-2">商品信息</li>
					<li class="top-4">订单数量</li>
					<li class="top-3">交易单价</li>
					<li class="top-6">合同数量</li>
					<li class="top-5">合同单价</li>
					<li class="top-8">验收数量</li>
					<li class="top-7">验收单价</li>
				</ul>
				<!--[if(goodsList){]-->
				<!--[for(i = 0; i < goodsList.length; i ++) {]-->
					<div class="pdt-detail-content last clearfix">
						<div class="top-1"><!--[= goodsList[i].goodsCode]--></div>
						<div class="top-2 clearfix">
							<div class="product-info">
								<div class="product-img">
										<img alt="" src="<!--[= $absImg(goodsList[i].thumb)]-->"/>
									</div>
									<div class="product-text">
										<ul>
											<li class="txt-desc name" title="<!--[= goodsList[i].goodsName]-->"><!--[= goodsList[i].goodsName || '--']--></li>
											<div class="txt-three"><li class="txt-desc"><span>单位：</span><!--[= goodsList[i].goodsUnit || '--']--></li>
											<!--[if(goodsList[i].attr){]-->
											<!--[for(j = 0; j < goodsList[i].attr.length; j++){]-->
												<li class="txt-desc"><span><!--[= goodsList[i].attr[j].name]-->：</span><!--[= goodsList[i].attr[j].value]--></li>
											<!--[}]-->
											<!--[}]-->
										</ul>					
									</div>
								</div>
						</div>
						<div class="top-4 c-red"><!--[= goodsList[i].orderNums]--></div>
						<div class="top-3 c-red"><!--[== $formatCurrency(goodsList[i].changePrice)]--></div>
						<div class="top-6"><!--[= $formatCurrency1(goodsList[i].contractNums)]--></div>
						<div class="top-5"><!--[== $formatCurrency(goodsList[i].contractPrice)]--></div>
						<div class="top-8"><!--[= $formatCurrency1(goodsList[i].checkNums)]--></div>
						<div class="top-7"><!--[== $formatCurrency(goodsList[i].checkPrice)]--></div>

						<!--[if(orderStatus == '0') {]-->
						<div class="goods-button">&nbsp;</div>
						<!--[}else if(orderStatus == '1'){]-->
						<div class="goods-button"><a class="button btn-primary" href="my-order-sub.html?orderSn=<!--[= orderSn]-->&goodsId=<!--[= goodsList[i].goodsId]-->">查看订单批次</a></div>
						<!--[}else if(orderStatus == '2'){]-->
						<div class="goods-button"><a class="button btn-primary" href="my-order-acceptance.html?orderSn=<!--[= orderSn]-->&goodsId=<!--[= goodsList[i].goodsId]-->">分批验收</a></div>
						<!--[}else if(orderStatus == '3'){]-->
						<div class="goods-button"><a class="button btn-primary" href="my-order-reconciliations.html?orderSn=<!--[= orderSn]-->&goodsId=<!--[= goodsList[i].goodsId]-->">订单对账</a></div>
						<!--[}else if(orderStatus == '4'){]-->
						<div class="goods-button"><a class="button btn-primary" href="my-order-reconciliations.html?orderSn=<!--[= orderSn]-->&goodsId=<!--[= goodsList[i].goodsId]-->">订单对账</a></div>
						<!--[}]-->

					</div>
				<!--[}]-->
				<!--[}else{]-->
				<tr><td class="nodata">暂无数据。</td></tr>
				<!--[}]-->
			</div>
		</div>
	</div>

	<div class="order-total my-dtl-btm gray-box">
		<table width="100%" height="100%">
			<tr>
				<td>订单总金额：<span class="c-red"><!--[= $formatCurrency1(typeof ordersum == 'undefined' ? '--' : ordersum)]--></span><!--[= typeof ordersum == 'undefined' ? '' : '&nbsp;信用B']--></td>
				<!-- <td>合同总金额：<span class="c-red"><!--[= $formatCurrency1(typeof contractsum == 'undefined' ? '--' : contractsum)]--></span><!--[= typeof contractsum == 'undefined' ? '' : '&nbsp;信用B']--></td> -->
				<!-- <td>验收总金额：<span class="c-red"><!--[= $formatCurrency1(typeof checksum == 'undefined' ? '--' : checksum)]--></span><!--[= typeof checksum == 'undefined' ? '' : '&nbsp;信用B']--></td> -->
			</tr>
		</table>
	</div>
	<div style="text-align:center;padding-bottom:15px;">
		<div id="handle-button">
			<span>
			<!--[if(childOrderStatus == 1){]-->
				<a class="button button-check" href="javascript:void(0)" data-id=<!--[= id]--> id="handle-button-check">到货验签</a>
			<!--[}]-->
			<!--[if(childOrderStatus == 5){]-->
				<a class="button button-check" href="javascript:void(0)" data-id=<!--[= id]--> id="handle-button-check">发货验签</a>
			<!--[}]-->
			</span>
			<a class="button" href="javascript:history.back()">返回</a>
		</div>
	</div>
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/personal/my-order-detail');
</script>

</body>
</html>
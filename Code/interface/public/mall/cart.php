<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>购物车-基建商城</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/mall.css" />
</head>
<body>

<?php include '../com/header.php'; ?>

<div class="section">
	<div class="page-vertical clearfix">
		<div class="breadcrumbs">
			<a href="../">首页</a> &gt; <a href="index.html">基建商城</a> &gt; <span>购物车</span>
			<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
		</div>
		<div class="shopping">
			<div id="zj-cart"></div>
			<div class="nodata" style="display: none;">您的购物车还没有数据，请到<a href="index.html">基建商城</a>添加。</div>
			<div class="add-other" style="display: none;"><a href="index.html">添加其他商品</a></div>
			<div class="settlement cart-bg">
				<div class="total-price-left clearfix">
					<span class="look-label">关联合同编号：</span>
					<div class="zj-select">
						<em><i></i></em>
						<span class="default">请选择合同编号</span>
						<ul class="dropdown" id="zj-contracts">
							<li data-v="">请选择合同编号</li>
						</ul>
						<input type="hidden" name="contractSn" />
					</div>
				</div>
					<div class="settlement-total"><span class="settlement-label">总价金额：</span><span class="totalprice"></span> 信用B</div>			
					<div class="cart-btm">
						<a href="http://lc.talk99.cn/chat/chat/p.do?c=10033976&f=10043368&g=10048426" target="_blank" class="call-service">物融客服</a>
						<button class="payfor">去结算</button>
					</div>
			</div>
		</div>
	</div>
</div><!--//section-->

<!--订单项目判断-->
<div class="modal" id="modal-contracts">
    <div class="modal-dialog">
        <div class="modal-content">
			<div class="modal-header">
	            <button class="modal-close">×</button>
	            <h4 class="modal-title changing">选择订单所属合同编号</h4>
			</div>
			<div class="modal-body" style="overflow-y: auto;max-height: 300px;">
				<ul id="zj-pact" class="pact-list clearfix"></ul>
				<div class="province-confirm">
					<button id="confirm-pro">确定</button>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-primary">确定</button>
				<button class="btn btn-default">取消</button>
			</div>
		</div>
	</div>
</div>
<!--订单项目判断end-->

<?php include '../com/footer.php'; ?>

<script id="zj-pact-tmpl" type="text/html">
	<!--[if(list.length == 0){]-->
	<span>没有合同编号</span>
	<!--[}]-->
	<!--[for(i = 0; i < list.length; i++){]-->
	<li data-code="<!--[= list[i].code]-->"><!--[= list[i].name]--></li>
	<!--[}]-->
</script>
<script id="zj-cart-tmpl" type="text/html">
	<!--[for(i = 0; i < list.length; i++){]-->
	<div class="shopping-item clearfix" data-id="<!--[= list[i].goodsId]-->">
		<div class="img"><img src="<!--[= $absImg(list[i].thumb)]-->"></div>
		<div class="shopping-info">
			<p><span>商品名称：</span><!--[= list[i].name]--></p>
			<p>
			<!--[for(j = 0; j < list[i].attr.length; j++){]-->
			<span><!--[= list[i].attr[j].name]-->：</span><span class="value"><!--[= list[i].attr[j].value]--></span>
			<!--[}]-->
			<span>计重方式：</span><span class="value"><!--[= list[i].unit]--></span>
			</p>
		</div>
		<div class="shopping-num clearfix">
			<label class="fl">数量：</label>
			<span class="jianfa sub fl"></span>
			<input class="numbers" value="<!--[= list[i].nums]-->" onkeyup="this.value=this.value.replace(/\D/g,'')"/>
			<span class="jiafa add"></span>
		</div>
		<div class="shopping-price">
			<p>交易单价：</p>
			<p><span><!--[== $formatCurrency(list[i].price)]--></span> 信用B</p>
		</div>
		<a class="delOrNot" href="#"></a>
	</div>
	<!--[}]-->
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/mall/cart');
</script>
</body>
</html>
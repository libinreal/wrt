<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title id="zj-title"></title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/mall.css" />
</head>
<body>

<?php include '../com/header.php'; ?>

<div class="section">
	<div class="page-vertical breadcrumbs list-header clearfix" id="zj-navi"></div>
	<div class="filter-condition">
		<div class="page-vertical">
			<div class="filter-item clearfix area">
				<label class="filter-label">销售区域：</label>
				<div class="filter-content clearfix">
					<ul class="clearfix" id="zj-area"></ul>
					<div class="filter-button"><a class="more" href="#">更多</a></div>
				</div>
			</div>
			<div class="filter-item clearfix factory">
				<label class="filter-label">厂家：</label>
				<div class="filter-content clearfix">
					<ul class="clearfix" id="zj-factory"></ul>	
					<div class="filter-button"><a class="more" href="#">更多</a></div>
				</div>
			</div>
			<div id="zj-condition"></div>
		</div>
	</div>
	<div class="filter-orderby">
		<div class="page-vertical">
			<label>排序：</label>
			<div class="clearfix">
				<a class="filter-orderby-item" data-key="price" href="#"><span>价格</span></a>
				<a class="filter-orderby-item" data-key="salesNum" href="#"><span>销量</span></a>
				<a class="filter-orderby-item" data-key="level" href="#" style="display: none;"><span>信誉等级</span></a>
				<a class="filter-orderby-item" data-key="storeNum" href="#"><span>可供数量</span></a>
			</div>
			<a class="btn-kefu" href="http://lc.talk99.cn/chat/chat/p.do?c=10033976&f=10043368&g=10046053" target="_blank">物融客服</a>
		</div>
	</div>
	<div class="page-vertical clearfix">
		<div class="general-list">
			<ul class="clearfix" id="zj-list"></ul>
		</div>
		<div class="paging" style="display: none;">
			<ul class="clearfix">
				<li><a href="" class="first unclick"><<</a></li><li><a href="" class="active unclick">1</a></li>
				<li><a href="">2</a></li><li><a href="">3</a></li>
				<li><a href="">4</a></li><li><a href="">5</a></li>
				<li><a href="" class="last">>></a></li>
			</ul>
		</div>
	</div>
</div><!--//section-->

<?php include '../com/footer.php';?>

<div class="modal modal3 fade" id="modal-province">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button class="modal-close">×</button>
				<h4 class="modal-title changing">切换省份</h4>
			</div>
			<div class="modal-body">
				<ul id="zj-province" class="province-list clearfix"></ul>
				<div class="province-confirm">
					<button id="confirm_pro">确定</button>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-primary">确定</button>
				<button class="btn btn-default">取消</button>
			</div>
		</div>
	</div>
</div>

<script id="zj-title-tmpl" type="text/html">
	<!--[for(i = 0; i < list.length; i++){]-->
	<!--[if(i == list.length - 1){]-->
		<!--[= list[i].name]-->
	<!--[}else{]-->
		<!--[= name]-->
	<!--[}]-->
	<!--[}]-->-基建商城
</script>

<script id="zj-navi-tmpl" type="text/html">
	<div class="navi">
	<a href="../mall/">首页</a> &gt; <a href="index.html">基建商城</a> &gt;
	<!--[for(i = 0; i < list.length; i++){]-->
	<!--[if(i == list.length - 1){]-->
	<span><!--[= list[i].name]--></span>
	<!--[}else{]-->
	<a href="<!--[= $getCartLink(list[i].code)]-->"><!--[= list[i].name]--></a> &gt; 
	<!--[}]-->
	<!--[}]-->
	</div>
	<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
</script>
<script id="zj-cart-good-tmpl" type="text/html">
	<div class="smt"><h4 class="fl">最新加入的商品</h4></div>
	<div class="smc">
		<ul id="mcart-mz">
			<li class="dt"><div class="fl"></div><div class="fr"><em>小计：￥<!--[= (price * nums).toFixed(2)]--></em></div></li>
			<li>
				<div class="p-img fl"><a href="<!--[= $getCartLink(code, id)]-->" target="_blank"><img src="<!--[= $absImg(thumb)]-->" width="50" height="50" alt=""></a></div>
				<div class="p-name fl"><span></span><a href="<!--[= $getCartLink(code, id)]-->" title="<!--[= goodsName]-->" target="_blank"><!--[= goodsName]--></a></div>
				<div class="p-detail fr ar"><span class="p-price"><strong>￥<!--[= price]--></strong>×<!--[= nums]--></span></div>
			</li>
		</ul>
	</div>
	<div class="smb t-r">共<b><!--[= total]--></b>件商品　共计<strong>￥ <!--[= totalPrice]--></strong><br><a href="cart.html" title="去购物车结算" id="btn-payforgoods">去购物车结算</a></div>
</script>
<script id="zj-province-tmpl" type="text/html">
    <!--[for(i = 0; i < list.length; i ++) {]-->
    <li><button data-id="<!--[= list[i].id]-->"><!--[= list[i].name]--></button></li>
    <!--[}]-->
</script>
<script id="zj-condition-tmpl" type="text/html">
	<!--[for(i = 0; i < list.length; i++){]-->
	<div class="filter-item clearfix">
		<label class="filter-label"><!--[= list[i].name]-->：</label>
		<div class="filter-content clearfix">
			<ul class="clearfix">
				<!--[for(j = 0; j < list[i].values.length; j++){]-->
				<li><a class="link" href="#" data-id="<!--[= list[i].id]-->" data-value="<!--[= list[i].values[j]]-->"><!--[= list[i].values[j]]--></a></li>
    			<!--[}]-->
			</ul>
			<div class="filter-button">
			<!--[if(list[i].values.length >= 10){]-->
				<a class="more" href="#">更多</a>
			<!--[}]-->
			</div>
		</div>
	</div>
    <!--[}]-->
</script>
<script id="zj-category-tmpl" type="text/html">
	<div class="filter-item clearfix">
		<label class="filter-label">商品分类：</label>
		<div class="filter-content clearfix">
			<ul class="clearfix">
				<!--[for(i = 0; i < list.length; i++){]-->
				<li><a class="link0" data-code="<!--[= list[i].code]-->" href="#"><!--[= list[i].name]--></a></li>
    			<!--[}]-->
			</ul>
			<!--[if(list.length >= 10){]-->
				<div class="filter-button"><a class="more" href="#">更多</a></div>
			<!--[}]-->
		</div>
	</div>
</script>
<script id="zj-list-tmpl" type="text/html">
	<!--[for(i = 0; i < list.length; i++){]-->
	<li>
		<div class="hist">
			<div class="title"><a href="detail.html?id=<!--[= list[i].id]-->" title="<!--[= list[i].name]-->"><!--[= list[i].name]--></a></div>
			<div class="bgimg"><a href="detail.html?id=<!--[= list[i].id]-->" title="<!--[= list[i].name]-->"><img src="<!--[= $absImg(list[i].thumb)]-->" /></a></div>
			<div class="price clearfix">
				<div style="text-align:center">
					<span class="price-label">交易单价</span><span 
					class="price-value"><!--[== $formatCurrency(list[i].vipPrice)]--></span><span 
					class="price-unit">信用B</span>
				</div>
			</div>
			<div class="operate clearfix">
				<a class="operate-shoucang <!--[= (list[i].hasFavorites == '1' ? 'active' : '')]-->" data-id="<!--[= list[i].id]-->" href="#"><span>收藏</span></a>
				<a class="operate-cart" data-id="<!--[= list[i].id]-->" href="#"><span>加入购物车</span></a>
			</div>
		</div>
	</li>
	<!--[}]-->
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/mall/list');
</script>
</body>
</html>
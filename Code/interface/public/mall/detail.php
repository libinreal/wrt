<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title id="zj-title"></title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/mall.css" />
</head>
<body>

<?php include '../com/header.php';?>

<div class="section">
	<div class="page-vertical clearfix">
		<div class="breadcrumbs" id="zj-navi"></div>
		<div class="detail-left-nav">
			<div class="strip">
				<img id="thumb" src="../content/images/common/blank.png" />
				<a class="back" href="#">返回列表</a>
			</div>
			<div class="similar">
				<div class="similar-logo"></div>
				<div id="zj-tltj">
					<ul class="similar-item">
						<li class="similar-thumb"></li>
						<li class="similar-name"></li>
					</ul>
				</div>
			</div>
		</div>	
		<div class="detail-right-content">
			<div class="info clearfix" id="zj-detail"></div>
			<ul class="intro_nav clearfix" style="display: none;">
				<li class="intro"><a class="active" href="#" data-to="#spjs">商品介绍</a></li>
				<!--<li class="stand"><a href="#" data-to="#ggjs">规格介绍</a></li>
				<li class="notice"><a href="#" data-to="#sysm">使用说明</a></li>
				<li class="saled"><a href="#" data-to="#shbz">售后保障</a></li>
				<li class="evald"><a href="#" data-to="#sppj">商品评价</a></li>-->
			</ul>
			<div class="shop_intro_list" style="display: none;">
				<ul class="clearfix" id="zj-detail1"></ul>
			</div>
			<div class="shop_intro_detail">
				<!--商品介绍-->
				<div class="article" id="spjs"></div>
				<!--//商品介绍-->
				<!--规格介绍-->
				<div class="article stan_table" id="ggjs" style="display: none;">
					<!--<table></table>-->
				</div>
				<!--//规格介绍-->
				<!--使用说明-->
				<div class="article" id="sysm" style="display: none;"></div>
				<!--//使用说明-->
				<!--售后保障-->
				<div class="article" id="shbz" style="display: none;"></div>
				<!--//售后保障-->
				<!--商品评价-->
				<div id="sppj" class="introduce">
					<div class="bline"></div>
					<div class="addcomment">
						<div class="addtitle">添加评论</div>
						<div class="tear">
							<textarea id="text" placeholder="请输入至少5个字符..."></textarea>
						</div>
						<div class="opear clearfix">
							<span class="fl">还可以输入<font style="color:#e34848;" id="text-stat">500</font>个字符</span>
							<button id="send">去评论</button>
						</div>
					</div>
					<div class="mbg"></div>
					<div class="comment_record">
						<div class="comment_title clearfix">
							<div class="username">用户名</div><div class="evaluate">评价</div><div class="all_evaluate">全部评价：(<span id="total">--</span>)</div>
						</div>
						<div class="comment_list" id="zj-comment"></div>
					</div>
				</div>
				<!--//商品评价-->
			</div>
		</div>
	</div>
</div><!--//section-->
<div class="general-list history-content">
	<a href="#" class="look-history">浏览历史</a>
	<div class="history-scroller">
		<ul class="clearfix" id="zj-history"></ul>
	</div>
</div>
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
	<!--[= name]-->-基建商城
</script>

<script id="zj-navi-tmpl" type="text/html">
	<a href="../mall/">首页</a> &gt; <a href="index.html">基建商城</a> &gt;
	<!--[for(i = 0; i < list.length; i++){]-->
	<a href="list.html?code=<!--[= list[i].code]-->"><!--[= list[i].name]--></a> &gt; 
	<!--[}]-->
	<span><!--[= name]--></span>
	<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
</script>
<script id="zj-detail-tmpl" type="text/html">
	<div class="img-info">
		<!--[if(pics && pics.length > 0 && pics[0]){]-->
		<img src="<!--[= $absImg(pics[0])]-->" alt="" class="bimg" />
		<div class="img-list">
			<div class="items clearfix">
			<!--[for(i = 0; i < pics.length; i++){]-->
			<a class="s-img" href="#" data-big="<!--[= $absImg(pics[i])]-->"><img src="<!--[= $absImg(pics[i])]-->" /></a>
			<!--[}]-->
			</div>
		</div>
		<!--[}else if(thumb){]-->
		<img src="<!--[= $absImg(thumb)]-->" alt="" class="bimg" />
		<div class="img-list">
			<div class="items clearfix">
				<a class="s-img" href="#" data-big="<!--[= $absImg(thumb)]-->"><img src="<!--[= $absImg(thumb)]-->" /></a>
			</div>
		</div>
		<!--[}else{]-->
		<img src="../content/images/common/d320x320-1.png" alt="" class="bimg" />
		<div class="img-list">
			<div class="items clearfix">
			</div>
		</div>
		<!--[}]-->
	</div>
	<div class="paramter-info">
		<div class="title"><!--[= name || '--']--></div>
		<div class="price clearfix">
			<div class="price-item">
				<span class="price-label">交易单价</span><span
				 class="price-value"><!--[== $formatCurrency1(vipPrice)]--></span><span
				 class="price-unit">&nbsp;信用B</span>
			</div>
			<!--<div class="price-item">
				<span class="price-label factory">挂牌单价</span><span
				 class="price-value"><!--[== $formatCurrency1(price)]--></span><span
				 class="price-unit">&nbsp;信用B</span>
			</div>-->
		</div>
		<!--<div class="comment"><span class="c-gray">商品评论：</span>(已有<!--[= total || '--']-->人参与)</div>-->
		<div class="distribution">
			<div class="distribution-name">配送至：<a class="distribution-choose operate" href="javascript:;"><!--[= salesArea || '--']--></a></div>
			<div class="distribution-name">物流费用：<span style="color:#ed1c24;"><!--[= shipping_fee || '--']--></span></div>
			<div class="distribution-desc"><span style="color:#7cb234;" id="quality-status"><!--[= (storeNum > 0 ? '有货' : '无货')]--></span> 可售数量：<span id="quality-num" style="color:#00a651;"><!--[= storeNum || '--']--></span><!--[= unit]--> 由<!--[= supplier || '--']-->负责配送从 <span style="color:#448ccb;"><!--[= shiplocal || '--']--></span> 发货，并提供售后服务。</div>
		</div>
		<div class="num-apply clearfix">
			<label class="fl">订购数量：</label>
			<a href="#" class="minus fl"></a>
			<input class="num-input fl" value="<!--[= (storeNum > 0 ? '1' : '0')]-->" maxlength="10" />
			<a href="#" class="add fl"></a>
		</div>
		<div class="apply-btn">
			<input id="max-num" type="hidden" value="<!--[= storeNum]-->" />
			<a href="#" class="shop-car" id="add-cart">加入购物车</a>
			<!--<a href="../customize/apply.html?goodsId=<!--[= id]-->" target="_blank" class="applying">申请定制</a>-->
			<a href="#" class="collect <!--[= (hasFavorites == 1) ? 'active' : '']-->">收藏</a>
			<!--<a href="http://lc.talk99.cn/chat/chat/p.do?c=10033976&f=10043368&g=10048426" target="_blank" class="call-service">物融客服</a>-->
		</div>
	</div>
</script>
<script id="zj-detail1-tmpl" type="text/html">
	<li class="shop-name">
		<label>商品名称：</label><!--[= goods.name || '--']-->
	</li>
	<li class="shop-no">
		<label>商品编号：</label><!--[= goods.wcode || '--']-->
	</li>
	<li class="shop-type">
		<label>类别：</label><!--[= type.name || '--']-->
	</li>
	<li class="shop-name lijl">
		<label>上架时间：</label><!--[= $formatDate(goods.createAt,'yyyy-MM-dd hh:mm:ss')]-->
	</li>
	<li class="shop-no lijl">
		<label>销售区域：</label><!--[= goods.salesArea || '--']-->
	</li>
	<li class="shop-type lijl">
		<label>提货地点：</label><!--[= goods.shiplocal || '--']-->
	</li>
</script>
<script id="zj-history-tmpl" type="text/html">
	<!--[for(i = 0; i < list.length; i++){]-->
	<li>
		<div class="hist">
			<div class="title"><a href="detail.html?id=<!--[= list[i].id]-->"><!--[= list[i].name]--></a></div>
			<div class="bgimg"><a href="detail.html?id=<!--[= list[i].id]-->"><img src="<!--[= $absImg(list[i].thumb)]-->" /></a></div>
			<div class="price clearfix">
				<div class="vip">
					<span class="price-label">交易单价</span><span 
					class="price-value"><!--[== $formatCurrency(list[i].vipPrice)]--></span><span 
					class="price-unit">信用B</span>
				</div>
				<!--<div class="default">
					<span class="price-label factory">挂牌单价</span><span 
					class="price-value"><!--[== $formatCurrency(list[i].price)]--></span><span 
					class="price-unit">信用B</span>
				</div>-->
			</div>
			<div class="operate">
				<a class="operate-shoucang <!--[= (list[i].hasFavorites ? 'active' : '')]-->" data-id="<!--[= list[i].id]-->" href="#"><span>已收藏</span></a>
				<a class="operate-cart" data-id="<!--[= list[i].id]-->" href="#"><span>加入购物车</span></a>
			</div>
		</div>
	</li>
	<!--[}]-->
</script>
<script id="zj-comment-tmpl" type="text/html">
	<!--[for(i = 0; i < list.length; i++){]-->
	<div class="list clearfix">
		<div class="username greencolor"><!--[= list[i].username]--></div>
		<div class="com_content fl">
			<!--[== list[i].content]-->
		</div>
		<label><!--[= $formatDate(list[i].createAt,'yyyy-MM-dd')]--></label>
	</div>
	<!--[}]-->
</script>
<script id="zj-tltj-tmpl" type="text/html">
	<!--[for(i = 0; i < list.length; i++){]-->
	<ul class="similar-item">
		<li class="similar-thumb"><a href="detail.html?id=<!--[= list[i].id]-->" title="<!--[= list[i].name]-->"><img src="<!--[= $absImg(list[i].thumb)]-->"/></a></li>
		<li class="similar-name"><a href="detail.html?id=<!--[= list[i].id]-->" title="<!--[= list[i].name]-->"><!--[= list[i].name]--></a></li>
		<li class="price large-price tuijian">
			<div class="vip">
				<span class="price-label">交易单价</span><span
				class="price-value"><!--[== $formatCurrency(list[i].vipPrice)]--></span><span 
				class="price-unit">信用B</span>
			</div>
		</li>
	</ul>
	<!--[}]-->
</script>
<script id="zj-province-tmpl" type="text/html">
    <!--[for(i = 0; i < list.length; i ++) {]-->
    <li><button data-id="<!--[= list[i].id]-->" type="<!--[= list[i].type]-->">
    	<!--[= list[i].name]--></button>
    </li>
    <!--[}]-->
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/mall/detail');
</script>
</body>
</html>
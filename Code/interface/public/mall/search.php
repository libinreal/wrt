<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>搜索-基建商城</title>
    <link rel="stylesheet" href="../content/css/common.css" />
    <link rel="stylesheet" href="../content/css/mall.css" />
</head>

<body>

    <?php include '../com/header.php';?>

    <div class="section section-search">
        <div class="page-vertical clearfix">
        	<div id="zj-dazong"></div>
        	<div id="zj-biaopin"></div>
        </div>
    </div>
    <!--//section-->

    <?php include '../com/footer.php';?>

    <script id="zj-dazong-tmpl" type="text/html">
    <div class="lst-nav clearfix">大宗类商品
        <em><!--[= count]--></em>个<a href="search-list.html?key=<!--[= key]-->">更多&gt;</a>
    </div>
    <ul class="large-list product-list-content">
    	<!--[for(i = 0; i < list.length; i++){]-->
    	<li class="clearfix">
    		<div class="large-logo"><img src="<!--[= $absImg(list[i].thumb)]-->"></div>
    		<div class="large-info">
    			<label><span>品名：</span><!--[= list[i].name]--><!--[for(j = 0; j < list[i].attr.length; j++){]-->
    			<span class="left_margin_s"><!--[= list[i].attr[j].name]-->：</span><!--[= list[i].attr[j].value]-->
    			<!--[}]--></label>
    			<label><span>厂家：</span><!--[= list[i].factoryName || '--']--><span class="left_margin_c">提货地点：</span><!--[= list[i].shiplocal]--><span class="left_margin_c">可销售数量：</span><font class="font-color"><!--[= list[i].storeNum]--></font> <!--[= list[i].unit]--></label>
    		</div>
    		<div class="price large-price">
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
    		<div class="operate large-operate">
    			<a class="operate-shoucang <!--[= (list[i].hasFavorites == '1' ? 'active' : '')]-->" data-id="<!--[= list[i].id]-->" href="#"><span>收藏</span></a>
    			<!--<a class="operate-dingzhi" href="../customize/apply.html?goodsId=<!--[= list[i].id]-->"><span>定制</span></a>-->
    			<a class="operate-cart" data-id="<!--[= list[i].id]-->" href="#"><span>加入购物车</span></a>
    		</div>
    	</li>
    	<!--[}]-->
    	<!--[if(list.length == 0){]-->
    	<div class="nodata">商城暂无出售此类商品，请重选其它类商品。</div>
    	<!--[}]-->
	</ul>
    </script>
    <script id="zj-biaopin-tmpl" type="text/html">
    <div class="normal-nav">标品类商品
        <em><!--[= count]--></em>个<a href="search-normal.html?key=<!--[= key]-->">更多&gt;</a>
    </div>
    <div class="general-list">
    	<!--[if(list.length == 0){]-->
    	<div class="nodata">商城暂无出售此类商品，请重选其它类商品。</div>
    	<!--[}]-->
        <ul class="product-list-content clearfix">
    	<!--[for(i = 0; i < list.length; i++){]-->
    	<li>
    		<div class="hist">
    			<div class="title"><a href="detail.html?id=<!--[= list[i].id]-->" title="<!--[= list[i].name]-->"><!--[= list[i].name]--></a></div>
    			<div class="bgimg"><a href="detail.html?id=<!--[= list[i].id]-->"><img src="<!--[= $absImg(list[i].thumb)]-->" /></a></div>
    			<div class="price clearfix">
                    <div style="text-align:center">
                    <span class="price-label">交易单价</span><span class="price-value"><!--[== $formatCurrency(list[i].vipPrice)]--></span><span class="price-unit">信用B</span>
                    </div>
    				<!--<div class="default">
    					<span class="price-label factory">挂牌单价</span><span 
    					class="price-value"><!--[== $formatCurrency(list[i].price)]--></span><span 
    					class="price-unit">信用B</span>
    				</div>-->
    			</div>
    			<div class="operate clearfix">
    				<a class="operate-shoucang <!--[= (list[i].hasFavorites == '1' ? 'active' : '')]-->" data-id="<!--[= list[i].id]-->" href="#"><span>收藏</span></a>
    				<a class="operate-cart" data-id="<!--[= list[i].id]-->" href="#"><span>加入购物车</span></a>
    			</div>
    		</div>
    	</li>
    	<!--[}]-->
    	</ul>
    </div>
    </script>

    <script src="../content/js/module/seajs/2.2.0/sea.js"></script>
    <script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
    <script>
    seajs.use('../content/js/mall/search');
    </script>
</body>

</html>

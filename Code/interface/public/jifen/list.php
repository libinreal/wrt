<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>积分商城-分类列表</title>
    <link rel="stylesheet" href="../content/css/common.css" />
    <link rel="stylesheet" href="../content/css/jifen.css" />
</head>
<body>

<?php include "../com/header.php"; ?>

<div class="section content-left-bg">
    <div class="page-vertical clearfix">
    	<div class="content-left menu">
			<a class="first" href="index.html"><span class="span">商品分类</span></a>
			<div id="zj-category"></div>
    	</div>
    	<div class="content-right jifen_right">
            <div class="breadcrumbs">
                <a href="../mall/">首页</a> &gt; <a href="index.html">积分商城</a> &gt; <span id="type-name">...</span>
                <a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
            </div>
			<div class="list category_product clearfix">
				<div class="category"><div id="category-name" class="line">...</div></div> 
			    <ul id="zj-list" class="clearfix"></ul>
		    </div>
    	</div>
    </div>
</div><!--//section-->

<?php include "../com/footer.php"; ?>

<script id="zj-category-tmpl" type="text/html">
    <!--[for(i = 0; i < list.length; i ++) {]-->
    <a href="list.html?type=<!--[= list[i].id]-->"><span class="span"><!--[= list[i].name]--></span></a>
    <!--[}]-->
</script>
<script id="zj-list-tmpl" type="text/html">
	<!--[for(i = 0; i < list.length; i ++) {]-->
	<div class="product_1<!--[= (i % 4 == 0 ? ' nol' : '')]-->">
    	<div class="top"><a href="detail.html?id=<!--[= list[i].id]-->" title="<!--[= list[i].name]-->"><img src="<!--[= list[i].imageUrl]-->" /></a></div>
		<div class="describe_title"><a href="detail.html?id=<!--[= list[i].id]-->" title="<!--[= list[i].name]-->"><!--[= list[i].name]--></a></div>
	    <div class="describe_jifen">所需积分：<b class="c-red"><!--[== $formatCurrency(list[i].credits)]--></b></div>
	</div>
	<!--[}]-->
</script>
<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
seajs.use("../content/js/jifen/list.js");
</script>
</body>
</html>
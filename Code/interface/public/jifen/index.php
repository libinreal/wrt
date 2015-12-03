<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>积分商城</title>
    <link rel="stylesheet" href="../content/css/common.css" />
    <link rel="stylesheet" href="../content/css/jifen.css" />
</head>
<body>

<?php include "../com/header.php"; ?>

<div class="section content-left-bg">
    <div class="page-vertical clearfix">
		<div class="content-left menu">
			<a class="first" href="javascript:;"><span class="span">商品分类</span></a>
			<div id="zj-category"></div>
   		</div>
    	<div class="content-right jifen_right jifen-home">
			<div class="jifen_first clearfix">
				<div class="jifen_first_left scroll-img">
					<div class="items" id="zj-ad"></div>
					<div class="navi"></div>
				</div>
				<div class="jifen_first_right">
					<div class="top">
				        <div class="top_one clearfix">
				        	<div class="advis">公告栏</div><a class="for_more" href="../notice/list.html?type=2004">更多></a>
				        </div>
				        <img src="../content/images/jifen/line.gif" />
						<ul id="zj-notice"></ul>
				    </div>
					<div class="next">
						<div class="img"><img src="../content/images/jifen/jifen_1.gif" /></div>
						<div class="my_jifen"><a href="../personal/my-jifen.html">我的积分：<b class="c-red" id="credits">...</b></a></div>
					</div>
				</div>
			</div>
		    <div id="zj-jifen"></div>
    	</div>
    </div>
</div><!--//section-->

<?php include "../com/footer.php"; ?>

<script id="zj-category-tmpl" type="text/html">
    <!--[for(i = 0; i < list.length; i ++) {]-->
	<a href="list.html?type=<!--[= list[i].id]-->"><span class="span"><!--[= list[i].name]--></span></a>
    <!--[}]-->
</script>
<script id="zj-jifen-tmpl" type="text/html">
	<!--[for(i = 0; i < list.length; i ++) {]-->
	<div class="home_eletrict">
        <a class="number" href="list.html?type=<!--[= $getFloor(category,i).id]-->"><em class="f-<!--[= (i+1)]-->"><!--[= (i+1)]-->F</em><span><!--[= $getFloor(category,i).name]--></span></a>
    </div>
    <div class="product clearfix">
        <!--[for(j = 0; j < 4; j ++) {]-->
		<!--[if(list[i][j]){]-->
		<div class="product_1">
			<div class="product_1_1">
				 <a class="img" href="detail.html?id=<!--[= list[i][j].id]-->" title="<!--[= list[i][j].name]-->"><img src="<!--[= $absImg(list[i][j].imageUrl)]-->" alt=""/></a>
				 <div class="desc"><a href="detail.html?id=<!--[= list[i][j].id]-->" title="<!--[= list[i][j].name]-->"><!--[= list[i][j].name]--></a></div>		
				 <div class="credits">所需积分：<span class="c-red"><!--[== $formatCurrency(list[i][j].credits)]--></span></div>
			</div>
		</div>
		<!--[}else{]-->
		<div class="product_1">
			<div class="product_1_1">
				 <a class="img" href="javascript:;"></a>
				 <div class="desc"><a href="javascript:;"></a></div>
				 <div class="credits"></div>
			</div>
		</div>
		<!--[}]-->
	    <!--[}]-->
    </div>  
	<!--[}]-->
</script>
<script id="zj-notice-tmpl" type="text/html">
    <!--[for(i = 0; i < list.length; i ++) {]-->
    <li>
        <a href="../notice/detail.html?type=2004&id=<!--[= list[i].id]-->" title="<!--[= list[i].title]-->">
            <span class="title"><!--[= list[i].title]--></span>
            <span class="date"><!--[= $formatDate(list[i].createAt,"MM/dd")]--></span>
        </a>
    </li>
    <!--[}]-->
</script>
<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
seajs.use("../content/js/jifen/index");
</script>
</body>
</html>
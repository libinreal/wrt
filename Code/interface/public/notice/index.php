<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>物融新闻-中交物融平台</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/news.css" />
</head>
<body>

<?php include '../com/header.php'; ?>

<div class="section content-news">
	<div class="page-vertical">
		<div class="content-top clearfix">
			<div class="scroll-img"><img src="../content/images/customize/big.png"></div>
			<div class="notice">
				<div class="left clearfix"><h3>公告栏</h3><a class="leftspan" href="#">更多 ></a></div>
				<ul id="zj-notice"></ul>
			</div>
		</div>
		<div class="list clearfix">
			<div class="list-ti">
				<div class="left clearfix"><h3>媒体声音</h3><a class="leftspan" href="list.html?type=1001">更多 ></a></div>
				<ul id="media-news"></ul>
			</div>
			<div class="list-ti">
				<div class="left clearfix"><h3>品牌新闻</h3><a class="leftspan" href="list.html?type=1002">更多 ></a></div>
				<ul id="brand-news"></ul>
			</div>
			<div class="list-ti">
				<div class="left clearfix"><h3>市场活动</h3><a class="leftspan" href="list.html?type=1003">更多 ></a></div>
				<ul id="market-news"></ul>
			</div>
		</div>
	</div>
</div><!--//section-->

<?php include '../com/footer.php'; ?>

<script id="zj-notice-tmpl" type="text/html">
    <!--[for(i = 0; i < list.length; i ++) {]-->
	<li>
	    <a class="<!--[= (i == list.length - 1) ? 'last' : '']-->" href="detail.html?id=<!--[= list[i].id]-->" title="<!--[= list[i].title]-->">
    		<span class="title">[<!--[= $noticeType(list[i].catType)]-->] <!--[= list[i].title]--></span>
    		<span class="date"><!--[= $formatDate(list[i].createAt,'MM/dd')]--></span>
	    </a>
	</li>
    <!--[}]-->
</script>
<script id="zj-news-tmpl" type="text/html">
    <!--[for(i = 0; i < list.length; i ++) {]-->
	<!--[if (i == 0){]-->
	<li class="top">
		<div class="list-img"><a href="detail.html?type=<!--[= type]-->&id=<!--[= list[i].id]-->"><img src="<!--[= $absImg(list[i].imgurl)]-->"></a></div>
        <h1><a href="detail.html?type=<!--[= type]-->&id=<!--[= list[i].id]-->" title="<!--[= list[i].title]-->"><!--[= list[i].title]--></a></h1>
        <p><!--[= list[i].brief]--></p>
	</li>	
	<!--[}else{]-->
	<li class="<!--[= (i == list.length - 1) ? 'last' : '']-->">
	    <a href="detail.html?type=<!--[= type]-->&id=<!--[= list[i].id]-->" title="<!--[= list[i].title]-->">
    	   <span class="title" style="width: 80%;"><!--[= list[i].title]--></span>
    	   <span class="date"><!--[= $formatDate(list[i].createAt,'MM/dd')]--></span>
	    </a>
	</li>
    <!--[}]-->
    <!--[}]-->
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
seajs.use('../content/js/news/index');
</script>

</body>
</html>
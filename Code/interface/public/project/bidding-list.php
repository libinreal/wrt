<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>工程招标-工程资讯</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/project.css" />
</head>
<body>

<?php include '../com/header.php'; ?>

<div class="section content-left-bg">
	<div class="page-vertical clearfix">
		<div class="content-left">
			<div class="bidding-menu-content">
				<a href="bidding-list.html?type=ZB_JZ" class="pro-1">建筑工程</a>
				<a href="bidding-list.html?type=ZB_SZ" class="pro-2">市政工程</a>
				<a href="bidding-list.html?type=ZB_TL" class="pro-3">铁路工程</a>
				<a href="bidding-list.html?type=ZB_GL" class="pro-4">公路工程</a>
				<a href="bidding-list.html?type=ZB_GH" class="pro-5">港航工程</a>
				<a href="bidding-list.html?type=ZB_SL" class="pro-6">水利工程</a>
				<a href="bidding-list.html?type=NZJ"   class="pro-7">拟在建工程</a>
	   		</div>
   		</div>
   		<div class="content-right">
			<div class="breadcrumbs">
				<a href="../">首页</a> &gt; <a href="index.html">工程咨讯</a> &gt; <a href="bidding.html">工程招标</a> &gt; <span>更多招标信息</span>
				<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
			</div>
   			<div class="bidding-list" id="zj-list">
   			</div>
   		</div>
	</div>
</div><!--//section-->

<?php include '../com/footer.php'; ?>

<script id="zj-list-tmpl" type="text/html">
    <!--[for(i = 0; i < list.length; i ++) {]-->
	<div class="bidding-item">
		<div class="bidding-item-title">
			<div class="bidding-item-name">项目名称：</div>
			<div class="bidding-item-text" title="<!--[=list[i].name]-->"><!--[=list[i].name]--></div>
			<div class="bidding-item-name">所在省份：</div>
			<div class="bidding-item-text2"><!--[=list[i].area]--></div>
		</div>
		<div class="bidding-item-content clearfix">
			<div class="bidding-item-content1">
				<div class="bidding-item-name">项目概况：</div>
				<div class="bidding-item-text"><!--[=list[i].prjdesc]--></div>
				<div class="bidding-item-name">招标内容：</div>
				<div class="bidding-item-text"><!--[=list[i].content]--></div>
			</div>
			<div class="bidding-item-content2">
				<div class="bidding-item-name">招标人：</div>
				<div class="bidding-item-text2"><!--[=list[i].biddingman]--></div>
				<div class="bidding-item-name">招标条件：</div>
				<div class="bidding-item-text2"><!--[=list[i].conditions]--></div>
				<div class="bidding-item-name">招标时间：</div>
				<div class="bidding-item-text2"><!--[=$formatDate(list[i].biddingAt,'yyyy年MM月dd日')]--></div>
				<div class="bidding-item-name">招标总额：</div>
				<div class="bidding-item-text2"><span class="c-red"><!--[=$formatCurrency1(list[i].amount,'','')]--></span>万元</div>
				<div class="bidding-item-name">项目地址：</div>
				<div class="bidding-item-text2"><!--[=list[i].prjaddress || '--']--></div>
			</div>
		</div>
	</div>
    <!--[}]-->
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
seajs.use('../content/js/project/list');
</script>

</body>
</html>
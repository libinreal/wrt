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

<div class="section content-left-bg bidding">
	<div class="page-vertical clearfix">
		<div class="content-left clearfix">
			<a href="#ZB_JZ" class="pro-1">建筑工程</a>
			<a href="#ZB_SZ" class="pro-2">市政工程</a>
			<a href="#ZB_TL" class="pro-3">铁路工程</a>
			<a href="#ZB_GL" class="pro-4">公路工程</a>
			<a href="#ZB_GH" class="pro-5">港航工程</a>
			<a href="#ZB_SL" class="pro-6">水利工程</a>
			<a href="#NZJ"   class="pro-7">拟在建工程</a>
   		</div>
		<div class="breadcrumbs">
			<a style="padding-left:10px" href="../">首页</a> &gt; <a href="index.html">工程咨讯</a> &gt; <span>工程招标</span>
			<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
		</div>
   		<div class="content-right bidding-map">
   			<div class="bidding-map-content" draggable="true" id="worldMap">
   				<img src="../content/images/project/map-2.jpg" alt="" />
   				<div id="zj-list"></div>
   			</div>
   		</div>
   		<div class="clear"></div>
   		<div class="bidding-friend clearfix">
   			<a class="bidding-friend-more" href="bidding-list.html"><span class="button btn-secondary">更多工程<br/>招标信息</span></a>
   			<div class="bidding-friend-content clearfix">
   				<div class="bidding-friend-title">相关链接：</div>
   				<ul class="bidding-friend-links clearfix" id="linksData"></ul>
   			</div>
   		</div>
	</div>
</div><!--//section-->

<?php include '../com/footer.php'; ?>

<div class="modal fade" id="modal-business">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button class="modal-close">×</button>
				<h4 class="modal-title">更多物资行情资讯</h4>
			</div>
			<div class="modal-body">
			</div>
			<div class="modal-footer">
				<button class="btn btn-primary">确定</button>
				<button class="btn btn-default">取消</button>
			</div>
		</div>
	</div>
</div>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script type="text/html" id="linksTmpl">
	<!--[for(i=0;i<list.length;i++){]-->
	<li><a href="<!--[=list[i].url]-->" target="_blank"><img src="<!--[=$absImg(list[i].icon)]-->" alt="<!--[=list[i].title]-->" class="qianlima"/></a></li>
	<!--[}]-->
</script>
<script type="text/html" id="zj-list-tmpl">
	<!--[for(i=0;i<list.length;i++){]-->
	<a class="bidding-tooltip" href="bidding-list.html?areaId=<!--[= list[i].areaId]-->" title="<!--[= list[i].area]-->" data-id="<!--[= list[i].areaId]-->">
		<i></i>
		<h3 class="clearfix">
			<span class="area"><!--[= list[i].area]--></span>
			<span class="count"><em><!--[= list[i].number]--></em>处</span>
		</h3>
		<p>总标的额：</p>
		<p><span class="c-red"><!--[= $formatCurrency1(list[i].amount)]--></span>万元</p>
	</a>
	<!--[}]-->
</script>
<script>
seajs.use('../content/js/project/bidding');
</script>

</body>
</html>
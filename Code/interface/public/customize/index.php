<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
	<title>定制专区</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/customize.css" />
</head>
<body>

<?php include '../com/header.php'; ?>

<div class="section customize">
	<div class="page-vertical">
		<div class="content-top clearfix">
			<div class="scroll-img">
				<div class="items" id="zj-ad"></div>
				<div class="navi"></div>
			</div>
			<div class="notice">
				<div class="notice-title"><span class="notice-bar">公告栏</span><a class="notice-more" href="../notice/list.html?type=2003">更多 &gt;</a></div>
				<ul id="zj-notice"></ul>
			</div>
			<div class="applying clearfix">
				<a class="apply" href="apply.html" id="btn-submit">
					<span>定制申请</span>
				</a>
				<a class="record" href="../personal/my-customize.html" class="apply_recordbg">
					<span>定制申请记录</span>
				</a>
			</div>
		</div>
		<div class="content-desc"><img src="../content/images/customize/desc.png" alt="" /></div>
		<div id="zj-list" class="content-bottom clearfix"></div>
		<!-- 类别筛选Start -->
		<div class="sort">
			<ul id="zj-sort"></ul>
		</div>
		<!-- 类别筛选End -->
	</div>
</div><!--//section-->

<?php include '../com/footer.php'; ?>

<script id="zj-list-tmpl" type="text/html">
    <!--[for(i = 0; i < list.length; i ++) {]-->
	<div class="list-item">
		<div class="list-block">
			<div class="title"><!--[= list[i].goodsName]--></div>
			<div class="standard clearfix">
				<div><span class="label">规格：</span><!--[= list[i].goodsSpec || '--']--></div>
				<div class="area fr" title="<!--[= list[i].area]-->"><span class="label">销售区域：</span><!--[= list[i].area || '--']--></div>
				<div class="model fl"><span class="label">型号：</span><!--[= list[i].goodsModel || '--']--></div>
			</div>
			<div class="extra clearfix">
				<div class="lafc fl">
					<span class="label">信用B：</span>
					<span class="price"><!--[== $formatCurrency(list[i].goodsPrice) ]--></span>
				</div>
				<div class="bor fr">
					<!--[if(list[i].expire=='unexpire'){]-->
						<a class="button btn-secondary big" href="apply.html?id=<!--[= list[i].id]-->">追加定制</a>
					<!--[}else{]-->
						<a class="button btn-gray big disabled" href="javascript:;">已过期</a>
					<!--[}]-->
				</div>
			</div>

			<div class="shop-img">
				<img src="<!--[= $absImg(list[i].thumb)]-->">
			</div>
		</div>
		<div class="list-num">
			<p><span class="label">今日累计定制数量：</span><!--[= list[i].weightTotal]--><!--[= list[i].goodsUnit]--></p>
			<p><span class="label">今日累计已追加数：</span><!--[= list[i].applyTotal]-->个</p>
		</div>
	</div>
    <!--[}]-->
</script>

<!-- 公告模板 -->
<script id="zj-notice-tmpl" type="text/html">
    <!--[for(i = 0; i < list.length; i ++) {]-->
    	<li><a href="../notice/detail.html?type=2003&id=<!--[= list[i].id]-->" title="<!--[= list[i].title]-->"><label><!--[= list[i].title]--></label><span><!--[= $formatDate(list[i].createAt,'MM/dd')]--></span></a></li>
    <!--[}]-->
</script>

<!-- 分类接口模板 -->
<script id="zj-sort-tmpl" type="text/html">
    <!--[for(i = 0; i < list.length; i ++) {]-->
        <a href="#" data-v="<!--[= list[i].code]-->" title="<!--[= list[i].name]-->"><!--[= list[i].name]--></a>
    <!--[}]-->
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/customize/index');
</script>
</body>
</html>
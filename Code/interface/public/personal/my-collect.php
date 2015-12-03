<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>我的收藏-个人中心</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/personal.css" />
</head>
<body>

<?php include '../com/header.php'; ?>

<div class="section content-left-bg">
	<div class="page-vertical clearfix">
	<?php include '../com/nav-left.php'; ?>
		<div class="content-right">
			<div class="breadcrumbs">
				<a href="../">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <span id="type-name">我的收藏</span>
				<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
			</div>
			<ul class="collect-list" id="collectListData"></ul>
		</div>
	</div>
</div>
<!--//section-->

<?php include '../com/footer.php'; ?>

<script type="text/html" id="collectListTmpl">
<!--[for(i=0;i<list.length;i++){]-->
	<li class="clearfix">
		<a style="display:block" href="<!--[= $getLink(list[i].category,list[i].id)]-->">
			<div class="clearfix three-a">
				<div class="c-l-img clearfix" style='float:left'>
					<img width="105" height="105" src="<!--[= $absImg(list[i].thumb)]-->" />
				</div>
				<div class="c-l-info clearfix">
					<div>
						<span class="gray-span">商品名称：</span><!--[= list[i].name || '--']-->
					</div>
					<div>
					<!--[for(j = 0; j < list[i].attr.length; j++){]-->
						<label>
							<span class="gray-span"><!--[= list[i].attr[j].name]-->：</span><!--[= list[i].attr[j].value || '--']-->
						</label>
					<!--[}]-->
					</div>
				</div>
				<div class="c-l-price clearfix">
					<label><span class="gray-span">挂牌单价：</span><span class="red-font"><!--[= $formatCurrency1(list[i].price)]--></span> 信用B</label>
				</div>
			</div>
		</a>
		<div style='float:right' class="c-l-remove removeCollect" data-id="<!--[=list[i].id]-->" title="删除收藏"></div>
	</li>
<!--[}]-->
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/personal/my-collect');
</script>

</body>
</html>
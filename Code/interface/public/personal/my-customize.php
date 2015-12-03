<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>我的定制-个人中心</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/personal.css" />
	<style type="text/css">
		.header-right .my-customize{
			display: block;
		}
	</style>
</head>
<body>

<?php include '../com/header.php'; ?>

<div class="section content-left-bg">
	<div class="page-vertical clearfix">
	<?php include '../com/nav-left.php'; ?>
		<div class="content-right">
			<div class="breadcrumbs">
				<a href="../">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <span id="type-name" >我的定制</span>
				<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
			</div>	
			<ul class="nav-tabs gcdz-qh clearfix">
				<li><a href="#exchange" class="active">物资定制</a></li>
				<li><a href="#record">工程定制</a></li>
			</ul>
				<div class="tab-ckdz">
					<ul class="customize-list tab-item clearfix" id="exchange"></ul>
					<ul class="customize-list tab-item clearfix" id="record"></ul>
				</div>
				<div class="nextpage"><a class="zj-prev disabled" href="#">上一页</a><a class="zj-next" href="#">下一页</a></div>
		</div>
	</div>
</div>
<!--//section-->

<?php include '../com/footer.php'; ?>

<script id="zj-exchange-tmpl" type="text/html">
    <!--[for(i = 0; i < list.length; i ++) {]-->
	<li class="gray-box wzdz-li clearfix">
		<div class="customize-product">
			<div class="customize-list-head">
				<span class="gray-span"><!--[= $formatDate(list[i].createAt,'yyyy-MM-dd')]--></span>
				<span class="float-right"><!--[= $getStatus(list[i].status)]--></span>
			</div>
			<div class="customize-list-content clearfix">
				<img class="customize-product-img" alt="" src="<!--[= $absImg(list[i].thumb)]-->">
				<div class="customize-product-info">
					<div class="product-name">
						<span class="gray-span">商品名称：</span><span><!--[= list[i].goodsName]--></span>
					</div>
					<div class="product-format">
						<label><span class="gray-span">商品规格：</span><!--[= list[i].goodsSpec || '--']--></label>
						<label><span class="gray-span">商品型号：</span><!--[= list[i].goodsModel || '--']--></label>
					</div>
					<div class="product-number">
						<span class="gray-span">购买数量：</span><!--[= list[i].number]--><!--[= list[i].goodsUnit]-->
						<label class="float-right">
							你的定制价格：<span class="red-font"><!--[= $formatCurrency1(list[i].goodsPrice)]--> 信用B</span>
						</label>
					</div>
				</div>
			</div>
		</div>
	</li>
    <!--[}]-->
</script>

<script id="zj-record-tmpl" type="text/html">
    <!--[for(i = 0; i < list.length; i ++) {]-->
	<li class="gray-box gcdz-items clearfix">
		<div class="customize-product">
			<div class="customize-list-head clearfix">
				<span class="gray-span"><!--[= $formatDate(list[i].createAt,'yyyy-MM-dd')]--></span>
				<div class="dz-top-r clearfix">
					<span class="dz-status"><!--[= $getStatus(list[i].status)]--></span>
					<span class="float-right ckxq"><a class="button btn-gray" href="my-customize-detail.html?id=<!--[= list[i].id]-->">查看详情</a></span>
				</div>
			</div>
			<ul class="gcdz-content clearfix">
				<li class="gcdz-name"><em>项目名称：</em><span><!--[= list[i].name]--></span></li>
				<li class="gcdz-name"><em class="r-next">联系人：</em><span><!--[= list[i].contactPeople]--></span></li>
				<li class="gcdz-name"><em class="r-next">联系方式：</em><span><!--[= list[i].contactTelephone]--></span></li>
				<li class="gcdz-name"><em>项目工期：</em><span><!--[= list[i].period]--></span></li>
				<li class="gcdz-name"><em class="r-next">项目投资额度：</em><span><!--[= list[i].amount]--></span></li>
				<li class="gcdz-name"><em class="r-next">所属区域：</em><span><!--[= list[i].area]--></span></li>
			</ul>
		</div>
	</li>
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
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/personal/my-customize');
</script>

</body>
</html>
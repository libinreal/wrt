<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>我的订单-个人中心</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/personal.css" />
</head>
<body>

<?php include '../com/header.php'; ?>

<div class="section content-left-bg">
	<div class="page-vertical clearfix">
	<?php include '../com/wrt-left.php'; ?>
		<div class="content-right">
			<div class="breadcrumbs">
				<a href="../">首页</a> &gt; <a href="index-in.html">物融通</a> &gt; <span id="type-name" >我的订单</span>
				<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
			</div>
			<div class="order-list gray-box">
				<div class="order-list-header clearfix">
					<div class="order-list-col c1">订单编号</div>
					<div class="order-list-col c2">项目编号</div>
					<div class="order-list-col c3">项目名称</div>
					<div class="order-list-col c4">订单状态</div>
					<div class="order-list-col c5"></div>
				</div>
				<div class="order-list-content" id="zj-list">
					<a href="my-order-detail.html?id=6" class="clearfix">
						<div class="order-list-col c1">2014120393133</div>
						<div class="order-list-col c2">HT201411065</div>
						<div class="order-list-col c3">2014-11-12test</div>
						<div class="order-list-col c4">已提交</div>
						<div class="order-list-col c5">
						
							<button class="button btn-gray" data-id="6">取消订单</button>
						
						</div>
					</a>
			    </div>
			    <div class="nextpage" style="">
			    	<a class="zj-prev disabled" href="#">上一页</a><a class="zj-next disabled" href="#">下一页</a>
			    </div>
			</div>

		</div>
	</div>
</div>
<!--//section-->

<?php include '../com/footer.php'; ?>

<script id="zj-list-tmpl" type="text/html">
   
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js');
</script>

</body>
</html>
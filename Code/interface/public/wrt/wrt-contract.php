<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>个人中心</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/personal.css" />
	<link rel="stylesheet" href="../content/css/personal-index.css" />
	<link rel="stylesheet" href="../content/css/wrt.css" />
</head>
<body>

<?php include "../com/header.php"; ?>

<div class="section content-left-bg">
	<div class="page-vertical clearfix">
		<?php include '../com/wrt-left.php'; ?>
		<div class="content-right"style="padding-top: 15px;">
			<!--中间-->
			<div class="project-list gray-box" style="min-height: 211px;">
				<div class="project-t">
					<div class="project-l" style="margin-left:10px;"><span style="margin-left: 0;">信用B合同</span></div>
				</div>
				<div class="wrt-creditb-coon clearfix">
					<div class="wrt-creditb-l clearfix">
						<div class="wrt-qdht">签订新信用B合同</div>
						<ul>
							<li class="msg-point">提示：请准备好U-key进入信用B合同签订流程</li>
							<li><a href="#" class="button btn-primary">建立新合同</a></li>
						</ul>
					</div>
					<div class="wrt-creditb-r" style="float:right">
						<div class="wrt-qdht">查看信用B合同记录</div>
						<ul>
							<li class="msg-point">若要查看账户的信用合同，请点击查询进入系统查看详情</li>
							<li><a href="#" class="button btn-gray">点击查看</a></li>
						</ul>
					</div>
				</div>
			</div>
			<!--中间-->
			<!--3-->
			<div class="order-list gray-box">
				<div class="wrt-3 clearfix" style="padding: 17px 10px;font-size:17px;border-bottom:1px solid #E6E6E6">
					<div class="title-l">信用B合同历史记录：</div>
					<div class="title-r">共<span>26</span>个订单</div>
				</div>
				<div class="order-list-header clearfix" style="font-weight:normal">
					<div class="order-list-col order-contract c1">合同编号</div>
					<div class="order-list-col order-contract c2">合同名称</div>
					<div class="order-list-col order-contract c3">合同建立时间</div>
					<div class="order-list-col order-contract c4">合同到期时间</div>
					<div class="order-list-col order-contract c5">合同总金额（元）</div>
					<div class="order-list-col order-contract c5">合同状态</div>
				</div>
				<div class="order-list-content">
					<a href="my-order-detail.html?id=6" class="clearfix">
						<div class="order-list-col order-contract c1">2014120393133</div>
						<div class="order-list-col order-contract c2">name</div>
						<div class="order-list-col order-contract c3">2015-1-1 19:23</div>
						<div class="order-list-col order-contract c4">2015-1-1 19:23</div>
						<div class="order-list-col order-contract c5"><span>100000.00</div>
						<div class="order-list-col order-contract c5">执行中</div>
					</a>
			    </div>
			    <div class="nextpage" style="">
			    	<a class="zj-prev disabled" href="#">上一页</a><a class="zj-next disabled" href="#">下一页</a>
			    </div>
			</div>
			<!--3-->
				
			</div>
			
		</div>
	</div>
</div>
<!--//section-->

<?php include "../com/footer.php"; ?>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/');
</script>

</body>
</html>
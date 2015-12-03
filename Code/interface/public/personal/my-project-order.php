<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>我的项目-个人中心</title>
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
				<a href="index.html">个人中心</a> / <span id="type-name" >我的项目</span> / <span id="type-name" >查看工程订单详情</span>
				<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
			</div>	
			<div class="gray-box">
				<div class="project-detail">
					<div class="project-t order-t top-box">
						<div class="project-l order-l">项目名：<span>中铁局xxx项目</span></div>					
					</div>
					<div class="project-m order-m middle-box">
						<ul>
							<li>公司名称：<span>xxx</span></li>
							<li>合同编号：<span>xxx</span></li>
							<li class="m-l">合同金额：<span><em>6806.00</em>&nbsp;/&nbsp;信用B</span></li>
							<li class="m-r">合同有效截止日：<span>2014-10-30</span></li>										
						</ul>
					</div>
					<div class="project-b clearfix">
						<ul>
							<li>商品清单：</li>
							<li class="b-l">商品名称：<span>xxxxxxxxxxxxx</span></li>
							<li class="b-c">规格：<span>xxxxxx</span></li>
							<li class="b-r">型号：<span>xxxxxx</span></li>

							<li class="b-l">商品名称：<span>xxxxxxxxxxxxx</span></li>
							<li class="b-c">规格：<span>xxxxxx</span></li>
							<li class="b-r">型号：<span>xxxxxx</span></li>						
						
							<li class="b-l">商品名称：<span>xxxxxxxxxxxxx</span></li>
							<li class="b-c">规格：<span>xxxxxx</span></li>
							<li class="b-r">型号：<span>xxxxxx</span></li>						
						</ul>
					</div>					
				</div>	
				<!---->
				<div class="bg-next">
					<div class="project-order">
						<div class="detail-b1 clearfix">
							<ul>
								<li class="b1-l">合同编号：<span>xxx</span></li>
								<li class="b1-c">单位名称：<span>中铁二十一局xxx</span></li>
								<li class="b1-r">用户名：<span>ZT001</span></li>
							</ul>
							<ul>
								<li class="b1-l">合同金额：<span><em>6806.00</em>&nbsp;/&nbsp;信用B</span></li>
								<li class="b1-c">交货时间：<span>2014-10-30</span></li>
							</ul>
						</div>
						<div class="detail-b2 clearfix">
							<ul>
								<li>商品清单：</li>
								<li class="b2-l">商品名称：<span>xxxxxxxxxxxxx</span></li>
								<li class="b2-c">规格：<span>xxxxxx</span></li>
								<li class="b2-r">型号：<span>xxxxxx</span></li>
								<li class="b2-btm"><a href="../mall/cart.html">立即下单</a></li>
							</ul>
						</div>
					</div>
				</div>
			    <!---->
			</div>
		</div>
	</div>
</div>
<!--//section-->

<?php include '../com/footer.php'; ?>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/personal/');
</script>

</body>
</html>
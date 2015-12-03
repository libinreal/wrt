<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>信用等级-我的信用B</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/personal.css" />
</head>
<body>

	<?php include "../com/header.php"; ?>

	<div class="section content-left-bg">
		<div class="page-vertical clearfix">
		<?php include "../com/nav-left.php"; ?>
		<div class="content-right">
			<div class="breadcrumbs">
				<a href="../">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <a href="my-creditb.html">我的信用B</a> &gt; <span id="type-name">信用等级</span>
				<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
			</div>
			<div class="gray-box creditb-level">
				<div class="credit-title clearfix">
					<div class="credit-item-left">信用等级</div>					
					<div class="credit-item-right"><span>评定标准区间 （信用兑付率+订购履约率）</span></div>	
				</div>							
				<div class="credit-second clearfix">
					<div class="credit-item-left">AAA</div>					
					<div class="credit-item-right"><span>90%+</span></div>	
				</div>
				<div class="credit-second clearfix">
					<div class="credit-item-left">AA</div>					
					<div class="credit-item-right"><span>85%+</span></div>	
				</div>
				<div class="credit-second clearfix">
					<div class="credit-item-left">A</div>					
					<div class="credit-item-right"><span>80%+</span></div>	
				</div>
				<div class="credit-second clearfix">
					<div class="credit-item-left">BBB</div>					
					<div class="credit-item-right"><span>75%+</span></div>	
				</div>
				<div class="credit-second clearfix">
					<div class="credit-item-left">BB</div>					
					<div class="credit-item-right"><span>70%+</span></div>	
				</div>
				<div class="credit-second clearfix">
					<div class="credit-item-left">B</div>
					<div class="credit-item-right"><span>65%+</span></div>	
				</div>
				<div class="credit-second clearfix">
					<div class="credit-item-left">CCC</div>
					<div class="credit-item-right"><span>60%+</span></div>	
				</div>
				<div class="credit-second clearfix">
					<div class="credit-item-left">CC</div>
					<div class="credit-item-right"><span>55%+</span></div>	
				</div>
				<div class="credit-second clearfix">
					<div class="credit-item-left">C</div>
					<div class="credit-item-right"><span>50%+</span></div>	
				</div>
				<div class="credit-second clearfix">
					<div class="credit-item-left">DDD</div>
					<div class="credit-item-right"><span>40%+</span></div>	
				</div>
				<div class="credit-second clearfix">
					<div class="credit-item-left">DD</div>
					<div class="credit-item-right"><span>30%+</span></div>	
				</div>
				<div class="credit-second last clearfix">
					<div class="credit-item-left">D</div>
					<div class="credit-item-right"><span>20%+</span></div>	
				</div>
			</div>
			<div id="zj-detail"></div>
		</div>
		</div>
	</div><!--//section-->	

	<?php include "../com/footer.php"; ?>
<script id="zj-detail-tmpl" type="text/html">
<div class="fwb-level gray-box"><!--[== content || '暂无富文本数据']--></div>
</script>
<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use("../content/js/personal/main");
	seajs.use("../content/js/personal/my-creditb-level");
</script>
</body>
</html>
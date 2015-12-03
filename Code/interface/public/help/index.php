<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>帮助中心</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/help.css" />
</head>
<body>

	<?php include '../com/header.php'; ?>

	<div class="section">
		<div class="page-vertical">
			<ul class="help-nav">
				<li><a class="icon-service1" href="self-service.html"></a></li>
				<li><a class="icon-service2" href="common-page.html?type=service"></a></li>
				<li><a class="icon-service3" href="people-service.html"></a></li>
				<li><a class="icon-demo" href="common-page.html?type=newhand"></a></li>
				<li><a class="icon-guest" href="common-page.html?type=guide"></a></li>
				<li><a class="icon-about" href="common-page.html?type=about"></a></li>
			</ul>
		</div>
	</div><!--//section-->

	<?php include '../com/footer.php'; ?>

	<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
	<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
	<script>
		seajs.use('../content/js/help/index');
	</script>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>基建商城-新增地址</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/mall.css" />
</head>
<body>

<?php include '../com/header.php'; ?>

<div class="section">
	<div class="page-vertical clearfix">
		<div class="breadcrumbs">
			<a href="../mall/">首页</a> &gt; <a href="index.html">基建商城</a> &gt; <span>新增收货地址</span>
			<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
		</div>
		<div class="address-add">
			<form class="form address-form" id="address-form">
				<div class="form-item">
					<div class="form-label">输入收货人姓名：</div>
					<div class="form-value"><input type="text" /></div>
				</div>
				<div class="form-item">
					<div class="form-label">收货地址：</div>
					<div class="form-value"><input type="text" /></div>
				</div>
				<div class="form-item">
					<div class="form-label">联系方式：</div>
					<div class="form-value"><input type="text" /></div>
				</div>
				<div class="form-item">
					<div class="form-label">地址标签：</div>
					<div class="form-value form-radio-group">
						<label><input type="radio" name="address_label" class="radio no_margin" checked />家庭地址</label>
						<label><input type="radio" name="address_label" class="radio" />工地</label>
						<label><input type="radio" name="address_label" class="radio" />办公室</label>
						<label><input type="radio" name="address_label" class="radio" />其他</label>
					</div>
				</div>
				<div class="form-buttons">
					<input class="btn btn-submit" type="submit" value="下一步" />
				</div>
			</form>
		</div>
	</div>
</div><!--//section-->

<div class="footer">
	<div class="page">
		
	</div>
</div><!--//footer-->
<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	//seajs.use('../content/js/mall/detail');
</script>
</body>
</html>
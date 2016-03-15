<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>我的收获地址-个人中心</title>
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
			<a href="../mall/">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <span id="type-name">收货地址</span>
			<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
		</div>
			<ul class="address-list" id="myAddressData"></ul>
			<div class="add-addres">
				<button class="button btn-secondary big" id="deleteAddress">编辑</button>
				<button class="button btn-secondary big" onclick="location.href='add-address.html'">新增</button>
			</div>
		</div>
	</div>
</div>
<!--//section-->

<?php include '../com/footer.php'; ?>
<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script id="myAddressTmpl" type="text/html">
<!--[for(i=0;i<list.length;i++){]-->
	<li class="gray-box clearfix">
		<div class="li-left">
			<div class="address-edit icon-tag">
				<div class="address-text"><!--[=list[i].tag]--></div>
				<button class="icon-pencil editAddress" data-id="<!--[=list[i].id]-->">编辑</button>
			</div>
			<div class="address-info">
				<div>
					<span class="info-label">收货人姓名：</span>
					<div><!--[=list[i].name]--></div>
				</div>
				<div>
					<span class="info-label">收货地址：</span>
					<div><!--[=list[i].address]--></div>
				</div>
				<div>
					<span class="info-label">联系方式：</span>
					<div><!--[=list[i].phone]--></div>
				</div>
			</div>
		</div>
		<a class="green-btn li-right default <!--[=list[i].default==1?'active':'']-->" data-id="<!--[=list[i].id]-->" href="javascript:;"></a>
		<a class="green-btn li-right delete" data-id="<!--[=list[i].id]-->" href="javascript:;"></a>
	</li>
<!--[}]-->
</script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/personal/my-address');
</script>

</body>
</html>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>收获地址管理-个人中心</title>
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
				<a href="../">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <span id="type-name">添加收货地址</span>
				<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
			</div>
			<div class="add-address gray-box">
	    		<form class="form address-form" id="saveAddressForm">
	    			<div class="form-item">
	    				<div class="form-label"><span>*</span>输入收货人姓名：</div>
	    				<div class="form-value"><input type="text" name="name" maxlength="20" /></div>
	    			</div>
	    			<div class="form-item">
	    				<div class="form-label"><span>*</span>收货地址：</div>
	    				<div class="form-value"><input type="text" name="address" maxlength="80" /></div>
	    			</div>
	    			<div class="form-item">
	    				<div class="form-label"><span>*</span>联系方式：</div>
	    				<div class="form-value"><input type="text" name="phone" maxlength="15" /></div>
	    			</div>
	    			<div class="form-item">
	    				<div class="form-label"><span>*</span>地址标签：</div>
	    				<div class="form-value form-radio-group">
	    					<label><input type="radio" name="tag-type" value="工地地址" />工地地址</label>
	    					<label><input type="radio" name="tag-type" value="办公地址" />办公地址</label>
	    					<label><input type="radio" name="tag-type" value="" />其他</label>
	    					<label><input type="radio" name="tag-type" value="家庭地址" checked />家庭地址</label>
	    				</div>
	    			</div>
	    			<div class="form-item">
	    				<div class="form-label">&nbsp;</div>
	    				<div class="form-value">
		    				<input class="other-input" type="text" readonly="true" />
		    				<input type="hidden" name="tag" value="家庭地址" maxlength="10" />
		    			</div>
	    			</div>
	    			<div class="form-buttons">
	    				<input type="hidden" name="id"/>
	    				<input class="button btn-secondary big" type="submit" value="保存" />
	    			</div>
	    		</form>
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
	seajs.use('../content/js/personal/my-address');
</script>

</body>
</html>
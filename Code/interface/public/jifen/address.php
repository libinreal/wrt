<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>收货地址-积分商城</title>
    <link rel="stylesheet" href="../content/css/common.css" />
    <link rel="stylesheet" href="../content/css/jifen.css" />
</head>
<body>

	<?php include '../com/header.php'; ?>

    <div class="section clearfix">
	    <div class="page-vertical">
			<div class="breadcrumbs">
				<a href="../">首页</a> &gt; <a href="index.html">积分商城</a> &gt; <span>收货地址</span>
				<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
			</div>
	    	<div class="address-list" style="display: none;">
	    		<div id="zj-list"></div>
				<div class="address-button">
					<a class="button btn-secondary big" href="address-add.html" id="add-address-btn">新增</a>
					<a class="button btn-secondary big" href="#" id="next-btn" style="margin-left: 15px;">下一步</a>
				</div>
	    	</div>
	    	<div class="address-add" style="display: none;">
	    		<form class="form address-form" id="address-form">
	    			<div class="form-item">
	    				<div class="form-label">输入收货人姓名：</div>
	    				<div class="form-value"><input type="text" name="name" maxlength="20" /></div>
	    			</div>
	    			<div class="form-item">
	    				<div class="form-label">收货地址：</div>
	    				<div class="form-value"><input type="text" name="address" maxlength="80" /></div>
	    			</div>
	    			<div class="form-item">
	    				<div class="form-label">联系方式：</div>
	    				<div class="form-value"><input type="text" name="phone" maxlength="15" /></div>
	    			</div>
	    			<div class="form-item">
	    				<div class="form-label">地址标签：</div>
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
	    				<input class="button btn-secondary big" type="submit" value="下一步" />
	    			</div>
	    		</form>
	    	</div>
		</div>
    </div><!--//section-->
    
    <?php include '../com/footer.php'; ?>

<script id="zj-list-tmpl" type="text/html">
<!--[for(i=0;i<list.length;i++){]-->
	<div class="address-item clearfix" data-id="<!--[=list[i].id]-->">
		<div class="address-name">
			<i></i>
			<span><!--[=list[i].tag]--></span>
			<a class="button btn-gray" href="address.html?id=<!--[=list[i].id]-->">编辑</a>
		</div>
		<ul class="address-detail">
			<li><span class="address-label">收货人姓名：</span><!--[=list[i].name]--></li>
			<li><span class="address-label">收货地址：</span><!--[=list[i].address]--></li>
			<li><span class="address-label">联系方式：</span><!--[=list[i].phone]--></li>
		</ul>
		<a class="address-operate <!--[=(list[i].default==1?'active':'')]-->" data-id="<!--[=list[i].id]-->" href="#"></a>
	</div>
<!--[}]-->
</script>
<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/jifen/address');
</script>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>申请自有授信-个人中心</title>
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
				<a href="../">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <a href="my-credit.html">自有授信</a> &gt; <span id="type-name">申请自有授信</span>
				<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
			</div>
			<div class="add-address gray-box">
	    		<form class="form address-form" id="saveCreditForm">
	    			<div class="form-item">
	    				<div class="form-label"><span>*</span>申请额度：</div>
	    				<div class="form-value"><input type="text" name="apply_amount" maxlength="20" /></div>
	    			</div>
	    			<div class="form-item">
	    				<div class="form-label"><span>*</span>用途：</div>
	    				<div class="form-value"><select name="contract_id" id="contract_list"></select></div>
	    			</div>
	    			<div class="form-item">
	    				<div class="form-label"><span>*</span>备注：</div>
	    				<div class="form-value"><textarea name="apply_remark" style="margin: 0px; width: 405px; height: 97px;"></textarea></div>
	    			</div>
	    			<div class="form-item">
	    				<div class="form-label"><span>*</span>申请附件：</div>
	    				<div class="form-value">
	    					<input type="file" id="uploadFile" style="width:337px;border:none;">
	    					<input type="hidden" name="img" id="apply_img"/>
	    				</div>
	    			</div>
	    			
	    			<div class="form-buttons">
	    				<input type="hidden" name="status" value="0">
	    				<input class="button btn-secondary big" type="submit" value="保存" />
	    			</div>
	    		</form>
			</div>

		</div>
	</div>
</div>
<!--//section-->

<script id="contract-list-tmpl" type="text/html">
	<option value="">请选择</option>
	<!--[for(i = 0; i < list.length; i ++) {]-->
	<option value="<!--[= list[i].contract_id || '--']-->"><!--[= list[i].contract_name || '--']--></option>
	<!--[}]-->
</script>

<?php include '../com/footer.php'; ?>
<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/personal/my-creadit-add');
</script>

</body>
</html>
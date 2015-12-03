<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>信用评测状态-信用池</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/credit.css" />
</head>
<body>

	<?php include '../com/header.php'; ?>

	<div class="section credit credit-status">
		<div class="breadcrumbs credit-test-header">
				<a href="../">首页</a> &gt; <a href="index.html">信用池</a> &gt; <span id="type-name" >信用评测状态</span>
				<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
		</div>	
		<div class="page-vertical" id="zj-detail"></div>			
	</div><!--//section-->

	<?php include '../com/footer.php'; ?>

	<script id="zj-detail-tmpl" type="text/html">
	    <div class="credit-box credit-info credit-progress">
	    	<ul class="clearfix status-<!--[= status]-->">
	    		<li class="<!--[= (status >= 1 ? 'active' : '')]-->"><i>&nbsp;</i>资料已提交</li>
	    		<li class="<!--[= (status >= 2 ? 'active' : '')]-->"><i>&nbsp;</i>受理成功</li>
	    		<li class="<!--[= (status >= 3 ? 'active' : '')]-->"><i>&nbsp;</i>审核通过</li>
	    		<li class="<!--[= (status >= 4 ? 'active' : '')]-->"><i>&nbsp;</i><!--[= (status == 5 ? '失败' : '成功')]--></li>
	    	</ul>
	    	<div class="credit-progress-bg"></div>
	    </div>
	    <form class="form credit-box credit-info">
	    	<div class="form-item">
	    		<div class="form-label"><span>*</span>用户名：</div>
	    		<div class="form-value" id="account"></div>
	    	</div>
	    	<div class="form-item">
	    		<div class="form-label"><span>*</span>第一联系人：</div>
	    		<div class="form-value form-value1" id="contacts"></div>
	    		<div class="form-label form-label2"><span>*</span>第二联系人：</div>
	    		<div class="form-value form-value2" id="secondContacts"></div>
	    	</div>
	    	<div class="form-item">
	    		<div class="form-label"><span>*</span>第一联系人手机号：</div>
	    		<div class="form-value form-value1" id="telephone"></div>
	    		<div class="form-label form-label2"><span>*</span>第二联系人手机号：</div>
	    		<div class="form-value form-value2" id="secondPhone"></div>
	    	</div>
	    	<div class="form-item">
	    		<div class="form-label"><span>*</span>单位名称：</div>
	    		<div class="form-value" id="companyName"></div>
	    	</div>
	    	<div class="form-item">
	    		<div class="form-label"><span>*</span>单位地址：</div>
	    		<div class="form-value" id="companyAddress"></div>
	    	</div>
	    </form>
	    <div class="form credit-box credit-info">
	    	<div class="form-item">
	    		<div class="form-label"><span>*</span>公司注册资本：</div>
	    		<div class="form-value"><!--[= money]--></div>
	    	</div>
	    	<div class="form-item">
	    		<div class="form-label"><span>*</span>公司成立时间：</div>
	    		<div class="form-value"><!--[= foundedDate]--></div>
	    	</div>
	    	<div class="form-item">
	    		<div class="form-label"><span>*</span>公司性质：</div>
	    		<div class="form-value form-radio-group"><!--[= $checkNatural(natural)]--></div>
	    	</div>
	    	<div class="form-item">
	    		<div class="form-label"><span>*</span>拟授信额度（万/人名币）：</div>
	    		<div class="form-value"><!--[= $formatCurrency1(amountLimit)]--></div>
	    	</div>
	    	<div class="form-item">
	    		<div class="form-label"><span>*</span>拟授信用途附加说明：</div>
	    		<div class="form-value"><!--[= use]--></div>
	    	</div>
	    </div>
	    <div class="form credit-box credit-info">
	    	<div class="form-item">
	    		<div class="form-label"><span>*</span>营业执照编码：</div>
	    		<div class="form-value"><!--[= businessCode]--></div>
	    	</div>
	    	<div class="form-item">
	    		<div class="form-label"><span>*</span>税务登记证编码：</div>
	    		<div class="form-value"><!--[= taxCode]--></div>
	    	</div>
	    	<div class="form-item">
	    		<div class="form-label"><span>*</span>组织机构代码：</div>
	    		<div class="form-value"><!--[= orgCode]--></div>
	    	</div>
	    </div>
	</script>

	<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
	<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
	<script>
		seajs.use('../content/js/credit/status');
	</script>
</body>
</html>
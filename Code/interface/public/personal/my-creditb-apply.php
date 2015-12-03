<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>采购额度追加申请-我的信用B</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/personal.css" />
</head>
<body>

<?php include "../com/header.php"; ?>

	<div class="section content-left-bg">
		<div class="page-vertical clearfix">
			<?php include '../com/nav-left.php'; ?>
			<div class="content-right">
			<div class="breadcrumbs">
				<a href="../">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <a href="my-creditb.html">我的信用B</a> &gt; <span id="type-name">采购额度追加申请</span>
				<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
			</div>
				<form class="form creditb-amount clearfix" id="apply-form">
		    		<div class="form-item">
		    			<div class="form-label t-r" width="25%"><em>*</em>合同编号：</div>
		    			<div class="form-value t-d">
			    			<select name="contractNo" id="zj-contracts">
			    				<option value="">请选择合同编号</option>
			    			</select>
		    			</div>
		    		</div>
		    		<div class="form-item">
		    			<div class="form-label t-r" width="25%"><em>*</em>联系人：</div>
		    			<div class="form-value t-d"><input type="text" size="50" name="name" placeholder="请填入联系人"/></div>	    				 			 
		    		</div>
		    		<div class="form-item">
		    			<div class="form-label t-r" width="25%"><em>*</em>联系电话：</div>
		    			<div class="form-value t-d"><input type="text" size="50" name="phone" placeholder="请填入联系电话"/></div>	    				 			 
		    		</div>
		    		<div class="form-item">
		    			<div class="form-label t-r" width="25%"><em>*</em>申请增加采购额度：</div>
		    			<div class="form-value t-d"><input type="text" size="50" name="amount" placeholder="请填入申请增加额度金额"/></div>	   				    												 				 			 
		    		</div>
		    		<div class="form-item">
		    			<div class="form-label t-r" width="25%"><em>*</em>申请理由：</div>
		    			<div class="form-value t-d"><textarea cols="52" rows="3" name="reason" placeholder="请输入申请理由..."></textarea></div> 					    								    				   				    												 				 			 
		    		</div>					    		
				   	<div class="form-buttons last_div"><input type="submit" class="button btn-secondary"/></div> 																				
				</form>
		   </div>
		</div>
	</div><!--//section-->	

<?php include "../com/footer.php"; ?>
<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use("../content/js/personal/my-creditb-apply");
</script>
</body>
</html>
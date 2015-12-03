<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>信用追加-信用池</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/credit.css" />
</head>
<body>

	<?php include '../com/header.php'; ?>

	<div class="section content-left-bg">
		<div class="page-vertical clearfix">
			<div class="content-left">
				<a href="history.html">信用评测</a>
				<a href="manage.html">信用管理</a>
				<a href="additional.html" class="active">信用追加</a>
				<a href="more.html">了解更多</a>
			</div>
			<div class="content-right">
				<div class="breadcrumbs">
						<a href="../">首页</a> &gt; <a href="index.html">信用池</a> &gt; <span id="type-name">信用追加</span>
						<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
				</div>	
				<form class="form additional-form credit-bg" id="additional-form">
					<div class="form-item">
						<div class="form-label"><span>*</span>合同编号：</div>
						<div class="form-value">
							<div class="zj-select">
								<em><i></i></em>
								<span class="default">请选择合同编号</span>
								<ul class="dropdown" id="zj-contracts">
									<li data-v="">请选择合同编号</li>
								</ul>
								<input type="hidden" name="contractNo" />
							</div>
						</div>
					</div>
					<div class="form-item">
						<div class="form-label"><span>*</span>联系人：</div>
						<div class="form-value"><input type="text" name="name" maxlength="15" placeholder="请填入联系人" /></div>
					</div>
					<div class="form-item">
						<div class="form-label"><span>*</span>联系电话：</div>
						<div class="form-value"><input type="text" name="phone" maxlength="13" placeholder="请填入联系电话" /></div>
					</div>
					<div class="form-item">
						<div class="form-label"><span>*</span>申请增加信用额度：</div>
						<div class="form-value"><input type="text" name="amount" maxlength="15" placeholder="请填入申请增加额度金额" /></div>
					</div>
					<div class="form-item">
						<div class="form-label"><span>*</span>申请理由</div>
						<div class="form-value"><textarea name="reason" placeholder="请填入申请理由..."></textarea></div>
					</div>
					<div class="form-buttons">
						<input type="submit" class="button btn-secondary big" value="提交" />
					</div>
				</form>
			</div>
		</div>
	</div><!--//section-->

	<?php include '../com/footer.php'; ?>

	<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
	<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
	<script>
		seajs.use('../content/js/credit/additional');
	</script>
</body>
</html>
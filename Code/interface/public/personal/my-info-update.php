<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>修改信息-个人中心</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/personal.css" />
</head>
<body>

<?php include '../com/header.php'; ?>

	<div class="section">
		<div class="page-vertical">
			<div class="breadcrumbs">
				<a href="../">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <span id="type-name" >修改用户信息</span>
				<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
			</div>
			<div class="update-info gray-box">
			<form id="updateInfoForm" class="form update-info-form">
				<div class="form-top">
					<div class="form-item">
						<div class="form-label">用户名：</div>
						<div class="form-value">
							<div class="form-span"><span id="username">&nbsp;</span></div>
						</div>
					</div>
					<div class="form-item">
						<div class="form-label">联系人手机号：</div>
						<div class="form-value">
							<div class="form-span">
								<span id="mobile">&nbsp;</span><a 
								class="go-safe" href="my-security.html">在安全中心修改绑定手机号</a>
							</div>
						</div>
					</div>
				</div>
				<div class="form-center">
					<div class="form-item form-item-2">
						<div class="form-label"><span>*</span>联系人：</div>
						<div class="form-value">
							<input type="text" name="contacts" maxlength="50" />
						</div>
						<div class="form-label form-label-2">性别：</div>
						<div class="form-value form-radio-group">
							<label><input type="radio" name="gender" value="0"><span>男</span></label>
							<label><input type="radio" name="gender" value="1"><span>女</span></label>
						</div>
					</div>
					<div class="form-item form-item-2">
						<div class="form-label">第二联系人：</div>
						<div class="form-value">
							<input type="text" name="secondContacts" maxlength="50" />
						</div>
						<div class="form-label form-label-2 sm">第二联系人<br/>手机号：</div>
						<div class="form-value">
							<input type="text" name="secondPhone" maxlength="50" />
						</div>
					</div>
					<div class="form-item">
						<div class="form-label">邮箱：</div>
						<div class="form-value">
							<input type="text" name="email" maxlength="50" />
						</div>
					</div>
					<div class="form-item">
						<div class="form-label"><span>*</span>职位：</div>
						<div class="form-value">
							<input type="text" name="position" maxlength="50" />
						</div>
					</div>
					<div class="form-item">
						<div class="form-label"><span>*</span>单位名称：</div>
						<div class="form-value">
							<input type="text" name="companyName" maxlength="50" />
						</div>
					</div>
					<div class="form-item">
						<div class="form-label"><span>*</span>单位地址：</div>
						<div class="form-value">
							<input type="text" name="companyAddress" maxlength="200" />
						</div>
					</div>
					<div class="form-item">
						<div class="form-label"><span>*</span>办公电话：</div>
						<div class="form-value">
							<input type="text" name="officePhone" maxlength="50" />
						</div>
					</div>
					<div class="form-item">
						<div class="form-label"><span>*</span>单位传真：</div>
						<div class="form-value">
							<input type="text" name="fax" maxlength="50" />
						</div>
					</div>
					<div class="form-item form-item-2">
						<div class="form-label">QQ号：</div>
						<div class="form-value">
							<input type="text" name="qq" maxlength="50" />
						</div>
						<div class="form-label form-label-2">微信号：</div>
						<div class="form-value">
							<input type="text" name="weixin" maxlength="50" />
						</div>
					</div>
					<div class="form-item" >
						<div class="form-label"><span>*</span>所在部门：</div>
						<div class="form-value">
							<input type="text" name="department" maxlength="50" />
						</div>
					</div>
				</div>
				<div class="form-bottom">
					<input type="submit" class="button btn-secondary big" value="保存">
				</div>
			</form>
			</div>
		</div>
	</div><!--//section-->

<?php include '../com/footer.php'; ?>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/my-info-update');
</script>

</body>
</html>
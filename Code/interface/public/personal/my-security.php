<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>安全中心-个人中心</title>
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
				<a href="../">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <span id="type-name" >安全中心</span>
				<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
			</div>	
			<div class="safe-center gray-box">
				<div class="box-title"><sapn>密码修改</sapn></div>
				<form id="modifyPwdForm" class="form form-security">
					<div class="form-item">
						<div class="form-label"><span>*</span>原始密码：</div>
						<div class="form-value">
							<input name="oldpwd" type="password" maxlength="50" placeholder="" />
						</div>
					</div>
					<div class="form-item">
						<div class="form-label"><span>*</span>输入密码：</div>
						<div class="form-value">
							<input name="newpwd_one" type="password" maxlength="50" placeholder="" />
						</div>
					</div>
					<div class="form-item">
						<div class="form-label"><span>*</span>确认密码：</div>
						<div class="form-value">
							<input name="newpwd" type="password" maxlength="50" placeholder="" />
						</div>
					</div>
					<div class="form-buttons">
						<input type="submit" class="button btn-secondary big" value="保存" />
					</div>
				</form>
			</div>
			
			<div class="safe-center gray-box">
				<div class="box-title"><sapn>手机号变更</sapn></div>
				<form id="modifyPhoneForm" class="form form-security clearfix">
					<div class="form-item">
						<div class="form-label"><span>*</span>新手机号：</div>
						<div class="form-value">
							<input name="mobile" type="text" maxlength="11" placeholder="" />
						</div>
					</div>
					<div class="form-item">
						<div class="form-label"><span>*</span>验证码：</div>
						<div class="form-value">
							<input id="vcode" name="vcode" type="text" maxlength="6" placeholder="" /><button 
								id="sendMsg1" class="button btn-secondary send-sms-btn">发送验证码</button>
							<div class="msg-box" for="vcode"></div>
							<div class="vcode-tip">
								<span>验证码有效期10分钟</span>
							</div>
						</div>
					</div>
					<div class="form-buttons">
						<input type="hidden" name="step" value="1"/>
						<input type="submit" class="button btn-secondary big" value="保存" />
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
	seajs.use('../content/js/personal/my-security');
</script>

</body>
</html>
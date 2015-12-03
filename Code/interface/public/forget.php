<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<link rel="stylesheet" href="content/css/common.css">
	<link rel="stylesheet" href="content/css/login-register.css">
</head>
<body>
	<div class="modal">
		<div class="modal-bg">
			<div class="modal-head forget-modal-head">
				<h5 class="modal-title">找回密码</h5>
				<span class="modal-close">X</span>
			</div>
			<div class="modal-content">
				<div class="forget-border-in">
					<form id="forget-form" class="form">
						<div class="forget-phone">
							<label>手机号：</label>
							<input type="text" name="mobile" placeholder="请输入手机号">
						</div>
						<div class="forget-vcode">
							<label>验证码：</label>
							<input type="text" name="verifyCode" placeholder="请输入验证码">
						</div>
						<div class="hr">
						</div>
						<div class="forget-btn">
							<input type="button" id="sendMsg" value="发送验证码">
							<label></label>
							<input type="submit" value="提交">
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div id="myPanel" class="panel">
        <div class="panel-content">
            <div class="panel-cell">
                <h3 class="panel-title">提示</h3>
                <p class="panel-text"></p>
            </div>
        </div>
        <div class="panel-buttons">
            <div class="options">
                <a href="javascript:;" class="btn btn-ok">确定</a>
                <a href="javascript:;" class="btn btn-cancel">取消</a>
            </div>
            <div class="panel-tips">若在<span class="panel-tick">5</span>秒内无反应自动跳转到订单的详情页面</div>
        </div>
	</div>
	<div id="panelBg" class="panel-bg"></div>
</body>
<script src="content/js/module/seajs/2.2.0/sea.js"></script>
<script src="content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('./content/js/login-register/forget');
</script>
</html>
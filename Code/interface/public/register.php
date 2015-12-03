<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<link rel="stylesheet" href="content/css/common.css">
	<link rel="stylesheet" href="content/css/login-register.css">
</head>
<body>
	<div class="register-head">注册</div>
	<div class="page-vertical">
		<div class="register">
			<div class="register-border">
				<form id="register-form">
					<div class="form-horizontal">
						<label><span>*</span>用户名：</label>
						<input type="text" name="account" id="username" placeholder="请输入用户名,不能少于3个字符">
					</div>

					<div class="form-horizontal">
						<label><span>*</span>密码：</label>
						<input type="password" name="pwd_one" placeholder="请输入密码，不能少于6个字符">
					</div>
					<div class="form-horizontal">
						<label><span>*</span>确认密码：</label>
						<input type="password" name="password"  placeholder="请再次输入密码">
					</div>
					<div class="form-horizontal">
						<label>邮箱：</label>
						<input type="text" name="email"  placeholder="请输入邮箱账号">
					</div>
					<div class="form-horizontal">
						<label><span>*</span>联系人：</label>
						<input type="text" name="contacts"  placeholder="请输入您的名字">
					</div>

					<div class="form-horizontal form-radio clearfix">
						<label>性别：</label>
						<label><input type="radio" name="gender" value="0" checked="checked" />男</label>
						<label><input type="radio" name="gender" value="1" />女</label>
					</div>
					
					<div class="form-horizontal input-sm">
						<label><span>*</span>联系人手机号码：</label>
						<input type="text" name="telephone" maxlength="11" id="mobile">
						<button class="green-btn"id="sendMsg">获取验证码</button>
					</div>
					<div class="form-horizontal">
						<label><span>*</span>手机验证码：</label>
						<input type="text" name="vcode"  placeholder="验证码有效期10分钟">
					</div>
					<div class="form-horizontal">
						<label>第二联系人：</label>
						<input type="text" name="secondContacts" placeholder="请输入备用联系人">
					</div>
					<div class="form-horizontal">
						<label>第二联系人手机号码：</label>
						<input type="text" name="secondPhone">
					</div>
					<div class="form-horizontal">
						<label>QQ：</label>
						<input type="text" name="qq"  placeholder="您填写后，我们会为您提供专属增值服务">
					</div>
					<div class="form-horizontal">
						<label>微信：</label>
						<input type="text" name="weixin"  placeholder="您填写后，我们会为您提供专属增值服务">
					</div>

					<div class="hr"></div>
					
					<div class="form-horizontal">
						<label><span>*</span>单位名称：</label>
						<input type="text" name="companyName"  placeholder="请输入单位名称全称">
					</div>
					<div class="form-horizontal">
						<label><span>*</span>单位地址：</label>
						<input type="text" name="companyAddress"  placeholder="请输入单位联系地址">
					</div>
					<div class="form-horizontal">
						<label><span>*</span>办公电话：</label>
						<input type="text" name="officePhone">
					</div>
					<div class="form-horizontal">
						<label><span>*</span>办公传真：</label>
						<input type="text" name="fax">
					</div>
					<div class="form-horizontal">
						<label><span>*</span>职位：</label>
						<input type="text" name="position"  placeholder="请告知您的职位">
					</div>
					<div class="form-horizontal">
						<label><span>*</span>所在部门：</label>
						<input type="text" name="department"  placeholder="请告知您的所在部门">
					</div>
					<div class="hr"></div>

					<div class="register-btn">
						<input class="button btn-secondary" type="submit" id="submitBtn" value="注册并保存" />
						<input class="button btn-secondary" type="button" onclick="javascript:history.go(-1);" value="返回" />
					</div>
				</form>
			</div>
		</div>
	</div>
    <div id="zj-panel" class="panel">
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
        </div>
    </div>
	<div id="zj-panel-bg" class="panel-bg"></div>
</body>
<script src="content/js/module/seajs/2.2.0/sea.js"></script>
<script src="content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('./content/js/login-register/register');
</script>
</html>
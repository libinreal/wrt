<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>我的信息-个人中心</title>
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
				<a href="../mall/">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <span id="type-name">我的信息</span>
				<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
			</div>	
			<form id="uploadIconForm" class="upload-user-icon gray-box clearfix">
				<div class="show-icon">
					<img alt="" class="user-icon" src="../content/images/common/blank.png" />
				</div>
				<div class="upload-img">
					<div class="button btn-gray upload-img-file">选择你要上传的文件
						<input type="file" />
						<input type="hidden" name="icon" />
					</div>
					<div class="upload-img-text">仅支持JPG、PNG、JPEG格式文件小于4M</div>
					<a class="button btn-gray edit-info" href="my-info-update.html">编辑</a>
				</div>
				<div class="suggest-icon">
					<div>推荐头像：</div>
					<ul id="icons" class="clearfix">
						<li>
							<img src="../content/images/personal/icon_head1.png" data-icon="icon01" height="78" width="78" />
						</li>
						<li>
							<img src="../content/images/personal/icon_head2.png" data-icon="icon02" height="78" width="78" />
						</li>
						<li>
							<img src="../content/images/personal/icon_head3.png" data-icon="icon03" height="78" width="78" />
						</li>
						<li>
							<img src="../content/images/personal/icon_head4.png" data-icon="icon04" height="78" width="78" />
						</li>
						<li>
							<img src="../content/images/personal/icon_head5.png" data-icon="icon05" height="78" width="78" />
						</li>
					</ul>
					<div>
						<input class="button btn-secondary" type="submit" value="保存">
					</div>
				</div>
			</form>
			<div id="user-data" class="personal-info gray-box"></div>
		</div>
	</div>
</div>
<!--//section-->

<?php include '../com/footer.php'; ?>
<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script id="user-tmpl" type="text/html">
	<div class="personal-item">
		<div class="personal-info-label">用户名：</div>
		<div class="personal-info-value"><!--[= account]--></div>
		<div class="personal-info-label">姓名：</div>
		<div class="personal-info-value"><!--[=contacts]--></div>
	</div>
	<div class="personal-item">
		<div class="personal-info-label">性别：</div>
		<div class="personal-info-value"><!--[=(gender=='0'?'男':'女')]--></div>
		<div class="personal-info-label">联系电话：</div>
		<div class="personal-info-value"><!--[=telephone]--></div>
	</div>
	<div class="personal-item">
		<div class="personal-info-label">公司名称：</div>
		<div class="personal-info-value"><!--[=companyName]--></div>
		<div class="personal-info-label">所在部门：</div>
		<div class="personal-info-value"><!--[=department]--></div>
	</div>
	<div class="personal-item">
		<div class="personal-info-label">职位：</div>
		<div class="personal-info-value"><!--[=position]--></div>
		<div class="personal-info-label">微信号：</div>
		<div class="personal-info-value"><!--[=weixin]--></div>
	</div>
	<div class="personal-item">
		<div class="personal-info-label">单位地址：</div>
		<div class="personal-info-value"><!--[=companyAddress]--></div>
		<div class="personal-info-label">办公电话：</div>
		<div class="personal-info-value"><!--[=officePhone]--></div>
	</div>
	<div class="personal-item">
		<div class="personal-info-label">办公传真号：</div>
		<div class="personal-info-value"><!--[=fax]--></div>
		<div class="personal-info-label">邮箱：</div>
		<div class="personal-info-value"><!--[=email]--></div>
	</div>
	<div class="personal-item">
		<div class="personal-info-label">第二联系人：</div>
		<div class="personal-info-value"><!--[=secondContacts || "--" ]--></div>
		<div class="personal-info-label">第二联系人手机号：</div>
		<div class="personal-info-value"><!--[=secondPhone || "--" ]--></div>
	</div>
	<div class="personal-item">
		<div class="personal-info-label">上级单位：</div>
		<div class="personal-info-value"><!--[=superiors || '--']--></div>
		<div class="personal-info-label">直属下级单位：</div>
		<div class="personal-info-value"><!--[=subordinate || '--']--></div>
	</div>
	<div class="personal-item">
		<div class="personal-info-label">会员类型：</div>
		<div class="personal-info-value"><!--[= $getRoleName(customLevel)]--></div>
		<div class="personal-info-label">会员权限：</div>
		<div class="personal-info-value"><!--[= $checkPermisson(customLevel)]--></div>
	</div>
	<div class="personal-item">
		<div class="personal-info-label">银行客户号：</div>
		<div class="personal-info-value">--</div>
		<div class="personal-info-label">所属银行：</div>
		<div class="personal-info-value">--</div>
	</div>
</script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/personal/my-info');
</script>

</body>
</html>
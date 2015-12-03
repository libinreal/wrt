<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>物融通</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/wrt.css" />
</head>
<body class="body-content">
	
	<?php include '../com/header.php'; ?>

	<div class="section">
		<div class='page-vertical wrt'>
		<div class="wrt_bg">
			<div class='wrt_log'>
				<ul>
				 	<li class='t'>登入物融通</li>
				 	<li class='method'>登入方式：</li>
				 	<li><input class='m_c' type='text' name='login_type' value='个性化账号登入' readonly="readonly" /></li>		 						 		 	 					 	
				 	<li class='name'>个性化账号：</li>	
				 	<li class='name_c'><input type='text' value="v00001"/></li>
				 	<li class='pwd'>物融通密码：</li>
				 	<li class='pwd_c'><input type='password' value="123456"/><span><a href='#'>忘记密码?</a></span></li>
				 	<li class='verify'>验证码：<span><a href='#'>看不清?&nbsp;&nbsp;换一张</a></span></li>
				 	<li class='verify_c'><input type='text' /><a href='#' class='verify_img'><img src='../content/images/wrt/img.gif' /></a></li>
				 	<li class='log_btn'><a class="button btn-secondary" href='index-in.html'>登录</a></li>
				 	<li class="bott"><a href="common-page.html#0" class="reg">在线注册 </a><a href="common-page.html#1" class="hp">使用帮助</a><a href="common-page.html#2">重要提示</a></li>
			 	</ul>
			 	<ul class='m_c_s'>
		 			<li data-v="个性化账号：">个性化账号登入</li>
		 			<li data-v="物融通账号：">物融通账号登入</li>
		 			<li data-v="企业营业执照字号：">企业营业执照字号登入</li>
			 	</ul>
			</div>
			</div>
		</div>
	</div><!--//section-->	

	<?php include '../com/footer.php'; ?>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/wrt/index');
</script>
</body>
</html>
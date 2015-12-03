<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>智能数据</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/znsj.css" />
</head>
<body>
<?php include "../com/header.php"; ?>
	<div class="section znsj">
	<div class="page-vertical">
		<div class="znsj-top clearfix">
			<div class="left scroll-img">
				<div class="items" id="zj-ad"></div>
				<div class="navi"></div>
			</div>
			<form class="right" id="znsj-form">
				<ul class="zform">
					<li class="t">登入数据智能平台</li>
					<li class="method">登入方式：</li>
					<li><input class="m_c" type="text" name="login_type" value="个性化账号登入" readonly="readonly" /></li>			 						 		 	 					 	
					<li class="name">个性化账号：</li>
					<li class="name_c"><input type="text" name="account" maxlength="50" /></li>
					<li class="pwd">密码：</li>
					<li class="pwd_c"><input type="password" name="password" maxlength="20" /><span><a href="#">忘记密码?</a></span></li>
					<li class="verify">验证码：<span><a href="#">看不清?&nbsp;&nbsp;换一张</a></span></li>
					<li class="verify_c"><input type="text" name="verifyCode" maxlength="6" /><a href="#" class="verify_img"></a></li>
					<li class="log_btn"><input type="submit" class="button btn" value="" /></li>
					<li class="bott"><a href="common-page.html#0" class="reg">在线注册 </a><a href="common-page.html#1" class="hp">使用帮助</a><a href="common-page.html#2">重要提示</a></li>
				</ul>
			 	<ul class='m_c_s'>
		 			<li data-v="个性化账号：">个性化账号登入</li>
		 			<li data-v="物融通账号：">物融通账号登入</li>
		 			<li data-v="企业营业执照字号：">企业营业执照字号登入</li>
			 	</ul>
			</form>
		</div>
		<div class="znsj-nav">信用数据采集与分析</div>
		<div class="content-1 extra">
			<a class="btn1 btn" href="../credit/apply.html">我的评测</a>
			<a class="btn2 btn" href="../personal/my-creditb.html">我的信用B</a>
		</div>
		<div class="content-2 extra">
			<a class="btn1" href="../project/bidding.html">更多>></a>
			<a class="btn2 btn" href="../project/bidding.html">工程招标</a>
			<a class="btn3" href="../project/business.html">更多>></a>
			<a class="btn4 btn" href="../project/business.html">商情中心</a>
		</div>
		<div class="content-3 extra">
			<a class="btn1" href="../personal/my-project.html">更多>></a>
			<a class="btn2 btn" href="../personal/my-project.html">我的项目</a>
			<a class="btn3" href="../personal/my-order.html">更多>></a>
			<a class="btn4 btn" href="../personal/my-order.html">我的订单</a>
		</div>
		</div>
	</div><!--//section-->	
<?php include "../com/footer.php"; ?>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use("../content/js/znsj/index");
</script>

</body>
</html>
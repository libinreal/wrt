<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>立即支付-物融通</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/personal.css" />
	<link rel="stylesheet" href="../content/css/personal-index.css" />
	<link rel="stylesheet" href="../content/css/wrt.css" />
	<link rel="stylesheet" href="../content/css/customize.css" />
</head>
<body>

<?php include "../com/header.php"; ?>

<div class="section customize">
	<div class="page-vertical" id="zj-detail">
		<div class="content-top clearfix">
			<div class="my-credit-info gray-box clearfix" style="height: auto;">
				<ul class="payfor-l">
					<li class="l-1">请您及时付款，以便订单尽快处理！订单编号：<span id="orderSn">--</span></li>
					<li class="l-2">请您在提交订单后24小时内完成支付，否则订单会自动取消。</li>
				</ul>
				<div class="payfor-r">
					<ul class="payfor-r-l">
						<li class="r-1">应付金额：</li>
						<li class="r-2"><span class="c-red" id="orderPrice">---</span> 信用B</li>
					</ul>
					<a href="../personal/my-order.html" id="dirOrder" class="button btn-gray">查看订单</a>
				</div>
			</div>
		</div>

		<div class="order-info gray-box payfor clearfix" style="margin-top: 14px">
			<div class="head-title" style="padding: 0 30px">
				<span>信用B支付：</span>
			</div>
			<div class="xy-hang clearfix">
				<span>已登录机构（实时扣减，实时到账，实时确认订单）</span>
				<a href="http://lc.talk99.cn/chat/chat/p.do?c=10033976&f=10043368&g=10048426" target="_blank">支付帮助</a>
			</div>
			<ul class="bank-info form-radio-group clearfix" style="padding: 0 0 20px;">
				<li>
					<label>
						<input type="radio" name="payOrgcode" value="07">
						<span class="huaxia">华夏银行</span>
					</label>
				</li>
				<li>
					<label class="active">
						<input type="radio" name="payOrgcode" value="01" checked>
						<span class="zheshang">浙商银行</span>
					</label>
				</li>
				<li>
					<label>
						<input type="radio" name="payOrgcode" value="02">
						<span class="gonghang">工商银行</span>
					</label>
				</li>
				<li>
					<label>
						<input type="radio" name="payOrgcode" value="04">
						<span class="pufa">浦发银行</span>
					</label>
				</li>
				<li>
					<label>
						<input type="radio" name="payOrgcode" value="05">
						<span class="zhongxin">中信银行</span>
					</label>
				</li>
				<li>
					<label>
						<input type="radio" name="payOrgcode" value="03">
						<span class="zhaohang">招商银行</span>
					</label>
				</li>
				<li>
					<label>
						<input type="radio" name="payOrgcode" value="06">
						<span class="zhongjiao">中交物融</span>
					</label>
				</li>
			</ul>
			<div class="lijizhifu clearfix">
				<a href="#" class="button btn-primary btn-zhifu">立即支付</a>
			</div>
		</div>
		
	</div>
</div>
<?php include "../com/footer.php"; ?>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/wrt/payfor');
</script>

</body>
</html>
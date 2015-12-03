<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>物融通</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/personal.css" />
	<link rel="stylesheet" href="../content/css/personal-index.css" />
	<link rel="stylesheet" href="../content/css/wrt.css" />
	<link rel="stylesheet" href="../content/css/customize.css" />
</head>
<body>

<?php include "../com/header.php"; ?>

<div class="section customize">
	<div class="page-vertical">
		<div class="content-top clearfix">
		<div class="my-credit-info gray-box clearfix" id="zj-summary"></div>
		<!--1-->
		<div class="content-top clearfix">
			<div class="wrt-auto-l">
				<div class="project-list gray-box" style="padding-bottom: 145px">
					<div class="project-t">
						<div class="project-l" style="margin-left:10px;"><span style="margin-left: 0;">信用B合同</span></div>
					</div>
					<div class="wrt-creditb-coon clearfix">
						<div class="wrt-creditb-l clearfix">
							<div class="wrt-qdht">签订新信用B合同</div>
							<ul>
								<li class="msg-point">提示：请准备好U-key进入信用B合同签订流程</li>
								<li><a href="https://115.238.51.98:465/B2BPAY" class="button btn-primary">建立新合同</a></li>
							</ul>
						</div>
						<div class="wrt-creditb-r" style="float:right">
							<div class="wrt-qdht">查看信用B合同记录</div>
							<ul>
								<li class="msg-point">若要查看账户的信用合同，请点击查询进入系统查看详情</li>
								<li><a href="https://115.238.51.98:465/B2BPAY" class="button btn-gray">点击查看</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<div class="scroll-img gray-box" style="float:right;height:auto">
				<div class="order-list gray-box">
					<div class="wrt-3 clearfix" style="padding: 17px 10px;font-size:17px;border-bottom:1px solid #E6E6E6">
						<div class="title-l">订单购销合同统计：</div>
						<div class="title-r margin" style="display: none;">共<span>26</span>个订单</div>
					</div>
					<div class="order-list-header clearfix" style="font-weight:normal">
						<div class="order-list-col c1">订单编号</div>
						<div class="order-list-col c2">项目编号</div>
						<div class="order-list-col c3">项目名称</div>
						<div class="order-list-col c4">验签状态</div>
						<div class="order-list-col c5"></div>
					</div>
					<div class="order-list-content" id="zj-list">
						<!-- <a href="wrt-order-confirm.html" class="clearfix">
							<div class="order-list-col c1">2014120393133</div>
							<div class="order-list-col c2">HT201411065</div>
							<div class="order-list-col c3">中铁二十一局</div>
							<div class="order-list-col c4 margin">订单未验签</div>
							<div class="order-list-col c5">
								<button class="button btn-gray">订单验签</button>
							</div>
						</a> -->
				    </div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include "../com/footer.php"; ?>

<script id="zj-summary-tmpl" type="text/html">
	<div class="m-c-i-left">
		<div class="m-c-i-info clearfix">
			<div class="show-icon">
				<a href="my-info.html"><img class="user-icon" alt="" src="<!--[= $getUserIcon(icon)]-->" /></a>
			</div>
			<div class="m-c-i-text">
				<div><span class="gray-span">信用等级：</span><!--[= cusLevel || '--']--></div>
				<div><span class="gray-span">客户号：</span><!--[= cusFnum || '--']--></div>
				<div title="<!--[= cusName]-->"><span class="gray-span">客户名：</span><!--[= cusName || '--']--></div>
			</div>
		</div>
		<div class="m-c-i-btns">
			<a class="button btn-gray"  href="../credit/more.html">了解信用B</a>
			<a class="button btn-gray"  href="../personal/my-address.html">收货地址</a>
			<a class="button btn-primary" href="javascript:;">安全等级：高</a>
		</div>
	</div>
	<table width="100%" cellpadding="0" cellspacing="0" class="m-c-i-center">
		<tr>
			<td class="gray-span">总信用额度：</td>
			<td class="money"><!--[= $formatCurrency1(creAmtTot)]-->RMB</td>
		</tr>
		<tr>
			<td class="gray-span">可用总信用额度：</td>
			<td><!--[= $formatCurrency1(lastAmtTot)]-->RMB</td>
		</tr>
		<tr>
			<td class="gray-span">已使用信用额度：</td>
			<td><!--[= $formatCurrency1(spendAmtTot)]-->RMB</td>
		</tr>
		<tr>
			<td class="gray-span">已恢复信用额度：</td>
			<td><!--[= $formatCurrency1(restoreAmtTot)]-->RMB</td>
		</tr>
	</table>
	<div class="m-c-i-right">
		<a class="button btn-yellow" href='../credit/additional.html'>追加信用额度</a>
		<a class="button btn-secondary" href='../personal/my-creditb-bill.html'>票据兑换到期提醒</a>
		<a class="button btn-cgdd" href='../personal/my-creditb-history.html'>使用流水历史记录</a>
	</div>
</script>

<script id="zj-list-tmpl" type="text/html">
    <!--[for(i = 0; i < list.length; i ++) {]-->
	<a href="../personal/my-order-detail.html?id=<!--[= list[i].id]-->" class="clearfix">
		<div class="order-list-col c1"><!--[= list[i].orderSn || '--']--></div>
		<div class="order-list-col c2"><!--[= list[i].prjNo || '--']--></div>
		<div class="order-list-col c3"><!--[= list[i].prjName || '--']--></div>
		<div class="order-list-col c4"><!--[= $getStatus(list[i].status)]--></div>
		<div class="order-list-col c5">
		<!--[if(list[i].status == -1) {]-->
			<a href="wrt-order-confirm.html?id=<!--[= list[i].id]-->" class="button btn-gray">订单验签</button>
		<!--[}]-->
		</div>
	</a>
    <!--[}]-->
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/wrt/index-in');
</script>

</body>
</html>
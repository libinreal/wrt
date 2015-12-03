<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>我的积分-个人中心</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/personal.css" />
</head>
<body>

<?php include "../com/header.php"; ?>

<div class="section content-left-bg">
	<div class="page-vertical clearfix">
	<?php include '../com/nav-left.php'; ?>
		<div class="content-right">
		<div class="breadcrumbs">
			<a href="../">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <span id="type-name">我的积分</span>
			<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
		</div>	
			<div class="score-info gray-box clearfix" id="user-data">
				<div class="show-icon">
					<a href="my-info.html"><img id="custom-icon" class="user-icon" alt="" src="../content/images/common/blank.png" /></a>
				</div>
				<div class="safe-level clearfix">
					<div class="safe-level-text">
						<span><label>信用等级</label><em id="credit-level"></em></span>
						<span class="gray-span">客户号：</span>
						<span class="gray-span" id="custom-no"><!--[=customNo]--></span>
						<span class="gray-span" style="margin-top: 5px;">客户名：</span>
						<span class="gray-span" id="account"><!--[=account]--></span>
					</div>
					<div class="safe-level-btns">
						<button class="button btn-primary">安全等级：高</button>
						<a class="button btn-gray" href="../help/common-page.html?type=guide&id=43">了解积分</a>
						<a class="button btn-gray" href="my-address.html">收货地址</a>
					</div>
				</div>
				<div class="score-totla">
					<div class="score-totla-text"><label>当前积分总额</label><span class="red-font" id="credits"><!--[=credits]--></span></div>
					<div>积分不够？马上去采购！</div>
					<div class="score-totla-btns">
						<a class="button btn-secondary" href="my-project.html">去采购</a><a 
						class="button btn-cgdd" href="my-order.html">查询采购订单</a>
					</div>
				</div>
			</div>
			<div class="score-list gray-box">
				<ul class="nav-tabs clearfix">
					<li><a href="#exchange" class="active" >积分商品兑换记录</a></li>
					<li><a href="#record">积分获取记录</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-item active" id="exchange">
					</div>
					<div class="tab-item " id="record"></div>
				</div>
				<div class="nextpage"><a class="zj-prev disabled" href="#">上一页</a><a class="zj-next" href="#">下一页</a></div>
			</div>
		</div>
	</div>
</div>
<!--//section-->

<?php include "../com/footer.php"; ?>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script type="text/html" id="zj-exchange-tmpl">
<table class="tr-bottom-line" width="100%">
<!--[for(i=0;i<list.length;i++){]-->
	<tr>
		<td style="width:170px;"><span class="gray-span">时间：</span><!--[= $formatDate(list[i].createAt) || '--']--></td>
		<td style="width:418px;"><span class="gray-span">商品名称：</span><!--[= list[i].giftName || '--']--></td>
		<td style="width:135px;"><span class="gray-span">消耗积分：</span><!--[= $formatCurrency1(list[i].credits)]--></td>
		<td style="width:125px;"><span class="gray-span">订单状态：</span><!--[= $getStatus(list[i].status)]--></td>
	</tr>
<!--[}]-->
</table>
</script>
<script type="text/html" id="zj-record-tmpl">
<table class="tr-bottom-line" width="100%">
<!--[for(i=0;i<list.length;i++){]-->
	<tr>
		<td><span class="gray-span">时间：</span><!--[= $formatDate(list[i].createAt) || '--']--></td>
		<td><span class="gray-span">订单号：</span><!--[= list[i].orderNo || '--']--></td>
		<td><span class="gray-span">获取积分：</span><!--[= $formatCurrency1(list[i].credits)]--></td>
	</tr>
<!--[}]-->
</table>
</script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/personal/my-jifen');
</script>

</body>
</html>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>个人中心</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/personal.css" />
	<link rel="stylesheet" href="../content/css/personal-index.css" />
	<link rel="stylesheet" href="../content/css/wrt.css" />
</head>
<body>

<?php include "../com/header.php"; ?>

<div class="section content-left-bg">
	<div class="page-vertical clearfix">
		<?php include '../com/wrt-left.php'; ?>
		<div class="content-right"style="padding-top: 15px;">
			<div class="my-credit-info gray-box clearfix" id="zj-summary"><div class="m-c-i-left">
					<div class="m-c-i-info clearfix">
						<div class="show-icon">
							<a href="my-info.html"><img class="user-icon" alt="" src="http://61.155.169.177:9999/pics/201411/141584810125.jpeg"></a>
						</div>
						<div class="m-c-i-text">
							<div><span class="gray-span">信用等级：</span>AAA</div>
							<div><span class="gray-span">客户号：</span>010010001</div>
							<div title="中铁十五局集团有限公司拉林公路四标项目经理部"><span class="gray-span">客户名：</span>中铁十五局集团有限公司拉林公路四标项目经理部</div>
						</div>
					</div>
					<div class="m-c-i-btns">
						<a class="button btn-gray" href="../credit/more.html">了解信用B</a>
						<a class="button btn-gray" href="../personal/my-address.html">收货地址</a>
						<a class="button btn-primary" href="javascript:;">安全等级：高</a>
					</div>
				</div>
				<table width="100%" cellpadding="0" cellspacing="0" class="m-c-i-center">
					<tbody><tr>
						<td class="gray-span">总信用额度：</td>
						<td class="money">300,000,000RMB</td>
					</tr>
					<tr>
						<td class="gray-span">可用总信用额度：</td>
						<td>278,980,000RMB</td>
					</tr>
					<tr>
						<td class="gray-span">已使用信用额度：</td>
						<td>21,020,000RMB</td>
					</tr>
					<tr>
						<td class="gray-span">已恢复信用额度：</td>
						<td>4,010,000RMB</td>
					</tr>
				</tbody></table>
				<div class="m-c-i-right">
					<a class="button btn-yellow" href="../credit/additional.html">追加信用额度</a>
					<a class="button btn-secondary" href="../personal/my-creditb-bill.html">票据兑换到期提醒</a>
					<a class="button btn-cgdd" href="../personal/my-creditb-history.html">使用流水历史记录</a>
				</div>
			</div>
			<!--3-->
			<div class="order-list gray-box">
				<div class="wrt-3 clearfix" style="padding: 17px 10px;border-bottom:1px solid #E6E6E6">
					<div class="title-l">我拥有的会员权限：<span>支付权限，验收权限，验章权限</span></div>
					<div class="title-r">我拥有的会员直属关系：<span style="color:#eb3b42">下级</span></div>
				</div>
				<div class="order-list-header clearfix" style="font-weight:normal">
					<div class="order-list-col wrt-order-list c1">会员编码</div>
					<div class="order-list-col wrt-order-list c2">直属关系</div>
					<div class="order-list-col wrt-order-list c3">会员名称</div>
					<div class="order-list-col wrt-order-list c4">物资验收权限</div>
					<div class="order-list-col wrt-order-list c4">合同验收权限</div>
					<div class="order-list-col wrt-order-list c4">订单验收权限</div>
					<div class="order-list-col wrt-order-list c5">单位名称</div>
				</div>
				<div class="order-list-content">
					<a href="my-order-detail.html?id=6" class="clearfix">
						<div class="order-list-col wrt-order-list c1">2014120393133</div>
						<div class="order-list-col wrt-order-list c2">下级</div>
						<div class="order-list-col wrt-order-list c3">v00001</div>
						<div class="order-list-col wrt-order-list c4"><img src="../content/images/wrt/right.png"/></div>
						<div class="order-list-col wrt-order-list c4"><img src="../content/images/wrt/worry.png"/></div>
						<div class="order-list-col wrt-order-list c4"><img src="../content/images/wrt/right.png"/></div>
						<div class="order-list-col wrt-order-list c5">中铁十五局集团</div>
					</a>
			    </div>
			    <div class="nextpage" style="">
			    	<a class="zj-prev disabled" href="#">上一页</a><a class="zj-next disabled" href="#">下一页</a>
			    </div>
			</div>
			<!--3-->
				
			</div>
			
		</div>
	</div>
</div>
<!--//section-->

<?php include "../com/footer.php"; ?>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/');
</script>

</body>
</html>
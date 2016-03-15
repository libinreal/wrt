<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>票据兑付到期提醒-我的信用B</title>
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
				<a href="../mall/">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <a href="my-creditb.html">我的信用B</a> &gt; <span id="type-name">票据兑付到期提醒</span>
				<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
			</div>
				<div class="bill_header"><span id="zj-notice">您有5张票据将在3天后到期，有3张票据将在30天后到期！</span></div>
				<div class="creditB_bill clearfix">
					<div class="bill clearfix">
						<div class="bill_l">票号</div>
						<div class="bill_c">票面金额</div>
						<div class="bill_r">票面到期日</div>	
					</div>
					<div id="zj-bill"></div>
			   </div>
			   <div class="bill_select">
		   		<div class="bill_select_drop" style="display: none;">
			   		<a href="?time=d3" data-time="d3"><span>3天内到期</span></a>
			   		<a href="?time=d5" data-time="d5"><span>5天内到期</span></a>
			   		<a href="?time=d15" data-time="d15"><span>15天内到期</span></a>
			   		<a href="?time=inm" data-time="inm"><span>一个月内到期</span></a>
			   		<a href="?time=outm" data-time="outm"><span>一个月后到期</span></a>
				   </div>
			   </div>
			   </div>
		   </div>
		</div>
	</div><!--//section-->	

	<?php include "../com/footer.php"; ?>

	<script id="zj-bill-tmpl" type="text/html">
	    <!--[for(i = 0; i < list.length; i ++) {]-->
   		<div class="bill_second clearfix">
			<div class="bill_left"><!--[= list[i].billNO]--></div>
			<div class="bill_c"><!--[= $formatCurrency1(list[i].billAmt)]--></div>
			<div class="bill_right"><!--[= $formatDate(list[i].billEndDate,'yyyy/MM/dd')]--></div>					 
   		</div>
	    <!--[}]-->
	</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use("../content/js/personal/my-creditb-bill");
</script>
</body>
</html>
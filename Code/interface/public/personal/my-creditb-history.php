<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>信用流水历史记录-我的信用B</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/personal.css" />
	<style type="text/css">
		.header-right .header-creditb{
			display: block;
		}
	</style>
</head>
<body>

	<?php include '../com/header.php'; ?>

	<div class="section content-left-bg">
		<div class="page-vertical clearfix">
		<?php include '../com/nav-left.php'; ?>
			<div class="content-right">
				<div class="breadcrumbs">
					<a href="../mall/">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <a href="my-creditb.html">我的信用B</a> &gt; <span id="type-name">信用流水历史记录</span>
					<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
				</div>
				<div id="zj-list"></div>
			</div>
		</div>
	</div><!--//section-->	

	<?php include '../com/footer.php'; ?>

	<script id="zj-list-tmpl" type="text/html">
	    <!--[for(i = 0; i < list.length; i ++) {]-->
		<div class='creditB_history clearfix'>
			<div class='history_l clearfix'>
				<p><span class='left'>单据编号：</span><span class='right'><!--[= list[i].billNO]--></span></p>	
				<p><span class='left'>客户编号：</span><span class='right'><!--[= list[i].cusFnum]--></span></p>
				<p><span class='left'>公司：</span><span class='right'><!--[= list[i].company]--></span></p>
				<p><span class='left'>扣减类型：</span><span class='right'><!--[= (list[i].chanKind == 0 ? '扣减' : '恢复')]--></span></p>	
				<p class='last'><span class='left'>备注：</span><span class='right'><!--[= list[i].remark]--></span></p>																		
			</div>
			<div class='history_r'>
				<p><span class='left'>单据日期：</span><span class='right'><!--[= $formatDate(list[i].chanDate,'yyyy/MM/dd')]--></span></p>	
				<p><span class='left'>客户：</span><span class='right'><!--[= list[i].cusName]--></span></p>
				<p><span class='left'>金额：</span><span class='right'><!--[= list[i].chanAmt]--></span></p>
				<p><span class='left'>登记机构：</span><span class='right'><!--[= list[i].bankName]--></span></p>
				<p class='last'><span class='right img' style='width:150px;'></span></p>			
			</div>
		</div>
	    <!--[}]-->
	</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/personal/my-creditb-history');
</script>
</body>
</html>
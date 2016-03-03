<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>我的票据-个人中心</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/personal.css" />
	<style type="text/css">
	.order-list-col{
		width: 105px;
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
				<a href="../">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <span id="type-name" >我的票据</span>
				<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
			</div>	
			<div class="order-list gray-box">
				<div class="order-list-header clearfix">
					<div style="height:10px;"></div>
					<form id="search_form" class="search_form" onsubmit="return false;">
						<div style="text-align:center;">
						<label>编号：</label><input type="text" name="bill_num">&nbsp;
						<label>出票人：</label><input type="text" name="drawer">&nbsp;
						<label>承兑人：</label><input type="text" name="acceptor">&nbsp;
						<label>状态：</label>
						<select name="bill_status">
							<option value="">全部</option>
							<option value="0">未还</option>
							<option value="1">已还</option>
						</select>&nbsp;
						<label>日期：</label>
                        <input type="text" name="start" id="start"> 到 <input type="text" name="end" id="end">
						</div>
						<div style="height:5px;"></div>
						<div style="text-align:right;">
						<button class="button" id="search_button">查询</button>&nbsp;
						</div>
					</form>
					<div style="height:10px;"></div>
				</div>
				<div class="order-list-header clearfix">
					<div class="order-list-col c1">票据号</div>
					<div class="order-list-col c2">签发日</div>
					<div class="order-list-col c3">到期日</div>
					<div class="order-list-col c4">票面金额</div>
					<div class="order-list-col c5">往来单位</div>
					<div class="order-list-col c6">出票人</div>
					<div class="order-list-col c7">承兑人</div>
					<div class="order-list-col c8">还票状态</div>
				</div>
				<div class="order-list-content" id="zj-list"></div>
			</div>

		</div>
	</div>
</div>
<!--//section-->

<?php include '../com/footer.php'; ?>

<script id="zj-list-tmpl" type="text/html">
    <!--[for(i = 0; i < list.length; i ++) {]-->
	<a href="my-note-detail.html?bill_id=<!--[= list[i].bill_id]-->" class="clearfix">
		<div class="order-list-col c1"><!--[= list[i].bill_num]--></div>
		<div class="order-list-col c2"><!--[= list[i].issuing_date || '--']--></div>
		<div class="order-list-col c3"><!--[= list[i].due_date || '--']--></div>
		<div class="order-list-col c4"><!--[= list[i].bill_amount || '--']--></div>
		<div class="order-list-col c5"><!--[= list[i].companyName || '--']--></div>
		<div class="order-list-col c5"><!--[= list[i].drawer || '--']--></div>
		<div class="order-list-col c5"><!--[= list[i].acceptor || '--']--></div>
		<div class="order-list-col c5"><!--[= list[i].status || '--']--></div>
	</a>
    <!--[}]-->
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/personal/my-note');
</script>

</body>
</html>
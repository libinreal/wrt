<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>我的票据-个人中心</title>
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
				<a href="../">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <span id="type-name" >我的票据</span>
				<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
			</div>	
			<div class="order-list gray-box">
				<div class="order-list-header clearfix">
					<div style="height:10px;"></div>
					<form id="search_form" class="search_form" onsubmit="return false;">
						<div>
						<label>票据编号：</label><input type="text" name="">&nbsp;&nbsp;
						<label>出票人：</label><input type="text" name="">&nbsp;&nbsp;
						<label>状态：</label>
						<select>
							<option value="-1">全部</option>
							<option value="0">未还</option>
							<option value="1">已还</option>
						</select>&nbsp;&nbsp;
						<label>日期：</label>
                        <input type="text" name=""> 到 <input type="text" name="">&nbsp;&nbsp;
						<button class="button">查询</button>
						</div>
					</form>
					<div style="height:10px;"></div>
				</div>
				<div class="order-list-header clearfix">
					<div class="order-list-col c1">票据id</div>
					<div class="order-list-col c2">票据编号</div>
					<div class="order-list-col c3">票据类型</div>
					<div class="order-list-col c4">币别</div>
					<div class="order-list-col c5">票面金额</div>
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
		<div class="order-list-col c1"><!--[= list[i].bill_id]--></div>
		<div class="order-list-col c2"><!--[= list[i].bill_num || '--']--></div>
		<div class="order-list-col c3"><!--[= list[i].bill_type || '--']--></div>
		<div class="order-list-col c4"><!--[= list[i].currency || '--']--></div>
		<div class="order-list-col c5"><!--[= list[i].bill_amount || '--']--></div>
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
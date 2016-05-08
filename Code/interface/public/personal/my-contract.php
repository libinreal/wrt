<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>我的合同-个人中心</title>
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
				<a href="../mall/">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <span id="type-name">我的合同</span>
				<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
			</div>

			<div class="order-list gray-box">
                <div class="order-list-header clearfix">
                    <div style="height:10px;"></div>
                    <form id="search_form" class="search_form" onsubmit="return false;">
                        <div style="text-align:center;">
                        <label>合同编号：</label><input type="text" name="contract_sn">&nbsp;&nbsp;&nbsp;&nbsp;
                        <label>合同名称：</label><input type="text" name="contract_name">&nbsp;&nbsp;&nbsp;&nbsp;
                        <label>合同日期：</label><input type="text" name="start" id="start" readonly="readonly"> 到 <input type="text" name="end" id="end" readonly="readonly">&nbsp;&nbsp;&nbsp;&nbsp;
                        <button class="button" id="search_button">查询</button></div>
                    </form>
                    <div style="height:10px;"></div>
                </div>
				<div id="contract-list"></div>
			</div>
		</div>
	</div>
</div>
<!--//section-->

<?php include '../com/footer.php'; ?>
<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>

<script id="contract-list-tmpl" type="text/html">
    <!--[for(i = 0; i < list.length; i ++) {]-->
    <table class="index-contract-list">
    <thead>
    <tr>
    <th class="gray-span">项目名称:</th><td><!--[= list[i].name || '--']--></td>
    <td colspan="2" align="right"><a href="my-contract-detail.html?contract_id=<!--[= list[i].id || '--']-->">查看合同详情</a>&nbsp;&nbsp;</td>
    </tr>
    </thead>
    <tbody>
    <tr>
    <th class="gray-span">公司名称:</th><td><!--[= list[i].companyName || '--']--></td>
    <th class="gray-span">合同编号:</th><td><!--[= list[i].num || '--']--></td>
    </tr>
    <tr>
    <th class="gray-span">合同金额:</th><td><!--[= list[i].amount || '--']--></td>
    <th class="gray-span">合同有效期:</th><td><!--[= $formatDate(list[i].startTime, 1) || '--']--> 至 <!--[= $formatDate(list[i].endTime, 1) || '--']--></td>
    </tr>
    </tbody>
    </table>
    <!--[}]-->
</script>

<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/personal/my-contract');
</script>

</body>
</html>
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
				<a href="../">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <span id="type-name">我的合同</span>
				<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
			</div>	
			
			<div class="order-list gray-box">
                <div class="order-list-header clearfix">
                    <div style="height:10px;"></div>
                    <form>
                        <div><label>合同编号：</label><input type="text" name="">&nbsp;&nbsp;&nbsp;&nbsp;
                        <label>合同名称：</label><input type="text" name="">&nbsp;&nbsp;&nbsp;&nbsp;
                        <button class="button">查询</button></div>
                        <div><label>合同日期：</label>
                        <input type="text" name=""> 到 <input type="text" name=""></div>
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
    <td>项目名称:&nbsp;&nbsp;<!--[= list[i].name || '--']--></td>
    <td><a href="my-contract-detail.html?contract_id=<!--[= list[i].id || '--']-->">查看合同详情</a></td>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>公司名称:&nbsp;&nbsp;<!--[= list[i].userName || '--']--></td>
    <td>合同编号:&nbsp;&nbsp;<!--[= list[i].num || '--']--></td>
    </tr>
    <tr>
    <td>合同金额:&nbsp;&nbsp;<!--[= list[i].amount || '--']--></td>
    <td>合同有效期:&nbsp;&nbsp;<!--[= $formatDate(list[i].startTime, 1) || '--']--> 至 <!--[= $formatDate(list[i].endTime, 1) || '--']--></td>
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
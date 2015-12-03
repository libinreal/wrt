<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>我的项目-个人中心</title>
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
				<a href="../">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <span id="type-name" >我的项目</span>
				<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
			</div>	
			<div id="zj-list"></div>
		</div>
	</div>
</div>
<!--//section-->

<?php include '../com/footer.php'; ?>
<script id="zj-list-tmpl" type="text/html">
<!--[for(i = 0; i < list.length; i ++) {]-->
	<div class="project-list gray-box">
		<div class="project-t">
			<div class="project-l">项目名：<span><!--[= list[i].prjName || '--']--></span></div>
            <!--[if (list[i].recommand && list[i].recommand != '0'){]-->
			<div class="project-r"><a class="button btn-secondary" href="my-project-detail.html?constractSn=<!--[= list[i].prjNo]-->">查看分期推荐订单详情</a></div>
            <!--[}else{]-->
			<div class="project-r"><a class="button disabled" href="javascript:;">查看分期推荐订单详情</a></div>
            <!--[}]-->
		</div>
		<ul class="project-m clearfix">
			<li>公司名称：<span><!--[= list[i].companyName || '--']--></span></li>
			<li>合同编号：<span><!--[= list[i].prjNo || '--']--></span></li>
			<li class="m-l">合同金额：<span><em class="c-red"><!--[= $formatCurrency1(list[i].price)]--></em>&nbsp;信用B</span></li>
			<li class="m-r">合同有效截止日：<span><!--[= $formatDate(list[i].deadline, 'yyyy-MM-dd')]--></span></li>										
		</ul>
	</div>   
<!--[}]-->
</script>
<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/personal/my-project');
</script>

</body>
</html>
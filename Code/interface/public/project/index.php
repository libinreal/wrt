<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>工程资讯</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/project.css" />
</head>
<body>

<?php include '../com/header.php'; ?>

<div class="section project-home">
	<div class="page-vertical">
		<a class="project-home-item" href="business.html">
	        <div class="project-home-link">
	    		<div class="project-home-thumb"><img src="../content/images/project/r-sqzx.png" /></div>
	    		<div class="project-home-title">
	    			<div class="project-home-icon project-home-icon-1"></div>
	    			<h3>商情中心</h3>
	    			<p>Business Center</p>
	    		</div>
	        </div>
			<ul class="project-home-list" id="zj-business"></ul>
		</a>
		<a class="project-home-item" href="bidding.html">
	        <div class="project-home-link">
	    		<div class="project-home-thumb"><img src="../content/images/project/r-gczb.png" /></div>
	    		<div class="project-home-title">
	    			<div class="project-home-icon project-home-icon-2"></div>
	    			<h3>工程招标</h3>
	    			<p>Project Bidding</p>
	    		</div>
	        </div>
			<ul class="project-home-list" id="zj-project"></ul>
		</a>
	</div>
</div><!--//section-->

<?php include '../com/footer.php'; ?>

<script id="zj-project-tmpl" type="text/html">
    <!--[for(i = 0; i < list.length; i ++) {]-->
	<li><!--[= list[i].name]--></li>
    <!--[}]-->
</script>

<script id="zj-business-tmpl" type="text/html">
    <!--[for(i = 0; i < list.length; i ++) {]-->
	<li>
		<span class="project-home-name"><em class="company-name"><!--[=list[i].factory]--></em><!--[=list[i].category]--></span>
		<span class="project-home-price"><!--[=$formatCurrency1(list[i].price)]--></span>
		<span class="project-home-amount"><!--[=list[i].vscope]--></span>
		<!--[if(list[i].vscope>0){]-->
			<span class="project-home-flag up">&nbsp;</span>
		<!--[}else if(list[i].vscope<0){]-->
			<span class="project-home-flag down">&nbsp;</span>
		<!--[}else{]-->
			<span class="project-home-flag no">&nbsp;</span>
		<!--[}]-->
	</li>
    <!--[}]-->
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
seajs.use('../content/js/project/index');
</script>

</body>
</html>
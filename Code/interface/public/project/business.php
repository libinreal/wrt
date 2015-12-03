<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>商情中心-工程资讯</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/project.css" />
</head>
<body>

<?php include '../com/header.php'; ?>

<div class="section business">
	<div class="business-nav">
		<div class="sqzx-t">
			<a href="#gangcai" for="gangcai">钢材</a>
			<a href="#gangjiaoxian" for="gangjiaoxian">钢绞线</a>
			<a href="#shuini" for="shuini">水泥</a>
			<a href="#liqing" for="liqing">沥青</a>
			<a href="index.html" class="return goback">返回 &gt;</a>
		</div>
	</div>
	<div class="page-vertical clearfix">
		<div class="business-chart">
			<div class="charts"></div>
			<div class="business-buttons">
				<a class="button btn-secondary" href="#" id="btn-more">更多物资行情资讯</a>
			</div>
		</div>
		<div class="business-data">
			<ul class="business-list" id="businessData"></ul>
		</div>
	</div>
</div><!--//section-->

<?php include '../com/footer.php'; ?>

<div class="modal fade" id="modal-business">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button class="modal-close">×</button>
				<h4 class="modal-title">更多物资行情资讯</h4>
			</div>
			<div class="modal-body">
				<ul id="links-data" class="links clearfix"></ul>
			</div>
			<div class="modal-footer">
				<button class="btn btn-primary">确定</button>
				<button class="btn btn-default">取消</button>
			</div>
		</div>
	</div>
</div>
<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script id="links-tmpl" type="text/html">
<!--[for(var i=0;i<list.length;i++){]-->
	<li>
		<a href="<!--[=list[i].url]-->" target="_blank" ><img alt="<!--[=list[i].title]-->" src="<!--[=$absImg(list[i].icon)]-->"/></a>
	</li>
<!--[}]-->
</script>
<script id="businessListDataTmpl" type="text/html">
<!--[for(i=0;i<list.length;i++){]-->
	<!--[if(i == 0){]-->
	<li class="business-rec" data-wcode="<!--[= list[i].wcode]-->" data-brand="<!--[= list[i].brandId]-->">
		<div class="business-rec-type clearfix">
			<span class="fl" title="<!--[=list[i].factory]-->"><em><!--[=list[i].factory]--></em></span>
			<span class="fr clearfix"><span><!--[= $formatCurrency1(list[i].price)]--></span><i>&nbsp;信用B</i></span>
		</div>
		<div class="business-rec-title"><!--[=list[i].name || '--']--></div>
		<div class="business-rec-sub clearfix"><span class="fl"><!--[=list[i].spec || '--']--></span><span class="fr">今日：<!--[= $formatDate(list[i].time,'MM月dd日')]--></span></div>
	</li>
	<!--[}else{]-->
	<!--[if(list[i].vary>0){]-->
		<li class="business-item business-item-up" data-wcode="<!--[= list[i].wcode]-->" data-brand="<!--[= list[i].brandId]-->">
	<!--[}else if(list[i].vary<0){]-->
		<li class="business-item business-item-down" data-wcode="<!--[= list[i].wcode]-->" data-brand="<!--[= list[i].brandId]-->">
	<!--[}else{]-->
		<li class="business-item business-item-no" data-wcode="<!--[= list[i].wcode]-->" data-brand="<!--[= list[i].brandId]-->">
	<!--[}]-->
		<span class="business-item-name"><em><!--[=list[i].factory]--></em></span><span 
			class="business-item-price"><!--[=$formatCurrency1(list[i].price)]--></span><span 
			class="business-item-amount"><!--[=list[i].vary]--></span><span 
			class="business-item-flag">&nbsp;</span>
	</li>
<!--[}]-->
<!--[}]-->
</script>
<script>
	seajs.use('../content/js/project/business');
</script>

</body>
</html>
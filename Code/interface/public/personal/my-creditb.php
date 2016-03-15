<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>我的信用B-个人中心</title>
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
					<a href="../mall/">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <span id="type-name" >我的信用B</span>
					<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
				</div>
				<div class="creditB clearfix" id="zj-summary"></div>
				<div class="creditB_list clearfix">
					<div class="list_l">
						<div class="t">信用额度登记机构：</div>
						<div id="zj-banks"></div>								
					</div>
					<div class="list_r">
						<div class="t">采购额度：</div>
						<div id="zj-cged"></div>
					</div>
				</div>
			</div>
		</div>
	</div><!--//section-->	

	<?php include "../com/footer.php"; ?>

	<script id="zj-summary-tmpl" type="text/html">
		<ul class="credit_level_l_m">
			<li class="img"><a href="my-info.html"><img class="user-icon" src="<!--[= $getUserIcon(icon)]-->" alt="" /></a></li>
			<li class="level_name"><a href="my-creditb-level.html">信用等级</a></li>
			<li class="level_value"><a href="my-creditb-level.html"><!--[= cusLevel || '--']--></a></li>
		</ul>
		<ul class="credit_level_l_t">
			<li class="clearfix"><span class="label">客户号：</span><span class="value"><!--[= cusFnum || '--']--></span></li>
			<li class="clearfix"><span class="label">客户名：</span><span class="value" title="<!--[= cusName]-->"><!--[= cusName || '--']--></span></li>
			<li><a class="button btn-primary" href="javascript:;">安全等级：高</a></li>
			<li><a class="button btn-gray" href='../credit/more.html'>了解信用B</a></li>
		</ul>
		<ul class="credit_level_r_left">
			<li class='count_ct'>总信用额度：<span><!--[= $formatCurrency1(creAmtTot)]-->  RMB</span></li>
			<li>可用总信用额度：<span><!--[= $formatCurrency1(lastAmtTot)]-->  RMB</span></li>
			<li>已使用信用额度：<span><!--[= $formatCurrency1(spendAmtTot)]-->  RMB</span></li>
			<li>已恢复信用额度：<span><!--[= $formatCurrency1(restoreAmtTot)]-->  RMB</span></li>
		</ul>
		<div class="credit_level_r_right">
			<a class="button btn-yellow" href="../credit/additional.html">追加信用额度</a>
			<a class="button btn-secondary" href="my-creditb-bill.html">票据兑付到期提醒</a>
			<a class="button btn-cgdd"  href="my-creditb-history.html">信用流水历史记录</a>						
		</div>
	</script>
	<script id="zj-banks-tmpl" type="text/html">
	    <!--[for(i = 0; i < 7; i ++) {]-->
		<div class="body clearfix">
			<div class="body_l"><span class="bank bank-<!--[= list[i].bankFnum]-->"><!--[= list[i].bankName]--></span></div>							
			<ul class="body_c">
				<li class="t-d">可用额度：</li>
				<li class="red"><!--[= $formatCurrency1(list[i].lastAmt)]--></li>
				<li class="t-t">额度单位&nbsp;&nbsp;&nbsp;<span>RMB</span></li>
			</ul>
			<ul class="body_r">
				<li><span class="t-d">总信用额度：</span><em><!--[= $formatCurrency1(list[i].creAmt)]--></em></li>
				<li><span class="t-d">已使用额度：</span><em><!--[= $formatCurrency1(list[i].spendAmt)]--></em></li>
				<li><span class="t-d">额度有效期：</span><em style="font-size:12px"><!--[= (list[i].strDate || '') + '-' + (list[i].endDate || '')]--></em></li>
			</ul>
    	</div>
		<!--[}]-->
	</script>
	<script id="zj-cged-tmpl" type="text/html">
	    <!--[for(i = 0; i < list.length; i ++) {]-->
		<div class="items">
			<table align="center" cellpadding="0" cellspacing="0" width="100%">
	    		<tr>
	    			<td class="t-r">项目编号：</td>
	    			<td class="t-d"><!--[= list[i].conFnum]--></td>
	    		</tr>
	    		<tr>
	    			<td class="t-r">项目名称：</td>
	    			<td class="t-d"><!--[= list[i].conName]--></td>
	    		</tr>
	    		<tr>
	    			<td class="t-r">项目采购总额：</td>
	    			<td class="t-d"><!--[= $formatCurrency1(list[i].buyAmt)]--><span class="unit">信用B</span></td>
	    		</tr>
	    		<tr>
	    			<td class="t-r">已使用采购额度：</td>
	    			<td class="t-d"><!--[= $formatCurrency1(list[i].spendAmt)]--><span class="unit">信用B</span></td>
	    		</tr>
	    		<tr>
	    			<td class="t-r">剩余采购总额：</td>
	    			<td class="t-d"><!--[= $formatCurrency1(list[i].lastAmt)]--><span class="unit">信用B</span></td>
	    		</tr>
	    		<tr>
	    			<td class="t-r">项目采购有效期：</td>
	    			<td class="t-d"><!--[= list[i].strDate]-->-<!--[= list[i].endDate]--></td>
	    		</tr>
	    		<tr>
	    			<td class="t-r">信用额度登记机构：</td>
	    			<td class="t-d">
	    			<!--[for(j = 0; j < list[i].banks.length; j++){]-->
	    				<span class="bank bank-<!--[= list[i].banks[j].bankFnum]-->" title="<!--[= list[i].banks[j].bankName]-->"></span>
	    			<!--[}]-->
	    			</td>
	    		</tr>
   			</table> 
			<div class="credit_button clearfix">
				<a class="button btn-blue" href="my-order.html">合同采购历史记录</a>
				<a class="button btn-secondary" href="my-creditb-apply.html">采购额度追加申请</a>
			</div>
		</div>
		<!--[}]-->
	</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use("../content/js/personal/my-creditb");
</script>
</body>
</html>
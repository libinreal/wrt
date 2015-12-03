<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>信用池</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/credit.css" />
</head>
<body>

<?php include "../com/header.php"; ?>

<div class="section credit-index">
	<div class="page-vertical">
		<div class="pool-top clearfix">
			<div class="scroll-img">
				<div class="items" id="zj-ad"></div>
				<div class="navi"></div>
			</div>
			<div class="pool-r">
				<p><a class="btn pool-r-one authority" href="apply.html"></a><a class="btn pool-r-three authority" href="manage.html"></a></p>
				<p><a class="btn pool-r-two authority" href="additional.html"></a><a class="btn pool-r-four authority" href="more.html"></a></p>
			</div>
		</div>	
		<div class="pool-center clearfix">
			<div class="pool-left">
				<div class="t">产业链融资新规则</div>
				<div class="ul clearfix">
					<div class="img img-1"></div>
					<ul class="word">
						<li class="size">闭环</li>
						<li>产业链体内循环</li>
						<li>零负债率</li>
						<li>平台封闭运作</li>
					</ul>
				</div>
				
				<div class="ul clearfix">
					<div class="img img-2"></div>
					<ul class="word">
						<li class="size">互惠</li>
						<li>低廉融资成本</li>
						<li>零保证金</li>
						<li>多方共赢体系</li>
					</ul>
				</div>
				
				<div class="ul clearfix">
					<div class="img img-3"></div>
					<ul class="word">
						<li class="size">高效</li>
						<li>交互授信</li>
						<li>信用前置</li>
						<li>票证互换</li>
					</ul>
				</div>
				
				<div class="ul clearfix">
					<div class="img img-4"></div>
					<ul class="word">
						<li class="size">保障</li>
						<li>便捷物融</li>
						<li>信用互联</li>
						<li>安全监管</li>
					</ul>
				</div>
			</div>
			<div class="pool-right">
				<div class="t">业务流程</div>
				<div class="r">
					<div class="img"></div>
					<div class="content" style="position:relative;">
						<ul class="content-l">
							<li>会员注册</li>
							<li>信用评测申请</li>
							<li>审核</li>
							<li>反馈评测结果</li>
							<li>我的评测</li>
						</ul>
						<ul class="content-c">
							<li>评测并开通账号权限</li>
							<li>信用额度</li>
							<li>采购额度</li>
							<li>票据到期兑付</li>
							<li>信用恢复</li>
						</ul>
						<ul class="content-r">
							<li>提交追加申请</li>
							<li>审核通过</li>
							<li>银行授信</li>
							<li>我的信用B</li>							
						</ul>
						<a class="btn ljpc authority" href="apply.html">立即评测</a>
						<a class="btn ljck authority" href="manage.html">立即 查看</a>	
						<a class="btn ljzj authority" href="additional.html">立即追加</a>							
					</div>						
				</div>
				<div class="btm-img"><img src="../content/images/credit/bg-img.png" alt=""/></div>
			</div>
		</div>	
		<div class="pool-bottom clearfix">
			<div class="lt">
				<div class="t">信用池融资业务</div>
				<div class="adv-img"></div>
			</div>
			<div class="rt online-apply">
				<div class="online-title">在线申请</div>
				<div class="online-header clearfix">
					<table class='tb-l' border="0" cellpadding="0" cellspacing="0">
						<tr>
							<th width="107px">会员编号</th>
							<th width="107px">信用额度</th>
					 		<th width="107px">增减幅度</th>
					 		<th width="107px">会员编号</th>
					 		<th width="107px">采购额度</th>
					  		<th width="107px">增减幅度</th>
					 	</tr>
					</table>
				</div>
				<div class="online-scroll tb">
					<div class="items clearfix" id="zj-credit"></div>
				</div>
			</div>
		</div>			
		<div class="pool-friends">
			<div class="wrd">合作伙伴：</div><div class="bank-img"><img src="../content/images/credit/credit_bank.gif" alt="" /></div>
		</div>								
	</div>
</div><!--//section-->	

<?php include "../com/footer.php"; ?>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>

<script id="zj-credit-tmpl" type="text/html">
	<table class='tb-l' width="100%" cellpadding="0" cellspacing="0">
    <!--[for(i = 0; i < 20; i ++) {]-->
		<tr class="<!--[= i % 2 == 0 ? 't-1' : 't-2']-->">
    <!--[if(creamt && creamt[i]) {]-->
    		<td width="107px"><!--[=creamt[i].customNo]--></td>
    		<td width="107px"><!--[= $formatCurrency1(creamt[i].curamt)]--></td>
			<td width="107px">
				<span class="word-udc"><!--[= $formatCurrency1(creamt[i].addamt)]--></span>
    		</td>
    <!--[}else{]-->
    		<td width="107px">--</td>
    		<td width="107px">--</td>
    		<td width="107px">
				<span>--</span>
    		</td>			 			 
    <!--[}]-->
    <!--[if(buyamt && buyamt[i]) {]-->
    		<td width="107px"><!--[= buyamt[i].customNo]--></td>
    		<td width="107px"><!--[= $formatCurrency1(buyamt[i].curamt)]--></td>
			<td width="107px">
				<span class="word-udc"><!--[= $formatCurrency1(buyamt[i].addamt)]--></span>
    		</td>
    <!--[}else{]-->
    		<td width="107px">--</td>
    		<td width="107px">--</td>
    		<td width="107px">
				<span>--</span>
    		</td>			 			 
    <!--[}]-->
    	</tr>
    <!--[}]-->
	</table>
</script>
<script>
	seajs.use("../content/js/credit/index");
</script>
</body>
</html>
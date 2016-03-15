<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>票据详情-个人中心</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/personal.css" />
	<style>
	.distance{
		margin-right:100px;
	}
	</style>
</head>
<body>

<?php include '../com/header.php'; ?>
<div class="section page-vertical">
	<div class="breadcrumbs">
		<a href="../mall/">首页</a> &gt; <a href="index.html">个人中心</a> &gt; <a href="my-note.html">我的票据</a> &gt; <span id="type-name">票据详情</span>
		<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
	</div>
	<div class="order" id="zj-detail"></div>
</div><!--//section-->

<?php include '../com/footer.php'; ?>
<script id="zj-detail-tmpl" type="text/html">
	
	<div class="order-info gray-box">
		<div class="head-title">
			<span>票据信息</span>
		</div>
		<div class="info-border">
			<table cellpadding="0" cellspacing="1">
				<tr>
				<th>票据编号：</th><td width=150><!--[= bill_num || '--']--></td><th>票据类型：</th><td><!--[= bill_type || '--']--></td><th>币别：</th><td><!--[= currency || '--']--></td><th>票面金额：</th><td><!--[= bill_amount || '--']--></td>
				</tr>
				<tr>
				<th>客户号：</th><td><!--[= customer_num || '--']--></td><th>合同编号：</th><td><!--[= contract_num || '--']--></td><th>付款利率(%)：</th><td><!--[= payment_rate || '--']-->%</td><th>到期金额：</th><td><!--[= expire_amount || '--']--></td>
				</tr>
				<tr>
				<th>签发日：</th><td><!--[= issuing_date || '--']--></td><th>到期日：</th><td><!--[= due_date || '--']--></td><th>付款期限：</th><td><!--[= prompt_day || '--']--></td><th>出票人：</th><td><!--[= drawer || '--']--></td>
				</tr>
				<tr>
				<th>承兑人：</th><td><!--[= issuing_date || '--']--></td><th>承兑协议编号：</th><td><!--[= accept_num || '--']--></td><th>承兑日期：</th><td><!--[= accept_date || '--']--></td><th>备注：</th><td><!--[= remark || '--']--></td>
				</tr>
				<tr>
				<th>来往单位：</th><td><!--[= current_unit || '--']--></td><th>收票日：</th><td><!--[= receive_date || '--']--></td><th>交易金额：</th><td><!--[= trans_amount || '--']--></td><th>销售人员：</th><td><!--[= saler || '--']--></td>
				</tr>
				<tr>
				<th>收款组织：</th><td><!--[= receiver || '--']--></td><th>结算组织：</th><td><!--[= balance || '--']--></td><th>折算比例：</th><td colspan="3"><!--[= discount_rate || '--']-->%</td>
				</tr>
				<tr>
				<th>ID：</th><td><!--[= bill_id || '--']--></td><th>票据状态：</th><td><!--[= status || '--']--></td><th>折后额度：</th><td><!--[= discount_rate*bill_amount/100 || '--']--></td><th>带追索权：</th><td><!--[= is_recourse || '--']--></td>
				</tr>
				<tr>
				<th>备注：</th><td colspan="20"><!--[= remark || '--']--></td>
				</tr>
				<tr>
				<th colspan="2">付款人：</th><td colspan="2"><!--[= pay_user || '--']--></td>
				<th colspan="2">收款人：</th><td colspan="2"><!--[= receive_user || '--']--></td>
				</tr>
				<tr>
				<th colspan="2">付款银行：</th><td colspan="2"><!--[= pay_bank || '--']--></td>
				<th colspan="2">收款银行：</th><td colspan="2"><!--[= receive_bank || '--']--></td>
				</tr>
				<tr>
				<th colspan="2">付款账号：</th><td colspan="2"><!--[= pay_account || '--']--></td>
				<th colspan="2">收款账号：</th><td colspan="2"><!--[= receive_account || '--']--></td>
				</tr>
			</table>
		</div>
		<div style="text-align:center;margin-bottom:10px;">
		<a class="button btn-gray" href="javascript:history.back()">返回</a>
		</div>		
	</div>

	

</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/personal/main');
	seajs.use('../content/js/personal/my-note-detail');
</script>

</body>
</html>
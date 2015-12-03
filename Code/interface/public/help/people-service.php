<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>人工服务-帮助中心</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/help.css" />
</head>
<body>

<?php include '../com/header.php'; ?>

<div class="section people-service">
	<div class="page-vertical">
		<div class="breadcrumbs">
			<a href="../">首页</a> &gt; <a href="index.html">帮助中心</a> &gt; <span id="type-name">人工服务</span>
			<a href="javascript:history.go(-1)" class="return">返回 &gt;</a>
		</div>
		<div class="manual">
	    	<div class="manual-item">
	    		<a class="manual-icon manual-icon1" href="online-service.html">&nbsp;</a>
	    		<div class="manual-content">
	    			<h3><a href="online-service.html">在线客服</a></h3>
	    			<p>9：00-17：00在线及时解答您的问题</p>
	    		</div>
	    	</div>
	    	<div class="manual-item">
	    		<a class="manual-icon manual-icon2 open-dialog-btn" href="#" for="phoneDialog">&nbsp;</a>
	    		<div class="manual-content">
	    			<h3>电话客服</h3>
	    			<p>24小时人工电话解答您的问题</p>
	    		</div>
	    	</div>
	    	<div class="manual-item">
	    		<a class="manual-icon manual-icon3" href="mailto:service@zhongjiao.cc">&nbsp;</a>
	    		<div class="manual-content">
	    			<h3>邮件客服<span>[mailto:service@zhongjiao.cc]</span></h3>
	    			<p>第一时间通过邮件解答您的问题</p>
	    		</div>
	    	</div>
	    	<div class="manual-item">
	    		<a class="manual-icon manual-icon4 open-dialog-btn" href="#" for="suggestDialog">&nbsp;</a>
	    		<div class="manual-content">
	    			<h3>投诉建议</h3>
	    			<p>第一时间通过邮件解答您的问题</p>
	    		</div>
	    	</div>
	    </div>
	</div>
</div><!--//section-->

<?php include '../com/footer.php'; ?>

<!-- 投诉建议 -->
<div id="suggestDialog" class="dialog">
	<div class="dialog-head">
		<span class="dialog-back"></span>
		<span class="dialog-title">投诉建议</span>
		<span class="dialog-close">X</span>
	</div>
	<div class="dialog-content suggest-form clearfix">
		<form class="form s-form" id="omplaint-form">
			<div class="input-group clearfix">
				<label><span>*</span>投诉内容：</label>
				<div class="input-radio">
					<label><input type="radio" name="type2" value="1" />建议与改进</label>
					<label><input type="radio" name="type2" value="2" />商品质量</label>
					<label><input type="radio" name="type2" value="3" />服务态度</label>
					<label><input type="radio" name="type2" value="4" />物流配送</label>
				</div>
				<input type="hidden" name="type" />
			</div>
			<div class="input-group clearfix">
				<label><span>*</span>具体内容：</label>
				<textarea class="suggest-content" name="content" placeholder="至少输入5个字符，最多输入..."></textarea>
			</div>
			<div class="input-group clearfix input-v">
				<label><span>*</span>相关订单号：</label>
				<input class="order" type="text" placeholder="请输入相关订单号" name="orderNo" maxlength="50" />
			</div>
			<div class="form-btn top-line">
				<input type="submit" class="button btn-secondary" value="提交" />
			</div>
		</form>
	</div>
</div>

<!-- 电话预约 -->
<div id="phoneDialog" class="dialog">
	<div class="dialog-head">
		<span class="dialog-back"></span>
		<span class="dialog-title">电话预约</span>
		<span class="dialog-close">X</span>
	</div>
	<div><img src="../content/images/help/img1.png" width="100%" /></div>
	<div class="dialog-content">
		<form class="form p-form" id="appointment-form">
			<div class="input-group clearfix input-xl">
				<label><span>*</span>预约：</label>
				<div class="input-radio l-b-m">
					<label><input type="radio" name="type1" value="1" />订单相关</label>
					<label><input type="radio" name="type1" value="2" />售后相关</label>
					<label><input type="radio" name="type1" value="3" />投诉与建议</label>
				</div>
				<input type="hidden" name="type"/>
			</div>
			<div class="input-group clearfix input-xl">
				<label><span>*</span>填写手机号码：</label>
				<input class="input-phone" type="text" placeholder="" name="telephone" maxlength="11" />
			</div>
			<div class="input-group clearfix input-xl">
				<label><span>*</span>选择回电时间：</label>
				<select id="day" class="lg-select">
					<option>今天</option>
					<option value="2014-09-13">2014-09-13</option>
					<option value="2014-09-14">2014-09-14</option>
					<option value="2014-09-15">2014-09-15</option>
				</select>
				<select id="hour">
					<option value="0">00</option>
					<option value="1">01</option>
					<option value="2">02</option>
					<option value="3">03</option>
					<option value="4">04</option>
					<option value="5">05</option>
					<option value="6">06</option>
					<option value="7">07</option>
					<option value="8">08</option>
					<option value="9">09</option>
					<option value="10">10</option>
					<option value="11">11</option>
					<option value="12">12</option>
					<option value="13">13</option>
					<option value="14">14</option>
					<option value="15">15</option>
					<option value="16">16</option>
					<option value="17">17</option>
					<option value="18">18</option>
					<option value="19">19</option>
					<option value="20">20</option>
					<option value="21">21</option>
					<option value="22">22</option>
					<option value="23">23</option>
				</select>
				<span>时</span>
				<select id="minute">
					<option value="0">00</option>
					<option value="5">05</option>
					<option value="10">10</option>
					<option value="15">15</option>
					<option value="20">20</option>
					<option value="25">25</option>
					<option value="30">30</option>
					<option value="35">35</option>
					<option value="40">40</option>
					<option value="45">45</option>
					<option value="50">50</option>
					<option value="55">55</option>
				</select>
				<span>分</span>
				<input type="hidden" name="time" />
			</div>
			<div class="form-btn top-line top-line-h">
				<input type="submit" class="button btn-secondary" value="提交预约">
			</div>
		</form>
	</div>
</div>

<script id="zj-detail-tmpl" type="text/html">
    <div class="new_detail_title"><!--[= title]--></div>
	<div class="new_detail_time">
		<span>时间：<!--[= $formatDate(createAt,'yyyy-MM-dd')]--></span>
		<span class="spanjuli">阅读：23次</span>
	</div>
	<div class="new_detail_img">
		<img src="<!--[= imgurl]-->">
	</div>
	<div class="new_detail_detail">
		<!--[= content]-->
	</div>
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/help/p-service');
</script>

</body>
</html>
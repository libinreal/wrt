<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>定制申请表-定制申请表</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/customize.css" />
</head>
<body>

<?php include '../com/header.php'; ?>

<div class="section customize">
	<div class="page-vertical">
		<div class="breadcrumbs">
			<a href="../">首页</a> &gt; <a href="index.html">定制专区</a> &gt; <span>定制申请表</span>
			<a href="javascript:history.go(-1)" class="return">返回 &gt;</a>
		</div>
		<div class="apply-form apply-2">
			<form id="applyForm">
			<div class="clearfix">
				<div class="groupbox applybox">
					<div class="group">
						<label><font class="color">*</font>项目名称：</label>
						<input id="name" name="name" maxlength="50" placeholder="请填入项目名称"/>
					</div>
					<div class="group">
						<label><font class="color">*</font>项目地址：</label>
						<input id="address" name="address" maxlength="20" placeholder="请填入项目地址"/>
					</div>
					<div class="group">
						<label><font class="color">*</font>项目工期：</label>
						<input id="period" name="period" maxlength="20" placeholder="请填入项目工期"/>
					</div>
					<div class="group">
						<label><font class="color">*</font>项目投资额度：</label>
						<input id="amount" name="amount" maxlength="20" placeholder="请填入项目投资额度"/>
					</div>
					<div class="group">
						<label><font class="color">*</font>所属区域：</label>
						<input id="area" name="area" maxlength="20" placeholder="请填入您的所属区域" />
						<input id="areaId" type="hidden" name="areaId" />
					</div>
				</div>
				<div class="group-right apply-right">
					<div class="group">
						<label><font class="color">*</font>联系人：</label>
						<input id="contactPeople" name="contactPeople" maxlength="20" placeholder="请填入您的联系人"/>
					</div>
					<div class="group">
						<label><font class="color">*</font>职位：</label>
						<input id="position" name="position" maxlength="20" placeholder="请填入您的职位"/>
					</div>
					<div class="group">
						<label><font class="color">*</font>联系方式：</label>
						<input id="contactTelephone" name="contactTelephone" placeholder="请填入您的联系方式" maxlength="13"/>
					</div>
					<div class="group">
						<label>公司名称：</label>
						<input id="companyName" maxlength="20" name="companyName" placeholder="请填入您的公司名称" maxlength="13"/>
					</div>
					<div class="group">
						<label>公司地址：</label>
						<input id="companyAddress" maxlength="200" name="companyAddress" placeholder="请填入您的公司地址" maxlength="13"/>
					</div>
				</div>
			</div>
				<div class="group apply">
					<label><font class="color">*</font>项目筹资情况：</label>
					<textarea id="remark" name="remark" class="tarea" maxlength="500" placeholder="请填入您的项目筹资情况"></textarea>
				</div>
				<div class="groupbtn applybtn">
					<input id="applyId" type="hidden" name="applyId"/>
					<input type="submit" class="button btn-secondary big" value="提交申请"/>
				</div>		
			</form>
		</div>
	</div>
</div><!--//section-->

<?php include '../com/footer.php'; ?>

<div class="modal modal3 fade" id="modal-province">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button class="modal-close">×</button>
				<h4 class="modal-title changing">切换省份</h4>
			</div>
			<div class="modal-body">
				<ul id="zj-province" class="province-list clearfix"></ul>
				<div class="province-confirm">
					<button id="confirm_pro">确定</button>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-primary">确定</button>
				<button class="btn btn-default">取消</button>
			</div>
		</div>
	</div>
</div>

<!-- 省份接口模板 -->
<script id="zj-province-tmpl" type="text/html">
    <!--[for(i = 0; i < list.length; i ++) {]-->
    <li><button data-id="<!--[= list[i].id]-->"><!--[= list[i].name]--></button></li>
    <!--[}]-->
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/customize/apply-list');
</script>
</body>
</html>
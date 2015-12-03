<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>定制申请表-定制专区</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/customize.css" />
</head>
<body>

<?php include '../com/header.php'; ?>

<div class="section customize">
	<div class="page-vertical">
		<div class="breadcrumbs">
			<a href="../">首页</a> &gt; <a href="index.html">定制专区</a> &gt; <span>定制申请表</span>
			<a href="index.html" class="return">返回 &gt;</a>
		</div>
		<div class="apply-form clearfix">
			<form id="applyForm" enctype="multipart/form-data">
				<div class="groupbox">
					<div class="group" style="height:62px;">
						<label><font class="color">*</font>分类：</label>
						<div class="zj-select" style="width: 392px;">
							<em><i></i></em>
							<span class="default">&nbsp;</span>
							<ul class="dropdown" id="categoryNo">
							</ul>
							<input type="hidden" />
						</div>
					</div>
					<div class="group" style="height:62px;">
						<label>&nbsp;</label>
						<div class="zj-select" style="width: 392px;">
							<em><i></i></em>
							<span class="default">&nbsp;</span>
							<ul class="dropdown" id="categoryNo-children">
							</ul>
							<input type="hidden" name="categoryNo" />
						</div>
					</div>
					<div class="group">
						<label><font class="color">*</font>商品名：</label>
						<input id="goodsName" name="goodsName" value="" placeholder="请填入商品名称"/>
					</div>
					<div class="group">
						<label><font class="color">*</font>数量：</label>
						<input id="goodsNum" name="goodsNum" value="" placeholder="请填入商品数量"/>
					</div>
					<div class="group">
						<label><font class="color">*</font>单位：</label>
						<input id="goodsUnit" name="goodsUnit" maxlength="8" placeholder="请填入商品单位"/>
					</div>
					<div class="group">
						<label>型号：</label>
						<input id="goodsSpec" name="goodsModel" value="" placeholder="请填入商品型号"/>
					</div>
					<div class="group">
						<label>规格：</label>
						<input id="goodsModel" name="goodsSpec" value="" placeholder="请填入商品规格"/>
					</div>
					<div class="group">
						<label><font class="color">*</font>价格：</label>
						<input id="goodsPrice" name="goodsPrice" value="" placeholder="请填入您期望的定制价格"/>
					</div>
					<div class="group">
						<label><font class="color">*</font>销售区域：</label>
						<input id="area" name="area" value="" placeholder="请填入您的销售区域" />
						<input id="areaId" type="hidden" name="areaId" />
					</div>
					<div class="group">
						<label><font class="color">*</font>地址：</label>
						<input id="address" name="address" value="" placeholder="请填入您的地址"/>
					</div>
					<div class="group">
						<label>留言：</label>
						<textarea id="remark" name="remark" class="tarea" maxlength="200" placeholder="请填入您的留言信息"></textarea>
					</div>
				</div>
				<div class="group-right">
					<div class="group-img">
						<div class="hid_img">
							<input id="picurl" type="file" class="fileInput">
							<img id="goodsImg" src="../content/images/common/blank.png">
						</div>
						<input type="hidden" name="originalImg" value="" />
						<input type="hidden" name="goodsImg" value="" />
						<div class="carmo"></div>
					</div>
					<div class="groupbox groupbox-top">
						<div class="group">
							<label><font class="color">*</font>联系电话：</label>
							<input id="telephone" name="telephone" value="" placeholder="请填入您的联系电话" maxlength="13"/>
						</div>
						<div class="group">
							<label><font class="color">*</font>联系人：</label>
							<input id="contacts" name="contacts" value="" placeholder="请填入您的联系人"/>
						</div>
						<div class="group" style="height:62px;">
							<label><font class="color">*</font>有效期：</label>
							<div class="zj-select" style="width: 232px;">
								<em><i></i></em>
								<span class="default">请选择有效期</span>
								<ul class="dropdown">
									<li value="">请选择有效期</li>
									<li value="M1">一个月内</li>
									<li value="M5">五个月内</li>
									<li value="Y1">一年内</li>
									<li value="Y2">二年内</li>
									<li value="Y3">三年内</li>
									<li value="Y4">四年内</li>
									<li value="Y5">五年内</li>
								</ul>
								<input type="hidden" name="validDateEnum" id="validDateEnum" />
							</div>
						</div>
					</div>
					<div class="groupbtn">
						<input id="applyId" type="hidden" name="applyId" />
						<input type="submit" class="button btn-secondary big" value="提交申请" />
					</div>
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

<script id="zj-category-tmpl" type="text/html">
    <!--[for(i = 0; i < list.length; i ++) {]-->
    <li><a href="#<!--[= list[i].goodsSpec]-->"><!--[= list[i].goodsName]--></a></li>
    <!--[}]-->
</script>

<!-- 省份接口模板 -->
<script id="zj-province-tmpl" type="text/html">
    <!--[for(i = 0; i < list.length; i ++) {]-->
    <li><button data-id="<!--[= list[i].id]-->"><!--[= list[i].name]--></button></li>
    <!--[}]-->
</script>

<!-- 分类接口模板 -->
<script id="zj-sort-tmpl" type="text/html">
    <!--[for(i = 0; i < list.length; i ++) {]-->
    <li data-v="<!--[= list[i].code]-->"><!--[= list[i].name]--></li>
    <!--[}]-->
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/customize/apply');
</script>
</body>
</html>
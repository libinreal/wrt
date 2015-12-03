<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>申请信用评测-信用池</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/credit.css" />
</head>
<body>

	<?php include '../com/header.php'; ?>

	<div class="section credit-apply">	
		<div class="page-vertical">
			<div class="breadcrumbs">
					<a href="../">首页</a> &gt; <a href="index.html">信用池</a> &gt; <span id="type-name">信用评测</span>
					<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
			</div>
			<form class="form credit-box credit-info">
				<div class="form-item">
					<div class="form-label"><span>*</span>用户名：</div>
					<div class="form-value" id="account">&nbsp;</div>
				</div>
				<div class="form-item">
					<div class="form-label"><span>*</span>第一联系人：</div>
					<div class="form-value form-value1" id="contacts">&nbsp;</div>
					<div class="form-label form-label2"><span>*</span>第二联系人：</div>
					<div class="form-value form-value2" id="secondContacts">&nbsp;</div>
				</div>
				<div class="form-item">
					<div class="form-label"><span>*</span>第一联系人手机号：</div>
					<div class="form-value form-value1" id="telephone">&nbsp;</div>
					<div class="form-label form-label2"><span>*</span>第二联系人手机号：</div>
					<div class="form-value form-value2" id="secondPhone">&nbsp;</div>
				</div>
				<div class="form-item">
					<div class="form-label"><span>*</span>单位名称：</div>
					<div class="form-value" id="companyName">&nbsp;</div>
				</div>
				<div class="form-item">
					<div class="form-label"><span>*</span>单位地址：</div>
					<div class="form-value" id="companyAddress">&nbsp;</div>
				</div>
				<div class="form-buttons">
					<a class="button btn-gray" href="../personal/my-info-update.html">修改账户信息</a>
				</div>
			</form>
			<form class="form apply-form" id="apply-form">
				<div class="credit-box credit-company">
					<div class="form-item">
						<div class="form-label"><span>*</span>公司注册资本：</div>
						<div class="form-value"><input type="text" name="money" maxlength="15" /></div>
					</div>
					<div class="form-item">
						<div class="form-label"><span>*</span>公司成立时间：</div>
						<div class="form-value"><input type="text" name="foundedDate" maxlength="15" /></div>
					</div>
					<div class="form-item">
						<div class="form-label"><span>*</span>公司性质：</div>
						<div class="form-value form-radio-group">
							<label><input type="radio" name="nature1" value="1" />央企</label>
							<label><input type="radio" name="nature1" value="2" />国企</label>
							<label><input type="radio" name="nature1" value="3" />股份制</label>
							<label><input type="radio" name="nature1" value="4" />私企</label>
							<input type="hidden" name="nature" />
						</div>
					</div>
					<div class="form-item">
						<div class="form-label"><span>*</span>拟授信额度（万/人民币）：</div>
						<div class="form-value"><input type="text" name="amountLimit" maxlength="15" /></div>
					</div>
					<div class="form-item">
						<div class="form-label"><span>*</span>拟授信用途附加说明：</div>
						<div class="form-value"><textarea name="use" maxlength="200"></textarea></div>
					</div>
				</div>
				<div class="credit-box credit-extra">
					<div class="form-item">
						<div class="form-label"><span>*</span>营业执照编码：</div>
						<div class="form-value"><input type="text" name="businessCode" maxlength="15" /></div>
					</div>
					<div class="form-item">
						<div class="form-label"><span>*</span>税务登记证编码：</div>
						<div class="form-value"><input type="text" name="taxcode" maxlength="15" /></div>
					</div>
					<div class="form-item">
						<div class="form-label"><span>*</span>组织机构代码：</div>
						<div class="form-value"><input type="text" name="orgcode" maxlength="15" /></div>
					</div>
					<div class="form-item">
						<div class="form-label"><span>*</span>上传营业执照副本：</div>
						<div class="form-value clearfix">
							<div class="form-image">
								<input id="picurl" type="file" class="file" />
								<img id="goodsImg" src="../content/images/common/d255x125-1.png" data-def="../content/images/common/d255x125-1.png" alt="" />
							</div>
							<input type="hidden" name="businessLicense" />
							<span class="form-tip">上传图片大小限制为5M内的JPG、PNG、JPEG</span>
						</div>
					</div>
					<div class="form-item">
						<div class="form-label"><span>*</span>上传税务登记证副本：</div>
						<div class="form-value clearfix">
							<div class="form-image">
								<input id="picurl" type="file" class="file" />
								<img id="goodsImg" src="../content/images/common/d255x125-1.png" data-def="../content/images/common/d255x125-1.png" alt="" />
							</div>
							<input type="hidden" name="taxcert" />
							<span class="form-tip">上传图片大小限制为5M内的JPG、PNG、JPEG</span>
						</div>
					</div>
					<div class="form-item">
						<div class="form-label"><span>*</span>上传组织机构代码证副本：</div>
						<div class="form-value clearfix">
							<div class="form-image">
								<input id="picurl" type="file" class="file" />
								<img id="goodsImg" src="../content/images/common/d255x125-1.png" data-def="../content/images/common/d255x125-1.png" alt="" />
							</div>
							<input type="hidden" name="orgcert" />
							<span class="form-tip">上传图片大小限制为5M内的JPG、PNG、JPEG</span>
						</div>
					</div>
					<div class="form-buttons">
						<input type="submit" class="button btn-secondary big" value="提交" />
					</div>
				</div>
			</form>
		</div>
	</div><!--//section-->

	<?php include '../com/footer.php'; ?>

	<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
	<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
	<script>
		seajs.use('../content/js/credit/apply');
	</script>
</body>
</html>
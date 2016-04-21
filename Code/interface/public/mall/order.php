<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>提交订单-基建商城</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/mall.css" />
</head>
<body>
<?php include '../com/header.php';?>
<div class="section">
	<div class="page-vertical clearfix">
		<div class="breadcrumbs">
			<a href="../mall/">首页</a> &gt; <a href="index.html">基建商城</a> &gt; <span>订单详情</span>
			<a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
		</div>
		<form class="form order-form" id="order-form" action="">
			<div class="order-no number-o2">确认订单</div>
			<div class="order clearfix" style='background:#fff;padding-bottom:22px'>
				<div class="form-detail" id="shipping-detail"></div>
				<div class="form-value form-radio-group" style="line-height:50px">
					<div class="number-o2-time">
						<label class="active">
							物融通大宗物流配送
						</label>
					</div>
					<div class="number-o2-time clearfix">
						<span>交货时间：</span>
						<input type="text" class="date" name="stime" checked="checked" /><span class="construction" style="padding-left:43px">(此订单支持预约配送，您可以选择指定的时间段)</span>
						<div class="calendar"><img class="date" src="../content/images/mall/time.png" width="36" height="36"/></div>
					</div>
				</div>
				<ul class="form-value form-radio-group" style="line-height:30px">
					<li class="msg-ship">温馨提示：</li>
					<li class="msg-color">1.我们会努力按照您指定的时间配送，但因天气、交通等各类因素影响，您的订单有可能会有延误现象！</li>
					<li class="msg-color">2.部分服务仅在物流配送区域提供，非配送区域无法选择！</li>
				</ul>
			</div>
            <div class="invoice">
                <div class="info">发票信息：</div>
                <div class="info-detail">
                    <div class="form-item invoice_info clearfix">
                        <div class="form-label"><span>*</span>发票信息：</div>
                        <div class="form-value form-radio-group">
                            <label id="invtype1">
                                <input type="radio" name="invType" value="0" id="tax-invoice-radio">增值税专用发票
                            </label>
                            <label id="invtype2">
                                <input type="radio" name="invType" value="1" id="invoice-radio" checked>普通发票
                            </label>
                        </div>
                    </div>

                    <!--增值税发票-->
                    <div id="tax-invoice" style="display:none"></div>
                    <!--普通发票-->
                    <div id="invoice"></div>
                </div>
            </div>

			<div class="look-shop-list">
				<div class="info">查看商品清单：</div>
				<ul class="items" id="zj-cart"></ul>
			</div>
			<div class="total-price clearfix">
				<div class="total-price-left">
					<span class="look-label">关联合同编号：</span>
					<div class="zj-select" style="width: 170px;">
						<em><i></i></em>
						<span class="default">请选择合同编号</span>
						<ul class="dropdown" id="zj-contracts">
							<li data-v="">请选择合同编号</li>
						</ul>
						<input type="hidden" name="contractSn" />
					</div>
					<div class="sel-drop">
						<img src="../content/images/customize/drop.png" />
					</div>
				</div>
				<div class="total-price-right">
					<input type="hidden" name="addressId" />
					<div class="kefu-produce"><span class="look-label">总价金额： </span><span class="look-value"><span class="c-red" id="total-price"></span> 信用B</span></div>	
					<a href="http://lc.talk99.cn/chat/chat/p.do?c=10033976&f=10043368&g=10048426" target="_blank" class="call-service">物融客服</a>
					<button type="submit" id="btn-submit" class="kefu-btm">提交订单</button>
				</div>
			</div>
		</form>
	</div>
</div><!--//section-->
<?php include '../com/footer.php';?>

<script id="zj-shipping-detail" type="text/html">
	<div class="number-o2-msg">收货人信息：</div>
	<div class="form-item" style="padding-bottom:0;padding-top:10px">
		<div class="form-label">收&nbsp;&nbsp;货&nbsp;&nbsp;人：</div>
		<div class="form-value number-o2-name"><!--[= list.name ]--></div>
	</div>
	<div class="form-item" style="padding-bottom:0">
		<div class="form-label">收货地址：</div>
		<div class="form-value number-o2-name"><!--[= list.address ]--></div>
	</div>
	<div class="form-item" style="padding-bottom:10px;border-bottom:1px solid #dadada">
		<div class="form-label">手&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;机：</div>
		<div class="form-value number-o2-name"><!--[= list.phone ]--></div>
	</div>
</script>
<script id="zj-cart-tmpl" type="text/html">
	<!--[for(i = 0; i < list.length; i++){]-->
	<li class="clearfix" id="goods-<!--[= list[i].goodsId]-->">
		<img src="<!--[= $absImg(list[i].thumb)]-->">
		<div class="look-info">
			<p><span class="look-label">商品名称：</span><span class="look-value"><!--[= list[i].name]--></span></p>
			<p><span class="look-label">商品编号：</span><span class="look-value"><!--[= list[i].wcode]--></span></span></p>
			<p>
				<!--[for(j = 0; j < list[i].attr.length; j++){]-->
				<span class="look-label"><!--[= list[i].attr[j].name]-->：</span><span class="look-value"><!--[= list[i].attr[j].value]--></span>
				<!--[}]-->
				<span class="look-label">计重方式：</span><span class="look-value"><!--[= list[i].unit]--></span>
			</p>
			<p>
				<span class="look-label">购买数量：</span><span class="look-value amount"><!--[= list[i].nums]-->件</span>
				<span class="look-label lastmarg">交易单价：</span><span class="look-value"><span class="c-red"><!--[== $formatCurrency(list[i].price)]--></span> 信用B</span>
			</p>
		</div>
		<span class="stock c-red">商品库存不足！</span>
	</li>
	<!--[}]-->
</script>
<script id="invoice-tmpl" type="text/html">
    <div class="form-item">
        <div class="form-label"><span></span>发票抬头：</div>
        <div class="form-value">
            <input type="text" name="invPayee" maxlength="50" value="<!--[= list.invPayee ]-->">
        </div>
    </div>
    <div class="form-item">
        <div class="form-label"><span></span>收票地址：</div>
        <div class="form-value">
            <input type="text" name="invAddress" maxlength="200" value="<!--[= list.invAddress ]-->">
        </div>
    </div>
    <div class="form-item">
        <div class="form-label">内<em class="e2"></em>容：</div>
        <div class="form-value">
            <textarea class="textarea" name="inv_context" maxlength="200"><!--[= list.inv_context ]--></textarea>
        </div>
    </div>
    <div class="form-item">
        <div class="form-label">备<em class="e2"></em>注：</div>
        <div class="form-value">
            <textarea class="textarea" name="inv_remark" maxlength="200"><!--[= list.inv_remark ]--></textarea>
        </div>
    </div>
    <!--[if(list.invId){]-->
	<input type="hidden" name="invId" value="<!--[= list.invId ]-->">
	<!--[}]-->
</script>
<script id="tax-invoice-tmpl" type="text/html">
    <div class="form-item">
        <div class="form-label"><span></span>公司名称：</div>
        <div class="form-value">
            <input type="text" name="invCompany" maxlength="50" value="<!--[= list.invCompany ]-->">
        </div>
    </div>
    <div class="form-item">
        <div class="form-label"><span></span>开户银行：</div>
        <div class="form-value">
            <input type="text" name="invBankName" maxlength="50" value="<!--[= list.invBankName ]-->">
        </div>
    </div>
    <div class="form-item">
        <div class="form-label"><span></span>开户银行账户：</div>
        <div class="form-value">
            <input type="text" name="invBankAccount" maxlength="50" value="<!--[= list.invBankAccount ]-->">
        </div>
    </div>
    <div class="form-item">
        <div class="form-label" style="width: 120px; margin-left: -10px;"><span></span>税务登记证号码：</div>
        <div class="form-value">
            <input type="text" name="invLicense" maxlength="50" value="<!--[= list.invLicense ]-->">
        </div>
    </div>
    <div class="form-item">
        <div class="form-label"><span></span>公司注册地址：</div>
        <div class="form-value">
            <input type="text" name="invCompanyAddr" maxlength="50" value="<!--[= list.invCompanyAddr ]-->">
        </div>
    </div>
    <div class="form-item">
        <div class="form-label"><span></span>电话号码：</div>
        <div class="form-value">
            <input type="text" name="invTel" maxlength="50" value="<!--[= list.invTel ]-->">
        </div>
    </div>
    <div class="form-item">
        <div class="form-label"><span></span>传真号码：</div>
        <div class="form-value">
            <input type="text" name="invFax" maxlength="50" value="<!--[= list.invFax ]-->" >
        </div>
    </div>
    <div class="form-item">
        <div class="form-label">内<em class="e2"></em>容：</div>
        <div class="form-value">
            <textarea class="textarea" name="inv_context" maxlength="200"><!--[= list.inv_context ]--></textarea>
        </div>
    </div>
    <div class="form-item">
        <div class="form-label">备<em class="e2"></em>注：</div>
        <div class="form-value">
            <textarea class="textarea" name="inv_remark" maxlength="200"><!--[= list.inv_remark ]--></textarea>
        </div>
    </div>
    <!--[if(list.invId){]-->
	<input type="hidden" name="invId" value="<!--[= list.invId ]-->">
	<!--[}]-->
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script src="../content/js/module/My97DatePicker/WdatePicker.js"></script>
<script>
	seajs.use('../content/js/mall/order');
</script>
</body>
</html>
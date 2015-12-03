<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>积分商城-订单详情</title>
    <link rel="stylesheet" href="../content/css/common.css" />
    <link rel="stylesheet" href="../content/css/jifen.css" />
</head>
<body>

<?php include "../com/header.php"; ?>

    <div class="section detail_bottom">
        <div class="page-vertical">       	
            <div class="breadcrumbs">
                <a href="../">首页</a> &gt; <a href="index.html">积分商城</a> &gt; <span>提交订单</span>
                <a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
            </div>
    		<div class="order order_start" id="zj-detail">
        	</div>
        </div>                     
    </div><!--//section-->   

    <?php include "../com/footer.php"; ?>
		
    <script id="zj-detail-tmpl" type="text/html">
    <div class="order_title">查看商品清单：</div>
    <div class="line"></div>
    <div class="order_detail">
    	<div class="product_list clearfix">
    		<div class="product_list_left clearfix">
    		    <div class="product_list_img"><img src="<!--[= $absImg(imageUrl)]-->" width='97' height='97'/></div>
    		    <div class="product_list_desc">
    		     	<div><span><!--[= name]--></span></div>
    		        <div>商品编号：<span><!--[= itemNo]--></span></div>
    		    </div>
    		</div>
    		<div class="product_list_right">所需积分：<b><!--[= credits]--></b></div>
        </div>
    </div>
    <div class="line"></div>
    <div class="exchange order-btn">
        <div class="count">兑换积分：<b><!--[= credits]--></b></div>
        <div class="img"><input type="submit" class="button btn-primary" value="提交订单" /></div>
    </div>
    </script>

    <script src="../content/js/module/seajs/2.2.0/sea.js"></script>
    <script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
    <script>
    seajs.use("../content/js/jifen/order");
    </script>
</body>
</html>
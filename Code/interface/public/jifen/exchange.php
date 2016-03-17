<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>积分商城-立即兑换</title>
    <link rel="stylesheet" href="../content/css/common.css" />
    <link rel="stylesheet" href="../content/css/jifen.css" />
</head>
<body>

    <?php include "../com/header.php"; ?>

    <div class="section detail_bottom">
        <div class="page-vertical">
            <div class="breadcrumbs">
                <a href="../mall/">首页</a> &gt; <a href="index.html">积分商城</a> &gt; <span>立即兑换</span>
                <a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
            </div>
            <div class="order_start" id="zj-detail"></div>
        </div>
    </div><!--//section-->    

    <?php include "../com/footer.php"; ?>

    <script id="zj-detail-tmpl" type="text/html">
	<div class="order">	  
		<div class="order_detail clearfix">      			 
        	<div class="exchange_1">商品名称：<em><!--[= name]--></em></div>    
        	<div class="exchange_2"><span class="span1">商品编号：<em><!--[= itemNo]--></em></span><span class="span2">所需积分：<b><!--[= credits]--></b></span></div>        			 				        				
    	</div>
    	<div class="line"></div>	   
		<div class="exchange">   
		 	<div class="count">总积分：<b><!--[= credits]--></b></div>
		 	<div class="img"><a class="button btn-primary" href="address.html?id=<!--[= id]-->">立即兑换</a></div>
		</div>	
	</div>
    </script>
    <script src="../content/js/module/seajs/2.2.0/sea.js"></script>
    <script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
    <script>
    seajs.use("../content/js/jifen/exchange");
    </script>
</body>
</html>

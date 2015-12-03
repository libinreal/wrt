<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>积分商城-详情</title>
    <link rel="stylesheet" href="../content/css/common.css" />
    <link rel="stylesheet" href="../content/css/jifen.css" />
</head>
<body>

    <?php include "../com/header.php"; ?>

    <div class="section detail_bottom">
    	<div class="page-vertical">
	        <div class="breadcrumbs">
	            <a href="../">首页</a> &gt; <a href="index.html">积分商城</a> &gt; <span>积分详情</span>
	            <a href="javascript:history.go(-1);" class="return">返回 &gt;</a>
	        </div>
	        <div class="detail_start" id="zj-detail"></div>
        </div>
    </div>
    <!--//section-->

    <?php include "../com/footer.php"; ?>

	<script id="zj-detail-tmpl" type="text/html">
	<div class="order"> 
		<div class="detail product_detail clearfix"> 
    		<div class="first_detail clearfix">
				<div class="left"><img src="<!--[= $absImg(imageUrl)]-->"width="290" height="289"/></div>
				<div class="right">
					<div class="title"><!--[= name]--></div>
					<div class="name">
						<div>商品货号：<em><!--[= itemNo]--></em></div>
						<div>商品重量：<em><!--[= weight]-->&nbsp;kg</em></div>
						<div>所需积分：<em><!--[= credits]--></em></div>
					</div>        								        								       	 
					<div class="line"></div> 
					<div class="img">
						<div class="img_1"><a class="button btn-primary" href="exchange.html?id=<!--[= id]-->">立即兑换</a></div>
						<div class="img_2"><a class="button btn-black" href="http://lc.talk99.cn/chat/chat/p.do?c=10033976&f=10043368&g=10048426" target="_blank">物融客服</a></div>
					</div>
				</div>
    		</div>          				        				         				    				
        </div> 
	</div>
	<ul id="tabBackground">
        <li class="active tabBackgroundActive">商品描述</li>
        <li class="tabBackgroundActive guige">商品规格</li>
        <li class="tabBackgroundActive">售后信息</li>
    </ul> 
    <div class="divBackground divContentActive">
        <p><!--[== des]--></p>  
    </div>
    <div class="divBackground guige">         
        <p><!--[== spec]--></p>    	 
    </div>
    <div class="divBackground msg">        
        <p><!--[== sales]--></p>
    </div>	  
	</script>
	<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
	<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>    
	<script>
    seajs.use("../content/js/jifen/detail");
	</script>
</body>
</html>
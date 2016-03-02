<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>首页-基建商城</title>
	<link rel="stylesheet" href="../content/css/common.css" />
	<link rel="stylesheet" href="../content/css/mall.css" />
</head>
<body>

<?php include '../com/header.php';?>

<div class="section">
	<div class="page-vertical">
		<div class="mall-header clearfix">
			<div class="userinfo" id="zj-userinfo"></div>
			<form class="search" action="search.html">
				<input type="text" class="search_input" name="key" placeholder="请输入关键字" />
				<input type="submit" class="search_bg" value="" />
			</form>
		</div>
		<div class="mall-content clearfix">
			<div class="left-bar">
				<div class="fli">商品分类</div>
				<ul class="menu" id="zj-categorys">
					<!--category start-->
						<li class="menu-item cat-1" data-code="10000000"><a class="menu-item-link" href="list-large.html?code=10000000"><span class="menu-item-text">钢材、钢绞线系列</span></a>
							<div class="shop-list-nav" id="cat-10000000" style="display: none;">
								<div class="nav-pptj-l clearfix">
									<div class="shop-list-item clearfix">
										<div class="list-title">钢材</div>
										<div class="list-detail">
											<div class="list1"> 
												<div class="small-title"><span>建筑钢材</span></div>
												<ul class="small-detail clearfix">
													<li><a href="list-large.html?code=10010101">线材</a></li>
													<li><a href="list-large.html?code=10010102">圆钢</a></li>
													<li><a href="list-large.html?code=10010103">螺纹钢</a></li>
													<li class="last"><a href="list-large.html?code=10010104">盘螺</a></li>
												</ul>
											</div>
										</div>
										<div class="list-detail">
											<div class="list1"> 
												<div class="small-title"><span>板材</span></div>
												<ul class="small-detail clearfix">
													<li><a href="list-large.html?code=10010201">热轧卷板</a></li>
													<li class="last"><a href="list-large.html?code=10010202">中厚板</a></li>
												</ul>
											</div>
										</div>
										<div class="list-detail">
											<div class="list1"> 
												<div class="small-title"><span>型材</span></div>
												<ul class="small-detail clearfix">
													<li><a href="list-large.html?code=10010301">工字钢</a></li>
													<li><a href="list-large.html?code=10010302">角钢</a></li>
													<li><a href="list-large.html?code=10010303">槽钢</a></li>
													<li><a href="list-large.html?code=10010304">H型钢</a></li>
													<li class="last"><a href="list-large.html?code=10010305">轨道钢</a></li>
												</ul>
											</div>
										</div>
										<div class="list-detail">
											<div class="list1"> 
												<div class="small-title"><span>管材</span></div>
												<ul class="small-detail clearfix">
													<li><a href="list-large.html?code=10010401">焊管</a></li>
													<li><a href="list-large.html?code=10010402">无缝钢管</a></li>
													<li><a href="list-large.html?code=10010403">方管</a></li>
													<li><a href="list-large.html?code=10010404">螺旋管</a></li>
													<li><a href="list-large.html?code=10010405">镀锌管</a></li>
													<li class="last"><a href="list-large.html?code=10010406">球墨铸铁管</a></li>
												</ul>
											</div>
										</div>
									</div>
									<div class="shop-list-item clearfix">
										<div class="list-title">钢绞线</div>
										<div class="list-detail">
											<div class="list1">
												<ul class="small-detail clearfix">
													<li><a href="list-large.html?code=10020100">预应力混凝土用钢绞线</a></li>
													<li><a href="list-large.html?code=10020200">镀锌钢绞线</a></li>
													<li class="last"><a href="list-large.html?code=10020300">铝包钢绞线</a></li>
												</ul>
											</div>
										</div>
									</div>
								</div>
								<div class="nav-pptj clearfix">
									<ul id="zj-bds-10000000"></ul>
								</div>
							</div>
						</li>
						<li class="menu-item cat-2" data-code="20000000"><a class="menu-item-link" href="list-large.html?code=20000000"><span class="menu-item-text">水泥、沥青、混凝土外加剂系列</span></a>
							<div class="shop-list-nav" id="cat-20000000" style="display: none;">
								<div class="nav-pptj-l clearfix">
									<div class="shop-list-item clearfix">
										<div class="list-title">水泥</div>
										<div class="list-detail">
											<div class="list1"> 
												<div class="small-title"><span>通用水泥</span></div>
												<ul class="small-detail clearfix">
													<li><a href="list-large.html?code=20010101">硅酸盐水泥</a></li>
													<li><a href="list-large.html?code=20010102">普通硅酸盐水泥</a></li>
													<li><a href="list-large.html?code=20010103">矿渣硅酸盐水泥</a></li>
													<li class="last"><a href="list-large.html?code=20010104">火山灰质硅酸盐水泥</a></li>
													<li><a href="list-large.html?code=20010105">粉煤灰硅酸盐水泥</a></li>
													<li class="last"><a href="list-large.html?code=20010106">复合硅酸盐水泥</a></li>
												</ul>
											</div>
										</div>
										<div class="list-detail">
											<div class="list1"> 
												<div class="small-title"><span>专用水泥</span></div>
												<ul class="small-detail clearfix">
													<li><a href="list-large.html?code=20010201">G级油井水泥</a></li>
													<li class="last"><a href="list-large.html?code=20010202">道路硅酸盐水泥</a></li>
												</ul>
											</div>
										</div>
										<div class="list-detail">
											<div class="list1"> 
												<div class="small-title"><span>特性水泥</span></div>
												<ul class="small-detail clearfix">
													<li><a href="list-large.html?code=20010301">快硬硅酸盐水泥</a></li>
													<li><a href="list-large.html?code=20010302">低热矿渣硅酸盐水泥</a></li>
													<li class="last"><a href="list-large.html?code=20010303">硫铝酸盐水泥</a></li>
												</ul>
											</div>
										</div>
									</div>
									<div class="shop-list-item clearfix">
										<div class="list-title">混泥土外加剂</div>
										<div class="list-detail">
											<div class="list1"> 
												<div class="small-title"><span>减水剂</span></div>
												<ul class="small-detail clearfix">
													<li><a href="list.html?code=20020101">普通减水剂</a></li>
													<li><a href="list.html?code=20020102">引气减水剂</a></li>
													<li><a href="list.html?code=20020103">高效减水剂</a></li>
													<li><a href="list.html?code=20020104">缓凝减水剂</a></li>
													<li class="last"><a href="list.html?code=20020105">早强减水剂</a></li>
												</ul>
											</div>
										</div>
										<div class="list-detail">
											<div class="list1"> 
												<ul class="small-detail clearfix">
													<li><a href="list.html?code=20020200">引气剂</a></li>
													<li><a href="list.html?code=20020300">防冻剂</a></li>
													<li><a href="list.html?code=20020400">膨胀剂</a></li>
													<li><a href="list.html?code=20020500">泵送剂</a></li>
													<li><a href="list.html?code=20020600">早强剂</a></li>
													<li><a href="list.html?code=20020700">缓凝剂</a></li>
													<li><a href="list.html?code=20020700">缓凝剂</a></li>
													<li class="last"><a href="list.html?code=20020800">阻锈剂</a></li>
													<li><a href="list.html?code=20020900">着色剂</a></li>
													<li><a href="list.html?code=20021000">加气剂</a></li>
													<li class="last"><a href="list.html?code=20021100">速凝剂</a></li>
												</ul>
											</div>
										</div>
									</div>
									<div class="shop-list-item clearfix">
										<div class="list-title">沥青</div>
										<div class="list-detail">
											<div class="list1">
												<ul class="small-detail clearfix">
													<li><a href="list-large.html?code=20030100">重交沥青</a></li>
													<li><a href="list-large.html?code=20030200">改性沥青</a></li>
													<li><a href="list-large.html?code=20030300">乳化沥青</a></li>
													<li class="last"><a href="list-large.html?code=20030400">基质沥青</a></li>
												</ul>
											</div>
										</div>
									</div>
								</div>
								<div class="nav-pptj clearfix">
									<ul id="zj-bds-20000000"></ul>
								</div>
							</div>
						</li>
						<li class="menu-item cat-3" data-code="30000000"><a class="menu-item-link" href="list.html?code=30000000"><span class="menu-item-text">锚具、锚杆系列</span></a>
							<div class="shop-list-nav" id="cat-30000000">
								<div class="nav-pptj-l clearfix">
									<div class="shop-list-item clearfix">
										<div class="list-title">锚具</div>
										<div class="list-detail">
											<div class="list1">
												<ul class="small-detail clearfix">
													<li><a href="list.html?code=30010100">圆形锚具</a></li>
													<li class="last"><a href="list.html?code=30010200">扁形锚具</a></li>
												</ul>
												<ul class="small-detail clearfix">
													<li><a href="list.html?code=30010300">握裹式锚具 ( P锚 ) </a></li>
													<li class="last"><a href="list.html?code=30010400">锚固体系</a></li>
												</ul>
											</div>
										</div>
									</div>
									<div class="shop-list-item clearfix">
										<div class="list-title">锚杆</div>
										<div class="list-detail">
											<div class="list1">
												<ul class="small-detail clearfix">
													<li><a href="list.html?code=30020100">插入式锚杆</a></li>
													<li><a href="list.html?code=30020200">涨壳式锚杆</a></li>
													<li class="last"><a href="list.html?code=30020300">中空注浆锚杆</a></li>
												</ul>
												<ul class="small-detail clearfix">
													<li><a href="list.html?code=30020400">自钻式锚</a></li>
													<li class="last"><a href="list.html?code=30020500">组合式锚杆</a></li>
												</ul>
											</div>
										</div>
									</div>
								</div>
								<div class="nav-pptj clearfix">
									<ul id="zj-bds-30000000"></ul>
								</div>
							</div>
						</li>
						<li class="menu-item cat-4" data-code="40000000"><a class="menu-item-link" href="list.html?code=40000000"><span class="menu-item-text">支座、伸缩缝、止水带系列</span></a>
							<div class="shop-list-nav" id="cat-40000000" style="display: none;">
								<div class="nav-pptj-l clearfix">
									<div class="shop-list-item clearfix">
										<div class="list-title">支座</div>
										<div class="list-detail">
											<div class="list1"> 
												<div class="small-title"><span>钢支座</span></div>
												<ul class="small-detail clearfix">
													<li><a href="list.html?code=40010101">平板支座</a></li>
													<li><a href="list.html?code=40010102">弧形支座</a></li>
													<li><a href="list.html?code=40010103">摇轴支座</a></li>
													<li class="last"><a href="list.html?code=40010104">辊轴支座</a></li>
												</ul>
											</div>
										</div>
										<div class="list-detail">
											<div class="list1"> 
												<div class="small-title"><span>橡胶支座</span></div>
												<ul class="small-detail clearfix">
													<li><a href="list.html?code=40010201">滑动支座</a></li>
													<li><a href="list.html?code=40010301">板式橡胶支座</a></li>
													<li><a href="list.html?code=40010302">盆式橡胶支座</a></li>
													<li class="last"><a href="list.html?code=40010303">私服板式橡胶支座</a></li>
													<li class="last"><a href="list.html?code=40010304">四氟板式橡胶支座</a></li>
												</ul>
											</div>
										</div>
										<div class="list-detail">
											<div class="list1"> 
												<ul class="small-detail clearfix">
													<li><a href="list.html?code=40010400">混凝土支座</a></li>
													<li class="last"><a href="list.html?code=40010500">铅支座</a></li>
												</ul>
											</div>
										</div>
									</div>
									<div class="shop-list-item clearfix">
										<div class="list-title">伸缩缝</div>
										<div class="list-detail">
											<div class="list1"> 
												<ul class="small-detail clearfix">
													<li><a href="list.html?code=40010600">对接式伸缩缝</a></li>
													<li><a href="list.html?code=40010700">钢制型式伸缩缝</a></li>
													<li class="last"><a href="list.html?code=40010800">板式橡胶伸缩缝</a></li>
												</ul>
												<ul class="small-detail clearfix">
													<li><a href="list.html?code=40010900">模数支承式伸缩缝</a></li>
													<li class="last"><a href="list.html?code=40011000">无缝式伸缩缝</a></li>
												</ul>
											</div>
										</div>
									</div>
									<div class="shop-list-item last clearfix">
										<div class="list-title">止水带</div>
										<div class="list-detail">
											<div class="list1"> 
												<ul class="small-detail clearfix">
													<li><a href="list.html?code=40011100">橡胶止水带</a></li>
													<li><a href="list.html?code=40011200">塑料（PVC）止水带</a></li>
													<li class="last"><a href="list.html?code=40011300">钢板止水带</a></li>
												</ul>
												<ul class="small-detail clearfix">
													<li class="last"><a href="list.html?code=40011400">橡胶加钢板止水带</a></li>
												</ul>
											</div>
										</div>
									</div>
								</div>
								<div class="nav-pptj clearfix">
									<ul id="zj-bds-40000000"></ul>
								</div>
							</div>
						</li>
						<li class="menu-item cat-5" data-code="50000000"><a class="menu-item-link" href="list.html?code=50000000"><span class="menu-item-text">土工材料、塑料管材系列</span></a>
							<div class="shop-list-nav" id="cat-50000000">
								<div class="nav-pptj-l clearfix">
									<div class="shop-list-item clearfix">
										<div class="list-title">土木材料</div>
										<div class="list-detail">
											<div class="list1">
												<ul class="small-detail clearfix">
													<li><a href="list.html?code=50010100">土工布</a></li>
													<li><a href="list.html?code=50010200">土工膜</a></li>
													<li class="last"><a href="list.html?code=50010500">土工网</a></li>
												</ul>
												<ul class="small-detail clearfix">
													<li><a href="list.html?code=50010300">土工格栅</a></li>
													<li class="last"><a href="list.html?code=50010400">土工格室</a></li>
												</ul>
											</div>
										</div>
									</div>
									<div class="shop-list-item clearfix">
										<div class="list-title">塑料管材</div>
										<div class="list-detail">
											<div class="list1">
												<ul class="small-detail clearfix">
													<li><a href="list.html?code=50020100">硬质聚氯乙烯（UPVC）管</a></li>
													<li><a href="list.html?code=50020200">氯化聚氯乙烯（CPVC）管</a></li>
													<li class="last"><a href="list.html?code=50020300">聚乙烯（PE）管</a></li>
													<li><a href="list.html?code=50020400">交联聚乙烯（PE-X）管</a></li>
													<li><a href="list.html?code=50020500">三型聚丙烯（PP-R）管</a></li>
													<li class="last"><a href="list.html?code=50020600">聚丁烯（PB）管</a></li>
													<li><a href="list.html?code=50020700">工程塑料（ABS）管</a></li>
													<li><a href="list.html?code=50020800">玻璃钢夹砂（RPM）管</a></li>
													<li class="last"><a href="list.html?code=50020900">铝塑料复合（PAP）管</a></li>
													<li class="last"><a href="list.html?code=50021000">钢塑复合（SP）管</a></li>
												</ul>
											</div>
										</div>
									</div>
								</div>
								<div class="nav-pptj clearfix">
									<ul id="zj-bds-50000000"></ul>
								</div>
							</div>
						</li>
						<li class="menu-item cat-6" data-code="60000000"><a class="menu-item-link" href="list.html?code=60000000"><span class="menu-item-text">工程机械、交通设施系列</span></a>
							<div class="shop-list-nav" id="cat-60000000">
								<div class="nav-pptj-l clearfix">
									<div class="shop-list-item clearfix">
										<div class="list-title">工程机械</div>
										<div class="list-detail">
											<div class="list1"> 
												<div class="small-title"><span>挖掘机械</span></div>
												<ul class="small-detail clearfix">
													<li><a href="list.html?code=60010101">挖掘机</a></li>
													<li><a href="list.html?code=60010102">挖沟机</a></li>
													<li class="last"><a href="list.html?code=60010103">隧洞掘进机</a></li>
												</ul>
											</div>
										</div>
										<div class="list-detail">
											<div class="list1"> 
												<div class="small-title"><span>铲土运输机械</span></div>
												<ul class="small-detail clearfix">
													<li><a href="list.html?code=60010201">推土机</a></li>
													<li><a href="list.html?code=60010202">铲运机</a></li>
													<li><a href="list.html?code=60010203">装载机</a></li>
													<li><a href="list.html?code=60010204">自卸车</a></li>
													<li><a href="list.html?code=60010205">叉装车</a></li>
													<li><a href="list.html?code=60010206">滑移装载机</a></li>
													<li class="last"><a href="list.html?code=60010207">挖掘装载机</a></li>
												</ul>
											</div>
										</div>
										<div class="list-detail">
											<div class="list1"> 
												<div class="small-title"><span>起重机械</span></div>
												<ul class="small-detail clearfix">
													<li><a href="list.html?code=60010301">履带起重机</a></li>
													<li><a href="list.html?code=60010302">越野轮胎起重机</a></li>
													<li><a href="list.html?code=60010303">门式起重机</a></li>
													<li><a href="list.html?code=60010304">汽车起重机</a></li>
													<li class="last"><a href="list.html?code=60010305">全地面起重机</a></li>
													<li><a href="list.html?code=60010306">随车起重机</a></li>
													<li><a href="list.html?code=60010307">塔式起重机</a></li>
													<li class="last"><a href="list.html?code=60010308">吊机</a></li>
												</ul>
											</div>
										</div>
										<div class="list-detail">
											<div class="list1"> 
												<div class="small-title"><span>压实机械</span></div>
												<ul class="small-detail clearfix">
													<li><a href="list.html?code=60010401">压路机</a></li>
													<li><a href="list.html?code=60010402">单钢轮压路机</a></li>
													<li><a href="list.html?code=60010403">冲击夯</a></li>
													<li><a href="list.html?code=60010404">光轮压路机</a></li>
													<li><a href="list.html?code=60010405">轮胎压路机</a></li>
													<li class="last"><a href="list.html?code=60010406">平板夯</a></li>
													<li><a href="list.html?code=60010407">三轮压路机</a></li>
													<li><a href="list.html?code=60010408">双钢轮压路机</a></li>
													<li><a href="list.html?code=60010409">小型压路机</a></li>
													<li><a href="list.html?code=60010410">压实机</a></li>
													<li class="last"><a href="list.html?code=60010411">捣固机</a></li>
												</ul>
											</div>
										</div>
										<div class="list-detail">
											<div class="list1"> 
												<div class="small-title"><span>桩工机械</span></div>
												<ul class="small-detail clearfix">
													<li><a href="list.html?code=60010501">打桩锤</a></li>
													<li><a href="list.html?code=60010502">连续翻墙抓斗</a></li>
													<li><a href="list.html?code=60010503">连续翻墙抓机</a></li>
													<li><a href="list.html?code=60010504">锚杆钻机</a></li>
													<li><a href="list.html?code=60010505">潜孔钻机</a></li>
													<li class="last"><a href="list.html?code=60010506">水平定向钻</a></li>
													<li><a href="list.html?code=60010507">旋挖钻机</a></li>
													<li><a href="list.html?code=60010508">压桩机</a></li>
													<li class="last"><a href="list.html?code=60010509">常螺旋钻孔机</a></li>
												</ul>
											</div>
										</div>
										<div class="list-detail">
											<div class="list1"> 
												<div class="small-title"><span>钢筋混凝土机械</span></div>
												<ul class="small-detail clearfix">
													<li><a href="list.html?code=60010601">混凝土搅拌机</a></li>
													<li><a href="list.html?code=60010602">混凝土搅拌钻</a></li>
													<li><a href="list.html?code=60010603">混凝土喷射机</a></li>
													<li><a href="list.html?code=60010604">搅拌运输车</a></li>
													<li><a href="list.html?code=60010605">泵车</a></li>
													<li class="last"><a href="list.html?code=60010606">车载泵</a></li>
													<li><a href="list.html?code=60010607">粉粒物料车</a></li>
													<li class="last"><a href="list.html?code=60010609">拖泵</a></li>
												</ul>
											</div>
										</div>
										<div class="list-detail">
											<div class="list1"> 
												<div class="small-title"><span>路面机械</span></div>
												<ul class="small-detail clearfix">
													<li class="last"><a href="list.html?code=60010701">平整机</a></li>
												</ul>
											</div>
										</div>
										<div class="list-detail">
											<div class="list1"> 
												<div class="small-title"><span>凿岩机械</span></div>
												<ul class="small-detail clearfix">
													<li><a href="list.html?code=60010801">凿岩台车</a></li>
													<li class="last"><a href="list.html?code=60010802">凿岩机</a></li>
												</ul>
											</div>
										</div>
									</div>
									<div class="shop-list-item clearfix">
										<div class="list-title">交通设施</div>
										<div class="list-detail">
											<div class="list1"> 
												<div class="small-title"><span>高速公路交通设施</span></div>
												<ul class="small-detail clearfix">
													<li><a href="list.html?code=60020101">防眩板</a></li>
													<li><a href="list.html?code=60020102">护栏栅</a></li>
													<li><a href="list.html?code=60020103">防眩网</a></li>
													<li><a href="list.html?code=60020104">防撞护栏</a></li>
													<li class="last"><a href="list.html?code=60020105">标线标牌</a></li>
												</ul>
											</div>
										</div>
										<div class="list-detail">
											<div class="list1"> 
												<div class="small-title"><span>市政道路交通设施</span></div>
												<ul class="small-detail clearfix">
													<li><a href="list.html?code=60020201">塑料路锥</a></li>
													<li><a href="list.html?code=60020202">橡胶路锥</a></li>
													<li><a href="list.html?code=60020203">隔离墩</a></li>
													<li><a href="list.html?code=60020204">减速带</a></li>
													<li><a href="list.html?code=60020205">信号灯</a></li>
													<li><a href="list.html?code=60020206">反光标志牌</a></li>
													<li class="last"><a href="list.html?code=60020207">道钉</a></li>
													<li><a href="list.html?code=60020208">三角警示架</a></li>
													<li><a href="list.html?code=60020209">广角镜</a></li>
													<li class="last"><a href="list.html?code=60020210">标志标牌</a></li>
												</ul>
											</div>
										</div>
									</div>
								</div>
								<div class="nav-pptj clearfix">
									<ul id="zj-bds-60000000"></ul>
								</div>
							</div>
						</li>
						<li class="menu-item cat-7" data-code="70000000"><a class="menu-item-link" href="list.html?code=70000000"><span class="menu-item-text">科技产品系列
					</span></a>
							<div class="shop-list-nav" id="cat-70000000">
								<div class="nav-pptj-l clearfix">
									<div class="shop-list-item clearfix">
										<div class="list-title">科技产品</div>
										<div class="list-detail">
											<div class="list1"> 
												<div class="small-title"><span>创新应用</span></div>
												<ul class="small-detail clearfix">
													<li><a href="list.html?code=70010100">SPP声测管</a></li>
													<li class="last"><a href="list.html?code=70010200">蟹钳式三角钢管支架</a></li>
												</ul>
											</div>
										</div>
										<div class="list-detail">
											<div class="list1"> 
												<div class="small-title"><span>高新材料</span></div>
												<ul class="small-detail clearfix">
													<li class="last"><a href="list.html?code=70010300">彩色橡胶路面</a></li>
												</ul>
											</div>
										</div>
										<div class="list-detail">
											<div class="list1"> 
												<div class="small-title"><span>智能科技</span></div>
												<ul class="small-detail clearfix">
													<li class="last"><a href="list.html?code=70010400">智能科技</a></li>
												</ul>
											</div>
										</div>
									</div>
								</div>
								<div class="nav-pptj clearfix">
									<ul id="zj-bds-70000000"></ul>
								</div>
							</div>
						</li>
					<!--category end-->
				</ul>
				<div class="lli">
					<div class="clearfix">
						<a class="integral" href="../jifen/">积分商城</a>
						<a class="mobile" href="../ydwr/">移动物融</a>
					</div>
					<div class="clearfix">
						<a class="service" href="../help/people-service.html">客服中心</a>
						<a class="help" href="../help/">帮助中心</a>
					</div>
				</div>
			</div>
			<div class="right-content">
				<div class="clearfix">
					<div class="img-list scroll-img">
						<div class="items" id="zj-ad"></div>
						<div class="navi"></div>
					</div>
					<div class="jj_notice">
						<div class="jj_notice_title clearfix">
							<a class="fr" href="../notice/list.html?type=2002">更多></a>
							<h3>公告栏</h3>
						</div>
						<div id="zj-notice"></div>
					</div>
				</div>
				<div id="zj-goods" style="padding-bottom: 20px;"></div>
			</div>
		</div>
	</div>
</div><!--//section-->

<?php include '../com/footer.php';?>

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

<script id="zj-userinfo-tmpl" type="text/html">
	<div class="avator">
		<a class="auth" href="../personal/my-info.html"><img src="<!--[= account.icon]-->" /></a>
	</div>
	<div class="userinfo-left">
		<div class="userinfo-item clearfix">
			<div class="userinfo-xydj">
				<a class="auth" href="../personal/my-creditb-level.html"><span 
					class="label label-grade">信用等级：</span><span 
					class="value"><!--[= account.creditLevel]--></span></a>
			</div>
			<div class="userinfo-cged">
				<a class="auth" href="../personal/my-creditb.html"><span 
					class="label">可用采购额度：</span><span 
					class="value c-red" id="buy-amt">...</span><span 
					class="unit" id="buy-unit">&nbsp;信用B</span></a>
			</div>
			<div class="userinfo-szdq">
				<span class="label">所在地区：</span><span 
					class="value"><!--[= (area && area.name) || '--']--></span><a 
					class="operate" href="#">更换</a>
			</div>
		</div>
		<div class="userinfo-item clearfix">
			<div class="userinfo-yhm">
				<a class="auth" href="../personal/"><span 
					class="label label-user">用户名：</span><span 
					class="value"><!--[= account.account]--></span></a>
			</div>
			<div class="userinfo-link">
				<a class="auth link" href="../personal/">个人中心</a>
				<a class="auth link" href="../personal/my-order.html">我的订单</a>
			</div>
			<div style="display: none;">
				<span class="label label-cart">购物车：</span><a href="cart.html" class="value value-cart"><em id="c3art-no">0</em>件商品</a>
			</div>
			<div id="settleup-2013">
				<dl class="settleup">
					<dt class="ld"><s></s><span class="shopping1"><span id="cart-no">0</span></span><a class="auth" href="cart.html">去购物车结算</a><b></b></dt>
					<dd>
						<div id="settleup-empty" class="prompt" style="display:none;"><div class="nogoods"><b></b>购物车中还没有商品，赶紧选购吧！</div></div>
						<div id="settleup-content"></div>
					</dd>
				</dl>
			</div>
		</div>
	</div>
</script>
<script id="zj-nouser-tmpl" type="text/html">
	<div class="avator">
		<a class="authority" href="../personal/my-info.html"><img src="../content/images/mall/person_logo.png" /></a>
	</div>
	<div class="userinfo-left">
		<div class="userinfo-item clearfix">
			<div class="userinfo-xydj">
				<a class="auth" href="../personal/my-creditb-level.html"><span 
					class="label label-grade">信用等级：</span><span 
					class="value">--</span></a>
			</div>
			<div class="userinfo-cged">
				<a class="auth" href="../personal/my-creditb.html"><span 
					class="label">可用采购额度：</span><span 
					class="value c-red" id="buy-amt">--</span></a>
			</div>
			<div class="userinfo-szdq">
				<span class="label">所在地区：</span><span 
					class="value"><!--[= (area && area.name) || '--']--></span><a 
					class="operate" href="#">更换</a>
			</div>
		</div>
		<div class="userinfo-item clearfix">
			<div class="userinfo-yhm">
				<a class="auth" href="../personal/"><span 
					class="label label-user">用户名：</span><span 
					class="value">--</span></a>
			</div>
			<div class="userinfo-link">
				<a class="auth link" href="../personal/">个人中心</a>
				<a class="auth link" href="../personal/my-order.html">我的订单</a>
			</div>
			<div id="settleup-2013">
				<dl class="settleup">
					<dt class="ld"><s></s><span class="shopping1"><span id="cart-no">0</span></span><a class="auth" href="cart.html">去购物车结算</a><b></b></dt>
					<dd>
						<div id="settleup-empty" class="prompt"><div class="nogoods"><b></b>购物车中还没有商品，赶紧选购吧！</div></div>
					</dd>
				</dl>
			</div>
		</div>
	</div>
</script>
<script id="zj-cart-good-tmpl" type="text/html">
	<div class="smt"><h4 class="fl">最新加入的商品</h4></div>
	<div class="smc">
		<ul id="mcart-mz">
			<li class="dt"><div class="fl"></div><div class="fr"><em>小计：￥<!--[= (price * nums).toFixed(2)]--></em></div></li>
			<li>
				<div class="p-img fl"><a href="<!--[= $getCartLink(code, id)]-->" target="_blank"><img src="<!--[= $absImg(thumb)]-->" width="50" height="50" alt=""></a></div>
				<div class="p-name fl"><span></span><a href="<!--[= $getCartLink(code, id)]-->" title="<!--[= goodsName]-->" target="_blank"><!--[= goodsName]--></a></div>
				<div class="p-detail fr ar"><span class="p-price"><strong>￥<!--[= price]--></strong>×<!--[= nums]--></span></div>
			</li>
		</ul>
	</div>
	<div class="smb t-r">共<b><!--[= total]--></b>件商品　共计<strong>￥ <!--[= (price * nums).toFixed(2)]--></strong><br><a href="cart.html" title="去购物车结算" id="btn-payforgoods">去购物车结算</a></div>
</script>
<script id="zj-province-tmpl" type="text/html">
    <!--[for(i = 0; i < list.length; i ++) {]-->
    <li><button data-id="<!--[= list[i].id]-->" type="<!--[= list[i].type]-->">
    	<!--[= list[i].name]--></button>
    </li>
    <!--[}]-->
</script>
<script id="zj-notice-tmpl" type="text/html">
	<!--[for(i = 0; i < list.length; i++) {]-->
	<a href="../notice/detail.html?type=2002&id=<!--[= list[i].id]-->" title="<!--[= list[i].title]-->"><span><!--[= list[i].title]--></span><em><!--[= $formatDate(list[i].createAt,'MM/dd')]--></em></a>
	<!--[}]-->
</script>
<script id="zj-category-tmpl" type="text/html">
	<!--[for(i = 0; i < list.length; i++) {]-->
	<li class="menu-item cat-<!--[= (i + 1)]-->" data-code="<!--[= list[i].code]-->"><a class="menu-item-link" href="<!--[= $getLink(list[i].code)]-->"><span class="menu-item-text"><!--[= list[i].name]--></span></a>
		<div class="shop-list-nav" id="cat-<!--[= list[i].code]-->">
			<div class="nav-pptj-l clearfix">
			<!--[for(j = 0; j < list[i].children.length; j++) {]-->
				<div class="shop-list-item clearfix">
					<div class="list-title"><a href="<!--[= $getLink(list[i].children[j].code)]-->"><!--[= list[i].children[j].name]--></a></div>
					<!--[if(list[i].children[j].children){]-->
					<!--[if(list[i].children[j].children[0].children){]-->
					<!--[for(k = 0; k < list[i].children[j].children.length; k++) {]-->
					<div class="list-detail">
						<div class="list1"> 
							<div class="small-title"><span><!--[= list[i].children[j].children[k].name]--></span></div>
							<!--[if(list[i].children[j].children[k].children){]-->
							<ul class="small-detail clearfix">
								<!--[for(m = 0; m < list[i].children[j].children[k].children.length; m++) {]-->
								<li><a href="<!--[= $getLink(list[i].children[j].children[k].children[m].code)]-->"><!--[= list[i].children[j].children[k].children[m].name]--></a></li>
								<!--[}]-->
							</ul>
							<!--[}else{]-->
							<ul class="small-detail clearfix">
								<li><a href="<!--[= $getLink(list[i].children[j].children[k].code)]-->"><!--[= list[i].children[j].children[k].name]--></a></li>
							</ul>
							<!--[}]-->
						</div>
					</div>
					<!--[}]-->
					<!--[}else{]-->
					<div class="list-detail">
						<div class="list1">
							<ul class="small-detail clearfix">
								<!--[for(k = 0; k < list[i].children[j].children.length; k++) {]-->
								<li><a href="<!--[= $getLink(list[i].children[j].children[k].code)]-->"><!--[= list[i].children[j].children[k].name]--></a></li>
								<!--[}]-->
							</ul>
						</div>
					</div>
					<!--[}]-->
					<!--[}else{]-->
					<div class="list-detail">没有数据</div>
					<!--[}]-->
				</div>
			<!--[}]-->
			</div>
			<div class="nav-pptj clearfix">
				<ul id="zj-bds-<!--[= list[i].code]-->">
					<li class="brand-tj">品牌推荐</li>
				</ul>
			</div>
		</div>
	</li>
	<!--[}]-->
</script>
<script id="zj-bds-tmpl" type="text/html">
	<li class="brand-tj">品牌推荐</li>
	<!--[for(i = 0; i < list.length; i++) {]-->
		<li class="img">
			<!--[if(list[i].adlink){]-->
			<a href="<!--[= list[i].adlink]-->" target="_blank"><img src="<!--[= $absImg(list[i].adimg)]-->"/></a>
			<!--[}else{]-->
			<a href="javascript:;"><img src="<!--[= $absImg(list[i].adimg)]-->" /></a>
			<!--[}]-->
		</li>
	<!--[}]-->
</script>
<script id="zj-brand-tmpl" type="text/html">
	<!--[for(i = 0; i < list.length; i++) {]-->
		<a href="<!--[= $getLink(code,list[i].id)]-->" title="<!--[= list[i].name]-->"><img src="<!--[= $absImg(list[i].logo)]-->"/></a>	
	<!--[}]-->	 
</script>
<script id="zj-goods-tmpl" type="text/html">
	<!--[for(i = 0; i < list.length; i++) {]-->
	<div class="sort-list floor<!--[= (i + 1)]--> clearfix">
	<a class="title clearfix" href="<!--[= $getLink(list[i].type)]-->">
		<span class="floor-num"><!--[= (i + 1)]-->F</span><span class="floor-name"><!--[= list[i].name]--></span>
	</a>
	<div class="detail-list clearfix">
		<!--[for(j = 0; j < 4; j++) {]-->
		<!--[if(list[i].goods[j]){]-->
			<a class="detail" href="<!--[= $getLink(list[i].goods[j].code,'',list[i].goods[j].id)]-->" title="<!--[= list[i].goods[j].name]-->">
				<div class="thumb"><img src="<!--[= $absImg(list[i].goods[j].thumb)]-->"></div>
				<div class="ch-title"><!--[= list[i].goods[j].name || '--']--></div>
				<div class="price">
					<!--[if(status === true){]-->
					<span class="price-label">交易单价</span><span
					 class="price-value"><!--[== $formatCurrency(list[i].goods[j].vipPrice)]--></span><span
					 class="price-unit">&nbsp;信用B</span>
					<!--[}else{]-->
					<span class="price-label">交易单价</span><span
					 class="price-unit">登录后显示</span>
					<!--[}]-->
				</div>
			</a>
		<!--[}else{]-->
				<div class="detail" href="javascript:;"></div>
		<!--[}]-->
		<!--[}]-->
	</div>
	
	<ul class="recommend">
		<li>诚信供应商</li>
		<!--[for(k = 0; k < list[i].brands.length; k++) {]-->
		<li class="pptj line clearfix">
		    <div class="img-l">
		    	<a href="<!--[= $getLink(list[i].type,list[i].brands[k].id)]-->"><img src="<!--[= $absImg(list[i].brands[k].logo)]-->"/></a>
		    </div>
		    <div class="word-r"><a href="<!--[= $getLink(list[i].type,list[i].brands[k].id)]-->" title="<!--[= list[i].brands[k].name]-->"><!--[= list[i].brands[k].name]--></a></div> 
		</li>
		<!--[}]-->
	</ul>
	</div>
	<!--[}]-->
</script>

<script src="../content/js/module/seajs/2.2.0/sea.js"></script>
<script src="../content/js/module/seajs/2.2.0/sea-config.js"></script>
<script>
	seajs.use('../content/js/mall/index');
</script>

</body>
</html>
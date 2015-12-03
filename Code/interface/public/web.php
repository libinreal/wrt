<?php

error_reporting(E_ALL);

try {

    /**
     * Read the configuration
     */
    $config = include __DIR__ . "/../app/config/config.php";

    /**
     * Read auto-loader
     */
    include __DIR__ . "/../app/config/loader.php";

    /**
     * Read services
     */
    include __DIR__ . "/../app/config/services.php";

    /**
     * Handle the request
     */
    $application = new \Phalcon\Mvc\Application($di);

	$routers = array(
		"/credit/apply.php",//信用池
		"/credit/additional.php",
		"/credit/manage.php",
		"/credit/more.php",
		"/credit/history.php",
		"/credit/status.php",

		"/mall/list.php",//基建商城
		"/mall/list-larg.php",
		"/mall/order.php",
		"/mall/detail.php",
		"/mall/cart.php",
		"/mall/search-list.php",
		"/mall/search-normal.php",
		"/mall/search.php",
		"/mall/address-add.php",
		"/mall/address.php",

		"/personal/index.php",//个人中心
		"/personal/add-address.php",
		"/personal/my-address.php",
		"/personal/my-bill.php",
		"/personal/my-collect.php",
		"/personal/my-creditb-apply.php",
		"/personal/my-creditb-bill.php",
		"/personal/my-creditb-history.php",
		"/personal/my-creditb-level.php",
		"/personal/my-creditb.php",
		"/personal/my-creditb-customize.php",
		"/personal/my-info-update.php",
		"/personal/my-info.php",
		"/personal/my-jifen.php",
		"/personal/my-order-acceptance.php",
		"/personal/my-order-detail.php",
		"/personal/my-order-logistics.php",
		"/personal/my-order-reconciliations.php",
		"/personal/my-order-sub.php",
		"/personal/my-order.php",
		"/personal/my-project-detail.php",
		"/personal/my-project.php",
		"/personal/my-security.php",
		"/personal/suggest-order.php",

		"/customize/index.php",//定制专区
		"/customize/apply.php",

		"/jifen/index.php",//积分商城
		"/jifen/address.php",
		"/jifen/detail.php",
		"/jifen/exchange.php",
		"/jifen/list.php",
		"/jifen/order.php"
	);

	// 记录流量信息
    $auth = $di->get("session")->get("auth");

    $url = $_SERVER['PHP_SELF'];
    $request = $di->get('request');
    Stats::visitStats($request);

	if(!isset($auth) && in_array($url ,$routers)){
		header("location:"."/index.html#open-login");
        exit();
	}
} catch (Exception $e) {
}
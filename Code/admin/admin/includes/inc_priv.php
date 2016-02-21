<?php

//权限对照表

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

//基建商城 管理权限
$purview['01_goods_list']        = array('goods_manage', 'remove_back');
$purview['02_goods_add']         = 'goods_manage';
$purview['05_comment_manage']    = 'comment_priv';
$purview['11_goods_trash']       = array('goods_manage', 'remove_back');
$purview['12_mall_goods']           = 'mall_goods_manage';
$purview['19_brand_recommend_list'] = 'brand_recommend_manage';

//订单管理权限
$purview['02_order_list']        = 'order_view';
$purview['03_order_query']       = 'order_view';
$purview['11_order_reminder']    = 'order_view';
$purview['03_recommendorder_list']  = 'credit_evaluation_state';
// 积分商城
$purview['01_exchange_category']    = "exchange_category_manage";
$purview['02_exchange_category_add']      = 'exchange_goods_manage';
$purview['03_exchange_goods']       = array("exchange_goods_manage","remove_exchange_back");
$purview['05_exchange_order_list']        = 'exchange_order_view';
$purview['06_exchange_delivery_order']    = 'exchange_delivery_view';
$purview['07_exchange_goods_trash'] = 'exchange_goods_manage';
$purview['04_exchange_goods_add'] = 'exchange_goods_add';
// 内容管理部分  广告 物融新闻 物融公告 帮助中心 客服
$purview['ad_list']              = 'ad_manage';
//物融新闻管理权限
$purview['02_wrnewscat_list']   = 'wrnews_cat';
$purview['03_wrnews_list']      = 'wrnews_manage';
//商城公告管理权限
$purview['02_noticecat_list']   = 'notice_cat';
$purview['03_notice_list']      = 'notice_manage';
//帮助中心管理权限
$purview['02_helpcentercat_list']   = 'helpcenter_cat';
$purview['03_helpcenter_list']      = 'helpcenter_manage';

// 基础数据
$purview['01_category_list']    = array('cat_manage', 'cat_drop');   //分类添加、分类转移和删除
$purview['05_goods_type']       = 'attr_manage'; //商品属性
$purview['03_brand_list']       = 'brand_manage';//厂商权限
$purview['01_shop_config']      = 'shop_config';// 积分设置
$purview['05_area_list']        = 'area_manage';//地区管理
$purview['suppliers_list']      = 'suppliers_manage'; // 供货商
$purview['21_goods_shipping_set'] = 'goods_shipping_set';//物流费用设置
//信用池管理
$purview['01_credit_evaluation']  = "credit_evaluation_state";
$purview['02_credit_quota_add']   = "credit_quota_add_state";
$purview['03_purchase_quota_add'] = "purchase_quota_add_state";
$purview['04_credit_intrinfo']    = 'credit_intrinfo_manage';
$purview['05_credit_recovery_history'] = 'credit_evaluation_state';
$purview['06_bill_notice']        = 'credit_evaluation_state';
$purview['07_contract']           = 'credit_evaluation_state';
$purview['08_credit_class']           = 'credit_evaluation_state';


//推荐订单管理权限
$purview['03_orderrecommend_list']   = 'orderrecommend_manage';
//定制申请状态管理
$purview['customize_list'] = "customize_state";
$purview['custom_list'] = 'custom_state';
//工程资讯状态管理
$purview['engineering_bidding'] = "engineering_bidding";
$purview['recommend_goods'] = "recommend_goods";
$purview['engineering_links'] = "engineering_links";
//客服中心
$purview['service_complaint'] = "complaint_state";
$purview['service_appointment'] = "appointment_state";


//会员管理权限
$purview['03_users_list']        = 'users_manage';
$purview['04_users_add']         = 'users_manage';
//权限管理
$purview['admin_logs']           = array('logs_manage', 'logs_drop');
$purview['admin_list']           = array('admin_manage', 'admin_drop');
$purview['admin_role']             = 'role_manage';

//$purview['agency_list']          = 'agency_manage';
//报表统计权限
$purview['credit_analysis']  = 'credit_analysis_stats';
$purview['flow_stats']       = 'client_flow_stats';
$purview['report_guest'] 	 = 'client_guest_stats';
$purview['report_order']     = 'sale_order_stats';
$purview['report_sell']      = 'sale_general_stats';
$purview['sale_list']		 = 'sale_list_info_stats';
$purview['sales_analysis']   = 'credit_analysis_stats';

$purview['01_contract_insert'] = 'contract_insert';
$purview['02_contract_list'] = 'contract_list';
$purview['05_contract_supplier_add'] = 'contract_supplier_add';
$purview['06_contract_supplier_list'] = 'contract_supplier_list';

$purview['01_credit_list'] = 'credit_list';

$purview['01_batch_price'] = 'batch_price';
$purview['02_price_list'] = 'price_list';

$purview['01_applyCredit_list'] = 'applyCredit_list';
$purview['02_applyCredit_rlist'] = 'applyCredit_rlist';

$purview['01_bill_manage_insert'] = 'bill_manage_insert';
$purview['02_bill_manage_list'] = 'bill_manage_list';
$purview['03_bill_manage_generate'] = 'bill_manage_generate';
$purview['04_bill_manage_order_list'] = 'bill_manage_generate';
$purview['05_bill_manage_repay_list'] = 'bill_manage_repay_list';
$purview['06_bill_manage_user_list'] = 'bill_manage_user_list';

$purview['01_bank_insert'] = 'bank_insert';
$purview['02_bank_list'] = 'bank_list';

//销售订单
$purview['01_sale_order_list'] = 'sale_order_list';
$purview['02_sale_order_suborder_all'] = 'sale_order_suborder_all';
//采购订单
$purview['01_supplier_order_list'] = 'supplier_order_list';

$purview['01_order_pay_list'] = 'order_pay_list';

$purview['01_order_recipient_comp'] = 'order_recipient_comp';
$purview['02_order_recipient_list'] = 'order_recipient_list';

$purview['01_purchase_order_manage'] = 'purchase_order_manage';


?>
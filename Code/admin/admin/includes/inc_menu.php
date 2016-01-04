<?php
//管理中心菜单数组

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

// 基建商城
$modules['01_cat_and_goods']['01_goods_list']       = 'goods.php?act=list';         // 商品列表
$modules['01_cat_and_goods']['02_goods_add']        = 'goods.php?act=add';          // 添加商品
$modules['01_cat_and_goods']['05_comment_manage']   = 'comment_manage.php?act=list';
$modules['01_cat_and_goods']['11_goods_trash']      = 'goods.php?act=trash';        // 商品回收站
$modules['01_cat_and_goods']['12_mall_goods'] = 'mall_goods.php?act=list';
$modules['01_cat_and_goods']['19_brand_recommend_list'] = 'brand_recommend.php?act=list';

// 基建商城订单
$modules['02_order']['02_order_list']               = 'order.php?act=list';
$modules['02_order']['03_order_query']              = 'order.php?act=order_query';
$modules['02_order']['11_order_reminder']           = 'order.php?act=reminder';
$modules['02_order']['03_recommendorder_list']           = 'recommendorder.php?act=list';

//积分商城管理
$modules['03_exchange']['01_exchange_category'] = 'exchange_category.php?act=list';
//$modules['04_exchange']['02_exchange_category_add'] = 'exchange_category.php?act=add';
$modules['03_exchange']['03_exchange_goods'] = 'exchange_goods.php?act=list';
$modules['03_exchange']['04_exchange_goods_add'] = "exchange_goods.php?act=add";
$modules['03_exchange']['06_exchange_delivery_order']  = 'exchange_order.php?act=delivery_list';
$modules['03_exchange']['05_exchange_order_list'] = 'exchange_order.php?act=list';
$modules['03_exchange']['07_exchange_goods_trash'] = "exchange_goods.php?act=trash";
//$modules['04_exchange']['03_exchange_order_query'] = 'exchange_order.php?act=order_query';

//物融新闻管理 内容管理
$modules['04_wrnews']['ad_list']                    = 'ads.php?act=list';
$modules['04_wrnews']['03_wrnews_list']           = 'wrnews.php?act=list';
$modules['04_wrnews']['02_wrnewscat_list']        = 'wrnewscat.php?act=list';
$modules['04_wrnews']['03_notice_list']           = 'notice.php?act=list';
$modules['04_wrnews']['02_noticecat_list']        = 'noticecat.php?act=list';
$modules['04_wrnews']['03_helpcenter_list']           = 'helpcenter.php?act=list';
$modules['04_wrnews']['02_helpcentercat_list']        = 'helpcentercat.php?act=list';

//基础数据
$modules['05_baseData']['01_category_list'] = 'category.php?act=list';//物料类别
$modules['05_baseData']['05_goods_type'] = 'goods_type.php?act=manage';//物料属性
$modules['05_baseData']['03_brand_list'] = 'brand.php?act=list';//厂商列表
$modules['05_baseData']['suppliers_list'] = 'suppliers.php?act=list'; // 供货商
$modules['05_baseData']['05_area_list'] = 'area_manage.php?act=list';// 地区列表
$modules['05_baseData']['01_shop_config'] = 'shop_config.php?act=list_edit'; //系统配置项

//信用池
$modules['06_credit']['01_credit_evaluation']       = 'evaluation.php?act=list';
$modules['06_credit']['02_credit_quota_add']        = 'credit_quota_add.php?act=list';
$modules['06_credit']['03_purchase_quota_add']      = 'purchase_quota_add.php?act=list';
$modules['06_credit']['04_credit_intrinfo']         = 'credit_intrinfo.php?act=edit&id=1';
$modules['06_credit']['05_credit_recovery_history'] = 'recovery_history.php?act=list';
$modules['06_credit']['06_bill_notice']             = 'bill_notice.php?act=list';
$modules['06_credit']['07_contract']                = 'contract.php?act=list';
$modules['06_credit']['08_credit_class']            = 'credit_class.php?act=edit&id=2';


//定制申请  ok
$modules['07_customize']['customize_list']  =  'customize.php?act=list';
$modules['07_customize']['custom_list']     = 'custom.php?act=list';
//工程咨询  ok
$modules['08_engineering']['engineering_bidding'] =  'bidding.php?act=list';
$modules['08_engineering']['engineering_links'] =  'links.php?act=list';
$modules['08_engineering']['recommend_goods'] =  'recommend.php?act=list';

//客服中心  ok
$modules['09_service']['service_complaint'] =  'complaint.php?act=list';
$modules['09_service']['service_appointment'] =  'appointment.php?act=list';

// 会员管理 ok
$modules['10_members']['03_users_list']             = 'users.php?act=list';
$modules['10_members']['04_users_add']              = 'users.php?act=add';

// 权限管理 ok
$modules['11_priv_admin']['admin_logs']             = 'admin_logs.php?act=list';
$modules['11_priv_admin']['admin_list']             = 'privilege.php?act=list';
$modules['11_priv_admin']['admin_role']             = 'role.php?act=list';

// 报表统计 ok
$modules['12_stats']['flow_stats']                  = 'flow_stats.php?act=view';
$modules['12_stats']['report_guest']                = 'guest_stats.php?act=list';//客户
$modules['12_stats']['report_order']                = 'order_stats.php?act=list';
$modules['12_stats']['report_sell']                 = 'sale_general.php?act=list';
$modules['12_stats']['sale_list']                   = 'sale_list.php?act=list';
$modules['12_stats']['credit_analysis']             = 'credit_analysis.php?act=list';// 信用
$modules['12_stats']['sales_analysis']              = 'sales_analysis.php?act=list';

//合同管理
$modules['13_contract']['01_contract_insert']          = 'contract_manage.php?act=contractInsert';
$modules['13_contract']['02_contract_list']         = 'contract_manage.php?act=contractList';
$modules['13_contract']['05_contract_supplier_add']     = 'contract_manage.php?act=supplierSet';
$modules['13_contract']['06_contract_supplier_list']    = 'contract_manage.php?act=supplierList';

//授信管理
$modules['14_credit']['01_credit_list']    = 'credit_manage.php?act=list';

?>

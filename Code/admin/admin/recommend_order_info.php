<?php

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH.'languages/' .$_CFG['lang']. '/admin/recommendorder.php');

/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = strtolower(trim($_REQUEST['act']));
}
$smarty -> assign('lang',$_LANG);
$conFnum = trim($_REQUEST['conFnum']);
if ($_REQUEST['act'] == 'list') {
    admin_priv('recommend_order_manage');
    $smarty->assign('ur_here',     $_LANG['recommend_order_info']);
    $smarty->assign('action_link', array('href'=>'recommendorder.php?act=list', 'text' => $_LANG['03_recommendorder_list']));
    $smarty->assign('full_page',   1);
    $sql = " SELECT odr.*,u.contacts,user_name ".
           " FROM ".$GLOBALS['ecs']->table('order_recommend')." AS odr ".
           " LEFT JOIN ".$GLOBALS['ecs']->table('users')." AS u ON u.customNo=odr.cusFnum ".
           " WHERE odr.conFnum='".$conFnum."'";
    $orderInfo = $GLOBALS['db']->getAll($sql);
    foreach ($orderInfo as $key=>$val) {
        $orderInfo[$key]['conCount'] = price_format($val['conCount']);
    }
    $smarty -> assign('orderInfo',$orderInfo);

    assign_query_info();
    $smarty->display('recommend_order_info.html');
}

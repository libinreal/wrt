<?php

/**
 * ECSHOP 客户统计
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: guest_stats.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/admin/statistic.php');

/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

/*------------------------------------------------------ */
//-- 客户统计列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 权限判断 */
    admin_priv('client_guest_stats');
    // 取得会员总数
    $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table("users");
    $res = $db->getCol($sql);
    $user_num   = $res[0];
    // 有过订单的会员数
    $sql = 'SELECT COUNT(DISTINCT user_id) FROM ' .$ecs->table('order_info').
           " WHERE parent_order_sn=0 AND user_id > 0  AND order_status IN (1,2,3,4)" ;
    $have_order_usernum = $db->getOne($sql);
    $user_all_order = array();
    //会员订单总数
    $sql2 = 'SELECT COUNT(DISTINCT order_id) as order_num FROM ' .$ecs->table('order_info').
        " WHERE parent_order_sn=0 AND user_id > 0  AND order_status IN (1,2,3,4)" ;
    $order = $GLOBALS['db']->getCol($sql2);
    $user_all_order['order_num'] = $order[0];
    // 会员购物总额
    $sql3 = 'SELECT SUM(order_amount) as turnover FROM ' .$ecs->table('order_info').
        " WHERE parent_order_sn=0 AND user_id > 0  AND order_status IN (1,2,3,4)" ;
    $user_order = $GLOBALS['db']->getCol($sql3);
    $user_all_order['turnover'] = $user_order[0];

    /* 赋值到模板 */
    $smarty->assign('user_num',            $user_num);                    // 会员总数
    $smarty->assign('have_order_usernum',  $have_order_usernum);          // 有过订单的会员数
    $smarty->assign('user_order_turnover', $user_all_order['order_num']); // 会员总订单数
    $smarty->assign('user_all_turnover',   price_format($user_all_order['turnover']));  //会员购物总额
    /* 每会员订单数 */
    $smarty->assign('ave_user_ordernum',  $user_num > 0 ? sprintf("%0.2f", $user_all_order['order_num'] / $user_num) : 0);
    /* 每会员购物额 */
    $smarty->assign('ave_user_turnover',  $user_num > 0 ? price_format($user_all_order['turnover'] / $user_num) : 0);
    $smarty -> assign("user_ratio", $have_order_usernum>0 ? sprintf("%0.2f",$have_order_usernum/$user_num):0);

    $smarty->assign('all_order',          $user_all_order);    //所有订单总数以及所有购物总额
    $smarty->assign('ur_here',            $_LANG['report_guest']);
    $smarty->assign('lang',               $_LANG);

    assign_query_info();
    $smarty->display('guest_stats.htm');
}

?>
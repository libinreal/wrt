<?php

/**
 * ECSHOP 销售概况
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: sale_general.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/admin/statistic.php');
$smarty->assign('lang', $_LANG);

/* 权限判断 */
admin_priv('sale_general_stats');

/* act操作项的初始化 */
if (empty($_REQUEST['act']) || !in_array($_REQUEST['act'], array('list', 'download')))
{
    $_REQUEST['act'] = 'list';
}

/* 取得查询类型和查询时间段 */
if (empty($_POST['query_by_year']) && empty($_POST['query_by_month']))
{
    if (empty($_GET['query_type']))
    {
        /* 默认当年的月走势 */
        $query_type = 'month';
        $start_time = local_mktime(0, 0, 0, 1, 1, intval(date('Y')));
        $end_time   = gmtime();
    }
    else
    {
        /* 下载时的参数 */
        $query_type = $_GET['query_type'];
        $start_time = $_GET['start_time'];
        $end_time   = $_GET['end_time'];
    }
}
else
{
    if (isset($_POST['query_by_year']))
    {
        /* 年走势 */
        $query_type = 'year';
        $start_time = local_mktime(0, 0, 0, 1, 1, intval($_POST['year_beginYear']));
        $end_time   = local_mktime(23, 59, 59, 12, 31, intval($_POST['year_endYear']));
    }
    else
    {
        /* 月走势 */
        $query_type = 'month';
        $start_time = local_mktime(0, 0, 0, intval($_POST['month_beginMonth']), 1, intval($_POST['month_beginYear']));
        $end_time   = local_mktime(23, 59, 59, intval($_POST['month_endMonth']), 1, intval($_POST['month_endYear']));
        $end_time   = local_mktime(23, 59, 59, intval($_POST['month_endMonth']), date('t', $end_time), intval($_POST['month_endYear']));

    }
}

/* 分组统计订单数和销售额：已发货时间为准 */
$format = ($query_type == 'year') ? '%Y' : '%Y-%m';
$sql = "SELECT DATE_FORMAT(FROM_UNIXTIME(add_time), '$format') AS period, COUNT(*) AS order_count, " .
            "SUM(order_amount) AS order_amount " .
        "FROM " . $ecs->table('order_info') .
        " WHERE parent_order_sn=0 AND user_id > 0 AND order_status in(1,2,3,4) " .
        " AND add_time >= '$start_time' AND add_time <= '$end_time'" .
        " GROUP BY period ";
$data_list = $db->getAll($sql);
//echo $sql;
/*------------------------------------------------------ */
//-- 显示统计信息
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 赋值查询时间段 */
    $smarty->assign('start_time',   local_date('Y-m-d', $start_time));
    $smarty->assign('end_time',     local_date('Y-m-d', $end_time));

    /* 赋值统计数据 */
    $xml = "<chart caption='' xAxisName='%s' showValues='0' decimals='0' formatNumberScale='0'>%s</chart>";
    $set = "<set label='%s' value='%s' />";
    $i = 0;
    $data_count  = '';
    $data_amount = '';
    foreach ($data_list as $data)
    {
        $data_count  .= sprintf($set, $data['period'], $data['order_count'], chart_color($i));
        $data_amount .= sprintf($set, $data['period'], $data['order_amount'], chart_color($i));
        $i++;
    }

    $smarty->assign('data_count',  sprintf($xml, '', $data_count)); // 订单数统计数据
    $smarty->assign('data_amount', sprintf($xml, '', $data_amount));    // 销售额统计数据

    $smarty->assign('data_count_name',  $_LANG['order_count_trend']);
    $smarty->assign('data_amount_name',  $_LANG['order_amount_trend']);

    /* 根据查询类型生成文件名 */
    if ($query_type == 'year')
    {
        $filename = date('Y', $start_time) . "_" . date('Y', $end_time) . '_report';
    }
    else
    {
       $filename = date('Ym', $start_time) . "_" . date('Ym', $end_time) . '_report';
    }

    /* 显示模板 */
    $smarty->assign('ur_here', $_LANG['report_sell']);
    assign_query_info();
    $smarty->display('sale_general.htm');
}

?>
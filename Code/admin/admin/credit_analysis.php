<?php
// 统计报表 信用分析

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['actt'] = strtolower(trim($_REQUEST['act']));
}
if ($_REQUEST['act'] == 'list') {
    admin_priv('credit_analysis_stats');

    $today      = strtotime(local_date('Y-m-d'));   //本地时间
    $start_date = $today - 86400 * 6;
    $end_date   = $today + 86400;
    $credit_analysis = get_credit_analysis();

    $smarty->assign('ur_here',$_LANG['credit_analysis']);
    $smarty->assign('start_date',local_date($_CFG['date_format'],$start_date));
    $smarty->assign('end_date',local_date($_CFG['date_format'],$end_date));
    $smarty->assign('cfg_lang',$_CFG['lang']);
    $smarty->assign('full_page',1);

    $smarty->assign('credit_analysis', $credit_analysis['credit_analysis']);
    $smarty->assign('filter',       $credit_analysis['filter']);
    $smarty->assign('record_count', $credit_analysis['record_count']);
    $smarty->assign('page_count',   $credit_analysis['page_count']);

    assign_query_info();
    $smarty->display('credit_analysis.html');
}
elseif ($_REQUEST['act'] == 'query') {
    admin_priv('credit_analysis_stats');
    $filter['start_date'] = empty($_REQUEST['start_date']) ? '' : local_strtotime($_REQUEST['start_date']);
    $filter['end_date'] = empty($_REQUEST['end_date']) ? '' : local_strtotime($_REQUEST['end_date']);
    $filter['credit_type'] =  $_REQUEST['credit_type'];
    $credit_analysis = get_credit_analysis();
    $smarty->assign('credit_analysis', $credit_analysis['credit_analysis']);
    $smarty->assign('filter',       $credit_analysis['filter']);
    $smarty->assign('record_count', $credit_analysis['record_count']);
    $smarty->assign('page_count',   $credit_analysis['page_count']);

    make_json_result($smarty->fetch('credit_analysis.html'), '', array('filter' => $credit_analysis['filter'], 'page_count' => $credit_analysis['page_count']));
}
/**
 * 信用额度追加记录
 */
function get_credit_analysis($is_pagination = true) {

    $filter['start_date'] = empty($_REQUEST['start_date']) ? '' : local_strtotime($_REQUEST['start_date']);
    $filter['end_date'] = empty($_REQUEST['end_date']) ? '' : local_strtotime($_REQUEST['end_date']);
    $filter['credit_type'] =  empty($_REQUEST['credit_type']) ? '' : intval($_REQUEST['credit_type']);

    $where = '';
    if (!empty($filter['start_date'])) {
        $where .= " AND a.createAt>".$filter['start_date']." ";
    }
    if (!empty($filter['end_date'])) {
        $where .= " AND a.createAt<".$filter['end_date']." ";
    }
    if (!empty($filter['credit_type'])) {
        $where .= " AND a.type=".$filter['credit_type']." ";
    }
    $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('users')." AS u ".
            " INNER JOIN ".$GLOBALS['ecs']->table('apply')." AS a ON u.user_id=a.user_id ".
            " WHERE a.`status`=1 ".$where;

    $filter['record_count'] = $GLOBALS['db']->getOne($sql);
    $filter = page_and_size($filter);

    $sql = " SELECT u.customNo,a.* FROM ".$GLOBALS['ecs']->table('users')." AS u ".
           " INNER JOIN ".$GLOBALS['ecs']->table('apply')." AS a ON u.user_id=a.user_id ".
            " WHERE a.`status`=1".$where." ORDER BY createAt desc ";
    if($is_pagination) {
        $sql .= " LIMIT " . $filter['start'] . ', ' . $filter['page_size'];
    }
    $credit_analysis = $GLOBALS['db']->getAll($sql);

    //echo $sql;
    foreach($credit_analysis as $key=>$item) {
        $credit_analysis[$key]['createAt']  = local_date($GLOBALS['_CFG']['time_format'], $credit_analysis[$key]['createAt']);
    }
    $arr = array('credit_analysis' => $credit_analysis, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    return $arr;
}
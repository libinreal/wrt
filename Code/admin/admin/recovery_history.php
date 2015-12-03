<?php

/**
 * 信用回复历史记录程序文件
 * $Author: xy $
 * $Id: recovery_history.php 2014-09-15 xy $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/*初始化数据交换对象 */
$exc   = new exchange($ecs->table("credit_change_log"), $db, 'id', 'billNO');

/*------------------------------------------------------ */
//-- 列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 取得过滤条件 */
    $filter = array();
    $smarty->assign('ur_here',      $_LANG['recovery_history_list']);
    $chanKind_list = array(0=>'扣减', 1=>'恢复');
    $smarty->assign('chanKind_list',     $chanKind_list);
    $smarty->assign('full_page',    1);
    $smarty->assign('filter',       $filter);

    $recovery_history_list = get_recovery_historylist();

    $smarty->assign('recovery_history_list',    $recovery_history_list['arr']);
    $smarty->assign('filter',          $recovery_history_list['filter']);
    $smarty->assign('record_count',    $recovery_history_list['record_count']);
    $smarty->assign('page_count',      $recovery_history_list['page_count']);
    $sort_flag  = sort_flag($recovery_history_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('recovery_history_list.htm');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    /* 取得过滤条件 */
    $filter = array();
    $smarty->assign('ur_here',      $_LANG['recovery_history_list']);
    $chanKind_list = array(0=>'扣减', 1=>'恢复');
    $smarty->assign('chanKind_list',     $chanKind_list);
    $smarty->assign('filter',       $filter);

    $recovery_history_list = get_recovery_historylist();

    $smarty->assign('recovery_history_list',    $recovery_history_list['arr']);
    $smarty->assign('filter',          $recovery_history_list['filter']);
    $smarty->assign('record_count',    $recovery_history_list['record_count']);
    $smarty->assign('page_count',      $recovery_history_list['page_count']);

    /* 获取商品类型存在规格的类型 */
    $sort_flag  = sort_flag($recovery_history_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('recovery_history_list.htm'), '',
        array('filter' => $recovery_history_list['filter'], 'page_count' => $recovery_history_list['page_count']));
}

/* 获得定制列表 */
function get_recovery_historylist()
{
    $result = get_filter();
    if ($result === false)
    {
        $filter = array();
        $filter['keyword']    = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
        $filter['starttime'] = empty($_REQUEST['starttime']) ? '' : $_REQUEST['starttime'];
        $filter['endtime'] = empty($_REQUEST['endtime']) ? '' : $_REQUEST['endtime'];
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keyword'] = json_str_iconv($filter['keyword']);
            $filter['starttime'] = gmstr2time($filter['starttime']);
            $filter['endtime'] = gmstr2time($filter['endtime']);
        }

        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $where = '';
        if (!empty($filter['keyword']))
        {
            $where = " AND u.user_name LIKE '%" . mysql_like_quote($filter['keyword']) . "%'";
        }
        if ($_REQUEST['chanKind']!='' && ($_REQUEST['chanKind'] == '0' || $_REQUEST['chanKind'] == '1') )
        {
            $where .= " AND e.chanKind = " . intval($_REQUEST['chanKind']);
        }
        if (!empty($filter['starttime']))
        {
            $where .= " AND e.chanDate >= ".$filter['starttime'];
        }
        if (!empty($filter['endtime']))
        {
            $where .= " AND e.chanDate <= ".$filter['endtime'];
        }
        /* 定制总数 */
        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('credit_chang_log'). 'AS e '.
            'LEFT JOIN'. $GLOBALS['ecs']->table('users'). 'AS u ON e.cusFnum = u.customNo ' .
            'WHERE 1 ' .$where;

        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        $filter = page_and_size($filter);
        /* 获取定制数据 */
        $sql = 'SELECT e.*,u.user_name,u.companyName FROM ' .$GLOBALS['ecs']->table('credit_chang_log'). 'AS e '.
            'LEFT JOIN'. $GLOBALS['ecs']->table('users'). 'AS u ON e.cusFnum = u.customNo ' .
            'WHERE 1 ' .$where. ' ORDER by '.$filter['sort_by'].' '.$filter['sort_order'];
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    $arr = array();
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);
    $chanKind_list = array(0=>'扣减', 1=>'恢复');
    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
        $rows['chanDate'] = local_date($GLOBALS['_CFG']['time_format'], $rows['chanDate']);
        $rows['chanKind'] = $chanKind_list[$rows['chanKind']];
        $arr[] = $rows;
    }

    return array('arr' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}


?>
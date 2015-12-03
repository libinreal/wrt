<?php

/**
 * 票据到期兑付提醒程序文件
 * $Author: xy $
 * $Id: bill_notice.php 2014-09-15 xy $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/*初始化数据交换对象 */
$exc   = new exchange($ecs->table("bill_notice"), $db, 'id', 'cusFnum');

/*------------------------------------------------------ */
//-- 列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    admin_priv('credit_evaluation_state');
    /* 取得过滤条件 */
    $filter = array();
    $smarty->assign('ur_here',      $_LANG['bill_notice_list']);
    $smarty->assign('full_page',    1);
    $smarty->assign('filter',       $filter);

    $bill_notice_list = get_bill_noticelist();

    $smarty->assign('bill_notice_list',    $bill_notice_list['arr']);
    $smarty->assign('filter',          $bill_notice_list['filter']);
    $smarty->assign('record_count',    $bill_notice_list['record_count']);
    $smarty->assign('page_count',      $bill_notice_list['page_count']);
    $sort_flag  = sort_flag($bill_notice_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('bill_notice_list.htm');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    admin_priv('credit_evaluation_state');
    /* 取得过滤条件 */
    $filter = array();
    $smarty->assign('ur_here',      $_LANG['bill_notice_list']);
    $smarty->assign('filter',       $filter);

    $bill_notice_list = get_bill_noticelist();

    $smarty->assign('bill_notice_list',    $bill_notice_list['arr']);
    $smarty->assign('filter',          $bill_notice_list['filter']);
    $smarty->assign('record_count',    $bill_notice_list['record_count']);
    $smarty->assign('page_count',      $bill_notice_list['page_count']);

    /* 获取商品类型存在规格的类型 */
    $sort_flag  = sort_flag($bill_notice_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('bill_notice_list.htm'), '',
        array('filter' => $bill_notice_list['filter'], 'page_count' => $bill_notice_list['page_count']));
}

/* 获得定制列表 */
function get_bill_noticelist()
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
        if (!empty($filter['starttime']))
        {
            $where .= " AND e.billEndDate >= ".$filter['starttime'];
        }
        if (!empty($filter['endtime']))
        {
            $where .= " AND e.billEndDate <= ".$filter['endtime'];
        }
        /* 定制总数 */
        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('bill_notice'). 'AS e '.
            'LEFT JOIN'. $GLOBALS['ecs']->table('users'). 'AS u ON e.cusFnum = u.customNo ' .
            'WHERE 1 ' .$where;

        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        $filter = page_and_size($filter);
        /* 获取定制数据 */
        $sql = 'SELECT e.*,u.user_name,u.companyName FROM ' .$GLOBALS['ecs']->table('bill_notice'). 'AS e '.
            'INNER JOIN'. $GLOBALS['ecs']->table('users'). 'AS u ON e.cusFnum = u.customNo ' .
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
    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
        $rows['billStrDate'] = local_date($GLOBALS['_CFG']['time_format'], $rows['billStrDate']);
        $rows['billEndDate'] = local_date($GLOBALS['_CFG']['time_format'], $rows['billEndDate']);
        $arr[] = $rows;
    }

    return array('arr' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}


?>
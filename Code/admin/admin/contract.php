<?php

/**
 * 合同数据程序文件
 * $Author: xy $
 * $Id: contract.php 2014-09-18 xy $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/*初始化数据交换对象 */
$exc   = new exchange($ecs->table("contract"), $db, 'id', 'cusFnum');

/*------------------------------------------------------ */
//-- 列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    admin_priv('credit_evaluation_state');
    /* 取得过滤条件 */
    $filter = array();
    $smarty->assign('ur_here',      $_LANG['contract_list']);
    $smarty->assign('full_page',    1);
    $smarty->assign('filter',       $filter);

    $contract_list = get_contractlist();
    $smarty->assign('contract_list',    $contract_list['arr']);
    $smarty->assign('filter',          $contract_list['filter']);
    $smarty->assign('record_count',    $contract_list['record_count']);
    $smarty->assign('page_count',      $contract_list['page_count']);
    $sort_flag  = sort_flag($contract_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('contract_list.htm');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    admin_priv('credit_evaluation_state');
    /* 取得过滤条件 */
    $filter = array();
    $smarty->assign('ur_here',      $_LANG['contract_list']);
    $smarty->assign('filter',       $filter);

    $contract_list = get_contractlist();

    $smarty->assign('contract_list',    $contract_list['arr']);
    $smarty->assign('filter',          $contract_list['filter']);
    $smarty->assign('record_count',    $contract_list['record_count']);
    $smarty->assign('page_count',      $contract_list['page_count']);

    /* 获取商品类型存在规格的类型 */
    $sort_flag  = sort_flag($contract_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('contract_list.htm'), '',
        array('filter' => $contract_list['filter'], 'page_count' => $contract_list['page_count']));
}

/* 获得定制列表 */
function get_contractlist()
{
    $result = get_filter();
    if ($result === false)
    {
        $filter = array();
        $filter['keyword'] = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keyword'] = json_str_iconv($filter['keyword']);
        }
        $filter['conState'] = empty($_REQUEST['conState']) ? 0 : intval($_REQUEST['conState']);
        $filter['cusFnum'] = empty($_REQUEST['cusFnum']) ? '' :trim($_REQUEST['cusFnum']);
        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $where = '';
        if (!empty($filter['keyword']))
        {
            $where = " AND u.user_name LIKE '%" . mysql_like_quote($filter['keyword']) . "%'";
        }
        if ($filter['conState'])
        {
            $where .= " AND e.conState = " . $filter['conState'];
        }
        if(!empty($filter['cusFnum'])) {
            $where .= " AND e.cusFnum  LIKE '%" . $filter['cusFnum']."%'";
        }
        /* 定制总数 */
        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('contract'). 'AS e '.
            'LEFT JOIN'. $GLOBALS['ecs']->table('users'). 'AS u ON e.cusFnum = u.customNo ' .
            'WHERE 1 ' .$where;

        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        $filter = page_and_size($filter);
        /* 获取定制数据 */
        $sql = 'SELECT e.*,u.user_name,u.customNo FROM ' .$GLOBALS['ecs']->table('contract'). 'AS e '.
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
    $state = array('1'=>'保存状态', '2'=>'提交审核', '3'=>'已审核');
    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
        $rows['Banks'] = unserialize($rows['Banks']);
        $rows['Mats'] = unserialize($rows['Mats']);
        $rows['conState'] = $state[$rows['conState']];
        $arr[] = $rows;
    }
    return array('arr' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}


?>
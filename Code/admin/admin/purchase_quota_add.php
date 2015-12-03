<?php

/**
 * 采购额度追加申请程序文件
 * $Author: xy $
 * $Id: purchase_quota_add.php 2014-09-15 xy $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/*初始化数据交换对象 */
$exc   = new exchange($ecs->table("apply"), $db, 'id', 'name');

/*------------------------------------------------------ */
//-- 列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 取得过滤条件 */
    $filter = array();
    $smarty->assign('ur_here',      $_LANG['purchase_quotaadd_list']);
    $type_list = array(1=>'订单相关', 2=>'售后相关', 3=>'投诉与建议');
    $smarty->assign('type_list',     $type_list);
    $smarty->assign('full_page',    1);
    $smarty->assign('filter',       $filter);

    $purchase_quotaadd_list = get_purchase_quotaaddlist();

    $smarty->assign('purchase_quotaadd_list',    $purchase_quotaadd_list['arr']);
    $smarty->assign('filter',          $purchase_quotaadd_list['filter']);
    $smarty->assign('record_count',    $purchase_quotaadd_list['record_count']);
    $smarty->assign('page_count',      $purchase_quotaadd_list['page_count']);
    $sort_flag  = sort_flag($purchase_quotaadd_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('purchase_quotaadd_list.htm');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    /* 取得过滤条件 */
    $filter = array();
    $smarty->assign('ur_here',      $_LANG['purchase_quotaadd_list']);
    $type_list = array(1=>'建议与改进', 2=>'商品投诉', 3=>'商城客服投诉', 4=>'物流投诉');
    $smarty->assign('type_list',     $type_list);
    $smarty->assign('filter',       $filter);

    $purchase_quotaadd_list = get_purchase_quotaaddlist();

    $smarty->assign('purchase_quotaadd_list',    $purchase_quotaadd_list['arr']);
    $smarty->assign('filter',          $purchase_quotaadd_list['filter']);
    $smarty->assign('record_count',    $purchase_quotaadd_list['record_count']);
    $smarty->assign('page_count',      $purchase_quotaadd_list['page_count']);

    /* 获取商品类型存在规格的类型 */
    $sort_flag  = sort_flag($purchase_quotaadd_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('purchase_quotaadd_list.htm'), '',
        array('filter' => $purchase_quotaadd_list['filter'], 'page_count' => $purchase_quotaadd_list['page_count']));
}

/*------------------------------------------------------ */
//-- 追加定制信息列表列表
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'info'){
    /* 根据订单id或订单号查询订单信息 */
    if (isset($_REQUEST['comid']))
    {
        $info = get_purchase_quotaadd_info();
    }

    $smarty->assign('ur_here',      $_LANG['purchase_quotaadd_info']);
    $action_link = ($_REQUEST['act'] == 'list') ? add_link($code) : array('href' => 'purchase_quota_add.php?act=list', 'text' => $_LANG['purchase_quotaadd_list']);
    $smarty->assign('action_link',  $action_link);
    $smarty->assign('info',    $info);

    assign_query_info();
    $smarty->display('purchase_quotaadd_info.htm');
}

/*------------------------------------------------------ */
//-- 修改定制状态
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'state')
{
    /* 权限判断 */
    admin_priv('purchase_quota_add_state');
    $id = intval($_REQUEST['comid']);
    $remark = addslashes($_POST['remark']);
    if ($exc->edit("status=1, remark='$remark'", $id))
    {
        $sql1 = "SELECT * FROM " .$ecs->table('apply'). " WHERE id='$id'";
        $msg = $db->GetRow($sql1);
        clear_cache_files();
        admin_log($id, 'state', 'apply');
        echo json_encode($msg);
    }
    else
    {
        echo $db->error();
    }
}
elseif ($_REQUEST['act'] == 'edit_remark')
{
    check_authz_json('purchase_quota_add_state');

    $id    = intval($_POST['id']);
    $remark = addslashes($_POST['remark']);
    if ($exc->edit("remark = '$remark'", $id)){
        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'purchase_quota_add.php?act=info&comid=' .$id ;

        $note = sprintf($_LANG['remarkedit_succeed'], stripslashes($_POST['id']));
        admin_log($_POST['id'], 'edit', 'apply');
        clear_cache_files();
        sys_msg($note, 0, $link);
    }
    else
    {
        die($db->error());
    }
}

/* 获得定制列表 */
function get_purchase_quotaaddlist()
{
    $result = get_filter();
    if ($result === false)
    {
        $filter = array();
        $filter['starttime'] = empty($_REQUEST['starttime']) ? '' : $_REQUEST['starttime'];
        $filter['endtime'] = empty($_REQUEST['endtime']) ? '' : $_REQUEST['endtime'];
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['starttime'] = gmstr2time($filter['starttime']);
            $filter['endtime'] = gmstr2time($filter['endtime']);
        }
        $filter['keyword']    = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keyword'] = json_str_iconv($filter['keyword']);
        }
        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $where = '';
        if (!empty($filter['keyword']))
        {
            $where = " AND name LIKE '%" . mysql_like_quote($filter['keyword']) . "%'";
        }
        if ($filter['type_id'])
        {
            $where .= " AND type_id = " . $filter['type_id'];
        }
        if (!empty($filter['starttime']))
        {
            $where .= " AND createAt >= ".$filter['starttime'];
        }
        if (!empty($filter['endtime']))
        {
            $where .= " AND createAt <= ".$filter['endtime'];
        }
        $type_id = " AND type = 1";
        /* 定制总数 */
        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('apply').
            'WHERE 1 '. $type_id  .$where;

        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        $filter = page_and_size($filter);
        /* 获取定制数据 */
        $sql = 'SELECT * FROM ' .$GLOBALS['ecs']->table('apply').
            'WHERE 1 ' . $type_id .$where. ' ORDER by '.$filter['sort_by'].' '.$filter['sort_order'];

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
        $rows['createAt'] = local_date($GLOBALS['_CFG']['time_format'], $rows['createAt']);
        $arr[] = $rows;
    }

    return array('arr' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

/* 获得详细信息 */
function get_purchase_quotaadd_info(){
    $result = get_filter();
    if ($result === false)
    {
        $filter = array();
        $filter['id'] = empty($_REQUEST['comid']) ? 0 : intval($_REQUEST['comid']);

        $where = '';
        if ($filter['id'])
        {
            $where = " AND id=".$filter['id'];
        }
        $type_id = " AND type = 1";
        /* 获取定制数据 */
        $sql = 'SELECT * FROM ' .$GLOBALS['ecs']->table('apply'). 'WHERE 1 ' . $type_id .$where;

        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    $info = $GLOBALS['db']->getRow($sql);
    $info['remark'] = stripslashes($info['remark']);
    $info['createAt'] = local_date($GLOBALS['_CFG']['time_format'], $info['createAt']);
    return $info;
}

?>
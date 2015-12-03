<?php

/**
 * 管理中心预约电话程序文件
 * $Author: xy $
 * $Id: appointment.php 2014-09-14 xy $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/*初始化数据交换对象 */
$exc   = new exchange($ecs->table("appointment"), $db, 'id', 'c_name');

/*------------------------------------------------------ */
//-- 列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 取得过滤条件 */
    $filter = array();
    $smarty->assign('ur_here',      $_LANG['appointment_list']);
    $type_list = array(1=>'订单相关', 2=>'售后相关', 3=>'投诉与建议');
    $smarty->assign('type_list',     $type_list);
    $smarty->assign('full_page',    1);
    $smarty->assign('filter',       $filter);

    $appointment_list = get_appointmentlist();

    $smarty->assign('appointment_list',    $appointment_list['arr']);
    $smarty->assign('filter',          $appointment_list['filter']);
    $smarty->assign('record_count',    $appointment_list['record_count']);
    $smarty->assign('page_count',      $appointment_list['page_count']);
    $sort_flag  = sort_flag($appointment_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('appointment_list.htm');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    /* 取得过滤条件 */
    $filter = array();
    $smarty->assign('ur_here',      $_LANG['appointment_list']);
    $type_list = array(1=>'建议与改进', 2=>'商品投诉', 3=>'商城客服投诉', 4=>'物流投诉');
    $smarty->assign('type_list',     $type_list);
    $smarty->assign('filter',       $filter);

    $appointment_list = get_appointmentlist();

    $smarty->assign('appointment_list',    $appointment_list['arr']);
    $smarty->assign('filter',          $appointment_list['filter']);
    $smarty->assign('record_count',    $appointment_list['record_count']);
    $smarty->assign('page_count',      $appointment_list['page_count']);

    /* 获取商品类型存在规格的类型 */
    $sort_flag  = sort_flag($appointment_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('appointment_list.htm'), '',
        array('filter' => $appointment_list['filter'], 'page_count' => $appointment_list['page_count']));
}

/*------------------------------------------------------ */
//-- 追加定制信息列表列表
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'info'){
    /* 根据订单id或订单号查询订单信息 */
    if (isset($_REQUEST['comid']))
    {
        $info = get_appointment_info();
    }

    $smarty->assign('ur_here',      $_LANG['appointment_info']);
    $action_link = ($_REQUEST['act'] == 'list') ? add_link($code) : array('href' => 'appointment.php?act=list', 'text' => $_LANG['appointment_list']);
    $smarty->assign('action_link',  $action_link);
    $smarty->assign('info',    $info);

    assign_query_info();
    $smarty->display('appointment_info.htm');
}

/*------------------------------------------------------ */
//-- 修改定制状态
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'state')
{
    /* 权限判断 */
    admin_priv('appointment_state');
    $id = intval($_REQUEST['comid']);
    $userid = $_SESSION['admin_id'];
    $remark = addslashes($_POST['remark']);
    if ($exc->edit("state=1, remark='$remark', user_id='$userid'", $id))
    {
        $sql1 = "SELECT * FROM " .$ecs->table('appointment'). " WHERE id='$id'";
        $msg = $db->GetRow($sql1);
        clear_cache_files();
        admin_log($id, 'state', 'appointment');
        echo json_encode($msg);
    }
    else
    {
        echo $db->error();
    }
}
elseif ($_REQUEST['act'] == 'edit_remark')
{
    check_authz_json('appointment_state');

    $id    = intval($_POST['id']);
    $remark = addslashes($_POST['remark']);
    if ($exc->edit("remark = '$remark'", $id)){
        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'appointment.php?act=info&comid=' .$id ;

        $note = sprintf($_LANG['remarkedit_succeed'], stripslashes($_POST['id']));
        admin_log($_POST['id'], 'edit', 'appointment');
        clear_cache_files();
        sys_msg($note, 0, $link);
    }
    else
    {
        die($db->error());
    }
}

/* 获得定制列表 */
function get_appointmentlist()
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

        $filter['type_id'] = empty($_REQUEST['type_id']) ? 0 : intval($_REQUEST['type_id']);
        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $where = '';
        if ($filter['type_id'])
        {
            $where = " AND type_id = " . $filter['type_id'];
        }
        if (!empty($filter['starttime']))
        {
            $where .= " AND createAt >= ".$filter['starttime'];
        }
        if (!empty($filter['endtime']))
        {
            $where .= " AND createAt <= ".$filter['endtime'];
        }

        /* 定制总数 */
        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('appointment').
            'WHERE 1 ' .$where;

        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        $filter = page_and_size($filter);
        /* 获取定制数据 */
        $sql = 'SELECT * FROM ' .$GLOBALS['ecs']->table('appointment').
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
    $type_list = array(1=>'订单相关', 2=>'售后相关', 3=>'投诉与建议');
    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
        $rows['createAt'] = local_date("Y-m-d H:i", $rows['createAt']);
        $rows['callback_time'] = local_date("Y-m-d H:i", $rows['callback_time']);
        $rows['type_id'] = $type_list[$rows['type_id']];
        $arr[] = $rows;
    }

    return array('arr' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

/* 获得详细信息 */
function get_appointment_info(){
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

        /* 获取定制数据 */
        $sql = 'SELECT * FROM ' .$GLOBALS['ecs']->table('appointment'). 'WHERE 1 '.$where;

        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    $info = $GLOBALS['db']->getRow($sql);
    $info['remark'] = stripslashes($info['remark']);
    $info['createAt'] = local_date("Y-m-d H:i", $info['createAt']);
    $info['callback_time'] = local_date("Y-m-d H:i", $info['callback_time']);
    $type_list = array(1=>'订单相关', 2=>'售后相关', 3=>'投诉与建议');
    $info['type_id'] = $type_list[$info['type_id']];
    return $info;
}

?>
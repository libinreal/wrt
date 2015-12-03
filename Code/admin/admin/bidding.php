<?php

/**
 * 工程招标
 * $Author: xy $
 * $Id: bidding.php 2014-09-11 xy $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/*初始化数据交换对象 */
$exc   = new exchange($ecs->table("bidding"), $db, 'id', 'name');

/*------------------------------------------------------ */
//-- 列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 取得过滤条件 */
    $filter = array();
    $type_list = array('ZB_JZ', 'ZB_SZ', 'ZB_TL', 'ZB_GL', 'ZB_GH', 'ZB_SL', 'NZJ');
    $smarty->assign('type_list',  $type_list);
    $smarty->assign('ur_here',      $_LANG['engineering_bidding_list']);
    $smarty->assign('action_link',  array('text' => $_LANG['engineering_bidding_add'], 'href' => 'bidding.php?act=add'));
    $smarty->assign('full_page',    1);
    $smarty->assign('filter',       $filter);

    $biddings_list = get_biddings_list();

    $smarty->assign('biddings_list',    $biddings_list['arr']);
    $smarty->assign('filter',          $biddings_list['filter']);
    $smarty->assign('record_count',    $biddings_list['record_count']);
    $smarty->assign('page_count',      $biddings_list['page_count']);

    $sort_flag  = sort_flag($biddings_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('bidding_list.htm');
}

/*------------------------------------------------------ */
//-- 翻页，排序
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    check_authz_json('engineering_bidding');

    $biddings_list = get_biddings_list();

    $smarty->assign('biddings_list',    $biddings_list['arr']);
    $smarty->assign('filter',          $biddings_list['filter']);
    $smarty->assign('record_count',    $biddings_list['record_count']);
    $smarty->assign('page_count',      $biddings_list['page_count']);

    $sort_flag  = sort_flag($biddings_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('bidding_list.htm'), '',
        array('filter' => $biddings_list['filter'], 'page_count' => $biddings_list['page_count']));
}

/*------------------------------------------------------ */
//-- 添加
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'add')
{
    /* 权限判断 */
    admin_priv('engineering_bidding');

    $type_list = array('ZB_JZ', 'ZB_SZ', 'ZB_TL', 'ZB_GL', 'ZB_GH', 'ZB_SL', 'NZJ');
    $area_list = get_regions(1,1);
    $smarty->assign('type_list',  $type_list);
    $smarty->assign('area_list',  $area_list);
    $smarty->assign('ur_here',     $_LANG['engineering_bidding_add']);
    $smarty->assign('action_link', array('href'=>'bidding.php?act=list', 'text' => $_LANG['engineering_bidding_list']));
    $smarty->assign('action',      'add');
    $smarty->assign('form_action',    'insert');

    assign_query_info();
    $smarty->display('bidding_info.htm');
}

/*------------------------------------------------------ */
//-- 添加
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'insert')
{
    /* 权限判断 */
    admin_priv('engineering_bidding');

    /*检查是否重复*/
    if(!empty($_POST['name'])){
        $is_only = $exc->is_only('name', $_POST['name'],0, "");
        if (!$is_only){
            sys_msg(sprintf($_LANG['name_exist'], stripslashes($_POST['name'])), 1);
        }
    }else{
        sys_msg(sprintf($_LANG['no_name'], stripslashes($_POST['name'])), 1);
    }
    if(!empty($_POST['biddingAt'])){
        $biddingAt = strtotime($_POST['biddingAt']);
    }else{
        sys_msg(sprintf($_LANG['no_biddingAt'], stripslashes($_POST['biddingAt'])), 1);
    }
    if(!empty($_POST['amount'])){
        $amount = intval($_POST['amount']);
    }else{
        sys_msg(sprintf($_LANG['no_amount'], stripslashes($_POST['amount'])), 1);
    }
    if(empty($_POST['type_id'])){
        sys_msg(sprintf($_LANG['no_type'], stripslashes($_POST['type_id'])), 1);
    }
    if(empty($_POST['area_id'])){
        sys_msg(sprintf($_LANG['no_area'], stripslashes($_POST['area_id'])), 1);
    }
    if(empty($_POST['biddingman'])){
        sys_msg(sprintf($_LANG['no_biddingman'], stripslashes($_POST['biddingman'])), 1);
    }
    if(empty($_POST['prjaddress'])){
        sys_msg(sprintf($_LANG['no_prjaddress'], stripslashes($_POST['prjaddress'])), 1);
    }
    if(empty($_POST['prjdesc'])){
        sys_msg(sprintf($_LANG['no_prjdesc'], stripslashes($_POST['prjdesc'])), 1);
    }
    if(empty($_POST['content'])){
        sys_msg(sprintf($_LANG['no_content'], stripslashes($_POST['content'])), 1);
    }
    if(empty($_POST['conditions'])){
        sys_msg(sprintf($_LANG['no_conditions'], stripslashes($_POST['conditions'])), 1);
    }

    /*插入数据*/
    $createat = gmtime();
    $userid = $_SESSION['admin_id'];
    $sql = "INSERT INTO ".$ecs->table('bidding').
        " (name, type, area_id, amount, biddingAt, biddingman, prjaddress, prjdesc, content, conditions,createAt, updateAt, userid )".
        " VALUES ('$_POST[name]', '$_POST[type_id]', '$_POST[area_id]', '$amount', '$biddingAt',
         '$_POST[biddingman]', '$_POST[prjaddress]', '$_POST[prjdesc]', '$_POST[content]', '$_POST[conditions]', '$createat', '$createat', '$userid')";
    $db->query($sql);


    $link[0]['text'] = $_LANG['continue_add'];
    $link[0]['href'] = 'bidding.php?act=add';

    $link[1]['text'] = $_LANG['back_list'];
    $link[1]['href'] = 'bidding.php?act=list';

    admin_log($_POST['name'],'add','bidding');

    clear_cache_files(); // 清除相关的缓存文件

    sys_msg($_LANG['biddingadd_succeed'],0, $link);
}

/*------------------------------------------------------ */
//-- 编辑
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'edit')
{
    /* 权限判断 */
    admin_priv('engineering_bidding');

    $type_list = array('ZB_JZ', 'ZB_SZ', 'ZB_TL', 'ZB_GL', 'ZB_GH', 'ZB_SL', 'NZJ');
    $area_list = get_regions(1,1);
    $smarty->assign('type_list',  $type_list);
    $smarty->assign('area_list',  $area_list);

    $sql = "SELECT * FROM " .$ecs->table('bidding'). " WHERE id='$_REQUEST[id]'";
    $bidding = $db->GetRow($sql);
    $bidding['biddingAt'] = local_date($GLOBALS['_CFG']['date_format'], $bidding['biddingAt']);
    $smarty->assign('bidding',     $bidding);
    $smarty->assign('ur_here',     $_LANG['bidding_edit']);
    $smarty->assign('action_link', array('text' => $_LANG['engineering_bidding_list'], 'href' => 'bidding.php?act=list&' . list_link_postfix()));
    $smarty->assign('form_action', 'update');

    assign_query_info();
    $smarty->display('bidding_info.htm');
}

if ($_REQUEST['act'] =='update')
{
    /* 权限判断 */
    admin_priv('engineering_bidding');

    /*检查是否重复*/
    if(!empty($_POST['name'])){
        $is_only = $exc->is_only('name', $_POST['name'], $_POST['id'], "");
        if (!$is_only){
            sys_msg(sprintf($_LANG['name_exist'], stripslashes($_POST['name'])), 1);
        }
    }else{
        sys_msg(sprintf($_LANG['no_name'], stripslashes($_POST['name'])), 1);
    }
    if(!empty($_POST['biddingAt'])){
        $biddingAt = strtotime($_POST['biddingAt']);
    }else{
        sys_msg(sprintf($_LANG['no_biddingAt'], stripslashes($_POST['biddingAt'])), 1);
    }
    if(!empty($_POST['amount'])){
        $amount = intval($_POST['amount']);
    }else{
        sys_msg(sprintf($_LANG['no_amount'], stripslashes($_POST['amount'])), 1);
    }
    if(empty($_POST['type_id'])){
        sys_msg(sprintf($_LANG['no_type'], stripslashes($_POST['type_id'])), 1);
    }
    if(empty($_POST['area_id'])){
        sys_msg(sprintf($_LANG['no_area'], stripslashes($_POST['area_id'])), 1);
    }
    if(empty($_POST['biddingman'])){
        sys_msg(sprintf($_LANG['no_biddingman'], stripslashes($_POST['biddingman'])), 1);
    }
    if(empty($_POST['prjaddress'])){
        sys_msg(sprintf($_LANG['no_prjaddress'], stripslashes($_POST['prjaddress'])), 1);
    }
    if(empty($_POST['prjdesc'])){
        sys_msg(sprintf($_LANG['no_prjdesc'], stripslashes($_POST['prjdesc'])), 1);
    }
    if(empty($_POST['content'])){
        sys_msg(sprintf($_LANG['no_content'], stripslashes($_POST['content'])), 1);
    }
    if(empty($_POST['conditions'])){
        sys_msg(sprintf($_LANG['no_conditions'], stripslashes($_POST['conditions'])), 1);
    }
    $updateat = gmtime();
    if ($exc->edit("name='$_POST[name]', type='$_POST[type_id]', area_id='$_POST[area_id]',
        amount='$amount', biddingAt='$biddingAt', biddingman='$_POST[biddingman]',
        prjaddress='$_POST[prjaddress]', prjdesc='$_POST[prjdesc]',
        content='$_POST[content]', conditions='$_POST[conditions]',updateAt='$updateat'", $_POST['id']))
    {
        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'bidding.php?act=list&' . list_link_postfix();

        $note = sprintf($_LANG['biddingedit_succeed'], stripslashes($_POST['title']));
        admin_log($_POST['title'], 'edit', 'bidding');

        clear_cache_files();

        sys_msg($note, 0, $link);
    }
    else
    {
        die($db->error());
    }
}
/*------------------------------------------------------ */
//-- 编辑项目主题
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_name')
{
    check_authz_json('engineering_bidding');

    $id    = intval($_POST['id']);
    $name = json_str_iconv(trim($_POST['val']));

    /* 检查项目名称是否重复 */
    if ($exc->num("name", $name, $id) != 0)
    {
        make_json_error(sprintf($_LANG['name_exist'], $name));
    }
    else
    {
        if ($exc->edit("name = '$name'", $id))
        {
            clear_cache_files();
            admin_log($name, 'edit', 'bidding');
            make_json_result(stripslashes($name));
        }
        else
        {
            make_json_error($db->error());
        }
    }
}

/*------------------------------------------------------ */
//-- 删除项目
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('engineering_bidding');

    $id = intval($_GET['id']);

    $name = $exc->get_name($id);
    if ($exc->drop($id))
    {
        admin_log(addslashes($name),'remove','bidding');
        clear_cache_files();
    }

    $url = 'bidding.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}
/*------------------------------------------------------ */
//-- 批量操作
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'batch')
{
    /* 批量删除 */
    if (isset($_POST['type']))
    {
        if ($_POST['type'] == 'button_remove')
        {
            admin_priv('engineering_bidding');

            if (!isset($_POST['checkboxes']) || !is_array($_POST['checkboxes']))
            {
                sys_msg($_LANG['no_select_bidding'], 1);
            }

            foreach ($_POST['checkboxes'] AS $key => $id)
            {
                if ($exc->drop($id))
                {
                    $name = $exc->get_name($id);
                    admin_log(addslashes($name),'remove','bidding');
                }
            }

        }
    }

    /* 清除缓存 */
    clear_cache_files();
    $lnk[] = array('text' => $_LANG['back_list'], 'href' => 'bidding.php?act=list');
    sys_msg($_LANG['batch_handle_ok'], 0, $lnk);
}

/* 获得工程招标列表 */
function get_biddings_list()
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
        $filter['type_id'] = $_REQUEST['type_id'];
        $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $where = '';
        if ($filter['type_id'])
        {
            $where =" AND type = '" . $filter['type_id'] ."'";
        }
        if (!empty($filter['keyword']))
        {
            $where .= " AND name LIKE '%" . mysql_like_quote($filter['keyword']) . "%'";
        }

        /* 获得总记录数据 */
        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('bidding') . 'WHERE 1 ' .$where;;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);

        /* 获取数据 */
        $sql  = 'SELECT id, name, type, area_id, amount, biddingAt, createAt, biddingman, conditions'.
            ' FROM ' .$GLOBALS['ecs']->table('bidding'). 'WHERE 1 ' .$where.
            " ORDER by $filter[sort_by] $filter[sort_order]";

        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

    $list = array();
    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
        $sql = "SELECT region_name FROM". $GLOBALS['ecs']->table('region') . "WHERE region_id=".$rows['area_id'];
        $rows['area_id'] = $GLOBALS['db']->getOne($sql);
        $rows['createAt'] = local_date($GLOBALS['_CFG']['time_format'], $rows['createAt']);
        $rows['biddingAt'] = local_date($GLOBALS['_CFG']['date_format'], $rows['biddingAt']);
        $list[] = $rows;
    }
    return array('arr' => $list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

?>
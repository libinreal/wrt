<?php

/**
 * ECSHOP 管理中心追加定制处理程序文件
 * $Author: xy $
 * $Id: customize_info.php 2014-09-03 xy $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . "includes/fckeditor/fckeditor.php");
require_once(ROOT_PATH . 'includes/cls_image.php');

/*初始化数据交换对象 */
$exc   = new exchange($ecs->table("customize_apply"), $db, 'id', 'goodsName');
//$image = new cls_image();

/*------------------------------------------------------ */
//-- 追加定制信息列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'info')
{
    admin_priv('customize_state');
    /* 取得过滤条件 */
    $filter = array();
    //$smarty->assign('cat_select',  article_cat_list(0));
    $smarty->assign('ur_here',      $_LANG['apply_list']);
    $smarty->assign('full_page',    1);
    $smarty->assign('filter',       $filter);

    if($_REQUEST['applyId']){
        $applyinfo_list = get_applyinfo();
    }
    $action_link = ($_REQUEST['act'] == 'list') ? add_link($code) : array('href' => 'customize.php?act=list', 'text' => $_LANG['customize_list']);
    $smarty->assign('action_link',  $action_link);
    $smarty->assign('applyinfo_list',    $applyinfo_list['arr']);
    $smarty->assign('filter',          $applyinfo_list['filter']);
    $smarty->assign('record_count',    $applyinfo_list['record_count']);
    $smarty->assign('page_count',      $applyinfo_list['page_count']);

    $sort_flag  = sort_flag($applyinfo_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('customize_info_list.htm');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    admin_priv('customize_state');
    /* 取得过滤条件 */
    $filter = array();
    $smarty->assign('ur_here',      $_LANG['apply_list']);
    $smarty->assign('filter',       $filter);
    if($_REQUEST['applyId']){
        $applyinfo_list = get_applyinfo();
    }

    $action_link = ($_REQUEST['act'] == 'list') ? add_link($code) : array('href' => 'customize.php?act=list', 'text' => $_LANG['customize_list']);
    $smarty->assign('action_link',  $action_link);
    $smarty->assign('applyinfo_list',    $applyinfo_list['arr']);
    $smarty->assign('filter',          $applyinfo_list['filter']);
    $smarty->assign('record_count',    $applyinfo_list['record_count']);
    $smarty->assign('page_count',      $applyinfo_list['page_count']);

    $sort_flag  = sort_flag($applyinfo_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('customize_info_list.htm'), '',
        array('filter' => $applyinfo_list['filter'], 'page_count' => $applyinfo_list['page_count']));
}
/*------------------------------------------------------ */
//-- 修改定制状态
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'state')
{
    /* 权限判断 */
    admin_priv('customize_state');

    /* 取定制数据 */
    $sql1 = "SELECT * FROM " .$ecs->table('customize_apply_info'). " WHERE id='$_REQUEST[applyid]'";
    $apply = $db->GetRow($sql1);
    if($apply['state'] == 0){
        $sql = "UPDATE ".$ecs->table('customize_apply_info')." SET state=1 WHERE id='$_REQUEST[applyid]'";
    }elseif($apply['state'] == 1){
        $sql = "UPDATE ".$ecs->table('customize_apply_info')." SET state=2 WHERE id='$_REQUEST[applyid]'";
    }
    $db->query($sql);
    assign_query_info();
    admin_log($apply['goodsName'], 'state', 'customize');
    $msg = $db->GetRow($sql1);
    echo $msg['state'];
}
elseif ($_REQUEST['act'] == 'applyinfo')
{
    admin_priv('customize_state');
    $smarty->assign('ur_here',      $_LANG['apply_list']);
    $smarty->assign('full_page',    1);
    $action_link = ($_REQUEST['act'] == 'list') ? add_link($code) : array('href' => 'customize.php?act=list', 'text' => $_LANG['customize_list']);
    $smarty->assign('action_link',  $action_link);
    $applyId = $_REQUEST['applyId'];
    $sql = " SELECT * from ".$GLOBALS['ecs']->table('customize_apply')." AS cus ".
           " INNER JOIN ".$GLOBALS['ecs']->table('customize_apply_info')." AS info ON cus.id=info.applyId ".
           " INNER JOIN ".$GLOBALS['ecs']->table('category')." AS cat ON cus.categoryNo=cat.code ".
           " INNER JOIN ".$GLOBALS['ecs']->table('region')." AS rg ON cus.areaId=rg.region_id ".
           " WHERE info.id=".$applyId."";
    $applyInfo = $GLOBALS['db']->getRow($sql);
    $applyInfo['thumb']  = $ecs->url()."".$applyInfo['thumb'];
    $applyInfo['goodsPrice']   = price_format($applyInfo['goodsPrice']);
    $applyInfo['expirationAt'] =  local_date($GLOBALS['_CFG']['time_format'],$applyInfo['expirationAt']);
    $applyInfo['createAt'] =  local_date($GLOBALS['_CFG']['time_format'],$applyInfo['createAt']);
    $applyInfo['updateAt'] =  local_date($GLOBALS['_CFG']['time_format'],$applyInfo['updateAt']);
    $smarty->assign('list',$applyInfo);
    assign_query_info();
    $smarty->display('customize_applyInfo.html');
}
/* 获得追加定制列表 */
function get_applyinfo(){
    $result = get_filter();
    if ($result === false)
    {
        $filter = array();
        $filter['applyId'] = empty($_REQUEST['applyId']) ? 0 : intval($_REQUEST['applyId']);
        $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'ic.id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $where = '';
        if ($filter['applyId'])
        {
            $where = " AND applyId=".$filter['applyId'];
        }

        /* 定制总数 */
        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('customize_apply_info'). ' AS ic '.
            'LEFT JOIN ' .$GLOBALS['ecs']->table('customize_apply'). ' AS c ON c.id = ic.applyId '.
            'WHERE 1 '.$where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);

        /* 获取定制数据 */
        $sql = 'SELECT ic.* , c.goodsName '.
            'FROM ' .$GLOBALS['ecs']->table('customize_apply_info'). ' AS ic '.
            'LEFT JOIN ' .$GLOBALS['ecs']->table('customize_apply'). ' AS c ON c.id = ic.applyId '.
            'WHERE 1 '.$where.' ORDER by '.$filter['sort_by'].' '.$filter['sort_order'];
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
        $rows['date'] = local_date($GLOBALS['_CFG']['time_format'], $rows['add_time']);
        $rows['createAt'] = local_date($GLOBALS['_CFG']['time_format'],$rows['createAt']);

        $arr[] = $rows;
    }
    return array('arr' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

?>
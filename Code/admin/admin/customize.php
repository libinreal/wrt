<?php

/**
 * ECSHOP 管理中心定制处理程序文件
 * $Author: xy $
 * $Id: customize.php 2014-09-02 xy $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/*初始化数据交换对象 */
$exc   = new exchange($ecs->table("customize_apply"), $db, 'id', 'goodsName');

/*------------------------------------------------------ */
//-- 定制列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 取得过滤条件 */
    $filter = array();
    $smarty->assign('ur_here',      $_LANG['customize_list']);
    $smarty->assign('cat_list',     cat_list(0, $cat_id,true,2));
    $smarty->assign('full_page',    1);
    $smarty->assign('filter',       $filter);

    $customize_list = get_customizelist();

    $smarty->assign('customize_list',    $customize_list['arr']);
    $smarty->assign('filter',          $customize_list['filter']);
    $smarty->assign('record_count',    $customize_list['record_count']);
    $smarty->assign('page_count',      $customize_list['page_count']);

    $sort_flag  = sort_flag($customize_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('customize_list.htm');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    /* 取得过滤条件 */
    $filter = array();
    $smarty->assign('ur_here',      $_LANG['customize_list']);
    $smarty->assign('cat_list',     cat_list(0, $cat_id,true,2));
    $smarty->assign('filter',       $filter);

    $customize_list = get_customizelist();

    $smarty->assign('customize_list',    $customize_list['arr']);
    $smarty->assign('filter',          $customize_list['filter']);
    $smarty->assign('record_count',    $customize_list['record_count']);
    $smarty->assign('page_count',      $customize_list['page_count']);

    /* 获取商品类型存在规格的类型 */
    $sort_flag  = sort_flag($customize_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('customize_list.htm'), '',
        array('filter' => $customize_list['filter'], 'page_count' => $customize_list['page_count']));
}

/*------------------------------------------------------ */
//-- 追加定制信息列表列表
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'info'){
    /* 取得过滤条件 */
    $filter = array();
    //$smarty->assign('cat_select',  article_cat_list(0));
    $smarty->assign('ur_here',      $_LANG['apply_list']);
    $smarty->assign('full_page',    1);
    $smarty->assign('filter',       $filter);

    if($_REQUEST['applyid']){
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


/* 获得定制列表 */
function get_customizelist()
{
    global $ecs;
    $result = get_filter();
    if ($result === false)
    {
        $filter = array();
        $filter['keyword'] = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keyword'] = json_str_iconv($filter['keyword']);
        }
        $filter['customNo'] = empty($_REQUEST['customNo']) ? '' : trim($_REQUEST['customNo']);
        $filter['cat_id'] = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'cus.id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
        if($filter['sort_by']=='cus.id') {
            $filter['sort_by'] = "cus.id";
        }
          $where = '';
        // 一级 二级分类
        if (!empty($filter['cat_id']))
        {
            $sql = "select cat_id,code,cat_level from ".$GLOBALS['ecs']->table('category')." where cat_id=".$filter['cat_id']." ";
            $category = $GLOBALS['db'] -> getRow($sql);
            if($category['cat_level']==1) {
                $sql2 = "select code from ".$GLOBALS['ecs']->table('category')." where parent_id=".$category['cat_id']." ";
                $codeArr = $GLOBALS['db'] -> getAll($sql2);
                $code .= '';
                foreach($codeArr as $key=> $value) {
                    $code .= ','.$value['code'];
                }
                $code = substr($code,1);
            } else {
                $code = $category['code'];
            }
            $where .= " AND cus.categoryNo in(".$code.") ";
        }
        // 商品名称
        if (!empty($filter['keyword']))
        {
            $where .= " AND cus.goodsName LIKE '%" . mysql_like_quote($filter['keyword']) . "%'";
        }
        if(!empty($filter['customNo']))
        {
            $where .= " AND u.customNo like '%".$filter['customNo']."%'";
        }
        /* 定制总数 */

        $sql = " SELECT  COUNT(DISTINCT cus.id) ".
            " FROM ".$GLOBALS['ecs']->table('customize_apply')." AS cus ".
            " INNER JOIN ".$GLOBALS['ecs']->table('customize_apply_info')." AS cusinfo ON cus.id=cusinfo.applyId ".
            " INNER JOIN ".$GLOBALS['ecs']->table('category')." AS cate ON cus.categoryNo=cate.code ".
            " INNER JOIN ".$GLOBALS['ecs']->table('users')." AS u ON cus.userId=u.user_id ".
            " INNER JOIN ".$GLOBALS['ecs']->table('region')." AS area ON cus.areaId=area.region_id " .
            " WHERE 1 ".$where." ";
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        $filter = page_and_size($filter);
        /* 获取定制数据 */
        $sql = " SELECT distinct cus.id, COUNT(cusinfo.userId) AS cusCount,area.region_name,cus.userId,cus.id,cate.cat_name,cus.*,u.customNo ".
               " FROM ".$GLOBALS['ecs']->table('customize_apply')." AS cus ".
               " INNER JOIN ".$GLOBALS['ecs']->table('customize_apply_info')." AS cusinfo ON cus.id=cusinfo.applyId ".
               " INNER JOIN ".$GLOBALS['ecs']->table('category')." AS cate ON cus.categoryNo=cate.code ".
               " INNER JOIN ".$GLOBALS['ecs']->table('users')." AS u ON cus.userId=u.user_id ".
               " INNER JOIN ".$GLOBALS['ecs']->table('region')." AS area ON cus.areaId=area.region_id " .
               " WHERE 1 ".$where."  GROUP BY cusinfo.applyId ORDER by ".$filter['sort_by']." ".$filter['sort_order']." ";
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
        $rows['goodsPrice']   = price_format($rows['goodsPrice']);
        $rows['thumb']  = $ecs->url()."".$rows['thumb'];
        $rows['createAt']     = local_date($GLOBALS['_CFG']['time_format'],$rows['createAt']);
        $rows['updateAt']     = local_date($GLOBALS['_CFG']['time_format'],$rows['updateAt']);
        $rows['expirationAt'] = local_date($GLOBALS['_CFG']['time_format'],$rows['expirationAt']);
        $arr[] = $rows;
    }
    return array('arr' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}



/* 获得追加定制列表 */
function get_applyinfo(){
    $result = get_filter();
    if ($result === false)
    {
        $filter = array();
        $filter['applyId'] = empty($_REQUEST['applyid']) ? 0 : intval($_REQUEST['applyid']);
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
        $rows['createAt'] = local_date($GLOBALS['_CFG']['time_format'],$rows['createAt']);
        $rows['expirationAt'] = local_date($GLOBALS['_CFG']['time_format'],$rows['expirationAt']);

        $arr[] = $rows;
    }
    return array('arr' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

?>
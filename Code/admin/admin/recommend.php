<?php

/**
 * 商品推荐
 * $Author: xy $
 * $Id: recommend.php 2014-09-11 xy $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/*初始化数据交换对象 */
$exc   = new exchange($ecs->table("business_recommend"), $db, 'id', 'cat_code');

/*------------------------------------------------------ */
//-- 列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 取得过滤条件 */
    $filter = array();
    $cat_list = array('20010000'=>$lang.cat_20010000, '20020000'=>$lang.cat_20020000, '10010000'=>$lang.cat_20020000, '10020000'=>$lang.cat_10020000,);
    $smarty->assign('cat_list',  $cat_list);
    $smarty->assign('ur_here',      $_LANG['recommend_goods_list']);
    $smarty->assign('action_link',  array('text' => $_LANG['recommend_goods_add'], 'href' => 'recommend.php?act=add'));
    $smarty->assign('full_page',    1);
    $smarty->assign('filter',       $filter);

    $recommends_list = get_recommends_list();

    $smarty->assign('recommends_list',    $recommends_list['arr']);
    $smarty->assign('filter',          $recommends_list['filter']);
    $smarty->assign('record_count',    $recommends_list['record_count']);
    $smarty->assign('page_count',      $recommends_list['page_count']);

    $sort_flag  = sort_flag($recommends_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('recommend_list.htm');
}

/*------------------------------------------------------ */
//-- 翻页，排序
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    check_authz_json('recommend_goods');

    $recommends_list = get_recommends_list();

    $smarty->assign('recommends_list',    $recommends_list['arr']);
    $smarty->assign('filter',          $recommends_list['filter']);
    $smarty->assign('record_count',    $recommends_list['record_count']);
    $smarty->assign('page_count',      $recommends_list['page_count']);

    $sort_flag  = sort_flag($recommends_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('recommend_list.htm'), '',
        array('filter' => $recommends_list['filter'], 'page_count' => $recommends_list['page_count']));
}

/*------------------------------------------------------ */
//-- 添加
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'add')
{
    /* 权限判断 */
    admin_priv('recommend_goods');

    $smarty->assign('ur_here',     $_LANG['recommend_goods_add']);
    $smarty->assign('action_link', array('href'=>'recommend.php?act=list', 'text' => $_LANG['recommend_goods_list']));
    $smarty->assign('action',      'add');
    $smarty->assign('form_action',    'insert');

    assign_query_info();
    $smarty->display('recommend_info.htm');
}

/*------------------------------------------------------ */
//-- 添加
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'insert')
{
    /* 权限判断 */
    admin_priv('recommend_goods');

    //分类下商品去重
    if(!empty($_POST['category'])){
        $is_only = $exc->is_only('cat_code', $_POST['category'],0, "");
        if (!$is_only){
            sys_msg(sprintf($_LANG['cat_exist'], stripslashes($_POST['category'])), 1);
        }
    }else{
        sys_msg(sprintf($_LANG['no_category'], stripslashes($_POST['no_category'])), 1);
    }

    if(empty($_POST['goods_id'])){
        sys_msg(sprintf($_LANG['no_goods_name'], stripslashes($_POST['goods_name'])), 1);
    }
    if(empty($_POST['brand_name'])){
        sys_msg(sprintf($_LANG['no_brand_name'], stripslashes($_POST['brand_name'])), 1);
    }
    if(is_array($_POST['brand_name'])){
        $brand_id = "";
        $count = count($_POST['brand_name']);
        $i = 1;
        foreach($_POST['brand_name'] as $key=>$value){
            if($count == $i){
                $brand_id .= $value ;
            }elseif($i < $count){
                $brand_id .= $value ."," ;
            }
            $i++;
        }
    }

    if($_POST['gname'] == "" || $_POST['wcode'] == ""){
        $sql ="SELECT goods_name, wcode  FROM " . $GLOBALS['ecs']->table('goods') . "
        WHERE goods_id = " .$_POST['goods_id']  ;
        $goods = $GLOBALS['db']->getRow($sql);
        $_POST['gname'] = $goods['goods_name'];
        $_POST['wcode'] = $goods['wcode'];
    }

    /*插入数据*/
    $createat = gmtime();
    $sql = "INSERT INTO ".$ecs->table('business_recommend').
        " (cat_code, goods_name, goods_wcode, goods_id, brand_id, createAt, updateAt )".
        " VALUES ('$_POST[category]', '$_POST[gname]', '$_POST[wcode]',
         '$_POST[goods_id]', '$brand_id', '$createat', '$createat')";
    $db->query($sql);


    $link[0]['text'] = $_LANG['continue_add'];
    $link[0]['href'] = 'recommend.php?act=add';

    $link[1]['text'] = $_LANG['back_list'];
    $link[1]['href'] = 'recommend.php?act=list';

    admin_log($_POST['category'],'add','recommend');

    clear_cache_files(); // 清除相关的缓存文件

    sys_msg($_LANG['recommendadd_succeed'],0, $link);
}

/*------------------------------------------------------ */
//-- 编辑
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'edit')
{
    /* 权限判断 */
    admin_priv('recommend_goods');
    $id = $_REQUEST['id'];

    $sql = "SELECT * FROM " .$ecs->table('business_recommend'). " WHERE id='$_REQUEST[id]'";
    $recommend = $db->GetRow($sql);
    $recommend['brand_id'] = json_encode(explode(',',$recommend['brand_id']));
    //分类下商品列表
    $cat_code = $recommend['cat_code'];
    $lt_cat_code = $cat_code + 10000;
    $sql1 ="SELECT goods_id, goods_name, wcode  FROM " . $GLOBALS['ecs']->table('goods') . "
        WHERE cat_code >= " . $cat_code . " AND cat_code < " . $lt_cat_code  ;
    $goods_list = $GLOBALS['db']->getAll($sql1);
    $smarty->assign('goods_list',     $goods_list);

    //商品的品牌列表
    $sql_brand = "SELECT brand_id FROM " . $GLOBALS['ecs']->table('goods') . "
        WHERE wcode = " . $recommend['goods_wcode'];
    $brand_ids = $GLOBALS['db']->getALL($sql_brand);
    foreach($brand_ids as $key=>$value){
        $brand_id[$key] = $value['brand_id'];
    }
    $brand_id = array_values(array_unique($brand_id));
    foreach ($brand_id as $key => $value) {
        $sql ="SELECT brand_id, brand_name FROM " . $GLOBALS['ecs']->table('brand') .
            " WHERE brand_id = " .$value ;
        $brands_list[] = $GLOBALS['db']->getRow($sql);
    }
    $smarty->assign('brands_list',     $brands_list);

    $smarty->assign('recommend',     $recommend);
    $smarty->assign('gname',     $recommend['goods_name']);
    $smarty->assign('wcode',     $recommend['goods_wcode']);
    $smarty->assign('ur_here',     $_LANG['recommend_edit']);
    $smarty->assign('action_link', array('text' => $_LANG['recommend_goods_list'], 'href' => 'recommend.php?act=list&' . list_link_postfix()));
    $smarty->assign('form_action', 'update');

    assign_query_info();
    $smarty->display('recommend_info.htm');
}

if ($_REQUEST['act'] =='update')
{
    /* 权限判断 */
    admin_priv('recommend_goods');

    //分类下商品去重
    if(!empty($_POST['category'])){
        $is_only = $exc->is_only('cat_code', $_POST['category'], $_POST['id'], "");
        if (!$is_only){
            sys_msg(sprintf($_LANG['cat_exist'], stripslashes($_POST['category'])), 1);
        }
    }else{
        sys_msg(sprintf($_LANG['no_category'], stripslashes($_POST['no_category'])), 1);
    }

    if(empty($_POST['goods_id'])){
        sys_msg(sprintf($_LANG['no_goods_name'], stripslashes($_POST['goods_name'])), 1);
    }
    if(empty($_POST['brand_name'])){
        sys_msg(sprintf($_LANG['no_brand_name'], stripslashes($_POST['brand_name'])), 1);
    }
    if(is_array($_POST['brand_name'])){
        $brand_id = "";
        $count = count($_POST['brand_name']);
        $i = 1;
        foreach($_POST['brand_name'] as $key=>$value){
            if($count == $i){
                $brand_id .= $value ;
            }elseif($i < $count){
                $brand_id .= $value ."," ;
            }
            $i++;
        }
    }

    if($_POST['gname'] == "" || $_POST['wcode'] == ""){
        $sql ="SELECT goods_name, wcode  FROM " . $GLOBALS['ecs']->table('goods') . "
        WHERE goods_id = " .$_POST['goods_id']  ;
        $goods = $GLOBALS['db']->getRow($sql);
        $_POST['gname'] = $goods['goods_name'];
        $_POST['wcode'] = $goods['wcode'];
    }

    $updateat = gmtime();
    if ($exc->edit("cat_code='$_POST[category]', goods_name='$_POST[gname]', goods_wcode='$_POST[wcode]',
        goods_id='$_POST[goods_id]', brand_id='$brand_id', updateAt='$updateat'", $_POST['id']))
    {
        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'recommend.php?act=list&' . list_link_postfix();

        $note = sprintf($_LANG['recommendedit_succeed'], stripslashes($_POST['category']));
        admin_log($_POST['category'], 'edit', 'recommend');

        clear_cache_files();

        sys_msg($note, 0, $link);
    }
    else
    {
        die($db->error());
    }
}

/*------------------------------------------------------ */
//-- 删除项目
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('recommend_goods');

    $id = intval($_GET['id']);

    $name = $exc->get_name($id);
    if ($exc->drop($id))
    {
        admin_log(addslashes($name),'remove','recommend');
        clear_cache_files();
    }

    $url = 'recommend.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

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
            admin_priv('recommend_goods');

            if (!isset($_POST['checkboxes']) || !is_array($_POST['checkboxes']))
            {
                sys_msg($_LANG['no_select_recommend'], 1);
            }

            foreach ($_POST['checkboxes'] AS $key => $id)
            {
                if ($exc->drop($id))
                {
                    $name = $exc->get_name($id);
                    admin_log(addslashes($name),'remove','recommend');
                }
            }

        }
    }

    /* 清除缓存 */
    clear_cache_files();
    $lnk[] = array('text' => $_LANG['back_list'], 'href' => 'recommend.php?act=list');
    sys_msg($_LANG['batch_handle_ok'], 0, $lnk);
}

/*------------------------------------------------------ */
//-- 取分类下商品
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'get_goods')
{
    check_authz_json('recommend_goods');

    $cat_code = intval($_GET['category']);
    $lt_cat_code = $cat_code + 10000;
    $sql ="SELECT goods_id, goods_name, wcode  FROM " . $GLOBALS['ecs']->table('goods') . "
        WHERE cat_code >= " . $cat_code . " AND cat_code < " . $lt_cat_code  ;
    $goods = $GLOBALS['db']->getAll($sql);
    echo json_encode($goods);

}

/*------------------------------------------------------ */
//-- 取商品厂商
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'get_brands')
{
    check_authz_json('recommend_goods');

    $goods_id = intval($_GET['goods_id']);
    $sql_wcode = "SELECT wcode FROM " . $GLOBALS['ecs']->table('goods') . " WHERE goods_id = " . $goods_id;

    $sql_brand = "SELECT brand_id FROM " . $GLOBALS['ecs']->table('goods') . "
        WHERE wcode in (" . $sql_wcode . ")";

    $brand_ids = $GLOBALS['db']->getALL($sql_brand);
    foreach($brand_ids as $key=>$value){
        $brand_id[$key] = $value['brand_id'];
    }
    $brand_id = array_values(array_unique($brand_id));
    foreach ($brand_id as $key => $value) {
        $sql ="SELECT brand_id, brand_name FROM " . $GLOBALS['ecs']->table('brand') .
            " WHERE brand_id = " .$value ;
        $brands[] = $GLOBALS['db']->getRow($sql);
    }
    echo json_encode($brands);
}

/* 获得推荐商品列表 */
function get_recommends_list()
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
        $filter['category'] = $_REQUEST['category'];
        $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $where = '';
        if ($filter['category'])
        {
            $where =" AND cat_code = '" . $filter['category'] ."'";
        }
        if (!empty($filter['keyword']))
        {
            $where .= " AND goods_name LIKE '%" . mysql_like_quote($filter['keyword']) . "%'";
        }

        /* 获得总记录数据 */
        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('business_recommend') . 'WHERE 1 ' .$where;;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);

        /* 获取数据 */
        $sql  = 'SELECT id, cat_code, goods_name, brand_id, createAt '.
            ' FROM ' .$GLOBALS['ecs']->table('business_recommend'). 'WHERE 1 ' .$where.
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
        $rows['createAt'] = local_date($GLOBALS['_CFG']['time_format'], $rows['createAt']);
        $brand_id = explode(',',$rows['brand_id']);
        foreach ($brand_id as $key => $value) {
            $sql ="SELECT brand_name FROM " . $GLOBALS['ecs']->table('brand') .
                " WHERE brand_id = " .$value ;
            $brands[] = $GLOBALS['db']->getOne($sql);
        }
        $rows['brand_id'] = implode(',',$brands);
        $list[] = $rows;
    }
    return array('arr' => $list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

?>
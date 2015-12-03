<?php

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
if ($_REQUEST['act'] == 'list')
{
    /* 取得过滤条件 */
    $filter = array();
    $smarty->assign('ur_here',      $_LANG['19_brand_recommend_list']);
    $smarty->assign('action_link',  array('text' => $_LANG['20_brand_recommend_add'], 'href' => 'brand_recommend.php?act=add'));
    $smarty->assign('full_page',    1);
    $smarty->assign('filter',       $filter);

    $region = get_regions(1,1);
    $category = cat_list();
    $sql = 'SELECT brand_id, brand_name FROM ' . $GLOBALS['ecs']->table('brand') . ' ORDER BY sort_order';
    $res = $GLOBALS['db']->getAll($sql);


    $brand_recommend_list = get_brand_recommend();
    $smarty->assign('region',$region);
    $smarty->assign('category',$category);
    $smarty->assign('brand_list',$res);
    $smarty->assign('brand_recommend_list',    $brand_recommend_list['arr']);
    $smarty->assign('filter',          $brand_recommend_list['filter']);
    $smarty->assign('record_count',    $brand_recommend_list['record_count']);
    $smarty->assign('page_count',      $brand_recommend_list['page_count']);

    $sort_flag  = sort_flag($brand_recommend_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);
    assign_query_info();
    $smarty->display('brand_recommend_list.htm');
}
elseif ($_REQUEST['act'] == 'query')
{
    check_authz_json('brand_recommend_manage');
    $region = get_regions(1,1);
    $category = cat_list();
    $sql = 'SELECT brand_id, brand_name FROM ' . $GLOBALS['ecs']->table('brand') . ' ORDER BY sort_order';
    $res = $GLOBALS['db']->getAll($sql);
    $brand_recommend_list = get_brand_recommend();
    $smarty->assign('region',$region);
    $smarty->assign('category',$category);
    $smarty->assign('brand_list',$res);
    $smarty->assign('brand_recommend_list',    $brand_recommend_list['arr']);
    $smarty->assign('filter',          $brand_recommend_list['filter']);
    $smarty->assign('record_count',    $brand_recommend_list['record_count']);
    $smarty->assign('page_count',      $brand_recommend_list['page_count']);
    $sort_flag  = sort_flag($brand_recommend_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('brand_recommend_list.htm'), '',array('filter' => $brand_recommend_list['filter'], 'page_count' => $brand_recommend_list['page_count']));
}
if ($_REQUEST['act'] == 'add')
{
    /* 权限判断 */
    admin_priv('brand_recommend_manage');
    /*初始化*/
    $barand = array();
    $barand['sort_order'] = 100;
    // 取得区域列表
    $region = get_regions(1,1);

    $category = cat_list();
    /* 取得分类、品牌 */
    $sql = 'SELECT brand_id, brand_name FROM ' . $GLOBALS['ecs']->table('brand') . ' ORDER BY sort_order';
    $res = $GLOBALS['db']->getAll($sql);
    $smarty->assign('region',$region);
    $smarty->assign('category', $category);
    $smarty->assign('brand_list',$res);
    $smarty -> assign('brands',$barand);
    $smarty->assign('ur_here',     $_LANG['brand_recommend_add']);
    $smarty->assign('action_link', array('text' => $_LANG['19_brand_recommend_list'], 'href' => 'brand_recommend.php?act=list'));
    $smarty->assign('form_action', 'insert');

    assign_query_info();
    $smarty->display('brand_recommend_info.htm');
}
if ($_REQUEST['act'] == 'insert')
{
    /* 权限判断 */
    admin_priv('brand_recommend_manage');

    $region = isset($_POST['region']) ? intval($_POST['region']) : '';
    $category_id = isset($_POST['category']) ? intval($_POST['category']) : '';
    $brand_id = isset($_POST['brand_id']) ? intval($_POST['brand_id']) : '';
    $sort_order = isset($_POST['sort_order']) ? intval($_POST['sort_order']) : '';

    if($category_id) {
        $sql = "SELECT code FROM ". $GLOBALS['ecs'] -> table('category') ." WHERE cat_id=".$category_id."";
        $cat_code = $GLOBALS['db'] -> getOne($sql);
        //unset($sql);
    }
    $sql = "INSERT INTO ". $GLOBALS['ecs'] -> table('brand_recommend') ."(area_id,cat_code,brand_id,sort_order) ".
        "VALUES($region,$cat_code,$brand_id,$sort_order)";
    $GLOBALS['db']-> query($sql);

    $link[0]['text'] = $_LANG['continue_add'];
    $link[0]['href'] = 'brand_recommend.php?act=add';

    $link[1]['text'] = $_LANG['back_list'];
    $link[1]['href'] = 'brand_recommend.php?act=list';

    admin_log('添加推荐品牌','add','brand_recommend');
    clear_cache_files(); // 清除相关的缓存文件
    sys_msg($_LANG['brand_recommend_add_succeed'],0, $link);
}
//-- 编辑
if ($_REQUEST['act'] == 'edit')
{
    /* 权限判断 */
    admin_priv('brand_recommend_manage');
    /* 取文章数据 */

    $sql = " SELECT br.*,c.cat_id FROM ". $GLOBALS['ecs'] -> table('brand_recommend') ." AS br " .
           " INNER JOIN ". $GLOBALS['ecs'] -> table('brand') ." AS b ON br.brand_id=b.brand_id ".
           " INNER JOIN ".$GLOBALS['ecs'] -> table('category')." AS c ON br.cat_code=c.code ".
           " INNER JOIN ".$GLOBALS['ecs'] -> table('region') ." AS r ON br.area_id=r.region_id ".
           " WHERE br.brand_rid=".$_GET['id']."";
    $brand = $GLOBALS['db']->getRow($sql);

    $region = get_regions(1,1);
    
    $sql = 'SELECT brand_id, brand_name FROM ' . $GLOBALS['ecs']->table('brand') . ' ORDER BY sort_order';
    $res = $GLOBALS['db']->getAll($sql);
    /* 取得分类、品牌 */
    $smarty->assign('category', cat_list(0,$brand['cat_id']));
    $smarty->assign('brand_list', $res);
    $smarty->assign('brands', $res);
    $smarty -> assign('brands',$brand);
    $smarty -> assign('region',$region);
    $smarty->assign('ur_here',     $_LANG['brand_recommend_edit']);
    $smarty->assign('action_link', array('text' => $_LANG['19_brand_recommend_list'], 'href' => 'brand_recommend.php?act=list'));
    $smarty->assign('form_action', 'update');

    assign_query_info();
    $smarty->display('brand_recommend_info.htm');
}

if ($_REQUEST['act'] =='update')
{
    /* 权限判断 */
    admin_priv('brand_recommend_manage');

    $region = isset($_POST['region']) ? intval($_POST['region']) : '';
    $category_id = isset($_POST['category']) ? intval($_POST['category']) : '';
    $brand_id = isset($_POST['brand_id']) ? intval($_POST['brand_id']) : '';
    $sort_order = isset($_POST['sort_order']) ? intval($_POST['sort_order']) : '';
    $brand_rid = isset($_POST['brand_rid']) ? intval($_POST['brand_rid']) : '';
    if($category_id) {
        $sql = "SELECT code FROM ". $GLOBALS['ecs'] -> table('category') ." WHERE cat_id=".$category_id."";
        $cat_code = $GLOBALS['db'] -> getOne($sql);
    }

    $sql = "UPDATE ".$GLOBALS['ecs']->table('brand_recommend')." SET area_id=$region,cat_code=$cat_code ,".
            " brand_id=$brand_id,sort_order=$sort_order WHERE brand_rid=".$brand_rid."";
    if($GLOBALS['db']->query($sql)) {
        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'brand_recommend.php?act=list';

        admin_log('编辑推荐品牌成功','edit','brand_recommend');
        clear_cache_files(); // 清除相关的缓存文件
        sys_msg($_LANG['brand_recommend_edit_succeed'],0, $link);
    }
}
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('brand_recommend_manage');

    $id = intval($_GET['id']);
    //DELETE FROM brand_recommend WHERE brand_rid=2
    $sql = "DELETE FROM " .$GLOBALS['ecs']->table('brand_recommend'). " WHERE brand_rid = '$id'";

    $db->query($sql);

    $url = 'brand_recommend.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

function get_brand_recommend()
{
    $result = get_filter();
    if($result === false)
    {
        $filter = array();
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $filter['region'] = empty($_REQUEST['region']) ? 0 : intval($_REQUEST['region']);
        $filter['category'] = empty($_REQUEST['category']) ? 0 : intval($_REQUEST['category']);
        $filter['brand_id'] = empty($_REQUEST['brand_id']) ? 0 : intval($_REQUEST['brand_id']);

        $where = '';
        if($filter['region']) {
            $where .= ' AND br.area_id='.$filter["region"].' ';
        }
        if($filter['brand_id']) {
            $where = ' AND br.brand_id='.$filter['brand_id'].' ';
        }
        if($filter['category']) {
            $sql_category = "select code FROM ". $GLOBALS['ecs'] ->table('category')." where cat_id=".$filter['category']."";
            $cat_code = $GLOBALS['db']->getOne($sql_category);
            $cat_code = substr($cat_code,0,4);
            $where .= " AND br.cat_code LIKE '%".$cat_code."%'";
        }
        $sql = " SELECT count(*) FROM ".$GLOBALS['ecs'] -> table('brand_recommend')." AS br ".
                " INNER JOIN ". $GLOBALS['ecs'] -> table('brand') ." AS b ON br.brand_id=b.brand_id ".
                " INNER JOIN ". $GLOBALS['ecs'] -> table('category') ." AS c ON br.cat_code=c.code ".
                " INNER JOIN ". $GLOBALS['ecs'] -> table('region') ." AS r ON br.area_id=r.region_id ".
                " WHERE 1 " .$where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        $filter = page_and_size($filter);
        $sql = " SELECT br.brand_rid,r.region_id,r.region_name,c.cat_name,b.brand_name,br.sort_order FROM ". $GLOBALS['ecs'] -> table('brand_recommend') ." AS br ".
                " INNER JOIN ". $GLOBALS['ecs'] -> table('brand') ." AS b ON br.brand_id=b.brand_id " .
                " INNER JOIN ". $GLOBALS['ecs'] -> table('category') ." AS c ON br.cat_code=c.code ".
                " INNER JOIN ". $GLOBALS['ecs'] -> table('region') ." AS r ON br.area_id=r.region_id ".
                " WHERE 1". $where ." ORDER by br.brand_rid desc";
    }
    else
    {
        $sql = $result['sql'];
        $filter = $result['filter'];
    }

    $arr = array();
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
        if($rows['region_id']==1) {
            $rows['region_name'] = '全部区域';
        }
        $arr[] = $rows;
    }
    return array('arr' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

}

?>
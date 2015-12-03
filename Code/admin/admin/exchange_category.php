<?php

// 积分商城商品分类管理程序

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
$exc = new exchange($ecs->table("exchange_category"), $db, 'cat_id', 'cat_name');

// act操作项的初始化
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}
//-- 商品分类列表
if ($_REQUEST['act'] == 'list')
{
    /* 获取分类列表 */
    $cat_list = exchangeCategoryList(0, 0, false);
    /* 模板赋值 */
    $smarty->assign('ur_here',      $_LANG['01_exchange_category']);
    $smarty->assign('action_link',  array('href' => 'exchange_category.php?act=add', 'text' => $_LANG['02_exchange_category_add']));
    $smarty->assign('full_page',    1);

    $smarty->assign('cat_info',     $cat_list);

    /* 列表页面 */
    assign_query_info();
    $smarty->display('exchange_category_list.htm');
}
//-- 排序、分页、查询
elseif ($_REQUEST['act'] == 'query')
{
    $cat_list = exchangeCategoryList(0, 0, false);
    $smarty->assign('cat_info',     $cat_list);
    make_json_result($smarty->fetch('exchange_category_list.htm'));
}
//-- 添加商品分类
if ($_REQUEST['act'] == 'add')
{
    /* 权限检查 */
    admin_priv('exchange_category_manage');
    /* 模板赋值 */
    $smarty->assign('ur_here',      $_LANG['02_exchange_category_add']);
    $smarty->assign('action_link',  array('href' => 'exchange_category.php?act=list', 'text' => $_LANG['01_exchange_category']));

    $smarty->assign('cat_select',   exchangeCategoryList(0, 0, true));
    $smarty->assign('form_act',     'insert');

    /* 显示页面 */
    assign_query_info();
    $smarty->display('exchange_category_info.htm');
}
//-- 商品分类添加时的处理
if ($_REQUEST['act'] == 'insert')
{
    /* 权限检查 */
    admin_priv('exchange_category_manage');

    /* 初始化变量 */
    $cat['cat_id']       = !empty($_POST['cat_id'])       ? intval($_POST['cat_id'])     : 0;
    $cat['parent_id']    = !empty($_POST['parent_id'])    ? intval($_POST['parent_id'])  : 0;
    $cat['sort_order']   = !empty($_POST['sort_order'])   ? intval($_POST['sort_order']) : 0;
    $cat['keywords']     = !empty($_POST['keywords'])     ? trim($_POST['keywords'])     : '';
    $cat['cat_desc']     = !empty($_POST['cat_desc'])     ? $_POST['cat_desc']           : '';
    $cat['measure_unit'] = !empty($_POST['measure_unit']) ? trim($_POST['measure_unit']) : '';
    $cat['cat_name']     = !empty($_POST['cat_name'])     ? trim($_POST['cat_name'])     : '';
    $cat['show_in_nav']  = !empty($_POST['show_in_nav'])  ? intval($_POST['show_in_nav']): 0;
    $cat['style']        = !empty($_POST['style'])        ? trim($_POST['style'])        : '';
    $cat['is_show']      = !empty($_POST['is_show'])      ? intval($_POST['is_show'])    : 0;
    $cat['grade']        = !empty($_POST['grade'])        ? intval($_POST['grade'])      : 0;
    $cat['filter_attr']  = !empty($_POST['filter_attr'])  ? implode(',', array_unique(array_diff($_POST['filter_attr'],array(0)))) : 0;

    if (exchange_cat_exists($cat['cat_name'], $cat['parent_id']))
    {
        /* 同级别下不能有重复的分类名称 */
        $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
        sys_msg($_LANG['catname_exist'], 0, $link);
    }
    /* 入库的操作 */
    if ($db->autoExecute($ecs->table('exchange_category'), $cat) !== false)
    {
        admin_log($_POST['cat_name'], 'add', 'exchange_category');   // 记录管理员操作
        clear_cache_files();    // 清除缓存

        /*添加链接*/
        $link[0]['text'] = $_LANG['continue_add'];
        $link[0]['href'] = 'exchange_category.php?act=add';

        $link[1]['text'] = $_LANG['back_list'];
        $link[1]['href'] = 'exchange_category.php?act=list';
        sys_msg($_LANG['catadd_succed'], 0, $link);
    }
}
//-- 编辑商品分类信息
if ($_REQUEST['act'] == 'edit')
{
    admin_priv('exchange_category_manage');   // 权限检查
    $cat_id = intval($_REQUEST['cat_id']);
    $cat_info = get_cat_info($cat_id);  // 查询分类信息数据

    /* 模板赋值 */
    $smarty->assign('ur_here',     $_LANG['category_edit']);
    $smarty->assign('action_link', array('text' => $_LANG['01_exchange_category'], 'href' => 'exchange_category.php?act=list'));

    $smarty->assign('cat_info',    $cat_info);
    $smarty->assign('form_act',    'update');
    $smarty->assign('cat_select',  exchangeCategoryList(0, $cat_info['parent_id'], true));
    $smarty->assign('goods_type_list',  goods_type_list(0)); // 取得商品类型

    /* 显示页面 */
    assign_query_info();
    $smarty->display('exchange_category_info.htm');
}

elseif($_REQUEST['act'] == 'add_category')
{
    $parent_id = empty($_REQUEST['parent_id']) ? 0 : intval($_REQUEST['parent_id']);
    $category = empty($_REQUEST['cat']) ? '' : json_str_iconv(trim($_REQUEST['cat']));

    if(exchange_cat_exists($category, $parent_id))
    {
        make_json_error($_LANG['catname_exist']);
    }
    else
    {
        $sql = "INSERT INTO " . $ecs->table('exchange_category') . "(cat_name, parent_id, is_show)" .
            "VALUES ( '$category', '$parent_id', 1)";

        $db->query($sql);
        $category_id = $db->insert_id();

        $arr = array("parent_id"=>$parent_id, "id"=>$category_id, "cat"=>$category);

        clear_cache_files();    // 清除缓存

        make_json_result($arr);
    }
}
//-- 编辑商品分类信息
if ($_REQUEST['act'] == 'update')
{
    /* 权限检查 */
    admin_priv('exchange_category_manage');
    /* 初始化变量 */
    $cat_id              = !empty($_POST['cat_id'])       ? intval($_POST['cat_id'])     : 0;
    $old_cat_name        = $_POST['old_cat_name'];
    $cat['parent_id']    = !empty($_POST['parent_id'])    ? intval($_POST['parent_id'])  : 0;
    $cat['sort_order']   = !empty($_POST['sort_order'])   ? intval($_POST['sort_order']) : 0;
    $cat['keywords']     = !empty($_POST['keywords'])     ? trim($_POST['keywords'])     : '';
    $cat['cat_desc']     = !empty($_POST['cat_desc'])     ? $_POST['cat_desc']           : '';
    $cat['measure_unit'] = !empty($_POST['measure_unit']) ? trim($_POST['measure_unit']) : '';
    $cat['cat_name']     = !empty($_POST['cat_name'])     ? trim($_POST['cat_name'])     : '';
    $cat['is_show']      = !empty($_POST['is_show'])      ? intval($_POST['is_show'])    : 0;
    $cat['show_in_nav']  = !empty($_POST['show_in_nav'])  ? intval($_POST['show_in_nav']): 0;
    $cat['style']        = !empty($_POST['style'])        ? trim($_POST['style'])        : '';
    $cat['grade']        = !empty($_POST['grade'])        ? intval($_POST['grade'])      : 0;
    $cat['filter_attr']  = !empty($_POST['filter_attr'])  ? implode(',', array_unique(array_diff($_POST['filter_attr'],array(0)))) : 0;
    $cat['cat_recommend']  = !empty($_POST['cat_recommend'])  ? $_POST['cat_recommend'] : array();
    /* 判断分类名是否重复 */

    if ($cat['cat_name'] != $old_cat_name)
    {
        if (exchange_cat_exists($cat['cat_name'],$cat['parent_id'], $cat_id))
        {
            $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
            sys_msg($_LANG['catname_exist'], 0, $link);
        }
    }
    /* 判断上级目录是否合法 */
    $children = array_keys(exchangeCategoryList($cat_id, 0, false));     // 获得当前分类的所有下级分类
    if (in_array($cat['parent_id'], $children))
    {
        /* 选定的父类是当前分类或当前分类的下级分类 */
        $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
        sys_msg($_LANG["is_leaf_error"], 0, $link);
    }

    $dat = $db->getRow("SELECT cat_name, show_in_nav FROM ". $ecs->table('exchange_category') . " WHERE cat_id = '$cat_id'");

    if ($db->autoExecute($ecs->table('exchange_category'), $cat, 'UPDATE', "cat_id='$cat_id'"))
    {
        /* 更新分类信息成功 */
        clear_cache_files(); // 清除缓存
        admin_log($_POST['cat_name'], 'edit', 'exchange_category'); // 记录管理员操作
        /* 提示信息 */
        $link[] = array('text' => $_LANG['back_list'], 'href' => 'exchange_category.php?act=list');
        sys_msg($_LANG['catedit_succed'], 0, $link);
    }
}
//-- 批量转移商品分类页面
if ($_REQUEST['act'] == 'move')
{
    /* 权限检查 */
    admin_priv('exchange_category_manage');

    $cat_id = !empty($_REQUEST['cat_id']) ? intval($_REQUEST['cat_id']) : 0;

    /* 模板赋值 */
    $smarty->assign('ur_here',     $_LANG['move_goods']);
    $smarty->assign('action_link', array('href' => 'exchange_category.php?act=list', 'text' => $_LANG['01_exchange_category']));
    $smarty->assign('cat_select', exchangeCategoryList(0, $cat_id, true));
    $smarty->assign('form_act',   'move_cat');
    /* 显示页面 */
    assign_query_info();
    $smarty->display('exchange_category_move.htm');
}
//-- 处理批量转移商品分类的处理程序
if ($_REQUEST['act'] == 'move_cat')
{
    /* 权限检查 */
    admin_priv('exchange_category_manage');

    $cat_id        = !empty($_POST['cat_id'])        ? intval($_POST['cat_id'])        : 0;
    $target_cat_id = !empty($_POST['target_cat_id']) ? intval($_POST['target_cat_id']) : 0;

    /* 商品分类不允许为空 */
    if ($cat_id == 0 || $target_cat_id == 0)
    {
        $link[] = array('text' => $_LANG['go_back'], 'href' => 'exchange_category.php?act=move');
        sys_msg($_LANG['cat_move_empty'], 0, $link);
    }
    /* 更新商品分类 */
    $sql = "UPDATE " .$ecs->table('exchange_goods'). " SET cat_id = '$target_cat_id' ".
        "WHERE cat_id = '$cat_id'";
    if ($db->query($sql))
    {
        /* 清除缓存 */
        clear_cache_files();
        /* 提示信息 */
        $link[] = array('text' => $_LANG['go_back'], 'href' => 'exchange_category.php?act=list');
        sys_msg($_LANG['move_cat_success'], 0, $link);
    }
}
//-- 编辑排序序号
if ($_REQUEST['act'] == 'edit_sort_order')
{
    check_authz_json('exchange_category_manage');

    $id = intval($_POST['id']);
    $val = intval($_POST['val']);
    if (cat_update($id, array('sort_order' => $val)))
    {
        clear_cache_files(); // 清除缓存
        make_json_result($val);
    }
    else
    {
        make_json_error($db->error());
    }
}
//-- 编辑数量单位
if ($_REQUEST['act'] == 'edit_measure_unit')
{
    check_authz_json('exchange_category_manage');
    $id = intval($_POST['id']);
    $val = json_str_iconv($_POST['val']);

    if (cat_update($id, array('measure_unit' => $val)))
    {
        clear_cache_files(); // 清除缓存
        make_json_result($val);
    }
    else
    {
        make_json_error($db->error());
    }
}
//-- 编辑排序序号
if ($_REQUEST['act'] == 'edit_grade')
{
    check_authz_json('exchange_category_manage');
    $id = intval($_POST['id']);
    $val = intval($_POST['val']);
    if($val > 10 || $val < 0)
    {
        /* 价格区间数超过范围 */
        make_json_error($_LANG['grade_error']);
    }

    if (cat_update($id, array('grade' => $val)))
    {
        clear_cache_files(); // 清除缓存
        make_json_result($val);
    }
    else
    {
        make_json_error($db->error());
    }
}
//-- 删除商品分类
if ($_REQUEST['act'] == 'remove')
{
    check_authz_json('exchange_category_manage');

    /* 初始化分类ID并取得分类名称 */
    $cat_id   = intval($_GET['id']);
    $cat_name = $db->getOne('SELECT cat_name FROM ' .$ecs->table('exchange_category'). " WHERE cat_id='$cat_id'");

    /* 当前分类下是否有子分类 */
    $cat_count = $db->getOne('SELECT COUNT(*) FROM ' .$ecs->table('exchange_category'). " WHERE parent_id='$cat_id'");

    /* 当前分类下是否存在商品 */
    $goods_count = $db->getOne('SELECT COUNT(*) FROM ' .$ecs->table('exchange_goods'). " WHERE cat_id='$cat_id'");

    /* 如果不存在下级子分类和商品，则删除之 */
    if ($cat_count == 0 && $goods_count == 0)
    {
        /* 删除分类 */
        $sql = 'DELETE FROM ' .$ecs->table('exchange_category'). " WHERE cat_id = '$cat_id'";
        if ($db->query($sql))
        {
            clear_cache_files();
            admin_log($cat_name, 'remove', 'exchange_category');
        }
    }
    else
    {
        make_json_error($cat_name .' '. $_LANG['cat_isleaf']);
    }

    $url = 'exchange_category.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
    ecs_header("Location: $url\n");
    exit;
}

/**
 * 获得积分商品分类的所有信息
 * @param   integer     $cat_id     指定的分类ID
 * @return  mix
 */
function get_cat_info($cat_id)
{
    $sql = "SELECT * FROM " .$GLOBALS['ecs']->table('exchange_category'). " WHERE cat_id='$cat_id' LIMIT 1";
    return $GLOBALS['db']->getRow($sql);
}

/**
 * 修改积分商品分类
 * @param   integer $cat_id
 * @param   array   $args
 * @return  mix
 */
function cat_update($cat_id, $args)
{
    if (empty($args) || empty($cat_id))
    {
        return false;
    }

    return $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('exchange_category'), $args, 'update', "cat_id='$cat_id'");
}

?>
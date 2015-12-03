<?php
// 商品管理程序

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . '/' . ADMIN_PATH . '/includes/lib_goods.php');

//-- 商品列表，商品回收站
if ($_REQUEST['act'] == 'list')
{
    admin_priv('mall_goods_manage');

    $cat_id = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
    $area_id = empty($_REQUEST['area_id']) ? '' :intval($_REQUEST['area_id']);
    /* 供货商名 */
    $ur_here = $_LANG['01_goods_list'];
    $smarty->assign('ur_here', $ur_here);

    $smarty->assign('action_link',  $action_link);
    $smarty->assign('cat_list',     cat_list(0, $cat_id));
    $smarty->assign('brand_list',   get_brand_list());
    $smarty->assign('intro_list',   get_intro_list());
    $smarty -> assign('regions',get_regions(1,1));
    $smarty->assign('lang',         $_LANG);
    $smarty->assign('list_type',    $_REQUEST['act'] == 'list' ? 'goods' : 'trash');
    $smarty->assign('use_storage',  empty($_CFG['use_storage']) ? 0 : 1);

    $goods_list = goods_mall_list($_REQUEST['act'] == 'list' ? 0 : 1);
    $smarty->assign('goods_list',   $goods_list['goods']);
    $smarty->assign('filter',       $goods_list['filter']);
    $smarty->assign('record_count', $goods_list['record_count']);
    $smarty->assign('page_count',   $goods_list['page_count']);
    $smarty->assign('full_page',    1);

    /* 排序标记 */
    $sort_flag  = sort_flag($goods_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    /* 获取商品类型存在规格的类型 */
    $specifications = get_goods_type_specifications();
    $smarty->assign('specifications', $specifications);

    /* 显示商品列表页面 */
    assign_query_info();
    $smarty->display('mall_goods_list.htm');
}
elseif ($_REQUEST['act'] == 'query')
{
    $is_delete = empty($_REQUEST['is_delete']) ? 0 : intval($_REQUEST['is_delete']);
    $code = empty($_REQUEST['extension_code']) ? '' : trim($_REQUEST['extension_code']);
    $goods_list = goods_list($is_delete, 1 );

    $smarty->assign('goods_list',   $goods_list['goods']);
    $smarty->assign('filter',       $goods_list['filter']);
    $smarty->assign('record_count', $goods_list['record_count']);
    $smarty->assign('page_count',   $goods_list['page_count']);

    /* 排序标记 */
    $sort_flag  = sort_flag($goods_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    /* 获取商品类型存在规格的类型 */
    $specifications = get_goods_type_specifications();
    $smarty->assign('specifications', $specifications);
    make_json_result($smarty->fetch('mall_goods_list.htm'), '',array('filter' => $goods_list['filter'], 'page_count' => $goods_list['page_count']));
}

elseif ($_REQUEST['act'] == 'batch')
{
    $code = empty($_REQUEST['extension_code'])? '' : trim($_REQUEST['extension_code']);

    /* 取得要操作的商品编号 */
    $goods_id = !empty($_POST['checkboxes']) ? join(',', $_POST['checkboxes']) : 0;

    if (isset($_POST['type']))
    {
        /* 设为精品 */
        if ($_POST['type'] == 'best')
        {
            /* 检查权限 */
            admin_priv('goods_manage');
            update_goods($goods_id, 'is_best', '1');
        }
        /* 取消精品 */
        elseif ($_POST['type'] == 'not_best')
        {
            /* 检查权限 */
            admin_priv('goods_manage');
            update_goods($goods_id, 'is_best', '0');
        }
    }
    /* 清除缓存 */
    clear_cache_files();

    $link[0] = array('href' => 'mall_goods.php?act=list', 'text' => $_LANG['12_mall_goods']);
    sys_msg($_LANG['batch_handle_ok'], 0, $link);
}

/*------------------------------------------------------ */
//-- 修改精品推荐状态
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_best')
{
    check_authz_json('mall_goods_manage');

    $goods_id = intval($_POST['id']);
    $is_best = intval($_POST['val']);
    $sql = "UPDATE ". $GLOBALS['ecs']->table('goods') ." SET is_best='".$is_best."',last_update=" .gmtime()." WHERE goods_id='".$goods_id."'";
    if($GLOBALS['db']->query($sql)) {
        clear_cache_files();
        make_json_result($is_best);
    }
}


/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */


/**
 * 列表链接
 * @param   bool    $is_add         是否添加（插入）
 * @param   string  $extension_code 虚拟商品扩展代码，实体商品为空
 * @return  array('href' => $href, 'text' => $text)
 */
function list_link($is_add = true, $extension_code = '')
{
    $href = 'goods.php?act=list';
    if (!empty($extension_code))
    {
        $href .= '&extension_code=' . $extension_code;
    }
    if (!$is_add)
    {
        $href .= '&' . list_link_postfix();
    }

    if ($extension_code == 'virtual_card')
    {
        $text = $GLOBALS['_LANG']['50_virtual_card_list'];
    }
    else
    {
        $text = $GLOBALS['_LANG']['01_goods_list'];
    }

    return array('href' => $href, 'text' => $text);
}

/**
 * 检查图片网址是否合法
 *
 * @param string $url 网址
 *
 * @return boolean
 */
function goods_parse_url($url)
{
    $parse_url = @parse_url($url);
    return (!empty($parse_url['scheme']) && !empty($parse_url['host']));
}
/**
 * 获得商品列表
 *
 * @access  public
 * @params  integer $isdelete
 * @params  integer $real_goods
 * @params  integer $conditions
 * @return  array
 */
function goods_mall_list($is_delete, $real_goods=1, $conditions = '')
{
    /* 过滤条件 */
    $param_str = '-' . $is_delete . '-' . $real_goods;
    $result = get_filter($param_str);
    if ($result === false)
    {
        $day = getdate();
        $today = local_mktime(23, 59, 59, $day['mon'], $day['mday'], $day['year']);

        $filter['cat_id']           = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
        $filter['intro_type']       = empty($_REQUEST['intro_type']) ? '' : trim($_REQUEST['intro_type']);
        $filter['is_promote']       = empty($_REQUEST['is_promote']) ? 0 : intval($_REQUEST['is_promote']);
        $filter['stock_warning']    = empty($_REQUEST['stock_warning']) ? 0 : intval($_REQUEST['stock_warning']);
        $filter['brand_id']         = empty($_REQUEST['brand_id']) ? 0 : intval($_REQUEST['brand_id']);
        $filter['area_id']         = empty($_REQUEST['area_id']) ? 0 : intval($_REQUEST['area_id']);
        $filter['keyword']          = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
        $filter['suppliers_id'] = isset($_REQUEST['suppliers_id']) ? (empty($_REQUEST['suppliers_id']) ? '' : trim($_REQUEST['suppliers_id'])) : '';
        $filter['is_on_sale'] = isset($_REQUEST['is_on_sale']) ? ((empty($_REQUEST['is_on_sale']) && $_REQUEST['is_on_sale'] === 0) ? '' : trim($_REQUEST['is_on_sale'])) : '';
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keyword'] = json_str_iconv($filter['keyword']);
        }
        $filter['sort_by']          = empty($_REQUEST['sort_by']) ? 'goods_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order']       = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
        $filter['extension_code']   = empty($_REQUEST['extension_code']) ? '' : trim($_REQUEST['extension_code']);
        $filter['is_delete']        = $is_delete;
        $filter['real_goods']       = $real_goods;

        $where = $filter['cat_id'] > 0 ? " AND " . get_children($filter['cat_id']) : '';

        /* 推荐类型 */
        switch ($filter['intro_type'])
        {
            case 'is_best':
                $where .= " AND g.is_best=1";
                break;
            case 'is_hot':
                $where .= ' AND g.is_hot=1';
                break;
            case 'is_new':
                $where .= ' AND g.is_new=1';
                break;
            case 'is_promote':
                $where .= " AND g.is_promote = 1 AND g.promote_price > 0 AND g.promote_start_date <= '$today' AND g.promote_end_date >= '$today'";
                break;
            case 'all_type';
                $where .= " AND (g.is_best=1 OR g.is_hot=1 OR g.is_new=1 OR (g.is_promote = 1 AND g.promote_price > 0 AND g.promote_start_date <= '" . $today . "' AND g.promote_end_date >= '" . $today . "'))";
        }

        /* 库存警告 */
        if ($filter['stock_warning'])
        {
            $where .= ' AND g.goods_number <= g.warn_number ';
        }

        /* 品牌 */
        if ($filter['brand_id'])
        {
            $where .= " AND g.brand_id='$filter[brand_id]'";
        }
        if($filter['area_id']) {
            $where .= " AND g.area_id='$filter[area_id]'";
        }
        /* 扩展 */
        if ($filter['extension_code'])
        {
            $where .= " AND g.extension_code='$filter[extension_code]'";
        }
        /* 关键字 */
        if (!empty($filter['keyword']))
        {
            $where .= " AND (g.goods_sn LIKE '%" . mysql_like_quote($filter['keyword']) . "%' OR g.goods_name LIKE '%" . mysql_like_quote($filter['keyword']) . "%')";
        }

        if ($real_goods > -1)
        {
            $where .= " AND g.is_real='$real_goods'";
        }

        /* 上架 */
        if ($filter['is_on_sale'] !== '')
        {
            $where .= " AND (g.is_on_sale = '" . $filter['is_on_sale'] . "')";
        }

        /* 供货商 */
        if (!empty($filter['suppliers_id']))
        {
            $where .= " AND (g.suppliers_id = '" . $filter['suppliers_id'] . "')";
        }

        $where .= $conditions;

        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('goods'). " AS g WHERE  is_delete='$is_delete' $where";
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT rg.region_name,g.goods_id, g.goods_name, g.goods_type, g.goods_sn, g.shop_price,g.market_price, g.is_on_sale,
            g.is_best, g.is_new, g.is_hot, g.sort_order, g.goods_number, g.integral, " .
            " (g.promote_price > 0 AND g.promote_start_date <= '$today' AND g.promote_end_date >= '$today') AS is_promote ".
            " FROM " . $GLOBALS['ecs']->table('goods') . " AS g INNER JOIN ".$GLOBALS['ecs']->table('region')." AS rg ON g.area_id=rg.region_id WHERE  g.is_delete='$is_delete' $where" .
            " ORDER BY $filter[sort_by] $filter[sort_order] ".
            " LIMIT " . $filter['start'] . ",$filter[page_size]";

        $filter['keyword'] = stripslashes($filter['keyword']);
        set_filter($filter, $sql, $param_str);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    $row = $GLOBALS['db']->getAll($sql);
    return array('goods' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

?>
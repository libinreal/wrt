<?php
// 工程定制

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');

if (empty($_REQUEST['act'])) {
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = strtolower(trim($_REQUEST['act']));
}
$smarty -> assign('lang',$_LANG);

if($_REQUEST['act'] == 'list_arr') {
    admin_priv('order_view');
    $smarty->assign('ur_here', "定制专区");
    $smarty->assign('full_page',        1);

    assign_query_info();
    $smarty->display('custom_list_arr.htm');
}
if ($_REQUEST['act'] == 'list') {

    $filter = array();
    $smarty->assign('ur_here',      $_LANG['custom_list']);
    $smarty->assign('full_page',    1);
    $smarty->assign('filter',       $filter);
    $custom = getCustom();
    $sort_flag  = sort_flag($custom['filter']);

    $smarty->assign('custom',          $custom['arr']);
    $smarty->assign('filter',          $custom['filter']);
    $smarty->assign('record_count',    $custom['record_count']);
    $smarty->assign('page_count',      $custom['page_count']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('custom_list.html');
}
elseif ($_REQUEST['act'] == 'query')
{
    /* 取得过滤条件 */
    $filter = array();
    $smarty->assign('ur_here',      $_LANG['custom_list']);
    $smarty->assign('filter',       $filter);
    $custom = getCustom();

    $smarty->assign('custom',    $custom['arr']);
    $smarty->assign('filter',          $custom['filter']);
    $smarty->assign('record_count',    $custom['record_count']);
    $smarty->assign('page_count',      $custom['page_count']);

    /* 获取商品类型存在规格的类型 */
    $sort_flag  = sort_flag($custom['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('custom_list.html'), '',
        array('filter' => $custom['filter'], 'page_count' => $custom['page_count']));
}
elseif ($_REQUEST['act'] == 'info')
{
    $proId = intval($_REQUEST['id']);
    $smarty->assign('ur_here',      $_LANG['custom_list']);
    $action_link = array('href' => 'custom.php?act=list', 'text' => $_LANG['custom_list']);
    $smarty->assign('action_link',  $action_link);
    $smarty->assign('full_page',    1);
    $sql = " SELECT cus.*,reg.region_name FROM ".$GLOBALS['ecs']->table('customize_project')." AS cus ".
        " INNER JOIN ".$GLOBALS['ecs']->table('region')." AS reg ON cus.areaId=reg.region_id ".
        " WHERE  cus.proId=".$proId."";

    $custom = $GLOBALS['db'] -> getRow($sql);
    $custom['proMoney']   = price_format($custom['proMoney']);
    $custom['cusStatus']  = $custom['status'];
    $custom['createAt'] = local_date($GLOBALS['_CFG']['time_format'],$custom['createAt']);
    $smarty -> assign('list',$custom);
    assign_query_info();
    $smarty->display('custom_info.html');
}
elseif ($_REQUEST['act'] == 'status')
{
    admin_priv('custom_state');
    $proId = intval($_REQUEST['id']);
    $sql = "SELECT status,proName FROM ".$GLOBALS['ecs']->table('customize_project')." WHERE proId=".$proId." ";
    $custom = $GLOBALS['db'] -> getRow($sql);
    if($custom['status'] ==0 ) {
        $sqlStatus = "UPDATE ".$GLOBALS['ecs']->table('customize_project')." SET status=1 WHERE proId =".$proId." ";
    } elseif ($custom['status'] ==1) {
        $sqlStatus = "UPDATE ".$GLOBALS['ecs']->table('customize_project')." SET status=2 WHERE proId =".$proId." ";
    }
    if($GLOBALS['db']->query($sqlStatus)) {
        $msg = $GLOBALS['db'] -> getOne($sql);
        admin_log($msg['proName'], 'status', 'customize_project');
    }
    echo $msg['cusStatus'];
}
// 工程定制
function getCustom() {
    $result = get_filter();
    if ($result === false)
    {
        $filter = array();
        $filter['keyword'] = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keyword'] = json_str_iconv($filter['keyword']);
        }

        $filter['telephone'] = empty($_REQUEST['telephone']) ? '' : trim($_REQUEST['telephone']);
        $filter['contacts'] = empty($_REQUEST['contacts']) ? '' : trim($_REQUEST['contacts']);

        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'cus.proId' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $where = '';
        if(!empty($filter['keyword'])) {
            $where .= " AND cus.proName LIKE '%".$filter['keyword']."%' ";
        }
        if(!empty($filter['telephone'])) {
            $where = " AND cus.telephone LIKE '%".$filter['telephone']."%' ";
        }
        if(!empty($filter['contacts'])) {
            $where = " AND cus.contacts LIKE '%".$filter['contacts']."%' ";
        }
        /* 定制总数 */
        $sql = " SELECT count(*) FROM ".$GLOBALS['ecs']->table('customize_project')." AS cus ".
            " INNER JOIN ".$GLOBALS['ecs']->table('region')." AS reg ON cus.areaId=reg.region_id ".
            " WHERE 1  ".$where." ORDER by ".$filter['sort_by']." ".$filter['sort_order']." ";

        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        $filter = page_and_size($filter);

        /* 获取定制数据 */
        $sql = " SELECT cus.*,reg.region_name FROM ".$GLOBALS['ecs']->table('customize_project')." AS cus ".
               " INNER JOIN ".$GLOBALS['ecs']->table('region')." AS reg ON cus.areaId=reg.region_id ".
               " WHERE 1  ".$where." ORDER by ".$filter['sort_by']." ".$filter['sort_order']." ";
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
        $rows['proMoney']   = price_format($rows['proMoney']);
        $rows['cusStatus']  = $rows['status'];
        $rows['createAt'] = local_date($GLOBALS['_CFG']['time_format'],$rows['createAt']);
        $arr[] = $rows;
    }
    return array('arr' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}
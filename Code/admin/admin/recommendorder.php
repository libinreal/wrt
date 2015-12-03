<?php
// 推荐订单
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
$exc = new exchange($ecs->table('order_recommend'), $db, 'id', 'prjName');

/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = strtolower(trim($_REQUEST['act']));
}
//-- 推荐订单列表
if ($_REQUEST['act'] == 'list')
{
    admin_priv('orderrecommend_manage');
    $smarty->assign('ur_here',     $_LANG['recommend_order']);
    $smarty->assign('full_page',   1);

    $contract_list = get_contractlist();
    $smarty->assign('contract_list',    $contract_list['arr']);
    $smarty->assign('filter',          $contract_list['filter']);
    $smarty->assign('record_count',    $contract_list['record_count']);
    $smarty->assign('page_count',      $contract_list['page_count']);
    $sort_flag  = sort_flag($contract_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('recommendorder_list.htm');
}
// 推荐订单翻页 查询
elseif ($_REQUEST['act'] == 'query')
{
    admin_priv('orderrecommend_manage');
    $contract_list = get_contractlist();
    $smarty->assign('contract_list',    $contract_list['arr']);
    $smarty->assign('filter',          $contract_list['filter']);
    $smarty->assign('record_count',    $contract_list['record_count']);
    $smarty->assign('page_count',      $contract_list['page_count']);
    $sort_flag  = sort_flag($contract_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    $sort_flag  = sort_flag($contract_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('recommendorder_list.htm'), '',array('filter' => $contract_list['filter'], 'page_count' => $contract_list['page_count']));
}

//-- 新建推荐订单
elseif ($_REQUEST['act'] == 'add')
{
    admin_priv('orderrecommend_manage');
    /* 取得合同数据 */
    $sql = "SELECT * FROM " .$ecs->table('contract'). " WHERE conFnum = '".$_REQUEST['conFnum']."'";
    $contract = $db->getRow($sql);
    $contract['endDate'] = local_date('Y-m-d', $contract['endDate']);
    $contract['Banks'] = unserialize($contract['Banks']);
    $contract['Mats'] = unserialize($contract['Mats']);

    $sqlUsers = "SELECT customNo,contacts FROM ".$GLOBALS['ecs'] -> table('users')." WHERE customNo LIKE '".substr($contract['cusFnum'],0,2)."%'";
    $userInfo = $GLOBALS['db'] -> getAll($sqlUsers);
    /*取得商品编号*/
    if (is_array($contract['Mats'])) {
        $goodsArr = array(array());
        foreach($contract['Mats'] as $key=>$value) {
            if($key==0) {
                if (strlen($value['matGroupFnum'])==2) $value['matGroupFnum'] = $value['matGroupFnum'].'0000';
                if (strlen($value['matGroupFnum'])==4) $value['matGroupFnum'] = $value['matGroupFnum'].'00';
                if(strlen($value['matGroupFnum']) == 6) {
                    $sql = " SELECT g.goods_id,g.wcode,g.goods_name,g.cat_code,g.shop_price,g.goods_number,b.brand_name,s.suppliers_name,rg.region_name ".
                        " FROM ".$GLOBALS['ecs'] -> table('goods')." AS g ".
                        " LEFT JOIN ".$GLOBALS['ecs']->table("region")." as rg ON g.area_id=rg.region_id ".
                        " LEFT JOIN ".$GLOBALS['ecs'] -> table('brand')." AS b ON g.brand_id=b.brand_id ".
                        " LEFT JOIN ".$GLOBALS['ecs']->table('suppliers')." AS s ON g.suppliers_id=s.suppliers_id ".
                        " WHERE wcode LIKE '" . $categoryCode."%'";
                    $GoodsArrInfo = $GLOBALS['db']->getALL($sql);
                    //echo $sql
                    $goodsArr = array_merge($goodsArr,$GoodsArrInfo);
                }
            }
        }
    }
    /* 模板赋值 */
    $smarty->assign('ur_here',     $_LANG['recommendorder_add']);
    $smarty->assign('action_link', array('href'=>'recommendorder.php?act=list&' . list_link_postfix(), 'text' => $_LANG['03_recommendorder_list']));
    $smarty->assign('form_act',    'insert');
    $smarty->assign('action',      'add');

    $smarty->assign('contract_info',    $contract);
    $smarty->assign('goodsInfo',    $goodsArr);
    $smarty -> assign('userInfo',$userInfo);
    assign_query_info();
    $smarty->display('recommendorder_info.htm');
}

//-- 编辑链接的处理页面
elseif ($_REQUEST['act'] == 'insert')
{
    /* 权限判断 */
    admin_priv('orderrecommend_manage');
    $conFnum = trim($_REQUEST['conFnum']);
    $cusFnum = trim($_REQUEST['cusFnum']);
    $prjNum = trim($_REQUEST['prjNum']);
    $conAmt = trim($_REQUEST['conAmt']);
    $prjName = trim($_REQUEST['prjName']);
    $endDate = trim(strtotime($_REQUEST['endDate']));
    $goodsId = $_REQUEST['goodsId'];
    $goodsId = implode(',',$goodsId);
    /*检查是否重复*/
    if(!empty($_POST['prjNum'])){
        $is_only = $exc->is_only('conChildFnum', $_POST['prjNum'],0, "");
        if (!$is_only){
            sys_msg(sprintf($_LANG['prjNum_exsits'], stripslashes($_POST['prjNum'])), 1);
        }
    }
    $sql = "INSERT INTO ".$GLOBALS['ecs'] -> table('order_recommend') ."(conFnum,cusFnum,conChildFnum,conCount,prjName,endDate,goodsid) ".
            "VALUES('".$conFnum."','".$cusFnum."','".$prjNum."','".$conAmt."','".$prjName."','".$endDate."','".$goodsId."')";
    if($GLOBALS['db'] -> query($sql)){
        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'recommendorder.php?act=list';
        admin_log($_POST['conFnum'],'add','recommendorder');
        clear_cache_files(); // 清除相关的缓存文件
        sys_msg($_LANG['recommendorderadd_succeed'],0, $link);
    }
}
elseif ($_REQUEST['act'] == 'recommendgoods') {
    admin_priv('orderrecommend_manage');
    $categoryCode = $_REQUEST['cat_code'];
    if (strlen($categoryCode) == 4) $categoryCode = $categoryCode."00";
    if (strlen($categoryCode) ==2) $categoryCode = $categoryCode."0000";

    $sql = " SELECT g.goods_id,g.wcode,g.goods_name,g.cat_code,g.shop_price,g.goods_number,b.brand_name,s.suppliers_name,rg.region_name ".
        " FROM ".$GLOBALS['ecs'] -> table('goods')." AS g ".
        " LEFT JOIN ".$GLOBALS['ecs']->table("region")." as rg ON g.area_id=rg.region_id ".
        " LEFT JOIN ".$GLOBALS['ecs'] -> table('brand')." AS b ON g.brand_id=b.brand_id ".
        " LEFT JOIN ".$GLOBALS['ecs']->table('suppliers')." AS s ON g.suppliers_id=s.suppliers_id ".
        " WHERE wcode LIKE '" . $categoryCode."%'";
    $GoodsArrInfo = $GLOBALS['db']->getALL($sql);
    echo json_encode($GoodsArrInfo);
}
/* 获取合同数据列表 */
function get_contractlist()
{
    $result = get_filter();
    if ($result === false)
    {
        $filter = array();
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['cusName'] = empty($_REQUEST['cusName']) ? '' : trim($_REQUEST['cusName']);
            $filter['conFnum'] = empty($_REQUEST['conFnum']) ? '' : trim($_REQUEST['conFnum']);
            $filter['conName'] = empty($_REQUEST['conName']) ? '' : trim($_REQUEST['conName']);
        }

        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $where .= '';
        if (!empty($filter['cusName'])) {
            $where .= " AND u.user_name LIKE '%".$filter['cusName']."%'";
        }
        if (!empty($filter['conFnum'])) {
            $where .= " AND e.conFnum LIKE '%".$filter['conFnum']."%'";
        }
        if (!empty($filter['conName'])) {
            $where .= " AND e.conName LIKE '%".$filter['conName']."%'";
        }

        /* 合同总数 */
        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('contract'). 'AS e '.
            'LEFT JOIN'. $GLOBALS['ecs']->table('users'). 'AS u ON e.cusFnum = u.customNo ' .
            'WHERE 1 ' .$where;

        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        $filter = page_and_size($filter);
        /* 获取合同数据 */
        $sql = 'SELECT e.*,u.user_name FROM ' .$GLOBALS['ecs']->table('contract'). 'AS e '.
            'LEFT JOIN'. $GLOBALS['ecs']->table('users'). 'AS u ON e.cusFnum = u.customNo ' .
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
    $state = array(1=>'保存状态', 2=>'提交审核', 3=>'已审核');
    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
        $rows['strDate'] = local_date($GLOBALS['_CFG']['time_format'], $rows['strDate']);
        $rows['endDate'] = local_date($GLOBALS['_CFG']['time_format'], $rows['endDate']);
        $rows['conAmt'] = price_format($rows['conAmt']);
        $rows['Banks'] = json_decode($rows['Banks'],true);
        $rows['Mats'] = json_decode($rows['Mats'],true);
        $rows['conState'] = $state[$rows['conState']];
        $arr[] = $rows;
    }
    return array('arr' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

?>
<?php

/**
 * 管理中心新闻处理程序文件
 * $Author: xy $
 * $Id: article.php 2014-09-03 xy $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/admin/statistic.php');
if (empty($_REQUEST['act'])) {
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = strtolower(trim($_REQUEST['act']));
}
$smarty->assign('lang', $_LANG);

if($_REQUEST['act'] == 'list_arr') {
    admin_priv('order_view');
    $smarty->assign('ur_here', "报表管理");
    $smarty->assign('full_page',        1);

    assign_query_info();
    $smarty->display('status_list_arr.htm');
}

if ($_REQUEST['act'] == 'list')
{
    admin_priv('credit_analysis_stats');
    /* 取得过滤条件 */
    $filter = array();
    $smarty->assign('ur_here',      $_LANG['03_article_list']);
    $smarty->assign('full_page',    1);
    $smarty->assign('filter',       $filter);
    $reportArr = getReport();
    $smarty->assign('ur_here',          $_LANG['sales_analysis']);
    $smarty->assign('full_page',        1);
    $smarty->assign('start_date',       local_date('Y-m-d', $reportArr['start_date']));
    $smarty->assign('end_date',         local_date('Y-m-d', $reportArr['end_date']));
    $smarty->assign('cfg_lang',     $_CFG['lang']);
    $smarty->assign('report',     $reportArr['dataTime']);
    $smarty->assign('dataInfo',   $reportArr['dataInfo']);
    assign_query_info();
    $smarty->display('sales_analysis.html');
}

function getReport() {
    $sql = "select cat_id,cat_name,code from ".$GLOBALS['ecs'] -> table('category')." where cat_level=1";
    $resCategory = $GLOBALS['db']->getAll($sql);

    if (isset($_REQUEST['start_date']) && !empty($_REQUEST['end_date']))
    {
        $start_date = strtotime($_REQUEST['start_date']);
        $end_date = strtotime($_REQUEST['end_date']);
    }
    else
    {
        $Strtoday = strtotime(date('Y-m-d', time()));
        $start_date = $Strtoday - 86400 * 7;
        $end_date   = $Strtoday;
    }
    $len = ($end_date - $start_date)/86400;
    for($i=1;$i<=$len;$i++) {
        $arr .= ','.date('m-d',($start_date+86400*$i));
    }
    $arr = substr($arr,1);
    foreach($resCategory as $key=>$val) {
        $aArrInfo[$key]['cate'] = trim(htmlspecialchars($val['cat_name']));
        for($i=1;$i<=$len;$i++) {
            $sqlSum = " SELECT SUM(oi.order_amount) FROM ".$GLOBALS['ecs']->table('goods')." AS g ".
                " INNER JOIN ".$GLOBALS['ecs']->table('order_goods')." AS og ON g.goods_id=og.goods_id ".
                " INNER JOIN ".$GLOBALS['ecs']->table('order_info')." AS oi ON og.order_id=oi.order_id ".
                " INNER JOIN ".$GLOBALS['ecs']->table('category')." AS c ON g.cat_id=c.cat_id ".
                " WHERE g.cat_code LIKE '".substr($val['code'],0,3)."%' AND oi.order_status IN (1,2,3,4) ".
                " AND oi.parent_order_id=0 AND oi.add_time >".($start_date+86400*$i)." AND oi.add_time < ".($start_date+(86400*($i+1)))."";
            $SunOrderCount = $GLOBALS['db']->getOne($sqlSum);
            if(empty($SunOrderCount)) {
                $SunOrderCount = '0.00';
            }else {
                $SunOrderCount = $SunOrderCount;
            }
            $countArr[$i] = $SunOrderCount;
            $aDateTime[$i] = date('Y-m-d' ,($Strtoday+86400*$i));
        }
        $aArrInfo[$key]['Info'] = $countArr;
    }
    return array('dataTime'=>$arr,'dataInfo'=>$aArrInfo,'end_date'=>$end_date,'start_date'=>$start_date);
}
?>
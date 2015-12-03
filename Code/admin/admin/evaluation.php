<?php

/**
 * 信用申请表单程序文件
 * $Author: xy $
 * $Id: evaluation.php 2014-09-15 xy $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/*初始化数据交换对象 */
$exc   = new exchange($ecs->table("credit_evaluation_info"), $db, 'id', 'userid');


if($_REQUEST['act'] == 'list_arr') {
    admin_priv('order_view');
    $smarty->assign('ur_here', "信用池管理");
    $smarty->assign('full_page',        1);

    assign_query_info();
    $smarty->display('eval_list_arr.htm');
}
/*------------------------------------------------------ */
//-- 列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 取得过滤条件 */
    $filter = array();
    $smarty->assign('ur_here',      $_LANG['evaluation_list']);
    $nature_list = array(1=>'央企', 2=>'国企', 3=>'股份制', 4=>'私企');
    $smarty->assign('nature_list',     $nature_list);
    $smarty->assign('full_page',    1);
    $smarty->assign('filter',       $filter);

    $evaluation_list = get_evaluationlist();

    $smarty->assign('evaluation_list',    $evaluation_list['arr']);
    $smarty->assign('filter',          $evaluation_list['filter']);
    $smarty->assign('record_count',    $evaluation_list['record_count']);
    $smarty->assign('page_count',      $evaluation_list['page_count']);
    $sort_flag  = sort_flag($evaluation_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('evaluation_list.htm');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    /* 取得过滤条件 */
    $filter = array();
    $smarty->assign('ur_here',      $_LANG['evaluation_list']);
    $nature_list = array(1=>'央企', 2=>'国企', 3=>'股份制', 4=>'私企');
    $smarty->assign('nature_list',     $nature_list);
    $smarty->assign('filter',       $filter);

    $evaluation_list = get_evaluationlist();

    $smarty->assign('evaluation_list',    $evaluation_list['arr']);
    $smarty->assign('filter',          $evaluation_list['filter']);
    $smarty->assign('record_count',    $evaluation_list['record_count']);
    $smarty->assign('page_count',      $evaluation_list['page_count']);

    /* 获取商品类型存在规格的类型 */
    $sort_flag  = sort_flag($evaluation_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('evaluation_list.htm'), '',array('filter' => $evaluation_list['filter'], 'page_count' => $evaluation_list['page_count']));
}

/*------------------------------------------------------ */
//-- 追加定制信息列表列表
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'info'){
    /* 根据订单id或订单号查询订单信息 */
    if (isset($_REQUEST['id']))
    {
        $info = get_evaluation_info();
    }

    $smarty->assign('ur_here',      $_LANG['evaluation_info']);
    $action_link = ($_REQUEST['act'] == 'list') ? add_link($code) : array('href' => 'evaluation.php?act=list', 'text' => $_LANG['evaluation_list']);
    $smarty->assign('action_link',  $action_link);
    $smarty->assign('info',    $info);

    assign_query_info();
    $smarty->display('evaluation_info.htm');
}

/*------------------------------------------------------ */
//-- 修改定制状态
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'state')
{
    /* 权限判断 */
    admin_priv('evaluation_state');
    $id = intval($_REQUEST['id']);
    $old_state = intval($_REQUEST['old_state']);
    $remark = '';
    $db_state = $exc->get_name($id,'status');
    if($db_state == $old_state){
        switch($old_state){
            case 1:
                $status = 2;
                $statusInfo = '受理成功';
                break;
            case 2:
                $status = 3;
                $statusInfo = '审核通过';
                break;
            case 3:
                $final = $_REQUEST['final'];
                if($final == "success"){
                    $status = 4;
                    $statusInfo = '评测成功';
                }elseif($final == "fail"){
                    $status = 5;
                    $statusInfo = '评测失败';
                }
                $remark = addslashes($_POST['remark']);
                break;
        }
    }

    if ($exc->edit("status='$status', remark='$remark'", $id))
    {
        $URI =  $ecs->url()."global/sendsms";

        $sqls = " SELECT U.contactsPhone FROM ".$GLOBALS['ecs']->table('credit_evaluation_info')." AS CI ".
                " INNER JOIN ".$GLOBALS['ecs']->table('users')." AS U ON CI.user_id=U.user_id ".
                " WHERE CI.id=$id";
        $mobiles = $GLOBALS['db']->getOne($sqls);
        $contentInfo = '尊敬的中交会员，您的信用申请'.$statusInfo.'。';
        $content = array('mobiles'=>$mobiles,'content'=>$contentInfo);
        get_evaluationlist_msg($URI,$content);
        $sql1 = "SELECT * FROM " .$ecs->table('credit_evaluation_info'). " WHERE id='$id'";
        $msg = $db->GetRow($sql1);
        clear_cache_files();
        admin_log($id, 'state', 'evaluation');
        echo json_encode($msg);
    }
    else
    {
        echo $db->error();
    }
}
elseif ($_REQUEST['act'] == 'edit_remark')
{
    check_authz_json('evaluation_state');

    $id    = intval($_POST['id']);
    $remark = addslashes($_POST['remark']);
    if ($exc->edit("remark = '$remark'", $id)){
        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'evaluation.php?act=info&id=' .$id ;

        $note = sprintf($_LANG['remarkedit_succeed'], stripslashes($_POST['id']));
        admin_log($_POST['id'], 'edit', 'evaluation');
        clear_cache_files();
        sys_msg($note, 0, $link);
    }
    else
    {
        die($db->error());
    }
}

/**
 * 后台发短信调用
 * @param string $url = $ecs->url./global/sendsms
 * @param array $content = array(
 * 		'mobiles' => '接收手机号码，多个手机号码用英文逗号分隔，最多500个，不能为空',
 * 		'content' => '短信内容，最多350汉字，不能为空'
 * 	)
 * @throws Exception
 * @return boolean|mixed
 */
function get_evaluationlist_msg($url, $content) {
    if(function_exists("curl_init")) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($content));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        $ret_data = curl_exec($curl);
        if (curl_errno($curl)) {
            printf("curl call error(%s): %s\n", curl_errno($curl), curl_error($curl));
            curl_close($curl);
            return false;
        } else {
            curl_close($curl);
            return $ret_data;
        }
    } else {
        throw new Exception("[PHP] curl module is required");
    }
}
/* 获得定制列表 */
function get_evaluationlist()
{
    global $ecs;
    $result = get_filter();
    if ($result === false)
    {
        $filter = array();
        $filter['keyword']    = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keyword'] = json_str_iconv($filter['keyword']);
        }
        $filter['starttime'] = empty($_REQUEST['starttime']) ? '' : $_REQUEST['starttime'];
        $filter['endtime'] = empty($_REQUEST['endtime']) ? '' : $_REQUEST['endtime'];
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['starttime'] = gmstr2time($filter['starttime']);
            $filter['endtime'] = gmstr2time($filter['endtime']);
        }

        $filter['status'] = empty($_REQUEST['status']) ? 0 : intval($_REQUEST['status']);
        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $where = '';
        if (!empty($filter['keyword']))
        {
            $where = " AND u.user_name LIKE '%" . mysql_like_quote($filter['keyword']) . "%'";
        }
        if ($filter['status'])
        {
            $where .= " AND e.status = " . $filter['status'];
        }
        if (!empty($filter['starttime']))
        {
            $where .= " AND e.createAt >= ".$filter['starttime'];
        }
        if (!empty($filter['endtime']))
        {
            $where .= " AND e.createAt <= ".$filter['endtime'];
        }

        /* 定制总数 */
        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('credit_evaluation_info'). 'AS e '.
            'LEFT JOIN'. $GLOBALS['ecs']->table('users'). 'AS u ON e.user_id = u.user_id ' .
            'WHERE 1=1 ' .$where;

        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        $filter = page_and_size($filter);
        /* 获取定制数据 */
        $sql = 'SELECT e.*,u.user_name FROM ' .$GLOBALS['ecs']->table('credit_evaluation_info'). 'AS e '.
            'LEFT JOIN'. $GLOBALS['ecs']->table('users'). 'AS u ON e.user_id = u.user_id ' .
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
    $nature_list = array(1=>'央企', 2=>'国企', 3=>'股份制', 4=>'私企');
    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
        $rows['createAt'] = local_date('Y-m-d H:i', $rows['createAt']);
           //$rows['foundedDate'] = local_date("Y-m-d", $rows['foundedDate']);
        $businessLicense = get_image_path($rows['arr']['id'], $rows['businessLicense'], true);
        $taxcert = get_image_path($rows['id'],$rows['taxcert'],true);
        $orgcert = get_image_path($rows['id'],$rows['orgcert'],true);
        $rows['businessLicense'] = (strpos($businessLicense, 'http://') === 0) ? $businessLicense : $ecs->url() . $businessLicense;
        $rows['taxcert'] = (strpos($taxcert, 'http://') === 0) ? $taxcert : $ecs->url() . $taxcert;
        $rows['orgcert'] = (strpos($orgcert, 'http://') === 0) ? $orgcert : $ecs->url() . $orgcert;

        $rows['nature'] = $nature_list[$rows['nature']];
        $arr[] = $rows;
    }

    return array('arr' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

/* 获得详细信息 */
function get_evaluation_info(){
    global $ecs;
    $result = get_filter();
    if ($result === false)
    {
        $filter = array();
        $filter['id'] = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
        $where = '';
        if ($filter['id'])
        {
            $where = " AND id=".$filter['id'];
        }
        /* 获取定制数据 */
        $sql = 'SELECT e.*,u.user_name FROM ' .$GLOBALS['ecs']->table('credit_evaluation_info') . 'AS e '.
            'LEFT JOIN'. $GLOBALS['ecs']->table('users'). 'AS u ON e.user_id = u.user_id ' .
            'WHERE 1 '.$where;
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    $businessLicense = get_image_path($info['id'], $info['businessLicense'], true);
    $taxcert = get_image_path($info['id'],$info['taxcert'],true);
    $orgcert = get_image_path($info['id'],$info['orgcert'],true);
    $info['businessLicense'] = (strpos($businessLicense, 'http://') === 0) ? $businessLicense : $ecs->url() . $businessLicense;
    $info['taxcert'] = (strpos($taxcert, 'http://') === 0) ? $taxcert : $ecs->url() . $taxcert;
    $info['orgcert'] = (strpos($orgcert, 'http://') === 0) ? $orgcert : $ecs->url() . $orgcert;

    $info = $GLOBALS['db']->getRow($sql);
    $info['remark'] = stripslashes($info['remark']);
    $info['createAt'] = local_date($GLOBALS['_CFG']['time_format'], $info['createAt']);
    //$info['foundedDate'] = local_date("Y-m-d", $info['foundedDate']);
    $nature_list = array(1=>'央企', 2=>'国企', 3=>'股份制', 4=>'私企');
    $info['nature'] = $nature_list[$info['nature']];
    return $info;
}

?>
<?php

/**
 * 会员管理程序
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');


$exc   = new exchange($GLOBALS['ecs']->table("users"), $GLOBALS['db'], 'user_id', 'user_name','email');
/*------------------------------------------------------ */
//-- 用户帐号列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 检查权限 */
    admin_priv('users_manage');
    $smarty->assign('ur_here',      $_LANG['03_users_list']);
    $smarty->assign('action_link',  array('text' => $_LANG['04_users_add'], 'href'=>'users.php?act=add'));

    $user_list = user_list();

    $smarty->assign('user_list',    $user_list['user_list']);
    $smarty->assign('filter',       $user_list['filter']);
    $smarty->assign('record_count', $user_list['record_count']);
    $smarty->assign('page_count',   $user_list['page_count']);
    $smarty->assign('full_page',    1);
    $smarty->assign('sort_user_id', '<img src="images/sort_desc.gif">');
    assign_query_info();
    $smarty->display('users_list.htm');
}

/*------------------------------------------------------ */
//-- ajax返回用户列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $user_list = user_list();

    $smarty->assign('user_list',    $user_list['user_list']);
    $smarty->assign('filter',       $user_list['filter']);
    $smarty->assign('record_count', $user_list['record_count']);
    $smarty->assign('page_count',   $user_list['page_count']);
    $sort_flag  = sort_flag($user_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('users_list.htm'), '', array('filter' => $user_list['filter'], 'page_count' => $user_list['page_count']));
}

/*------------------------------------------------------ */
//-- 添加会员帐号
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
    /* 检查权限 */
    admin_priv('users_manage');
    
    $parents = parentList();
    $smarty->assign('parents', $parents);

    $user = array('sex'=> 0,'customLevel'=> 1);
    $smarty->assign('ur_here',$_LANG['04_users_add']);
    $smarty->assign('action_link',array('text' => $_LANG['03_users_list'], 'href'=>'users.php?act=list'));
    $smarty->assign('form_action','insert');
    $smarty->assign('user',$user);
    assign_query_info();
    $smarty->display('user_info.htm');
}
/*------------------------------------------------------ */
//-- 添加会员帐号
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert')
{
    /* 检查权限 */
    admin_priv('users_manage');
    $username = empty($_POST['username']) ? '' : trim($_POST['username']);
    $password = empty($_POST['password']) ? '' : trim($_POST['password']);
    $email = empty($_POST['email']) ? '' : trim($_POST['email']);
    $sex = empty($_POST['sex']) ? 0 : intval($_POST['sex']);
    $sex = in_array($sex, array(0, 1)) ? $sex : 0;
    //$birthday = $_POST['birthdayYear'] . '-' .  $_POST['birthdayMonth'] . '-' . $_POST['birthdayDay'];
    $qq = empty($_POST['qq']) ? '':trim($_POST['qq']);
    $weixin = empty($_POST['weixin']) ? '' : trim($_POST['weixin']);
    $companyName = empty($_POST['companyName']) ? '' : trim($_POST['companyName']);
    $companyAddress = empty($_POST['companyAddress']) ? '' : trim($_POST['companyAddress']);
    $officePhone = empty($_POST['officePhone']) ? '' : trim($_POST['officePhone']);
    $fax = empty($_POST['fax']) ? '' : trim($_POST['fax']);
    $position = empty($_POST['position']) ? '' : trim($_POST['position']);
    $projectName = empty($_POST['projectName']) ? '' : trim($_POST['projectName']);
    $projectBrief = empty($_POST['projectBrief']) ? '' : trim($_POST['projectBrief']);
    $contacts = empty($_POST['contacts']) ? '' : trim($_POST['contacts']);
    $contactsPhone = empty($_POST['contactsPhone']) ? '' : trim($_POST['contactsPhone']);
    $secondContacts = empty($_POST['secondContacts']) ? '' : trim($_POST['secondContacts']);
    $secondPhone = empty($_POST['secondPhone']) ? '' : trim($_POST['secondPhone']);
    
    $parentId = empty($_POST['parent_id']) ? '' : trim($_POST['parent_id']);
    
    $customNo = empty($_POST['customNo']) ? '' : trim($_POST['customNo']);
    $customLevel = empty($_POST['customLevel']) ? '' : trim($_POST['customLevel']);
    $customerNo = empty($_POST['customerNo']) ? '' : trim($_POST['customerNo']);
    $customerAccount = empty($_POST['customerAccount']) ? '' : trim($_POST['customerAccount']);
    $privilege = $_POST['privilege'];
    if( !empty( $privilege ) ){
        $privilege = implode(',',$privilege);
    }else{
        $privilege = '';
    }

    $customLevel = in_array($customLevel,array(0,1,2))? $customLevel : 0;
    $credit_rank = trim($_POST['credit_rank']);
    $department = trim($_POST['department']);
    //唯一性校验 用户名 邮箱和联系人手机号码
    $sql2 = "select * from ".$GLOBALS['ecs']->table('users')." where user_name='".$username."'";
    $res = $GLOBALS['db']->getRow($sql2);
    if($res['user_name']==$username) {
        sys_msg($_LANG['username_exists']);
        exit();
    }
//    $sql3 = "select * from ".$GLOBALS['ecs']->table('users')." where email='".$email."'";
//    $resEmail = $GLOBALS['db']->getRow($sql3);
//    if($resEmail['email']==$email) {
//        sys_msg($_LANG['email_exists']);
//        exit();
//    }

    $sql4 = "select * from ".$GLOBALS['ecs']->table('users')." where contactsPhone='".$contactsPhone."'";
    $resCPhone = $GLOBALS['db']->getRow($sql4);
    if($resCPhone['contactsPhone'] == $contactsPhone) {
        sys_msg($_LANG['contactsPhone_exists']);
        exit();
    }
    //libin 2016-02-02 不对会员编码做重复检查
    /*$sql5 = "select * from ".$GLOBALS['ecs']->table('users')." where customNo='".$customNo."'";
    $res = $GLOBALS['db']->getRow($sql5);
    if($res['customNo']==$customNo) {
        sys_msg($_LANG['customNo_exists']);
        exit();
    }*/

    if( !$parentId ){//请选择总账号
        sys_msg($_LANG['parentId_empty']);
        exit();
    }

    $insert_data = array();
    $insert_data['user_name'] = $username;
    $insert_data['password'] = sha1($password);
    $insert_data['email'] = $email;
    $insert_data['sex'] = $sex;
    $insert_data['qq'] = $qq;
    $insert_data['weixin'] = $weixin;
    $insert_data['companyName'] = $companyName;
    $insert_data['companyAddress'] = $companyAddress;
    $insert_data['officePhone'] = $officePhone;
    $insert_data['fax'] = $fax;
    $insert_data['position'] = $position;
    $insert_data['projectName'] = $projectName;
    $insert_data['projectBrief'] = $projectBrief;
    $insert_data['contacts'] = $contacts;
    $insert_data['contactsPhone'] = $contactsPhone;
    $insert_data['secondContacts'] = $secondContacts;
    $insert_data['secondPhone'] = $secondPhone;
    $insert_data['customNo'] = $customNo;
    $insert_data['customLevel'] = $customLevel;
    $insert_data['reg_time'] = gmtime();
    $insert_data['credit_rank'] = $credit_rank;
    $insert_data['department'] = $department;
    $insert_data['customerNo'] = $customerNo;
    $insert_data['customerAccount'] = $customerAccount;
    $insert_data['parent_id'] = $parentId;

    $sql = 'INSERT INTO ' . $GLOBALS['ecs']->table('users') .'(';
    $insert_keys = array_keys( $insert_data );
    foreach ($insert_keys as $v) {
        $sql .= '`' . $v .'`,';
    }
    $sql = substr($sql, 0, -1) . ") VALUES(";

    foreach ($insert_data as $v) {
        if( is_null( $v ) )
            $v = '';
        if(is_string( $v ) )
            $v = '\'' . $v . '\'';

        $sql = $sql . $v . ',';
    }
    $sql = substr($sql, 0, -1) . ")";

    /*
    
     $sql = "INSERT INTO ".$GLOBALS['ecs']->table('users')."".
        "(user_name,password,email,sex,qq,weixin,companyName,companyAddress,officePhone,fax,position,projectName,projectBrief,contacts,contactsPhone,secondContacts,secondPhone,customNo,customLevel,reg_time,credit_rank,department,msn,customerNo,customerAccount,parent_id)".
        " VALUES('".$username."','".sha1($password)."','".$email."','".$sex."','".$qq."','".$weixin."','".$companyName."','".$companyAddress."','".$officePhone."','".$fax."','".$position."','".$projectName."','".$projectBrief."','".$contacts."','".$contactsPhone."','".$secondContacts."','".$secondPhone."','".$customNo."','".$customLevel."','".gmtime()."','".$credit_rank."','".$department."','".$privilege."','".$customerNo."','".$customerAccount."','".$parentId."')";
    */
    $res = $GLOBALS['db']->query($sql);
    if($res) {// 添加用户成功记录日志，如果是Vip下单会员还要发送短息通知对方

        /* 记录管理员操作 */
        admin_log($_POST['username'], 'add', 'users');
        /* 提示信息 */
        $link[] = array('text' => $_LANG['go_back'], 'href'=>'users.php?act=list');
        sys_msg(sprintf($_LANG['add_success'], htmlspecialchars(stripslashes($_POST['username']))), 0, $link);
    }
}

/*------------------------------------------------------ */
//-- 编辑用户帐号
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'edit')
{
    /* 检查权限 */
    admin_priv('users_manage');
    $userId = intval($_GET['id']);
    $sql = "SELECT * from ".$GLOBALS['ecs']->table('users')." where user_id=".$userId."";
    $users = $db->GetRow($sql);
    assign_query_info();
    
    $parents = parentList();
    $smarty->assign('parents', $parents);
    $smarty->assign('ur_here',          $_LANG['users_edit']);
    $smarty->assign('action_link',      array('text' => $_LANG['03_users_list'], 'href'=>'users.php?act=list&' . list_link_postfix()));
    $smarty->assign('user',$users);
    $smarty->assign('form_action',      'update');
    $smarty->assign('special_ranks',    get_rank_list(true));
    $smarty->display('user_info.htm');
}
/*------------------------------------------------------ */
//-- 更新用户帐号
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'update')
{
    /* 检查权限 */
    admin_priv('users_manage');
    $userId = intval($_POST['id']);
    $username = empty($_POST['username']) ? '' : trim($_POST['username']);
    $password = empty($_POST['password']) ? '' : trim($_POST['password']);
    $email = empty($_POST['email']) ? '' : trim($_POST['email']);
    $sex = empty($_POST['sex']) ? 0 : intval($_POST['sex']);
    $sex = in_array($sex, array(0, 1)) ? $sex : 0;
    //$birthday = $_POST['birthdayYear'] . '-' .  $_POST['birthdayMonth'] . '-' . $_POST['birthdayDay'];
    $qq = empty($_POST['qq']) ? '':trim($_POST['qq']);
    $weixin = empty($_POST['weixin']) ? '' : trim($_POST['weixin']);
    $companyName = empty($_POST['companyName']) ? '' : trim($_POST['companyName']);
    $companyAddress = empty($_POST['companyAddress']) ? '' : trim($_POST['companyAddress']);
    $officePhone = empty($_POST['officePhone']) ? '' : trim($_POST['officePhone']);
    $fax = empty($_POST['fax']) ? '' : trim($_POST['fax']);
    $position = empty($_POST['position']) ? '' : trim($_POST['position']);
    $projectName = empty($_POST['projectName']) ? '' : trim($_POST['projectName']);
    $projectBrief = empty($_POST['projectBrief']) ? '' : trim($_POST['projectBrief']);
    $contacts = empty($_POST['contacts']) ? '' : trim($_POST['contacts']);
    $contactsPhone = empty($_POST['contactsPhone']) ? '' : trim($_POST['contactsPhone']);
    $secondContacts = empty($_POST['secondContacts']) ? '' : trim($_POST['secondContacts']);
    $secondPhone = empty($_POST['secondPhone']) ? '' : trim($_POST['secondPhone']);
    
    $parentId = empty($_POST['parent_id']) ? '' : trim($_POST['parent_id']);
    
    $customNo = empty($_POST['customNo']) ? '' : trim($_POST['customNo']);
    $customLevel = empty($_POST['customLevel']) ? '' : trim($_POST['customLevel']);
    $customerNo = empty($_POST['customerNo']) ? '' : trim($_POST['customerNo']);
    $customerAccount = empty($_POST['customerAccount']) ? '' : trim($_POST['customerAccount']);

    $privilege = $_POST['privilege'];
    $privilege = implode(',',(array)$privilege);

    //print_r($privilege);
    //exit();
    $credit_rank = trim($_POST['credit_rank']);
    $department = trim($_POST['department']);
    //唯一性校验邮箱和联系人手机号码
   /* $sql3 = "select * from ".$GLOBALS['ecs']->table('users')." where email='".$email."' and user_id!=".$userId."";
    $resEmail = $GLOBALS['db']->getRow($sql3);
    if($resEmail['email']==$email) {
        sys_msg($_LANG['email_exists']);
        exit();
    }*/
    $sql4 = "select * from ".$GLOBALS['ecs']->table('users')." where contactsPhone='".$contactsPhone."' and user_id!=".$userId." ";
    $resCPhone = $GLOBALS['db']->getRow($sql4);
    if($resCPhone['contactsPhone'] == $contactsPhone) {
        sys_msg($_LANG['contactsPhone_exists']);
        exit();
    }
    //libin 2016-02-02 编码重复暂不检查
    /*$sql5 = "select * from ".$GLOBALS['ecs']->table('users')." where customNo='".$customNo."' and user_id!=".$userId."  ";
    $res = $GLOBALS['db']->getRow($sql5);
    if($res['customNo']==$customNo) {
        sys_msg($_LANG['customNo_exists']);
        exit();
    }*/
    if(!empty($password)) {
        $sql = "UPDATE ".$GLOBALS['ecs'] -> table('users')." SET user_name='".$username."', password='".sha1($password)."',".
            " email='".$email."',sex='".$sex."',qq='".$qq."',weixin='".$weixin."',companyName='".$companyName."',".
            " companyAddress='".$companyAddress."',officePhone='".$officePhone."',fax='".$fax."',position='".$position."',".
            " projectName='".$projectName."',projectBrief='".$projectBrief."',contacts='".$contacts."',contactsPhone='".$contactsPhone."',".
            " secondContacts='".$secondContacts."',secondPhone='".$secondPhone."',parent_id='".$parentId."',customNo='".$customNo."',customLevel='".$customLevel."',credit_rank='".$credit_rank."',department='".$department."',msn='".$privilege."',customerNo='".$customerNo."',customerAccount='".$customerAccount."' Where user_id=".$userId."";
    }else {
        $sql = "UPDATE ".$GLOBALS['ecs'] -> table('users')." SET user_name='".$username."',".
            " email='".$email."',sex='".$sex."',qq='".$qq."',weixin='".$weixin."',companyName='".$companyName."',".
            " companyAddress='".$companyAddress."',officePhone='".$officePhone."',fax='".$fax."',position='".$position."',".
            " projectName='".$projectName."',projectBrief='".$projectBrief."',contacts='".$contacts."',contactsPhone='".$contactsPhone."',".
            " secondContacts='".$secondContacts."',secondPhone='".$secondPhone."',parent_id='".$parentId."',customNo='".$customNo."',customLevel='".$customLevel."',credit_rank='".$credit_rank."',department='".$department."',msn='".$privilege."',customerNo='".$customerNo."',customerAccount='".$customerAccount."' Where user_id=".$userId."";
    }
    $GLOBALS['db']->query($sql);
    /* 记录管理员操作 */
    admin_log($username, 'edit', 'users');

    /* 提示信息 */
    $links[0]['text']    = $_LANG['goto_list'];
    $links[0]['href']    = 'users.php?act=list&' . list_link_postfix();
    $links[1]['text']    = $_LANG['go_back'];
    $links[1]['href']    = 'javascript:history.back()';

    sys_msg($_LANG['update_success'], 0, $links);

}

/*------------------------------------------------------ */
//-- 批量删除会员帐号
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'batch_remove')
{
    /* 检查权限 */
    admin_priv('users_drop');

    if (isset($_POST['checkboxes']))
    {
        $userIds = implode(',',$_POST['checkboxes']);
        $sql = "SELECT user_name FROM " . $ecs->table('users') . " WHERE user_id in (".$userIds.")";
        $col = $db->getCol($sql);
        $usernames = implode(',',addslashes_deep($col));
        $sql2 = "UPDATE ".$GLOBALS['ecs']->table('users')." SET `alias`='1' WHERE `user_id` in (".$userIds.")";
        if($GLOBALS['db']->query($sql2)) {
            /* 记录管理员操作 */
            admin_log(addslashes($usernames), 'remove', 'users');

            /* 提示信息 */
            $link[] = array('text' => $_LANG['go_back'], 'href'=>'users.php?act=list');
            sys_msg(sprintf($_LANG['batch_remove_successArr'], $usernames), 0, $link);
        }else {
            /* 提示信息 */
            $link[] = array('text' => $_LANG['go_back'], 'href'=>'users.php?act=list');
            sys_msg(sprintf($_LANG['remove_fail'], $usernames), 0, $link);
        }
    }
    else
    {
        $lnk[] = array('text' => $_LANG['go_back'], 'href'=>'users.php?act=list');
        sys_msg($_LANG['no_select_user'], 0, $lnk);
    }
}
/*------------------------------------------------------ */
//--  删除会员帐号
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'remove')
{
    /* 检查权限 */
    admin_priv('users_drop');
    $sql = "SELECT user_name FROM " . $ecs->table('users') . " WHERE user_id = '" . $_GET['id'] . "'";
    $username = $db->getOne($sql);
    $userId = intval($_GET['id']);//获取删除用户的idUPDATE users SET alias='1' WHERE user_id=1
    $sql = "UPDATE ".$GLOBALS['ecs']->table('users')." SET `alias`='1' WHERE `user_id`=".$userId."";
    if($GLOBALS['db']->query($sql)) {
        /* 记录管理员操作 */
        admin_log(addslashes($username), 'remove', 'users');

        /* 提示信息 */
        $link[] = array('text' => $_LANG['go_back'], 'href'=>'users.php?act=list');
        sys_msg(sprintf($_LANG['remove_success'], $username), 0, $link);
    }else {
        /* 提示信息 */
        $link[] = array('text' => $_LANG['go_back'], 'href'=>'users.php?act=list');
        sys_msg(sprintf($_LANG['remove_fail'], $username), 0, $link);
    }
}

/*------------------------------------------------------ */
//--  收货地址查看
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'address_list')
{
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $sql = "SELECT a.*, c.region_name AS country_name, p.region_name AS province, ct.region_name AS city_name, d.region_name AS district_name ".
           " FROM " .$ecs->table('user_address'). " as a ".
           " LEFT JOIN " . $ecs->table('region') . " AS c ON c.region_id = a.country " .
           " LEFT JOIN " . $ecs->table('region') . " AS p ON p.region_id = a.province " .
           " LEFT JOIN " . $ecs->table('region') . " AS ct ON ct.region_id = a.city " .
           " LEFT JOIN " . $ecs->table('region') . " AS d ON d.region_id = a.district " .
           " WHERE user_id='$id'";
    $address = $db->getAll($sql);
    $smarty->assign('address',          $address);
    assign_query_info();
    $smarty->assign('ur_here',          $_LANG['address_list']);
    $smarty->assign('action_link',      array('text' => $_LANG['03_users_list'], 'href'=>'users.php?act=list&' . list_link_postfix()));
    $smarty->display('user_address_list.htm');
}
/*------------------------------------------------------ */
//-- 脱离推荐关系
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'remove_parent')
{
    /* 检查权限 */
    admin_priv('users_manage');

    $sql = "UPDATE " . $ecs->table('users') . " SET parent_id = 0 WHERE user_id = '" . $_GET['id'] . "'";
    $db->query($sql);

    /* 记录管理员操作 */
    $sql = "SELECT user_name FROM " . $ecs->table('users') . " WHERE user_id = '" . $_GET['id'] . "'";
    $username = $db->getOne($sql);
    admin_log(addslashes($username), 'edit', 'users');

    /* 提示信息 */
    $link[] = array('text' => $_LANG['go_back'], 'href'=>'users.php?act=list');
    sys_msg(sprintf($_LANG['update_success'], $username), 0, $link);
}

/*------------------------------------------------------ */
//-- 查看用户推荐会员列表
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'aff_list')
{
    /* 检查权限 */
    admin_priv('users_manage');
    $smarty->assign('ur_here',      $_LANG['03_users_list']);

    $auid = $_GET['auid'];
    $user_list['user_list'] = array();

    $affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
    $smarty->assign('affiliate', $affiliate);

    empty($affiliate) && $affiliate = array();

    $num = count($affiliate['item']);
    $up_uid = "'$auid'";
    $all_count = 0;
    for ($i = 1; $i<=$num; $i++)
    {
        $count = 0;
        if ($up_uid)
        {
            $sql = "SELECT user_id FROM " . $ecs->table('users') . " WHERE parent_id IN($up_uid)";
            $query = $db->query($sql);
            $up_uid = '';
            while ($rt = $db->fetch_array($query))
            {
                $up_uid .= $up_uid ? ",'$rt[user_id]'" : "'$rt[user_id]'";
                $count++;
            }
        }
        $all_count += $count;

        if ($count)
        {
            $sql = "SELECT user_id, user_name, '$i' AS level, email, is_validated, user_money, frozen_money, rank_points, pay_points, reg_time ".
                    " FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id IN($up_uid)" .
                    " ORDER by level, user_id";
            $user_list['user_list'] = array_merge($user_list['user_list'], $db->getAll($sql));
        }
    }

    $temp_count = count($user_list['user_list']);
    for ($i=0; $i<$temp_count; $i++)
    {
        $user_list['user_list'][$i]['reg_time'] = local_date($_CFG['date_format'], $user_list['user_list'][$i]['reg_time']);
    }

    $user_list['record_count'] = $all_count;

    $smarty->assign('user_list',    $user_list['user_list']);
    $smarty->assign('record_count', $user_list['record_count']);
    $smarty->assign('full_page',    1);
    $smarty->assign('action_link',  array('text' => $_LANG['back_note'], 'href'=>"users.php?act=edit&id=$auid"));

    assign_query_info();
    $smarty->display('affiliate_list.htm');
}

/**
 *  返回用户列表数据
 *
 * @access  public
 * @param
 *
 * @return void
 */
function user_list()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 过滤条件 */
        $filter['keywords'] = empty($_REQUEST['keywords']) ? '' : trim($_REQUEST['keywords']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keywords'] = json_str_iconv($filter['keywords']);
        }
        $filter['parnetName'] = empty($_REQUEST['parnetName']) ? '' : trim($_REQUEST['parnetName']);
        $filter['company'] = empty($_REQUEST['company']) ? '' : trim($_REQUEST['company']);
        $filter['sort_by']    = empty($_REQUEST['sort_by'])    ? 'customNo' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'ASC'     : trim($_REQUEST['sort_order']);

        $ex_where .= ' WHERE alias=0 ';
        if ($filter['keywords'])
        {
            $ex_where .= " AND user_name LIKE '%" . mysql_like_quote($filter['keywords']) ."%'";
        }
        if ($filter['parnetName']) {
            $sql2 = "select customNo from ".$GLOBALS['ecs']->table('users')." where user_name='".$filter['parnetName']."'";
            $customNo = $GLOBALS['db']->getOne($sql2);
            //$len = strlen($customNo);
            //$customNosub = substr($customNo,0,$len-5);
            $customNoMax = $customNo+100000;
            $ex_where .= " AND customNo>=".$customNo." AND customNo< ".$customNoMax."";
        }
        if ($filter['company']) {
            $ex_where .= " AND companyName like '%".$filter['company']."%' ";
        }
        $filter['record_count'] = $GLOBALS['db']->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('users') . $ex_where);

        /* 分页大小 */
        $filter = page_and_size($filter);
        $sql = "SELECT * ".
                " FROM " . $GLOBALS['ecs']->table('users') . $ex_where .
                " ORDER by " . $filter['sort_by'] . ' ' . $filter['sort_order'] .
                " LIMIT " . $filter['start'] . ',' . $filter['page_size'];

        $filter['keywords'] = stripslashes($filter['keywords']);
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    //echo $sql;
    $user_list = $GLOBALS['db']->getAll($sql);
    $count = count($user_list);
    foreach ($user_list as $key =>&$val) {
        $level = explode(',',$user_list[$key]['msn']);
        $user_list[$key]['msn'] =$level;
        $len = strlen($user_list[$key]['customNo']);
        $Subordinate = substr($user_list[$key]['customNo'],$len-4,$len);
        if($Subordinate =='0000') {
            $aa = $user_list[$key]['user_name']."的";
            $user_list[$key]['SubordinateLevel'] = '直属上级';
        }else if($Subordinate != '0000'){
            $user_list[$key]['SubordinateLevel'] = $aa.'直属下级';
        }
        $user_list[$key]['reg_time'] = local_date($GLOBALS['_CFG']['date_format'], $user_list[$key]['reg_time']);
    }
    $arr = array('user_list' => $user_list, 'filter' => $filter,'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    return $arr;
}

/**
 * 总账号列表
 */
function parentList() 
{
	$sql = 'SELECT user_id,user_name FROM '.$GLOBALS['ecs']->table('users').' WHERE `user_name`!="" AND `parent_id` = 0';
	$data = $GLOBALS['db']->getAll($sql);
	return $data;
}
?>
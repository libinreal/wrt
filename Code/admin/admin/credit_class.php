<?php

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');

if($_REQUEST['act'] == 'edit') {
    admin_priv('credit_evaluation_state');
    $id = $_REQUEST['id'];
    $sql = "select * from  ".$GLOBALS['ecs']->table('credit_intrinfo')." where id=".$id." ";
    $credit = $GLOBALS['db']->getRow($sql);

    $smarty->assign('ur_here',      $_LANG['08_credit_class']);
    create_html_editor('content', $credit['content']);
    $smarty -> assign('credit',$credit);
    $smarty->assign('form_action','update');
    assign_query_info();
    $smarty->display('credit_class.html');
}
elseif ($_REQUEST['act'] == 'update') {
    admin_priv('credit_evaluation_state');
    $id = $_REQUEST['id'];
    $content = $_REQUEST['content'];

    $sql = "UPDATE ".$GLOBALS['ecs']->table('credit_intrinfo')." SET content='".$content."' WHERE id=".$id." ";
    if($GLOBALS['db']->query($sql)) {
        admin_log('信用等级介绍', 'edit', 'credit_intrinfo');   // 记录管理员操作
        clear_cache_files();    // 清除缓存
        $link[] = array('text' => $_LANG['back_list'], 'href' => 'credit_class.php?act=edit&id=2');
        sys_msg($_LANG['catedit_succed'], 0, $link);
    }
}
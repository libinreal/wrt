<?php
/**
 * 
 * @author 
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');

if ( $_REQUEST['act'] == 'list' )
{
    $smarty->display('cont_list.htm');
    exit;
}

//API
else {
    list($command, $entity, $params) = $_POST;
    $arr = array();
    if ( empty($arr[$command]) ) jsonBack('未知操作');
    if ( empty($entity) ) jsonBack('非法访问');
    
    $cont = Contract::get_instance();
    $cont->$command($entity, $params);
}

class Contract
{
    private static $_instance;
    private function __construct(){}
    private function __clone(){}
    
    public static function get_instance()
    {
        if (!self::$_instance) self::$_instance = new self();
        return self::$_instance;
    }
    
    public function test()
    {
        echo '01230';
    }
}
function jsonBack($message)
{
    make_json_response('', 0, $message);
}

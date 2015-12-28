<?php
/**
 * 授信管理页面
 * API :
 * class Credit
 * @author 
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
/**
 * 授信单列表
 */
if ($_REQUEST['act'] == 'list') 
{
    $smarty->display('second/credit_list.htm');
    exit;
}
/**
 * 授信单详情
 */
elseif ($_REQUEST['act'] == 'detail') 
{
    $smarty->display('second/credit_detail.htm');
    exit;
}
/**
 * API Access
 */
//Api 接口列表
$ApiList = array(
    'creditList', 
    'creditInfo', 
    'creditRemark'
);
$json = jsonAction($ApiList);
$credit = Credit::get_instance();
$credit->run($json);

/**
 * 授信管理API
 * @author luolu<luolu@3ti.us>
 * API接口访问地址：http://admin.zj.dev/admin/credit_manage.php
 * API接口方法的参数及返回值：
 * @param string $entity api接口参数,数据表名
 * @param array|null $parameters api接口需要的参数，例如：搜索条件等...
 * @return json
 * ----------------------
 */
class Credit 
{
    private $table;
    private $db;
    private $sql;
    private static $_instance;
    private function __construct() {}
    private function __clone() {}
    
    
    public static function get_instance() 
    {
        if (!self::$_instance) self::$_instance = new self();
        return self::$_instance;
    }
    
    public function run($json) 
    {
        $command = $json['command'];
        $entity = $json['entity'];
        $parameters = $json['parameters'];
        self::$command($entity, $parameters);
    }
    
    /**
     * 银行授信列表
     * {
     *      "command" : "creditList",
     *      "entity"  : "bank_credit",
     *      "parameters" : {
     *          "limit" : "(int)",
     *          "offset": "(int)"
     *      }
     * }
     */
    public function creditList($entity, $parameters) 
    {
        self::init($entity, 'bank_credit');
    
        $config = $this->creditConf();
    
        //page
        if (is_numeric($parameters['limit']) && is_numeric($parameters['offset'])) {
            $page = intval($parameters['limit']);
            $offset = intval($parameters['offset']);
            $limit = 'limit '.$page.','.$offset;
        }
    
        self::selectSql(array(
            'fields' => array(
                'credit_num',
                'customer_num',
                'customer_name',
                'amount_all',
                'amount_remain',
                'credit_status',
                'start_time',
                'end_time',
                'registration_name',
                'create_type'
            ),
            'extend' => 'ORDER BY add_time ASC '.$limit
        ));
        $res = $this->db->getAll($this->sql);
    
        foreach ($res as $k=>$v) {
            $res[$k]['credit_status'] = $config['creditStatus'][$v['credit_status']];
            $res[$k]['create_type']   = $config['creditType'][$v['create_type']];
        }
        
        self::selectSql(array(
            'fields' => 'COUNT(*) AS num',
        ));
        $total = $this->db->getOne($this->sql);
        make_json_result(array('total'=>$total, 'data'=>$res));
    }
    
    
    /**
     * 授信详细信息
     * {
     *      "command" : "creditInfo",
     *      "entity"  : "bank_credit",
     *      "parameters" : {
     *          "credit_id" : "(int)"
     *      }
     * }
     */
    public function creditInfo($entity, $parameters) 
    {
        self::init($entity, 'bank_credit');
    
        $creditId = intval($parameters['credit_id']);
        if (!$creditId) failed_json('没有传参`credit_id`');
    
        $config = $this->creditConf();
    
        self::selectSql(array(
            'fields' => array(
                'credit_id',
                'add_time',
                'customer_name',
                'amount_all',
                'credit_status',
                'registration_name',
                'end_time',
                'registration_num',
                'customer_bank_num',
                'remark'
            ),
            'where'  => 'credit_id='.$creditId,
        ));
        $res = $this->db->getRow($this->sql);
        $res['credit_status'] = $config['creditStatus'][$res['credit_status']];
        $res['add_time'] = date('Y/m/d', strtotime($res['add_time']));
        $res['end_time'] = date('Y/m/d', $res['end_time']);
        make_json_result($res);
    }
    
    
    /**
     * 银行授信信息备注
     * {
     *      "command" : "creditRemark",
     *      "entity"  : "bank_credit",
     *      "parameters" : {
     *          "credit_id" : "(int)"
     *          "params" : {
     *              "remark" : "(string)"
     *          }
     *      }
     * }
     */
    public function creditRemark($entity, $parameters) 
    {
        self::init($entity, 'bank_credit');
    
        $creditId = intval($parameters['credit_id']);
        if (!$creditId) failed_json('没有传参`credit_id`');
    
        $remark = htmlspecialchars(trim($parameters['params']['remark']));
        if (!$remark) make_json_result(true);
    
        $sql = 'UPDATE '.$this->table.' SET remark='.$remark.' WHERE credit_id='.$creditId;
        $res = $this->db->query($sql);
        make_json_result($res);
    }
    
    
    /**
     * 银行授信配置
     * @return array $config
     */
    private function creditConf() 
    {
        $config = require_once('bankCredit_config.php');
        return $config;
    }
    
    
    /**
     * @param array $params
     */
    private function selectSql($params) 
    {
        if ( is_array($params['fields']) ) {
            $params['fields'] = implode(',', $params['fields']);
        }
        if ( !empty(trim($params['as'])) ) {
            $params['as'] = ' AS '.$params['as'];
        }
        if ( !empty(trim($params['where'])) ) {
            $params['where'] = ' WHERE '.$params['where'];
        }
        $this->sql = 'SELECT '.$params['fields'].' FROM '.$this->table.' '.$params['as']
        .' '.$params['join'].' '.$params['where'].' '.$params['extend'];
        return ;
    }
    
    
    /**
     * @param string $entity
     * @param string $tableName
     */
    private function init($entity, $tableName) 
    {
        if ( $entity != $tableName ) {
            failed_json('数据表`'.$entity.'`不存在');
        }
        $this->table = $GLOBALS['ecs']->table($entity);
        $this->db = $GLOBALS['db'];
        return ;
    }
}
function failed_json($msg) {
    make_json_response('', -1, $msg);
}
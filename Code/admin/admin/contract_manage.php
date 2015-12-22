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
    $command = $_POST['command'];
    $entity = $_POST['entity'];
    $parameters = $_POST['parameters'];
    
    $params = json_decode(stripslashes($parameters), true);
    if ( !empty($parameters) && $params == null )
    {
        failed_json('parameters参数错误');
    }
    unset($parameters);
    
    $arr = array(
        'catlist'=>1, 
        'orglist'=>1, 
        'userlist' => 1, 
        'singleCon' => 1, 
        'contlist' => 1
    );
    
    if ( empty($arr[$command]) ) failed_json('未知操作');
    if ( empty($entity) ) failed_json('非法访问');
    
    $cont = Contract::get_instance();
    $cont->$command($entity, $params);
}

class Contract
{
    private $table;
    private $db;
    private $sql;
    private static $_instance;
    private function __construct(){}
    private function __clone(){}
    
    
    public static function get_instance()
    {
        if (!self::$_instance) self::$_instance = new self();
        return self::$_instance;
    }
    
    
    /**
     * 物料类型列表
     */
    public function catlist($entity, $params)
    {
        self::init($entity, 'goods_type');
        self::selectSql(array(
            'fields' => array( 'cat_id', 'cat_name' ), 
            'where'  => ' enabled=1 ', 
            'extend' => ' ORDER BY cat_id ASC'
        ));
        
        $res = $this->db->getAll($this->sql);
        make_json_result($res);
    }
    
    
    /**
     * 登记机构列表（银行列表）
     */
    public function orglist($entity, $params)
    {
        self::init($entity, 'bank');
        self::selectSql(array(
            'fields' => array(
                'bank_id',
                'bank_name'
            ), 
            
        ));
        $res = $this->db->getAll($this->sql);
        make_json_result($res);
    }
    
    
    /**
     * 客户列表
     */
    public function userlist($entity, $params)
    {
        self::init($entity, 'users');
        self::selectSql(array(
            'fields' => array( 'user_id', 'companyName' ), 
            'where'  => ' alias=0 ', 
            
        ));
        $res = $this->db->getAll($this->sql);
        make_json_result($res);
    }
    
    
    /**
     * 合同信息（单个）
     */
    public function singleCon($entity, $params)
    {
        self::init($entity, 'contract');
        
        $con_id = $params['contract_id'];
        if ( !$con_id ) failed_json('没有传参`contract_id`');
        
        //获取数据
        self::selectSql(array(
            'fields' => 'c.*,u.companyName', 
            'as'     => 'c', 
            'join'   => 'LEFT JOIN users AS u on c.customer_id=u.user_id', 
            'where'  => ' contract_id='.$con_id
        ));
        $res = $this->db->getRow($this->sql);
        
        $res['start_time'] = date('Y-m-d H:i:d', $res['start_time']);
        $res['end_time'] = date('Y-m-d H:i:d', $res['end_time']);
        
        make_json_result($res);
    }
    
    
    /**
     * 合同列表
     */
    public function contlist($entity, $params)
    {
        self::init($entity, 'contract');
        $params = $params['params'];
        $pageStart = ( $params['limit'] - 1 )*$params['offset'];
        self::selectSql(array(
            'fields' => array(
                'c.contract_id', 
                'c.contract_num', 
                'c.contract_name', 
                'c.contract_status', 
                'c.contract_type', 
                'c.customer_id', 
                'c.start_time', 
                'c.end_time', 
                'c.registration', 
                'c.bill_amount_history', 
                'c.bill_amount_valid', 
                'c.cash_amount_history', 
                'c.cash_amount_valid', 
                'u.companyName'
            ), 
            'as'     => 'c',
            'join'   => 'LEFT JOIN users AS u on c.customer_id=u.user_id', 
            'where'  => ' ', 
            'extend' => ' ORDER BY create_time DESC '
        ));
        $res = $this->db->selectLimit($this->sql, $params['offset'], $pageStart);
        $arr = array();
        while ( $rows = $this->db->fetchRow($res) )
        {
            if ($rows['contract_status'] == 0)
            {
                $rows['contract_status'] = '过期';
            }else if ($rows['contract_status'] == 1)
            {
                $rows['contract_status'] = '生效';
            }
            $rows['contract_type'] = ($rows['contract_type'] == 1)?'销售合同':'采购合同';
            $rows['start_time'] = date('Y-m-d H:i:s', $rows['start_time']);
            $rows['end_time'] = date('Y-m-d H:i:s', $rows['end_time']);
            $arr[] = $rows;
        }
        make_json_result($arr);
    }
    
    
    /**
     * @param array $params
     */
    private function selectSql($params)
    {
        if ( is_array($params['fields']) )
        {
            $params['fields'] = implode(',', $params['fields']);
        }
        if ( !empty(trim($params['as'])) ) 
        {
            $params['as'] = ' AS '.$params['as'];
        }
        if ( !empty(trim($params['where'])) ) 
        {
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
        if ( $entity != $tableName ) failed_json('数据表`'.$entity.'`不存在');
        $this->table = $GLOBALS['ecs']->table($entity);
        $this->db = $GLOBALS['db'];
        return ;
    }
    
}

function failed_json($msg)
{
    make_json_response('', -1, $msg);
}
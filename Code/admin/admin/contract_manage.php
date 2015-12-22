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
    $parameters = json_decode(stripslashes($_POST['parameters']), true);
    
    //API 接口列表
    $arr = array(
        'catList'=>1, 
        'orgList'=>1, 
        'userList' => 1, 
        'singleCont' => 1, 
        'contList' => 1, 
        'contIn' => 1
    );
    
    //验证参数
    if ( empty($arr[$command]) ) failed_json('未知操作');
    if ( empty($entity) ) failed_json('非法访问');
    
    //access
    $cont = Contract::get_instance();
    $cont->$command($entity, $parameters);
}


/***
 * 后台合同管理API类
 * @author luolu<luolu@3ti.us>
 * API接口访问地址：http://admin.zj.dev/admin/contract_manage.php
 * API接口方法的参数及返回值：
 * @param string $entity api接口参数,数据表名
 * @param array|null $parameters api接口需要的参数，例如：搜索条件等...
 * @return json
 * ----------------------
 */
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
     * {
     *      "command" : "catList", 
     *      "entity"  : "goods_type", 
     *      "parameters" : {}
     * }
     */
    public function catList($entity, $parameters)
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
     * {
     *      "command" : "orgList", 
     *      "entity"  : "bank", 
     *      "parameters" : {}
     * }
     */
    public function orgList($entity, $parameters)
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
     * {
     *      "command" : "userList", 
     *      "entity"  : "users", 
     *      "parameters" : {}
     * }
     */
    public function userList($entity, $parameters)
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
     * {
     *      "command" : "singleCont", 
     *      "entity"  : "contract", 
     *      "parameters" : {
     *          "contract_id" : "(int)"
     *      }
     * }
     */
    public function singleCont($entity, $parameters)
    {
        self::init($entity, 'contract');
        
        $contractId = $parameters['contract_id'];
        if ( !$contractId ) failed_json('没有传参`contract_id`');
        
        //获取数据
        self::selectSql(array(
            'fields' => 'c.*,u.companyName', 
            'as'     => 'c', 
            'join'   => 'LEFT JOIN users AS u on c.customer_id=u.user_id', 
            'where'  => ' contract_id='.$contractId
        ));
        $res = $this->db->getRow($this->sql);
        
        $res['start_time'] = date('Y-m-d H:i:d', $res['start_time']);
        $res['end_time'] = date('Y-m-d H:i:d', $res['end_time']);
        
        make_json_result($res);
    }
    
    
    /**
     * 合同列表
     * {
     *      "command" : "contList", 
     *      "entity"  : "contract", 
     *      "parameters" : {
     *          "params" : {
     *              "where" : {
     *                  "like" : {
     *                      "search_type" : "(int)", 
     *                      "search_value" : "(string)"
     *                  }, 
     *                  "contract_status" : "(int)", 
     *                  "start_time" : "2015-10-10(string)"
     *                  "end_time" : "2015-12-12(string)"
     *              }, 
     *              "limit" : "(int)", 
     *              "offset" : "(int)"
     *          }
     *      }
     * }
     */
    public function contList($entity, $parameters)
    {
        self::init($entity, 'contract');
        
        $params = $parameters['params'];
        //where
        $where = '';
        $params_where = $params['where'];
        
        //搜索类型
        $search_type = $params_where['like']['search_type'];
        $search_value = $params_where['like']['search_value'];
        if ( $search_type && $search_value ) {
            if ($search_type == 'companyName') {
                $where .= 'u.';
            } else {
                $where .= 'c.';
            }
            $where .= $search_type.' LIKE "%'.$search_value.'%"';
        }
        //合同状态
        if ( $params_where['contract_status'] >= 0 ) {
            if (!empty(trim($where))) $where .= ' and ';
            $where .= 'c.contract_status='.$params_where['contract_status'];
        }
        //日期
        if ( $params_where['start_time'] ) {
            if (!empty(trim($where))) $where .= ' and ';
            $where .= 'FROM_UNIXTIME(c.start_time, "%Y-%m-%d")>="'.$params_where['start_time'].'"';
        }
        if ( $params_where['end_time'] ) {
            if (!empty(trim($where))) $where .= ' and ';
            $where .= 'FROM_UNIXTIME(c.end_time, "%Y-%m-%d")<="'.$params_where['end_time'].'"';
        }
        
        //page
        if ( !$params['offset'] ) {
            $limit = ' limit '.$params['limit'];
        } elseif ( $params['limit'] ) {
            $page = ($params['limit'] - 1) * $params['offset'];
            $limit = ' limit '.$page.','.$params['offset'];
        }
        
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
                'u.companyName'
            ), 
            'as'     => 'c',
            'join'   => 'LEFT JOIN users AS u on c.customer_id=u.user_id', 
            'where'  => $where, 
            'extend' => ' ORDER BY create_time DESC '.$limit
        ));
        $res = $this->db->getAll($this->sql);
        foreach ($res as $k=>$v) {
            if ($v['contract_type'] == 1) {
                $res[$k]['contract_type'] = '销售合同';
            } elseif ($v['contract_type'] == 2) {
                $res[$k]['contract_type'] = '采购合同';
            }
            if ($v['end_time'] < time()) {
                $res[$k]['contract_status'] = '过期';
            } else {
                if ($v['contract_status'] == 0) {
                    $res[$k]['contract_status'] = '作废';
                } elseif ($v['contract_status'] == 1) {
                    $res[$k]['contract_status'] = '生效';
                }
            }
            
            $res[$k]['start_time'] = date('Y-m-d H:i:s', $v['start_time']);
            $res[$k]['end_time'] = date('Y-m-d H:i:s', $v['end_time']);
        }
        make_json_result($res);
    }
    
    
    /**
     * 合同录入
     * {
     *      "command" : "contIn", 
     *      "entity"  : "contract", 
     *      "parameters" : {
     *          "user_id" : "(int)", 
     *          "params" : {}
     *      }
     * }
     */
    public function contIn($entity, $parameters) {
        self::init($entity, 'contract');
        
        
        make_json_result($res);
    }
    
    
    /**
     * 验证提交的合同数据
     * @param int $type 1添加操作 2修改操作
     * @param array $params
     */
    private function validationCont($type, $parameters) 
    {
        $params = $parameters['params'];
        
        //修改时
        if ($type == 2 && $parameters['contract_id']) {
            $where = ' and contract_id<>'.$parameters['contract_id'];
        } elseif ($type == 2) {
            failed_json('没有传参`contract_id`');
        }
        
        $params = self::filterContValue($params);
        
        //合同编号，合同名称不能重复
        self::selectSql(array(
            'fields' => 'contract_id',
            'where'  => 'contract_num="'.$params['contract_num'].'"'.$where
        ));
        $res = $this->db->getRow($this->sql);
        if ($res['contract_id'] != null ) {
            failed_json('该合同编号已经存在！');
        }
        self::selectSql(array(
            'fields' => 'contract_id',
            'where'  => 'contract_name="'.$params['contract_name'].'"'.$where
        ));
        $res = $this->db->getRow($this->sql);
        if ($res['contract_id'] != null ) {
            failed_json('该合同名称已经存在！');
        }
        
        $fields = 'contract_num,contract_name,contract_amount,contract_status,';
        $sql = 'INSERT INTO '.$this->table.'()';
        
    }
    
    
    /**
     * 验证合同信息值的合法性
     * @param array $params
     */
    private function filterContValue($params) 
    {
        foreach ($params as $k=>$v) {
            $params[$k] = htmlspecialchars(trim($v));
        }
        return $params;
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

function failed_json($msg)
{
    make_json_response('', -1, $msg);
}
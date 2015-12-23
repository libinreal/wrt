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
elseif ( $_REQUEST['act'] == 'insert' )
{
    $smarty->display('');
}

//API
else {
    $command = $_POST['command'];
    $entity = $_POST['entity'];
    $parameters = $_POST['parameters'];
    
    //插件测试接口时
    if (!is_array($parameters) && $parameters != '') {
        $parameters = json_decode(stripslashes($_POST['parameters']), true);
    }
    
    
    //API 接口列表
    $arr = array(
        'catList'=>1, 
        'orgList'=>1, 
        'userList' => 1, 
        'singleCont' => 1, 
        'contList' => 1, 
        'contIn' => 1, 
        'contUp' => 1, 
        'suppliers' => 1, 
        'regionList' => 1, 
        'buyCont' => 1, 
        'contSupsList' => 1, 
        'contInSups' => 1, 
        'uploadify' => 2, 
    );
    
    //验证参数
    switch ( $arr[$command] ) {
        case 1:
            if ( empty($entity) ) failed_json('非法访问');
            break;
        case 2:
            if ( empty($entity) ) failed_json('未定义上传表单名称');
            break;
        default:
            failed_json('未知操作');
            break;
    }
    
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
     *                      "search_type" : "(string)", 
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
        if ( $params_where['contract_status'] != '' ) {
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
        if ($params['limit'] != '' && $params['offset'] == '') {
            $limit = ' limit '.$params['limit'];
        } elseif ($params['limit'] == '' && $params['offset'] != '') {
            $limit = ' limit '.$params['offset'];
        } elseif ($params['limit'] != '' && $params['offset'] != '') {
            $page = ($params['limit'] - 1) * $params['offset'];
            $limit = 'limit '.$page.','.$params['offset'];
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
                'u.companyName', 
                's.suppliers_name'
            ), 
            'as'     => 'c',
            'join'   => 'LEFT JOIN users AS u on c.customer_id=u.user_id LEFT JOIN suppliers as s on c.customer_id=s.suppliers_id', 
            'where'  => $where, 
            'extend' => ' ORDER BY start_time ASC '.$limit
        ));
        $res = $this->db->getAll($this->sql);
        foreach ($res as $k=>$v) {
            if ($v['contract_type'] == 1) {
                $res[$k]['contract_type'] = '销售合同';
            } elseif ($v['contract_type'] == 2) {
                $res[$k]['contract_type'] = '采购合同';
                $res[$k]['companyName'] = $v['suppliers_name'];
            }
            unset($res[$k]['suppliers_name']);
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
        
        self::selectSql(array(
            'fields' => 'COUNT(*) AS num', 
            'where' => $where
        ));
        $total = $this->db->getone($this->sql);
        make_json_result(array('total'=>$total, 'data'=>$res));
    }
    
    
    /**
     * 合同录入
     * {
     *      "command" : "contIn", 
     *      "entity"  : "contract", 
     *      "parameters" : {
     *          "user_id" : "(int)", //创建者id 
     *          "contract_id" : "(int)", //修改合同信息时才会有此值 
     *          "params" : {
     *              "contract_num" : "(string)", 
     *              "contract_name" : "(string)", 
     *              "contract_amount" : "(float)", 
     *              "contract_status" : "(int)", 
     *              "contract_type" : "(int)", 
     *              "contract_sign_type" : "(int)", 
     *              "customer_id" : "(int)", 
     *              "start_time" : "2015-10-26(string)", 
     *              "end_time" : "2015-10-26(string)", 
     *              "is_control" : "(int)", 
     *              "rate" : "(string)", 
     *              "bank_id" : "(int)", 
     *              "attachment" : "(string)", 
     *              "remark" : "(string)", 
     *              "goods_type" : "1,2,23(string)", 
     *          }
     *      }
     * }
     */
    public function contIn($entity, $parameters) 
    {
        self::init($entity, 'contract');
        
        $arr = self::validateCont(1, $parameters);
        
        //合同 insert sql
        $params = $arr['params'];
        $fields = implode(',', array_keys($params));
        $values = '("'.implode('","', $params).'")';
        
        if (strpos($values, ',,') !== false ) {
            failed_json('可能是传递的参数不全或者传递null或者空字符串');
        }
        
        //添加合同
        $sql = 'INSERT INTO '.$this->table.'('.$fields.')values'.$values;
        $res = $this->db->query($sql);
        $insert_id = $this->db->insert_id();
        if ($res && $insert_id) {
            
            //添加合同物料类型
            $goods_type = explode(',', $arr['goods_type']);
            foreach ($goods_type as $k=>$v) {
                $goods_type_values .= '('.$insert_id.','.$v.'),';
            }
            $goods_type_values = substr($goods_type_values, 0, -1);
            $sql = 'INSERT INTO contract_category (contract_id,category_id)values'.$goods_type_values;
            $res = $this->db->query($sql);
            
            if ( $res ) {
                make_json_result($res);
            } else {
                failed_json('添加合同失败');
            }
            
        } else {
            failed_json('添加合同失败');
        }
    }
    
    
    /**
     * 修改合同信息
     * 参数信息同 contIn
     */
    public function contUp($entity, $parameters) 
    {
        self::init($entity, 'contract');
        
        $arr = self::validateCont(2, $parameters);
        
        $insert_id = $parameters['contract_id'];
        //合同 update sql
        $params = $arr['params'];
        foreach ($params as $k=>$v) {
            $update_set .= $k.'="'.$v.'",';
        }
        $update_set = substr($update_set, 0, -1);
        
        //修改合同
        $sql = 'UPDATE '.$this->table.' SET '.$update_set.' WHERE contract_id='.$insert_id;
        $res = $this->db->query($sql);
        if ($res && $insert_id) {
        
            //已经存在的物料类型
            $this->table = 'contract_category';
            self::selectSql(array(
                'fields' => 'category_id', 
                'where'  => 'contract_id="'.$insert_id.'"'
            ));
            $res = $this->db->getAll($this->sql);
            $have_goods_type = array();
            foreach ($res as $k=>$v) {
                $have_goods_type[] = $v['category_id'];
            }
            
            //现有物料类型
            $goods_type = explode(',', $arr['goods_type']);
            
            //需要删除的物料
            $remove_goods_type = implode(',', array_diff($have_goods_type, $goods_type));
            $sql = 'DELETE FROM '.$this->table.' WHERE category_id in('.$remove_goods_type.') and contract_id='.$insert_id;
            $res = $this->db->query($sql);
            if ( $res ) {
                //需要添加的物料类型
                $insert_goods_type = array_diff($goods_type, $have_goods_type);
                foreach ($insert_goods_type as $k=>$v) {
                    $goods_type_values .= '('.$insert_id.','.$v.'),';
                }
                $goods_type_values = substr($goods_type_values, 0, -1);
                $sql = 'INSERT INTO contract_category (contract_id,category_id)values'.$goods_type_values;
                $res = $this->db->query($sql);
                
                if ( $res ) {
                    make_json_result($res);
                } else {
                    failed_json('添加合同失败');
                }
                
            } else {
                failed_json('修改物料类型失败！');
            }
            
        } else {
            failed_json('修改合同失败');
        }
    }
    
    
    /**
     * 供应商列表
     * {
     *      "command" : "suppliers", 
     *      "entity"  : "admin_user", 
     *      "parameters" : {
     *          "region_id" : "(int)"
     *      }
     * }
     */
    public function suppliers($entity, $parameters) 
    {
        self::init($entity, 'admin_user');
        
        //根据地区搜索供应商，只精确到省份
        $region_id = $parameters['region_id'];
        if ($region_id > 0) {
            $where = ' and s.region_id='.$region_id;
        }
        
        self::selectSql(array(
            'as'     => 'a', 
            'fields' => array(
                's.suppliers_id', 
                's.suppliers_name'
            ), 
            'join'   => 'LEFT JOIN suppliers AS s on a.suppliers_id=s.suppliers_id', 
            'where'  => 'a.role_id=2 and s.is_check=1'.$where
        ));
        $res = $this->db->getAll($this->sql);
        make_json_result($res);
    }
    
    
    /**
     * 地区（只精确到省份）
     * {
     *      "command" : "regionList", 
     *      "entity"  : "region", 
     *      "parameters" : {}
     * }
     */
    public function regionList($entity, $parameters) 
    {
        self::init($entity, 'region');
        self::selectSql(array(
            'fields' => array(
                'region_id', 
                'region_name'
            ), 
            'where' => 'region_type in(0,1)'
        ));
        $res = $this->db->getAll($this->sql);
        make_json_result($res);
    }
    
    
    /**
     * 下游客户的采购合同列表
     * {
     *      "command" : "buyCont", 
     *      "entity"  : "contract", 
     *      "parameters" : {
     *          "customer_id" : "(int)"
     *      }
     * }
     */
    public function buyCont($entity, $parameters) 
    {
        self::init($entity, 'contract');
        
        $cutomerId = intval($parameters['customer_id']);
        if ( !$cutomerId ) failed_json('没有传参`customer_id`');
        
        self::selectSql(array(
            'fields' => array(
                'contract_id', 
                'contract_name'
            ), 
            'where' => 'contract_type=2 and customer_id='.$cutomerId, 
        ));
        $res = $this->db->getAll($this->sql);
        make_json_result($res);
    }
    
    
    /**
     * 合同关联供应商设置
     * {
     *      "command" : "contInSups", 
     *      "entity"  : "contract_suppliers", 
     *      "parameters" : {
     *          "contract_id" : "(int)", 
     *          "params" : {
     *              "suppliers_id" : "1,2,3(array)"
     *          }
     *      }
     * }
     */
    public function contInSups($entity, $parameters) 
    {
        self::init($entity, 'contract_suppliers');
        
        $contractId = intval($parameters['contract_id']);
        $suppliersId = $parameters['params']['suppliers_id'];
        
        if ($contractId <= 0) failed_json('没有传参`contract_id`或传参错误');
        if ($suppliersId <= 0) failed_json('没有传参`suppliers_id`或传参错误');
        
        //查看原有的合同绑定哪些供应商
        self::selectSql(array(
            'fields' => 'suppliers_id', 
            'where'  => 'contract_id='.$contractId
        ));
        $res = $this->db->getAll($this->sql);
        $haveSupId = array();
        foreach ($res as $v) {
            if ( in_array($v['suppliers_id'], $suppliersId) ) {
                $haveSupId[] = $v['suppliers_id'];
            }
        }
        
        //过滤掉已经存在与数据表的供应商
        $suppliersId = array_diff($suppliersId, $haveSupId);
        if (empty($suppliersId)) make_json_result(true);
            
        $arr = array();
        foreach ($suppliersId as $k=>$v) {
            $arr[] = '('.$contractId.','.$v.')';
        }
        $values = implode(',', $arr);
        $sql = 'INSERT INTO '.$this->table.' values'.$values;
        $res = $this->db->query($sql);
        if ($res) {
            make_json_result($res);
        } else {
            failed_json('合同关联供应商失败！');
        }
    }
    
    
    /**
     * 合同关联供应商列表
     * {
     *      "command" : "ContSupsList", 
     *      "entity"  : "contract_suppliers", 
     *      "parameters" : {
     *          "params" : {
     *              "where" : {
     *                  "customer_id" : "(int)", 
     *                  "contract_id" : "(int)", 
     *                  "region_id"   : "(int)"
     *              }, 
     *              "limit" : "(int)", 
     *              "offset": "(int)"
     *          }
     *      }
     * }
     */
    public function contSupsList($entity, $parameters) 
    {
        self::init($entity, 'contract_suppliers');
        
        $params = $parameters['params'];
        $where = '';
        
        $customerId = intval($params['where']['customer_id']);
        $contractId = intval($params['where']['contract_id']);
        $regionId   = intval($params['where']['region_id']);
        
        //where
        if ($customerId > 0) {
            $where .= 'c.customer_id='.$customerId;
        }
        if ($contractId > 0) {
            if (!empty(trim($where))) $where .= ' and ';
            $where .= 'c.contract_id='.$contractId;
        }
        if ($regionId > 0) {
            if (!empty(trim($where))) $where .= ' and ';
            $where .= 's.region_id='.$regionId;
        }
        
        //page
        if ($params['limit'] != '' && $params['offset'] == '') {
            $limit = ' limit '.$params['limit'];
        } elseif ($params['limit'] == '' && $params['offset'] != '') {
            $limit = ' limit '.$params['offset'];
        } elseif ($params['limit'] != '' && $params['offset'] != '') {
            $page = ($params['limit'] - 1) * $params['offset'];
            $limit = 'limit '.$page.','.$params['offset'];
        }
        
        self::selectSql(array(
            'fields' => array(
                'u.companyName', 
                'c.contract_num', 
                'c.contract_name', 
                's.suppliers_name', 
                'r.region_name'
            ), 
            'as'   => 'cs', 
            'join' => 'LEFT JOIN contract AS c on cs.contract_id=c.contract_id'
                    .' LEFT JOIN users AS u on c.customer_id=u.user_id'
                    .' LEFT JOIN suppliers AS s on cs.suppliers_id=s.suppliers_id'
                    .' LEFT JOIN region AS r on s.region_id=r.region_id', 
            'where' => $where, 
            'extend'=> $limit
        ));
        $res = $this->db->getAll($this->sql);
        
        self::selectSql(array(
            'fields' => 'COUNT(cs.contract_id) AS num', 
            'as'   => 'cs',
            'join' => 'LEFT JOIN contract AS c on cs.contract_id=c.contract_id '
                    .' LEFT JOIN users AS u on c.customer_id=u.user_id'
                    .' LEFT JOIN suppliers AS s on cs.suppliers_id=s.suppliers_id'
                    .' LEFT JOIN region AS r on s.region_id=r.region_id',
            'where' => $where            
        ));
        $total = $this->db->getOne($this->sql);
        make_json_result(array('total'=>$total, 'data'=>$res));
    }
    
    
    /**
     * 上传合同附件（只允许pdf）
     * {
     *      "command" : "uploadify", 
     *      "entity"  : "(input name)", 
     *      "parameters" : {} 
     * }
     */
    public function uploadify($entity, $parameters) 
    {
        require('../includes/cls_image.php');
        if (empty($_FILES)) failed_json('上传的图片是空的资源');
        $file = pathinfo($_FILES[$entity]['name']);
        if ($file['extension'] != 'pdf') {
            failed_json('只允许上传pdf格式的文件！');
        }
        //upload
        $upload = new cls_image();
        $fileName = date('YmdHis').'.pdf';
        $res = $upload->upload_image($_FILES[$entity], 'contract', $fileName);
        if ($res === false) {
            failed_json('文件上传失败，可能因为服务器不允许上传太大的pdf文件！');
        } else {
            make_json_result($fileName);
        }
    }
    
    
    /**
     * 验证提交的合同数据（合同添加，编辑时）
     * @param int $type 1添加操作 2修改操作
     * @param array $params
     */
    private function validateCont($type, $parameters) 
    {
        $user_id = $parameters['user_id'];
        if (empty($user_id)) {
            $user_id = $_SESSION['admin_id'];
        }
        $params = $parameters['params'];
        
        //修改时
        if ($type == 2 && $parameters['contract_id']) {
            $where = ' and contract_id<>'.$parameters['contract_id'];
        } elseif ($type == 2) {
            failed_json('没有传参`contract_id`');
        }
        
        $params = self::validContValue($params);
        
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
        
        //登记机构名称
        $params['registration'] = $this->db->getOne('SELECT bank_name FROM bank WHERE bank_id='.$params['bank_id']);
        
        if ($type == 1) {
            //创建人
            $params['create_by'] = $this->db->getOne("SELECT user_name FROM admin_user WHERE user_id=".$user_id);
        }
        
        $arr = array(
            'contract_num'       => $params['contract_num'], 
            'contract_name'      => $params['contract_name'], 
            'contract_amount'    => $params['contract_amount'], 
            'contract_status'    => $params['contract_status'], 
            'contract_type'      => $params['contract_type'], 
            'contract_sign_type' => $params['contract_sign_type'], 
            'customer_id'        => $params['customer_id'], 
            'start_time'         => strtotime($params['start_time']), 
            'end_time'           => strtotime($params['end_time']), 
            'is_control'         => $params['is_control'], 
            'rate'               => $params['rate'], 
            'registration'       => $params['registration'], 
            'bank_id'            => $params['bank_id'], 
            'attachment'         => $params['attachment'], 
            'remark'             => $params['remark']
        );
        if ($type == 1) {
            $arr['create_user'] = $user_id;
            $arr['create_by'] = $params['create_by'];
            $arr['create_time'] = time();
        }
        return array( 'params'=>$arr, 'goods_type'=>$params['goods_type'] );
    }
    
    
    /**
     * 合同信息值的合法化（合同添加，编辑时）
     * @param array $params
     * @return array $params
     */
    private function validContValue($params) 
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

function failed_json($msg) {
    make_json_response('', -1, $msg);
}

/* 
 * 合同测试信息
 * echo json_encode(array('contract_id'=>1, 'params'=> array(
 'contract_num'       => '156894321',
 'contract_name'      => 'hetong0001',
 'contract_amount'    => '1000000',
 'contract_status'    => '1',
 'contract_type'      => '1',
 'contract_sign_type' => '1',
 'customer_id'        => '2',
 'start_time'         => strtotime('2015-12-22'),
 'end_time'           => strtotime('2015-12-30'),
 'is_control'         => '1',
 'rate'               => '0.2%',
 'bank_id'            => '1',
 'attachment'         => 'pdf',
 'remark'             => 'benfen',
 'goods_type'         => '1,2,8,9'
))); */
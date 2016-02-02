<?php
/**
 * 合同管理页面
 * API :
 * class Contract
 * @author 
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require_once('ManageModel.php');
/**
 * 合同列表
 */
if ( $_REQUEST['act'] == 'contractList' )
{
    $smarty->display('second/contract_list.html');
    exit;
}
/**
 * 合同添加
 */
elseif ( $_REQUEST['act'] == 'contractInsert' )
{
    $smarty->display('second/contract_insert.html');
    exit;
}
/**
 * 合同编辑
 */
elseif ( $_REQUEST['act'] == 'contractEdit' )
{
    $smarty->display('second/contract_edit.html');
    exit;
}
elseif ( $_REQUEST['act'] == 'contractView' )
{
    $smarty->display('second/contract_view.html');
    exit;
}
/**
 * 合同关联供应商设置
 */
elseif ( $_REQUEST['act'] == 'supplierSet' ) 
{
    $smarty->display('second/contract_supplier_link_set.html');
    exit;
}
/**
 * 合同关联供应商列表
 */
elseif ( $_REQUEST['act'] == 'supplierList' ) 
{
    $smarty->display('second/contract_supplier_link_list.html');
    exit;
}
/**
 * API Access
 */
//Api 接口列表
$ApiList = array(
    'catList',
    'orgList',
    'userList',
    'singleCont',
    'contList',
    'contIn',
    'contUp',
    'suppliers',
    'regionList',
    'buyCont',
    'contSupsList',
    'contInSups',
    'contToSup',
    'uploadify'
);

/***
 * 合同管理API
 * @author luolu<luolu@3ti.us>
 * API接口访问地址：http://admin.zj.dev/admin/contract_manage.php
 * API接口方法的参数及返回值：
 * @param string $entity api接口参数,数据表名
 * @param array|null $parameters api接口需要的参数，例如：搜索条件等...
 * @return json
 * ----------------------
 */
class Contract extends ManageModel 
{
    protected static $_instance;
    
    protected $table;
    protected $db;
    protected $sql;
    
    
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
            )
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
            'fields' => array( 'user_id', 'companyName', 'user_name' ), 
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
        
        $this->table = 'contract_category';
        self::selectSql(array(
            'fields' => array(
                'c.contract_id', 
                'g.cat_id', 
                'g.cat_name'
            ), 
            'as'     => 'cc', 
            'join'   => 'LEFT JOIN contract AS c on cc.contract_id=c.contract_id'
                        .' LEFT JOIN goods_type AS g on cc.category_id=g.cat_id', 
            'where'  => 'cc.contract_id='.$contractId
        ));
        $cat = $this->db->getAll($this->sql);
        
        make_json_result(array('cat'=>$cat, 'data'=>$res));
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
        $params_where = $params['where'];
        
        //where
        $where = '';
        
        //搜索类型
        $search_type = $params_where['like']['search_type'];
        $search_value = $params_where['like']['search_value'];
        if ( $search_type && $search_value ) {
            $where .= ($search_type == 'companyName') ? 'u.' : 'c.';
            $where .= $search_type.' LIKE "%'.$search_value.'%"';
        }
        
        //合同状态
        $contStauts = $params_where['contract_status'];
        if (is_numeric($contStauts)) {
            if ($contStauts == 0 || $contStauts == 1) {
                if (!empty($where)) $where .= ' and ';
                $where .= 'c.contract_status='.$contStauts.' and FROM_UNIXTIME(c.end_time, "%Y-%m-%d")>"'.date('Y-m-d').'"';
            } elseif ($contStauts == 2) {
                if (!empty($where)) $where .= ' and ';
                $where .= 'FROM_UNIXTIME(c.end_time, "%Y-%m-%d")<="'.date('Y-m-d').'"';
            }
        }
        
        //日期
        $sTime = $params_where['start_time'];
        if ( $sTime ) {
            if (!empty($where)) $where .= ' and ';
            $where .= 'FROM_UNIXTIME(c.start_time, "%Y-%m-%d")>="'.$sTime.'"';
        }
        $eTime = $params_where['end_time'];
        if ( $eTime ) {
            if (!empty($where)) $where .= ' and ';
            $where .= 'FROM_UNIXTIME(c.end_time, "%Y-%m-%d")<="'.$eTime.'"';
        }
        
        //page
        if (is_numeric($params['limit']) && is_numeric($params['offset'])) {
            $page = intval($params['limit']);
            if ($page < 0) $page = 0;
            $offset = intval($params['offset']);
            if ($offset < 0) $offset = 0;
            $limit = 'limit '.$page.','.$offset;
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
                'c.bill_amount_history', 
                'c.bill_amount_valid', 
                'c.cash_amount_history', 
                'c.cash_amount_valid', 
                'u.companyName', 
                's.suppliers_name'
            ), 
            'as'     => 'c',
            'join'   => 'LEFT JOIN users AS u on c.customer_id=u.user_id LEFT JOIN suppliers as s on c.customer_id=s.suppliers_id', 
            'where'  => $where, 
            'extend' => 'ORDER BY contract_id DESC '.$limit
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
            if ($v['end_time'] <= time()) {
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
            'as'     => 'c', 
            'join'   => 'LEFT JOIN users AS u on c.customer_id=u.user_id', 
            'where'  => $where
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
     *              "goods_type" : "1,2,23(array)", 
     *              "user_id"   : "(int)", 
     *              "user_name" : "(string)"
     *          }
     *      }
     * }
     */
    public function contIn($entity, $parameters) 
    {
        self::init($entity, 'contract');
        
        $data = self::validateCont(1, $parameters);
        $goodsTypeList = $data['goods_type'];
        $data = $data['params'];
        
        //constract data
        $fields = implode(',', array_keys($data));
        $values = '("'.implode('","', $data).'")';
        if (strpos($values, ',,') !== false) failed_json('传参错误');
        
        //insert
        $sql = 'INSERT INTO '.$this->table.'('.$fields.')values'.$values;
        $res = $this->db->query($sql);
        $insertId = $this->db->insert_id();
        if ($res && $insertId) {
            
            if (empty($goodsTypeList)) {
                make_json_result(true);
            }
            //添加合同物料类型
            if (!is_array($goodsTypeList)) failed_json('没有传参`goods_type`');
            foreach ($goodsTypeList as $k=>$v) {
                $goodsTypeValues .= '('.$insertId.','.$v.'),';
            }
            $goodsTypeValues = substr($goodsTypeValues, 0, -1);
            $sql = 'INSERT INTO contract_category (contract_id,category_id)values'.$goodsTypeValues;
            $res = $this->db->query($sql);
            
            if ( $res ) {
                make_json_result($res);
            } else {
                failed_json('添加合同失败');
            }
            
        } else {
            failed_json('添加合同失败！');
        }
    }
    
    
    /**
     * 修改合同信息
     * 参数信息同 contIn
     */
    public function contUp($entity, $parameters) 
    {
        self::init($entity, 'contract');
        
        $data = self::validateCont(2, $parameters);
        $contractId = $data['contract_id'];
        $goodsTypeList = $data['goods_type'];
        $data = $data['params'];
        
        //update contract data
        foreach ($data as $k=>$v) {
            $updateSet .= $k.'="'.$v.'",';
        }
        $updateSet = substr($updateSet, 0, -1);
        
        //update
        $sql = 'UPDATE '.$this->table.' SET '.$updateSet.' WHERE contract_id='.$contractId;
        $res = $this->db->query($sql);
        if (!$res) failed_json('修改合同失败！');
        
        //goods_type
        if (empty($goodsTypeList)) {
            make_json_result(true);
        }
        if (!is_array($goodsTypeList)) {
            failed_json('没有传参`goods_type`或者传参错误');
        }
        
        //exist goods_type
        $this->table = 'contract_category';
        self::selectSql(array(
            'fields' => 'category_id',
            'where'  => 'contract_id="'.$contractId.'"'
        ));
        $res = $this->db->getAll($this->sql);
        $existGoodsType = array();
        foreach ($res as $k=>$v) {
            $existGoodsType[] = $v['category_id'];
        }
        
        //delete goods_type
        $removeGoodsType = array_diff($existGoodsType, $goodsTypeList);
        if (!empty($removeGoodsType)) {
            $sql = 'DELETE FROM '.$this->table.' WHERE category_id in('.implode(',', $removeGoodsType).') and contract_id='.$contractId;
            $this->db->query($sql);
        }
        
        //insert goods_type
        $insertGoodsType = array_diff($goodsTypeList, $existGoodsType);
        if (!empty($insertGoodsType)) {
            foreach ($insertGoodsType as $k=>$v) {
                $insertGoodsTypeValues .= '('.$contractId.','.$v.'),';
            }
            $insertGoodsTypeValues = substr($insertGoodsTypeValues, 0, -1);
            $sql = 'INSERT INTO contract_category (contract_id,category_id)values'.$insertGoodsTypeValues;
            $this->db->query($sql);
        }
        make_json_result(true);
    }
    
    
    /**
     * 供应商列表
     * {
     *      "command" : "suppliers", 
     *      "entity"  : "admin_user", 
     *      "parameters" : {
     *          "region_id" : "(int)", 
     *          "contract_id" : "(int)", 
     *          "flag" : "1"    //当获取合同下的供应商时需要传此职，不传则是对所有的供应商进行筛选
     *      }
     * }
     */
    public function suppliers($entity, $parameters) 
    {
        self::init($entity, 'admin_user');
        
        $where = '';
        
        $contractId = $parameters['contract_id'];
        if ( $contractId > 0) {
            $this->table = 'contract_suppliers';
            self::selectSql(array(
                'fields' => 'suppliers_id', 
                'where'  => 'contract_id='.$contractId
            ));
            $res = $this->db->getAll($this->sql);
            $exist = array();
            foreach ($res as $v){
                $exist[] = $v['suppliers_id'];
            }
            
            if ($parameters['flag'] == 1) {
                if (empty($exist)) make_json_result(array());
                $this->table = 'suppliers';
                self::selectSql(array(
                    'fields' => array(
                        'suppliers_id', 
                        'suppliers_name'
                    ), 
                    'where'  => 'suppliers_id in('.implode(',', $exist).')'
                ));
                $res = $this->db->getAll($this->sql);
                make_json_result($res);
            }

            if (!empty($exist)) {
                $where .= ' and s.suppliers_id not in('.implode(',', $exist).')';
            }
        }
        
        if (!$contractId && $parameters['flag'] == 1) make_json_result(array());
        
        //根据地区搜索供应商，只精确到省份
        $region_id = $parameters['region_id'];
        if ($region_id > 0) {
            $where .= ' and s.region_id='.$region_id;
        }
        
        $this->table = 'admin_user';
        self::selectSql(array(
            'as'     => 'a', 
            'fields' => array(
                's.suppliers_id', 
                's.suppliers_name'
            ), 
            'join'   => 'LEFT JOIN suppliers AS s on a.suppliers_id=s.suppliers_id', 
            'where'  => 'a.role_id=2 and a.suppliers_id<>0 and s.is_check=1'.$where, 
            'extend' => ' ORDER BY s.suppliers_id ASC'
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
            'where' => 'region_type=1'
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
        
        $cutomerId = $parameters['customer_id'];
        if ( !$cutomerId ) failed_json('没有传参`customer_id`');
        if ($cutomerId < 0) make_json_result(array());
        
        if ($cutomerId > 0) {
            $where = ' and customer_id='.$cutomerId;
        }
        self::selectSql(array(
            'fields' => array(
                'contract_id', 
                'contract_name'
            ), 
            'where' => 'contract_type=2 '.$where, 
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
        if (!is_array($suppliersId)) failed_json('没有传参`suppliers_id`或传参错误，格式是数组');
        if (empty($suppliersId)) make_json_result(true);
        
        //查看原有的合同绑定哪些供应商
        self::selectSql(array(
            'fields' => 'suppliers_id', 
            'where'  => 'contract_id='.$contractId
        ));
        $res = $this->db->getAll($this->sql);
        $haveSupId = array();
        foreach ($res as $v) {
            $haveSupId[] = $v['suppliers_id'];
        }
        
        //应该删除的供应商
        $removeId = array_diff($haveSupId, $suppliersId);
        if (!empty($removeId)) {
            $sql = 'DELETE FROM '.$this->table.' WHERE suppliers_id in ('.implode(',', $removeId).')'.' and contract_id='.$contractId;
            $res = $this->db->query($sql);
            if (!$res) failed_json('合同关联供应商失败！');
        }
        
        //需要添加的供应商
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
            if (!empty($where)) $where .= ' and ';
            $where .= 'c.contract_id='.$contractId;
        }
        if ($regionId > 0) {
            if (!empty($where)) $where .= ' and ';
            $where .= 's.region_id='.$regionId;
        }
        
        //page
        if (is_numeric($params['limit']) && is_numeric($params['offset'])) {
            $page = intval($params['limit']);
            if ($page < 0) $page = 0;
            $offset = intval($params['offset']);
            if ($offset < 0) $offset = 0;
            $limit = 'limit '.$page.','.$offset;
        }
        
        self::selectSql(array(
            'fields' => array(
                'c.contract_id', 
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
            'extend'=> 'ORDER BY cs.contract_id DESC,cs.suppliers_id DESC '.$limit
        ));
        $res = $this->db->getAll($this->sql);
        
        self::selectSql(array(
            'fields' => 'COUNT(cs.contract_id) AS num', 
            'as'     => 'cs',
            'join'   => 'LEFT JOIN contract AS c on cs.contract_id=c.contract_id '
                      .' LEFT JOIN users AS u on c.customer_id=u.user_id'
                      .' LEFT JOIN suppliers AS s on cs.suppliers_id=s.suppliers_id'
                      .' LEFT JOIN region AS r on s.region_id=r.region_id',
            'where'  => $where            
        ));
        $total = $this->db->getOne($this->sql);
        make_json_result(array('total'=>$total, 'data'=>$res));
    }
    
    
    /**
     * 合同对应供应商的信息
     * {
     *      "command" : "contToSup", 
     *      "entity"  : "contract", 
     *      "parameters" : {
     *          "contract_id" : "(int)"
     *      }
     * }
     */
    public function contToSup($entity, $parameters) 
    {
        self::init($entity, 'contract');
        
        $contractId = $parameters['contract_id'];
        if (!$contractId) failed_json('没有传参`contract_id`');
        
        //合同信息
        self::selectSql(array(
            'fields' => array(
                'contract_id', 
                'customer_id'
            ), 
            'where'  => 'contract_type=2 and contract_id='.$contractId
        ));
        $res = $this->db->getRow($this->sql);
        if ($res === false) failed_json('没有此合同相关信息');
        
        //合同下的所有供应商
        $this->table = 'contract_suppliers';
        self::selectSql(array(
            'fields' => array(
                's.suppliers_id', 
                's.suppliers_name', 
                's.region_id'
            ), 
            'as'     => 'cs', 
            'join'   => 'LEFT JOIN suppliers AS s on cs.suppliers_id=s.suppliers_id', 
            'where'  => 'cs.contract_id='.$contractId
        ));
        $res['suppliers'] = $this->db->getAll($this->sql);
        make_json_result($res);
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
        $user_id = empty($user_id) ? $_SESSION['admin_id'] : $parameters['user_id'];
        if (empty($user_id)) failed_json('没有传参`user_id`');
        
        if ($type == 2) {
            $contractId = intval($parameters['contract_id']);
            if (empty($parameters['contract_id'])) failed_json('没有传参`contract_id`');
            $where = ' and contract_id<>'.$contractId;
        }
        
        $params = self::validContValue($parameters['params']);
        
        //合同编号，合同名称不能重复
        self::selectSql(array(
            'fields' => 'contract_id',
            'where'  => 'contract_num="'.$params['contract_num'].'"'.$where
        ));
        $exist = $this->db->getOne($this->sql);
        if ($exist) failed_json('该合同编号已经存在！');
       
        self::selectSql(array(
            'fields' => 'contract_id',
            'where'  => 'contract_name="'.$params['contract_name'].'"'.$where
        ));
        $exist = $this->db->getOne($this->sql);
        if ($exist) failed_json('该合同名称已经存在！');
        
        //登记机构
        if (!$params['bank_id']) failed_json('没有传参`bank_id`');
        $sql = 'SELECT bank_name FROM bank WHERE bank_id='.$params['bank_id'];
        $params['registration'] = $this->db->getOne($sql);
        
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
            $sql = "SELECT user_name FROM admin_user WHERE user_id=".$user_id;
            $arr['create_by'] = $this->db->getOne($sql);
            
            $arr['create_user'] = $user_id;
            $arr['create_time'] = time();
        }
        return array(
            'params'=>$arr, 
            'goods_type'=>$params['goods_type'], 
            'contract_id'=>$contractId 
        );
    }
    
    
    /**
     * 合同信息值的合法化（合同添加，编辑时）
     * @param array $params
     * @return array $params
     */
    private function validContValue($params) 
    {
        foreach ($params as $k=>$v) {
            if (!is_array($v)) {
                $params[$k] = htmlspecialchars(trim($v));
            }
        }
        return $params;
    }
}
if ($_POST['command'] == 'uploadify') {
    $cont = Contract::getIns();
    $cont->run(array(
        'command'=> $_POST['command'],
        'entity' => $_POST['entity'],
        'parameters' => $_POST['parameters']
    ));
} else {
    $json = jsonAction($ApiList);
    $cont = Contract::getIns();
    $cont->run($json);
}

/* 
 * 合同测试信息
 * $var = array('contract_id'=>1, 'params'=> array(
    'contract_num'       => '0210000',
    'contract_name'      => 'ht0000',
    'contract_amount'    => '230000',
    'contract_status'    => '1',
    'contract_type'      => '2',
    'contract_sign_type' => '0',
    'customer_id'        => '2',
    'start_time'         => '2015-10-22',
    'end_time'           => '2015-12-31',
    'is_control'         => '1',
    'rate'               => '0.2%',
    'bank_id'            => '1',
    'attachment'         => '20151225153106.pdf',
    'remark'             => '测试',
    'goods_type'         => array(3,4,5)
)); */
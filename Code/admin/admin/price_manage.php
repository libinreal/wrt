<?php 
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require_once('ManageModel.php');

/**
 * 加价列表
 */
if ($_REQUEST['act'] == 'list') 
{
    $smarty->display('cont_list.htm');
    exit;
}
/**
 * 批量加价
 */
elseif ($_REQUEST['act'] == 'batch') 
{
    $smarty->display('');
    exit;
}
/**
 * 单个加价
 */
elseif ($_REQUEST['act'] == 'single') 
{
    $smarty->display('');
    exit;
}


$ApiList = array(
    'getAttributes', 
    'getBrands', 
    'batchPrice', 
    'deletePrice', 
    'getExistBatch', 
    'priceList'
);
/**
 * 加价管理API
 * @author <luolu@3ti.us>
 * 接口访问地址：http://admin.zj.dev/admin/price_manage.php
 * API接口方法的参数及返回值：
 * @param string $entity api接口参数,数据表名
 * @param array|null $parameters api接口需要的参数，例如：搜索条件等...
 * @return {"error":"成功返回0，失败返回-1", "message":"错误时返回错误信息", "content":"正确时返回数据"}
 */
class Price extends ManageModel 
{
    protected static $_instance;
    
    protected $table;
    protected $db;
    protected $sql;
    
    
    
    /**
     * 厂家列表
     * {
     *      "command" : "getBrands", 
     *      "entity"  : "brand", 
     *      "parameters" : {}
     * }
     */
    public function getBrands($entity, $parameters) 
    {
        self::init($entity, 'brand');
        self::selectSql(array(
            'fields' => array(
                'brand_id', 
                'brand_name'
            ), 
            'where'  => 'is_show=0', 
            'extend' => 'ORDER BY sort_order DESC'
        ));
        $data = $this->db->getAll($this->sql);
        if ($data === false) {
            failed_json('获取列表失败');
        } else {
            make_json_result($data);
        }
    }
    
    
    
    /**
     * 物料类型属性列表
     * {
     *      "command" : "getAttributes",
     *      "entity"  : "attribute",
     *      "parameters" : {
     *          "cat_id" : "(int)"  //`goods_type`的ID
     *      }
     * }
     */
    public function getAttributes($entity, $parameters) 
    {
        self::init($entity, 'attribute');
        $catId = $parameters['cat_id'];
        if (!($catId > 0)) failed_json('没有传参`cat_id`');
    
        self::selectSql(array(
            'fields' => array(
                'attr_id',
                'attr_name',
                'attr_values'
            ),
            'where'  => 'cat_id='.$catId,
            'extend' => 'ORDER BY sort_order DESC'
        ));
        $data = $this->db->getAll($this->sql);
        if ($data === false) {
            failed_json('获取列表失败');
        }
        foreach ($data as $k=>$v) {
            if (!$v['attr_values']) {
                $data[$k]['attr_values'] = array();
            } else {
                $values = explode("\r\n", $v['attr_values']);
                $data[$k]['attr_values'] = $values != false ? $values : array();
            }
        }
        make_json_result($data);
    }
    
    
    
    /**
     * 筛选批量加价   根据物料类别、厂家、供应商筛选已经存在的加价记录
     * {
     *      "command" : "getExistBatch", 
     *      "entity"  : "price_adjust", 
     *      "parameters" : {
     *              "cat_id"       : "(int)", //不能为空
     *              "brand_id"     : "(int)", 
     *              "suppliers_id" : "(int)"
     *      }
     * }
     */
    public function getExistBatch($entity, $parameters) 
    {
        self::init($entity, 'price_adjust');
        
        $catId = $parameters['cat_id'];
        $brandId = $parameters['brand_id'];
        $suppliersId = $parameters['suppliers_id'];
        if ($catId <= 0) failed_json('没有传参`cat_id`');
        
        //类型名称
        $this->table = 'goods_type';
        self::selectSql(array(
            'fields' => 'cat_name', 
            'where'  => 'cat_id='.$catId
        ));
        $catName = $this->db->getOne($this->sql);
        
        //厂家名称
        $brandName = '';
        if ($brandId) {
            $this->table = 'brand';
            self::selectSql(array(
                'fields' => 'brand_name', 
                'where'  => 'brand_id='.$brandId
            ));
            $brandName = $this->db->getOne($this->sql);
        }
        
        //供应商名称
        $suppliersName = '';
        if ($suppliersId) {
            $this->table = 'suppliers';
            self::selectSql(array(
                'fields' => 'suppliers_name', 
                'where'  => 'suppliers_id='.$suppliersId
            ));
            $suppliersName = $this->db->getOne($this->sql);
        }
        
        //where条件
        $where = 'and brand_id='.intval($brandId).' and suppliers_id='.intval($suppliersId);
        
        //查询批量加价
        $this->table = 'price_adjust';
        self::selectSql(array(
            'fields' => '*', 
            'where'  => 'cat_id='.$catId.' '.$where
        ));
        $res = $this->db->getAll($this->sql);
        if (!empty($res)) {
            foreach ($res as $k=>$v) {
                $res[$k]['cat_name'] = $catName;
                $res[$k]['brand_name'] = $brandName;
                $res[$k]['suppliers_name'] = $suppliersName;
            }
        }
        if ($res === false) {
            failed_json('获取信息失败');
        } else {
            make_json_result($res);
        }
    }
    
    
    
    /**
     * 批量加价    物料类别、厂家、供应商唯一存在不能同时重复
     * {
     *      "command" : "batchPrice", 
     *      "entity"  : "price_adjust", 
     *      "parameters" : {
     *              "user_id": "(int)", 
     *              "params" : [
     *                  {
     *                      "price_adjust_id" : "(int)", //编辑时传此值
     *                      "cat_id"          : "(int)", //不能为空
     *                      "brand_id"        : "(int)", 
     *                      "suppliers_id"    : "(int)", 
     *                      "price_num"       : "(int)", //不能为空
     *                      "price_rate"      : "(float)" //不能为空
     *                  }, ...
     *              ]
     *      }
     * }
     */
    public function batchPrice($entity, $parameters) 
    {
        self::init($entity, 'price_adjust');
        
        //user id
        $userId = (!$parameters['user_id']) ? $_SESSION['admin_id'] : $parameters['user_id'];
        if (!$userId) {
            failed_json('没有传参`user_id`');
        }
        
        //params
        $params = $parameters['params'];
        if (!is_array($params) || empty($params)) {
            failed_json('传参错误');
        }
        
        $upData = array();  //update
        $inData = array();  //insert
        
        //区分修改的批量加价和新添加的批量加价
        foreach ($params as $k=>$v) {
            if ($v['price_adjust_id'] > 0) {
                $upData[] = $v;
            } else {
                $inData[] = $v;
            }
        }
        
        //修改批量加价信息
        if (!empty($upData)) {
            $this->batchUpdate($upData);
        }
        
        //添加新信息
        if (!empty($inData)) {
            $this->batchInsert($inData, $userId);
        }
        make_json_result(true);
    }
    
    
    
    /**
     * 删除批量加价中的某一条数据
     * {
     *      "command" : "deletePrice", 
     *      "entity"  : "price_adjust", 
     *      "parameters" : {
     *          "price_adjust_id" : "(int)"
     *      }
     * }
     */
    public function deletePrice($entity, $parameters) 
    {
        self::init($entity, 'price_adjust');
        
        $id = $parameters['price_adjust_id'];
        if (!$id) failed_json('没有传参`price_adjust_id`');
        
        $sql = 'DELETE FROM '.$this->table.' WHERE price_adjust_id='.$id;
        $res = $this->db->query($sql);
        if ($res) {
            make_json_result($res);
        } else {
            failed_json('删除失败');
        }
    }
    
    
    
    /**
     * 加价列表
     * {
     *      "command" : "priceList", 
     *      "entity"  : "price_adjust", 
     *      "parameters" : {
     *          "params" : {
     *              "where" : {
     *                  "cat_id"       : "(int)", 
     *                  "brand_id"     : "(int)", 
     *                  "suppliers_id" : "(int)", 
     *                  "attributes"   : [
     *                      {
     *                          "attr_id"     : "(int)", 
     *                          "attr_values" : "(string)"
     *                      }, ...
     *                  ]
     *              }, 
     *              "limit" : "(int)", 
     *              "offset": "(int)"
     *          }
     *      }
     * }
     */
    public function priceList($entity, $parameters) 
    {
        self::init($entity, 'price_adjust');
        
        //page
        $params = $parameters['params'];
        if (is_numeric($params['limit']) && is_numeric($params['offset'])) {
            $page = intval($params['limit']);
            if ($page < 0) $page = 0;
            $offset = intval($params['offset']);
            if ($offset < 0) $offset = 0;
            $limit = 'limit '.$page.','.$offset;
        }
        
        self::selectSql(array(
            'fields' => '*', 
            'where'  => '', 
            'extend' => 'ORDER BY price_adjust_id ASC '.$limit
        ));
        $data = $this->db->getAll($this->sql);
        print_r($data);
    }
    
    
    
    /**
     * 修改批量加价信息
     * @param array $upData
     * @return boolean|json
     */
    private function batchUpdate($upData) 
    {
        //更新字段的数组
        $where = array();
        $fields = array();
        foreach ($upData as $k=>$v) {
            foreach ($v as $vk=>$vv) {
                $fields[$vk][] = 'WHEN '.$v['price_adjust_id'].' THEN '.$v[$vk];
            }
            $where[] = $v['price_adjust_id'];
        }
        
        //更新字段拼接
        $values = '';
        foreach ($fields as $k=>$v) {
            if ($k != 'price_adjust_id') {
                $values .= $k.'=CASE price_adjust_id ';
                foreach ($v as $vk=>$vv) {
                    $values .= $vv.' ';
                }
                $values .= ' END, ';
            }
        }
        $values = substr($values, 0, -2);
        
        //sql
        $where = implode(',', $where);
        $sql = 'UPDATE '.$this->table.' SET '.$values.' WHERE price_adjust_id in('.$where.')';
        $res = $this->db->query($sql);
        if ($res === false) {
            failed_json('修改失败');
        } else {
            return true;
        }
    }
    
    
    
    /**
     * 添加批量加价信息
     * @param array $inData
     * @param int $userId
     * @return boolean|json
     */
    private function batchInsert($inData, $userId) 
    {
        //存在的加价信息
        self::selectSql(array(
            'fields' => array(
                'cat_id',
                'brand_id',
                'suppliers_id'
            ),
            'where'  => 'type=0'
        ));
        $data = $this->db->getAll($this->sql);
        foreach ($data as $k=>$v) {
            $data[$k] = $v['cat_id'].$v['brand_id'].$v['suppliers_id'];
        }
        
        $exist = array();   //不需要添加的
        $new = array();     //新的信息
        
        //筛选需要添加的信息
        if (empty($data)) {
            $new = $inData;
        } else {
            $id = 0;
            $n = 0;     //数据已经存在的数量
            foreach ($inData as $k=>$v) {
                $id = $v['cat_id'].intval($v['brand_id']).intval($v['suppliers_id']);
                if (in_array($id, $data)) {
                    $exist[] = $inData[$k];
                    $n++;
                } else {
                    $new[] = $inData[$k];
                }
            }
            if ($n > 0) failed_json('添加的数据中有'.$n.'条数据已经存在');
        }
        
        if (empty($new)) {
            return true;
        }
        
        //字段名
        $fields = array(
            'cat_id',
            'brand_id',
            'suppliers_id',
            'price_num',
            'price_rate',
            'type',
            'update_time',
            'update_by'
        );
        $fields = implode(',', $fields);
        
        //字段值
        $values = array();$str = '';
        foreach ($new as $k=>$v) {
            $str = $v['cat_id'].',"'.$v['brand_id'].'","'.$v['suppliers_id'].'",'
                 .$v['price_num'].','.$v['price_rate']
                 .',0,'.time().','.$userId;
            $values[] = '('.$str.')';
        }
        $values = implode(',', $values);
        
        //insert
        $sql = 'INSERT '.$this->table.' ('.$fields.') values '.$values;
        $res = $this->db->query($sql);
        if ($res === false) {
            failed_json('批量加价失败');
        } else {
            return true;
        }
    }
    
    
}
$json = jsonAction($ApiList);
$price = Price::getIns();
$price->run($json);
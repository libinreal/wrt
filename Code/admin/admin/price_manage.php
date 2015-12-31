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
    'deletePrice'
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
        make_json_result($data);
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
        
    }
    
    
    /**
     * 批量加价    物料类别、厂家、供应商唯一存在不能同时重复
     * {
     *      "command" : "batchPrice", 
     *      "entity"  : "price_adjust", 
     *      "parameters" : {
     *              "user_id": "(int)", 
     *              "params" : {[
     *                  {
     *                      "price_adjust_id" : "(int)", //编辑时传此值
     *                      "cat_id"          : "(int)", //不能为空
     *                      "brand_id"        : "(int)", 
     *                      "suppliers_id"    : "(int)", 
     *                      "price_num"       : "(int)", //不能为空
     *                      "price_rate"      : "(float)" //不能为空
     *                  }, ...
     *              ]}
     *      }
     * }
     */
    public function batchPrice($entity, $parameters) 
    {
        self::init($entity, 'price_adjust');
        
        //user id
        $userId = (!$parameters['user_id']) ? $_SESSION['admin_id'] : $parameters['user_id'];
        if (!$userId) failed_json('没有传参`user_id`');
        
        //参数传递
        $params = $parameters['params'];
        if (!is_array($params) || empty($params)) {
            failed_json('传参错误');
        }
        
        //数据表存在的数据
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
        
        //已经存在不能添加的数据集合
        $exist = array();
        
        //不存在可以添加的数据集合
        $new = array();
        
        //需要添加的数据
        $id = 0;$n = 0;
        foreach ($params as $k=>$v) {
            $id = $v['cat_id'].$v['brand_id'].$v['suppliers_id'];
            if (in_array($id, $data)) {
                $exist[] = $params[$k];
                $n++;
            } else {
                $new[] = $params[$k];
            }
        }
        unset($params);
        
        if ($n > 0) failed_json('添加的数据中已经存在'.$n.'条数据');
        
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
        
        //字段值
        $values = array();$str = '';
        foreach ($new as $k=>$v) {
            $str = $v['cat_id'].','.$v['brand_id'].','.$v['suppliers_id'].','.$v['price_num'].','.$v['price_rate']
                    .',0,'.time().','.$userId;
            $values[] = '('.$str.')';
        }
        $values = implode(',', $values);
        
        //写入
        $sql = 'INSERT '.$this->table.' ('.implode(',', $fields).') values '.$values;
        $res = $this->db->query($sql);
        if ($res) {
            make_json_result($res);
        } else {
            failed_json('批量加价失败');
        }
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
    
}
$json = jsonAction($ApiList);
$price = Price::getIns();
$price->run($json);
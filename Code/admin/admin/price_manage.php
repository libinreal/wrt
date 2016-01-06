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
            'where'  => 'is_show=1', 
            'extend' => 'ORDER BY brand_id ASC,sort_order DESC'
        ));
        $data = $this->db->getAll($this->sql);
        if ($data === false) {
            failed_json('获取列表失败');
        } else {
            make_json_result($data);
        }
    }
    
    
    
    /**
     * 供应商列表
     * Api URL:http://admin.zj.dev/admin/contract_manage.php
     * args : 
     * {
     *      "command" : "suppliers", 
     *      "entity"  : "admin_user", 
     *      "parameters" : {}
     * }
     */
    
    
    
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
        if ($catId <= 0) {
            failed_json('没有传参`cat_id`');
        }
        
        //类型名称
        $this->table = 'goods_type';
        self::selectSql(array(
            'fields' => 'cat_name',
            'where'  => 'cat_id='.$catId
        ));
        $catName = $this->db->getOne($this->sql);
        if (!$catName) {
            failed_json('`cat_id`传参错误');
        }
        
        //where
        $where = 'cat_id='.$catId;
        if ($brandId) {
            $where .= ' and brand_id='.$brandId;
        }
        if ($suppliersId) {
            $where .= ' and suppliers_id='.$suppliersId;
        }
        
        //批量加价数据
        $this->table = 'price_adjust';
        self::selectSql(array(
            'fields' => '*', 
            'where'  => $where
        ));
        $res = $this->db->getAll($this->sql);
        if ($res === false) {
            failed_json('获取列表失败');
        }
        
        //查询所有厂家、供应商
        $brandId = array();
        $suppliersId = array();
        foreach ($res as $k=>$v) {
            if ($v['brand_id'] != 0 && !in_array($v['brand_id'], $brandId)) {
                array_push($brandId, $v['brand_id']);
            }
            if ($v['suppliers_id'] != 0 && !in_array($v['suppliers_id'], $suppliersId)) {
                array_push($suppliersId, $v['suppliers_id']);
            }
        }
        
        //所有厂家名称
        if ($brandId) {
            $this->table = 'brand';
            self::selectSql(array(
                'fields' => 'brand_id,brand_name',
                'where'  => 'brand_id in('.implode(',', $brandId).')'
            ));
            $brandName = $this->db->getAll($this->sql);
        }
        
        //所有供应商名称
        if ($suppliersId) {
            $this->table = 'suppliers';
            self::selectSql(array(
                'fields' => 'suppliers_id,suppliers_name',
                'where'  => 'suppliers_id in('.implode(',', $suppliersId).')'
            ));
            $suppliersName = $this->db->getAll($this->sql);
        }
        
        //对接厂家、供应商名称
        foreach ($res as $k=>$v) {
            $res[$k]['cat_name'] = $catName;
            if (!$brandName && !$suppliersName) {
                $res[$k]['brand_name'] = '';
                $res[$k]['suppliers_name'] = '';
            }
            if ($brandName) {
                foreach ($brandName as $bv) {
                    if ($v['brand_id'] == $bv['brand_id']) {
                        $res[$k]['brand_name'] = $bv['brand_name'];
                    } elseif ($v['brand_id'] == 0) {
                        $res[$k]['brand_name'] = '';
                    }
                }
            }
            if ($suppliersName) {
                foreach ($suppliersName as $sv) {
                    if ($v['suppliers_id'] == $sv['suppliers_id']) {
                        $res[$k]['suppliers_name'] = $sv['suppliers_name'];
                    } elseif ($v['suppliers_id'] == 0) {
                        $res[$k]['suppliers_name'] = '';
                    }
                }
            }
        }
        make_json_result($res);
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
     *                      "brand_id"        : "(int)", //为空则值是0
     *                      "suppliers_id"    : "(int)", //为空则值是0
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
        
        
        //获取批量加价的商品
        $this->table = 'goods';
        self::selectSql(array(
            'fields' => array(
                'goods_id',
                'cat_id',
                'brand_id',
                'suppliers_id'
            ),
            'where'  => 'price_type=0'
        ));
        $goods = $this->db->getAll($this->sql);
        if ($goods === false) {
            failed_json('获取商品信息失败');
        }
        
        
        //修改批量加价信息
        if ($upData) {
            $this->batchUpdate($upData);
            if ($goods) {
                $this->batchGoods($upData, $goods);
            }
        }
        
        //添加新信息
        if ($inData) {
            $this->batchInsert($inData, $userId);
            if ($goods) {
                $this->batchGoods($inData, $goods);
            }
        }
        
        make_json_result(true);
    }
    
    
    
    /**
     * 删除批量加价中的某一条数据
     * {
     *      "command" : "deletePrice", 
     *      "entity"  : "price_adjust", 
     *      "parameters" : {
     *          "price_adjust_id" : "(int)", //不能为空
     *          "cat_id"          : "(int)", //不能为空
     *          "brand_id"        : "(int)", //为空时值为0
     *          "suppliers_id"    : "(int)"  //为空时值为0
     *      }
     * }
     */
    public function deletePrice($entity, $parameters) 
    {
        self::init($entity, 'price_adjust');
        
        $id = $parameters['price_adjust_id'];
        $catId = $parameters['cat_id'];
        $brandId = $parameters['brand_id'];
        $suppliersId = $parameters['suppliers_id'];
        if (!$id || !$catId) failed_json('没有传参`price_adjust_id`或`cat_id`，或者传参错误');
        
        //删除加价规则
        $sql = 'DELETE FROM '.$this->table.' WHERE price_adjust_id='.$id;
        $res = $this->db->query($sql);
        if ($res === false) {
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
    
    
    
    /**
     * 修改加价规则对应的商品的加价幅度和加价比例
     * $params =    [
     *                  {
     *                      "price_adjust_id" : "(int)", //编辑时传此值
     *                      "cat_id"          : "(int)", //不能为空
     *                      "brand_id"        : "(int)", //为空则值是0
     *                      "suppliers_id"    : "(int)", //为空则值是0
     *                      "price_num"       : "(int)", //不能为空
     *                      "price_rate"      : "(float)" //不能为空
     *                  }, ...
     *              ];
     * $goods 商品数据
     */
    private function batchGoods($params, $goods) 
    {
        //加价规则集合
        $rule = array();
        foreach ($params as $k=>$v) {
            if ($v['cat_id'] && $v['brand_id'] && $v['suppliers_id']) {
                $rule[$v['cat_id']]['first'][] = $v;
            } elseif ($v['cat_id'] && $v['suppliers_id']) {
                $rule[$v['cat_id']]['second'][] = $v;
            } elseif ($v['cat_id'] && $v['brand_id']) {
                $rule[$v['cat_id']]['third'][] = $v;
            } elseif ($v['cat_id']) {
                $rule[$v['cat_id']]['fourth'][] = $v;
            }
        }
        if (!$rule) {
            failed_json('`params`传参内容错误');
        }
        
        //商品匹配加价规则
        $newPrice = $this->ruleFirst($goods, $rule);
        if (!$newPrice) {
            make_json_result('none catch');
        }
        
        //修改商品加价
        $fields = array();
        $id = array();
        foreach ($newPrice as $k=>$v) {
            $id[] = $v['goods_id'];
            foreach ($v as $vk=>$vv) {
                if ($vk != 'goods_id') {
                    $fields[$vk][$k] = 'WHEN '.$v['goods_id'].' THEN "'.$v[$vk].'"';
                }
                
            }
        }
        $str = '';
        foreach ($fields as $k=>$v) {
            $str .= $k.'=CASE goods_id';
            foreach ($v as $vk=>$vv) {
                $str .= ' '.$vv.' ';
            }
            $str .= ' END, ';
        }
        $str = substr($str, 0, -2);
        $sql = 'UPDATE '.$this->table.' SET '.$str.' WHERE goods_id in('.implode(',', $id).')';
        $res = $this->db->query($sql);
        if ($res === false) {
            failed_json('商品加价失败');
        } else {
            return true;
        }
    }
    
    
    
    /**
     * 商品匹配加价规则第1级，匹配物料类型、供应商和厂家
     * @param array $goods
     * @param array $rule
     * @return array|boolean
     */
    private function ruleFirst($goods, $rule) 
    {
        $newPrice = array();
        $srule = array();
        $i = 0;
        foreach ($goods as $k=>$v) {
            $srule = $rule[$v['cat_id']];
            if ($srule && $srule['first']) {
                foreach ($srule['first'] as $sv) {
                    if ($v['suppliers_id'] == $sv['suppliers_id'] && $v['brand_id'] == $sv['brand_id']) {
                        $newPrice[$i]['goods_id'] = $v['goods_id'];
                        $newPrice[$i]['price_num'] = $sv['price_num'];
                        $newPrice[$i]['price_rate'] = $sv['price_rate'];
                    }
                }
            }
            $i++;
        }
        if (!$newPrice) {
            return $this->ruleSecond($goods, $rule);
        } else {
            return $newPrice;
        }
    }
    
    
    
    /**
     * 商品匹配加价规则第2级，匹配物料类型和供应商
     * @param array $goods
     * @param array $rule
     * @return array|boolean
     */
    private function ruleSecond($goods, $rule) 
    {
        $newPrice = array();
        $srule = array();
        $i = 0;
        foreach ($goods as $k=>$v) {
            $srule = $rule[$v['cat_id']];
            if ($srule && $srule['second']) {
                foreach ($srule['second'] as $sv) {
                    if ($v['suppliers_id'] == $sv['suppliers_id']) {
                        $newPrice[$i]['goods_id'] = $v['goods_id'];
                        $newPrice[$i]['price_num'] = $sv['price_num'];
                        $newPrice[$i]['price_rate'] = $sv['price_rate'];
                    }
                }
                
            }
            $i++;
        }
        if (!$newPrice) {
            return $this->ruleThird($goods, $rule);
        } else {
            return $newPrice;
        }
    }
    
    
    
    /**
     * 商品匹配加价规则第3级，匹配物料类型和厂家
     * @param array $goods
     * @param array $rule
     * @return array|boolean
     */
    private function ruleThird($goods, $rule) 
    {
        $newPrice = array();
        $srule = array();
        $i = 0;
        foreach ($goods as $k=>$v) {
            $srule = $rule[$v['cat_id']];
            if ($srule && $srule['third']) {
                foreach ($srule['third'] as $sv) {
                    if ($v['brand_id'] == $sv['brand_id']) {
                        $newPrice[$i]['goods_id'] = $v['goods_id'];
                        $newPrice[$i]['price_num'] = $sv['price_num'];
                        $newPrice[$i]['price_rate'] = $sv['price_rate'];
                    }
                }
                
            }
            $i++;
        }
        if (!$newPrice) {
            return $this->ruleFourth($goods, $rule);
        } else {
            return $newPrice;
        }
    }
    
    
    
    /**
     * 商品匹配加价规则第4级，只匹配物料类型
     * @param array $goods
     * @param array $rule
     * @return array|boolean
     */
    private function ruleFourth($goods, $rule) 
    {
        $newPrice = array();
        $srule = array();
        $i = 0;
        foreach ($goods as $k=>$v) {
            $srule = $rule[$v['cat_id']];
            if ($srule && $srule['fourth']) {
                foreach ($srule['fourth'] as $sv) {
                    if ($v['cat_id'] == $sv['cat_id']) {
                        $newPrice[$i]['goods_id'] = $v['goods_id'];
                        $newPrice[$i]['price_num'] = $sv['price_num'];
                        $newPrice[$i]['price_rate'] = $sv['price_rate'];
                    }
                }
            }
            $i++;
        }
        if (!$newPrice) {
            return false;
        } else {
            return $newPrice;
        }
    }
    
    
}
$json = jsonAction($ApiList);
$price = Price::getIns();
$price->run($json);
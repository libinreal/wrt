<?php 
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require_once('ManageModel.php');

/**
 * 加价列表
 */
if ($_REQUEST['act'] == 'list') 
{
    $smarty->display('second/purchase_price_increase_list.html');
    exit;
}
/**
 * 批量加价
 */
elseif ($_REQUEST['act'] == 'batch') 
{
    $smarty->display('second/purchase_price_increase_batch.html');
    exit;
}
/**
 * 单个加价
 */
elseif ($_REQUEST['act'] == 'single') 
{
    $smarty->display('second/purchase_price_increase_single.html');
    exit;
}


$ApiList = array(
    'getAttributes', 
    'getBrands', 
    'batchPrice', 
    'deletePrice', 
    'getExistBatch', 
    'priceList', 
    'singlePrice', 
    'setPrice', 
	'deleteGoodsPrice'
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
            'where'  => '', //is_show=1', 
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
    
        $data = getAttributesByCatId($catId);
        if ($data === false) {
        	return failed_json('获取列表失败');
        }
        if (empty($data)) {
        	return make_json_result(array());
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
        die;
        //根据cat_id 获取code
        $this->table = 'category';
        self::selectSql(array(
        		'fields' => 'code', 
        		'where'  => 'cat_id='.$catId
        ));
        $code = $this->db->getOne($this->sql);
        
        //根据code 获取 goods_type 表的cat_id
        $this->table = 'goods_type';
        self::selectSql(array(
        		'fields' => 'cat_id', 
        		'where'  => 'code="'.$code.'"'
        ));
        $catId = $this->db->getOne($this->sql);
        
        //根据cat_id 获取属性
        $this->table = 'attribute';
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
     *              "cat_id"       : "(int)", //都为空则返回所有记录
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
        
        
        $where = '';
        //where
        if ($catId) {
            if (!empty($where)) $where .= ' and ';
            $where .= 'cat_id='.$catId;
        }
        if ($catId && $brandId) {
            if (!empty($where)) $where .= ' and ';
            $where .= 'brand_id='.$brandId;
        }
        if ($catId && $suppliersId) {
            if (!empty($where)) $where .= ' and ';
            $where .= 'suppliers_id='.$suppliersId;
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
        
        //查询所有物料类型、厂家、供应商
        $catId = array();
        $brandId = array();
        $suppliersId = array();
        foreach ($res as $k=>$v) {
            if ($v['cat_id'] != 0 && !in_array($v['cat_id'], $catId)) {
                array_push($catId, $v['cat_id']);
            }
            if ($v['brand_id'] != 0 && !in_array($v['brand_id'], $brandId)) {
                array_push($brandId, $v['brand_id']);
            }
            if ($v['suppliers_id'] != 0 && !in_array($v['suppliers_id'], $suppliersId)) {
                array_push($suppliersId, $v['suppliers_id']);
            }
        }
        
        //所有物料类型
        if ($catId) {
            $this->table = 'category';
            self::selectSql(array(
                'fields' => 'cat_id,cat_name',
                'where'  => 'cat_id in('.implode(',', $catId).')'
            ));
            $catName = $this->db->getAll($this->sql);
            if ($catName === false) failed_json('查询物料失败');
        }
        
        
        //所有厂家名称
        if ($brandId) {
            $this->table = 'brand';
            self::selectSql(array(
                'fields' => 'brand_id,brand_name',
                'where'  => 'brand_id in('.implode(',', $brandId).')'
            ));
            $brandName = $this->db->getAll($this->sql);
            if ($brandName === false) failed_json('查询厂家失败');
        }
        
        //所有供应商名称
        if ($suppliersId) {
            $this->table = 'suppliers';
            self::selectSql(array(
                'fields' => 'suppliers_id,suppliers_name',
                'where'  => 'suppliers_id in('.implode(',', $suppliersId).')'
            ));
            $suppliersName = $this->db->getAll($this->sql);
            if ($suppliersName === false) failed_json('查询供应商失败');
        }
        
        //对接厂家、供应商名称
        foreach ($res as $k=>$v) {
            $res[$k]['cat_name'] = $catName;
            if (!$brandName && !$suppliersName) {
                $res[$k]['cat_name'] = '';
                $res[$k]['brand_name'] = '';
                $res[$k]['suppliers_name'] = '';
            }
            if ($catName) {
                foreach ($catName as $cv) {
                    if ($v['cat_id'] == $cv['cat_id']) {
                        $res[$k]['cat_name'] = $cv['cat_name'];
                    } elseif ($v['cat_id'] == 0) {
                        $res[$k]['cat_name'] = '';
                    }
                }
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
     *                      "price_rate"      : "(float)" //不能为空 与price_num不能同时为空
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
        
        foreach ($params as $k=>$v) {
        	if (!$v['price_num'] && !$v['price_rate']) {
        		unset($params[$k]);
        		return failed_json('加价规则里存在加价幅度和加价比例都为空的规则');
        	}
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
        }
        
        //添加新信息
        if ($inData) {
            $data = $this->batchInsert($inData, $userId);
        }
        
        //匹配修改符合批量加价条件的商品加价规则
        if ($upData && $goods) {
        	//匹配4级加价规则
        	$this->batchGoods($upData, $goods);
        }
        
        //匹配添加符合批量加价条件的商品加价规则
        if ($inData && $goods && is_array($data)) {
        	//匹配4级加价规则
        	$this->batchGoods($data, $goods);
        }
        
        make_json_result(true);
    }
    
    
    
    /**
     * 删除批量加价中的某一条数据
     * {
     *      "command" : "deletePrice", 
     *      "entity"  : "goods", 
     *      "parameters" : {
     *          "price_adjust_id" : "(int)", //不能为空
     *      }
     * }
     */
    public function deletePrice($entity, $parameters) 
    {
        self::init($entity, 'goods');
        
        $id = $parameters['price_adjust_id'];
        if (!$id) {
            failed_json('没有传参`price_adjust_id`');
        }
        //删除加价规则对应的商品加价幅度、加价比例、加价规则id
        $sql = 'UPDATE '.$this->table.' SET price_num="0",price_rate="0",price_rule="0" WHERE price_rule="'.$id.'"';
        $res = $this->db->query($sql);
        if ($res === false) {
            failed_json('删除商品加价规则失败');
        }
        
        //删除加价规则
        $this->table = 'price_adjust';
        $sql = 'DELETE FROM '.$this->table.' WHERE price_adjust_id='.$id;
        $res = $this->db->query($sql);
        if ($res === false) {
            failed_json('删除加价规则失败');
        } else {
            make_json_result($res);
        }
    }
    
    
    
    /**
     * 加价列表
     * {
     *      "command" : "priceList", 
     *      "entity"  : "goods", 
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
        self::init($entity, 'goods');
        $params = $parameters['params'];
        $psWhere = $params['where'];
        
        //where筛选条件
        $catId = $psWhere['cat_id'];
        $brandId = $psWhere['brand_id'];
        $suppliersId = $psWhere['suppliers_id'];
        $attributes = $psWhere['attributes'];
        $where = '';
        
        //获取可筛选的所有物料类型
        $this->table = 'category';
        self::selectSql(array(
        		'fields' => array( 'cat_id', 'parent_id' ),
        		'extend' => ' ORDER BY cat_id ASC'
        ));
        $result = $this->db->getAll($this->sql);
        $cateList = $this->levelCat($catId, $result);
        array_push($cateList, $catId);
        $cateList = array_unique($cateList);
        
        //筛选条件
        if ($catId && $cateList) {
            $where .= ' and cat_id in('.implode(',', $cateList).')';
        }
        if ($brandId) {
            $where .= ' and brand_id='.$brandId;
        }
        if ($suppliersId) {
            $where .= ' and suppliers_id='.$suppliersId;
        }
        
        //当传值属性筛选时，不用mysql limit
        $useLimit = true;
        if ($catId && $attributes) {
            $useLimit = false;
        }
        
        //page
        if ($useLimit && is_numeric($params['limit']) && is_numeric($params['offset'])) {
            $page = (intval($params['limit']) < 0) ? 0 : intval($params['limit']);
            $offset = (intval($params['offset']) < 0) ? 0 : intval($params['offset']);
            $limit = 'limit '.$page.','.$offset;
        }
        
        if (!$useLimit) {
            $limit = '';
        }
        
        //获取商品所有加价记录
        $this->table = 'goods';
        self::selectSql(array(
            'fields' => array(
                'goods_id', 
                'cat_id', 
                'brand_id', 
                'suppliers_id', 
                'goods_name', 
                'price_num', 
                'price_rate', 
                'price_type', 
                'shop_price'
            ),  
            'where'  => '((price_num!=0 and price_rule!=0) or (price_type=1) or (price_rate!=0)) '.$where, 
            'extend' => 'ORDER BY goods_id ASC,sort_order DESC '.$limit
        ));
        //echo $this->sql;die;
        $data = $this->db->getAll($this->sql);
        if ($data === false) {
            failed_json('获取列表失败');
        }
        
        //总记录数
        if ($limit) {
            self::selectSql(array(
                'fields' => 'COUNT(*) AS num',
                'where'  => '((price_num!=0 and price_rule!=0) or (price_type=1) or (price_rate!=0)) '.$where,
            ));
            $total = $this->db->getOne($this->sql);
            if ($total === false) {
                failed_json('获取总记录数失败');
            }
        }
        
        
        //所有物料类型id
        $catId = array();
        $goodsId = array();
        foreach ($data as $k=>$v) {
            $catId[] = $v['cat_id'];
            $goodsId[] = $v['goods_id'];
        }
        $catId = array_unique($catId);
        
        //获取物料类型名称
        if ($catId) {
            $this->table = 'category';
            self::selectSql(array(
                'fields' => array(
                    'cat_id',
                    'cat_name',
                ),
                'where'  => 'cat_id in('.implode(',', $catId).')'
            ));
            $catName = $this->db->getAll($this->sql);
            if ($catName === false) {
                failed_json('获取物料类型失败');
            }
        }
        
        
        //获取所有属性
        if ($goodsId) {
            $this->table = 'goods_attr';
            self::selectSql(array(
                'fields' => array(
                    'goods_id', 
                    'attr_id', 
                    'attr_value'
                ),
                'where'  => 'goods_id in('.implode(',', $goodsId).')'
            ));
            $attr = $this->db->getAll($this->sql);
            if ($attr === false) {
                failed_json('获取属性列表失败');
            }
            $values = array();
            foreach ($attr as $k=>$v) {
                $values[$v['goods_id']]['attr_id'][] = $v['attr_id'];
                $values[$v['goods_id']]['attr_value'][] = $v['attr_value'];
            }
        }
        
        
        //物料名称、属性与商品绑定
        if ($data) {
            foreach ($data as $k=>$v) {
                foreach ($catName as $cv) {
                    if ($v['cat_id'] == $cv['cat_id']) {
                        $data[$k]['cat_name'] = $cv['cat_name'];
                    }
                }
                foreach ($values as $vk=>$vv) {
                    if ($v['goods_id'] == $vk) {
                        $data[$k]['attr_values'] = $vv['attr_value'];
                        $data[$k]['attr_id'] = $vv['attr_id'];
                    }
                }
                $data[$k]['price_type'] = ($v['price_type'] == 0) ? '批量' : '单个';
            }
        }
        
        //当有属性筛选记录时，则使用手动数组分页
        if (!$useLimit) {
            $atWhere = array();
            
            foreach ($attributes as $k=>$v) {
                foreach ($v as $vk=>$vv) {
                    $atWhere[$vk][] = $vv;
                }
                
            }
            
            foreach ($atWhere as $k=>$v) {
                foreach ($data as $dk=>$dv) {
                    if ($k == 'attr_values' && array_diff($v, $dv['attr_values'])) {
                        unset($data[$dk]);
                    }
                    if ($k == 'attr_id' && array_diff($v, $dv['attr_id'])) {
                        unset($data[$dk]);
                    }
                }
            }
            sort($data);
            $total = count($data);
            $data = array_slice($data, $params['limit'], $params['offset']);
        }
        
        
        make_json_result(array('total'=>$total, 'data'=>$data));
    }
    
    
    /**
     * 所有下级物料类别
     * @param int $parentId 父级id
     * @param array $cateList
     * @return 0|array
     */
    private function levelCat($parentId, $cateList)
    {
    	$tmpRes = $this->getKidCates($parentId, $cateList);
    	$output = array();
    	foreach ($tmpRes as $k => $v)
    	{
    		$output []= $v;
    		if (!empty($tmpRes))
    		{
    			$output = array_merge($output, $this->levelCat($v, $cateList));
    		}
    	}
    	return $output;
    }
    
    
    /**
     * 获取子集物料
     * @param int $parentId
     * @param array $result
     * @return NULL|array
     */
    private function getKidCates($parentId, $result)
    {
    	$data = array();$i = 0;
    	if (!$result) return null;
    	foreach ($result as $k=>$v) {
    		if ($v['parent_id'] == $parentId) {
    			$data[$i] = $v['cat_id'];
    			$i++;
    		}
    	}
    	return $data;
    }
    
    
    
    /**
     * 单个商品加价信息
     * {
     *      "command" : "singlePrice", 
     *      "entity"  : "goods", 
     *      "parameters" : {
     *          "goods_id" : "(int)"
     *      }
     * }
     */
    public function singlePrice($entity, $paramters) 
    {
        self::init($entity, 'goods');
        $goodsId = $paramters['goods_id'];
        if (!$goodsId) {
            failed_json('没有传参`goods_id`');
        }
        
        //获取加价信息
        self::selectSql(array(
            'fields' => array(
                'a.goods_id',
                'a.cat_id',
                'a.brand_id',
                'a.suppliers_id',
                'a.goods_name',
                'a.price_num',
                'a.price_rate',
                'a.price_type',
                'a.shop_price', 
                'b.cat_name'
            ), 
            'as'     => 'a', 
            'join'   => 'LEFT JOIN goods_type AS b on a.cat_id=b.cat_id', 
            'where'  => 'a.goods_id='.$goodsId
        ));
        $data = $this->db->getRow($this->sql);
        if ($data === false) {
            failed_json('获取信息失败，或者信息不存在');
        }
        
        //获取商品对应的属性
        $this->table = 'goods_attr';
        self::selectSql(array(
            'fields' => array(
                'a.goods_id', 
                'attr_value', 
                'b.attr_name'
            ), 
            'as'     => 'a', 
            'join'   => 'LEFT JOIN attribute AS b on a.attr_id=b.attr_id', 
            'where'  => 'a.goods_id='.$data['goods_id']
        ));
        $attr = $this->db->getAll($this->sql);
        if ($attr === false) {
            failed_json('获取属性失败');
        }
        
        $data['attr'] = $attr;
        make_json_result($data);
    }
    
    
    
    /**
     * 单个加价
     * {
     *      "command" : "setPrice", 
     *      "entity"  : "goods", 
     *      "parameters" : {
     *          "goods_id" : "(int)", 
     *          "params"   : {
     *              "price_num" : "(int)"
     *          }
     *      }
     * }
     */
    public function setPrice($entity, $parameters) 
    {
        self::init($entity, 'goods');
        $goodsId = $parameters['goods_id'];
        if (!$goodsId) {
            failed_json('没有传参`goods_id`');
        }
        
        $priceNum = $parameters['params']['price_num'];
        if ($priceNum <= 0) {
        	failed_json('价格必须大于0');
        }
        
        $sql = 'UPDATE '.$this->table.' SET price_num="'.$priceNum.'",price_type=1 WHERE goods_id='.$goodsId;
        $res = $this->db->query($sql);
        if ($res === false) {
            failed_json('改价失败');
        }
        make_json_result(true);
    }
    
    
    
    /**
     * 删除单个加价商品
     * {
     *      "command" : "deleteGoodsPrice",
     *      "entity"  : "goods",
     *      "parameters" : {
     *          "goods_id" : "(int)",
     *      }
     * }
     */
    public function deleteGoodsPrice($entity, $parameters) 
    {
    	self::init($entity, 'goods');
    	$goodsId = $parameters['goods_id'];
    	if (!$goodsId) {
    		failed_json('没有传参`goods_id`');
    	}
    	
    	$sql = 'UPDATE '.$this->table.' SET price_num=0,price_rate=0,price_type=0,price_rule=0 WHERE goods_id='.$goodsId;
    	$res = $this->db->query($sql);
    	if ($res === false) {
    		failed_json('改价失败');
    	}
    	make_json_result(true);
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
                $fields[$vk][] = 'WHEN '.$v['price_adjust_id'].' THEN '.intval($v[$vk]);
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
        $this->table = 'price_adjust';
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
        $this->table = 'price_adjust';
        self::selectSql(array(
            'fields' => array(
                'cat_id',
                'brand_id',
                'suppliers_id'
            ),
            'where'  => 'type=0'
        ));
        $data = $this->db->getAll($this->sql);
        if ($data === false) failed_json('查询加价信息失败');
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
            failed_json('添加加价规则失败');
        }
        
        //获取批量添加的id
        $fields = array();
        foreach ($new as $k=>$v) {
            $fields[] = '(cat_id='.intval($v['cat_id']).' and brand_id='.intval($v['brand_id']).' and suppliers_id='.intval($v['suppliers_id']).')';
        }
        $where = implode(' or ', $fields);
        self::selectSql(array(
            'fields' => array(
                'price_adjust_id',
                'cat_id',
                'brand_id',
                'suppliers_id', 
                'price_num', 
                'price_rate'
            ),
            'where'  => $where
        ));
        $data = $this->db->getAll($this->sql);
        if ($data === false) {
            failed_json('获取加价规则id失败');
        }
        return $data;
    }
    
    
    
    /**
     * 修改加价规则对应的商品的加价幅度和加价比例
     * $params = [
     *            {
     *                "price_adjust_id" : "(int)", //编辑时传此值
     *                "cat_id"          : "(int)", //不能为空
     *                "brand_id"        : "(int)", //为空则值是0
     *                "suppliers_id"    : "(int)", //为空则值是0
     *                "price_num"       : "(int)", //不能为空
     *                "price_rate"      : "(float)" //不能为空
     *             }, ...
     *  ];
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
            return true;
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
        $this->table = 'goods';
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
                        $newPrice[$i]['price_rule'] = $sv['price_adjust_id'];
                    }
                }
            }
            $i++;
        }
        
        if ($newPrice) {
            foreach ($newPrice as $k=>$v) {
                foreach ($goods as $gk=>$gv) {
                    if ($v['goods_id'] == $gv['goods_id']) {
                        unset($goods[$gk]);
                    }
                    
                }
            }
            
        }
        return $this->ruleSecond($goods, $rule, $newPrice);
    }
    
    
    
    /**
     * 商品匹配加价规则第2级，匹配物料类型和供应商
     * @param array $goods
     * @param array $rule
     * @return array|boolean
     */
    private function ruleSecond($goods, $rule, $newPrice) 
    {
        $srule = array();
        $i = count($newPrice)+1;
        foreach ($goods as $k=>$v) {
            $srule = $rule[$v['cat_id']];
            if ($srule && $srule['second']) {
                foreach ($srule['second'] as $sv) {
                    if ($v['suppliers_id'] == $sv['suppliers_id']) {
                        $newPrice[$i]['goods_id'] = $v['goods_id'];
                        $newPrice[$i]['price_num'] = $sv['price_num'];
                        $newPrice[$i]['price_rate'] = $sv['price_rate'];
                        $newPrice[$i]['price_rule'] = $sv['price_adjust_id'];
                    }
                }
                
            }
            $i++;
        }
        
        if ($newPrice) {
            foreach ($newPrice as $k=>$v) {
                foreach ($goods as $gk=>$gv) {
                    if ($v['goods_id'] == $gv['goods_id']) {
                        unset($goods[$gk]);
                    }
                }
            }
        
        }
        return $this->ruleThird($goods, $rule, $newPrice);
    }
    
    
    
    /**
     * 商品匹配加价规则第3级，匹配物料类型和厂家
     * @param array $goods
     * @param array $rule
     * @return array|boolean
     */
    private function ruleThird($goods, $rule, $newPrice) 
    {
        $srule = array();
        $i = count($newPrice)+1;
        foreach ($goods as $k=>$v) {
            $srule = $rule[$v['cat_id']];
            if ($srule && $srule['third']) {
                foreach ($srule['third'] as $sv) {
                    if ($v['brand_id'] == $sv['brand_id']) {
                        $newPrice[$i]['goods_id'] = $v['goods_id'];
                        $newPrice[$i]['price_num'] = $sv['price_num'];
                        $newPrice[$i]['price_rate'] = $sv['price_rate'];
                        $newPrice[$i]['price_rule'] = $sv['price_adjust_id'];
                    }
                }
                
            }
            $i++;
        }
        if ($newPrice) {
            foreach ($newPrice as $k=>$v) {
                foreach ($goods as $gk=>$gv) {
                    if ($v['goods_id'] == $gv['goods_id']) {
                        unset($goods[$gk]);
                    }
                }
            }
        
        }
        return $this->ruleFourth($goods, $rule, $newPrice);
    }
    
    
    
    /**
     * 商品匹配加价规则第4级，只匹配物料类型
     * @param array $goods
     * @param array $rule
     * @return array|boolean
     */
    private function ruleFourth($goods, $rule, $newPrice) 
    {
        $srule = array();
        $i = count($newPrice)+1;
        foreach ($goods as $k=>$v) {
            $srule = $rule[$v['cat_id']];
            if ($srule && $srule['fourth']) {
                foreach ($srule['fourth'] as $sv) {
                    if ($v['cat_id'] == $sv['cat_id']) {
                        $newPrice[$i]['goods_id'] = $v['goods_id'];
                        $newPrice[$i]['price_num'] = $sv['price_num'];
                        $newPrice[$i]['price_rate'] = $sv['price_rate'];
                        $newPrice[$i]['price_rule'] = $sv['price_adjust_id'];
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
<?php

use Phalcon\Db\Column as Column;
use PhpRudder\Mvc\ControllerBase;
use PhpRudder\Http\ResponseApi;
use Phalcon\Mvc\Model\Transaction\Manager as TxManager;

class GoodsController extends ControllerBase {

	//返回浏览记录条数
	const HISTORY_SZIE = 9;

	//推荐商品条数
	const RD_GOODS_SIZE = 5;

	//大宗类商品主分类code
	private static $goodsDZCodes = array('10000000', '20000000');

	//标品类商品主分类code
	private static $goodsBPCodes = array('30000000', '40000000', '50000000', '60000000', '70000000');

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;

    }

    /**
     * 获取商品分类列表 JJSC-01
     */
    public function getAllCategoryAction() {
        $result = Category::find(array(
            "columns"=>'id,name,cdesc,parentId,unit,attr,level,code',
            "order"=>"code"
        ));
        if (is_object($result) && $result->count()) {
            $r = $result->toArray();
            $nodes = array();
            $root = new \PhpRudder\Mvc\TreeNode();
            foreach($r as $item) {
                $node = new \PhpRudder\Mvc\TreeNode();
                $node->id = (int)$item['id'];
                $node->name = $item['name'];
                $node->parentId = (int)$item['parentId'];
                $node->level = (int)$item['level'];
                $node->code = $item['code'];
                $nodes[$node->id] = $node;
                if ($node->parentId == 0) {
                    $root->addChild($node);
                } else {
                    $nodes[$node->parentId]->addChild($node);
                }
            }
        }

        return ResponseApi::send($root->children);
    }

    /**
     * 获取推荐商品 JJSC-02
     */
    public function getRdGoodsAction() {
// 		$cache = $this->get_cache();
// 		if($cache->exists('rdGoods')) {
// 			$rdGoods = $cache->get('rdGoods');
// 			return ResponseApi::send(unserialize($rdGoods));
// 		}
    	$areaId = $this->request->get('areaId', 'int');
    	$size = $this->request->get('size', 'int') ?: self::RD_GOODS_SIZE;
    	if(!$areaId) {
    		return ResponseApi::send(null, Message::$_ERROR_NOFOUND, "请先选择区域！");
    	}
    	
    	$goodsObj = new Goods();
    	$rdGoods = array();
		foreach(array_merge(self::$goodsDZCodes, self::$goodsBPCodes) as $code) {
			$code = substr($code, 0, 2);
	    	$sqlStatement = 'SELECT
							    goods_id id,
							    goods_name name,
								IF(goods_thumb LIKE "http://%", goods_thumb, CONCAT("' . $this->get_url() . '", goods_thumb)) thumb,
							    market_price price,
							    shop_price vipPrice,
								RPAD(LEFT(cat_code, 4), 8, 0) code, 
								price_num, price_rate
							FROM goods
							WHERE (is_best = 1)
										AND (LEFT(cat_code, 2) = '.$code.')
										AND '.($areaId != 1 ? '(area_id = '.$areaId.' OR area_id = 1)' : '(1)').'
							ORDER BY last_update DESC
							LIMIT ' . $size;
	    	$result = $goodsObj->getReadConnection()->query($sqlStatement);
			if($result->numRows()) {
				$result->setFetchMode(PDO::FETCH_ASSOC);
				$goods = $result->fetchAll();
				$rdGoods[str_pad($code, 8, '0')] = $goods;
			}
		}
// 		$cache->save('rdGoods', serialize($rdGoods));
		foreach ($rdGoods as $k=>$v) {
			foreach ($v as $vk=>$vv) {
				$rdGoods[$k][$vk]['vipPrice'] = $this->showShopPrice($vv);
			}			
		}
		return ResponseApi::send($rdGoods);
    }

    
    /**
     * 获取推荐品牌 JJSC-03
     */
    public function getRdBrandsAction() {
    	$areaId = $this->request->get('areaId', 'int');
    	if(!$areaId) {
    		return ResponseApi::send(null, Message::$_ERROR_NOFOUND, "请先选择区域！");
    	}
    	$code = $this->request->get('code', 'int');
    	$brands = array();
    	if($code) {
			$size = 6;
			$brandRecommend = new BrandRecommend();
			$sqlStatement = 'SELECT
							    GROUP_CONCAT(DISTINCT brand_id
							        ORDER BY brand_rid DESC) brandIds,
								RPAD(LEFT(cat_code, 6), 8, 0) code
							FROM
							    brand_recommend
							WHERE
							    '.($areaId != 1 ? '(area_id = '.$areaId.' OR area_id = 1)' : '(1)').' AND LEFT(cat_code, 1) = '.substr($code, 0, 1).'
							GROUP BY LEFT(cat_code, 6)';
			$result = $brandRecommend->getReadConnection()->query($sqlStatement);
			if(is_object($result) && $result->numRows()) {
				$result->setFetchMode(PDO::FETCH_ASSOC);
				foreach($result->fetchAll() as $r) {
					$brandIds = $r['brandIds'];
					$code = $r['code'];
					$sqlStatement = 'SELECT
										brand_id id,
										CONCAT("'.$this->get_url().'", brand_logo) logo,
										brand_name name
									FROM brand WHERE brand_id IN ('.$brandIds.')
									ORDER BY FIND_IN_SET(brand_id, "'.$brandIds.'")
									LIMIT '.$size;
					$brand = new Brand();
					$result = $brand->getReadConnection()->query($sqlStatement);
					$result->setFetchMode(PDO::FETCH_ASSOC);
					$brands[$code] = $result->fetchAll();
				}
			}

    	} else {
 			$size = 4;
			$brandRecommend = new BrandRecommend();
			$sqlStatement = 'SELECT
							    GROUP_CONCAT(DISTINCT brand_id
							        ORDER BY brand_rid DESC) brandIds,
								RPAD(LEFT(cat_code, 2), 8, 0) code
							FROM
							    brand_recommend
							WHERE
							    '.($areaId != 1 ? '(area_id = '.$areaId.' OR area_id = 1)' : '(1)').'
							GROUP BY LEFT(cat_code, 2)';
			$result = $brandRecommend->getReadConnection()->query($sqlStatement);
			if(is_object($result) && $result->numRows()) {
				$result->setFetchMode(PDO::FETCH_ASSOC);
				foreach($result->fetchAll() as $r) {
					$brandIds = $r['brandIds'];
					$code = $r['code'];
					$sqlStatement = 'SELECT
										brand_id id,
										CONCAT("'.$this->get_url().'", brand_logo) logo,
										brand_name name
									FROM brand WHERE brand_id IN ('.$brandIds.')
									ORDER BY FIND_IN_SET(brand_id, "'.$brandIds.'")
									LIMIT '.$size;
					$brand = new Brand();
					$result = $brand->getReadConnection()->query($sqlStatement);
					$result->setFetchMode(PDO::FETCH_ASSOC);
					$brands[$code] = $result->fetchAll();
				}
			}
    	}
    	return ResponseApi::send($brands);
    }

    /**
     * 获取商品分类的搜索条件 JJSC-04
     */
    public function getSearchConditionAction() {
		$code = $this->request->get('code', 'int');
		if(!$code){
			return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法");
		}
        //$code = substr($code, 0, 4) . '0000';
        //else {
			//list($minimum, $maximum) = array_values(self::getCodeConditions($code));
		//}
		$criteria = GoodsType::query();
		$criteria->innerJoin('Attribute', 'a.code=GoodsType.code', 'a');
		//$criteria->betweenWhere('GoodsType.code', $minimum, $maximum);
        $criteria->where('GoodsType.code='.$code);
		$criteria->orderBy('a.sort');
		$criteria->columns('a.id,a.name,a.avalues');
		$result = $criteria->execute();
		$condition = $category = array();
		if(is_object($result) && $result->count()) {
			$result = $result->toArray();
			foreach($result as $r) {
				$r['values'] = explode("\r\n", $r['avalues']);
				unset($r['avalues']);
				$condition[] = $r;
			}
		}
		//增加分类
		if(substr($code, -2) == '00') {
			if(substr($code, 2, 4) == '00') {
				$conditions = 'substring(code, 1, 2) = "' . substr($code, 0, 2) . '" AND substring(code, -4) = "0000" AND code <> ' . $code;
			} else if(substr($code, 4, 6) == '00') {
				$conditions = 'substring(code, 1, 4) = "' . substr($code, 0, 4) . '" AND substring(code, -2) = "00" AND code <> ' . $code;
			} else {
				$conditions = 'substring(code, 1, 6) = "' . substr($code, 0, 6) . '" AND code <> ' . $code;
			}
			$result = Category::find(array(
					'conditions' => $conditions,
					'columns' => 'name,code',
					'order' => 'code'
			));
			if(is_object($result) && $result->count()) {
				$category = $result->toArray();
			}
		}
		$conditions = compact('condition', 'category');
		return ResponseApi::send($conditions);
    }

    /**
     * 获取大宗类的商品列表 JJSC-05
     */
    public function searchDZAction() {
		$code = $this->request->get('code', 'int');
		$areaId = $this->request->get('areaId', 'int');
		$createAt = $this->request->get('createAt', 'int') ?: time();
		$queryKeys = $this->request->get('queryKeys');
		$goodsId = $this->request->get('goodsId', 'int');
		$forward = $this->request->get('forward', 'int');
		if(!$code || !$areaId){
			return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法");
		}
		$sortKey = $this->request->get('sortkey');
		$sort = $this->request->get('sort');
		if($sortKey) {
			$sortArr = array('price' => 'market_price', 'salesNum' => '', 'storeNum' => 'goods_number');
			if(!array_key_exists($sortKey, $sortArr)) {
				return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法");
			}
			if($sortKey != 'salesNum') {
				$sortKey = 'goods.'.$sortArr[$sortKey];
			}
		}
		if($sort) {
			$sort = strtoupper($sort);
			if(!in_array($sort, array('ASC', 'DESC'))) {
				return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法");
			}
		} else {
			$sort = 'DESC';
		}
		if($queryKeys) {
			$queryKeys = array_map(function($queryKey) {
				$queryKey = explode(':', $queryKey);
				if(!is_array($queryKey) || !isset($queryKey[1]) || $queryKey[1] == "" || isset($queryKey[2])) {
					return  false;
				}
				return $queryKey;
			}, explode(';', trim($queryKeys, ';')));
			if(in_array(false, $queryKeys, true)) {
				return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法");
			}
		}
		$factoryId = $this->request->get('factoryId');
		$goodsName = $this->request->get('goodsName');
		$size = $this->request->get('size', 'int') ?: parent::SIZE;
		$codeConditions = self::getCodeConditions($code);
		$orderBy = array();
		if($goodsId) {
			$orderBy[0] = 'IF(goods.goods_id = '.$goodsId.', 1, 0) DESC';
		}
		$conditions = '(goods.is_delete = 0)';
		if($forward) {
			$conditions .= ' AND (goods.add_time > '.$createAt.')';
			$orderBy[2] = 'goods.add_time ASC';
		} else {
			$conditions .= ' AND (goods.add_time < '.$createAt.')';
			$orderBy[2] = 'goods.add_time DESC';
		}
		if(substr($code, 0, 4) == '2000') {
			$conditions .= ' AND (LEFT(goods.cat_code, 4) <> "2002" AND goods.cat_code>='.$codeConditions['minCode'].' AND goods.cat_code<='.$codeConditions['maxCode'].') AND '.($areaId != 1 ? '(area_id = '.$areaId.' OR area_id = 1)' : '(1)').'';
		} else {
			$conditions .= ' AND (goods.cat_code>='.$codeConditions['minCode'].' AND goods.cat_code<='.$codeConditions['maxCode'].') AND '.($areaId != 1 ? '(area_id = '.$areaId.' OR area_id = 1)' : '(1)').'';
		}
		if($factoryId) {
			$conditions .= ' AND (goods.brand_id='.$factoryId.')';
		}
		if($goodsName) {
			$conditions .= ' AND (goods.goods_name LIKE "%'.$goodsName.'%")';
		}
		if($queryKeys) {
			$querys = '';
			foreach($queryKeys as $queryKey) {
				$querys .= ' AND FIND_IN_SET("'.implode('', $queryKey).'", attr.querys)';
			}
			$querys = trim($querys, ' AND ');
			$conditions .= ' AND ('.$querys.')';
		}
		if($sortKey != 'salesNum' && $sortKey) {
			$orderBy[1] = $sortKey.' '.$sort;
		}
		$userId = $this->get_user()->id;
		$leftOrderGoods = '';
		if($sortKey == 'salesNum') {
			$orderBy[1] = 'FLOOR(COUNT(goods.goods_id) / COUNT(DISTINCT goods_attr.goods_attr_id))'.' '.$sort;
			$leftOrderGoods = '
					    LEFT JOIN
				    order_goods ON order_goods.goods_id = goods.goods_id
			';
		}
		ksort($orderBy);
		$orderBy = implode(', ', $orderBy);
		$sqlStatement = "
				SELECT
				    goods.goods_id id,
				    goods.goods_name name,
				    goods.market_price price,
				    IF(goods.goods_thumb LIKE 'http://%',
				        goods.goods_thumb,
				        CONCAT('" . $this->get_url() . "',
				                goods.goods_thumb)) thumb,
				    goods.shiplocal,
				    goods.shop_price vipPrice,
				    goods.price_num, 
				    goods.price_rate, 
				    goods.goods_number storeNum,
				    brand.brand_name factoryName,
				    suppliers.suppliers_name supplier,
				    category.measure_unit unit,
				    GROUP_CONCAT(DISTINCT CONCAT(goods_attr.goods_attr_id,
				                ':',
				                IF(attribute.attr_name IS NULL,
				                    '',
				                    attribute.attr_name),
				                ':',
				                IF(goods_attr.attr_value IS NULL,
				                    '',
				                    goods_attr.attr_value),
				                ':',
				                IF(goods_attr.attr_id IS NULL,
				                    '',
				                    goods_attr.attr_id),
				                ':',
				                IF(attribute.sort_order IS NULL,
				                    '',
				                    attribute.sort_order))) attr,
				    IF(collect_goods.rec_id > 0, 1, 0) hasFavorites,
				    goods.add_time createAt
				FROM
				    (SELECT
				        GROUP_CONCAT(attr_id, attr_value) querys,
				        goods.goods_id
				    FROM
				        goods
				    LEFT JOIN goods_attr ON goods.goods_id = goods_attr.goods_id
				    GROUP BY goods.goods_id) attr
				        LEFT JOIN
				    goods ON goods.goods_id = attr.goods_id
				        LEFT JOIN
				    collect_goods ON (collect_goods.goods_id = goods.goods_id
				        AND collect_goods.user_id = {$userId})
				        LEFT JOIN
				    brand ON brand.brand_id = goods.brand_id
				        LEFT JOIN
				    goods_attr ON goods_attr.goods_id = goods.goods_id
				        LEFT JOIN
				    attribute ON attribute.attr_id = goods_attr.attr_id
				        LEFT JOIN
				    suppliers ON suppliers.suppliers_id = goods.suppliers_id
				        LEFT JOIN
				    category ON category.code = goods.cat_code
				    {$leftOrderGoods}
				WHERE
				    {$conditions}
				GROUP BY goods.goods_id
				ORDER BY {$orderBy}
				LIMIT {$size}
		";
		$goodsObj = new Goods();
		$result = $goodsObj->getReadConnection()->query($sqlStatement);
		$goods = array();
		$goodsIds = array();
		if(is_object($result) && $result->numRows()) {
			$result->setFetchMode(PDO::FETCH_ASSOC);
			$result  = $result->fetchAll();
			foreach($result as $r) {
				$goodsIds[] = $r['id'];
				if($r['attr']) {
					$sort = array();
					$r['attr'] = array_map(function($a) use (&$sort) {
						$a = explode(':', $a);
						array_shift($a);
						$_a = array_combine(array('name', 'value', 'pid', 'sort'), $a);
						$sort[] = $_a['sort'];
						return $_a;
					}, array_values(explode(',', $r['attr'])));
					array_multisort($sort, SORT_ASC, SORT_NUMERIC, $r['attr']);
					$r['attr'] = array_map(function($v) {
						unset($v['sort']);
						return $v;
					}, $r['attr']);
				} else {
					$r['attr'] = array();
				}
				$goods[] = $r;
			}
			if($forward) {
				$goods = array_reverse($goods);
			}
		}
		foreach ($goods as $k=>$v) {
			$goods[$k]['vipPrice'] = $this->showShopPrice($v);
		}
    
		$nav = $this->getNavigate($code);
		return ResponseApi::send(compact('goods', 'nav'));
    }

    /**
     * 获取标品类的商品列表 JJSC-06
     */
    public function searchAction() {
    	$code = $this->request->get('code', 'int');
    	$areaId = $this->request->get('areaId', 'int');
    	$createAt = $this->request->get('createAt', 'int') ?: time();
    	$forward = $this->request->get('forward', 'int');
    	if(!$code || !$areaId){
    		return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法");
    	}
    	$sortKey = $this->request->get('sortkey');
    	$sort = $this->request->get('sort');
    	if($sortKey) {
    		if(!in_array($sortKey, array('price', 'salesNum', 'storeNum'))) {
    			return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法");
    		}
    		if(in_array($sortKey, array('price', 'storeNum'))) {
	    		$sortKey = 'Goods.'.$sortKey;
    		}
    	}
    	if($sort) {
    		$sort = strtoupper($sort);
    		if(!in_array($sort, array('ASC', 'DESC'))) {
    			return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法");
    		}
    	} else {
    		$sort = 'DESC';
    	}
    	$factoryId = $this->request->get('factoryId');
    	$goodsName = $this->request->get('goodsName');
    	$size = $this->request->get('size', 'int') ?: parent::SIZE;
    	$codeConditions = self::getCodeConditions($code);
		$conditions = '(Goods.isDelete = 0)';
    	if($forward) {
    		$conditions .= ' AND (Goods.createAt > '.$createAt.')';
    		$orderBy = 'Goods.createAt ASC';
    	} else {
    		$conditions .= ' AND (Goods.createAt < '.$createAt.')';
    		$orderBy = 'Goods.createAt DESC';
    	}
    	$conditions .= ' AND (Goods.code>='.$codeConditions['minCode'].' AND Goods.code<='.$codeConditions['maxCode'].') AND '.($areaId != 1 ? '(areaId = '.$areaId.' OR areaId = 1)' : '(1)').'';
    	if($factoryId) {
    		$conditions .= ' AND (Goods.factoryId=' . $factoryId . ')';
    	}
    	if($goodsName) {
    		$conditions .= ' AND (Goods.name LIKE "%'.$goodsName.'%")';
    	}
    	if($sortKey !== 'salesNum' && $sortKey) {
    		$orderBy = $sortKey.' '.$sort.','.$orderBy;
    	}
		$userId = $this->get_user()->id;
    	$builder = $this->modelsManager->createBuilder();
    	$builder->from('Goods');
    	$builder->leftJoin('CollectGoods', 'c.goodsId=Goods.id AND c.userId='.$userId, 'c');
    	$builder->leftJoin('Brand', 'b.id=Goods.factoryId', 'b');
    	if($sortKey == 'salesNum') {
    		$orderBy = 'COUNT(og.goodsId)'.' '.$sort.','.$orderBy;
	    	$builder->leftJoin('OrderGoods', 'og.goodsId=Goods.id', 'og');
    	}
    	$builder->columns("Goods.id,
    			Goods.name,
    			IF(Goods.thumb LIKE 'http://%', Goods.thumb, CONCAT('" . $this->get_url() . "', Goods.thumb)) thumb,
    			Goods.price,
    			Goods.vipPrice,
    			Goods.price_num, 
    			Goods.price_rate, 
    			IF(c.recId>0, 1, 0) hasFavorites,
    			Goods.createAt");
    	$builder->where($conditions);
    	$builder->orderBy($orderBy);
    	$builder->groupBy('Goods.id');
    	$builder->limit($size);
    	$result = $builder->getQuery()->execute();
    	$goods = array();
    	if(is_object($result) && $result->count()) {
    		$goods  = $result->toArray();
    		if($forward) {
    			$goods = array_reverse($goods);
    		}
    	}
    	foreach ($goods as $k=>$v) {
    		$goods[$k]['vipPrice'] = $this->showShopPrice($v);
    	}
    
    	$nav = $this->getNavigate($code);
    	return ResponseApi::send(compact('goods', 'nav'));
    }

    /**
     * 获取商品详情 JJSC-07
     */
    public function detailAction() {
		$goodsId = $this->request->get('id', 'int');
		if(!$goodsId) {
			return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法");
		}
		$userId = $this->get_user()->id;
		$builder = $this->modelsManager->createBuilder();
		$builder->from('Goods');
		$builder->leftJoin('Region', 'r.id=Goods.areaId', 'r');
		$builder->leftJoin('Suppliers', 's.id=Goods.suppliersId', 's');
		$builder->leftJoin('CollectGoods', 'c.goodsId=Goods.id', 'c');
		$builder->leftJoin('GoodsAttr', 'a.goodsId=Goods.id', 'a');
		$builder->leftJoin('Attribute', 'ab.id=a.attrId', 'ab');
		$builder->leftJoin('GoodsGallery', 'p.goodsId=Goods.id', 'p');
		$builder->leftJoin('Category', 'cat.code=Goods.code', 'cat');
		$builder->columns("Goods.id,
				Goods.wcode,
				Goods.name,
				r.name salesArea,
				CONCAT('".$this->get_url()."', Goods.thumb) thumb,
				GROUP_CONCAT(DISTINCT IF(p.thumb LIKE 'http://%', p.thumb, CONCAT('".$this->get_url()."', p.thumb))) thumbs,
				GROUP_CONCAT(DISTINCT IF(p.imgOriginal LIKE 'http://%', p.imgOriginal, CONCAT('".$this->get_url()."', p.imgOriginal))) pics,
				Goods.code,
				s.supplier,
				Goods.shiplocal,
				Goods.storeNum,
				cat.unit,
				Goods.price,
				Goods.vipPrice,
				Goods.price_num, 
				Goods.price_rate, 
				Goods.createAt,
				Goods.des,
				Goods.spec,
				Goods.instr,
				Goods.afterSale,
				IF(c.userId={$userId}, 1, 0) hasFavorites");
		$builder->where('Goods.isDelete = 0');
		$builder->andWhere('Goods.id = ' . $goodsId);
		$builder->groupBy('Goods.id');
		$builder->limit(1);
		$result = $builder->getQuery()->execute();
		$goodsDetail = array();
		if(is_object($result) && $result->count()) {
			$goodsDetail = $result->getFirst()->toArray();
			$goodsDetail['thumbs'] = explode(',', $goodsDetail['thumbs']);
			$goodsDetail['pics'] = explode(',', $goodsDetail['pics']);
// 			if($goodsDetail['spec']) {
// 				$goodsDetail['spec'] = array_values(explode(',', $goodsDetail['spec']));
// 				$sort = array();
// 				$goodsDetail['spec'] = array_map(function($a) use (&$sort) {
// 					$a = explode(':', $a);
// 					array_shift($a);
// 					$_a = array_combine(array('name', 'value', 'pid', 'sort'), $a);
// 					$sort[] = $_a['sort'];
// 					return $_a;
// 				}, $goodsDetail['spec']);
// 				array_multisort($sort, SORT_ASC, SORT_NUMERIC, $goodsDetail['spec']);
// 				$goodsDetail['spec'] = array_map(function($v) {
// 					unset($v['sort']);
// 					return $v;
// 				}, $goodsDetail['spec']);
// 			}
			$goodsDetail['spec'] = parent::replaceImgUrl($goodsDetail['spec']);
			$total = Comment::count(array(
				'conditions' => 'goodsId = :goodsId:',
				'bind' => compact('goodsId'),
			));
			$goodsDetail['total'] = $total;
			extract($goodsDetail);
			$_goodsDetail = compact('id', 'name', 'thumb', 'price', 'vipPrice', 'hasFavorites');
			$this->setHistory($_goodsDetail);
			$nav = $this->getNavigate($goodsDetail['code']);
			unset($goodsDetail['code']);
		}
		$goodsDetail['vipPrice'] = $this->showShopPrice($goodsDetail);
		
		return ResponseApi::send(compact('goodsDetail', 'nav'));
    }

    /**
     * 获取历史浏览数据 JJSC-08
     */
    public function historyListAction() {
    	$size = $this->request->get('size', 'int') ?: self::HISTORY_SZIE;
    	$historyList = array();
		$user = $this->get_user();
		if(is_object($user) && $user) {
			$key = 'his_'.$user->id;
			$historyList = unserialize($this->get_cache()->hGet($key, 'historyList'));
			$historyList = array_reverse(array_slice($historyList, -$size));
		}
		return ResponseApi::send($historyList);
    }

    /**
     *
     * 获取商品评价列表
     * JJSC-09
     */
    public function getCommonListAction() {
        $goodsId = $this->request->get('id', 'int');
        if(!$goodsId) {
            return ResponseApi::send(null, Message::$_ERROR_CODING, "您访问的资源不存在或者已经删除！");
        }
        $createAt = $this->request->get('createAt', 'int') ?: time() + 10;
        $size = $this->request->get('size', 'int') ?: parent::SIZE;
        $forward = $this->request->get('forward', 'int');
        $commentList = array();
        $commentList['content'] = array();
        $commentList['total'] = 0;
        $conditions = 'goodsId = :goodsId:';
        $order = '';
		if($forward) {
			$conditions .= ' AND createAt > :createAt:';
			$order = 'createAt';
		} else {
			$conditions .= ' AND createAt < :createAt:';
			$order = 'createAt DESC';
		}
        $result = Comment::find(array(
        		'conditions' => $conditions,
        		'bind' => compact('goodsId', 'createAt'),
        		'columns' => array('id', 'content', 'createAt', 'userId', 'username'),
        		'order' => $order,
        		'limit' => $size
        ));
        if(is_object($result) && $result->count()) {
        	if($forward) {
        		$commentList['content'] = array_reverse($result->toArray());
        	} else {
        		$commentList['content'] = $result->toArray();
        	}
        	$total = Comment::count(array(
        			'conditions' => 'goodsId = :goodsId:',
        			'bind' => compact('goodsId'),
        	));
        	$commentList['total'] = $total;
        }
        return ResponseApi::send($commentList);
    }

	/**
	 * 发表商品评价JJSC-10
	 */
	public function addCommonAction() {
		if (!$this->request->isPost()) {
			return ResponseApi::send(null, Message::$_ERROR_SYSTEM, "只支持POST请求");
		}
		$goodsId = $this->request->getPost('goodsId', 'int');
		$content = $this->request->getPost('content');
		$l = mb_strlen($content, 'UTF-8');
		if(!$goodsId || $l < 5 || $l > 500) {
			return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法");
		}
		$user = $this->get_user();
		$common = new Comment();
		$common->userId = $user->id;
		$common->username = $user->account;
		$common->content = $content;
		$common->goodsId = $goodsId;
		if(!$common->save()) {
			foreach ($common->getMessages() as $message) {
				return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $message);
			}
		}
		return ResponseApi::send();

	}

	/**
	 * 获取购物车信息 JJSC-11
	 */
	public function getCartListAction() {
		$userId = $this->get_user()->id;
		$builder = $this->modelsManager->createBuilder();
		$builder->from('Cart');
		$builder->leftJoin('GoodsAttr', 'a.goodsId=Cart.goodsId', 'a');
		$builder->leftJoin('Goods', 'g.id=Cart.goodsId', 'g');
		$builder->leftJoin('Attribute', 'ab.id=a.attrId', 'ab');
		$builder->leftJoin('Category', 'c.code=g.code', 'c');
		$builder->where('Cart.userId='.$userId);
		$builder->groupBy('Cart.goodsId');
		$builder->orderBy('Cart.id DESC');
		$builder->columns('
					Cart.goodsId,
					g.wcode,
					Cart.goodsName name,
					IF(g.thumb LIKE "http://%", g.thumb, CONCAT("'.$this->get_url().'", g.thumb)) thumb,
					g.vipPrice price,
					g.price_num, 
					g.price_rate, 
					Cart.nums,
					c.unit,
					GROUP_CONCAT(DISTINCT CONCAT(a.goodsAttrId, ":", ab.name,":",a.attr_value,":",a.attrId,":",ab.sort)) attr');
		$result = $builder->getQuery()->execute();
		$cartList = array();
		if(is_object($result) && $result->count()) {
			$result  = $result->toArray();
			foreach($result as $r) {
				$sort = array();
				if($r['attr']) {
					$r['attr'] = array_map(function($a) use (&$sort) {
						$a = explode(':', $a);
						array_shift($a);
						$_a = array_combine(array('name', 'value', 'pid', 'sort'), $a);
						$sort[] = $_a['sort'];
						return $_a;
					}, explode(',', $r['attr']));
					array_multisort($sort, SORT_ASC, SORT_NUMERIC, $r['attr']);
					$r['attr'] = array_map(function($v) {
						unset($v['sort']);
						return $v;
					}, $r['attr']);
				} else {
					$r['attr'] = array();
				}
				$cartList[] = $r;
			}
		}
		foreach ($cartList as $k=>$v) {
			$cartList[$k]['price'] = $this->showShopPrice($v, 'price');
		}
	
		return ResponseApi::send($cartList);

	}

	/**
	 * 添加商品到购物车
     * JJSC-12
     *
	 */
	public function addCartAction() {
		if (!$this->request->isPost()) {
			return ResponseApi::send(null, Message::$_ERROR_CODING, "只支持POST请求");
		}
		$goodsId = $this->request->getPost('id', 'int');
		$nums = $this->request->getPost('number', 'int');
		if(!$goodsId || !$nums) {
			return ResponseApi::send(null, Message::$_ERROR_CODING, "数据格式错误");
		}
		$result = Goods::findFirst(array(
			'conditions' => 'id=:goodsId: AND isDelete = 0',
			'bind' => compact('goodsId'),
			'columns' => 'name goodsName, vipPrice price, goodsSn, storeNum, price_num, price_rate',
		));
		//添加商品到购物车
		if(is_object($result) && $result) {
			$goods = $result->toArray();
			$goods['price'] = $this->showShopPrice($goods, 'price');
			//库存检查
			if($goods['storeNum'] < $nums) {
				return ResponseApi::send(null, Message::$_ERROR_NOFOUND, "商品库存不足");
			}
			$userId = $this->get_user()->id;
			extract($goods);
			$cart = Cart::findFirst('goodsId=' . $goodsId . ' AND userId = '.$userId);
			if(is_object($cart) && $cart) {
				$cart->nums +=  $nums;
			} else {
				$cart = new Cart();
				foreach(array('nums', 'userId', 'goodsId', 'goodsSn', 'goodsName', 'price') as $v) {
					$cart->$v = $$v;
				}
			}
			if(!$cart->save()) {
				foreach($cart->getMessages() as $message) {
					return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
				}
			}
		} else {
			return ResponseApi::send(null, Message::$_ERROR_NOFOUND, '源请求错误，试图获取已经过期或则并不存在的资源');
		}
		//计算购物车商品总数
		$total = $this->getCartTotal();
		return ResponseApi::send(compact('total'));
	}

	/**
	 * 获取合同列表 JJSC-13
	 */
	public function getContractsAction() {
		$customerId = $this->get_user()->id;
		
		$result = ContractModel::find(array(
			'conditions' => 'customer_id = '.$customerId,
			'columns' => 'contract_id, contract_name name, contract_num code'
		));
		$contract = array();
		if(is_object($result) && $result->count()) {
			$contract = $result->toArray();
		}
		return ResponseApi::send($contract);
	}

	/**
	 * 提交订单JJSC-14
	 */
	public function addOrderAction() {
		if (!$this->request->isPost()) {
			return ResponseApi::send(null, Message::$_ERROR_CODING, "只支持POST请求");
		}
		//发票信息
		$invType = $this->request->getPost('invType');
		$invPayee = $this->request->getPost('invPayee');
		$invAddress = $this->request->getPost('invAddress');
		$invContent = $this->request->getPost('invContent');
		$addressId = $this->request->getPost('addressId', 'int');
		$payOrgcode = $this->request->getPost('payOrgcode');
		$contractSn = $this->request->getPost('contractSn');
		if(!$invPayee || !$invAddress || !$addressId || !$contractSn) {
			return ResponseApi::send(null, Message::$_ERROR_CODING, "数据格式错误");
		}
		$address = UserAddress::findFirst($addressId);
		if(!is_object($address) || !$address) {
			return ResponseApi::send(null, Message::$_ERROR_CODING, "数据格式错误");
		}
		$user = $this->get_user();
		$userId = $user->id;
// 		$cartResults = Cart::find(array(
// 				'conditions' => 'userId = :userId:',
// 				'bind' => compact('userId'),
// 				'columns' => 'id, goodsId, goodsName, goodsSn, price, nums'
// 		));
		$cartResults = Cart::query()
		->leftJoin('Goods', 'G.id = Cart.goodsId', 'G')
		->where('Cart.userId = '. $userId)
		->columns('
				Cart.id,
				Cart.goodsId,
				Cart.goodsName,
				Cart.goodsSn,
				G.vipPrice price, 
				G.price_num, 
				G.price_rate, 
				Cart.nums
				')->execute();
		if(!is_object($cartResults) || !$cartResults->count()) {
			return ResponseApi::send(null, Message::$_ERROR_CODING, "数据格式错误");
		}
		//库存检查和采购额度检查
		$buyGoods = array();
		$totalAmt = 0;
		foreach($cartResults as $cartResult) {
			if ($cartResult->price_num) {
				$cartResult->price = $cartResult->price + $cartResult->price_num;
			} else {
				$cartResult->price = $cartResult->price * (1+($cartResult->price_rate/100));
			}
		
			$buyGoods[$cartResult->goodsId] = $cartResult->nums;
			$totalAmt += $cartResult->nums * $cartResult->price;
		}
		//从金蝶接口得到采购额度
		$api = new ApiController();
		$cmt = $api->getCreAmt();	//得到采购额度 #bug#
		
		$cmt = '1000000';
		
		if($cmt === false) {
			return ResponseApi::send(null, Message::$_ERROR_SYSTEM, "您的订单暂时无法提交！");
		}
		if($cmt < $totalAmt) {
			return ResponseApi::send(null, Message::$_ERROR_CREDITS, "您的采购额度不足，可取消重下单或追加申请额度后下单！");
		}
		//得到所要购买商品的库存
		$ids = implode(', ', array_keys($buyGoods));
		$goodsObjs = Goods::find(array(
			'conditions' => 'id IN ('.$ids.')',
			'columns' => 'id, storeNum'
		));
		if(is_object($goodsObjs) && $goodsObjs->count() == count($buyGoods)) {
			$goodsShort = array();
			foreach($goodsObjs as $goodsObj) {
				if($goodsObj->storeNum < $buyGoods[$goodsObj->id]) {
					$goodsShort[] = array(
						'id' => $goodsObj->id,
						'num' => $buyGoods[$goodsObj->id] - $goodsObj->storeNum
					);
				}
			}
			if($goodsShort) {
				return ResponseApi::send($goodsShort, Message::$_ERROR_NOFOUND, "你的购物车中商品库存不足");
			}
		} else {
			return ResponseApi::send(null, Message::$_ERROR_NOFOUND, '获取商品库存信息出错，请稍后重试！');
		}

		$orderInfo = new OrderInfo();
		//开启事务
		$manager = new TxManager();
		$transaction = $manager->get();
		$orderInfo->setTransaction($transaction);
		$orderInfo->orderSn = $this->getOrderSn();
		$orderInfo->userId = $userId;
		$orderInfo->name = $address->name;
		$orderInfo->phone = $address->phone;
		$orderInfo->address = $address->address;
		$orderInfo->tag = $address->tag;
		//保存发票信息
		$userInv = UserInv::findFirst('userId = ' . $userId);
		if(!is_object($userInv) || !$userInv) {
			$userInv = new UserInv();
			$userInv->userId = $userId;
		}
		$userInv->setTransaction($transaction);
		$userInv->invType = $invType;
		$userInv->invPayee = $invPayee;
		$userInv->invAddress = $invAddress;
		try {
			if(!$userInv->save()) {
				foreach($userInv->getMessages() as $message) {
					return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
				}
			}
		} catch (\Exception $ex) {
			$manager->rollback($transaction);
			return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $ex->getMessage());
		}
		//订单中的发票信息
		$orderInfo->invType = $invType;
		$orderInfo->invPayee = $invPayee;
		$orderInfo->invAddress = $invAddress;
		$orderInfo->invContent = $invContent;
		//银行相关
		$orderInfo->payOrgcode = $payOrgcode;
		//合同相关
		$orderInfo->contractSn = $contractSn;
		//获取订单总价
		$orderAmount = $this->getCartAmount();
		
		$orderInfo->orderAmount = $orderAmount;
		$orderInfo->goodsAmount = $orderAmount;
		$orderInfo->status = -1;
		try {
			if(!$orderInfo->save()) {
				foreach($orderInfo->getMessages() as $message) {
					return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
				}
			}
		} catch (\Exception $ex) {
			$manager->rollback($transaction);
			return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $ex->getMessage());
		}
		//从购物车得到数据
		//签名额外数据
		$submitData['extraData'] = array();
		foreach($cartResults as $cartResult) {
			$orderGoods = new OrderGoods();
			$orderGoods->setTransaction($transaction);
			$orderGoods->orderId = $orderInfo->id;
			$orderGoods->goodsId = $cartResult->goodsId;
			$orderGoods->goodsSn = $cartResult->goodsSn;
			$orderGoods->goodsName = $cartResult->goodsName;
			$orderGoods->goodsPrice = $cartResult->price;
			$orderGoods->nums = $cartResult->nums;
			//签名额外数据
			$submitData['extraData'][] = implode(':', array(/* $orderGoods->goodsName, */ $orderGoods->goodsSn, $orderGoods->goodsPrice));
			try {
				if(!$orderGoods->save()) {
					foreach($orderGoods->getMessages() as $message) {
						return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
					}
				}
			} catch (\Exception $ex) {
				$manager->rollback($transaction);
				return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $ex->getMessage());
			}
			//减少商品库存
			$goods = Goods::findFirst($cartResult->goodsId);
			$goods->setTransaction($transaction);
			$goods->storeNum -= $cartResult->nums;
			try {
				if(!$goods->save()) {
					foreach($goods->getMessages() as $message) {
						return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
					}
				}
			} catch (\Exception $ex) {
				$manager->rollback($transaction);
				return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $ex->getMessage());
			}

			$cartResult = Cart::findFirst($cartResult->id);
			$cartResult->setTransaction($transaction);
			try {
				if(!$cartResult->delete()) {
					foreach($cartResult->getMessages() as $message) {
						return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
					}
				}
			} catch (\Exception $ex) {
				$manager->rollback($transaction);
				return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $ex->getMessage());
			}
		}
		//保存签名数据
		$salerInfo = $this->getSalerInfo();
		$bankSign = new BankSign();
		$bankSign->setTransaction($transaction);
		$bankSign->orderSn = $orderInfo->orderSn;
		$submitData['interfaceName'] = 'CZB_SUBMIT_CONTACT';
		$submitData['interfaceVersion'] = '1_1';
		$submitData['bussinessTime'] = date('YmdHis', $orderInfo->createAt);
		$submitData['merId'] = $salerInfo['mer_id']; //客户号

		$submitData['signVersion'] = 'WRT1.0';
		$submitData['timeTamp'] = date('YmdHis');
		$submitData['contractNo'] = $orderInfo->contractSn;
		$submitData['orderNo'] = $orderInfo->orderSn;
		$submitData['buyerCstno'] = $user->customerNo;
		$submitData['buyerAccno'] = $user->customerAccount;
		$submitData['buyerbookSum'] = 0;
		//中交客户号 客户账号
		//$submitData['salerCstno'] = '201404044';
		//$submitData['salerAccno'] = '3310010010120192110599';

		$submitData['salerCstno'] = $salerInfo['saler_cstno'];
		$submitData['salerAccno'] = $salerInfo['saler_accno'];

		$submitData['salerbookSum'] = 0;
		$submitData['allGoodsMoney'] = sprintf('%0.2f', $orderInfo->orderAmount);
		$submitData['tranID'] = $orderInfo->orderSn;
        $submitData['extraData'] = implode(',', $submitData['extraData']);
		$submitData['creditMode'] = '0';
		$submitData['creditBank'] = 'CZB';

		$bankSign->submitData = serialize($submitData);
		$signRawField = array(
		    'signVersion',
		    'timeTamp',
		    'contractNo',
		    'orderNo',
		    'buyerCstno',
		    'buyerAccno',
		    'buyerbookSum',
		    'salerCstno',
		    'salerAccno',
		    'salerbookSum',
		    'allGoodsMoney',
		    'tranID',
		    'extraData',
		);
		foreach($signRawField as $rawField) {
		    $signData[$rawField] = $submitData[$rawField];
		}
		$bankSign->signData = serialize($signData);

		try {
		    if(!$bankSign->save()) {
		        foreach($bankSign->getMessages() as $message) {
		            return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
		        }
		    }
		} catch (\Exception $ex) {
		    $manager->rollback($transaction);
		    return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $ex->getMessage());
		}
		$manager->commit();
		$orderNo = $orderInfo->orderSn;
		return ResponseApi::send(compact('orderNo'));
	}

	/**
	 * 修改购物车商品数量 JJSC-15
	 */
	public function updateCartAction() {
		if (!$this->request->isPost()) {
			return ResponseApi::send(null, Message::$_ERROR_CODING, "只支持POST请求");
		}
		$goodsId = $this->request->getPost('id', 'int');
		$nums = $this->request->getPost('number', 'int');
		if(!$goodsId) {
			return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式错误");
		}
		$userId = $this->get_user()->id;
		$cartRecord = Cart::findFirst(array(
				'conditions' => 'userId = :userId: AND goodsId = :goodsId:',
				'bind' => compact('userId', 'goodsId')
		));
		if(is_object($cartRecord) && $cartRecord) {
			if($nums) {
				$goodsObj = Goods::findFirst(array(
					'conditions' => 'id = :goodsId:',
					'bind' => compact('goodsId'),
					'columns' => 'storeNum'
				));
				if(is_object($goodsObj) && $goodsObj) {
					if($goodsObj->storeNum < $nums) {
						return ResponseApi::send(
								array(
									'id' => $goodsId,
									'num' => $nums - $goodsObj->storeNum
								),
								Message::$_ERROR_NOFOUND, "当前商品库存不足");
					}
				} else {
					return ResponseApi::send(null, Message::$_ERROR_NOFOUND, '源请求错误，试图获取已经过期或则并不存在的资源');
				}
				$cartRecord->nums = $nums;
				if(!$cartRecord->save()) {
					foreach($cartRecord->getMessages() as $message) {
						return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
					}
				}
			} else {
				// 清空购物车数据
				if(!$cartRecord->delete()) {
					foreach($cartRecord->getMessages() as $message) {
						return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
					}
				}
			}
		} else {
			return ResponseApi::send(null, Message::$_ERROR_NOFOUND, '源请求错误，试图获取已经过期或则并不存在的资源');
		}
		return ResponseApi::send();
	}

	/**
	 * 获取购物车添加的商品数 JJSC-16
	 */
	public function getCartTotalAction() {
		$total = $this->getCartTotal();
		return ResponseApi::send(compact('total'));
	}

	/**
	 * 获取同类推荐商品 JJSC-17
	 */
	public function getSimilarAction() {
		$id = $this->request->get('id', 'int');
		$r = Goods::findFirst(array(
				'conditions' => 'id = :id:',
				'bind' => compact('id'),
				'columns' => 'code, areaId'
		));
		$similarGoods = array();
		if(is_object($r) && $r) {
			$goods = $r->toArray();
			extract($goods);
			$result = Goods::find(array(
					'conditions' => '(code = :code:) AND (IF(:areaId: = 1, 1, areaId = :areaId: OR areaId = 1)) AND (id <> :id:)',
					'bind' => compact('code', 'areaId', 'id'),
					'columns' => 'id, name, IF(thumb LIKE "http://%", thumb, CONCAT("'.$this->get_url().'", thumb)) thumb, price, vipPrice, price_num, price_rate',
					'order' => 'createAt DESC',
					'limit' => 5
			));
			if(is_object($result) && $result) {
				$similarGoods = $result -> toArray();
			}
		}
		foreach ($similarGoods as $k=>$v) {
			$similarGoods[$k]['vipPrice'] = $this->showShopPrice($v);
		}
	
		return ResponseApi::send($similarGoods);
	}

	/**
	 * 商品预览搜索 JJSC-18
	 */
	public function findAction() {
		$name = $this->request->get('key');
		$createAt = $this->request->get('createAt', 'int') ?: time();
		$userId = $this->get_user()->id;
		$goodsBP = $this->findbp($name, $createAt, 8, $userId);
		$goodsDZ = $this->findDZ($name, $createAt, 4, $userId);
		$goods = array();
		$goods['countDZ'] = self::findDZCount($name);
		$goods['dataDZ'] = $goodsDZ;
		$goods['countBP'] = self::findBPCount($name);
		$goods['dataBP'] = $goodsBP;
		return ResponseApi::send($goods);
	}


	/**
	 * 标品类商品搜索 JJSC-19
	 */
	public function findbpAction() {
		$name = $this->request->get('key');
		$createAt = $this->request->get('createAt', 'int') ?: time();
		$size = $this->request->get('size', 'int') ?: parent::SIZE;
		$userId = $this->get_user()->id;
		$goods = $this->findbp($name, $createAt, $size, $userId);
		return ResponseApi::send($goods);
	}

	/**
	 * 大宗类商品搜索 JJSC-20
	 */
	public function findDZAction() {
		$name = $this->request->get('key');
		$createAt = $this->request->get('createAt', 'int') ?: time();
		$size = $this->request->get('size', 'int') ?: parent::SIZE;
		$userId = $this->get_user()->id;
		$goods = $this->findDZ($name, $createAt, $size, $userId);
		return ResponseApi::send($goods);
	}

	/**
	 * 获取加入购物车最新商品 JJSC-21
	 */
	public function getCartLastAction() {
		$userId = $this->get_user()->id;
		$result = Cart::query()
		->where('Cart.userId = :userId:', compact('userId'))
		->join('Goods', 'g.id = Cart.goodsId', 'g', 'LEFT')
		->columns('Cart.goodsId id,
				g.code,
				Cart.goodsName,
				Cart.price,
				Cart.nums,
				CONCAT("'.$this->get_url().'", g.thumb) thumb')
		->orderBy('Cart.id DESC')
		->limit(1)
		->execute();
		$cartLastGoods = array();
		if(is_object($result) && $result->count()) {
			$cartLastGoods = $result->getFirst()->toArray();
			$cartLastGoods['total'] = $this->getCartTotal();
			$cartLastGoods['totalPrice'] = $this->getCartAmount();
		}
		return ResponseApi::send($cartLastGoods);
	}

	/**
	 * 得到当前用户发票信息JJSC-22
	 */
	public function getInvAction() {
		$userId = $this->get_user()->id;
		$result = UserInv::findFirst(array(
				'conditions' => 'userId = :userId:',
				'bind' => compact('userId'),
				'columns' => 'invType, invPayee, invAddress',
		));
		$invInfo = array();
		if(is_object($result) && $result) {
			$invInfo = $result->toArray();
		}
		return ResponseApi::send($invInfo);
	}


	/**
	 * 标品类商品搜索
	 */
	private function findbp($name, $createAt, $size, $userId) {
		$criteria = Goods::query();
		$criteria->leftJoin('CollectGoods', 'c.goodsId=Goods.id AND c.userId=' . $userId, 'c');
		foreach(self::$goodsBPCodes as $k => $goodsBPCode) {
			${'minimum'.$k} = $goodsBPCode;
			${'maximum'.$k} = self::getMaxCode(${'minimum'.$k});
			$criteria->orWhere("Goods.code BETWEEN :minimum{$k}: AND :maximum{$k}:", compact("minimum{$k}", "maximum{$k}"));
		}
		if($name) {
			$criteria->andWhere('Goods.name LIKE :name:', array('name' => '%'.$name.'%'), array(Column::BIND_PARAM_STR));
		}
		$criteria->andWhere('Goods.createAt < ' . $createAt);
		$criteria->columns(array(
				'Goods.id',
				'Goods.name',
				'IF(Goods.thumb LIKE "http://%", Goods.thumb, CONCAT("'.$this->get_url().'", Goods.thumb)) thumb',
				'Goods.price',
				'Goods.vipPrice', 
				'Goods.price_num', 
				'Goods.price_rate', 
				'Goods.createAt',
				'IF(c.recId > 0, 1, 0) hasFavorites'
		));
		$criteria->orderBy('Goods.createAt DESC');
		$criteria->limit($size);
		$result = $criteria->execute();
		$goods = array();
		if(is_object($result) && $result) {
			$goods = $result -> toArray();
		}
		foreach ($goods as $k=>$v) {
			$goods[$k]['vipPrice'] = $this->showShopPrice($v);
		}
	
		return $goods;
	}

	/**
	 * 大宗类商品搜索
	 */
	private function findDZ($name, $createAt, $size, $userId) {
		$builder = $this->modelsManager->createBuilder();
		$builder->from('Goods');
		$builder->leftJoin('CollectGoods', 'c.goodsId=Goods.id AND c.userId='.$userId, 'c');
		$builder->leftJoin('Brand', 'b.id=Goods.factoryId', 'b');
		$builder->leftJoin('GoodsAttr', 'a.goodsId=Goods.id', 'a');
		$builder->leftJoin('Attribute', 'ab.id=a.attrId', 'ab');
		$builder->leftJoin('Suppliers', 's.id=Goods.suppliersId', 's');
		$builder->leftJoin('Category', 'cat.code=Goods.code', 'cat');
		$builder->columns("Goods.id,
				Goods.name,
				Goods.price,
				IF(Goods.thumb LIKE 'http://%', Goods.thumb, CONCAT('" . $this->get_url() . "', Goods.thumb)) thumb,
				Goods.shiplocal,
				Goods.vipPrice,
				Goods.price_num, 
				Goods.price_rate, 
				Goods.storeNum,
				b.factoryName,
				s.supplier,
				cat.unit,
				GROUP_CONCAT(DISTINCT CONCAT(a.goodsAttrId,':',ab.name,':',a.attr_value,':',a.attrId,':',ab.sort)) attr,
				IF(c.recId>0, 1, 0) hasFavorites");

		foreach(self::$goodsDZCodes as $k => $goodsDZCode) {
			${'minimum'.$k} = $goodsDZCode;
			${'maximum'.$k} = self::getMaxCode(${'minimum'.$k});
			$builder->orWhere("Goods.code BETWEEN {${'minimum'.$k}} AND {${'maximum'.$k}}");
		}
		$builder->andWhere('Goods.createAt < ' . $createAt);
		if($name) {
			$builder->andWhere('Goods.name LIKE "%'.$name.'%"');
		}
		$builder->orderBy('Goods.createAt DESC');
		$builder->groupBy('Goods.id');
		$builder->limit($size);
		$result = $builder->getQuery()->execute();
		$goods = array();
		if(is_object($result) && $result->count()) {
			$result  = $result->toArray();
			foreach($result as $r) {
				$goodsIds[] = $r['id'];
				if($r['attr']) {
					$sort = array();
					$r['attr'] = array_map(function($a) use (&$sort) {
						$a = explode(':', $a);
						array_shift($a);
						$_a = array_combine(array('name', 'value', 'pid', 'sort'), $a);
						$sort[] = $_a['sort'];
						return $_a;
					}, array_values(explode(',', $r['attr'])));
					array_multisort($sort, SORT_ASC, SORT_NUMERIC, $r['attr']);
					$r['attr'] = array_map(function($v) {
						unset($v['sort']);
						return $v;
					}, $r['attr']);
				} else {
					$r['attr'] = array();
				}
				$goods[] = $r;
			}
		}
		foreach ($goods as $k=>$v) {
			$goods[$k]['vipPrice'] = $this->showShopPrice($v);
		}
			
		return $goods;
	}

	/**
	 * 满足一定条件标品类商品总数
	 * @param string $name
	 * @return number
	 */
	private function findBPCount($name) {
		$codeConditions = '';
		foreach(self::$goodsBPCodes as $k => $goodsBPCode) {
			${'minimum'.$k} = $goodsBPCode;
			${'maximum'.$k} = self::getMaxCode(${'minimum'.$k});
			$codeConditions .= " OR (Goods.code BETWEEN {${'minimum'.$k}} AND {${'maximum'.$k}})";
		}
		$codeConditions = trim($codeConditions, ' OR ');
		$conditions = '1';
		if($name) {
			$conditions .= ' AND (name LIKE "%'.$name.'%")';
		}
		if($codeConditions) {
			$conditions .= ' AND ('.$codeConditions.')';
		}
		return Goods::count($conditions);
	}

	/**
	 * 满足一定条件大宗类商品总数
	 * @param string $name
	 * @return number
	 */
	private function findDZCount($name) {
		$codeConditions = '';
		foreach(self::$goodsDZCodes as $k => $goodsDZCode) {
			${'minimum'.$k} = $goodsDZCode;
			${'maximum'.$k} = self::getMaxCode(${'minimum'.$k});
			$codeConditions .= " OR (Goods.code BETWEEN {${'minimum'.$k}} AND {${'maximum'.$k}})";
		}
		$codeConditions = trim($codeConditions, ' OR ');
		$conditions = '1';
		if($name) {
			$conditions .= ' AND (name LIKE "%'.$name.'%")';
		}
		if($codeConditions) {
			$conditions .= ' AND ('.$codeConditions.')';
		}
		return Goods::count($conditions);
	}

    /**
     * 保存浏览记录
     */
    private function setHistory($_goodsDetail) {
    	if(!$_goodsDetail) {
    		return false;
    	}
    	$key = 'his_' . $this->get_user()->id;
    	$cache = $this->get_cache();
    	$goodsList = array();
		if($cache->exists($key)) {
			$historyList = $cache->hGet($key, 'historyList');
			$goodsList = unserialize($historyList);
			$flag = true;
			foreach($goodsList as $k => $v) {
				if($_goodsDetail['id'] == $v['id']) {
					$goodsList[$k] = $_goodsDetail;
					$flag = false;
				}
			}
			if($flag) {
				$goodsList[] = $_goodsDetail;
			}
		} else {
			$goodsList[] = $_goodsDetail;
		}
		$historyList = serialize($goodsList);
		$cache->hset($key, 'historyList', $historyList);
    }

    /**
     * 获得导航栏
     * @param unknown $code
     * @return multitype:
     */
    private function getNavigate($code) {
		$ary = str_split($code, 2);

		$codes = '(';
		foreach($ary as $k => $v) {
			if($v == '00') {
				break;
			}
			$codes .= str_pad(substr($code, 0, ($k + 1) * 2), 8, 0) . ',';
		}
		$codes = substr($codes, 0, -1) . ')';
		$result = Category::find(array(
			'conditions' => 'code IN '.$codes,
			'bind' => compact('codes'),
			'bindTypes' => array(Column::BIND_PARAM_STR),
			'columns' => 'code, name',
			'order' => 'code'
		));
		$nav = array();
		if(is_object($result) && $result->count()) {
			$nav = $result->toArray();
		}
		return $nav;
    }

    /**
     * 获得购物车商品数量
     * @return number
     */
    private function getCartTotal() {
    	$userId = $this->get_user()->id;
    	$total = Cart::sum(array(
    			'conditions' => 'userId = :userId:',
    			'bind' => compact('userId'),
    			'column' => 'nums'
    	));
    	return $total;
    }

    /**
     * 计算购物车商品总价
     * @return number
     */
    private function getCartAmount() {
    	$userId = $this->get_user()->id;
    	$result = Cart::query()
	    	->leftJoin('Goods', 'G.id = Cart.goodsId', 'G')
	    	->where('Cart.userId = ' . $userId)
	    	//->columns('IF(SUM(Cart.nums * G.vipPrice) IS NULL , 0, SUM(Cart.nums * G.vipPrice)) amount')
	    	->columns('Cart.nums,G.vipPrice,G.price_num,G.price_rate')
	    	->execute()
	    	//->getFirst()
	    	->toArray();
    	$amount = 0;
    	foreach ($result as $k=>$v) {
    		$amount += $this->showShopPrice($v);
    	}
		return $amount;
    }

    /**
     * 得到新订单号
     * @return  string
     */
    private function getOrderSn() {
    	return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }

    /**
     * 根据较大的分类code值，获取最小分类的code值
     */
    private static function getMaxCode($code) {
		return (floor($code / 10000000) + 1) * 10000000 - 1;
    }

    /**
     * 根据code值取得code搜索范围
     */
    private static function getCodeConditions($code) {
		$codes = str_split($code, 2);
		$minCode = $maxCode = '';
		if($codes[count($codes) - 1] != '00') {
			$minCode = $maxCode = $code;
		} else {
			$minCode = $code;
			foreach($codes as $k => $v) {
				if($v == '00') {
					break;
				}
				$maxCode .= $v;
			}
			$maxCode = str_pad($maxCode, 8, 9);
		}
		return compact('minCode', 'maxCode');
    }

    private function getSalerInfo() {
        $salerInfoObj = ShopConfig::find(array (
            'conditions' => 'code IN ("saler_cstno", "saler_accno", "mer_id")',
            'columns' => 'code, value'
        ));
        if(is_object($salerInfoObj) && $salerInfoObj->count()) {
            foreach($salerInfoObj as $obj) {
                $salerInfo[$obj->code] = $obj->value;
            }
        }
        return $salerInfo;
    }

    
    /**
     * the price after batch price
     * @param array $arr
     * @return number
     */
    private function showShopPrice($arr, $value = 'vipPrice')
    {
    	if ($arr[$value] && $arr['price_num']) {
    		return $arr[$value] + $arr['price_num'];
    	} elseif ($arr[$value]) {
    		return $arr[$value] * (1 + ($arr['price_rate'] / 100));
    	} else {
    		return $arr[$value];
    	}
    }
}

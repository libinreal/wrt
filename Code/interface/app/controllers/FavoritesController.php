<?php

use PhpRudder\Mvc\ControllerBase;
use PhpRudder\Http\ResponseApi;

class FavoritesController extends ControllerBase
{

	/**
	 * Index Action
	 */
	public function indexAction()
	{
		$this->persistent->parameters = null;
	}

	/**
	 * 我的收藏GRZX-1
	 */
	public function getListAction() {
		$userId = $this->get_user()->id;
		$builder = $this->modelsManager->createBuilder();
		$builder->from('CollectGoods');
		$builder->leftJoin('Goods', 'g.id=CollectGoods.goodsId', 'g');
		$builder->leftJoin('GoodsAttr', 'a.goodsId=g.id', 'a');
		$builder->leftJoin('Attribute', 'ab.id=a.attrId', 'ab');
		$builder->where('CollectGoods.userId ='.$userId);
		$builder->groupBy('g.id');
		$builder->columns("g.id, g.name, g.price, g.vipPrice,g.code category,
				IF(g.thumb LIKE 'http://%', g.thumb, CONCAT('" . $this->get_url() . "', g.thumb)) thumb,
				GROUP_CONCAT(CONCAT(ab.name,':',a.attr_value,':',a.attrId,':',ab.sort)) attr");
		$result = $this->modelsManager->executeQuery($builder->getPhql());
		$list = array();
		if(is_object($result) && $result) {
			$result = $result->toArray();
			foreach($result as $r) {
				if($r['attr']) {
					$sort = array();
					$r['attr'] = array_map(function($a) use (&$sort) {
						$_a = array_combine(array('name', 'value', 'pid', 'sort'), explode(':', $a));
						$sort[] = $_a['sort'];
						return $_a;
					}, array_values(array_unique(explode(',', $r['attr']))));
					array_multisort($sort, SORT_ASC, SORT_NUMERIC, $r['attr']);
					$r['attr'] = array_map(function($v) {
						unset($v['sort']);
						return $v;
					}, $r['attr']);
				} else {
					$r['attr'] = array();
				}
				$list[] = $r;
			}
		}
		return ResponseApi::send($list);
	}

	/**
	 * 新增收藏商品GRZX-2
	 */
	public function saveAction() {
		$goodsId = $this->request->get('id', 'int');
		$userId = $this->get_user()->id;
		$this->get_logger()->debug(var_export(array($goodsId, $userId), true));
		$collectGoods = CollectGoods::findFirst(array(
				'conditions' => 'goodsId = :goodsId: AND userId = :userId:',
				'bind' => compact('goodsId', 'userId'),
		));
		if(is_object($collectGoods) && $collectGoods) {
			return ResponseApi::send(null, Message::$_ERROR_LOGIC, '请不要重复收藏');
		}
		$result = Goods::findFirst(array(
				'conditions' => 'id = :goodsId:',
				'bind' => compact('goodsId'),
		));
		if(!is_object($result) || !$result) {
			return ResponseApi::send(null, Message::$_ERROR_NOFOUND, '源请求错误，试图获取已经过期或则并不存在的资源');
		}
		$collectGoods = new CollectGoods();
		$collectGoods->userId = $userId;
		$collectGoods->goodsId = $goodsId;
		$collectGoods->createAt = time();
		if(!$collectGoods->save()) {
			foreach($collectGoods->getMessages() as $message) {
				return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
			}
		}
		return ResponseApi::send();
	}

	/**
	 * 取消收藏商品GRZX-3
	 */
	public function deleteAction() {
		$goodsId = $this->request->get('id');
		$userId = $this->get_user()->id;
		$collectGoods = CollectGoods::findFirst(array(
				'conditions' => 'goodsId = :goodsId: AND userId = :userId:',
				'bind' => compact('goodsId', 'userId'),
		));
		if(is_object($collectGoods) && $collectGoods) {
			if(!$collectGoods->delete()) {
				foreach($collectGoods->getMessages() as $message) {
					return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
				}
			} else {
				return ResponseApi::send();
			}
		} else {
			return ResponseApi::send(null, Message::$_ERROR_NOFOUND, '源请求错误，试图获取已经过期或则并不存在的资源');
		}
	}

}
<?php

use Phalcon\Mvc\Model\Transaction\Manager as TxManager;
use PhpRudder\Mvc\ControllerBase;
use PhpRudder\Http\ResponseApi;

class GiftController extends ControllerBase
{

	/**
	 * Index Action
	 */
    public function indexAction()
    {
    	$this->persistent->parameters = null;
    }

    /**
     * 获得商品分类 JFSC-01
     */
    public function getCategorysAction() {
    	$result = ExchangeCategory::find(array(
    			'columns' => 'id, name',
    			'order' => 'sort'
    	));
    	$cats = array();
    	if(is_object($result) && $result) {
    		$cats = $result->toArray();
    	}
    	return ResponseApi::send($cats);
    }

    /**
     * 获得商品列表 JFSC-02
     */
    public function  getListAction() {
		$categoryId = $this->request->get('categoryId', 'int');
		$updateAt = $this->request->get('updateAt', 'int') ?: time() + 10;
		$size = $this->request->get('size', 'int') ?: parent::SIZE;
        $forward = $this->request->get('forward', 'int');
        $conditions = 'isDelete = 0 AND isOnSale = 1';
		if ($categoryId) {
			$conditions .= " AND categoryId = :categoryId:";
		}
		if($forward) {
			$conditions .= " AND updateAt > :updateAt:";
			$order = 'sort DESC, updateAt';
		} else {
			$conditions .= " AND updateAt < :updateAt:";
			$order = 'sort, updateAt DESC';
		}
		$goodsList = array();
		if($categoryId) {
			$criteria = ExchangeGoods::query();
			$criteria->columns(array (
					'id',
					'name',
					'IF(imageUrl LIKE "http://%", imageUrl, CONCAT("' . $this->get_url() . '", imageUrl)) imageUrl',
					'IF(originalImg LIKE "http://%", originalImg, CONCAT("' . $this->get_url() . '", originalImg)) originalImg',
					'credits',
					'updateAt'
			));
			$criteria->conditions($conditions);
			$criteria->bind(compact('categoryId', 'updateAt'));
			$criteria->orderBy($order);
			$criteria->limit($size);
			$result = $criteria->execute();
			if(is_object($result) && $result->count()) {
				$goodsList = $result->toArray();
				if($forward) {
					$goodsList = array_reverse($goodsList);
				}
			}
		} else {
			$connection = $this->getDI()->get("db");
			$sql = "SELECT * FROM `exchange_category` ORDER BY `sort_order`";
			$result = $connection->fetchAll($sql);
			$goods = array();
			$k = 0;
			foreach($result as $r) {
				$k++;
				$goodsList[$k] = $connection->fetchAll("select g.goods_name name,g.goods_id id,IF(g.goods_img LIKE 'http://%', g.goods_img, CONCAT('" . $this->get_url() . "', g.goods_img)) imageUrl,IF(g.original_img LIKE 'http://%', g.original_img, CONCAT('" . $this->get_url() . "', g.original_img)) originalImg,g.shop_price credits,last_update updateAt from exchange_goods g where is_delete = 0 AND is_on_sale = 1 AND g.cat_id={$r['cat_id']} order by g.sort_order,g.last_update desc limit 15", Phalcon\Db::FETCH_ASSOC);
			}
		}
		return ResponseApi::send($goodsList);
    }

    /**
     * 查看商品详情 JFSC-03
     */
    public function getDetailAction() {
		$id = $this->request->get('id', 'int');
		$result = ExchangeGoods::findFirst(array (
				'columns' => array(
						'id',
						'name',
						'weight',
						'itemNo',
						'credits',
						'IF(imageUrl LIKE "http://%", imageUrl, CONCAT("' . $this->get_url() . '", imageUrl)) imageUrl',
						'IF(originalImg LIKE "http://%", originalImg, CONCAT("' . $this->get_url() . '", originalImg)) originalImg',
						'des',
						'sales',
						'spec'
				),
				'conditions' => "id = :id:",
				'bind' => compact('id')
		));
		if(is_object($result) && $result->count()) {
			$goods = $result->toArray();
			$goods['des'] = parent::replaceImgUrl($goods['des']);
			$goods['sales'] = parent::replaceImgUrl($goods['sales']);
			return ResponseApi::send($goods);
		} else {
			return ResponseApi::send(null, Message::$_ERROR_NOFOUND, "要查看的商品不存在！");
		}
    }

    /**
     * 积分兑换 JFSC-04
     */
    public function exchangeAction() {
    	if (!$this->request->isPost()) {
    		return ResponseApi::send(null, Message::$_ERROR_CODING, "只支持POST请求");
    	}
    	$id = $this->request->get('id', 'int');
    	$addressId = $this->request->get('addressId', 'int');
    	if(!$id || !$addressId) {
    		return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法！");
    	}
    	$user = $this->get_user();
    	$exchangeGoods = ExchangeGoods::findFirst($id);
    	if(!is_object($exchangeGoods) || !$exchangeGoods) {
    		return ResponseApi::send(null, Message::$_ERROR_NOFOUND, "要兑换的商品不存在！");
    	}
    	//判断积分商品库存是否充足
		if($exchangeGoods->storeNum <= 0) {
			return ResponseApi::send(null, Message::$_ERROR_NOFOUND, "要兑换的商品库存不足！");
		}
		if($exchangeGoods->credits > $user->credits) {
			return ResponseApi::send($user->credits, Message::$_ERROR_LOGIC, '您的积分不足以完成本次兑换');
		} else {
			$user = Users::findFirst($user->id);
			//积分订单处理
			//开启事务
			$manager = new TxManager();
			$transaction = $manager->get();
			//积分商品数量减少
			$exchangeGoods->setTransaction($transaction);
			$exchangeGoods->storeNum -= 1;
			try {
				if(!$exchangeGoods->save()) {
					foreach($exchangeGoods->getMessages() as $message) {
						return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
					}
				}
			} catch (\Exception $ex) {
				$manager->rollback($transaction);
				return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $ex->getMessage());
			}
			//扣减积分
			$rem = $user->credits - $exchangeGoods->credits;
			//设置用户积分
			$user->credits = $rem;
			//设置默认收货地址
			$user->addressId = $addressId;
			$user->setTransaction($transaction);
			try {
				if(!$user->save()) {
					foreach($user->getMessages() as $message) {
						return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
					}
				}
			} catch (\Exception $ex) {
				$manager->rollback($transaction);
				return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $ex->getMessage());
			}
			//更新会话中用户信息
			$user = $this->get_user();
			$user->credits = $rem;
			$this->get_session()->set('auth', $user);

			//获取用户地址信息
			$userAddress = UserAddress::findFirst($addressId);
			$exchangeOrderInfo = new ExchangeOrderInfo();
			$exchangeOrderInfo->setTransaction($transaction);
			//订单信息
			$exchangeOrderInfo->orderSn = $this->getOrderSn();
			$exchangeOrderInfo->userId = $user->id;
			$exchangeOrderInfo->orderStatus = 0;
			$exchangeOrderInfo->shippingStatus = 0;
			$exchangeOrderInfo->payStatus = 0;
			$exchangeOrderInfo->name = $userAddress->name;
			$exchangeOrderInfo->address = $userAddress->address;
			$exchangeOrderInfo->phone = $userAddress->phone;
			$exchangeOrderInfo->email = $userAddress->email;
			$exchangeOrderInfo->tag = $userAddress->tag;
			$exchangeOrderInfo->goodsAmount = $exchangeGoods->credits;
			try {
				if(!$exchangeOrderInfo->save()) {
					foreach($exchangeOrderInfo->getMessages() as $message) {
						return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
					}
				}
			} catch (\Exception $ex) {
				$manager->rollback($transaction);
				return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $ex->getMessage());
			}
			//积分订单商品信息
			$exchangeOrderGoods = new ExchangeOrderGoods();
			//开启事务
			$exchangeOrderGoods->setTransaction($transaction);
			$exchangeOrderGoods->orderId = $exchangeOrderInfo->orderId;
			$exchangeOrderGoods->goodsId = $exchangeGoods->id;
			$exchangeOrderGoods->goodsName = $exchangeGoods->name;
			$exchangeOrderGoods->goodsSn = $exchangeGoods->itemNo;
			$exchangeOrderGoods->goodsNum = 1;
			try {
				if(!$exchangeOrderGoods->save()) {
					foreach($exchangeOrderGoods->getMessages() as $message) {
						return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
					}
				}
			} catch (\Exception $ex) {
				$manager->rollback($transaction);
				return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $ex->getMessage());
			}
			$manager->commit();
			return ResponseApi::send(array(
				'credits' => $rem,
				'orderCode' => $exchangeOrderInfo->orderSn,
			));
		}
    }

    /**
     * 得到新订单号
     * @return  string
     */
    private function getOrderSn() {
    	return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }

}


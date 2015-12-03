<?php

use PhpRudder\Mvc\ControllerBase;
use PhpRudder\Http\ResponseApi;

class PersoncenterController extends ControllerBase {

	/**
	 * Index action
	 */
	public function indexAction()
	{
		$this->persistent->parameters = null;
	}

	/**
	 * 查看我的积分兑换订单 GRZX-06
	 */
	public function creditsOrderAction() {
		$createAt = $this->request->get('createAt', 'int');
		$size = $this->request->get('size', 'int');
        $forward = $this->request->get('forward', 'int');

		if(!$size) $size = parent::SIZE;

		$result = $this->_query1($createAt,$size,$forward);
        $orders = array();
        if(is_object($result) && $result) {
            if ($forward && $forward==1) {
                if (count($result->toArray())<$size && count($result->toArray())>=1 ) {
                    $result = $this->_query1(null, $size,0);
                    $orders = $result -> toArray();
                } else{
                    $orders = array_reverse($result -> toArray());
                }
            } else {
                $orders = $result->toArray();
            }
        }
		return ResponseApi::send($orders);
	}

    private function _query1($createAt, $size, $forward) {
        $sort = 'DESC';
        if ($forward && $forward==1) $sort = 'ASC';

        $userId = $this->get_user()->id;
        $criteria = ExchangeOrderInfo::query();
        $criteria -> leftJoin('ExchangeOrderGoods');
        $criteria -> where('ExchangeOrderInfo.userId = :userId:', compact('userId'));
        if($createAt) {
            if ($sort=='DESC') {
                $criteria->andWhere('ExchangeOrderInfo.createAt < ' . $createAt);
            } else {
                $criteria->andWhere('ExchangeOrderInfo.createAt > ' . $createAt);
            }
        } else {
            $criteria->andWhere('ExchangeOrderInfo.createAt < '.time());
        }
        $criteria -> orderBy('ExchangeOrderInfo.createAt '.$sort);
        $criteria -> limit($size);
        $criteria -> columns('ExchangeOrderInfo.goodsAmount credits, ExchangeOrderGoods.goodsId giftId,
				ExchangeOrderGoods.goodsName giftName, ExchangeOrderInfo.orderStatus status, ExchangeOrderInfo.createAt');
        return $criteria -> execute();
    }


    /**
	 * 查看积分获取记录 GRZX-07
	 */
	public function creditsLogAction() {
		$createAt = $this->request->get('createAt', 'int') ?: time();
		$size = $this->request->get('size', 'int') ?: parent::SIZE;
		$forward = $this->request->get('forward', 'int') ?: 0;
		$userId = $this->get_user()->id;
		$conditions = 'userId = :userId:';
		if($forward) {
			$conditions .= ' AND createAt > :createAt:';
			$order = 'createAt';
		} else {
			$conditions .= ' AND createAt < :createAt:';
			$order = 'createAt DESC';
		}
		$result = ExchangeGetRecord::find(array(
			'conditions' => $conditions,
			'bind' => compact('createAt', 'userId'),
			'order' => $order,
			'columns' => 'credits, orderNo, createAt',
			'limit' => $size
		));
		$logs = array();
		if(is_object($result) && $result->count()) {
			$logs = $result -> toArray();
			if($forward) {
				$logs = array_reverse($logs);
			}
		}
		return ResponseApi::send($logs);
	}

}

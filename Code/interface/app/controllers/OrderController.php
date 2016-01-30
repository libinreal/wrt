<?php

use PhpRudder\Mvc\ControllerBase;
use PhpRudder\Http\ResponseApi;

class OrderController extends ControllerBase
{

    public function indexAction()
    {
    	$this->persistent->parameters = null;
    }

    /**
     * 我的订单 DDGL-01
     */
	public function getListAction() {
		$status = $this->request->get('status', 'int');
		$createAt = $this->request->get('createAt', 'int') ?: time();
		$size = $this->request->get('size', 'int') ?: parent::SIZE;
		$forward = $this->request->get('forward', 'int');
		$parentId = $this->request->get('parent_id', 'int');
		$userId = $this->get_user()->id;
		$criteria = OrderInfo::query();
		$criteria->leftJoin('OrderInfo', 'OI.orderSn LIKE CONCAT(OrderInfo.orderSn, "-%")', 'OI');
		$criteria->leftJoin('ContractModel', 'C.contract_num = OrderInfo.contractSn', 'C');
		$criteria->where('OrderInfo.userId = :userId:', compact('userId'));
		if (!$parentId) {
			$criteria->andWhere('OrderInfo.parentOrderId = 0');
		} else {
			$criteria->andWhere('OrderInfo.parentOrderId = '.intval($parentId));
		}
		
		$criteria->notInWhere('OrderInfo.status', array(5, 6));
		if ($status) {
			$criteria->andWhere('OrderInfo.status = :status:' , compact('status'));
		}
		if($forward) {
			$criteria->andWhere('OrderInfo.createAt > :createAt:', compact('createAt'));
			$criteria->orderBy('OrderInfo.createAt ASC');
		} else {
			$criteria->andWhere('OrderInfo.createAt < :createAt:', compact('createAt'));
			$criteria->orderBy('OrderInfo.createAt DESC');
		}
		$criteria->limit($size);
		$criteria->columns('DISTINCT OrderInfo.id,
				OrderInfo.orderSn,
				C.contract_num prjNo,
				C.contract_name prjName,
				OrderInfo.status,
				IF((OI.id > 0 OR OrderInfo.status >=2), 0, 1) allowCancel,
				OrderInfo.createAt');
		$result = $criteria->execute();
		$orderList = array();
		if(is_object($result) && $result->count()) {
			$orderList = $result->toArray();
			if($forward) {
				$orderList = array_reverse($orderList);
			}
		}
		//cancelStatus 0 未取消 1取消中 2取消完成
		foreach($orderList as &$order) {
            $orderSn = $order['orderSn'];
            $sign = BankSign::findFirst('orderSn = "' . $orderSn . '" AND signType = 2');
            if(!is_object($sign) || !$sign) {
                $order['cancelStatus'] = 0;
            } else {
                if(!$sign->signResult) {
                    $order['cancelStatus'] = 1;
                } else {
                    $order['cancelStatus'] = 2;
                }
            }
		}
		return ResponseApi::send($orderList);
	}

	/**
	 * 获取订单详情及状态 DDGL-02
	 */
	public function getDetailAction() {
		$id = $this->request->get('id', 'int');
		if(!$id) {
			return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法");
		}
		$orderDetail = array();
		$builder = $this->modelsManager->createBuilder();
		$builder->from('OrderInfo');
		$builder->leftJoin('ContractModel', 'C.contract_num = OrderInfo.contractSn', 'C');
		$builder->where('OrderInfo.id = :id:', compact('id'));
		$builder->columns('
				OrderInfo.id,
				OrderInfo.orderSn,
				OrderInfo.status orderStatus,
				OrderInfo.createAt doTime,
				OrderInfo.remark,
				C.contract_num prjNo,
				C.contract_name prjName,
				OrderInfo.isRemaind,
				OrderInfo.name payer,
				OrderInfo.address,
				OrderInfo.phone mobile,
				OrderInfo.payOrgcode payOrg,
				OrderInfo.invType,
				OrderInfo.invPayee,
				OrderInfo.invContent');
		$result = $builder->getQuery()->execute()->getFirst();
		if(!is_object($result) || !$result) {
			return ResponseApi::send(null, Message::$_ERROR_LOGIC, "要查看的订单不存在，或者已经被管理员删除！");
		}
		$order = $result->toArray();
		
		$order['payKind'] = 0;

		$sqlStatement = 'SELECT
							order_id
						FROM
							order_info
						WHERE
							parent_order_id = '.$id;
		$orderInfo = new OrderInfo();
		$result = $orderInfo->getReadConnection()->query($sqlStatement);
		$result->setFetchMode(PDO::FETCH_ASSOC);
		$goodsList = array();
		if(!$result->numRows()) {
			$sqlStatement = 'SELECT
				order_goods.goods_id goodsId,
			    order_goods.goods_sn goodsCode,
			    order_goods.goods_name goodsName,
			    goods.goods_thumb thumb,
				goods.price_num, 
				goods.price_rate, 
			    category.measure_unit goodsUnit,
			    order_goods.goods_number orderNums,
			    order_goods.goods_price changePrice,
			    IF(1, 0, 0) AS contractNums,
			    IF(1, 0, 0) AS checkNums,
			    IF(1, 0, 0) AS contractPrice,
			    IF(1, 0, 0) AS checkPrice
			FROM
			    order_goods
			        LEFT JOIN
			    goods ON goods.goods_id = order_goods.goods_id
			        LEFT JOIN
			    category ON category.code = goods.cat_code
			WHERE
			    order_goods.order_id = '.$id;
			$orderGoods = new OrderGoods();
			$result = $orderGoods->getReadConnection()->query($sqlStatement);
			$result->setFetchMode(PDO::FETCH_ASSOC);
			$result = $result->fetchAll();
			$ordersum = $contractsum = $checksum = 0;
			foreach ($result as $key => $r) {
				$id = $r['goodsId'];
				$goodsAttr = new GoodsAttr();
				$sqlStatement = 'SELECT
								    attribute.attr_name name,
								    goods_attr.attr_value value,
								    goods_attr_id pid
								FROM
								    goods_attr
								        LEFT JOIN
								    attribute ON attribute.attr_id = goods_attr.attr_id
								WHERE
								    goods_attr.goods_id = '.$id.'
								ORDER BY attribute.sort_order';
				$result = $goodsAttr->getReadConnection()->query($sqlStatement);
				$result->setFetchMode(PDO::FETCH_ASSOC);
				$attr = $result->fetchAll();
				$r['attr'] = $attr;
				if(!preg_match('/^http:\/\//', $r['thumb'])) {
					$r['thumb'] = $this->get_url() . $r['thumb'];
				}
				$ordersum += $r['orderNums'] * $r['changePrice'];
				$contractsum += $r["contractNums"] * $r['contractPrice'];
				$checksum += $r['checkNums'] * $r['checkPrice'];
				$goodsList[] = $r;
			}
			$orderDetail = array_merge($orderDetail, $order, compact('goodsList', 'ordersum', 'contractsum', 'checksum'));
		} else {
			$sqlStatement = 'SELECT
					order_goods.goods_id goodsId,
				    order_goods.goods_sn goodsCode,
				    order_goods.goods_name goodsName,
				    goods.goods_thumb thumb,
					goods.price_num, 
					goods.price_rate, 
				    category.measure_unit goodsUnit,
				    order_goods.goods_number orderNums,
				    order_goods.goods_price changePrice
				FROM
					order_info
				LEFT JOIN order_goods ON order_goods.order_id = order_info.order_id
				LEFT JOIN goods ON goods.goods_id = order_goods.goods_id
				LEFT JOIN category ON category.code = goods.cat_code
				WHERE
					order_info.order_id = ' . $id;
			$orderResult = $orderInfo->getReadConnection()->fetchAll($sqlStatement, PDO::FETCH_ASSOC);
			$sqlStatementSub = 'SELECT
					*
				FROM
					order_info
				LEFT JOIN order_goods ON order_goods.order_id = order_info.order_id
				WHERE
					order_info.parent_order_id = ' . $id;
			$subResult = $orderInfo->getReadConnection()->fetchAll($sqlStatementSub, PDO::FETCH_ASSOC);
			$ordersum = $contractsum = $checksum = 0;
			foreach($orderResult as $orderR) {
				if(!preg_match('/^http:\/\//', $orderR['thumb'])) {
					$orderR['thumb'] = $this->get_url() . $orderR['thumb'];
				}
				$goodsId = $orderR['goodsId'];
				$goodsAttr = new GoodsAttr();
				$sqlStatement = 'SELECT
								    attribute.attr_name name,
								    goods_attr.attr_value value,
								    goods_attr_id pid
								FROM
								    goods_attr
								        LEFT JOIN
								    attribute ON attribute.attr_id = goods_attr.attr_id
								WHERE
								    goods_attr.goods_id = '.$goodsId.'
								ORDER BY attribute.sort_order';
				$result = $goodsAttr->getReadConnection()->query($sqlStatement);
				$result->setFetchMode(PDO::FETCH_ASSOC);
				$attr = $result->fetchAll();
				$orderR['attr'] = $attr;
				$contractNums = $checkNums = $contractTotal = $checkTotal = 0;
				$orderR['orderNums'] = 0;
				array_map(function($subR) use ($goodsId, &$contractsum, &$checksum, &$contractNums, &$checksum, &$contractTotal, &$checkTotal, &$orderR) {
					$contractsum += $subR['contract_number'] * $subR['contract_price'];
					$checksum += $subR['check_number'] * $subR['check_price'];
					if($subR['goods_id'] != $goodsId) {
						return;
					}
					$contractNums += $subR['contract_number'];
					$checksum += $subR['check_number'];
					$contractTotal += $subR['contract_number'] * $subR['contract_price'];
					$checkTotal += $subR['check_number'] * $subR['check_price'];
					if($subR['goods_id'] == $orderR['goodsId']) {
						$orderR['orderNums'] += $subR['check_number'];
					}
				}, $subResult);
				$contractPrice = $contractNums ? $contractTotal / $contractNums : 0;
				$checkPrice = $checkNums ? $checkTotal / $checkNums : 0;
				$goodsList[] = array_merge($orderR, compact('contractNums', 'checkNums', 'contractPrice', 'checkPrice'));
				// $ordersum += $orderR['orderNums'] * $orderR['changePrice']; //TODO 20150923 对账总价
			}
			$ordersum = 0;
			//获取所有子订单
			$subOrder = OrderInfo::find(array(
					'columns' => 'id',
					'conditions' => 'parentOrderId = '.$id,
			))->toArray();
			$tmpArr = array_map('current', $subOrder);
			$ordersum = OrderGoods::sum(array(
					'column' => 'checkPrice * checkNums',
					'conditions' => 'orderId in ('.implode(', ', $tmpArr).')',
			));
			$orderDetail = array_merge($orderDetail, $order, compact('goodsList', 'ordersum', 'contractsum', 'checksum'));
		}
        $orderSn = $orderDetail['orderSn'];
        $sign = BankSign::findFirst('orderSn = "' . $orderSn . '" AND signType = 2');
        if(!is_object($sign) || !$sign) {
            $orderDetail['cancelStatus'] = 0;
        } else {
            if(!$sign->signResult) {
                $orderDetail['cancelStatus'] = 1;
            } else {
                $orderDetail['cancelStatus'] = 2;
            }
        }
        $orderDetail['isAllCheck'] = 0;
        $subOrder = OrderInfo::find('orderSn LIKE "' . $orderSn . '_%"');
        if(is_object($subOrder) && $subOrder->count()) {
            foreach($subOrder as $_subOrder) {
                $subOrderStatus[] = $_subOrder->status;
            }
            if(count(array_unique($subOrderStatus)) == 1) {
                $orderDetail['isAllCheck'] = 1;
            }
        }
        foreach ($orderDetail['goodsList'] as $k=>$v) {
        	$orderDetail['goodsList'][$k]['changePrice'] = $this->showShopPrice($v, 'changePrice');
        }
        
		return ResponseApi::send($orderDetail);
	}

	/**
	 * 查看订单批次详情DDGL-03
	 */
	public function getBatchsAction() {
		$orderSn = $this->request->get('orderSn', 'int');
		$type = $this->request->get('type', 'int');
		$goodsId = $this->request->get('goodsId', 'int');
		$typeStatus = array(1 => '1', 2 => '3');
		if(!$orderSn || !in_array($type, array(1, 2))) {
			return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法");
		}
		$goodsId = ($type == 1) ? null : $goodsId; //修补参数漏洞
		//TODO 修改父订单状态
		if($type == 2) {
			$api = new ApiController();
			$order = OrderInfo::findFirst('orderSn = "' . $orderSn .'"');
			if((is_object($order)) && $order->status != 3) {
    			$api->uStatus($orderSn, 4);
			}
		}
		$builder = $this->modelsManager->createBuilder();
		$builder->from('OrderInfo');
		$builder->leftJoin('OrderGoods', 'OG.orderId = OrderInfo.id', 'OG');
		$builder->leftJoin('ContractModel', 'C.contract_num = OrderInfo.contractSn', 'C');
		$builder->leftJoin('Goods', 'G.id = OG.goodsId', 'G');
		$builder->leftJoin('Category', 'CAT.code=G.code', 'CAT');
		$builder->leftJoin('GoodsAttr', 'A.goodsId=G.id', 'A');
		$builder->leftJoin('Attribute', 'AB.id=A.attrId', 'AB');
		if($goodsId) {
			$builder->where('OG.goodsId = '.$goodsId);
		}
		$builder->andWhere('OrderInfo.orderSn LIKE "' . $orderSn . '-%" AND OrderInfo.status >= ' . $typeStatus[$type]);
		$builder->columns('
				OrderInfo.id,
				OrderInfo.orderSn,
				C.contract_num prjNo,
				C.contract_name prjName,
				G.goodsSn goodsCode,
				CONCAT("'.$this->get_url().'", G.thumb) thumb,
				G.name goodsName,
				CAT.unit goodsUnit,
				IF('.$type.' = 1, OG.contractPrice, OG.checkPrice) price,
				IF('.$type.' = 1, OG.contractNums, OG.checkNums) nums,
				OrderInfo.vtime,
				OrderInfo.status,
				GROUP_CONCAT(DISTINCT CONCAT(
							IF(AB.name IS NULL, "", AB.name), ":",
							IF(A.attr_value IS NULL, "", A.attr_value), ":",
							IF(A.attrId IS NULL, "", A.attrId), ":",
							IF(AB.sort IS NULL, "", AB.sort))) spec
				');
		$builder->groupBy('OG.id');
		$batchs = $orderBatchs = array();
		$result = $builder->getQuery()->execute();
		if(is_object($result) && $result->count()) {
			$orderBatchs = $result->toArray();
			$batchs['id'] = $orderBatchs[0]['id'];
			$batchs['orderSn'] = preg_replace('/^(\d+)\-\d*$/', "$1", $orderBatchs[0]['orderSn']);
			$batchs['prjNo'] = $orderBatchs[0]['prjNo'];
			$batchs['prjName'] = $orderBatchs[0]['prjName'];
			$numsTotal = $sumTotal = $unnumsTotal = $unsumTotal = 0;
 			$orderBatchs = array_map(function($order) use ($type, $typeStatus, &$numsTotal, &$sumTotal, &$unnumsTotal, &$unsumTotal) {
				$spec = $sort = array();
				$spec = array_map(function($goodsAttr) use(&$sort) {
					if(!$goodsAttr || $goodsAttr == ':::') {
						return array();
					}
					$spec = array_combine(array('name', 'value', 'pid', 'sort'), explode(':', $goodsAttr));
					$sort[] = $spec['sort'];
					return $spec;
				}, array_values(explode(',', $order['spec'])));
				if($sort) {
					array_multisort($sort, SORT_ASC, SORT_NUMERIC, $spec);
					unset($order['spec']);
					$order['attr'] = $spec;
				} else {
					$order['attr'] = array();
				}
				unset($order['prjNo'], $order['prjName']);
				//统计数据 1>=2
				//2>=4
				$countStatus = array(1 => 2, 2 => 4);
				$countNotStatus = array(1 => 1, 2 => 3);
				if($order['status'] >= $countStatus[$type]) {
					$numsTotal += $order['nums'];
					$sumTotal += $order['nums'] * $order['price'];
				} else if($order['status'] == $countNotStatus[$type]) {
					$unnumsTotal += $order['nums'];
					$unsumTotal += $order['nums'] * $order['price'];
				}
				return $order;
			}, $orderBatchs);
			$batchs['numsTotal'] = $numsTotal;
			$batchs['sumTotal'] = $sumTotal;
			$batchs['unnumsTotal'] = $unnumsTotal;
			$batchs['unsumTotal'] = $unsumTotal;
			$batchs['orders'] = $orderBatchs;
		}
		return ResponseApi::send($batchs);
	}

	/**
	 * 查看批次订单的物流详情DDGL-04
	 */
	public function getLogisticsAction() {
		$id = $this->request->get('id', 'int');
		if(!$id) {
			return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法");
		}
		$builder = $this->modelsManager->createBuilder();
		$builder->from('OrderInfo');
		$builder->leftJoin('OrderInfo', 'OI.id = OrderInfo.parentOrderId', 'OI');
		$builder->leftJoin('OrderGoods', 'OG.orderId = OrderInfo.id', 'OG');
		$builder->leftJoin('Contract', 'C.contract_num = OrderInfo.contractSn', 'C');
		$builder->leftJoin('Goods', 'G.id = OG.goodsId', 'G');
		$builder->where('OrderInfo.id = ' . $id);
		$builder->columns('OrderInfo.id,
				OrderInfo.orderSn,
				OrderInfo.status orderStatus,
				OrderInfo.createAt doTime,
				OrderInfo.remark,
				C.contract_num prjNo,
				C.contract_name prjName,
				OI.isRemaind,
				OI.id parentOrderId,
				OrderInfo.name payer,
				OrderInfo.address,
				OrderInfo.phone mobile,

				OrderInfo.payOrgcode payOrg,
				OrderInfo.invType,
				OrderInfo.invPayee,
				OrderInfo.invContent
				');
		$result = $builder->getQuery()->execute();
		/**
		 * payKind
		 * lastEvent
		 * proclist = array(createAt
							event
							whodo
							)
		 */
		$logistics = array();
		if(is_object($result) && $result->count()) {
			$logistics = $result->getFirst()->toArray();
			$logistics['payKind'] = 11111;
			$logistics['lastEvent'] = '对应事件数组最后一条数据';
			$logistics['proclist'][] = array(
				'createAt' => 1411014182,
				'event' => '发货',
				'whodo' => '系统'
			);
			$logistics['proclist'][] = array(
				'createAt' => 1411014182,
				'event' => '发货',
				'whodo' => '客户'
			);
		}
		return ResponseApi::send($logistics);

	}


	/**
	 * 变更订单状态 DDGL-05
	 */
	public function uStatusAction() {
		$id = $this->request->get('id', 'int');
		$status = $this->request->get('status', 'int');
		if(!$id || !$status) {
			return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法");
		}
		$criteria = OrderInfo::query();
		$criteria->leftJoin('OrderInfo', '(OI.orderSn LIKE CONCAT(OrderInfo.orderSn, "-%") OR OI.id = OrderInfo.id)', 'OI');
		$criteria->leftJoin('OrderGoods', 'OG.orderId = OI.id', 'OG');
		$criteria->where('OrderInfo.id = :id:', compact('id'));
		$criteria->columns('
				OI.id,
				OG.nums,
				OI.status
				');
		$result = $criteria->execute();
		$nums = $subNums = 0;
		$subOrder = array();
		if(is_object($result) && $result->count()) {
			foreach($result as $r) {
				if($r->id == $id) {
					$nums += $r->nums;
				} else {
					$subNums += $r->nums;
					$subOrder[] = $r->status;
				}
			}
		}
		if($nums != $subNums && $subNums) {
			return ResponseApi::send(null, Message::$_ERROR_SYSTEM, '不能更改订单状态');
		}
		$flag = 0;
		$order = OrderInfo::findFirst($id);
		$parentStatus = $order->status;
		if(!$subOrder) {
			if($status != 5 || $parentStatus >= 5 || ($parentStatus != 0 && $parentStatus != -1)) {
				$flag = 1;
			}
		} else {
	        array_map(function($subStatus) use (&$flag, $status, $parentStatus) {
	        	switch ($status) {
	        		case 2 :
						if($subStatus < 2) {
							$flag = 1;
						}
	        			break;
	        		case 3:
						if($subStatus < 4) {
							$flag = 1;
						}
	        			break;
	        		case 4 :
	        			if($subStatus < 4 || $parentStatus != 3) {
							$flag = 1;
	        			}
	        			break;
	        		case 5 :
						if($subStatus > 0) {
							$flag = 1;
						}
	        			break;
	        	}
	        }, $subOrder);
		}

		if($flag) {
			return ResponseApi::send(null, Message::$_ERROR_SYSTEM, '不能更改订单状态');
		}
		if($order->status >= $status) {
			return ResponseApi::send(null, Message::$_ERROR_SYSTEM, '不能更改订单状态');
		}
		$order->status = $status;
		if(!$order->save()) {
			foreach($order->getMessages() as $message) {
				return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
			}
		}
		return ResponseApi::send();
	}


	/**
	 * 变更订单状态 DDGL-05
	 */
	public function uStatus($id, $status) {
		$criteria = OrderInfo::query();
		$criteria->leftJoin('OrderInfo', 'IF(instr(OI.orderSn, "-"), substring(OI.orderSn, 1, instr(OI.orderSn, "-") - 1), OI.orderSn) =
				IF(instr(OrderInfo.orderSn, "-"),substring(OrderInfo.orderSn,1 , instr(OrderInfo.orderSn, "-")-1),OrderInfo.orderSn)',
    			'OI');
		$criteria->leftJoin('OrderGoods', 'OG.orderId = OI.id', 'OG');
		$criteria->where('OrderInfo.id = :id:', compact('id'));
		$criteria->columns('
				OI.id,
				OG.nums,
				OI.status
				');
		$result = $criteria->execute();
		$nums = $subNums = 0;
		$subOrder = array();
		if(is_object($result) && $result->count()) {
			foreach($result as $r) {
				if($r->id == $id) {
					$nums += $r->nums;
				} else {
					$subNums += $r->nums;
					$subOrder[] = $r->status;
				}
			}
		}
		if($nums != $subNums && $subNums) {
			return false;
		}
		$flag = 0;
		$order = OrderInfo::findFirst($id);
		$parentStatus = $order->status;
		if(!$subOrder) {
			if($status != 5 || $parentStatus >= 5 || $parentStatus != 0) {
				$flag = 1;
			}
		} else {
			array_map(function($subStatus) use (&$flag, $status, $parentStatus) {
				switch ($status) {
					case 2 :
						if($subStatus < 2) {
							$flag = 1;
						}
						break;
					case 3:
						if($subStatus < 4) {
							$flag = 1;
						}
						break;
					case 4 :
						if($subStatus < 4 || $parentStatus != 3) {
							$flag = 1;
						}
						break;
					case 5 :
						if($subStatus > 0) {
							$flag = 1;
						}
						break;
				}
			}, $subOrder);
		}

		if($flag) {
			return false;
		}
		if($order->status >= $status) {
			return false;
		}
		$order->status = $status;
		if(!$order->save()) {
			foreach($order->getMessage() as $message) {
				return false;
			}
		}
		return true;
	}

	/**
	 * 支付 DDGL-06
	 */
	public function payAction() {
		$id = $this->request->get('id', 'int');
		if(!$id) {
			return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法");
		}
		$order = OrderInfo::findFirst($id);
		if(!is_object($order) || !$order->count()) {
			return ResponseApi::send(null, Message::$_ERROR_NOFOUND, "订单不存在，请重试");
		}
		//合同验签与提交接口

		$erpFnum = $order->erpSn;
		$b2bFnum = $order->orderSn;
		$this->dispatcher->forward(array(
				'controller' => 'api',
				'action' => 'sendplyorder',
				'params' => compact('erpFnum', 'b2bFnum')
		));
	}

	/**
	 * 订单验收 DDGL-07
	 */
	public function checkAction() {
		$id = $this->request->get('id', 'int');
		if(!$id) {
			return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法");
		}
		$order = OrderInfo::findFirst($id);
		if(!is_object($order) || !$order->count()) {
			return ResponseApi::send(null, Message::$_ERROR_NOFOUND, "订单不存在，请重试");
		}
		$orderId = $order->id;
		$arrGoods = OrderGoods::find(array(
			'conditions' => 'orderId = '.$orderId,
			'columns' => 'checkPrice, checkNums'
		));
		foreach($arrGoods as $goods) {
			if(!($goods->checkPrice * $goods->checkNums)) {
				return ResponseApi::send(null, Message::$_ERROR_NOFOUND, "您的订单还没有到货，请到货后确认。");
			}
		}
		$erpFnum = $order->erpSn;
		$b2bFnum = $order->orderSn;
		$this->dispatcher->forward(array(
				'controller' => 'api',
				'action' => 'sendcheckorder',
				'params' => compact('erpFnum', 'b2bFnum')
		));
	}

	/**
	 * 催办订单 DDGL-08
	 */
	public function requestProcAction() {
		$id = $this->request->get('id', 'int');
		if(!$id) {
			return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法");
		}
		$order = OrderInfo::findFirst($id);
		if(!is_object($order) || !$order) {
			return ResponseApi::send(null, Message::$_ERROR_NOFOUND, "订单不存在");
		}
		if($order->isRemaind == 1) {
			return ResponseApi::send(null, Message::$_ERROR_NOFOUND, "您已经催办过了，不能重复催办！");
		}
		$order->isRemaind = 1;
		if(!$order->save()) {
			foreach($order->getMessages() as $message) {
				return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
			}
		}
		return ResponseApi::send();
	}

	/**
	 * 客户验签（发货 :1 -> 2 收货 5 -> 6）
	 * @date 2016/01/30
	 * @param string $value [description]
	 */
	public function uChildStatusAction()
	{
		$order = $this->cMyOrder();
		if( !empty( $order ) ){
			if( $order->childOrderStatus == SOS_CONFIRMED ){
				$order->childOrderStatus = SOS_SEND_CC;//客户已验签(发货)
			} else if( $order->childOrderStatus == SOS_SEND_PC2 ){
				$order->childOrderStatus = SOS_ARR_CC;//客户已验签(到货)
			} else {
				return ResponseApi::send(null, Message::$_ERROR_LOGIC, "此订单当前不能验签");
			}
			
			if(!$order->save()) {
				foreach($order->getMessages() as $message) {
					return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
				}
			}
			return ResponseApi::send();

		}else{
			return ResponseApi::send(null, Message::$_ERROR_NOFOUND, "订单不存在");
		}
	}

	/**
	 * 验证是否是我的订单
	 * @date 2016/01/30 
	 * @return 
	 */
	private function cMyOrder()
	{
		$order_id = $this->request->get('oid', 'int');
		$user_id = $this->get_user()->id;

		$order = OrderInfo::findFirst( array( 
					'id = ?1 AND userId = ?2',
					'bind' => array(
							1 => $order_id,
							2 => $user_id
							),
					//'columns' => 'id, userId, childOrderStatus'
						) );
		if( $order ){
			
			$tArr = array('invFax', 'invTel', 'invBankName', 'invBankAccount', 'invBankAddress');
			foreach( $tArr as $p ){
				if( empty( $order->$p ) )
					$order->$p = ' ';
			}
			return $order;
		} else {
			return array();
		}

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


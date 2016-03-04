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
		
		//搜索条件
		$orderSn = $this->request->get('order_sn');
		$contractName = $this->request->get('contract_name');
		$orderStatus = $this->request->get('order_status');
		
		//获取客户名称
		$users = Users::findFirst(array(
				'conditions' => 'id='.$userId, 
				'columns' => 'companyName'
		))->toArray();
		$companyName = $users['companyName'];
		
		//查询订单
		$criteria = OrderInfo::query();
		$criteria->leftJoin('OrderInfo', 'OI.orderSn LIKE CONCAT(OrderInfo.orderSn, "-%")', 'OI');
		$criteria->leftJoin('ContractModel', 'C.num = OrderInfo.contractSn', 'C');
		$criteria->where('OrderInfo.userId = :userId:', compact('userId'));
		$criteria->andWhere('C.type=1'); //销售合同
		$criteria->andWhere('OrderInfo.parentOrderId=0');
		/* if (!$parentId) {
			$criteria->andWhere('OrderInfo.parentOrderId = 0');
		} else {
			$criteria->andWhere('OrderInfo.parentOrderId = '.intval($parentId));
		}
		
		$criteria->notInWhere('OrderInfo.status', array(5, 6));
		if ($status) {
			$criteria->andWhere('OrderInfo.status = :status:' , compact('status'));
		}
		 */
		if($forward) {
			$criteria->andWhere('OrderInfo.createAt > :createAt:', compact('createAt'));
			$criteria->orderBy('OrderInfo.createAt ASC');
		} else {
			$criteria->andWhere('OrderInfo.createAt < :createAt:', compact('createAt'));
			$criteria->orderBy('OrderInfo.createAt DESC');
		}
		
		//搜索
		if ($orderSn) {
			$criteria->andWhere('OrderInfo.orderSn LIKE "%'.$orderSn.'%"');
		}
		if ($contractName) {
			$criteria->andWhere('C.name LIKE "%'.$contractName.'%"');
		}
		if ($orderStatus >= 0 && isset($orderStatus)) {
			$criteria->andWhere('OrderInfo.status = '.$orderStatus);
		}
		
		$criteria->limit($size);
		$criteria->columns('DISTINCT OrderInfo.id,
				OrderInfo.orderSn,
				C.num prjNo,
				C.name prjName,
				OrderInfo.status,
				OrderInfo.orderAmount,
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
            $order['company_name'] = $companyName;
		}
		
		return ResponseApi::send($orderList);
	}

	
	
	/**
	 * 订单详情
	 */
	public function getinfoAction() {
		$orderId = $this->request->get('order_id');
		$orderId = intval($orderId);
		if (!isset($orderId) || !$orderId) {
			return ResponseApi::send(null, -1, 'doesnot get `order_id`');
		}
		
		//订单信息
		$orderinfo = OrderInfo::query();
		$orderinfo->leftjoin('Users', 'U.id=OrderInfo.userId', 'U');
		$orderinfo->leftjoin('ContractModel', 'C.num=OrderInfo.contractSn', 'C');
		$orderinfo->where('OrderInfo.id='.$orderId);
		$orderinfo->columns('
				OrderInfo.id, 
				OrderInfo.orderSn, 
				OrderInfo.status, 
				OrderInfo.createAt, 
				OrderInfo.contractSn, 
				OrderInfo.userId, 
				U.id, 
				U.account,
				U.companyName, 
				C.id contid, 
				C.name contName, 
				C.num contNum, 
				OrderInfo.invType, 
				OrderInfo.invPayee, 
				OrderInfo.invContent, 
				OrderInfo.name consignee, 
				OrderInfo.phone, 
				OrderInfo.address, 
				OrderInfo.tag
			');
		$result = $orderinfo->execute();
		$result = $result->toArray();
		$info = $result[0];
		
		//订单下的子订单
		$orders = OrderInfo::find(array(
				'conditions' => 'parentOrderId='.$orderId, 
				'columns' => 'id'
		))->toArray();
		foreach ($orders as &$order) {
			$order = $order['id'];
		}
		$orders[] = $orderId;
		
		//订单商品
		$goods = OrderGoods::find(array(
				'conditions' => 'orderId IN('.implode(',', $orders).')', 
				'columns' => 'orderId,goodsId,goodsName,nums,goodsPrice'
		))->toArray();
		$hostGoods = array();
		$lineGoods = array();
		foreach ($goods as $k=>$v) {
			if ($v['orderId'] == $orderId) {
				$hostGoods[] = $v;
			} else {
				$lineGoods[] = $v;
			}
		}
		
		//拆单数量
		$goodsId = array();
		foreach ($hostGoods as $k=>$v) {
			foreach ($lineGoods as $lk=>$lv) {
				if ($lv['goodsId'] == $v['goodsId']) {
					@$hostGoods[$k]['dnums'] += $lv['nums'];
				} else {
					@$hostGoods[$k]['dnums'] = 0;
				}
			}
			$hostGoods[$k]['totalPrice'] = $v['nums']*$v['goodsPrice']; //价格小计
			$goodsId[] = $v['goodsId'];
		}
		
		//商品供应商信息
		$goodsObj = Goods::query();
		$goodsObj->leftjoin('suppliers', 'S.id=Goods.id', 'S');
		$goodsObj->where('Goods.id IN('.implode(',', $goodsId).')');
		$goodsObj->columns('
				Goods.id, 
				Goods.cat_id, 
				S.supplier
			');
		$result = $goodsObj->execute();
		$suppliers = $result->toArray();
		$cateId = array();
		$i = 0;
		foreach ($hostGoods as $k=>$v) {
			foreach ($suppliers as $sk=>$sv) {
				if ($sv['id'] == $v['goodsId']) {
					$hostGoods[$k]['suppliers'] = $sv['supplier'];
					$hostGoods[$k]['cat_id'] = $sv['cat_id'];
					$cateId[$i] = $sv['cat_id'];
					$i++;
				}
			}
		}
		
		//获取商品属性值
		$attrValue = GoodsAttr::find(array(
				'conditions' => 'goodsId IN('.implode(',', $goodsId).')', 
				'columns' => 'goodsId,attr_value'
		))->toArray();
		$attributes = array();
		$attrString = '';
		foreach ($attrValue as $v) {
			$attrString .= $v['attr_value'].'/';
			$attributes[$v['goodsId']] = substr($attrString, 0, -1);
		}
		
		//获取物料代码
		$category = Category::find(array(
				'conditions' => 'id IN('.implode(',', $cateId).')',
				'columns' => 'id,code'
		))->toArray();
		
		
		
		foreach ($hostGoods as $k=>$v) {
			//属性
			foreach ($attributes as $sk=>$sv) {
				if ($sk == $v['goodsId']) {
					$hostGoods[$k]['attributes'] = $sv;
				}
			}
			
			//物料代码
			foreach ($category as $ck=>$cv) {
				if ($cv['id'] == $v['cat_id']) {
					$hostGoods[$k]['code'] = $cv['code'];
				}
			}
		}
		
		$info['goods'] = $hostGoods;
		return ResponseApi::send($info);
	}
	
	
	/**
	 * 子订单列表
	 */
	public function childrenlistAction() {
		$createAt = $this->request->get('createAt', 'int') ?: time();
		$size = $this->request->get('size', 'int') ?: parent::SIZE;
		$forward = $this->request->get('forward', 'int');
		
		$orderId = $this->request->get('order_id');
		$orderId = intval($orderId);
		if (!isset($orderId) || !$orderId) {
			return ResponseApi::send(null, -1, 'doesnot get `order_id`');
		}
		
		//子订单查询
		$orders = OrderInfo::query();
		$orders->leftjoin('OrderGoods', 'OG.orderId=OrderInfo.id', 'OG');
		$orders->leftjoin('Goods', 'G.id=OG.goodsId', 'G');
		$orders->leftjoin('Category', 'C.id=G.cat_id', 'C');
		$orders->leftjoin('ContractModel', 'CM.num=OrderInfo.contractSn', 'CM');
		$orders->where('OrderInfo.parentOrderId='.$orderId);
		
		if($forward) {
			$orders->andWhere('OrderInfo.createAt > :createAt:', compact('createAt'));
			$orders->orderBy('OrderInfo.createAt ASC');
		} else {
			$orders->andWhere('OrderInfo.createAt < :createAt:', compact('createAt'));
			$orders->orderBy('OrderInfo.createAt DESC');
		}
		
		$orders->limit($size);
		$orders->columns('
				OrderInfo.id, 
				OrderInfo.createAt, 
				OrderInfo.orderSn, 
				OrderInfo.status, 
				OrderInfo.contractSn, 
				OG.goodsPrice, 
				OG.nums, 
				G.id goodsId, 
				G.name, 
				C.name cat_name, 
				CM.name contract_name
			');
		$result = $orders->execute();
		$info = $result->toArray();
		if (!$info) return ResponseApi::send(array());
		
		$goodsId = array();
		foreach ($info as $v) {
			if ($v['goodsId']) 
				$goodsId[] = $v['goodsId'];
		}
		$goodsId = array_unique($goodsId);
		if (!$goodsId) return ResponseApi::send(null, -1, '查询失败');
		
		//商品属性
		$attributes = GoodsAttr::query();
		$attributes->where('goodsId IN('.implode(',', $goodsId).')');
		$attributes->columns('
				goodsId, 
				attr_value
			');
		$result = $attributes->execute();
		$attrValue = $result->toArray();
		
		$attributes = array();
		$attrString = '';
		foreach ($attrValue as $v) {
			$attrString .= $v['attr_value'].'/';
			$attributes[$v['goodsId']] = substr($attrString, 0, -1);
		}
		
		foreach ($info as $k=>$v) {
			foreach ($attributes as $sk=>$sv) {
				if ($sk == $v['goodsId']) {
					$info[$k]['attributes'] = $sv;
				}
			}
			$info[$k]['createAt'] = date('Y/m/d', $v['createAt']);
		}
		return ResponseApi::send($info);
	}
	
	
	
	/**
	 * 子订单详情
	 */
	public function childreninfoAction() {
		$childrenId = $this->request->get('order_id');
		$childrenId = intval($childrenId);
		if (!isset($childrenId) || !$childrenId) {
			return ResponseApi::send(null, -1, 'doesnot get `order_id`');
		}
		
		//查询订单详情
		$order = OrderInfo::query();
		$order->leftjoin('ContractModel', 'CM.num=OrderInfo.contractSn', 'CM');
		$order->leftjoin('Suppliers', 'S.id=OrderInfo.suppersId', 'S');
		$order->leftjoin('Users', 'U.id=OrderInfo.userId', 'U');
		$order->leftjoin('OrderGoods', 'OG.orderId=OrderInfo.id', 'OG');
		$order->leftjoin('Goods', 'G.id=OG.goodsId', 'G');
		$order->leftjoin('OrderInfo', 'I.id=OrderInfo.parentOrderId', 'I');
		$order->where('OrderInfo.id='.$childrenId);
		$order->columns('
				OrderInfo.orderSn, 
				OrderInfo.childOrderStatus, 
				FROM_UNIXTIME(OrderInfo.createAt, "%Y/%m/%d") createAt, 
				OrderInfo.invType, 
				OrderInfo.invPayee, 
				OrderInfo.invContent, 
				OrderInfo.name consignee, 
				OrderInfo.address, 
				OrderInfo.phone, 
				OrderInfo.tag, 
				OrderInfo.shippingInfo, 
				OrderInfo.shippingLog, 
				OrderInfo.financialSendRate, 
				OrderInfo.financialArrRate, 
				OrderInfo.shippingFeeSendBuyer, 
				OrderInfo.shippingFeeArrBuyer, 
				OrderInfo.orderAmountSendBuyer, 
				OrderInfo.orderAmountArrBuyer, 
				CM.num contract_num, 
				CM.name contract_name, 
				S.supplier, 
				U.account, 
				OG.goodsId, 
				OG.goodsNumberSendBuyer, 
				OG.goodsNumberArrBuyer, 
				OG.goodsPriceSendBuyer, 
				OG.goodsPriceArrBuyer, 
				G.name goods_name, 
				I.id parentId, 
				I.status parentStatus
			');
		$result = $order->execute();
		$result = $result->toArray();
		$info = $result[0];
		
		$attributes = GoodsAttr::find(array(
				'conditions' => 'goodsId='.$info['goodsId'], 
				'columns' => 'goodsId,attr_value'
		))->toArray();
		$attrValue = '';
		foreach ($attributes as $v) {
			$attrValue .= $v['attr_value'].'/';
		}
		$attrValue = substr($attrValue, 0, -1);
		$info['attributes'] = $attrValue;
		
		$info['shippingInfo'] = json_decode(urldecode($info['shippingInfo']));
		$info['shippingLog'] = json_decode(urldecode($info['shippingLog']));
		
		return ResponseApi::send($info);
	}
	
	
	
	/**
	 * 获取订单详情及状态 DDGL-02
	 */
	public function getDetailAction() {
		$id = $this->request->get('id', 'int');
		if(!$id) {
			return ResponseApi::send(null, -1, 'doesnot get `id`');
		}
		$orderDetail = array();
		$builder = $this->modelsManager->createBuilder();
		$builder->from('OrderInfo');
		$builder->leftJoin('ContractModel', 'C.num = OrderInfo.contractSn', 'C');
		$builder->where('OrderInfo.id = :id:', compact('id'));
		$builder->columns('
				OrderInfo.id,
				OrderInfo.orderSn,
				OrderInfo.status orderStatus,
				OrderInfo.createAt doTime,
				OrderInfo.remark,
				C.num prjNo,
				C.name prjName,
				OrderInfo.isRemaind,
				OrderInfo.name payer,
				OrderInfo.address,
				OrderInfo.phone mobile,
				OrderInfo.payOrgcode payOrg,
				OrderInfo.invType,
				OrderInfo.invPayee,
				OrderInfo.orderAmount,
				OrderInfo.orderAmountSendBuyer,
				OrderInfo.orderAmountArrBuyer,
				OrderInfo.childOrderStatus,
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
		if(!$result->numRows()) {//这是子订单 或未拆分的订单
			$sqlStatement = 'SELECT
				order_goods.goods_id goodsId,
			    order_goods.goods_sn goodsCode,
			    order_goods.goods_name goodsName,
			    goods.goods_thumb thumb,
				goods.price_num, 
				goods.price_rate, 
			    category.measure_unit goodsUnit,
			    order_goods.goods_number_send_buyer orderNums,
			    order_goods.goods_number_arr_buyer orderNumsArr,
			    order_goods.goods_price_send_buyer changePrice,
			    order_goods.goods_price_arr_buyer changePriceArr,
			    order_goods.contract_number contractNums,
			    order_goods.contract_price contractPrice,
			    IFNULL(order_goods.check_number, 0) checkNums,
			    IFNULL(order_goods.check_price, 0) checkPrice
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

				//libin 2016-02-04
				if( preg_match( '/-/', $order['orderSn'] ) ){//物料单价
					if( $order['childOrderStatus'] >= SOS_SEND_PP ){
						$r['changePrice'] = $r['changePriceArr'];
						$r['orderNums'] = $r['orderNumsArr'];
					}
				}

				$r['checkPrice'] = $r['contractPrice'];//验收单价 = 原始价格
				
				$contractsum += $r["contractNums"] * $r['contractPrice'];
				$checksum += $r['checkNums'] * $r['checkPrice'];


				$goodsList[] = $r;
			}

			//libin 2016-02-04
			if( preg_match( '/-/', $order['orderSn'] ) ){
				if( $order['childOrderStatus'] < SOS_SEND_PP )
					$ordersum = $order['orderAmountSendBuyer'];
				else
					$ordersum = $order['orderAmountArrBuyer'];
			} else {
				$ordersum = $order['orderAmount'];
			}
			$orderDetail = array_merge($orderDetail, $order, compact('goodsList', 'ordersum', 'contractsum', 'checksum'));
		} else {//主订单
			//order_goods.goods_price_add 改价后的单价  order_goods.goods_price 供应商报价\
			
			$sqlStatement = 'SELECT
					order_goods.goods_id goodsId,
				    order_goods.goods_sn goodsCode,
				    order_goods.goods_name goodsName,
				    goods.goods_thumb thumb,
					goods.price_num, 
					goods.price_rate, 
				    category.measure_unit goodsUnit,
				    order_goods.goods_number orderNums,
				    order_goods.contract_price changePrice
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

			foreach($orderResult as $orderK=>$orderR) {
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
				$contractNums = $checkNums = $orderR['orderNums'];
				$contractTotal = $checkTotal = $contractsum = $checksum = $orderR['orderNums'] * $orderR['changePrice'];
				// $orderR['orderNums'] = 0;
				
				$contractPrice = $contractNums ? $contractTotal / $contractNums : 0;
				$checkPrice = $checkNums ? $checkTotal / $checkNums : 0;
				$goodsList[] = array_merge($orderR, compact('contractNums', 'checkNums', 'contractPrice', 'checkPrice'));
			}
			$ordersum = $contractsum;
			//获取所有子订单
			$subOrder = OrderInfo::find(array(
					'columns' => 'id',
					'conditions' => 'parentOrderId = '.$id,
			))->toArray();
			$tmpArr = array_map('current', $subOrder);

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
		$builder->leftJoin('ContractModel', 'C.num = OrderInfo.contractSn', 'C');
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
				C.num prjNo,
				C.name prjName,
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
		$builder->leftJoin('Contract', 'C.num = OrderInfo.contractSn', 'C');
		$builder->leftJoin('Goods', 'G.id = OG.goodsId', 'G');
		$builder->where('OrderInfo.id = ' . $id);
		$builder->columns('OrderInfo.id,
				OrderInfo.orderSn,
				OrderInfo.status orderStatus,
				OrderInfo.createAt doTime,
				OrderInfo.remark,
				C.num prjNo,
				C.name prjName,
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
				OG.goodsId,
				OI.status,
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
			$orders = $result->toArray();
		}else{
			return false;
		}
		if($nums != $subNums && $subNums) {
			return ResponseApi::send(null, Message::$_ERROR_SYSTEM, '不能更改订单状态');
		}
		$flag = 0;
		$order = OrderInfo::findFirst($id);
		$parentStatus = $order->status;
		if( count($orders) == 1 ){
			if($status != POS_CANCEL || $parentStatus >= POS_CANCEL || $parentStatus != POS_SUBMIT) {
				$flag = 1;
			}
		} else {

			array_map(function($subStatus) use (&$flag, $status, $parentStatus) {
				switch ($status) {
					case POS_CHECK :
						if($subStatus < SOS_SEND_CC) {
							$flag = 1;
						}
						break;
					case POS_BALANCE:
						if($subStatus < SOS_ARR_PC2) {
							$flag = 1;
						}
						break;
					case POS_COMPLETE :
						if($subStatus < SOS_ARR_PC2 || $parentStatus != POS_BALANCE) {
							$flag = 1;
						}
						break;
					case POS_CANCEL :
						if($subStatus > POS_SUBMIT) {
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

		/** @var tArr [必填字段] */
		$tArr = array('invFax', 'invTel', 'invBankName', 'invBankAccount', 'invBankAddress');
		foreach( $tArr as $p ){
			if( empty( $order->$p ) )
				$order->$p = ' ';
		}

		if(!$order->save()) {
			foreach($order->getMessages() as $message) {
				return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
			}
		}

		//库存回滚
		foreach ($orders as $o) {

			$goods = Goods::findFirst( $o['goodsId'] );
			if( $goods ){
				$goods->storeNum += $o['nums'];

				if(!$goods->save()) {
					foreach($goods->getMessage() as $message) {
						return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
					}

				}
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
				OG.goodsId,
				OI.status,
				OI.childOrderStatus
				');
		//需要判断是否有子订单
		//。。。
		
		$result = $criteria->execute();
		$nums = $subNums = 0;
		$subOrder = array();
		//结果是否为空
		if(is_object($result) && $result->count()) {
			foreach($result as $r) {
				if($r->id == $id) {
					$nums += $r->nums;
				} else {
					$subNums += $r->nums;
					$subOrder[] = $r->childOrderStatus;
				}
			}
		}else{
			return false;
		}

		$flag = 0;
		$order = OrderInfo::findFirst($id);
		$parentStatus = $order->status;
		
		if(empty($subOrder)){
			if($status != POS_CANCEL || $parentStatus >= POS_CANCEL || $parentStatus != POS_SUBMIT) {
				$flag = 1;
			}else{
				//库存回滚
				$goods = Goods::findFirst( $result->goodsId );
				if( $goods ){
					$goods->storeNum += $result->nums;

					if(!$goods->save()) {
						foreach($goods->getMessage() as $message) {
							return false;
						}

					}
				}
			}
		} else {
			array_map(function($subStatus) use (&$flag, $status, $parentStatus) {
				switch ($status) {
					case POS_CHECK :
						if($subStatus < SOS_SEND_CC) {
							$flag = 1;
						}
						break;
					case POS_BALANCE:
						if($subStatus < SOS_ARR_PC2) {
							$flag = 1;
						}
						break;
					case POS_COMPLETE :
						if($subStatus < SOS_ARR_PC2 || $parentStatus != POS_BALANCE) {
							$flag = 1;
						}
						break;
					case POS_CANCEL :
						if($subStatus > POS_SUBMIT) {
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

		/** @var tArr [必填字段] */
		$tArr = array('invFax', 'invTel', 'invBankName', 'invBankAccount', 'invBankAddress');
		foreach( $tArr as $p ){
			if( empty( $order->$p ) )
				$order->$p = ' ';
		}

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
			$user = $this->get_user();
			$userId = $user->id;

			$contract_info = ContractModel::findFirst( array( 
					'num = ?1 AND userId = ?2',
					'bind' => array(
							1 => $order->contractSn,
							2 => $userId
							)
					
						) );
			if( !$contract_info ){
				return ResponseApi::send(null, Message::$_ERROR_SYSTEM, "合同不存在！");
			}

			if( $order->childOrderStatus < SOS_ARR_CC ){//发货阶段
				$totalAmt = $order->orderAmountSendBuyer;//改价后的总金额（商品总价+金融+物流）
			}else{//到货阶段 
				$totalAmt = $order->orderAmountArrBuyer;//改价后的总金额（商品总价+金融+物流）
			}

			if( $order->childOrderStatus == SOS_CONFIRMED ){
				//发货阶段扣除订单关联的合同额度
				if( $contract_info->cashValid >= $totalAmt ){
					$contract_info->cashValid -= $totalAmt;

					$order->cashUsed = $totalAmt;//订单使用的现金总额

				}else{
					if( $contract_info->cashValid + $contract_info->billValid > $totalAmt ){//现金+票据>总金额，优先扣除现金额度
						$bill_red = $totalAmt - $contract_info->cashValid;
						$contract_info->cashValid = 0;
						$contract_info->billValid -= $bill_red;

						$order->cashUsed = $contract_info->cashValid;//订单使用的现金总额
						$order->billUsed = $bill_red;//订单使用的票据总额

					}else{//现金为负，优先扣除票据，让现金额度为负
						$cash_red = $totalAmt - $contract_info->billValid;
						$contract_info->billValid = 0;
						$contract_info->cashValid -= $cash_red;

						//现金额度小于-10000
						if( $contract_info->cashValid < -10000 ){
							return ResponseApi::send(null, Message::$_ERROR_LOGIC, "合同额度超过负1W无法验签");
						}

						$order->cashUsed = $cash_red;//订单使用的现金总额
						$order->billUsed = $contract_info->billValid;//订单使用的票据总额
					}
				}


				if(!$contract_info->save()) {
					foreach($contract_info->getMessages() as $message) {
						return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
					}
				}

				$order->childOrderStatus = SOS_SEND_CC;//客户已验签(发货)
			} else if( $order->childOrderStatus == SOS_SEND_PC2 ){

				//合同额度 多退少补
				//。。。。
				
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
		return $arr[$value];
		if ($arr[$value] && $arr['price_num']) {
			return $arr[$value] + $arr['price_num'];
		} elseif ($arr[$value]) {
			return $arr[$value] * (1 + ($arr['price_rate'] / 100));
		} else {
			return $arr[$value];
		}
	}
}


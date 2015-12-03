<?php

use PhpRudder\Mvc\ControllerBase;
use PhpRudder\Http\ResponseApi;
use PhpRudder\Http\RequestUtil;
use Phalcon\Db\Column as Column;

class ApiController extends ControllerBase{

    const BASE_URL = "http://115.236.64.221/WebHttp/operation.ashx";

	/**
	 * 得到采购额度接口数据
	 */
	public function getPurchaseAction(){
		$customerNo = $this->get_user()->customNo;
		$url = self::BASE_URL;
		$ch = curl_init();
		$timeout = 5;
		$param = '{"name":"administrator","pwd":"888888","type":"b2bToErp_buyAmt_sel","json":{"cusFnum":"' . $customerNo . '","conFnum":"String"}}';
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array (
				'name' => $param
		));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$response = curl_exec($ch);
		curl_close($ch);
		if ($response) {
			$response = json_decode($response, true);
			if ($response['isSuccess'] == 0) {
				unset($response['resInfo'], $response['isSuccess']);
				$response = json_encode($response);
                $this->get_logger()->debug($response);
				$this->get_cache()->_redis->set("API_CACHE_CGED_" . $customerNo, json_encode($response));
				$response = json_decode($response, true);
				$poDetails = array();
				$poDetails = $response['PoDetail'];
				$conFnums = array ();
				foreach($poDetails as $k => $poDetail) {
					unset($poDetails[$k]);
					$conFnums[] = $poDetail['conFnum'];
					$poDetails[$poDetail['conFnum']] = $poDetail;
				}
				$conFnum = '("' . implode('", "', $conFnums) . '")';
				$result = Contract::find(array (
						'conditions' => 'conFnum IN ' . $conFnum,
						'columns' => 'conFnum, Banks, name conName'
				));
				$banks = array();
				if (is_object($result) && $result->count()) {
					$contract = $result->toArray();
					foreach ($contract as $k => $v) {
						unset($contract[$k]);
						$contract[$v['conFnum']] = unserialize($v['Banks']);
						$poDetails[$v['conFnum']] = array_merge($poDetails[$v['conFnum']], array('conName' => $v['conName']));
					}
				}
				$poDetails = array_map(function ($poDetail) use($contract) {
					$poDetail['banks'] = $contract[$poDetail['conFnum']];
					return $poDetail;
				}, $poDetails);
				return ResponseApi::send(array_values($poDetails));
			} else if ($response['isSuccess'] == 1) {
				$response_fail = array (
						'fail' => true,
						'info' => $response['resInfo']
				);
				$response_fail = json_encode($response_fail);
				return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $response_fail);
			}
		} else {
			return ResponseApi::send($this->get_cache()->_redis->get("API_CACHE_CGED_".$customerNo));
        }
    }

    /**
     * 得到采购额度接口数据
     */
    public function getPurchase() {
        $customerNo = $this->get_user()->customNo;
        $url = self::BASE_URL;
        $ch = curl_init();
        $timeout = 5;
		$param = '{"name":"administrator","pwd":"888888","type":"b2bToErp_buyAmt_sel","json":{"cusFnum":"' . $customerNo . '","conFnum":"String"}}';
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('name' => $param));
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $response = curl_exec($ch);
        curl_close($ch);
        if ($response) {
            $response = json_decode($response, true);
            if($response['isSuccess'] === 0){
                unset($response['resInfo'], $response['isSuccess']);
                $lastTotalAmt = 0;
                array_map(function($v) use (&$lastTotalAmt) {
					$lastTotalAmt += $v['lastAmt'];
                }, $response['PoDetail']);
                return $lastTotalAmt;
            }elseif($response['isSuccess'] == 1){
                return 0;
            }
        } else {
            if ($this->get_cache()->_redis->get("API_CACHE_CGED_".$customerNo)) {
                return $this->get_cache()->_redis->get("API_CACHE_CGED_".$customerNo);
            } else {
                return 0;
            }
        }
    }

    /**
     * 得到信用币接口
     * @return integer;
     */
    public function getCreAmtAction() {
        $customerNo = $this->get_user()->customNo;
        $url = self::BASE_URL;
        $ch = curl_init();
        $timeout = 10;
        $param = '{"name": "administrator", "pwd": "888888", "type": "b2bToErp_creAmt_sel", "json":{"cusFnum":"'.$customerNo.'"}}';
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_POST, 1);
        curl_setopt ($ch, CURLOPT_POSTFIELDS, http_build_query(array('name' => $param)));
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $response = curl_exec($ch);
        curl_close($ch);
        if ($response) {
        	$user = $this->get_user();
       		$response = json_decode($response, true);
            $this->get_logger()->debug("我的信用数据".$response);
        	if($response['isSuccess'] == 0) {
        		$creAmtTot = $spendAmtTot = $lastAmtTot = $restoreAmtTot =  0;
        		array_map(function($v) use(&$creAmtTot, &$spendAmtTot, &$lastAmtTot) {
        			$creAmtTot += $v['creAmt'];
        			$spendAmtTot += $v['spendAmt'];
        			$lastAmtTot += $v['lastAmt'];
        		}, $response['Fdata']);

				$response['creAmtTot'] = $creAmtTot;
				$response['spendAmtTot'] = $spendAmtTot;
				$response['lastAmtTot'] = $lastAmtTot;
				$response['restoreAmtTot'] = $restoreAmtTot;
				$response['banks'] = $response['Fdata'];
				$response['banks'] = array_map(function($bank) {
					$bank['strDate'] = preg_replace('/(\d{4})(\d{2})(\d{2})/', '$1\\\$2\\\$3', $bank['strDate']);
					$bank['endDate'] = preg_replace('/(\d{4})(\d{2})(\d{2})/', '$1\\\$2\\\$3', $bank['endDate']);
					return $bank;
				}, $response['banks']);

        		$response['cusLevel'] = $user->creditLevel;
        		$response['cusName'] = $user->companyName;
                //查询历史恢复信用总额。填充历史恢复额度字段内容。
                $sqlstatement = "select sum(chanAmt) chanAmt from credit_chang_log log where log.chanKind =1 and cusFnum = ".$customerNo;
                $db = $this->get_db();
                $result = $db->query($sqlstatement)->fetch();
                if($result && is_array($result)) {
                    $response['restoreAmtTot'] = $result['chanAmt'];
                } else {
                    $response['restoreAmtTot'] = 0;
                }
        		unset($response['resInfo'], $response['isSuccess'], $response['Fdata']);
        		$response = json_encode($response);
        		$this->get_cache()->_redis->set("API_CACHE_XYED_".$customerNo, json_encode($response));
                $this->get_logger()->debug(var_export(json_decode($response, true), true));
        		return ResponseApi::send(json_decode($response, true));
        	}else if($response['isSuccess'] == 1) {
        		$response = array();
        		$fieldsMap = array(
        			'creAmtTot' => '',
        			'spendAmtTot' => '',
        			'lastAmtTot' => '',
        			'restoreAmtTot' => '',
        			'banks' => array(),
        			'cusFnum' => 'customNo',
        			'cusLevel' => 'creditLevel',
        			'cusName' => '',
        		);
        		foreach($fieldsMap as $k => $v) {
					$response[$k] = $v ? $user->$v : $v;
        		}
                return ResponseApi::send($response);
        	}
        } else {
            return ResponseApi::send($this->get_cache()->_redis->get("API_CACHE_XYED_".$customerNo));
        }
    }

    /**
     * 得到信用额度
     */
    public function getCreAmt() {
        $customerNo = $this->get_user()->customNo;
        $url = self::BASE_URL;
        $ch = curl_init();
        $timeout = 10;
        $param = '{"name": "administrator", "pwd": "888888", "type": "b2bToErp_creAmt_sel", "json":{"cusFnum":"'.$customerNo.'"}}';
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_POST, 1);
        curl_setopt ($ch, CURLOPT_POSTFIELDS, array('name' => $param));
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $response = curl_exec($ch);
        curl_close($ch);
        $creAmtTot = 0;
        if ($response) {
            $response = json_decode($response, true);
            if(json_last_error() !== JSON_ERROR_NONE) {
            	return false;
            }
            array_map(function($v) use(&$creAmtTot) {
                $creAmtTot += $v['creAmt'];
            }, $response['Fdata']);
            if($response['isSuccess'] === 0) {
                return $creAmtTot;
            }else if($response['isSuccess'] == 1){
                return 0;
            }
        } else {
            if ($this->get_cache()->_redis->get("API_CACHE_XYED_".$customerNo)) {
                return ResponseApi::send($this->get_cache()->_redis->get("API_CACHE_XYED_".$customerNo));
            } else {
                return 0;
            }
        }
    }

    /**
     * 写信用流水
     *
     * @return array
     */
    public function creRec_saveAction() {
    	if (!$this->request->isPost()) {
    		return ResponseApi::send(null, Message::$_ERROR_CODING, "只支持POST请求");
    	}
		$datas = $this->request->getPost('data');
        $this->get_logger()->debug("$datas");
		$datas = json_decode($datas, true);
		if(!$datas) {
			return ResponseApi::send(null, Message::$_ERROR_SYSTEM, json_last_error());
		}
		$db = $this->get_db();
		$db->begin();
		$keys = '(`'. implode('`, `', array_keys($datas[0])) . '`)';
		$values = '';
		foreach($datas as $k => $v) {
			$values .= ",('".implode("', '", array_values($v))."')";
		}
		$values = substr($values, 1);
		if(!$db->execute("DELETE FROM credit_chang_log")) {
			$db->rollback();
			return ResponseApi::send(null, Message::$_ERROR_SYSTEM, "保存数据失败");
		}
		if(!$db->execute("ALTER TABLE credit_chang_log AUTO_INCREMENT = 1")) {
			$db->rollback();
			return ResponseApi::send(null, Message::$_ERROR_SYSTEM, "保存数据失败");
		}
		$sql = "INSERT INTO credit_chang_log {$keys} VALUES {$values}";
		if(!$db->execute($sql)) {
			$db->rollback();
			return ResponseApi::send(null, Message::$_ERROR_SYSTEM, "保存数据失败");
		}
		$db->commit();
		return ResponseApi::send();
    }

    /**
     * 推送票据接口（票据数据需要进入到数据库）
     *
     */
    public function sendbillAction() {
    	if (!$this->request->isPost()) {
    		return ResponseApi::send(null, Message::$_ERROR_CODING, "只支持POST请求");
    	}
    	$data = $this->request->getPost('data');
    	$notice = json_decode($data, true);
        $this->get_logger()->debug($notice);
    	if(!is_array($notice) || !$notice) {
    		return ResponseApi::send(null, Message::$_ERROR_LOGIC, json_last_error());
    	}
    	$notice['billStrDate'] = strtotime($notice['billStrDate']);
    	$notice['billEndDate'] = strtotime($notice['billEndDate']);
    	extract($notice);
    	$billNotice = new BillNotice();
		foreach(array_keys($notice) as $key) {
			$billNotice->$key = $$key;
		}
		if(!$billNotice->save()) {
			foreach($billNotice->getMessages() as $message) {
				return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
			}
		}
		return ResponseApi::send();
    }

    /**
     * 同步合同的接口（合同需要保存到数据库）
     */
    public function sendContractsAction() {
    	if (!$this->request->isPost()) {
    		return ResponseApi::send(null, Message::$_ERROR_CODING, "只支持POST请求");
    	}
    	$contract = new Contract();
    	$versionNo = $this->request->getPost('versionNo');
    	if (!$versionNo) {
    		return ResponseApi::send(null, Message::$_ERROR_LOGIC, "版本号数据格式错误");
    	}
    	$datas = $this->request->getPost('data');
    	$datas = json_decode($datas, true);
    	if(!$datas) {
    		return ResponseApi::send(null, Message::$_ERROR_LOGIC, json_last_error());
    	}
		$db = $this->get_db();
		$db->begin();
		$keys = '(`'. implode('`, `', array_keys($datas[0])) . '`)';
		$values = '';
    	foreach($datas as $k => $v) {
			$v['Banks'] = serialize($v['Banks']);
			$v['Mats'] = serialize($v['Mats']);
			$values .= ",('".implode("', '", array_values($v))."')";
 		}
		$values = substr($values, 1);
    	if(!$db->execute("DELETE FROM contract")) {
			$db->rollback();
			return ResponseApi::send(null, Message::$_ERROR_SYSTEM, "保存数据失败");
		}
		if(!$db->execute("ALTER TABLE contract AUTO_INCREMENT = 1")) {
			$db->rollback();
			return ResponseApi::send(null, Message::$_ERROR_SYSTEM, "保存数据失败");
		}
		$sql = "INSERT INTO contract {$keys} VALUES {$values}";
		$this->get_logger()->debug($sql);
		if(!$db->execute($sql)) {
			$db->rollback();
			return ResponseApi::send(null, Message::$_ERROR_SYSTEM, "保存数据失败");
		}
		$db->commit();
		return ResponseApi::send();
    }

    private static function checkDate($date) {
		preg_match_all('/(\d{4})(\d{2})(\d{2})/', $date, $matches);
		return checkdate($matches[2], $matches[3], $matches[1]);
    }

    /**
     * 商城向erp同步订单数据
     */
    public function saveOrderAction() {
        $orderstr = $this->request->get("orderstr");
        $params = array();
        $params['type'] = 'b2bToErp_saleOrder_save';
        $params['json'] = json_decode($orderstr, true);
        $param = json_encode($params);

        //unicode转中文
        function decodeUnicode($str){
			return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', create_function('$matches', 'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'), $str);
		}
		$param = decodeUnicode($param);
		$this->get_logger()->debug($param);
        $url = self::BASE_URL;
        $ch = curl_init();
        $timeout = 20;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('name' => $param));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $response = curl_exec($ch);
        $this->get_logger()->debug($response);
        curl_close($ch);
        $return = json_decode($response, true);
        if ($response && is_array($return)) {
            if($return['isSuccess'] == 0) {
            	$orderSn = $return['b2bFnum'];
            	$erpSn = $return['erpFnum'];
            	$order = OrderInfo::findFirst(array(
					'conditions' => 'orderSn = :orderSn:',
            		'bind' => compact('orderSn')
            	));
            	if(is_object($order) && $order->count()) {
            		$order->erpSn = $erpSn;
            		$order->status = 0;
            		if(!$order->save()) {
            			foreach($order->getMessages() as $message) {
            				return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
            			}
            		}
            	}
                return ResponseApi::send();
            } else if($return['isSuccess'] == 1) {
                return ResponseApi::send(null, Message::$_ERROR_SYSTEM, "ERP接口出错。");
            }
        } else {
            return ResponseApi::send(null, Message::$_ERROR_SYSTEM, "ERP接口出错。");
        }
    }

    /**
     * 支付确认
     */
    public function sendPlyOrderAction() {
    	$erpFnum = $this->dispatcher->getParam('erpFnum');
    	$b2bFnum = $this->dispatcher->getParam('b2bFnum');
    	$param = '{"type":"b2bToErp_saleOrder_Confirm","json":{"erpFnum":"'.$erpFnum.'","b2bFnum":"'.$b2bFnum.'","isConfirm":1}}';
    	$response = RequestUtil::syncPost(self::BASE_URL, array('name' => $param));
    	$return = json_decode($response, true);
    	if ($response && is_array($return)) {
    		if($return['isSuccess'] == 0) {
    			$orderSn = $return['b2bFnum'];
    			$erpSn = $return['erpFnum'];
    			$order = OrderInfo::findFirst(array(
    					'conditions' => 'orderSn = :orderSn:',
    					'bind' => compact('orderSn')
    			));
    			$order->status = 3;
    			if(!$order->save()) {
    				foreach($order->getMessage() as $message) {
    					return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
					}
				}
				// 给用户增加积分
				$ratio = ShopConfig::findFirst(array (
						'conditions' => 'code = "integral_scale"',
						'columns' => 'value'
				));
				$ratio = explode(':', $ratio->value);
				//获得订单信息
				$order = OrderInfo::findFirst(array(
					'conditions' => 'orderSn = :b2bFnum:',
					'bind' => compact('b2bFnum'),
					'bindTypes' => array(Column::BIND_PARAM_STR),
					'columns' => 'orderAmount'
				));
				// 新获得积分
				$credits = floor($order->orderAmount / $ratio[1] * $ratio[0]);
				// 保存积分获取记录
				$userId = $this->get_user()->id;
				$record = new ExchangeGetRecord();
				// $record->setTransaction($transaction);
				$record->userId = $userId;
				$record->credits = $credits;
				$record->orderNo = $b2bFnum;
				if (!$record->save()) {
					foreach ($record->getMessages() as $message) {
						return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
					}
				}
				// 用户增加积分
				$user = Users::findFirst($userId);
				$user->credits = $user->credits + $credits;
				if (!$user->save()) {
					foreach ($user->getMessages() as $message) {
						return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
					}
				}
				// 更新会话中用户信息
				$user = $this->get_user();
				$user->credits = $user->credits + $credits;
	    		$this->get_session()->set('auth', $user);
    			$this->uStatus($b2bFnum, 2);
    			return ResponseApi::send();
    		} else if($return['isSuccess'] == 1) {
    			return ResponseApi::send(null, Message::$_ERROR_SYSTEM, "ERP接口出错。");
    		}
    	} else {
    		return ResponseApi::send(null, Message::$_ERROR_SYSTEM, "ERP接口出错。");
    	}
    }

    /**
     * 验收确认
     */
    public function sendCheckOrderAction() {
    	$erpFnum = $this->dispatcher->getParam('erpFnum');
    	$b2bFnum = $this->dispatcher->getParam('b2bFnum');
    	$param = '{"name":"administrator","pwd":"888888","type":"b2bToErp_saleOrder_Check","json":{"erpFnum":"'.$erpFnum.'","b2bFnum":"'.$b2bFnum.'","isCheck":1}}';
    	$response = RequestUtil::syncPost(self::BASE_URL, array('name' => $param));
    	$this->get_logger()->debug('sendCheckOrder' . $response);
    	$return = json_decode($response, true);
    	if ($response && is_array($return)) {
    		if($return['isSuccess'] == 0) {
    			$orderSn = $return['b2bFnum'];
    			$erpSn = $return['erpFnum'];
    			$order = OrderInfo::findFirst(array(
    					'conditions' => 'orderSn = :orderSn:',
    					'bind' => compact('orderSn')
    			));
    			$order->status = 4;
    			if(!$order->save()) {
    				foreach($order->getMessage() as $message) {
    					return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
    				}
    			}
		    	$this->uStatus(array_shift(explode('-', $b2bFnum)), 3);
    			return ResponseApi::send();
    		} else if($return['isSuccess'] == 1) {
    			return ResponseApi::send(null, Message::$_ERROR_SYSTEM, "ERP接口出错。");
    		}
    	} else {
    		return ResponseApi::send(null, Message::$_ERROR_SYSTEM, "ERP接口出错。");
    	}
    }

    /**
     * 变更订单状态
     * DDGL-05
     */
    public function uStatus($orderSn, $status) {
    	$criteria = OrderInfo::query();
    	$criteria->leftJoin('OrderInfo', 'IF(instr(OI.orderSn, "-"), substring(OI.orderSn, 1, instr(OI.orderSn, "-") - 1), OI.orderSn) =
				IF(instr(OrderInfo.orderSn, "-"),substring(OrderInfo.orderSn,1 , instr(OrderInfo.orderSn, "-")-1),OrderInfo.orderSn)',
    			'OI');
    	$criteria->leftJoin('OrderGoods', 'OG.orderId = OI.id', 'OG');
    	$criteria->where('OrderInfo.orderSn = :orderSn:', compact('orderSn'));
    	$criteria->columns('
				OI.orderSn,
				OG.nums,
				OI.status
				');
    	$result = $criteria->execute();
    	$nums = $subNums = 0;
    	$subOrder = array();
    	$parentOrderSn = strpos($orderSn, '-') !== false ? strstr($orderSn, '-', true) : $orderSn;
    	if(is_object($result) && $result->count()) {
    		foreach($result as $r) {
    			if($r->orderSn == $parentOrderSn) {
    				$nums += $r->nums;
    			} else {
    				$subNums += $r->nums;
    				$subOrder[] = $r->status;
    			}
    		}
    	}
    	if($nums != $subNums || !$subNums) {
    		return false;
    	}
    	$flag = 0;
    	$order = OrderInfo::findFirst('orderSn = ' . $parentOrderSn);
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
     * 订单接口
     */
    public function orderAction() {
        $data = $this->request->getPost("data");
        $this->get_logger()->debug("order\n" . $data);
		$typeFields = array(
				1 => array('contractNums' => 'qtyAudit', 'contractPrice' => 'priceAudit'),
				2 => array('checkNums' =>'qtyExam', 'checkPrice' => 'priceExam'));
		$amountType = array(1 => 'contractCount', 2 => 'checkCount');
		$statusType = array(1 => 1, 2 => 3);
		if($data) {
			$data = json_decode($data, true);
			if(!json_last_error()) {
				$orderSn = $data['b2bFnum'];
				$saleOrder = array_pop($data['SAL_SaleOrder__FSaleOrderEntry']);
				$confirmType = $data['confirmType'];
				if(!in_array($confirmType, array(1, 2))) {
					return ResponseApi::send(null, Message::$_ERROR_SYSTEM, "数据格式错误");
				}
				$order = OrderInfo::findFirst(array(
					'conditions' => 'orderSn = :orderSn:',
					'bind' => compact('orderSn')
				));
				if(!is_object($order) || !$order) {
					return ResponseApi::send(null, Message::$_ERROR_SYSTEM, "订单不存在");
				}
                //
                if ($confirmType == 1) {
                    if($order->status > 1) {
                        return ResponseApi::send(null,Message::$_ERROR_LOGIC,"订单已经确认支付，不允许修改价格信息！！！");
                    }
                } else {
                    if($order->status > 3) {
                        return ResponseApi::send(null,Message::$_ERROR_LOGIC,"订单已经确认验收，不允许修改价格信息！！！");
                    }
                }

				//保存订单合同总价或者验收总价
				$order->{$amountType[$confirmType]} = $saleOrder['FALLAMOUNT'];
				//更改订单状态
				$order->status = $statusType[$confirmType];
				if(!$order->save()) {
					foreach($order->getMessages() as $message) {
						return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
					}
				}
				$orderId = $order->id;
				$orderGoods = OrderGoods::findFirst(array(
					'conditions' => 'orderId = :orderId:',
					'bind' => compact('orderId')
				));
				if(is_object($orderGoods) && $orderGoods) {
					foreach($typeFields[$confirmType] as $k => $v) {
						$orderGoods->$k = $saleOrder[$v];
					}
					if(!$orderGoods->save()) {
						foreach($orderGoods->getMessages() as $message) {
							return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
						}
					}
				} else {
					return ResponseApi::send(null, Message::$_ERROR_SYSTEM, "订单不存在");
				}
			} else {
				return ResponseApi::send(null, Message::$_ERROR_SYSTEM, "数据格式错误");
			}
		} else {
			return ResponseApi::send(null, Message::$_ERROR_SYSTEM, "数据格式错误");
		}
        return ResponseApi::send();
    }
}

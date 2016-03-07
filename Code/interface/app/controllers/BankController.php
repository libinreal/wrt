<?php

use PhpRudder\Mvc\ControllerBase;
use PhpRudder\Http\RequestUtil;
use PhpRudder\Http\ResponseApi;
use Phalcon\Mvc\Model\Transaction\Manager as TxManager;

class BankController extends ControllerBase {

    const B2BPAY_URL = 'https://60.191.15.92:465/B2BPAY';
    const WITHOUT_ERROR = false;

    /**
     * Index action
     */
    public function indexAction() {
        $this->persistent->parameters = null;
    }

    /**
     * 签署订单合同，用户验签，保存签名数据到数据库
     */
    public function submitContractAction() {
        $orderSn = $this->request->getPost('orderSn');
        $signId = $this->request->getPost('signId', 'int');
        $buyerSign = $this->request->getPost('buyerSign');
        if(!$orderSn || !$signId || !$buyerSign) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, '数据格式错误！');
        }
        $sign = BankSign::findFirst('signId = "' . $signId . '" AND orderSn = "' . $orderSn . '"');
        if(!is_object($sign) || !$sign) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, '系统异常');
        }
        $sign->buyerSign = $buyerSign;
        $sign->buyerSignTime = time();
        $sign->signType = 1;
        $manager = new TxManager();
        $transaction = $manager->get();
        $sign->setTransaction($transaction);
        try {
			if(!$sign->save()) {
				foreach($sign->getMessages() as $message) {
					return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
				}
			}
		} catch (\Exception $ex) {
			$manager->rollback($transaction);
			return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $ex->getMessage());
		}
		//修改订单状态
		$orderInfo = OrderInfo::findFirst('orderSn = ' . $orderSn);
		$orderInfo->status = 0;
		$orderInfo->setTransaction($transaction);
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
		$manager->commit();
        return ResponseApi::send();
    }

    /**
     * 签署订单合同，中交验签，发送数据到银行
     */
    public function submitContractAdminAction() {
        $orderSn = $this->request->getPost('orderSn');
        $signId = $this->request->getPost('signId');
        $salerSign = $this->request->getPost('salerSign');
        if(!$orderSn || !$signId || !$salerSign) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, '数据格式错误！');
        }
        $sign = BankSign::findFirst('signId = "' . $signId . '" AND orderSn = "' . $orderSn . '"');
        if(!is_object($sign) || !$sign) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, '系统异常');
        }
        $sign->salerSign = $salerSign;
        $sign->salerSignTime = time();
        $sign->signType = 1;
        $sign->signResult = 1;
        $manager = new TxManager();
        $transaction = $manager->get();
        $sign->setTransaction($transaction);
        try {
            if(!$sign->save()) {
                foreach($sign->getMessages() as $message) {
                    return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
                }
            }
        } catch (\Exception $ex) {
            $manager->rollback($transaction);
            return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $ex->getMessage());
        }

        //发送数据到银行
        $submitData = unserialize($sign->submitData);
        $submitData['buyerSign'] = $sign->buyerSign;
        $submitData['salerSign'] = $sign->salerSign;
        $rs = RequestUtil::httpsPost($submitData, self::B2BPAY_URL . '/SubmitContract');
        $msg = null;
        if(!self::checkResult($rs, $msg)) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, $msg);
        }
        //银行验签成功过，修改订单状态
        $orderInfo = OrderInfo::findFirst('orderSn = ' . $orderSn);
        $orderInfo->status = 1;
        $orderInfo->setTransaction($transaction);
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
        $manager->commit();
        return ResponseApi::send();
    }

    public function getSubmitDataAction() {
        $orderId = $this->request->getPost('orderId', 'int');
        if(!$orderId) {
            ResponseApi::send(null, Message::$_ERROR_LOGIC, '订单ID不能为空！');
        }
        $orderInfo = OrderInfo::findFirst("id = '{$orderId}'");
        if(!$orderInfo) {
            ResponseApi::send(null, Message::$_ERROR_LOGIC, '订单不存在！');
        }
        $sign = BankSign::findFirst('orderSn = "' . $orderInfo->orderSn .'"');
        if(!is_object($sign) || !$sign) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, '系统异常，请联系管理员！');
        }
        //签名原始数据
        $signRawData = unserialize($sign->signData);
        $signData = array(
            'signId' => $sign->signId,
            'orderSn' => $sign->orderSn,
            'signRawData' => $signRawData,
        );
        return ResponseApi::send($signData);
    }

    public function cancelContractAction() {
        $orderSn = $this->request->getPost('orderSn');
        $signId = $this->request->getPost('signId');
        $buyerSign = $this->request->getPost('buyerSign');
        if(!$orderSn || !$signId || !$buyerSign) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, '数据格式错误！');
        }
        $sign = BankSign::findFirst('signId = "' . $signId . '" AND orderSn = "' . $orderSn . '"');
        if(!is_object($sign) || !$sign) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, '系统异常');
        }
        $sign->buyerSign = $buyerSign;
        $sign->buyerSignTime = time();
        $sign->signType = 2;
        $manager = new TxManager();
        $transaction = $manager->get();
        $sign->setTransaction($transaction);
        try {
            if(!$sign->save()) {
                foreach($sign->getMessages() as $message) {
                    return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
                }
            }
        } catch (\Exception $ex) {
            $manager->rollback($transaction);
            return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $ex->getMessage());
        }
        $manager->commit();
        return ResponseApi::send();
    }

    public function cancelContractAdminAction() {
        $orderSn = $this->request->getPost('orderSn');
        $signId = $this->request->getPost('signId');
        $salerSign = $this->request->getPost('salerSign');
        if(!$orderSn || !$signId || !$salerSign) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, '数据格式错误！');
        }
        $sign = BankSign::findFirst('signId = "' . $signId . '" AND orderSn = "' . $orderSn . '"');
        if(!is_object($sign) || !$sign) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, '系统异常');
        }
        $sign->salerSign = $salerSign;
        $sign->salerSignTime = time();
        $sign->signType = 2;
        $manager = new TxManager();
        $transaction = $manager->get();
        $sign->setTransaction($transaction);
        try {
            if(!$sign->save()) {
                foreach($sign->getMessages() as $message) {
                    return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
                }
            }
        } catch (\Exception $ex) {
            $manager->rollback($transaction);
            return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $ex->getMessage());
        }
        $manager->commit();
        //发送数据给银行
        $submitData = unserialize($sign->submitData);
        $submitData['buyerSign'] = $sign->buyerSign;
        $submitData['salerSign'] = $sign->salerSign;
        $rs = RequestUtil::httpsPost($submitData, self::B2BPAY_URL . '/cancelContract');
        $msg = null;
        if(!self::checkResult($rs, $msg)) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, $msg);
        }
        //修改订单状态
        $orderInfo = OrderInfo::findFirst('orderSn = ' . $orderSn);
        $orderInfo->status = 5;
        $orderInfo->setTransaction($transaction);
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
        $manager->commit();
        return ResponseApi::send();
    }

    public function getCancelDataAction() {
        $this->salerInfo = $this->getSalerInfo();
        $orderId = $this->request->getPost('orderId', 'int');
        if(!$orderId) {
            ResponseApi::send(null, Message::$_ERROR_LOGIC, '订单ID不能为空！');
        }
        $orderInfo = OrderInfo::findFirst("id = '{$orderId}'");
        if(!$orderInfo) {
            ResponseApi::send(null, Message::$_ERROR_LOGIC, '订单不存在！');
        }
        //数据是否存在
        $sign = BankSign::findFirst('orderSn = ' . $orderInfo->orderSn . ' AND signType = "2"');
        if(!is_object($sign) || !$sign) {
            $sign = new BankSign();
        }
//         $submitData['interfaceName'] = 'CZB_CANCEL_CONTACT';
//         $submitData['interfaceVersion'] = '1_0';
//         $submitData['bussinessTime'] = date('YmdHis', $orderInfo->createAt);
//         $submitData['merId'] = '201406277';
//         $submitData['timeTamp'] = date('YmdHis');
//         $submitData['contractNo'] = $orderInfo->contractSn;
//         $submitData['orderNo'] = $orderInfo->orderSn;
//         $submitData['operate'] = 'CANCEL';

        $submitData['interfaceName'] = 'CZB_CANCEL_CONTACT';
        $submitData['interfaceVersion'] = '1_0';
        $submitData['bussinessTime'] = date('YmdHis', $orderInfo->createAt);
        $submitData['merId'] = $this->salerInfo['merId'];
        //$submitData['timeTamp'] = '20141203174859';
        $submitData['timeTamp'] = '20150115180953';
        //$submitData['timeTamp'] = '20150115180953';

        $submitData['contractNo'] = $orderInfo->contractSn;
        $submitData['orderNo'] = $orderInfo->orderSn;
        $submitData['operate'] = 'CANCEL';

        //数据
        $manager = new TxManager();
        $transaction = $manager->get();
        $sign->setTransaction($transaction);
        $sign->orderSn = $orderInfo->orderSn;
        $sign->submitData = serialize($submitData);

        //签名数据
        $user = $this->get_user();
        $signData = array(
            'merId' => $submitData['merId'],
            'timeTamp' => $submitData['timeTamp'],
            'orderNo' => $submitData['orderNo'],
            'buyerCstno' => $user->customerNo,
            'salerCstno' => $this->salerInfo['saler_cstno'],
            'contractNo' => $submitData['contractNo'],
            'operate' => 'CANCEL',
        );

        $sign->signData = serialize($signData);
        $sign->signType = 2;
        try {
            if(!$sign->save()) {
                foreach($sign->getMessages() as $message) {
                    return ResponseApi::send(null,  Message::$_ERROR_LOGIC, $message);
                }
            }
        } catch (\Exception $ex) {
            $manager->rollback($transaction);
            return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $ex->getMessage());
        }
        $manager->commit();
        $signRawData = array(
            'signId' => $sign->signId,
            'orderSn' => $orderInfo->orderSn,
            'signRawData' => implode('|', $signData),
        );
        return ResponseApi::send($signRawData);
    }

    /**
     * 订单对账，用户验签，保存签名数据到数据库
     */
    public function confirmContractAction() {
        $orderSn = $this->request->getPost('orderSn');
        $signId = $this->request->getPost('signId', 'int');
        $buyerSign = $this->request->getPost('buyerSign');
        if(!$orderSn || !$signId || !$buyerSign) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, '数据格式错误！');
        }
        $sign = BankSign::findFirst('signId = "' . $signId . '" AND orderSn = "' . $orderSn . '"');
        if(!is_object($sign) || !$sign) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, '系统异常');
        }
        $sign->buyerSign = $buyerSign;
        $sign->buyerSignTime = time();
        $sign->signType = 3;
        $manager = new TxManager();
        $transaction = $manager->get();
        $sign->setTransaction($transaction);
        try {
            if(!$sign->save()) {
                foreach($sign->getMessages() as $message) {
                    return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
                }
            }
        } catch (\Exception $ex) {
            $manager->rollback($transaction);
            return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $ex->getMessage());
        }
        //修改订单状态
        /* $orderInfo = OrderInfo::findFirst('orderSn = ' . $orderSn);
        $orderInfo->status = 4;
        $orderInfo->setTransaction($transaction);
        try {
            if(!$orderInfo->save()) {
                foreach($orderInfo->getMessages() as $message) {
                    return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
                }
            }
        } catch (\Exception $ex) {
            $manager->rollback($transaction);
            return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $ex->getMessage());
        } */
        $manager->commit();
        return ResponseApi::send();
    }

    /**
     * 签署订单合同，中交验签，发送数据到银行
     */
    public function confirmContractAdminAction() {
        $orderSn = $this->request->getPost('orderSn');
        $signId = $this->request->getPost('signId');
        $salerSign = $this->request->getPost('salerSign');
        if(!$orderSn || !$signId || !$salerSign) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, '数据格式错误！');
        }
        $sign = BankSign::findFirst('signId = "' . $signId . '" AND orderSn = "' . $orderSn . '"');
        if(!is_object($sign) || !$sign) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, '系统异常');
        }
        $sign->salerSign = $salerSign;
        $sign->salerSignTime = time();
        $sign->signType = 3;
        $sign->signResult = 1;
        $manager = new TxManager();
        $transaction = $manager->get();
        $sign->setTransaction($transaction);
        try {
            if(!$sign->save()) {
                foreach($sign->getMessages() as $message) {
                    return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
                }
            }
        } catch (\Exception $ex) {
            $manager->rollback($transaction);
            return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $ex->getMessage());
        }

        //发送数据到银行
        $submitData = unserialize($sign->submitData);
        $submitData['buyerSign'] = $sign->buyerSign;
        $submitData['salerSign'] = $sign->salerSign;
        $rs = RequestUtil::httpsPost($submitData, self::B2BPAY_URL . '/SubmitContract');
        $msg = null;
        if(!self::checkResult($rs, $msg)) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, $msg);
        }
        //银行验签成功过，修改订单状态
        $orderInfo = OrderInfo::findFirst('orderSn = ' . $orderSn);
        $orderInfo->status = 4;
        $orderInfo->setTransaction($transaction);
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
        $manager->commit();
        return ResponseApi::send();
    }

    public function getConfirmDataAction() {
        $orderId = $this->request->getPost('orderId', 'int');
        if(!$orderId) {
            ResponseApi::send(null, Message::$_ERROR_LOGIC, '订单ID不能为空！');
        }
        $orderInfo = OrderInfo::findFirst("id = '{$orderId}'");
        if(!$orderInfo) {
            ResponseApi::send(null, Message::$_ERROR_LOGIC, '订单不存在！');
        }

        $confirmSign = BankSign::findFirst('orderSn = "' . $orderInfo->orderSn .'" AND signType = 3');
        if(!is_object($confirmSign) || !$confirmSign) {
            $sign = BankSign::findFirst('orderSn = "' . $orderInfo->orderSn .'" AND signType = 1');
            if(!is_object($sign) || !$sign) {
                return ResponseApi::send(null, Message::$_ERROR_LOGIC, '系统异常，请联系管理员！');
            }
            //保存验签原始数据
            $manager = new TxManager();
            $transaction = $manager->get();
            $confirmSign = new BankSign();
            $confirmSign->setTransaction($transaction);
            $confirmSign->orderSn = $sign->orderSn;
            $submitData = unserialize($sign->submitData);
            $signData = unserialize($sign->signData);
            $submitData['timeTamp'] = $signData['timeTamp'] = date('YmdHis');
            //获取所有子订单
            $subOrder = OrderInfo::find(array(
            		'columns' => 'id',
            		'conditions' => 'orderSn LIKE "'.$confirmSign->orderSn.'-%"',
            ))->toArray();
            $tmpArr = array_map('current', $subOrder);
            $allGoodsMoney = OrderGoods::sum(array(
            	'column' => 'checkPrice * checkNums',
            	'conditions' => 'orderId in ('.implode(', ', $tmpArr).')',
            ));
            $submitData['allGoodsMoney'] = $signData['allGoodsMoney'] = $allGoodsMoney;
            $confirmSign->submitData = serialize($submitData);
            $confirmSign->signData = serialize($signData);
            $confirmSign->createAt = time();
            $confirmSign->signType = 3;
            
            try {
                if(!$confirmSign->save()) {
                    foreach($confirmSign->getMessages() as $message) {
                        return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
                    }
                }
            } catch (\Exception $ex) {
                $manager->rollback($transaction);
                return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $ex->getMessage());
            }
            $manager->commit();
        }
        //签名原始数据
        $signRawData = unserialize($confirmSign->signData);
        $confirmSignData = array(
            'signId' => $confirmSign->signId,
            'orderSn' => $confirmSign->orderSn,
            'signRawData' => $signRawData,
        );
        return ResponseApi::send($confirmSignData);
    }

    /**
     * 用户发货(到货)验签,获取签名相关信息
     * 
     * @return code 0:正常 其它:失败
     */
    public function getSubmitSaleOrderAction() {
        
        $orderId = $this->request->getPost('orderId', 'int');
        if(!$orderId) {
            ResponseApi::send(null, Message::$_ERROR_LOGIC, '订单ID不能为空！');
        }
        $orderInfo = OrderInfo::findFirst("id = '{$orderId}'");
        if(!$orderInfo) {
            ResponseApi::send(null, Message::$_ERROR_LOGIC, '订单不存在！');
        }

        if( $orderInfo->childOrderStatus < SOS_SEND_CC ){
            $signType = 1;
        }else{
            $signType = 2;
        }

        $sign = BankSign::findFirst(
                array( 'conditions' => 'signType = ?1 AND orderSn =?2 ',
                                            'bind'      => array(1 => $signType, 2 => $orderInfo->orderSn)
                )

            );
        if(!is_object($sign) || !$sign) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, '系统异常，请联系管理员！');
        }
        //签名原始数据
        $signRawData = unserialize($sign->signData);
        $signData = array(
            'signId' => $sign->signId,
            'orderSn' => $sign->orderSn,
            'signRawData' => $signRawData,
        );
        return ResponseApi::send($signData);
    }

    /**
     * 用户发货(到货)验签,保存签名
     * 
     * @return code 0:正常 其它:失败
     */
    public function submitSaleOrderAction() {
        
        $signId = $this->request->getPost('signId', 'int');
        $buyerSign = $this->request->getPost('buyerSign');
        if(!$signId || !$buyerSign) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, '数据格式错误！');
        }
        
        $sign = BankSign::findFirst( array( 'conditions' => 'signId = ?1',
                                            'bind'      => array(1 => $signId)
                )
            );
        if(!is_object($sign) || !$sign) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, '系统异常');
        }
        $sign->buyerSign = $buyerSign;
        $sign->buyerSignTime = time();
        
        if(!$sign->save()) {
            foreach($sign->getMessages() as $message) {
                return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
            }
        }
        
        return ResponseApi::send();
    }

    /**
     * 签署订单合同，中交验签，发送数据到银行
     */
    public function submitSaleOrderAdminAction() {
        $orderSn = $this->request->getPost('orderSn');
        $signId = $this->request->getPost('signId');
        $salerSign = $this->request->getPost('salerSign');
        if(!$orderSn || !$signId || !$salerSign) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, '数据格式错误！');
        }
        $sign = BankSign::findFirst('signId = "' . $signId . '" AND orderSn = "' . $orderSn . '"');
        if(!is_object($sign) || !$sign) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, '系统异常');
        }
        $sign->salerSign = $salerSign;
        $sign->salerSignTime = time();
        $sign->signType = 1;
        $sign->signResult = 1;
        $manager = new TxManager();
        $transaction = $manager->get();
        $sign->setTransaction($transaction);
        try {
            if(!$sign->save()) {
                foreach($sign->getMessages() as $message) {
                    return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
                }
            }
        } catch (\Exception $ex) {
            $manager->rollback($transaction);
            return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $ex->getMessage());
        }

        //发送数据到银行
        $submitData = unserialize($sign->submitData);
        $submitData['buyerSign'] = $sign->buyerSign;
        $submitData['salerSign'] = $sign->salerSign;
        $rs = RequestUtil::httpsPost($submitData, self::B2BPAY_URL . '/SubmitContract');
        $msg = null;
        if(!self::checkResult($rs, $msg)) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, $msg);
        }
        //银行验签成功过，修改订单状态
        $orderInfo = OrderInfo::findFirst('orderSn = ' . $orderSn);
        $orderInfo->status = 1;
        $orderInfo->setTransaction($transaction);
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
        $manager->commit();
        return ResponseApi::send();
    }


    private static function checkResult($rs, &$msg) {
        if(self::WITHOUT_ERROR) {
            return true;
        }
        $rs = json_decode($rs, true);
        if($rs['errno'] == '000000') {
            return true;
        } else {
            $msg = $rs['errmsg'];
        }
    }

    private function getSalerInfo() {
        $salerInfoObj = ShopConfig::find(array (
            'conditions' => 'code IN ("saler_cstno", "saler_accno")',
            'columns' => 'code, value'
        ));
        if(is_object($salerInfoObj) && $salerInfoObj->count()) {
            foreach($salerInfoObj as $obj) {
                $salerInfo[$obj->code] = $obj->value;
            }
        }
        return $salerInfo;
    }

    public static $submitData = array(
        'interfaceName|Y',
        'interfaceVersion|Y',
        'bussinessTime|Y',
        'merId|Y',
        'signVersion|Y',
        'timeTamp|Y',
        'contractNo|Y',
        'orderNo|Y',
        'buyerCstno|Y',
        'buyerAccno|Y',
        'buyerbookSum|Y',
        'salerCstno|Y',
        'salerAccno|Y',
        'salerbookSum|Y',
        'allGoodsMoney|Y',
        'tranID|Y',
        'extraData|Y',
        'buyerSign|Y',
        'salerSign|N',
        'creditMode|Y',
        'creditBank|N'
    );

    public static $zjwcData = array(
        'interfaceName|Y',
        'interfaceVersion|Y',
        'bussinessTime|Y',
        'merId|Y',
        'signVersion|Y',
        'rawData|Y',
        'signData|Y',
    );

    public static $cancelData = array(
        'interfaceName|Y',
        'interfaceVersion|Y',
        'bussinessTime|Y',
        'merId|Y',
        'timeTamp|Y',
        'contractNo|Y',
        'orderNo|Y',
        'operate|Y',
        'buyerSign|Y',
        'salerSign|Y'
    );


}
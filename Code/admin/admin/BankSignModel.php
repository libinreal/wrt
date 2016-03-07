<?php
/**
 * 销售订单的显示、详情
 */

define('IN_ECS', true);
//
require(dirname(__FILE__) . '/includes/init.php');
	
	/**
	 * 主要作用是：仅仅只做模板输出。具体数据需要POST调用 class里面的方法。
	 */
	
	class BankSignModel {

		//$_POST
		protected $content = false;
		//
		protected $command = false;
		//
		public function __construct($content){
			$this->content = $content;
			$this->command = $content['command'];
		}
	
		/**
		 *
		 */
		public function run(){
			if ($this->command == 'getSubmitSaleOrder') {
				//
				$this->getSubmitSaleOrderAction();
			}elseif ($this->command == 'submitOrder'){
				//
				$this->submitOrderAction();
			}elseif ($this->command == 'getSubmitPurchaseOrder'){
				//
				$this->getSubmitPurchaseOrderAction();
			}
		}

		/**
		 * 接口名称: 销售子订单验签数据获取
		 * 接口地址：http://admin.zj.dev/admin/BankSignModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params)：
	     *  {
	     *      "entity": "bank_sign",
	     *      "command": "getSubmitSaleOrder",
	     *      "parameters": {
	     *          "params": {
	     *          	"order_id":101//订单ID
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "获取订单成功",
		 *	    "content": {
		 *	    	"signId": "107",
         *    		"signRawData": {
         *   		 	 "signVersion": "WRT1.0",
         *   		  	 "timeTamp": "20160304152930",
         *   		   	 "contractNo": "ht-m2001",
		 *               "orderNo": "2016030401179-2",
		 *               "buyerCstno": "",
		 *               "buyerAccno": "",
		 *               "buyerbookSum": 0,
		 *               "salerCstno": "150917017",
		 *               "salerAccno": "3310010010120192110599",
		 *               "salerbookSum": 0,
		 *               "allGoodsMoney": "1714.40",
		 *               "tranID": "2016030401179",
		 *               "extraData": "0:001056:1200.00:1.00"
		 *           }
		 *	    }
		 *  }
		 */
		public function getSubmitSaleOrderAction()
		{
			$params = $this->content['parameters']['params'];
			$order_id = $params['order_id'];

			if( !$order_id ){
				make_json_response('', '-1', '订单ID错误');
			}

			$order_info_table = $GLOBALS['ecs']->table('order_info');
			$bank_sign_table = $GLOBALS['ecs']->table('bank_sign');

			$order_sql = 'SELECT `order_sn`, `child_order_status` FROM ' . $order_info_table . ' WHERE `order_id` = ' . $order_id;
			$order_info = $GLOBALS['db']->getRow( $order_sql );

			if( !$order_info ){
				make_json_response('', '-1', '订单不存在');
			}

			if( $order_info['child_order_status'] <= SOS_SEND_CC ){//发货
				$signType = 1;
			}else{//到货
				$signType = 2;
			}

			$bank_sign_sql = 'SELECT `sign_id` AS `signId`, `sign_data` AS `signRawData` FROM ' . $bank_sign_table .
							 ' WHERE `order_sn` = \'' . $order_info['order_sn'] . '\' AND `sign_type` = ' . $signType;
			$bank_sign = $GLOBALS['db']->getRow( $bank_sign_sql );

			if( $bank_sign ){
				$bank_sign['signRawData'] = unserialize( $bank_sign['signRawData'] );
				make_json_response($bank_sign, '0', '获取订单成功');
			}else{
				make_json_response($bank_sign, '-1', '获取订单失败');
			}
		}

		/**
		 * 接口名称:子订单验签数据保存
		 * 接口地址：http://admin.zj.dev/admin/BankSignModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params)：
	     *  {
	     *      "entity": "bank_sign",
	     *      "command": "submitOrder",
	     *      "parameters": {
	     *          "params": {
	     *          	"sign_id":101,//签名ID
	     *          	"saler_sign":"xxxxxx",//卖家签名
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "保存签名成功",
		 *	    "content": {}
		 *  }
		 * @return [type] [description]
		 */
		public function submitOrderAction()
		{
			$params = $this->content['parameters']['params'];
			$sign_id = $params['sign_id'];
			$saler_sign = $params['saler_sign'];

			if( !$sign_id ){
				make_json_response('', '-1', '签名ID错误');
			}

			if( !$saler_sign ){
				make_json_response('', '-1', '签名数据错误');
			}

			$bank_sign_table = $GLOBALS['ecs']->table('bank_sign');

			$sign_sql = 'SELECT `sign_id` FROM ' . $bank_sign_table . ' WHERE `sign_id` = ' . $sign_id;
			$sign_data = $GLOBALS['db']->getOne( $sign_sql );

			if( !$sign_data ){
				make_json_response('', '-1', '签名数据获取失败');
			}

			$bank_sign_sql = 'UPDATE ' . $bank_sign_table . ' SET `saler_sign` = \'' . $saler_sign . '\',' .
							 ' `saler_sign_time` = ' . time() . ' WHERE `sign_id` = ' . $sign_id . ' LIMIT 1';
			$bank_sign = $GLOBALS['db']->query( $bank_sign_sql );

			if( $bank_sign ){
				make_json_response('', '0', '签名数据保存成功');
			}else{
				make_json_response('', '-1', '签名数据保存失败');
			}	
		}		

		/**
		 * 接口名称: 采购子订单验签数据获取
		 * 接口地址：http://admin.zj.dev/admin/BankSignModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params)：
	     *  {
	     *      "entity": "bank_sign",
	     *      "command": "getSubmitPurchaseOrder",
	     *      "parameters": {
	     *          "params": {
	     *          	"order_id":101//订单ID
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "获取订单成功",
		 *	    "content": {
		 *	    	"signId": "107",
         *    		"signRawData": {
         *   		 	 "signVersion": "WRT1.0",
         *   		  	 "timeTamp": "20160304152930",
         *   		   	 "contractNo": "ht-m2001",
		 *               "orderNo": "2016030401179-2",
		 *               "buyerCstno": "",
		 *               "buyerAccno": "",
		 *               "buyerbookSum": 0,
		 *               "salerCstno": "150917017",
		 *               "salerAccno": "3310010010120192110599",
		 *               "salerbookSum": 0,
		 *               "allGoodsMoney": "1714.40",
		 *               "tranID": "2016030401179",
		 *               "extraData": "0:001056:1200.00:1.00"
		 *           }
		 *           
         *	
		 *  }
		 * @return [type] [description]
		 */
		public function getSubmitPurchaseOrderAction()
		{
			$params = $this->content['parameters']['params'];
			$order_id = $params['order_id'];

			if( !$order_id ){
				make_json_response('', '-1', '订单ID错误');
			}

			$order_info_table = $GLOBALS['ecs']->table('order_info');
			$bank_sign_table = $GLOBALS['ecs']->table('bank_sign');

			$order_sql = 'SELECT `order_sn`, `child_order_status` FROM ' . $order_info_table . ' WHERE `order_id` = ' . $order_id;
			$order_info = $GLOBALS['db']->getRow( $order_sql );

			if( !$order_info ){
				make_json_response('', '-1', '订单不存在');
			}

			if( $order_info['child_order_status'] <= SOS_SEND_SC ){//发货
				$signType = 3;
			}else{//到货
				$signType = 4;
			}

			$bank_sign_sql = 'SELECT `sign_id` AS `signId`, `sign_data` AS `signData` FROM ' . $bank_sign_table .
							 ' WHERE `order_sn` = \'' . $order_info['order_sn'] . '\' AND `sign_type` = ' . $signType;
			$bank_sign = $GLOBALS['db']->getRow( $bank_sign_sql );

			if( $bank_sign ){
				$bank_sign['signData'] = unserialize( $bank_sign['signData'] );
				make_json_response($bank_sign, '0', '获取订单成功');
			}else{
				make_json_response($bank_sign, '-1', '获取订单失败');
			}		
		}

	}

$content = jsonAction( array( 'getSubmitSaleOrder', 'submitOrder', 'getSubmitPurchaseOrder'
	)
	);
$bankSignModel = new BankSignModel($content);
$bankSignModel->run();
	
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
	if ($_REQUEST['act'] == 'detail') {
		$smarty->display('order_pay_detail.html');
		exit;
	}elseif ($_REQUEST['act'] == 'list'){
		$smarty->display('order_pay_list.html');
		exit;
	}
	
	class OrderPayModel {
	
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
			if ($this->command == 'find') {
				//
				$this->findAction();
			}elseif ($this->command == 'page'){
				//
				$this->pageAction();
			}elseif ($this->command == 'uPayStat'){
				//
				$this->uPayStatAction();
			}
		}
		
		/**
		 * 应付单列表（包括分页）
		 * 接口地址：http://admin.zjgit.dev/admin/OrderPayModel.php
		 * 传入参数格式(分页参数limit,offset。查询条件在where中，模糊查询在like中):
		 * {
		 *	    "entity": "OrderPay",
		 *	    "command": "page",
		 *	    "parameters": {
		 *	        "params": {
		 *	            "where": {
		 *	               "like":{
		 *	               		"order_sn":"6789-1",
		 *	               		"suppliers_name":"供应商",
		 *	               		"pay_status":"1",
		 *	               		"contract_sn": "2015-12-12" ,//合同号
		 *	               		"purchase_order_sn":"",//采购订单号
		 *	               	}
		 *	            },
		 *	            "limit": 0,
		 *	            "offset": 2
		 *	        }
		 *	    }
		 *	}
		 * 返回
		 *	{
		 *	    "error": "0",
		 *	    "message": "应付款查询成功",
		 *	    "content": {
		 *	        "data": [
		 *	            {
		 *	                "order_pay_id": "1",
		 *	                "user_id": "2",
		 *	                "suppliers_id": "3",
		 * 	                "order_id_str": "1,2,3",
		 *	                "order_sn_str": "1234-1-CG,1234-2-CG,6789-1-CG",
		 *	                "order_total": "200.00",
		 *	                "suppliers_name": "供应商名称",
		 *	                "create_time": "2016-02-04 11:12:25",
		 *	                "pay_status": "1"
		 *	            },
		 *	            {
		 *	                "order_pay_id": "2",
		 *	                "user_id": "3",
		 *	                "suppliers_id": "4",
		 *	                "order_id_str": "4,5,6",
		 *	                "order_sn_str": "5678-1-CG,5678-2-CG,6789-1-CG",
		 *	                "order_total": "300.00",
		 *	                "suppliers_name": "供应商测试名称",
		 * 	                "create_time": "2016-02-04 11:12:21",
		 *	                "pay_status": "1"
		 *	            }
		 *	        ],
		 *	        "total": "2"
		 *	    }
		 *	}
		 */
		public function pageAction(){
			$content = $this->content;
			$params = $content['parameters']['params'];
			
			$order_pay_table = $GLOBALS['ecs']->table('order_pay');
			$order_info_table = $GLOBALS['ecs']->table('order_info');

			$like = $params['where']['like'];
			$condition = '1=1 ';
			if (isset($like['order_sn'])) {
				$condition .= ' AND order_sn_str like '."'%".$like['order_sn']."%'";
			}
			if (isset($like['suppliers_name'])) {
				$condition .= ' AND suppliers_name like '."'%".$like['suppliers_name']."%'"; 
			}
			if (isset($like['pay_status'])) {
				$condition .= ' AND pay_status = '.$like['pay_status'];
			}
			if (isset($like['contract_sn'])) {
				$contract_sql = 'SELECT `order_id` FROM ' . $order_info_table . ' WHERE `contract_sn` LIKE \'%' . $like['contract_sn']  . '%\'';
				$contract_ret = $GLOBALS['db']->getAll( $contract_sql );

				$contract_order_arr = array();

				if( $contract_ret ){

					$contract_order_like = '';
					foreach ($contract_ret as $o) {
						
						$contract_order_like .= ' `order_id_str` REGEXP \'(^|,)' . $contract_order_like . '(,|$)\' OR ';
					}
					$condition .= ' AND ' . substr( $contract_order_like, 0, -4);

				}else{
					make_json_response( array( 'data' =>array(), 'total' => 0 ) , "0", "应付款查询成功");
				}


			}

			$sql = 'SELECT * FROM '.$order_pay_table. ' WHERE '.$condition.' ORDER BY create_time DESC LIMIT '.$params['limit'].",".$params['offset'];
			$total_sql = 'SELECT COUNT(*) AS `total` FROM '.$order_pay_table. ' WHERE '.$condition;

			$list = $GLOBALS['db']->getAll($sql);
			$total = $GLOBALS['db']->getRow($total_sql);
			
			$content = array();
			$content['data'] = $list;
			$content['total'] = $total['total'];
			make_json_response( $content, "0", "应付款查询成功");
		}
		
		/**
		 * 应付单详情
		 *  接口地址：http://admin.zjgit.dev/admin/OrderPayModel.php
		 *  传入参数格式(主键id在parameters下级)：
		 *  {
		 *	    "command": "find",
		 *	    "entity": "OrderPay",
		 *	    "parameters": {
		 *	        "order_pay_id":"1"
		 *	    }
		 *	}
		 *	 
		 *  返回
		 *	{
		 *	    "error": "0",
		 *	    "message": "应付款详情查询成功",
		 *	    "content": {
		 *	        "data": {
		 *	            "order_pay_id": "1",
		 *	            "create_time": "2016-02-04 11:12:25",
		 *	            "total": 3,
		 *	            "order_total": "200.00",
		*	            "goods_list": [
		*	                {
		*	                    "goods_id": "993",
		*	                    "goods_name": "同鑫晟 Φ12.7mm-1860Mpa预应力钢绞线",
		*	                    "goods_sn": "001056",
		*	                    "goods_number": "0",
		*	                    "goods_price": "0",
		*						"order_sn": "2014111986226-CG",
		*	                    "shipping_fee": "0.00",
		*	                    "financial_arr": "0.00",
		*	                    "order_amount": "0.00",
		*	                    "attributes": "代码:9.50mm\r\n10.80mm\r\n11.10mm\r\n12.70mm\r\n12.90mm\r\n15.20mm\r\n15.24mm\r\n15.70mm\r\n17.80mm结构:1*2\r\n1*3\r\n1*7\r\n1*19\r\n1*37"
		*	                }
		*	            ]
		*	        },
		*	        "file_0"://发票文件
		*	        [
		*	        	{
		*	        		"upload_name":"20160224_s1.jpg",//文件名字
		*	        		"upload_id":1,//文件id
		*	        	}
		*	        ],
		*	        "file_1"://送货单文件
		*	        [
		*	        	{
		*	        		"upload_name":"20160224_s1.jpg",//文件名字
		*	        		"upload_id":1,//文件id
		*	        	}
		*	        ]
		*	        "buttons":["审核通过","付款","驳回"]//可操作的按钮
		*	    }
		*	}
		*/
		public function findAction(){
			$content = $this->content;
			$params = $content['parameters'];
			
			if (isset($params['order_pay_id'])) {

				$order_pay_id = $params['order_pay_id'];

				$order_pay_table = $GLOBALS['ecs']->table('order_pay');//生成单表
				$upload_table = $GLOBALS['ecs']->table('order_pay_upload');//生成单文件表

				$sql_order_pay = 'SELECT * FROM ' . $order_pay_table . ' WHERE order_pay_id = '.$params['order_pay_id'];
				$order_pay_info = $GLOBALS['db']->getRow($sql_order_pay);
				$order_goods = array_values( json_decode( $order_pay_info['goods_json'], true ) );
				//获取商品属性
				foreach ($order_goods as &$value){
					$value['goods_name'] = urldecode( $value['goods_name'] );
					$value['attributes'] = urldecode( $value['attr'] );
					$value['order_amount'] = $value['total'];
					$value['order_sn'] .= '-cg';
					unset( $value['attr'] );
				}
				unset( $value );

				$content = array();


				//已上传文件
				$upload_sql = 'SELECT `upload_name`,`upload_type`, `upload_id` FROM ' . $upload_table .
							  'WHERE `order_pay_id` = ' . $order_pay_id;
				$files = $GLOBALS['db']->getAll( $upload_sql );

				$file_0 = $file_1 = array();

				if( $files ){

					foreach ($files as $f) {
						if( $f['upload_type'] == 0 )
							$file_0[] = array('upload_id' => $f['upload_id'], 'upload_name' => preg_replace( '/\d+\//', '', $f['upload_name'] ), 'upload_url' => '/' . DATA_DIR . '/' . PURCHASE_ORDER_DIR . '/' .  $f['upload_name']);
						else
							$file_1[] = array('upload_id' => $f['upload_id'], 'upload_name' => preg_replace( '/\d+\//', '', $f['upload_name'] ), 'upload_url' => '/' . DATA_DIR . '/' . PURCHASE_ORDER_DIR . '/' .  $f['upload_name']);
					}
				}

				$content['file_0'] = $file_0;
				$content['file_1'] = $file_1;
				
				//应付单基本信息
				$content['data']['order_pay_id'] = $order_pay_info['order_pay_id'];//流水编号
				$content['data']['create_time'] = $order_pay_info['create_time'];//发起时间
				$content['data']['total'] = count($order_goods);
				$content['data']['order_total'] = $order_pay_info['order_total'];

				$buttons = array();
				// 可操作按钮
				switch( $order_pay_info['pay_status'] ){
					case PURCHASE_ORDER_PAY_SUBMIT://已生成
						$buttons = array('审核通过', '驳回');
						break;
					case PURCHASE_ORDER_PAY_SUCCESS://审核通过
						$buttons = array('付款');
						break;
					case PURCHASE_ORDER_PAY_RECEIVED://已付款
						break;
					case PURCHASE_ORDER_PAY_FAIL://审核不通过（驳回）
						break;
					default:
						break;
				}


				//子订单信息
				$content['data']['goods_list'] = $order_goods;
				$content['buttons'] = $buttons;
				make_json_response( $content, "0", "应付款详情查询成功");
			}
			make_json_response( array('data'=>array()), "-1", "参数错误");
		}

		/**
		 * 接口名称：应付款单操作
		 *  接口地址：http://admin.zj.dev/admin/OrderPayModel.php
		 *  传入参数格式(主键id在parameters下级)：
		 *  {
		 *	    "command": "find",
		 *	    "entity": "uPayStat",
		 *	    "parameters": {
		 *	        "order_pay_id":"1",
		 *	        "button":"审核通过"
		 *	    }
		 *	}
		 *	 
		 *  返回
		 *	{
		 *	    "error": "0",
		 *	    "message": "应付款详情操作成功",
		 *	    "content": {}
		 *	        
		 *	}
		 */
		public function uPayStatAction()
		{
			$params = $this->content['parameters'];
			$order_pay_id = $params['order_pay_id'];
			$button = $params['button'];

			if( !$order_pay_id ){
				make_json_response('', '-1', '参数错误');
			}

			$order_pay_table = $GLOBALS['ecs']->table('order_pay');
			$order_info_table = $GLOBALS['ecs']->table('order_info');
			$find_sql = 'SELECT `pay_status`,`order_id_str` FROM ' . $order_pay_table . ' WHERE `order_pay_id` = ' . $order_pay_id;
			
			$find = $GLOBALS['db']->getRow( $find_sql );
			$stat = $find['pay_status'];
			$order_id_str = $find['order_id_str'];

			$update_sql = 'UPDATE ' . $order_pay_table . ' SET `pay_status` = %d ' . ' WHERE `order_pay_id` = ' . $order_pay_id . ' LIMIT 1';

			$msg = '生成单状态为 %s, 无法执行该操作';
			$statusCfg = C('purchase_order_pay_status');
			switch ( $button ) {
				case '审核通过':
					if( $stat == PURCHASE_ORDER_PAY_SUBMIT ){//已生成 可以审核通过
						$update_sql = sprintf($update_sql, PURCHASE_ORDER_PAY_SUCCESS);
						$msg = '';
					}

					break;
				case '付款':
					if( $stat == PURCHASE_ORDER_PAY_SUCCESS ){//审核通过 可以付款
						$update_sql = sprintf($update_sql, PURCHASE_ORDER_PAY_RECEIVED);
						$msg = '';
					}

					break;
				case '驳回':
					if( $stat == PURCHASE_ORDER_PAY_SUBMIT ){//已生成 可以驳回
						$update_sql = sprintf($update_sql, PURCHASE_ORDER_PAY_FAIL);
						$msg = '';

						$order_up_sql = 'UPDATE ' . $order_info_table . ' SET `purchase_pay_status` = ' . PURCHASE_ORDER_PAY_UNMAKE .
										' WHERE order_id IN(' . $order_id_str .')';
						$order_up = $GLOBALS['db']->query( $order_up_sql );
					}

					break;
				default:
					
					break;
			}
			
			if( $msg ){//无法执行操作
				$msg = sprintf($msg, $statusCfg[ $stat ]);
				make_json_response('', '-1', $msg);
			}else{
				$GLOBALS['db']->query( $update_sql );
				make_json_response('', 0, '操作成功');
			}

		}
		
	}
	
	$content = jsonAction( array( 'uPayStat'
		)
		);
	$orderModel = new OrderPayModel($content);
	$orderModel->run();
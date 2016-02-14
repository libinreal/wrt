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
		 *	               "like":{"order_sn":"6789-1","suppliers_name":"供应商","pay_status":"1"}
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
			$sql_total = 'SELECT COUNT(*) as total FROM '.$order_pay_table;
			$total = $GLOBALS['db']->getRow($sql_total);

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
			$sql = 'SELECT * FROM '.$order_pay_table. ' WHERE '.$condition.' ORDER BY create_time DESC LIMIT '.$params['limit'].",".$params['offset'];
			$list = $GLOBALS['db']->getAll($sql);
			
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
		*	                    "goods_number_arrival": "0",
		*	                    "goods_price_add": "0",
		*						"order_sn": "2014111986226-CG",
		*	                    "shipping_fee_arr_saler": "0.00",
		*	                    "financial_arr": "0.00",
		*	                    "order_amount_arr_saler": "0.00",
		*	                    "attributes": "代码:9.50mm\r\n10.80mm\r\n11.10mm\r\n12.70mm\r\n12.90mm\r\n15.20mm\r\n15.24mm\r\n15.70mm\r\n17.80mm结构:1*2\r\n1*3\r\n1*7\r\n1*19\r\n1*37"
		*	                },
		*	                {
		*	                    "goods_id": "993",
		*	                    "goods_name": "同鑫晟 Φ12.7mm-1860Mpa预应力钢绞线",
		*	                    "goods_sn": "001056",
		*	                    "goods_number_arrival": "0",
		*	                    "goods_price_add": "0",
		*						"order_sn": "2014111986226-1-CG",
		*	                    "shipping_fee_arr_saler": "0.00",
		*	                    "financial_arr": "0.00",
		*	                    "order_amount_arr_saler": "0.00",
		*	                    "attributes": "代码:9.50mm\r\n10.80mm\r\n11.10mm\r\n12.70mm\r\n12.90mm\r\n15.20mm\r\n15.24mm\r\n15.70mm\r\n17.80mm结构:1*2\r\n1*3\r\n1*7\r\n1*19\r\n1*37"
		*	                },
		*	                {
		*	                    "goods_id": "366",
		*	                    "goods_name": "日照钢铁螺纹钢 HRB400 Ф20",
		*	                    "goods_sn": "001056",
		*	                    "goods_number_arrival": "0",
		*	                    "goods_price_add": "2900",
		*						"order_sn": "2014111986226-1-CG",
		*	                    "shipping_fee_arr_saler": "0.00",
		*	                    "financial_arr": "0.00",
		*	                    "order_amount_arr_saler": "0.00",
		*	                    "attributes": "牌号:HRB400\r\nHRB400E\r\nHRB335\r\nPSB1080规格:Φ10\r\nΦ12\r\nΦ14\r\nΦ16\r\nΦ18\r\nΦ20\r\nΦ22\r\nФ25\r\nФ28\r\nФ32\r\nФ36\r\nФ40长度:9米\r\n12米"
		*	                }
		*	            ]
		*	        }
		*	    }
		*	}
		*/
		public function findAction(){
			$content = $this->content;
			$params = $content['parameters'];
			
			if (isset($params['order_pay_id'])) {
				$sql_order_pay = 'SELECT * FROM order_pay WHERE order_pay_id = '.$params['order_pay_id'];
				$order_pay_info = $GLOBALS['db']->getrow($sql_order_pay);
				
				$sql_order_goods = "SELECT  og.goods_id,og.goods_name,og.goods_sn,og.goods_number_arrival,og.goods_price_add,oi.order_sn,oi.shipping_fee_arr_saler,oi.financial_arr,oi.order_amount_arr_saler FROM
						 order_goods og,order_info oi WHERE og.order_id = oi.order_id AND  og.order_id in (".$order_pay_info['order_id_str'].')';
				$order_goods = $GLOBALS['db']->getAll($sql_order_goods);
				//获取商品属性
				foreach ($order_goods as $key => $value){
					$attributes = getAttributesByCatId(getCatIdByGoodsId($value['goods_id']));
					$str = '';
					foreach ($attributes as $v){
						$str .= $v['attr_name'].':'.$v['attr_values'];
					}
					$order_goods[$key]['attributes'] = $str;
					$order_goods[$key]['order_sn'] = $value['order_sn'].'-CG';
				}
				$content = array();
				//应付单基本信息
				$content['data']['order_pay_id'] = $order_pay_info['order_pay_id'];//流水编号
				$content['data']['create_time'] = $order_pay_info['create_time'];//发起时间
				$content['data']['total'] = count($order_goods);
				$content['data']['order_total'] = $order_pay_info['order_total'];
				//子订单信息
				$content['data']['goods_list'] = $order_goods;
				make_json_response( $content, "0", "应付款详情查询成功");
			}
			make_json_response( array('data'=>array()), "-1", "参数错误");
		}
		
	}
	
	$content = jsonAction();
	$orderModel = new OrderPayModel($content);
	$orderModel->run();
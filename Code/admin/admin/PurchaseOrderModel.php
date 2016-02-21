<?php
/**
 * 采购订单的显示、详情
 */

define('IN_ECS', true);
//
require(dirname(__FILE__) . '/includes/init.php');
	
	/**
	 * 主要作用是：仅仅只做模板输出。具体数据需要POST调用 class里面的方法。
	 */
	class PurchaseOrderModel {
		
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
			if ($this->command == 'page'){
				//
				$this->pageAction();
			}elseif ($this->command == 'initPriceSend'){
				//
				$this->initPriceSendAction();
			}elseif ($this->command == 'updatePriceSend'){
				//
				$this->updatePriceSendAction();
			}elseif ($this->command == 'childerDetail'){
				//
				$this->childerDetailAction();
			}elseif ($this->command == 'initPriceArr'){
				//
				$this->initPriceArrAction();
			}elseif ($this->command == 'updatePriceArr'){
				//
				$this->updatePriceArrAction();
			}elseif ($this->command == 'updateChilderStatus'){
				//
				$this->updateChilderStatusAction();
			}
		}


		/**
		 * 接口名称：订单列表  
		 * 接口地址：http://admin.zj.dev/admin/PurchaseOrderModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params， "where"可以为空，有则 表示搜索条件，"limit"表示页面首条记录所在行数, "offset"表示要显示的数量)：
	     *  {
	     *      "entity": 'order_info',
	     *      "command": 'page',
	     *      "parameters": {
	     *          "params": {
	     *              "where": {
	     *              "like":{"order_sn":"no11232","suppliers_name":"郭某某","contract_name":"xxxxx需求合同"},//订单编号 客户名称 合同名称
	     *                  "status": 0,//订单状态
	     *                  "due_date1": 2015-01-01,//起始日期
	     *                  "due_date2": 2015-01-01//结束日期
	     *              },
	     *              "limit": 0,//起始行号
	     *              "offset": 2//返回的行数
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "订单查询成功",
		 *	    "content": { 
		 *	    	"data":[
		 *	        {
		 *	        		"order_id":1, //订单ID
		 *                  "order_sn":2 ,//订单号
		 *                  "goods_name": 'xxx' ,//商品名称
		 *                  "goods_sn": "ad11223", //商品编号
		 *                  "attr": "2015-12-12" ,//规格属性
		 *                  "price": "薛某" ,//单价
		 *                  "number": "薛某" ,//数量
		 *                  "shipping": "薛某" ,//物流
		 *                  "add_time": "2015-12-12 16:41:00" ,  //下单时间
		 *                  "status": 0 //订单状态
		 *           }
		 *	         ],
		 *	         "total":3
		 *	}
		 */
		public	function pageAction(){
			$content = $this->content;
			$params = $content['parameters']['params'];
			
			$contract_table = $GLOBALS["ecs"]->table("contract");
			$suppliers_table = $GLOBALS["ecs"]->table("suppliers");
			$order_table = $GLOBALS['ecs']->table('order_info');
			$order_goods_table = $GLOBALS['ecs']->table('order_goods');//订单商品
			$goods_attr_table = $GLOBALS['ecs']->table('goods_attr');//规格/型号/材质

			$sql = 'SELECT odr.`order_id` , odr.`order_sn`, odr.`add_time`, odr.`order_status`, odr.`child_order_status`,' .
				   ' odr.`shipping_fee_send_saler`,odr.`shipping_fee_arr_saler`,' .		
				   ' og.`goods_id`,og.`goods_name`,og.`goods_sn`, og.`goods_number_send_saler`,og.`goods_price_send_saler`,og.`goods_number_arr_saler`,og.`goods_price_arr_saler`' .
				   ' FROM ' . $order_table .
				   ' AS odr';

			$total_sql = 'SELECT COUNT(*) as `total` FROM ' . $order_table .' AS odr'; 	
		
			$where = array();	
			if( isset($params['where']) )
				$where = $params['where'];

			$where_str = '';

			$order_ids = '';
			$contract_ids = '';
			$user_ids = '';

			if( isset( $where["like"] ) )
			{
				$like = $where["like"];
				if( isset( $like["order_sn"] ) ){
					$temp_order_sn = array();
					$like["order_sn"] = preg_match( '(\d+-?\d*)'  , $like['order_sn'], $temp_order_sn );
					$like["order_sn"] = $temp_order_sn[0];
					$order_sn_sql =  'SELECT `order_id` FROM ' . $order_table . ' WHERE `order_sn` like \'%' . $like['order_sn'] . '%\' ORDER BY `order_id` ASC';
					$order_sn_arr = $GLOBALS['db']->getAll( $order_sn_sql );

					if( !empty( $order_sn_arr ) ){
						$order_sn_id = array();
						foreach ($order_sn_arr as $k => $v) {
							$order_sn_id[] = $v['order_id'];
						}
						$order_ids = implode(',', $order_sn_id);

						$where_str = ' WHERE odr.`order_id` IN(' . $order_ids . ')';
					}	
				}
				if( isset( $like["suppliers_name"] ) ){
					$suppliers_name_sql = 'SELECT `suppliers_id` FROM ' . $suppliers_table . ' WHERE `suppliers_name` like \'%' . $like['suppliers_name'] . '%\' ORDER BY `suppliers_id` ASC';
					$suppliers_name_arr = $GLOBALS['db']->getAll( $suppliers_name_sql );

					if( !empty( $suppliers_name_arr ) ){
						$suppliers_id_arr = array();
						foreach ($suppliers_name_arr as $k => $v) {
							$suppliers_id_arr[] = $v['suppliers_id'];
						}
						$suppliers_ids = implode(',', $suppliers_id_arr);

						if( $where_str )
							$where_str .= ' AND spl.`suppliers_id` IN(' . $suppliers_ids . ')';
						else
							$where_str .= ' WHERE spl.`suppliers_id` IN(' . $user_ids . ')';
					}

				}
				if( isset( $like["contract_name"] ) && $like['contract_name'] ){
					$contract_name_sql = 'SELECT `contract_id` FROM ' . $contract_table . ' WHERE `contract_name` like \'%' . $like['contract_name'] . '%\' ORDER BY `contract_id` ASC';
					$contract_name_arr = $GLOBALS['db']->getAll( $contract_name_sql );
					
					if( !empty( $contract_name_arr ) ){
						$contract_id = array();
						foreach ($contract_name_arr as $k => $v) {
							$contract_id[] = $v['contract_id'];
						}
						$contract_ids = implode(',', $contract_id);

						if( $where_str )
							$where_str .= ' AND crt.`contract_id` IN(' . $contract_ids . ')';
						else
							$where_str .= ' WHERE crt.`contract_id` IN(' . $contract_ids . ')';
					}
				}
			}
			
			if( isset( $where["status"] ) )
			{
				if( $where_str )
					$where_str .= " AND odr.`order_status` = " . $where['status'];
				else
					$where_str .= " WHERE odr.`order_status` = " . $where['status'];
			}	

			if( isset( $where["due_date1"] ) && isset( $where["due_date2"] ) )
			{
				$where['due_date1'] = strtotime( $where['due_date1'] );
				$where['due_date2'] = strtotime( $where['due_date2'] );
				if( $where_str )
					$where_str .= " AND odr.`add_time` >= '" . $where['due_date1'] . "' AND odr.`add_time` <= '" . $where['due_date2'] . "'";
				else
					$where_str .= " WHERE odr.`add_time` >= '" . $where['due_date1'] . "' AND odr.`add_time` <= '" . $where['due_date2'] . "'";
			}
			else if( isset( $where["due_date1"] ) )
			{
				$where['due_date1'] = strtotime( $where['due_date1'] );
				if( $where_str )
					$where_str .= " AND odr.`add_time` >= '" . $where['due_date1'] . "'";
				else
					$where_str .= " WHERE odr.`add_time` >= '" . $where['due_date1'] . "'";
			}
			else if( isset( $where["due_date2"] ) )
			{
				$where['due_date2'] = strtotime( $where['due_date2'] );
				if( $where_str )
					$where_str .= " AND odr.`add_time` <= '" . $where['due_date2'] . "'";
				else
					$where_str .= " WHERE odr.`add_time` <= '" . $where['due_date2'] . "'";
			}

			//过滤原始订单
			if( $where_str ){
				$where_str .= ' AND odr.`parent_order_id` IS NOT NULL AND odr.`parent_order_id` <> 0 AND odr.`child_order_status` >= ' . SOS_SEND_PP  . ' AND odr.`child_order_status` <= ' . SOS_ARR_PC2;
			}else{
				$where_str .= ' WHERE odr.`parent_order_id` IS NOT NULL AND odr.`parent_order_id` <> 0 AND odr.`child_order_status` >= ' . SOS_SEND_PP . ' AND odr.`child_order_status` <= ' . SOS_ARR_PC2;
			}

			$sql = $sql .
				   ' LEFT JOIN ' . $suppliers_table . ' as spl ON odr.`suppers_id` = spl.`suppliers_id` '.
				   ' LEFT JOIN ' . $contract_table . ' as crt ON odr.`contract_sn` = crt.`contract_num` ' .
				   ' LEFT JOIN ' . $order_goods_table . ' as og ON og.`order_id` = odr.`order_id` ' .
				   $where_str . ' ORDER BY odr.`add_time` DESC' .
				   ' LIMIT ' . $params['limit'].','.$params['offset'];
			$orders = $GLOBALS['db']->getAll($sql);
			
			$total_sql = $total_sql . 
						' LEFT JOIN ' . $suppliers_table . ' as spl ON odr.`suppers_id` = spl.`suppliers_id` '.
				   		' LEFT JOIN ' . $contract_table . ' as crt ON odr.`contract_sn` = crt.`contract_num` ' .
				   		' LEFT JOIN ' . $order_goods_table . ' as og ON og.`order_id` = odr.`order_id` ' .
						$where_str;
			$resultTotal = $GLOBALS['db']->getRow($total_sql);

			if( $resultTotal )
			{
				$orders = $orders ? $orders : array();
				foreach($orders as &$v){

					if( $v['child_order_status'] < SOS_ARR_CC ){//发货
						$v['number'] = $v['goods_number_send_saler'];//单价
						$v['price'] = $v['goods_price_send_saler'];//数量
						$v['shipping'] = $v['shipping_fee_send_saler'];//物流
					}else{//到货
						$v['number'] = $v['goods_number_arr_saler'];
						$v['price'] = $v['goods_price_arr_saler'];
						$v['shipping'] = $v['shipping_fee_arr_saler'];
					}
					$v['order_sn'] = $v['order_sn'] ? $v['order_sn'] . '-cg' : '';//订单编号
					$v['status'] = $v['order_status'];//状态
					//规格、型号、材质
					$goods_attr_sql = 'SELECT `attr_value` FROM ' . $goods_attr_table .' WHERE `goods_id` = ' . $v['goods_id'];
					$goods_attr = $GLOBALS['db']->getAll( $goods_attr_sql );
					if( empty( $goods_attr ) ){
						$v['attr'] = '';
					}else{
						$attr_arr = array();
						foreach ($goods_attr as $value) {
							$attr_arr[] = $value['attr_value'];
						}
						$v['attr'] = implode('/', $attr_arr);
					}

					unset( $v['goods_number_send_saler'] );
					unset( $v['goods_price_send_saler'] );
					unset( $v['shipping_fee_send_saler'] );
					unset( $v['goods_number_arr_saler'] );
					unset( $v['goods_price_arr_saler'] );
					unset( $v['shipping_fee_arr_saler'] );
					unset( $v['goods_id']);
					unset( $v['order_status'] );
				}
				
				unset( $v );

				$content = array();
				$content['data'] = $orders;
				$content['total'] = $resultTotal['total'];
				make_json_response( $content, "0", "订单查询成功");
			}
			else
			{
				make_json_response("", "-1", "订单查询失败");
			}
		}


		/**
		 * 接口名称：采购订单详情
		 * 接口地址：http://admin.zj.dev/admin/PurchaseOrderModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params， "where"可以为空，有则 表示搜索条件，"limit"表示页面首条记录所在行数, "offset"表示要显示的数量)：
	     *  {
	     *      "entity": "order_info",
	     *      "command": "childerDetail",
	     *      "parameters": {
	     *          "params": {
	     *          	"order_id":101//订单ID
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "",
		 *	    "content": { 
		 *	    	"info"://订单
		 *	        {
		 *	        	"order_id":1,//订单id
		 *	        	"order_status":0,//订单状态 0 '未确认', 1 '已确认', 2 '待收货', 3 '已完成', 4 '订单取消'
		 *	        	"order_sn":"os122311",//订单编号
		 *	        	"user_id":1,//下单人id 
		 *	        	"user_name":"王x",//下单人
		 *	        	"add_time":"2015-01-01:11",//拆单时间
		 *	        	"contract_sn":"10000",//合同号
		 *	        	"contract_name":"钢材销售",//合同名称
		 *	        	"company_name":"中铁一局",//公司名称
		 *          	"check_status":0,//验签状态
		 * 
		 *	        	"consignee":"aa",//收货人
		 *	        	"address":"xx地址",//收货地址
		 *	        	"mobile":"13011111111",//手机号码
		 *	        	"sign_building":"xx桥"//地址标签
		 *	        },
		 *	        "invoice"://发票
		 *	        {
		 *	        	"inv_type":0,//发票类型 0 增值税专用 1 普通发票
		 *	        	"inv_payee":"xx公司",//发票抬头
		 *	        },
		 *	        "goods"://商品资料
		 *	        {
		 *	        	"goods_id":1,//商品id
		 *	        	"goods_name":"螺纹hb400",//商品名称
		 *	        	"attr":"45/T450/gangjin",//规格/型号/材质
		 *
		 *	        	"goods_number_send_saler":90,//采购信息.发货数量 = 子订单数量
		 *	        	"goods_price_send_saler":20, //采购信息.发货单价
		 *	        	"shipping_fee_send_saler":120,//采购信息.发货物流费
		 *	        	"payment_send":100,//采购信息.发货付款方式
		 *	        	"order_amount_send_saler":1800,//采购信息.发货总金额 = 物流费 + 金融费 + 单价 * 数量
		 *	        	
		 *          	"goods_number_arr_saler":90,//采购信息.到货数量 = 实际到货数量
		 *	        	"goods_price_arr_saler":20, //采购信息.到货单价 = 采购信息.发货单价
		 *	        	"shipping_fee_arr_saler":120,//采购信息.到货物流费
		 *	        	"suppliers_name":100,//供应商
		 *	        	"order_amount_arr_saler":1800,//采购信息.到货总金额 = 物流费 + 金融费 + 单价 * 数量
		 *
		 *	        },
		 *	        "shipping"://物流
		 *	        {
		 *	        	"company_name":"EMS",//物流公司
		 *	        	"shipping_num":"xxx",//物流单号
		 *	        	"tel":"1111111",//联系电话
		 *	        	"shipping_time":"2017-01-01",//发货时间
		 *	        	"log"://动态列表
		 *	        	[
		 *	        	{
		 *	        		"date":"2017/01/02",//日期
		 *	        		"content":"物流记录"//物流记录
		 *	        	}
		 *	        	]
		 *	        },
		 *	        "buttons"://平台可操作按钮
		 *	        [
		 *	        	"发货验签",
		 *	         	"取消验签"
		 *	        ]
		 *	    }
		 *	 }   
		 */
		public function childerDetailAction()
		{
			$content = $this->content;
			$params = $content['parameters']['params'];

			if( !isset( $params['order_id'] ) ){
				make_json_response('', '-1', '订单ID错误');
			}
			$order_id = intval( $params['order_id'] );

			$order_info_table = $GLOBALS['ecs']->table('order_info');
			$user_table = $GLOBALS['ecs']->table('users');
			$contract_table = $GLOBALS['ecs']->table('contract');
			$suppliers_table = $GLOBALS['ecs']->table('suppliers');

			//订单详情
			$order_sql = 'SELECT odr.`order_status`, odr.`child_order_status`, odr.`order_sn`, odr.`user_id`, usr.`user_name`, usr.`companyName` as `company_name`, odr.`add_time`, odr.`contract_sn`, '. "ifnull(crt.contract_name, '') as contract_name," .//订单相关
						 ' odr.`consignee`, odr.`address`, odr.`mobile`, odr.`sign_building`, ' .
					 	 ' odr.`inv_type`, odr.`inv_payee`, odr.`inv_bank_name`, odr.`inv_bank_account`, odr.`inv_bank_address`, odr.`inv_tel`, odr.`inv_fax`, ' .	//发票相关
					 	 ' odr.`shipping_fee_send_saler`, odr.`shipping_fee_arr_saler`, ' .//商品资料
					 	 ' odr.`order_amount_send_saler`, odr.`order_amount_arr_saler`, ' .//总金额
					 	 ' odr.`shipping_info`, odr.`shipping_log`, ' .//物流信息
					 	 ' odr.`suppers_id` ' .//供应商信息
						 'FROM ' .$order_info_table . ' AS odr LEFT JOIN ' . $user_table . '  AS usr ON odr.`user_id` = usr.`user_id` LEFT JOIN ' . $contract_table . ' AS crt ON odr.`contract_sn` = crt.`contract_num` ' . 
						 ' WHERE odr.`order_id` = ' . $order_id;
			$order_info = $GLOBALS['db']->getRow( $order_sql );

			if( empty( $order_info ) )
				make_json_response('', '-1', '子订单详情获取失败');

			$order_info['order_id'] = $order_id;

			//发票内容
			$invoice = array();
			$invoice['inv_type'] = $order_info['inv_type'];//类型
			$invoice['inv_payee'] = $order_info['inv_payee'];//公司名称
			$invoice['inv_bank_name'] = $order_info['inv_bank_name'];//银行
			$invoice['inv_bank_account'] = $order_info['inv_bank_account'];//帐号
			$invoice['inv_bank_address'] = $order_info['inv_bank_address'];//银行地址
			$invoice['inv_tel'] = $order_info['inv_tel'];//联系电话
			$invoice['inv_fax'] = $order_info['inv_fax'];//联系传真

			unset( $order_info['inv_type'] );
			unset( $order_info['inv_payee'] );
			unset( $order_info['inv_bank_name'] );
			unset( $order_info['inv_bank_account'] );
			unset( $order_info['inv_bank_address'] );
			unset( $order_info['inv_tel'] );
			unset( $order_info['inv_fax'] );

			//商品详情
			$order_goods_table = $GLOBALS['ecs']->table('order_goods');
			$goods_table = $GLOBALS['ecs']->table('goods');
			$goods_attr_table = $GLOBALS['ecs']->table('goods_attr');//规格/型号/材质

			// $attribute_table = $GLOBALS['ecs']->table('attribute');
			$order_goods_sql = 'SELECT og.`goods_id`, og.`goods_name`, og.`goods_number_send_saler`, ' .
							   'og.`goods_number_arr_saler`, og.`goods_price_send_saler`, og.`goods_price_arr_saler` FROM ' .
							   $order_goods_table . //物料编码 名称 下单数 供应商
							   ' AS og LEFT JOIN '. $goods_table . ' AS g ON og.`goods_id` = g.`goods_id` ' .
							   ' WHERE `order_id` = ' . $order_id;
			$order_good = $GLOBALS['db']->getRow($order_goods_sql);

			$order_info['add_time'] = date('Y-m-d H:i:s', $order_info['add_time']);

			if( !empty( $order_good ) ){
				
					$order_good['add_time'] = $order_info['add_time'];

					//规格、型号、材质
					$goods_attr_sql = 'SELECT `attr_value` FROM ' . $goods_attr_table .' WHERE `goods_id` = ' . $order_good['goods_id'];
					$goods_attr = $GLOBALS['db']->getAll( $goods_attr_sql );
					if( empty( $goods_attr ) ){
						$order_good['attr'] = '';
					}else{
						$attr_arr = array();
						foreach ($goods_attr as $value) {
							$attr_arr[] = $value['attr_value'];
						}
						$order_good['attr'] = implode('/', $attr_arr);
					}
				
				//采购信息
				
				$order_good['shipping_fee_send_saler'] = $order_info['shipping_fee_send_saler'];//订单记录 物流
				$order_good['shipping_fee_arr_saler'] = $order_info['shipping_fee_arr_saler'];//订单记录 物流

				
				$order_good['order_amount_send_saler'] = $order_info['order_amount_send_saler'];//采购订单 发货总金融
				$order_good['order_amount_arr_saler'] = $order_info['order_amount_arr_saler'];//采购订单 到货总金融

				unset($order_info['shipping_fee_send_saler']);
				unset($order_info['shipping_fee_arr_saler']);
				unset($order_info['order_amount_send_saler']);
				unset($order_info['order_amount_arr_saler']);

				//物流
				if( empty( $order_info['shipping_info'] ) ){
					$shipping = array();
				}else{

					$shipping = json_decode( $order_info['shipping_info'], true);
					$shipping['company_name'] = urldecode( $shipping['company_name'] );
				}

				//供应商
				$suppliers_name = '';
				if( $order_info['suppers_id'] ){
					$suppliers_sql = 'SELECT `suppliers_name` FROM ' . $suppliers_table .
									 'WHERE `suppliers_id` = ' . $order_info['suppers_id'] . ' LIMIT 1';
					unset( $order_info['suppers_id'] );
					$suppliers = $GLOBALS['db']->getOne( $suppliers_sql );
					$suppliers_name = empty( $suppliers ) ? '' : $suppliers;
				}	
				$order_info['suppliers_name'] = $suppliers_name;
				

				if( empty( $order_info['shipping_log'] ) ){
					$shipping['log'] = array();
				}else{

					$shipping['log'] = json_decode( $order_info['shipping_log'], true);
					if( !empty( $shipping['log'] ) ){
						foreach( $shipping['log'] as &$l )
							$l['content'] = urldecode( $l['content'] );
					}
				}				


				$purchase_status = C('purchase_status');//采购订单状态,用于页面显示
				$childer_order_status = C('childer_order_status');//验签状态
				switch ( $order_info['child_order_status'] ) {
					case SOS_SEND_PP://平台已推单(发货)
						$order_info['order_status'] = $purchase_status[PURCHASE_ORDER_UNCOMPLETE];
						$order_info['check_status'] = $childer_order_status[SOS_SEND_PP];
						$buttons = array('发货改价');
						break;
					case SOS_SEND_SC://供应商已验签(发货)
						$order_info['order_status'] = $purchase_status[PURCHASE_ORDER_UNCOMPLETE];
						$order_info['check_status'] = $childer_order_status[SOS_SEND_SC];
						$buttons = array('发货验签', '取消验签');
						break;
					case SOS_SEND_PC2://平台已验签(发货)
						$order_info['order_status'] = $purchase_status[PURCHASE_ORDER_UNCOMPLETE];
						$order_info['check_status'] = $childer_order_status[SOS_SEND_PC2];
						$buttons = array();
						break;
					case SOS_ARR_CC://客户已验签(到货)
						$order_info['order_status'] = $purchase_status[PURCHASE_ORDER_UNCOMPLETE];
						$order_info['check_status'] = $childer_order_status[SOS_ARR_CC];
						$buttons = array();
						break;
					case SOS_ARR_PC://平台已验签(到货)
						$order_info['order_status'] = $purchase_status[PURCHASE_ORDER_UNCOMPLETE];
						$order_info['check_status'] = $childer_order_status[SOS_ARR_PC];
						$buttons = array();
						break;	

					//************************** 采购到货中 BEGIN **************************
					case SOS_ARR_SC://供应商已验签(到货)
						$order_info['order_status'] = $purchase_status[PURCHASE_ORDER_UNCOMPLETE];
						$order_info['check_status'] = $childer_order_status[SOS_ARR_SC];
						$buttons = array('到货验签','到货改价');
						break;
					case SOS_ARR_PC2://平台已验签(到货)
						$order_info['order_status'] = $purchase_status[PURCHASE_ORDER_COMPLETE];
						$order_info['check_status'] = $childer_order_status[SOS_ARR_PC2];
						$buttons = array();
						break;
					//************************** 采购到货中 END **************************
					
					default://未命名状态
						$cs = $order_info['child_order_status'];
						$order_info['order_status'] = $purchase_status[PURCHASE_ORDER_UNCONFIRMED];
						$order_info['check_status'] = $childer_order_status[$cs];
						$buttons = array();
						break;
				}

			}else{
				$order_good = array();
				$buttons = array();
			}
				
			$content = array();

			$content['info'] = $order_info;
			$content['invoice'] = $invoice;
			$content['goods'] = $order_good;
			$content['shipping'] = $shipping;
			$content['buttons'] = $buttons;

			make_json_response($content, '0', '采购订单详情查询成功');

		}



	}

$content = jsonAction( array( "initPriceSend", "childerDetail", "initPriceSend", "updatePriceSend", "initPriceArr", "updatePriceArr", "updateChilderStatus",
							 
					 ) );
$orderModel = new PurchaseOrderModel($content);
$orderModel->run();
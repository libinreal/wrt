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
	if ($_REQUEST['act'] == 'edit' || $_REQUEST['act'] == 'add' ) {
		
		
		$smarty->display('admin_bill.html');
		exit;
	}

	class OrderInfoModel {
		
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
			}elseif ($this->command == 'splitInit'){
				//
				$this->splitInitAction();
			}elseif ($this->command == 'split'){
				//
				$this->splitAction();
			}
		}
		
		/**
		 * 接口名称：订单详情
		 * 接口地址：http://admin.zj.dev/admin/OrderInfoModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params， "where"可以为空，有则 表示搜索条件，"limit"表示页面首条记录所在行数, "offset"表示要显示的数量)：
	     *  {
	     *      "entity": "order_info",
	     *      "command": "find",
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
		 *	    	"info":
		 *	        {
		 *	        	"order_id":1,//订单编号
		 *	        	"order_status":0,//0 新增 1 已完成 2处理中 3已撤单
		 *	        	"order_sn":"os122311",//订单编号
		 *	        	"user_id":1,//客户编号 
		 *	        	"user_name":"王x",//客户名称
		 *	        	"add_time":"2015-01-01:11",//下单时间
		 *	        	"contract_sn":"10000",//销售合同号
		 *	        	"contract_name":"钢材销售",//销售合同名称
		 *	        	"consignee":"aa",//收货人
		 *	        	"address":"xx地址",//收货地址
		 *	        	"mobile":"13011111111",//手机号码
		 *	        	"sign_building":"xx桥"//标志性建筑
		 *	        },
		 *	        "invoice":
		 *	        {
		 *	        	"inv_type":0,//发票类型 0 增值税专用 1 普通发票
		 *	        	"inv_payee":"xx公司",//发票抬头
		 *	        },
		 *	        "goods":
		 *	        [
		 *	        {
		 *	        	"goods_sn":"g0001",//物料代码
		 *	        	"goods_name":"螺纹hb400",//名称
		 *	        	"attr":"45/T450/gangjin",//规格/型号/材质
		 *	        	"goods_number":90,//下单数量
		 *	        	"send_number":10, //已拆单数量
		 *	        	"remain_number":80, //未拆单数量
		 *	        	"goods_price":20, //单价
		 *	        	"subtotal":1800,//小计
		 *	        	"suppliers_name":"中交1",//所选供应商
		 *	        	"add_time":"2015-01-01:11"//下单时间
		 *	        }
		 *	        ]
		 *	    }
		 *	 }   
		 */
		public function findAction(){
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
			$order_sql = 'SELECT odr.`order_status`, odr.`order_sn`, odr.`user_id`, usr.`companyName` as `user_name`, odr.`add_time`, odr.`contract_sn`, '. "ifnull(crt.contract_name, '') as contract_name," .//订单相关
						 ' odr.`consignee`, odr.`address`, odr.`mobile`, odr.`sign_building`, ' .
					 	 ' odr.`inv_type`, odr.`inv_payee`, odr.`inv_bank_name`, odr.`inv_bank_account`, odr.`inv_bank_address`, odr.`inv_tel`, odr.`inv_fax` ' .	//发票相关
						 'FROM ' .$order_info_table . ' AS odr LEFT JOIN ' . $user_table . '  AS usr ON odr.`user_id` = usr.`user_id` LEFT JOIN ' . $contract_table . ' AS crt ON odr.`contract_sn` = crt.`contract_num` ' . 
						 ' WHERE odr.`order_id` = ' . $order_id;
			$order_info = $GLOBALS['db']->getRow( $order_sql );

			if( empty( $order_info ) )
				make_json_response('', '-1', '订单详情获取失败');

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

			//物料详情
			$order_goods_table = $GLOBALS['ecs']->table('order_goods');
			$goods_table = $GLOBALS['ecs']->table('goods');
			$goods_attr_table = $GLOBALS['ecs']->table('goods_attr');//规格/型号/材质

			// $attribute_table = $GLOBALS['ecs']->table('attribute');
			$order_goods_sql = 'SELECT og.`goods_id`, og.`goods_name`, og.`goods_sn`, og.`goods_price`, og.`goods_number`, og.`send_number`, sp.`suppliers_name` FROM ' .
							   $order_goods_table . //物料编码 名称 下单数 已拆 未拆 供应商
							   ' AS og LEFT JOIN '. $goods_table . ' AS g ON og.`goods_id` = g.`goods_id` ' .
						 	   'LEFT JOIN ' . $suppliers_table . ' AS sp ON g.`suppliers_id` = sp.`suppliers_id`' . 
							   ' WHERE `order_id` = ' . $order_id;
			$order_goods_arr = $GLOBALS['db']->getAll($order_goods_sql);

			if( !empty( $order_goods_arr ) ){
				//未拆单 小计
				foreach($order_goods_arr as &$order_good){
					$order_good['remain_number'] = $order_good['goods_number'] - $order_good['send_number'];//未拆单
					$order_good['subtotal'] = $order_good['goods_price'] * $order_good['goods_number'];//小计
					$order_good['add_time'] = date('Y-m-d H:i:s', $order_info['add_time'] );

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
				}
				unset( $order_info['add_time'] );
				unset( $order_good );
			}else{
				$order_goods_arr = array();
			}
				
			$content = array();

			$content['info'] = $order_info;
			$content['invoice'] = $invoice;
			$content['goods'] = $order_goods_arr;
			make_json_response($content, '0', '订单详情查询成功');

		}
		
		/**
		 * 接口名称：订单列表  
		 * 接口地址：http://admin.zj.dev/admin/OrderInfoModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params， "where"可以为空，有则 表示搜索条件，"limit"表示页面首条记录所在行数, "offset"表示要显示的数量)：
	     *  {
	     *      "entity": 'order_info',
	     *      "command": 'page',
	     *      "parameters": {
	     *          "params": {
	     *              "where": {
	     *              "like":{"order_sn":"no11232","user_name":"郭某某","contract_name":"xxxxx需求合同"},//订单编号 客户名称 合同名称
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
		 *                  "customer_name": 'xxx' ,//客户名称
		 *                  "customer_id": "ad11223", //客户号
		 *                  "contract_sn": "2015-12-12" ,//合同号
		 *                  "contract_name": "薛某" ,//销售合同名称
		 *                  "add_time": "2015-12-12 16:41:00" ,  //下单时间
		 *                  "best_time": "2015-12-12 16:41:00" ,//希望到货时间
		 *                  "goods_amount": 200,//下单金额
		 *                  "status": 0 ,//订单状态 0:未偿还 1:已偿还
		 *           }
		 *	         ],
		 *	         "total":3
		 *	}
		 */
		public	function pageAction(){
			$content = $this->content;
			$params = $content['parameters']['params'];
			
			$contract_table = $GLOBALS["ecs"]->table("contract");
			$user_table = $GLOBALS["ecs"]->table("users");
			$order_table = $GLOBALS['ecs']->table('order_info');

			$sql = 'SELECT odr.`order_id` , odr.`order_sn`, odr.`contract_sn`, odr.`user_id` AS `customer_id`, odr.`add_time`, odr.`best_time`, odr.`goods_amount`, odr.`order_status`' .
				   ' ,usr.`companyName` as `customer_name`, IFNULL(crt.`contract_name`,\'\') as `contract_name` ' .
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
				if( isset( $like["user_name"] ) ){
					$user_name_sql = 'SELECT `user_id` FROM ' . $user_table . ' WHERE `companyName` like \'%' . $like['user_name'] . '%\' ORDER BY `user_id` ASC';
					$user_name_arr = $GLOBALS['db']->getAll( $user_name_sql );

					if( !empty( $user_name_arr ) ){
						$user_id_arr = array();
						foreach ($user_name_arr as $k => $v) {
							$user_id_arr[] = $v['user_id'];
						}
						$user_ids = implode(',', $user_id_arr);

						if( $where_str )
							$where_str .= ' AND usr.`user_id` IN(' . $user_ids . ')';
						else
							$where_str .= ' WHERE usr.`user_id` IN(' . $user_ids . ')';
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

			$sql = $sql . 
				   ' LEFT JOIN ' . $user_table . ' as usr ON odr.`user_id` = usr.`user_id` '.
				   ' LEFT JOIN ' . $contract_table . ' as crt ON odr.`contract_sn` = crt.`contract_num` ' .
				   $where_str .
				   ' LIMIT ' . $params['limit'].','.$params['offset'];
			$orders = $GLOBALS['db']->getAll($sql);
			
			$total_sql = $total_sql . 
						' LEFT JOIN ' . $user_table . ' as usr ON odr.`user_id` = usr.`user_id` '.
				   		' LEFT JOIN ' . $contract_table . ' as crt ON odr.`contract_sn` = crt.`contract_num` ' .
						$where_str;
			$resultTotal = $GLOBALS['db']->getRow($total_sql);

			if( $resultTotal )
			{
				$content = array();
				$content['data'] = $orders ? $orders : array();
				$content['total'] = $resultTotal['total'];
				make_json_response( $content, "0", "票据查询成功");
			}
			else
			{
				make_json_response("", "-1", "票据查询失败");
			}
		}
		
		/**
		 * 接口名称：子订单列表
		 * 接口地址：http://admin.zj.dev/admin/OrderInfoModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters)：
	     *  {
	     *      "entity": "order_info",
	     *      "command": "childerList",
	     *      "parameters": {
	     *      	"order_id":101,//主订单id
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",//("0": 成功 ,"-1": 失败)
		 *	    "message": "订单物品拆分信息获取成功",
		 *	    "content":
		 *	    [
		 *	    {
		 *     		"add_time":"2015/01/01",//拆单时间
		 *     		"contract_name":"ht222",//合同名称
		 *     		"order_sn":"2014120330115-20",//订单号
		 *     		"goods_name":"钢材24m",//物料名称
		 *     		"cat_name":"锚具",//物料类别
		 *     		"attr":"1/2/3",//规格/型号/牌号
		 *     		"goods_price":100,//单价
		 *     		"goods_number":20,//数量
		 *     		""
		 *	    }
		 *	    ]
		 *	}
		 */
		public function childerListAction($value='')
		{
			# code...
		}
		
		public	function updateAction(){
			$content = $this->content;
			$params = $content['parameters'];

			
		}
		
		/**
		 * 接口名称：子订单拆分初始化
		 * 接口地址：http://admin.zj.dev/admin/OrderInfoModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters)：
	     *  {
	     *      "entity": "order_info",
	     *      "command": "splitInit",
	     *      "parameters": {
	     *      	"order_id":101,//主订单id
	     *      	"goods_id":993//商品id
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "订单物品拆分信息获取成功",
		 *	    "content": { 
		 *	    	"good":
		 *	        {
		 *	        	"goods_name":1, //商品名
		 *              "goods_sn":2 ,//商品编号
		 *              "goods_id": "xxx" ,//商品id
		 *              "order_id":101,//订单id
		 *              "suppliers_id":17,//供应商id
		 *              "suppliers_name":"上海大汉钢铁",//供应商
		 *              "attr": "ad11223" //规格/型号/材质
		 *           },
		 *	         "order":
		 *	         {
		 *	        	"remain_number":80, //未拆单数量
		 *	        	"goods_price":20, //单价
		 *	        	"rate":0.008,//金融费率
		 *	        	"payment":
		 *	        	[
		 *	        	{
		 *	        		"pay_id":1,//支付方式id
		 *	        		"pay_name":"货到付款"//支付名称
		 *	        	}
		 *	        	],
		 *	         	"suppliers":
		 *	         	[
		 *	         	{
		 *	         		"suppliers_id":17,//供应商ID
		 *	         		"suppliers_name":"上海大汉钢铁"//供应商名字
		 *	         	}
		 *	         	]
		 *	         }
		 *	    }
		 *	}
		 */
		public function splitInitAction()
		{
			$content = $this->content;
			$params = $content['parameters'];
			$goods_id = $params['goods_id'];
			$order_id = $params['order_id'];

			$goods_table = $GLOBALS['ecs']->table('goods');
			$payment_table = $GLOBALS['ecs']->table('payment');
			$contract_table = $GLOBALS['ecs']->table('contract');

			$order_goods_table = $GLOBALS['ecs']->table('order_goods');
			$goods_attr_table = $GLOBALS['ecs']->table('goods_attr');//规格/型号/材质
			$suppliers_table = $GLOBALS['ecs']->table('suppliers');

			$order_info_table = $GLOBALS['ecs']->table('order_info');

			//商品详情
			$good_sql = 'SELECT og.`goods_name`, og.`goods_price`, og.`goods_number`, og.`send_number`, og.`goods_sn`, s.`suppliers_name`, s.`suppliers_id`, ' .
						' IFNULL(c.rate,0.00) AS `rate` FROM ' . $order_goods_table . 
						' AS og LEFT JOIN ' . $goods_table . ' AS g ON g.`goods_id` = og.`goods_id` LEFT JOIN ' . $suppliers_table .
						' AS s ON s.`suppliers_id` = g.`suppliers_id` ' . ' LEFT JOIN ' . $order_info_table .' AS o ON o.`order_id` = og.`order_id` ' .
						' LEFT JOIN ' . $contract_table . ' AS c ON c.`contract_num` = o.`contract_sn` ' .
						' WHERE og.`goods_id` = ' . $goods_id . ' AND og.`order_id` = ' . $order_id;
			$goods = $GLOBALS['db']->getRow( $good_sql );

			if( empty( $goods ) )
				make_json_response('', '-1', '商品不存在');

			

			//规格、型号、材质
			$goods_attr_sql = 'SELECT `attr_value` FROM ' . $goods_attr_table .' WHERE `goods_id` = ' . $goods_id;
			$goods_attr = $GLOBALS['db']->getAll( $goods_attr_sql );

			if( empty( $goods_attr ) ){
				$goods['attr'] = '';
			}else{
				$attr_arr = array();
				foreach ($goods_attr as $value) {
					$attr_arr[] = $value['attr_value'];
				}
				$goods['attr'] = implode('/', $attr_arr);
			}

			$goods['remain_number'] = $goods['goods_number'] - $goods['send_number'];//未拆单

			//订单详情
			//该商品的供应商列表
			$suppliers_sql = 'SELECT `suppliers_id`, `suppliers_name` FROM ' . $suppliers_table;
			$suppliers = $GLOBALS['db']->getAll( $suppliers_sql );
			if( empty( $suppliers ) )
				$suppliers = array();

			//支付方式
			$payment_sql = 'SELECT `pay_id`, `pay_name` FROM ' . $payment_table;
			$payment = $GLOBALS['db']->getAll( $payment_sql );
			if( empty( $payment ) )
				$payment = array();

			$order['suppliers'] = $suppliers;
			$order['payment'] = $payment;
			$order['remain_number'] = $goods['remain_number'];
			$order['goods_price'] = $goods['goods_price'];
			$order['rate'] = $goods['rate'];
			
			unset($goods['remain_number']);
			unset($goods['goods_price']);
			unset($goods['rate']);

			$content = array();
			$content['good'] = $goods;
			$content['order'] = $order;

			make_json_response( $content, '0', '商品拆分初始化成功' );
		}

		/**
		 * 接口名称：子订单拆分
		 * 接口地址：http://admin.zj.dev/admin/OrderInfoModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters)：
	     *  {
	     *      "entity": "order_info",
	     *      "command": "split",
	     *      "parameters": {
	     *      	"order_id":101,//主订单id
	     *      	"goods_id":993,//商品id
	     *      	"send_number":1,//拆单数量
	     *      	"goods_price":20,//物料单价
	     *      	"shipping_fee":120,//物流费用
	     *      	"finance_fee":20,//金融费用
	     *      	"pay_id":1//支付方式
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "订单物品拆分成功",
		 *	    "content": {}
		 */	        	
		public function splitAction()
		{
			$content = $this->content;
			$order_id = $content['parameters']['order_id'];
			$goods_id = $content['parameters']['goods_id'];
			$send_number = ( double )( $content['parameters']['send_number'] );
			$goods_price = ( double )( $content['parameters']['goods_price'] );
			$shipping_fee = ( double )( $content['parameters']['shipping_fee'] );
			$finance_fee = ( double )( $content['parameters']['finance_fee'] );
			$pay_id = $content['parameters']['pay_id'];

			if( empty( $order_id) )
				make_json_response('', '-1', '订单id错误');

			if( empty( $goods_id) )
				make_json_response('', '-1', '商品id错误');

			$goods_table = $GLOBALS['ecs']->table('goods');
			$payment_table = $GLOBALS['ecs']->table('payment');
			$contract_table = $GLOBALS['ecs']->table('contract');

			$order_goods_table = $GLOBALS['ecs']->table('order_goods');
			$goods_attr_table = $GLOBALS['ecs']->table('goods_attr');//规格/型号/材质
			$suppliers_table = $GLOBALS['ecs']->table('suppliers');

			$order_info_table = $GLOBALS['ecs']->table('order_info');
			$sql = 'SELECT * FROM ' . $order_goods_table . ' WHERE `goods_id` = ' . $goods_id .' AND `order_id` = '. $order_id;
			$order_good = $GLOBALS['db']->getRow( $sql );
			
			if( empty( $order_good ) ){
				make_json_response('', '-1', '主订单id和商品id不正确');
			}

			//是否可以拆分
			if( $order_good['goods_number'] - $order_good['send_number'] < $send_number) {
				make_json_response('', '-1', '可拆分数量不足');
			}

			//主订单信息
			$order_info_sql = 'SELECT * FROM ' . $order_info_table . ' WHERE `order_id` = ' . $order_id;
			$parent_order = $GLOBALS['db']->getRow( $order_info_sql );
			if( empty($parent_order) )
				make_json_response('', '-1', '主订单id错误');

			//创建子订单序列号
			$childer_sn_sql = 'SELECT `order_sn` FROM ' . $order_info_table . ' WHERE `parent_order_sn` = \'' .
					      $parent_order['order_sn'] . '\' ORDER BY `order_id` DESC';
			$childer_sn = $GLOBALS['db']->getRow( $childer_sn_sql );

			if( !empty( $childer_sn ) ){
				$snArr = explode('-', $childer_sn['order_sn'] );
				$childer_sn_new = $snArr[1] + 1;
				$childer_sn_new = $parent_order['order_sn'] . '-' . $childer_sn_new;
			}else{
				$childer_sn_new = $parent_order['order_sn'] . '-1';
			}
			
			//复制主订单信息
			$childer_order = $parent_order;
			$childer_order['order_sn'] = $childer_sn_new;
			$childer_order['parent_order_id'] = $parent_order['order_id'];
			$childer_order['parent_order_sn'] = $parent_order['order_sn'];

			$childer_order['goods_amount'] = $send_number * $goods_price;
			$childer_order['order_amount'] = $send_number * $goods_price;

            $childer_order['add_time'] = gmtime();
            $childer_order['order_status'] = 0;
            $childer_order['pay_id'] = $pay_id;

            $childer_order['shipping_status'] = 0;
            $childer_order['shipping_time'] = 0;
            $childer_order['money_paid'] = 0;

            $childer_order['shipping_fee'] = 0;
            $childer_order['invoice_no'] = '';
            $childer_order['money_paid'] = 0;

            $childer_order['order_amount'] = $send_number * $goods_price + $shipping_fee + $finance_fee;
			unset( $childer_order['order_id'] );

			$childer_order_sql = 'INSERT INTO ' . $order_info_table .'(';
			$childer_order_keys = array_keys( $childer_order );
			// print_r( $childer_order_keys );exit;
			foreach ($childer_order_keys as $v) {
				$childer_order_sql .= '`' . $v .'`,';
			}
			$childer_order_sql = substr($childer_order_sql, 0, -1) . ") VALUES(";

			foreach ($childer_order as $v) {
				if( is_null( $v ) )
					$v = '';
				if(is_string( $v ) )
					$v = '\'' . $v . '\'';

				$childer_order_sql = $childer_order_sql . $v . ',';
			}
			$childer_order_sql = substr($childer_order_sql, 0, -1) . ")";
			
			// $GLOBALS['db']->query("START TRANSACTION");//开启事务
			$createOrder = $GLOBALS['db']->query($childer_order_sql);

			if( $createOrder ){
				//子订单商品
				$insert_order_id = $GLOBALS['db'] -> insert_id(); // 获取新插入子订单的订单id

				unset( $order_good['rec_id'] );
				$order_good['order_id'] = $insert_order_id;
				$order_good['goods_number'] = $send_number;
				$order_good['goods_price'] = $goods_price;
				$order_good['send_number'] = 0;
				$order_good['contract_price'] = $goods_price;
				$order_good['contract_number'] = $send_number;
				$order_good['check_price'] = $goods_price;
				$order_good['check_number'] = $send_number;

				$order_good_keys = array_keys( $order_good );
				$order_good_sql = 'INSERT INTO ' . $order_goods_table .'(';
				
				foreach ($order_good_keys as $v) {
					$order_good_sql .= '`' . $v .'`,';
				}
				$order_good_sql = substr($order_good_sql, 0, -1) . ") VALUES(";

				foreach ($order_good as $v) {
					if( is_null( $v ) )
						$v = '';
					if(is_string( $v ) )
						$v = '\'' . $v . '\'';

					$order_good_sql = $order_good_sql . $v . ',';
				}

				$order_good_sql = substr($order_good_sql, 0, -1) . ")";
				$createOrderGood = $GLOBALS['db']->query($order_good_sql);

				if( $createOrderGood ){
					// $GLOBALS['db']->query("COMMIT");//事务提交
					$order_info_sql = 'UPDATE ' . $order_goods_table . ' SET `send_number` = `send_number` + ' . $send_number .
										' WHERE `order_id` = ' . $order_id . ' AND `goods_id` = ' . $goods_id;
					$order_info_update = $GLOBALS['db']->query( $order_info_sql );
					
					if( $order_info_update ){					
						make_json_response('', '0', '订单拆分成功');
					}else{
						$GLOBALS['db']->query('DELETE FROM ' . $order_info_table . ' WHERE `order_sn` = ' . $childer_sn_new);
						$order_good_id = $GLOBALS['db'] -> insert_id(); // 获取新插入订单商品id
						$GLOBALS['db']->query('DELETE FROM ' . $order_goods_table . ' WHERE `rec_id` = ' . $order_good_id);

						make_json_response('', '-1', '订单拆分失败');
					}

				}else{
					// $GLOBALS['db']->query("ROLLBACK");//事务回滚
					$GLOBALS['db']->query('DELETE FROM ' . $order_info_table . ' WHERE `order_sn` = ' . $childer_sn_new);
					make_json_response('', '-1', '订单拆分失败');
				}

			}else{
				// $GLOBALS['db']->query("ROLLBACK");//事务回滚
				make_json_response('', '-1', '订单拆分失败');
			}

		}
	
		
	}
	$content = jsonAction( array( "splitInit", "split" ) );
	$orderModel = new OrderInfoModel($content);
	$orderModel->run();
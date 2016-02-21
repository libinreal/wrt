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
			}elseif ($this->command == 'childerList'){
				//
				$this->childerListAction();
			}elseif ($this->command == 'childerDetail'){
				//
				$this->childerDetailAction();
			}elseif ($this->command == 'addShippingInfo'){
				//
				$this->addShippingInfoAction();
			}elseif ($this->command == 'addShippingLog'){
				//
				$this->addShippingLogAction();
			}elseif ($this->command == 'initPriceSend'){
				//
				$this->initPriceSendAction();
			}elseif ($this->command == 'updatePriceSend'){
				//
				$this->updatePriceSendAction();
			}elseif ($this->command == 'initPriceArr'){
				//
				$this->initPriceArrAction();
			}elseif ($this->command == 'updatePriceArr'){
				//
				$this->updatePriceArrAction();
			}elseif ($this->command == 'updateChilderStatus'){
				//
				$this->updateChilderStatusAction();
			}elseif ($this->command == 'getSuppliersPrice'){
				//
				$this->getSuppliersPriceAction();
			}elseif ($this->command == 'searchChilderList'){
				//
				$this->searchChilderListAction();
			}elseif ($this->command == 'cancelOrder'){
				//
				$this->cancelOrderAction();
			}elseif ($this->command == 'removeGoods'){
				//
				$this->removeGoodsAction();
			}elseif( $this->command == 'getFinaceFee'){
				$this->getFinaceFeeAction();
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
			$order_sql = 'SELECT odr.`order_status`, odr.`order_sn`, odr.`user_id`, usr.`user_name`, usr.`companyName` as `company_name`, odr.`add_time`, odr.`contract_sn`, '. "ifnull(crt.contract_name, '') as contract_name," .//订单相关
						 ' odr.`consignee`, odr.`address`, odr.`mobile`, odr.`sign_building`, ' .
					 	 ' odr.`inv_type`, odr.`inv_payee`, odr.`inv_bank_name`, odr.`inv_bank_account`, odr.`inv_bank_address`, odr.`inv_tel`, odr.`inv_fax` ' .	//发票相关
						 'FROM ' .$order_info_table . ' AS odr LEFT JOIN ' . $user_table . '  AS usr ON odr.`user_id` = usr.`user_id` LEFT JOIN ' . $contract_table . ' AS crt ON odr.`contract_sn` = crt.`contract_num` ' . 
						 ' WHERE odr.`order_id` = ' . $order_id;
			$order_info = $GLOBALS['db']->getRow( $order_sql );

			if( empty( $order_info ) )
				make_json_response('', '-1', '订单详情获取失败');

			$order_info['order_id'] = $order_id;

			$order_status_cfg = C('order_status');
			switch ( $order_info['order_status'] ) {
				case POS_SUBMIT:
					$order_info['order_status'] = $order_status_cfg[POS_SUBMIT];
					break;
				case POS_HANDLE:
					$order_info['order_status'] = $order_status_cfg[POS_HANDLE];
					break;
				case POS_COMPLETE:
					$order_info['order_status'] = $order_status_cfg[POS_COMPLETE];
					break;
				case POS_CANCEL:
					$order_info['order_status'] = $order_status_cfg[POS_CANCEL];
					break;

				default:
					$order_info['order_status'] = '';
					break;
			}



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
							   ' WHERE og.`order_id` = ' . $order_id . ' AND sp.`suppliers_name` IS NOT NULL';
			$order_goods_arr = $GLOBALS['db']->getAll($order_goods_sql);

			$order_info['add_time'] = date('Y-m-d H:i:s', $order_info['add_time']);

			if( !empty( $order_goods_arr ) ){
				//未拆单 小计
				foreach($order_goods_arr as &$order_good){
					$order_good['remain_number'] = $order_good['goods_number'] - $order_good['send_number'];//未拆单
					$order_good['subtotal'] = $order_good['goods_price'] * $order_good['goods_number'];//小计
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
				}
				
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
		 *                  "status": 0 ,//订单状态
		 *                  "is_cancel":"yes"//是否可以取消("yes":可以取消 "no"：不能取消)
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

			//过滤子订单
			if( $where_str ){
				$where_str .= ' AND ( odr.`parent_order_id` IS NULL OR odr.`parent_order_id` = 0 )';
			}else{
				$where_str .= ' WHERE ( odr.`parent_order_id` IS NULL OR odr.`parent_order_id` = 0 )';
			}

			$sql = $sql .
				   ' LEFT JOIN ' . $user_table . ' as usr ON odr.`user_id` = usr.`user_id` '.
				   ' LEFT JOIN ' . $contract_table . ' as crt ON odr.`contract_sn` = crt.`contract_num` ' .
				   $where_str . 'ORDER BY odr.`add_time` DESC' .
				   ' LIMIT ' . $params['limit'].','.$params['offset'];
			$orders = $GLOBALS['db']->getAll($sql);
			
			$total_sql = $total_sql . 
						' LEFT JOIN ' . $user_table . ' as usr ON odr.`user_id` = usr.`user_id` '.
				   		' LEFT JOIN ' . $contract_table . ' as crt ON odr.`contract_sn` = crt.`contract_num` ' .
						$where_str;
			$resultTotal = $GLOBALS['db']->getRow($total_sql);

			if( $resultTotal )
			{
				$orders = $orders ? $orders : array();
				//订单是否可取消
				foreach ($orders as &$v) {

					$child_total_sql = 'SELECT COUNT(*) AS `child_number` FROM ' . $order_table . ' WHERE `parent_order_id` = ' .
							 		   $v['order_id'] . ' AND `child_order_status` <> ' . SOS_UNCONFIRMED .
							   ' AND `child_order_status` <> ' . SOS_CANCEL;
					$child_total = $GLOBALS['db']->getRow( $child_total_sql );

					if( $child_total['child_number'] == 0 ){
						$v['is_cancel'] = 'yes';
					}else{
						$v['is_cancel'] = 'no';
					}

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
		 * 接口名称：子订单列表
		 * 接口地址：http://admin.zj.dev/admin/OrderInfoModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters)：
	     *  {
	     *      "entity": "order_info",
	     *      "command": "childerList",
	     *      "parameters": {
	     *      	"order_id":101//主订单id
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",//("0": 成功 ,"-1": 失败)
		 *	    "message": "订单物品拆分信息获取成功",
		 *	    "content":
		 *	    [
		 *	    {
		 *     		"order_id":49,//订单id
		 *     		"goods_id":339,//商品id
		 *     		"add_time":"2015/01/01",//拆单时间
		 *     		"contract_name":"ht222",//合同名称
		 *     		"order_sn":"2014120330115-20",//订单号
		 *     		"goods_name":"钢材24m",//物料名称
		 *     		"cat_name":"锚具",//物料类别
		 *     		"attr":"1/2/3",//规格/型号/牌号
		 *     		"goods_price":100,//单价
		 *     		"goods_number":20,//数量
		 *     		"order_status":0,//客户未验签
		 *     		"child_order_status":0//子订单状态
		 *	    }
		 *	    ]
		 *	}
		 */
		public function childerListAction()
		{
			$content = $this->content;
			$order_id = $content['parameters']['order_id'];

			if( empty( $order_id ) ){
				make_json_response('', '-1', '订单ID错误');
			}

			$order_info_table = $GLOBALS['ecs']->table('order_info');
			$contract_table = $GLOBALS['ecs']->table('contract');//合同
			$goods_table = $GLOBALS['ecs']->table('goods');//物料

			$category_table = $GLOBALS['ecs']->table('category');//物料类别
			$goods_attr_table = $GLOBALS['ecs']->table('goods_attr');//物料属性
			$order_goods_table = $GLOBALS['ecs']->table('order_goods');//订单商品

			//所有子订单id
			$all_order_sql = 'SELECT `order_id` FROM ' . $order_info_table . ' WHERE `parent_order_id` = ' . $order_id;
			$all_order_id = $GLOBALS['db']->getAll( $all_order_sql );
				
			if( empty( $all_order_id ) ){

				$content = array();
				$content['data'] = array();
				$content['total'] = 0;

				make_json_response($content, '0', '查询子订单成功');
			}	

			$all_order_id_arr = array();

			foreach ($all_order_id as $v) {
				$all_order_id_arr[] = $v['order_id'];
			}
			$all_order_ids = implode(',', $all_order_id_arr);



			$childer_order_sql = 'SELECT odr.`order_id`, odr.`child_order_status`, odr.`order_sn`, odr.`add_time`, odr.`order_status`, IFNULL(crt.`contract_name`, \'\') AS `contract_name`, ogd.`goods_id`, ' .
							     'ogd.`goods_name`, ogd.`goods_price`, ogd.`goods_number`, cat.`cat_name` FROM ' .
							     $order_goods_table . ' AS ogd LEFT JOIN ' .
							     $goods_table . ' AS g ON g.`goods_id` = ogd.`goods_id` LEFT JOIN ' .
							     $category_table . ' AS cat ON cat.`cat_id` = g.`cat_id` LEFT JOIN '.
							     $order_info_table . ' AS odr ON odr.`order_id` = ogd.`order_id` LEFT JOIN '.
							     $contract_table . ' AS crt ON crt.`contract_num` = odr.`contract_sn`' .
							     ' WHERE ogd.`order_id` IN(' . $all_order_ids . ')' . ' ORDER BY odr.`add_time` DESC';

			$childer_orders = $GLOBALS['db']->getAll( $childer_order_sql );

			$childer_goods_id_arr = array();
			foreach ($childer_orders as $v) {
				$childer_goods_id_arr[] = $v['goods_id'];
			}
			$childer_goods_ids = implode(',', $childer_goods_id_arr);

			$goods_attr_sql = 'SELECT `goods_id`, `attr_value` FROM ' . $goods_attr_table .' WHERE `goods_id` IN(' . $childer_goods_ids . ')';
			$goods_attr_arr = $GLOBALS['db']->getAll( $goods_attr_sql );

			$goods_attr_val = array();
			foreach ($goods_attr_arr as $v) {
				if( isset( $goods_attr_val[ $v['goods_id'] ] ) )
					array_push( $goods_attr_val[ $v['goods_id'] ], $v['attr_value'] );
				else
					$goods_attr_val[ $v['goods_id'] ] = array( $v['attr_value'] );
			}
			
			foreach( $goods_attr_val as &$v ){
				$v = implode('/', $v);
			}	
			unset($v);

			foreach ($childer_orders as &$v) {
				$v['attr'] = $goods_attr_val[ $v['goods_id'] ];
				$v['add_time'] = date('Y-m-d', $v['add_time']);
			}
			unset( $v );

			$content = array();
			$content['data'] = $childer_orders;
			$content['total'] = count( $all_order_id );
			make_json_response($content, '0', '子订单查询成功');
			
		}

		/**
		 * 接口名称：所有子订单
		 * 接口地址：http://admin.zj.dev/admin/OrderInfoModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters)：
	     *  {
	     *      "entity": "order_info",
	     *      "command": "searchChilderList",
	     *      "parameters": {
	     *          "params": {
	     *              "where": {
	     *              	"like":{"order_sn":"no11232","user_name":"郭某某","contract_name":"xxxxx需求合同"},//订单号 客户名称 合同名称
	     *                  "child_order_status": 0,//验签状态
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
		 *		"error": "0",//("0": 成功 ,"-1": 失败)
		 *	    "message": "订单物品拆分信息获取成功",
		 *	    "content":
		 *	    [
		 *	    {
		 *     		"order_id":49,//订单id
		 *     		"goods_id":339,//商品id
		 *     		"add_time":"2015/01/01",//拆单时间
		 *     		"contract_name":"ht222",//合同名称
		 *     		"order_sn":"2014120330115-20",//订单号
		 *     		"goods_name":"钢材24m",//物料名称
		 *     		"cat_name":"锚具",//物料类别
		 *     		"attr":"1/2/3",//规格/型号/牌号
		 *     		"goods_price":100,//单价
		 *     		"goods_number":20,//数量
		 *     		"child_order_status":0//订单未确认
		 *	    }
		 *	    ]
		 *	}
		 *	
		 */
		public function searchChilderListAction(){
			$content = $this->content;
			$params = $content['parameters']['params'];

			$order_info_table = $GLOBALS['ecs']->table('order_info');
			$contract_table = $GLOBALS['ecs']->table('contract');//合同
			$goods_table = $GLOBALS['ecs']->table('goods');//物料

			$category_table = $GLOBALS['ecs']->table('category');//物料类别
			$goods_attr_table = $GLOBALS['ecs']->table('goods_attr');//物料属性
			$order_goods_table = $GLOBALS['ecs']->table('order_goods');//订单商品

			//搜索条件
			if( isset( $params["where"] ) && isset( $params["where"]['customer_id'] ) )
				$customer_id = trim( $params["where"]['customer_id'] );

			$where = array();
			if( isset($params['where']) )
				$where = $params['where'];

			$where_str = '';

			if( isset( $where["like"] ) )
				{
				$like = $where["like"];
				if ( isset( $like['user_name'] ) )//条件包含客户名的(先查users表，再根据结果中的user_id查找)
				{
					$user_table = $GLOBALS['ecs']->table('users');
					$users_sql = 'SELECT `user_id` FROM ' . $user_table . ' WHERE `companyName` like \'%' . $like["user_name"] . '%\'';
					$resultUsers = $GLOBALS['db']->getAll($users_sql);
					
					if( empty( $resultUsers ) )
					{
						$content = array();
						$content['data'] = array();
						$content['total'] = 0;
						make_json_response( $content , '0', '未找到符合条件的订单');
					}
					else
					{

						$users = array();
						foreach($resultUsers as $u)
						{
							$users[] = $u['user_id'];
						}
						
						$users_ids = implode(',', $users);
						$where_str = ' WHERE odr.`user_id` in(' . $users_ids . ')';
					}
				}

				if ( isset( $like['contract_name'] ) )//根据合同名搜索
				{
					$contract_table = $GLOBALS['ecs']->table('contract');
					$contract_sql = 'SELECT `contract_num` FROM ' . $contract_table . ' WHERE `contract_name` like \'%' . $like["contract_name"] . '%\'';
					$contract = $GLOBALS['db']->getAll( $contract_sql );

					if( empty( $contract ) ){

						$content = array();
						$content['data'] = array();
						$content['total'] = 0;

						make_json_response( $content, '0', '未找到符合条件的订单');
					}

					$contracts = array();
					foreach ($contract as $c) {
						$contracts[] = "'" . $c['contract_num'] . "'";
					}

					$contracts_str = implode(',', $contracts);

					if( $where_str )
						$where_str .= " AND odr.`contract_sn` IN(" . $contracts_str . ")";
					else
						$where_str .= " WHERE odr.`contract_sn` IN(" . $contracts_str . ")";				

				}

				if ( isset( $like['order_sn'] ) )//根据订单号搜索
				{
					$like['order_sn'] = trim($like['order_sn']);

					if( $where_str )
						$where_str .= " AND odr.`order_sn` LIKE '%" . $like['order_sn'] . "%'";
					else
						$where_str .= " WHERE odr.`order_sn` LIKE '%" . $like['order_sn'] . "%'";
				}

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

			if( isset( $where['child_order_status'] ) )
			{
				$where['child_order_status'] = intval( $where['child_order_status'] );

				if( $where_str )
					$where_str .= " AND odr.`child_order_status` = " . $where['child_order_status'];
				else
					$where_str .= " WHERE odr.`child_order_status` = " . $where['child_order_status'];
			}

			if( $where_str )
				$where_str .= " AND odr.`parent_order_id` IS NOT NULL AND  odr.`parent_order_id` <> 0";
			else
				$where_str .= " WHERE odr.`parent_order_id` IS NOT NULL AND  odr.`parent_order_id` <> 0";


			//所有子订单id
			$total_sql = 'SELECT COUNT(*) AS `total` FROM ' . $order_info_table . ' AS odr ' . $where_str;
			$total_data = $GLOBALS['db']->getRow( $total_sql );
			$total = $total_data['total'];

			if( empty( $total ) ){

				$content = array();
				$content['data'] = array();
				$content['total'] = 0;

				make_json_response($content, '0', '查询子订单成功');
			}	

			$order_str = ' ORDER BY odr.`add_time` DESC';
			$limit_str = ' LIMIT ' . intval( $params['limit'] ) . ',' . intval( $params['offset'] );

			$childer_order_sql = 'SELECT odr.`order_id`, odr.`order_sn`, odr.`add_time`, odr.`child_order_status`, odr.`order_status`, IFNULL(crt.`contract_name`, \'\') AS `contract_name`, ogd.`goods_id`, ' .
							     'ogd.`goods_name`, ogd.`goods_price`, ogd.`goods_number`, cat.`cat_name` FROM ' .
							     $order_goods_table . ' AS ogd LEFT JOIN ' .
							     $goods_table . ' AS g ON g.`goods_id` = ogd.`goods_id` LEFT JOIN ' .
							     $category_table . ' AS cat ON cat.`cat_id` = g.`cat_id` LEFT JOIN '.
							     $order_info_table . ' AS odr ON odr.`order_id` = ogd.`order_id` LEFT JOIN '.
							     $contract_table . ' AS crt ON crt.`contract_num` = odr.`contract_sn` ' .
							     $where_str . $order_str . $limit_str;

			$childer_orders = $GLOBALS['db']->getAll( $childer_order_sql );

			$childer_goods_id_arr = array();
			foreach ($childer_orders as $v) {
				$childer_goods_id_arr[] = $v['goods_id'];
			}
			$childer_goods_ids = implode(',', $childer_goods_id_arr);

			$goods_attr_sql = 'SELECT `goods_id`, `attr_value` FROM ' . $goods_attr_table .' WHERE `goods_id` IN(' . $childer_goods_ids . ')';
			$goods_attr_arr = $GLOBALS['db']->getAll( $goods_attr_sql );

			$goods_attr_val = array();
			foreach ($goods_attr_arr as $v) {
				if( isset( $goods_attr_val[ $v['goods_id'] ] ) )
					array_push( $goods_attr_val[ $v['goods_id'] ], $v['attr_value'] );
				else
					$goods_attr_val[ $v['goods_id'] ] = array( $v['attr_value'] );
			}
			
			foreach( $goods_attr_val as &$v ){
				$v = implode('/', $v);
			}	
			unset($v);

			foreach ($childer_orders as &$v) {
				$v['attr'] = $goods_attr_val[ $v['goods_id'] ];
				$v['add_time'] = date('Y-m-d', $v['add_time']);
			}
			unset( $v );

			$content = array();
			$content['data'] = $childer_orders;
			$content['total'] = $total;
			make_json_response($content, '0', '子订单查询成功');

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
		 *	        	"total_price":10000,//发货总价
		 *	        	"finance":200,//金融费用
		 *	        	"split_number":1,//拆单数量
		 *	        	"shipping_price_info":100/元/吨,//物流费用详细信息
		 *	        	"shipping_price":100,//物流费用
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
			$shipping_price_table = $GLOBALS['ecs']->table('shipping_price');

			//商品详情
			$good_sql = 'SELECT og.`goods_name`, og.`goods_price_send_buyer` as `goods_price`, og.`goods_number`, og.`send_number`, og.`goods_sn`, s.`suppliers_name`, s.`suppliers_id`, ' .
						' o.`contract_sn`, ' .
						' IFNULL(c.rate,0.00) AS `rate`, g.`cat_id` FROM ' . $order_goods_table . 
						' AS og LEFT JOIN ' . $goods_table . ' AS g ON g.`goods_id` = og.`goods_id` LEFT JOIN ' . $suppliers_table .
						' AS s ON s.`suppliers_id` = g.`suppliers_id` ' . ' LEFT JOIN ' . $order_info_table .' AS o ON o.`order_id` = og.`order_id` ' .
						' LEFT JOIN ' . $contract_table . ' AS c ON c.`contract_num` = o.`contract_sn` ' .
						' WHERE og.`goods_id` = ' . $goods_id . ' AND og.`order_id` = ' . $order_id . ' AND s.`suppliers_id` IS NOT NULL';
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

			//物流费用
			$shipping_price_sql = 'SELECT ifnull( `shipping_fee`, \'\') AS `shipping_fee` FROM ' . $shipping_price_table . ' WHERE `goods_category_id` = ' .
								  $goods['cat_id'] . ' AND `suppliers_id` = ' . $goods['suppliers_id'];
			$shipping_price = $GLOBALS['db']->getRow( $shipping_price_sql );
			$order['shipping_price_info'] = '';
			$order['shipping_price'] = 0;
			
			if( !empty( $shipping_price ) ){

				$m_arr = array();
				if ( preg_match( '/[\d]+[\.]*[\d]*/', $shipping_price['shipping_fee'], $m_arr ) ){
					$order['shipping_price'] = $m_arr[0];
				}
				$order['shipping_price_info'] = $shipping_price['shipping_fee'];
			}
			
			$goods['remain_number'] = $goods['goods_number'] - $goods['send_number'];//未拆单
			$order['split_number'] = $goods['remain_number'];//拆单数量
			$order['finance'] = round( $goods['goods_price'] * $goods['split_number'] * $goods['rate'], 2 );//金融费
			$order['total_price'] = round($order['finance'] + $order['shipping_price'] + $goods['goods_price'] * $order['split_number'], 2 );//发货总价
			$order['contract_sn'] = strval( $goods['contract_sn'] );
			//订单详情
			//该类商品的供应商列表
			$suppliers_sql = 'SELECT s.`suppliers_id`, s.`suppliers_name` FROM ' . $goods_table .
							 ' AS g LEFT JOIN ' . $suppliers_table . ' AS s ON g.`suppliers_id` = s.`suppliers_id`' .
							 ' WHERE g.`cat_id` = ' . $goods['cat_id'] .
							 ' AND s.`suppliers_id` IS NOT NULL GROUP BY s.`suppliers_id`';
			$suppliers = $GLOBALS['db']->getAll( $suppliers_sql );
			if( empty( $suppliers ) )
				$suppliers = array();

			//支付方式
			/*$payment_sql = 'SELECT `pay_id`, `pay_name` FROM ' . $payment_table;
			$payment = $GLOBALS['db']->getAll( $payment_sql );
			if( empty( $payment ) )
				$payment = array();*/

			$payment = array();
			$pay_cfg = C('payment');
			foreach ($pay_cfg as $pay_id => $pay_name) {
				$payment[] = array( 'pay_id' => $pay_id,
							   		'pay_name' => $pay_name
				 );
			}

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
	     *      	"shipping_fee_send_buyer":120,//物流费用
	     *      	"shipping_fee_send_saler":120,//物流费用
	     *      	"financial_send_rate":0.0001,//金融费率
	     *      	"financial_send":20,//金融费用
	     *      	"pay_id":1,//支付方式
	     *      	"suppliers":1//供应商id
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "订单物品拆分成功",
		 *	    "content": {}
		 *	}   
		 */	        	
		public function splitAction()
		{
			$content = $this->content;
			$order_id = $content['parameters']['order_id'];
			$goods_id = $content['parameters']['goods_id'];
			$send_number = ( double )( $content['parameters']['send_number'] );
			$goods_price = ( double )( $content['parameters']['goods_price'] );
			$shipping_fee = ( double )( $content['parameters']['shipping_fee'] );
			$finance_rate = ( double )( $content['parameters']['financial_send_rate'] / 100);//金融费率
			$suppliers_id = intval( $content['parameters']['suppliers'] );//供应商id
			$finance_manual = $finance = ( double )( $content['parameters']['financial_send'] );
			$pay_id = $content['parameters']['pay_id'];

			if( empty( $order_id) )
				make_json_response('', '-1', '订单id错误');
			if( empty( $suppliers_id) )
				make_json_response('', '-1', '请选择供应商');
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
			
			if( $parent_order['parent_order_id'] || $parent_order['parent_order_sn'] ){
				make_json_response('', '-1', '已经拆分的子订单不能被再次拆分');
			}
			
			//*****************金融费计算 BEGIN **************
			$contract_sn = $parent_order['contract_sn'];
			if( empty( $finance) ){
				$contract_sql = 'SELECT `bill_amount_valid`,`cash_amount_valid` FROM ' . $contract_table .' WHERE `contract_num` = \'' .
						  $contract_sn . '\' LIMIT 1';
				$contract_amount = $GLOBALS['db']->getRow( $contract_sql );//合同额度查询

				$finance = 0;

				if( !empty( $contract_amount ) ){
					$goods_total_price = $goods_price * $send_number;
					if( $goods_total_price <= $contract_amount['cash_amount_valid'] )
					{
						$finance = 0;

					}else{
						$goods_total_price -= $contract_amount['cash_amount_valid'];
						$finance = round( ( $goods_total_price * $finance_rate ) / 100 , 2);
					}
					
				}

			}

			if( empty( $finance_rate ) ){
				$finance_rate = $finance / ( $send_number * $goods_price ) / 100;
			}
			//*****************金融费计算 END **************


			//***************** 获取供应商报价 BEGIN *************
			$cat_sql = 'SELECT `cat_id` FROM ' . $goods_table . ' WHERE `goods_id` = ' . $goods_id;
			$cat_id = $GLOBALS['db']->getOne( $cat_sql );
			$goods_price_send_saler = $this->_getSuppliersPrice($suppliers_id, $goods_id, $cat_id);
			//***************** 获取供应商报价 END *************

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

            $childer_order['shipping_fee'] = $shipping_fee;

            $childer_order['shipping_fee_send_buyer'] = $shipping_fee;
            $childer_order['shipping_fee_send_saler'] = $shipping_fee;
            $childer_order['shipping_fee_arr_buyer'] = 0;
            $childer_order['shipping_fee_arr_saler'] = 0;

            $childer_order['invoice_no'] = '';
            $childer_order['money_paid'] = 0;

            $childer_order['financial_send_rate'] = $finance_rate * 100;
            $childer_order['financial_arr_rate'] = 0;
            $childer_order['financial_send'] = $finance_manual;//只保存手动更改的金融费
            $childer_order['financial_arr'] = 0;


            $childer_order['suppers_id'] = $suppliers_id;
            $childer_order['child_order_status'] = SOS_UNCONFIRMED;//子订单状态-未确认
            $childer_order['order_amount'] = $send_number * $goods_price + $shipping_fee + $finance;

            $childer_order['order_amount_send_buyer'] = $childer_order['order_amount'];

            //***************采购发货信息 BEGIN***************
			if( $goods_price_send_saler ){//供应商选择，更新单价、金额
				$order_good['goods_price_send_saler'] = $goods_price_send_saler;
			}else{
				$goods_price_send_saler = $order_good['goods_price_send_saler'];
			}

			$order_amount_send_saler = $goods_price_send_saler * $send_number + $shipping_fee;
			$childer_order['order_amount_send_saler'] = $order_amount_send_saler;
			//***************采购发货信息 END***************
				
            $childer_order['order_amount_arr_buyer'] = 0;
            $childer_order['order_amount_arr_saler'] = 0;

			unset( $childer_order['order_id'] );
			
			$childer_order_sql = 'INSERT INTO ' . $order_info_table .'(';
			$childer_order_keys = array_keys( $childer_order );
			
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
			$new_order_id = $GLOBALS['db']->insert_id();

			if( $createOrder ){
				//子订单商品
				$insert_order_id = $GLOBALS['db'] -> insert_id(); // 获取新插入子订单的订单id

				unset( $order_good['rec_id'] );
				$order_good['order_id'] = $insert_order_id;
				$order_good['goods_number'] = $send_number;
				// $order_good['goods_price'] = $goods_price;
				$order_good['send_number'] = $send_number;
				// $order_good['contract_price'] = $goods_price;
				$order_good['contract_number'] = $send_number;
				// $order_good['check_price'] = $goods_price;
				$order_good['check_number'] = $send_number;
				$order_good['goods_number_send_buyer'] = $send_number;//发货数量
				$order_good['goods_price_send_buyer'] = $goods_price;//发货单价						       		

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
										' WHERE `order_id` = ' . $order_id . ' AND `goods_id` = ' . $goods_id . ' LIMIT 1';
					$order_info_update = $GLOBALS['db']->query( $order_info_sql );
					
					$parent_order_sql = ' UPDATE ' . $order_info_table . ' SET `order_status` = ' .
							       		POS_HANDLE . ' WHERE `order_id` = ' . $parent_order['order_id'] . ' LIMIT 1'; //父订单状态-处理中
					$parent_order_update = $GLOBALS['db']->query( $parent_order_sql );		       		
						

					if( $order_info_update && $parent_order_update ){					
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

		/**
		 * 接口名称：子订单详情
		 * 接口地址：http://admin.zj.dev/admin/OrderInfoModel.php
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
		 *	        	"goods_number_send_buyer":90,//销售信息.发货数量 = 子订单数量
		 *	        	"goods_price_send_buyer":20, //销售信息.发货单价 = 采购信息.发货单价 + 加价幅度
		 *	        	"shipping_fee_send_buyer":120,//销售信息.发货物流费
		 *	        	"financial_send":100,//销售信息.发货金融费
		 *	        	"order_amount_send_buyer":1800,//销售信息.发货总金额 = 物流费 + 金融费 + 单价 * 数量
		 *
		 *          	"goods_number_arr_buyer":90,//销售信息.到货数量 = 实际到货数量
		 *	        	"goods_price_arr_buyer":20, //销售信息.到货单价 = 销售信息.发货单价
		 *	        	"shipping_fee_arr_buyer":120,//销售信息.到货物流费
		 *	        	"financial_arr":100,//销售信息.到货金融费
		 *	        	"order_amount_arr_buyer":1800,//销售信息.到货总金额 = 物流费 + 金融费 + 单价 * 数量
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
			$order_sql = 'SELECT odr.`order_status`, odr.`child_order_status`, odr.`order_sn`, odr.`user_id`, usr.`user_name`, usr.`companyName` as `company_name`, odr.`add_time`, odr.`pay_id`, odr.`contract_sn`, '. "ifnull(crt.contract_name, '') as contract_name," .//订单相关
						 ' odr.`consignee`, odr.`address`, odr.`mobile`, odr.`sign_building`, ' .
					 	 ' odr.`inv_type`, odr.`inv_payee`, odr.`inv_bank_name`, odr.`inv_bank_account`, odr.`inv_bank_address`, odr.`inv_tel`, odr.`inv_fax`, ' .	//发票相关
					 	 ' odr.`shipping_fee_send_buyer`, odr.`shipping_fee_arr_buyer`, odr.`shipping_fee_send_saler`, odr.`shipping_fee_arr_saler`, ' .//商品资料
					 	 ' odr.`financial_send`, odr.`financial_arr`, ' .//金融费
					 	 ' odr.`order_amount_send_buyer`, odr.`order_amount_arr_buyer`, odr.`order_amount_send_saler`, odr.`order_amount_arr_saler`, ' .//总金额
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
			$order_goods_sql = 'SELECT og.`goods_id`, og.`goods_name`, og.`goods_number_send_buyer`, og.`goods_number_send_saler`, og.`goods_number_arr_buyer`, ' .
							   'og.`goods_number_arr_saler`, og.`goods_price_send_buyer`, og.`goods_price_send_saler`, og.`goods_price_arr_buyer`, og.`goods_price_arr_saler` FROM ' .
							   $order_goods_table . //物料编码 名称 下单数 供应商
							   ' AS og LEFT JOIN '. $goods_table . ' AS g ON og.`goods_id` = g.`goods_id` ' .
						 	   'LEFT JOIN ' . $suppliers_table . ' AS sp ON g.`suppliers_id` = sp.`suppliers_id`' . 
							   ' WHERE og.`order_id` = ' . $order_id;
			$order_good = $GLOBALS['db']->getRow($order_goods_sql);

			$order_info['add_time'] = date('Y-m-d H:i:s', $order_info['add_time']);

			if( !empty( $order_good ) ){
				//未拆单 小计
				// foreach($order_goods_arr as &$order_good){
					$order_good['remain_number'] = $order_good['goods_number'] - $order_good['send_number'];//未拆单
					$order_good['subtotal'] = $order_good['goods_price'] * $order_good['goods_number'];//小计
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
				// }
				// unset( $order_good );
				
				//采购信息
				// $order_good['goods_price_arr_saler'] = $order_good['goods_price_send_saler'];//销售订单 商品记录 价格
				// $order_good['goods_number_send_saler'] = $order_good['goods_number_send_buyer'];//销售订单 商品记录 数量
				// $order_good['goods_number_arr_saler'] = $order_good['goods_number_arr_buyer'];//销售订单 商品记录 数量
				$order_good['shipping_fee_send_saler'] = $order_info['shipping_fee_send_saler'];//订单记录 物流
				$order_good['shipping_fee_arr_saler'] = $order_info['shipping_fee_arr_saler'];//订单记录 物流

				// $payment_cfg = C( 'payment' );
				// $order_good['payment_arr'] = $order_good['payment_send'] = $payment_cfg[ $order_info['pay_id'] ];
				$order_good['order_amount_send_saler'] = $order_info['order_amount_send_saler'];//采购订单 发货总金融
				$order_good['order_amount_arr_saler'] = $order_info['order_amount_arr_saler'];//采购订单 到货总金融

				unset($order_info['shipping_fee_send_saler']);
				unset($order_info['shipping_fee_arr_saler']);
				unset($order_info['order_amount_send_saler']);
				unset($order_info['order_amount_arr_saler']);

				//销售信息
				// $order_good['goods_price_arr_buyer'] = $order_good['goods_price_send_buyer'];//销售订单 商品记录 加价后价格
				$order_good['shipping_fee_send_buyer'] = $order_info['shipping_fee_send_buyer'];//销售订单 物流
				$order_good['shipping_fee_arr_buyer'] = $order_info['shipping_fee_arr_buyer'];//销售订单 物流
				$order_good['financial_send'] = $order_info['financial_send'];//销售订单 发货金融费
				$order_good['financial_arr'] = $order_info['financial_arr'];//销售订单 到货金融费
				
				$order_good['order_amount_send_buyer'] = $order_info['order_amount_send_buyer'];//销售订单 发货总金融
				$order_good['order_amount_arr_buyer'] = $order_info['order_amount_arr_buyer'];//销售订单 到货总金融

				unset($order_info['shipping_fee_send_buyer']);
				unset($order_info['shipping_fee_arr_buyer']);
				unset($order_info['financial_send']);
				unset($order_info['financial_arr']);

				unset($order_info['order_amount_send_buyer']);
				unset($order_info['order_amount_arr_buyer']);

				//物流
				// $shipping = empty( $order_info['shipping_info'] ) ? array() : json_decode( $order_info['shipping_info'], true);
				// $shipping['log'] = empty( $order_info['shipping_log'] ) ? array() : json_decode( $order_info['shipping_log'], true);
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


				$sale_status = C('sale_status');//销售订单状态,用于页面显示
				$childer_order_status = C('childer_order_status');//验签状态
				switch ( $order_info['child_order_status'] ) {
					case SOS_UNCONFIRMED://未确认
						$order_info['order_status'] = $sale_status[SALE_ORDER_UNCONFIRMED];
						$order_info['check_status'] = $childer_order_status[SOS_UNCONFIRMED];
						$buttons = array('发货改价', '确认', '撤销订单');
						break;
					case SOS_CONFIRMED://已确认
						$order_info['order_status'] = $sale_status[SALE_ORDER_CONFIRMED];
						$order_info['check_status'] = $childer_order_status[SOS_CONFIRMED];
						$buttons = array('发货改价', '撤销订单');
						break;
					case SOS_SEND_CC://客户已验签(发货)
						$order_info['order_status'] = $sale_status[SALE_ORDER_CONFIRMED];
						$order_info['check_status'] = $childer_order_status[SOS_SEND_CC];
						$buttons = array('发货验签', '发货改价', '取消验签');
						break;
					case SOS_SEND_PC://平台已验签(发货)
						$order_info['order_status'] = $sale_status[SALE_ORDER_CONFIRMED];
						$order_info['check_status'] = $childer_order_status[SOS_SEND_PC];
						$buttons = array('采购下单');//'取消验签',
						break;
					case SOS_SEND_PP://平台已推单(发货)
						$order_info['order_status'] = $sale_status[SALE_ORDER_CONFIRMED];
						$order_info['check_status'] = $childer_order_status[SOS_SEND_PP];
						$buttons = array('到货改价');//销售价格修改
						break;	

					//************************** 采购发货中 BEGIN **************************
					case SOS_SEND_SC://供应商已验签(发货)
						$order_info['order_status'] = $sale_status[SALE_ORDER_CONFIRMED];
						$order_info['check_status'] = $childer_order_status[SOS_SEND_SC];
						$buttons = array();
						break;
					case SOS_SEND_PC2://平台已验签(发货)
						$order_info['order_status'] = $sale_status[SALE_ORDER_CONFIRMED];
						$order_info['check_status'] = $childer_order_status[SOS_SEND_PC2];
						$buttons = array();
						break;
					//************************** 采购发货中 END **************************
					
					case SOS_ARR_CC://客户已验签(到货)
						$order_info['order_status'] = $sale_status[SALE_ORDER_UNRECEIVE];
						$order_info['check_status'] = $childer_order_status[SOS_ARR_CC];
						$buttons = array('到货验签', '到货改价');
						break;
					case SOS_ARR_PC://平台已验签(到货)
						$order_info['order_status'] = $sale_status[SALE_ORDER_COMPLETE];
						$order_info['check_status'] = $childer_order_status[SOS_ARR_PC];
						$buttons = array();
						break;

					//************************** 采购到货中 BEGIN **************************
					case SOS_ARR_SC://供应商已验签(到货)
						$order_info['order_status'] = $sale_status[SALE_ORDER_COMPLETE];
						$order_info['check_status'] = $childer_order_status[SOS_ARR_SC];
						$buttons = array();
						break;
					case SOS_ARR_PC2://平台已验签(到货)
						$order_info['order_status'] = $sale_status[SALE_ORDER_COMPLETE];
						$order_info['check_status'] = $childer_order_status[SOS_ARR_PC2];
						$buttons = array();
						break;
					case SOS_CANCEL://验签已取消
						$order_info['order_status'] = $sale_status[SALE_ORDER_CANCEL];
						$order_info['check_status'] = $childer_order_status[SOS_CANCEL];
						$buttons = array();
						break;
					//************************** 采购到货中 END **************************
					
					default://未命名状态
						$cs = $order_info['child_order_status'];
						$order_info['order_status'] = $sale_status[SALE_ORDER_UNCONFIRMED];
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

			make_json_response($content, '0', '子订单详情查询成功');

		}

		/**
		 * 接口名称:子订单详情- 平台操作按钮对应的接口("确认"  "发货验签" "取消验签"...)
		 * 接口地址：http://admin.zj.dev/admin/OrderInfoModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params)：
	     *  {
	     *      "entity": "order_info",
	     *      "command": "updateChilderStatus",
	     *      "parameters": {
	     *          "params": {
	     *          	"order_id":101//订单ID
	     *           	"button":"确认"//按钮名称（可能的名称有: 确认 发货验签、取消验签 到货验签 ）
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "确认成功",
		 *	    "content": {}
		 *  }
		 * @return [type] [description]
		 */
		public function updateChilderStatusAction()
		{
			$content = $this->content;
			$params = $content['parameters']['params'];

			if( !isset( $params['order_id'] ) ){
				make_json_response('', '-1', '订单ID错误');
			}
			$order_id = intval( $params['order_id'] );

			$order_goods_table = $GLOBALS['ecs']->table('order_goods');
			$order_info_table = $GLOBALS['ecs']->table('order_info');
			$contract_table = $GLOBALS['ecs']->table('contract');

			//检查订单状态
			$order_info_sql = 'SELECT odr.`child_order_status`, odr.`suppers_id`, odr.`parent_order_id`, odr.`order_amount_send_buyer`, odr.`order_amount_arr_buyer`,' .
							  ' odr.`contract_sn` ,odr.`cash_used` , odr.`bill_used`, og.`goods_id`, og.`goods_number` FROM ' . $order_info_table .
							  ' AS odr LEFT JOIN ' . $order_goods_table . ' AS og ON odr.`order_id` = og.`order_id` ' .
					 		  ' WHERE odr.`order_id` = ' . $order_id;
			$order_status = $GLOBALS['db']->getRow( $order_info_sql );
			if( !$order_status ){
				make_json_response('', '-1', '订单不存在');
			}

			//子订单状态更改
			$childer_order_update_sql = 'UPDATE ' . $order_info_table . ' SET `child_order_status` = %d, `order_status` = %d WHERE `order_id` = ' . $order_id;

			$buttons = trim($params['button']);
			switch ( $buttons ) {
				case '确认':
					if( $order_status['child_order_status'] == SOS_UNCONFIRMED ){
						$childer_order_update_sql = sprintf($childer_order_update_sql, SOS_CONFIRMED, SALE_ORDER_CONFIRMED);
						$childer_order_update = $GLOBALS['db']->query( $childer_order_update_sql );

						if( $childer_order_update )
							make_json_response('', '0', '确认 成功');
						else
							make_json_response('', '-1', '确认 失败');
					}		

					break;
				case '发货验签':
					if( $order_status['child_order_status'] == SOS_SEND_CC ){
						$childer_order_update_sql = sprintf($childer_order_update_sql, SOS_SEND_PC, SALE_ORDER_UNRECEIVE);
						$childer_order_update = $GLOBALS['db']->query( $childer_order_update_sql );

						if( $childer_order_update )
							make_json_response('', '0', '发货验签 成功');
						else
							make_json_response('', '-1', '发货验签 失败');
					}					
					break;
				case '取消验签':
					
					if( $order_status['child_order_status'] == SOS_SEND_CC ){//客户已验签
						$childer_order_update_sql = 'UPDATE ' . $order_info_table . ' SET `child_order_status` = %d, `order_status` = %d, `bill_used` = 0, `cash_used`= 0' . ' WHERE `order_id` = ' . $order_id;
						$childer_order_update_sql = sprintf($childer_order_update_sql, SOS_CONFIRMED, SALE_ORDER_CONFIRMED);

					}else{

						break;
					}

					//额度回滚到现金
					//现金、票据各回滚到对应的数据中
					//待修改2016-02-19
					// $return_amount = $order_status['order_amount_send_buyer'];
					$update_contract_sql = ' UPDATE ' . $contract_table . ' SET `cash_amount_valid` = `cash_amount_valid` + ' . $order_status['cash_used'] .
											', `bill_amount_valid` = `bill_amount_valid` + ' . $order_status['bill_used'] . ' WHERE `contract_num` = ' . $order_status['contract_sn'] . ' LIMIT 1';;
					$GLOBALS['db']->query( $update_contract_sql );

					$childer_order_update = $GLOBALS['db']->query( $childer_order_update_sql );

					if( $childer_order_update )
						make_json_response('', '0', '取消验签 成功');
					else
						make_json_response('', '-1', '取消验签 失败');

					break;
				case '采购下单':

					if( !$order_status['suppers_id'] ){
						make_json_response('', '-1', '未分配供应商，不能推单');
					}

					if(  $order_status['child_order_status'] == SOS_SEND_PC ){
						$childer_order_update_sql = sprintf($childer_order_update_sql, SOS_SEND_PP, SALE_ORDER_UNRECEIVE );
					}

					$childer_order_update = $GLOBALS['db']->query( $childer_order_update_sql );

					if( $childer_order_update )
						make_json_response('', '0', '采购下单 成功');
					else
						make_json_response('', '-1', '采购下单 失败');
					break;
				case '到货验签':
					if( $order_status['child_order_status'] == SOS_ARR_CC ){
						$childer_order_update_sql = sprintf($childer_order_update_sql, SOS_ARR_PC, SALE_ORDER_COMPLETE);
						$childer_order_update = $GLOBALS['db']->query( $childer_order_update_sql );

						if( $childer_order_update )
							make_json_response('', '0', '到货验签 成功');
						else
							make_json_response('', '-1', '到货验签 失败');
					}					
					break;
				case '撤销订单'://更改子订单状态 恢复主订单商品对应子订单的数量 判断是否可以取消主订单
					if( $order_status['child_order_status'] >= SOS_SEND_PC ){//已验签的 无法取消订单
						break;
					}
					$childer_order_update_sql = 'UPDATE ' . $order_info_table . ' SET `child_order_status` = %d, `order_status` = %d, `bill_used` = 0, `cash_used`= 0' . ' WHERE `order_id` = ' . $order_id;//扣除归0
					$childer_order_update_sql = sprintf($childer_order_update_sql, SOS_CONFIRMED, SALE_ORDER_CONFIRMED);

					$childer_order_update = $GLOBALS['db']->query( $childer_order_update_sql );

					//恢复主订单扣除的数量
					//查询主订单商品的数量
					$order_sql = 'SELECT og.`send_number` FROM ' . $order_goods_table . ' AS og WHERE og.`goods_id` = ' .
								 $order_status['goods_id'] . ' AND og.`order_id` = ' . $order_status['parent_order_id'];
					$send_number_data = $GLOBALS['db']->getRow( $order_sql );

					$send_number_new = ( $send_number_data['send_number'] - $order_status['goods_number'] < 0 ) ? 0 : ( $send_number_data['send_number'] - $order_status['goods_number'] );
					
					$update_order_sql = 'UPDATE ' . $order_goods_table . ' SET `send_number` = ' . $send_number_new .
										' WHERE `order_id` = ' . $order_status['parent_order_id'] . ' AND `goods_id` =' . $order_status['goods_id'] . ' LIMIT 1';
					$update_parent = $GLOBALS['db']->query( $update_order_sql );//echo '  '	. $update_order_sql;
					//主订单是否可以取消(验证全部子订单的状态)
					$all_childer_status_sql = 'SELECT `child_order_status` FROM ' . $order_info_table . ' WHERE `parent_order_id` = ' . $order_status['parent_order_id'];
					$all_childer_status = $GLOBALS['db']->getAll( $all_childer_status_sql );

					//商城库存回滚
					$update_goods_sql = 'UPDATE ' . $GLOBALS['ecs']->table('goods') . ' SET `goods_number` = `goods_number` + ' . $order_status['goods_number'] .
										' WHERE `goods_id` = ' . $order_status['goods_id'];
					$GLOBALS['db']->query( $update_goods_sql );

					//票据和现金各回滚到对应的数据中
					//待修改2016-02-19
					$update_contract_sql = ' UPDATE ' . $GLOBALS['ecs']->table('contract') . ' SET `cash_amount_valid` = `cash_amount_valid` + ' . $order_status['cash_used'] .
											', `bill_amount_valid` = `bill_amount_valid` + ' . $order_status['bill_used'] . ' WHERE `contract_num` = ' . $order_status['contract_sn'] . ' LIMIT 1';
					$GLOBALS['db']->query( $update_contract_sql );//商城库存回滚

					$cancel = true;
					foreach ($all_childer_status as $c) {
						if ( $c['child_order_status'] != SOS_CANCEL ) {
							$cancel = false;
							break;
						}
					}

					if( $cancel ){//更新主订单状态 变为已取消
						$cancel_sql = 'UPDATE ' . $order_info_table . ' SET `order_status` = ' . POS_CANCEL .
										' WHERE `order_id` = ' . $order_status['parent_order_id'] . ' LIMIT 1';
						$update_parent = $GLOBALS['db']->query( $cancel_sql );
					}

					if( $childer_order_update && $update_parent )
						make_json_response('', '0', '撤销订单 成功');
					else
						make_json_response('', '-1', '撤销订单 失败');
					break;	
				default:
					
					break;
			}

			$childer_order_status = C('childer_order_status');
			// 状态提示
			$msg = '订单当前状态是 %s, 无法执行该操作';
			switch ( $order_status['child_order_status'] ) {
				case SOS_UNCONFIRMED:
					$msg = sprintf($msg, $childer_order_status[SOS_UNCONFIRMED]);
					break;
				case SOS_CONFIRMED:
					$msg = sprintf($msg, $childer_order_status[SOS_CONFIRMED]);
					break;
				case SOS_SEND_CC:
					$msg = sprintf($msg, $childer_order_status[SOS_SEND_CC]);
					break;
				case SOS_SEND_PC:
					$msg = sprintf($msg, $childer_order_status[SOS_SEND_PC]);
					break;
				case SOS_SEND_PP:
					$msg = sprintf($msg, $childer_order_status[SOS_SEND_PP]);
					break;
				case SOS_SEND_SC:
					$msg = sprintf($msg, $childer_order_status[SOS_SEND_SC]);
					break;
				case SOS_SEND_PC2:
					$msg = sprintf($msg, $childer_order_status[SOS_SEND_PC2]);
					break;

				case SOS_ARR_CC:
					$msg = sprintf($msg, $childer_order_status[SOS_ARR_CC]);
					break;
				case SOS_ARR_PC:
					$msg = sprintf($msg, $childer_order_status[SOS_ARR_PC]);
					break;		
				case SOS_ARR_SC:
					$msg = sprintf($msg, $childer_order_status[SOS_ARR_SC]);
					break;
				case SOS_ARR_PC2:
					$msg = sprintf($msg, $childer_order_status[SOS_ARR_PC2]);
					break;		
				case SOS_CANCEL:
					$msg = sprintf($msg, $childer_order_status[SOS_CANCEL]);
					break;	
				default:
					$msg = sprintf($msg, '未知');
					break;
			}
			make_json_response('', '-1', $msg);
		}

		/**
		 * 接口名称:自订单详情-添加物流
		 * 接口地址：http://admin.zj.dev/admin/OrderInfoModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params)：
	     *  {
	     *      "entity": "order_info",
	     *      "command": "addShippingInfo",
	     *      "parameters": {
	     *          "params": {
	     *          	"order_id":101//订单ID
	     *          	"company_name":"EMS",//物流公司
	     *          	"shipping_num":"e87694202100",//物流编号
	     *          	"tel":"021-62420011",//联系电话
	     *          	"shipping_time":"2017-01-01"//发货时间
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "物流添加成功",
		 *	    "content": {}
		 *	 }
		 */
		public function addShippingInfoAction()
		{
			$content = $this->content;
			$params = $content['parameters']['params'];

			if( !isset( $params['order_id'] ) ){
				make_json_response('', '-1', '订单ID错误');
			}
			$order_id = intval( $params['order_id'] );

			$company_name = strval( trim( $params['company_name'] ) );
			$shipping_num = strval( trim( $params['shipping_num'] ) );
			$tel = strval( trim( $params['tel'] ) );

			$shipping_time = strval( trim( $params['shipping_time'] ) );

			$order_info_table = $GLOBALS['ecs']->table('order_info');
			
			$shipping_info['company_name'] = urlencode($company_name);
			$shipping_info['shipping_num'] = $shipping_num;
			$shipping_info['tel'] = $tel;
			$shipping_info['shipping_time'] = $shipping_time;

			$shippinf_info_str = json_encode( $shipping_info );
			
			$add_shipping_info_sql = 'UPDATE ' . $order_info_table . ' SET `shipping_info` = \'' . $shippinf_info_str .
									 '\' WHERE `order_id` = ' . $order_id; 
			$add_shipping = $GLOBALS['db']->query( $add_shipping_info_sql );

			if( $add_shipping ){
				make_json_response('', '0', '物流添加成功');
			} else {
				make_json_response('', '-1', '物流添加失败');
			}

		}

		/**
		 * 接口名称:自订单详情-添加物流信息(物流动态)
		 * 接口地址：http://admin.zj.dev/admin/OrderInfoModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params)：
	     *  {
	     *      "entity": "order_info",
	     *      "command": "addShippingLog",
	     *      "parameters": {
	     *          "params": {
	     *          	"order_id":101,//订单ID
	     *          	"log":"物流记录"//物流记录
	     *          	"date":"2017-02-06"//内容日期(不填则默认为当前时间)
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "物流信息动态添加成功",
		 *	    "content": {}
		 *	 }
		 */
		public function addShippingLogAction()
		{
			$content = $this->content;
			$params = $content['parameters']['params'];

			if( !isset( $params['order_id'] ) ){
				make_json_response('', '-1', '订单ID错误');
			}
			$order_id = intval( $params['order_id'] );

			$order_info_table = $GLOBALS['ecs']->table('order_info');

			$log = strval( trim( $params['log'] ) );
			@$shipping_date = strval( trim( $params['date'] ) );
			if( empty( $shipping_date ) ){
				$shipping_date = date('Y-m-d H:i:s');
			}

			$shipping_log_sql = 'SELECT `shipping_log` FROM ' . $order_info_table . ' WHERE `order_id` = ' . $order_id;
			$shipping_log_old = $GLOBALS['db']->getRow( $shipping_log_sql );

			if( empty( $shipping_log_old ) ){
				make_json_response('', '-1', '订单不存在');
			}

			if( empty( $shipping_log_old['shipping_log'] ) ){
				$shipping_log = array();
			}else{
				$shipping_log = json_decode( $shipping_log_old['shipping_log'] );
			}
			$shipping_log_temp['content'] = urlencode( $log );
			$shipping_log_temp['date'] = $shipping_date;
			array_push( $shipping_log, $shipping_log_temp );

			$shipping_log_str = json_encode( $shipping_log );	

			$add_shipping_log_sql = 'UPDATE ' . $order_info_table . ' SET `shipping_log` = \'' . $shipping_log_str .
									 '\' WHERE `order_id` = ' . $order_id; 
			$add_shipping_log = $GLOBALS['db']->query( $add_shipping_log_sql );

			if( $add_shipping_log ){
				make_json_response('', '0', '物流信息动态添加成功');
			} else {
				make_json_response('', '-1', '物流信息动态添加失败');
			}			
		}

		/**
		 * 接口名称: 发货改价表单数据
		 * 接口地址：http://admin.zj.dev/admin/OrderInfoModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params)：
	     *  {
	     *      "entity": "order_info",
	     *      "command": "initPriceSend",
	     *      "parameters": {
	     *          "params": {
	     *          	"order_id":142//订单ID
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
	     *  	"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "发货改价信息获取成功",
		 *	    "content": 
		 *	    {
		 *	        "info":
		 *	        { 	
		 *	        	"order_status":"",//订单状态 0 '未确认', 1 '已确认', 2 '待收货', 3 '已完成', 4 '订单取消'
		 *	        	"order_sn":"os122311",//订单编号
		 *	        	"user_id":1,//下单人id 
		 *	        	"user_name":"王x",//下单人
		 *	        	"add_time":"2015-01-01:11",//拆单时间
		 *	        	"contract_sn":"10000",//合同号
		 *	        	"contract_name":"钢材销售",//合同名称
		 *	        	"company_name":"中铁一局",//公司名称
		 *          	"check_status":"",//验签状态
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
		 *	    	"form":
		 *	    	{
		 *	    		"order_id":142,
		 *	    	 	"goods_price_send_buyer":100,//客户价格.物料单价 
		 *	    	  	"goods_number_send_buyer":100,//客户价格.物料数量
		 *	    	   	"suppers_id"://客户价格.实际供应商列表
		 *	    	    [
		 *	    	    {
		 *	    		   "suppliers_id":1,//供应商id
		 *	    		   "suppliers_name":"天津天佑"//供应商名字
		 *	    	    }
		 *	    	    ],
		 *	    	    "default_suppliers_id":1,//供应商id
		 *	    	    "financial_send_rate":0.0001,//客户价格.金融费率 (数字)
		 *	    	    "shipping_fee_send_buyer":82,//客户价格.物流费用
		 *	    	    "financial_send":1,//客户价格.金融费用
		 *      	    "order_amount_send_buyer":200,//客户价格.发货总价
		 *      	    
		 *	    	 	"goods_price_send_saler":100,//供应商价格.物料单价
		 *	    	 	"goods_number_send_saler":100,//供应商价格.物料数量
		 *	    	  	"shipping_fee_send_saler":82,//供应商价格.物流费用
		 *	    	  	"order_amount_send_saler":82//供应商价格.发货总价
		 *	    	   	"pay_id"://支付方式列表
		 *	    	    [
		 *	    	    {
		 *	    		   "id":1,
		 *	    		   "name":"现金"
		 *	    	    }
		 *	    	    ]
		 *	    	 },
		 *	    	 "price_log":
		 *	    	 {
		 *	    	 [
		 *	    	 	"good_code":"010102010701",//物料代码
		 *	    	 	"goods_name":"钢圈",//物料名称
		 *	    	 	"goods_attr":"45/T450/gangjin",//规格/型号/材质
		 *	    	 	"suppliers_name":"天津钢铁",//供应商
		 *	    	 	"suppliers_price":2000,//供应商报价
		 *	    	 	"actual_price":1800,//实际单价
		 *	    	 	"shipping_fee":50,//物流费用
		 *	    	 	"financial":100,//金融费用
		 *	    	 	"total":2150,//总金额
		 *	    	 	"payment":"现金",//支付方式
		 *	    	 	"price_date":"2016-01-07 10:09:00"//报价日期
		 *	    	 ]
		 *	    	 }
		 *	    }  	
	     *  }
		 * 
		 */
		public function initPriceSendAction()
		{
			$content = $this->content;
			$params = $content['parameters']['params'];

			if( !isset( $params['order_id'] ) ){
				make_json_response('', '-1', '订单ID错误');
			}
			$order_id = intval( $params['order_id'] );

			$order_info_table = $GLOBALS['ecs']->table('order_info');
			$contract_table = $GLOBALS['ecs']->table('contract');//合同
			$goods_table = $GLOBALS['ecs']->table('goods');//物料

			$category_table = $GLOBALS['ecs']->table('category');//物料类别
			$goods_attr_table = $GLOBALS['ecs']->table('goods_attr');//物料属性
			$order_goods_table = $GLOBALS['ecs']->table('order_goods');//订单商品

			$user_table = $GLOBALS['ecs']->table('users');
			$suppliers_table = $GLOBALS['ecs']->table('suppliers');

			//子订单+商品信息
			$order_sql = 'SELECT og.`goods_id`, og.`goods_number_send_buyer`, og.`goods_number_send_saler`, og.`goods_price_send_buyer`, og.`goods_price_send_saler`,' .
						 ' o.`suppers_id` as `default_suppliers_id`, g.`cat_id`, ' .
						 ' o.`add_time`,o.`child_order_status`,o.`contract_sn`, o.`order_sn`, ifnull( c.`contract_name`, \'\' ) AS `contract_name`,u.`user_name`, u.`companyName` AS `company_name`,' .//订单信息
						 ' o.`consignee`,o.`address`,o.`mobile`,o.`sign_building`,o.`inv_type`,o.`inv_payee`,' .
						 ' o.`shipping_fee_send_buyer`, o.`financial_send`, o.`financial_send_rate`, o.`order_amount_send_buyer`,' .
						 ' o.`shipping_fee_send_saler`, o.`order_amount_send_saler` ' .
						 ' FROM ' . $order_info_table . ' AS o LEFT JOIN ' . 
						 $order_goods_table . ' AS og ON og.`order_id` = o.`order_id` LEFT JOIN ' .
						 $goods_table . ' AS g ON og.`goods_id` = g.`goods_id` ' .
						 ' LEFT JOIN ' . $user_table . ' AS u ON u.`user_id` = o.`user_id` ' .
						 // ' LEFT JOIN ' . $suppliers_table . ' AS s ON s.`suppliers_id` = o.`suppers_id` ' .
						 ' LEFT JOIN ' . $contract_table . ' AS c ON c.`contract_num` = o.`contract_sn` ' .
						 'WHERE o.`order_id` = ' . $order_id;
			$order_info = $GLOBALS['db']->getRow( $order_sql );

			if( empty( $order_info ) ){
				make_json_response('', '-1', '订单查询失败');
			}

			//订单详情
			$sale_status = C('sale_status');//销售订单状态,用于页面显示
			$childer_order_status = C('childer_order_status');//验签状态
			$order_info['add_time'] = date('Y-m-d H:i:s', $order_info['add_time']);
			switch ( $order_info['child_order_status'] ) {
					case SOS_UNCONFIRMED://未确认
						$order_info['order_status'] = $sale_status[SALE_ORDER_UNCONFIRMED];
						$order_info['check_status'] = $childer_order_status[SOS_UNCONFIRMED];
		
						break;
					case SOS_CONFIRMED://已确认
						$order_info['order_status'] = $sale_status[SALE_ORDER_CONFIRMED];
						$order_info['check_status'] = $childer_order_status[SOS_CONFIRMED];
		
						break;
					case SOS_SEND_CC://客户已验签(发货)
						$order_info['order_status'] = $sale_status[SALE_ORDER_CONFIRMED];
						$order_info['check_status'] = $childer_order_status[SOS_SEND_CC];
		
						break;
					case SOS_SEND_PC://平台已验签(发货)
						$order_info['order_status'] = $sale_status[SALE_ORDER_CONFIRMED];
						$order_info['check_status'] = $childer_order_status[SOS_SEND_PC];
		
						break;
					case SOS_SEND_PP://平台已推单(发货)
						$order_info['order_status'] = $sale_status[SALE_ORDER_CONFIRMED];
						$order_info['check_status'] = $childer_order_status[SOS_SEND_PP];
						break;
						
					case SOS_SEND_SC://供应商已验签(发货)
						$order_info['order_status'] = $sale_status[SALE_ORDER_CONFIRMED];
						$order_info['check_status'] = $childer_order_status[SOS_SEND_SC];
		
						break;
					case SOS_SEND_PC2://平台已验签(发货)
						$order_info['order_status'] = $sale_status[SALE_ORDER_CONFIRMED];
						$order_info['check_status'] = $childer_order_status[SOS_SEND_PC2];
		
						break;
					case SOS_ARR_CC://客户已验签(到货)
						$order_info['order_status'] = $sale_status[SALE_ORDER_UNRECEIVE];
						$order_info['check_status'] = $childer_order_status[SOS_ARR_CC];
		
						break;
					case SOS_ARR_PC://平台已验签(到货)
						$order_info['order_status'] = $sale_status[SALE_ORDER_COMPLETE];
						$order_info['check_status'] = $childer_order_status[SOS_ARR_PC];
		
						break;
					case SOS_ARR_SC://供应商已验签(到货)
						$order_info['order_status'] = $sale_status[SALE_ORDER_COMPLETE];
						$order_info['check_status'] = $childer_order_status[SOS_ARR_SC];
		
						break;
					case SOS_ARR_PC2://平台已验签(到货)
						$order_info['order_status'] = $sale_status[SALE_ORDER_COMPLETE];
						$order_info['check_status'] = $childer_order_status[SOS_ARR_PC2];
		
						break;
					case SOS_CANCEL://验签已取消
						$order_info['order_status'] = $sale_status[SALE_ORDER_CANCEL];
						$order_info['check_status'] = $childer_order_status[SOS_CANCEL];
		
						break;
					
					default://未命名状态
						$order_info['order_status'] = '未知';
						$order_info['check_status'] = '未知';
						break;
			}

			$invoice = $info = array();
			$info['order_status'] = $order_info['order_status'];
			$info['order_sn'] = $order_info['order_sn'];
			$info['user_name'] = $order_info['user_name'];
			$info['company_name'] = $order_info['company_name'];
			$info['add_time'] = $order_info['add_time'];
			$info['contract_sn'] = $order_info['contract_sn'];
			$info['contract_name'] = $order_info['contract_name'];
			$info['check_status'] = $order_info['check_status'];
			$info['consignee'] = $order_info['consignee'];
			$info['address'] = $order_info['address'];
			$info['mobile'] = $order_info['mobile'];
			$info['sign_building'] = $order_info['sign_building'];

			unset( $order_info['order_status'] );
			unset( $order_info['order_sn'] );
			unset( $order_info['user_name'] );
			unset( $order_info['company_name'] );
			unset( $order_info['add_time'] );
			unset( $order_info['contract_sn'] );
			unset( $order_info['contract_name'] );
			unset( $order_info['check_status'] );
			unset( $order_info['consignee'] );
			unset( $order_info['address'] );
			unset( $order_info['mobile'] );
			unset( $order_info['sign_building'] );

			$invoice['inv_type'] = $order_info['inv_type'];
			$invoice['inv_payee'] = $order_info['inv_payee'];

			unset( $order_info['inv_type'] );
			unset( $order_info['inv_payee'] );

			//总额计算
			/*if( empty( $order_info['price_num'] ) ){
				$order_info['price_num'] = ($order_info['price_rate'] / 100 ) * $order_info['shop_price'];
			}

			$order_info['goods_price_add'] = $order_info['goods_price'] + $order_info['price_num'];
			if( empty( $order_info['financial_send'] ) ){
				$order_info['financial_send'] = $order_info['goods_number'] * $order_info['goods_price_add'] * (double)( $order_info['financial_send_rate'] );
			}
			$order_info['order_amount_send_buyer'] = $order_info['goods_number'] * $order_info['goods_price_add'] + $order_info['shipping_fee_send_buyer'] + $order_info['financial_send'];//客户价格.发货总价
			$order_info['order_amount_send_saler'] = $order_info['goods_number'] * $order_info['goods_price'] + $order_info['shipping_fee_send_saler'];//供应商价格.发货总价
			*/
			//该类商品的供应商列表
			$suppliers_sql = 'SELECT s.`suppliers_id`, s.`suppliers_name` FROM ' . $goods_table .
							 ' AS g LEFT JOIN ' . $suppliers_table . ' AS s ON g.`suppliers_id` = s.`suppliers_id`' .
							 ' WHERE g.`cat_id` = ' . $order_info['cat_id'] .
							 ' AND s.`suppliers_name` IS NOT NULL' . ' GROUP BY s.`suppliers_id`';//供应商名字不为空
			$suppliers = $GLOBALS['db']->getAll( $suppliers_sql );

			if( empty( $suppliers ) )
				$suppliers = array();
			$order_info['suppers_id'] = $suppliers;

			//支付方式
			$pay_id = array();
			$pay_cfg = C('payment');
			foreach ($pay_cfg as $i => $v) {
				$pay_id[] = array('id' => $i, 'name' => $v );
			}
			$order_info['pay_id'] = $pay_id;
			$order_info['order_id'] = $order_id;

			//历史报价
			$price_log_table = $GLOBALS['ecs']->table( 'price_log' );
			$price_log_sql = 'SELECT p.*, og.`goods_sn` FROM ' . $price_log_table . ' AS p LEFT JOIN ' .
						     $order_goods_table . ' AS og ON og.`order_id` = p.`order_id` WHERE p.`order_id` = ' .
						     $order_id . ' ORDER BY `total` ASC';
			$price_log_arr = $GLOBALS['db']->getAll( $price_log_sql );

			$content = array();
			$content['info'] = $info;
			$content['invoice'] = $invoice;

			$content['form'] = $order_info;
			$content['price_log'] = empty( $price_log_arr ) ? array() : $price_log_arr;

			make_json_response($content, '0', '发货改价初始化成功');
		}

		/**
		 * 接口名称: 发货改价保存
		 * 接口地址：http://admin.zj.dev/admin/OrderInfoModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params)：
		 * {
	     *      "entity": "order_info",
	     *      "command": "updatePriceSend",
	     *      "parameters": {
	     *          "params": {
	     *          	"order_id":101,//订单ID
	     *           	"goods_price_send_buyer": 20200,//客户价格.物料单价
	     *           	"goods_number_send_buyer": 20200,//客户价格.物料数量
	     *           	"order_amount_send_buyer": 20200,//客户价格.发货总价
		 *		        "goods_price_send_saler": "20000.00",//供应商价格.物料单价
		 *		        "goods_number_send_saler": "20000.00",//供应商价格.物料数量
		 *		        "shipping_fee_send_saler": "20000.00",//供应商价格.物流费用
		 *		        "order_amount_send_saler": "20000.00",//供应商价格.发货总价
		 *		        "suppers_id":1,//客户价格.实际供应商id
		 *		        "shipping_fee_send_buyer": "999.99",//客户价格.物流费用
		 *		        "financial_send": "0.00",//客户价格.金融费用
		 *		        "financial_send_rate": "0.00",//客户价格.金融费率 (小数数字)
		 *		        "pay_id":0//支付方式id
	     * 
	     *          }
	     *      }
	     *  }
		 * 返回数据格式如下 :
		 * 	{
		 * 		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "发货改价成功",
		 *	    "content": {}
		 *	}
		 * 
		 */
		public function updatePriceSendAction()
		{
			$content = $this->content;
			$params = $content['parameters']['params'];

			if( !isset( $params['order_id'] ) ){
				make_json_response('', '-1', '订单ID错误');
			}

			if( !isset( $params['suppers_id'] ) ){
				make_json_response('', '-1', '请选择供应商');
			}	
			$order_id = intval( $params['order_id'] );

			$order_info_table = $GLOBALS['ecs']->table('order_info');
			$contract_table = $GLOBALS['ecs']->table('contract');//合同
			$goods_table = $GLOBALS['ecs']->table('goods');//物料

			$category_table = $GLOBALS['ecs']->table('category');//物料类别
			$goods_attr_table = $GLOBALS['ecs']->table('goods_attr');//物料属性
			$order_goods_table = $GLOBALS['ecs']->table('order_goods');//订单商品

			//检查订单状态
			$order_info_sql = 'SELECT o.`child_order_status`, og.`goods_name`, g.`goods_id`, g.`price_num`, g.`price_rate` FROM ' . $order_info_table .
							  ' AS o LEFT JOIN ' . $order_goods_table . ' AS og ON o.`order_id` = og.`order_id` ' .
							  ' LEFT JOIN ' . $goods_table . ' AS g ON og.`goods_id` = g.`goods_id` ' .
					 		  ' WHERE o.`order_id` = ' . $order_id;
			$order_status = $GLOBALS['db']->getRow( $order_info_sql );
			if( !$order_status ){
				make_json_response('', '-1', '订单不存在');
			}

			if( $order_status['child_order_status'] > SOS_SEND_PC ){//平台已验签(发货)
				make_json_response('', '-1', '发货验签完毕，无法发货改价');
			}

			$goods_number_send_buyer = $params['goods_number_send_buyer'];
			$goods_price_send_buyer = (double)( $params['goods_price_send_buyer'] );
			$goods_number_send_saler = $params['goods_number_send_saler'];
			$goods_price_send_saler = (double)( $params['goods_price_send_saler'] );

			$suppers_id = intval( $params['suppers_id'] );//销售信息.供货商id

			$shipping_fee_send_buyer = ( double )( $params['shipping_fee_send_buyer'] );//销售信息.发货物流费用
			$financial_send = $params['financial_send'] ? (double)($params['financial_send']) : 0;//销售信息.发货金融费
			$financial_send_rate = ( double )( $params['financial_send_rate'] / 100 );//销售信息.发货金融费

			$shipping_fee_send_saler = (double)( $params['shipping_fee_send_saler'] );//供货信息.发货物流费
			$pay_id = intval( $params['pay_id'] );//支付方式id

			$order_amount_send_buyer = (double)( $params['order_amount_send_buyer'] );
			$order_amount_send_saler = (double)( $params['order_amount_send_saler'] );

			$order_info = array();
			$order_info['suppers_id'] = $suppers_id;
			$order_info['financial_send_rate'] = (double)( $params['financial_send_rate'] );

			$order_info['financial_send'] = $financial_send;
			$order_info['shipping_fee_send_buyer'] = $shipping_fee_send_buyer;
			$order_info['shipping_fee_send_saler'] = $shipping_fee_send_saler;

			$order_info['order_amount_send_buyer'] = $order_amount_send_buyer;
			$order_info['order_amount_send_saler'] = $order_amount_send_saler;
			$order_info['pay_id'] = $pay_id;

			$order_info_update_sql = 'UPDATE ' . $order_info_table .' SET ';

			foreach ($order_info as $cn => $cv) {
				$order_info_update_sql .= '`' . $cn . '` = ' . $cv . ',';
			}

			$order_info_update_sql = substr( $order_info_update_sql, 0, -1 );
			$order_info_update_sql .= ' WHERE `order_id` = ' . $order_id . ' LIMIT 1';
			$order_info_update = $GLOBALS['db']->query( $order_info_update_sql );// update `order_info`

			if( $order_info_update ) {// update `order_goods`
				$order_goods_update_sql = 'UPDATE ' . $order_goods_table . ' SET ';

				$order_goods = array();

				$order_goods['goods_number_send_buyer'] = $goods_number_send_buyer;
				$order_goods['goods_number_send_saler'] = $goods_number_send_saler;

				$order_goods['goods_price_send_buyer'] = $goods_price_send_buyer;
				$order_goods['goods_price_send_saler'] = $goods_price_send_saler;

				foreach ($order_goods as $cn => $cv) {
					$order_goods_update_sql .= '`' . $cn . '` = ' . $cv . ',';
				}

				$order_goods_update_sql = substr( $order_goods_update_sql, 0, -1 );
				$order_goods_update_sql .= ' WHERE `order_id` = ' . $order_id . ' LIMIT 1';
				$order_goods_update = $GLOBALS['db']->query( $order_goods_update_sql );

				if ( $order_goods_update ) {

					//保存报价到`price_log`
					$price_log_table = $GLOBALS['ecs']->table( 'price_log' );
					$price_log_sql = 'INSERT INTO ' . $price_log_table . ' (';

					//商品属性	
					$goods_attr_sql = 'SELECT `goods_id`, `attr_value` FROM ' . $goods_attr_table .' WHERE `goods_id` = ' . $order_status['goods_id'];
					$goods_attr_arr = $GLOBALS['db']->getAll( $goods_attr_sql );

					$goods_attr_val = array();
					foreach ($goods_attr_arr as $v) {
						$goods_attr_val[] = $v['attr_value'];
					}
					
					$goods_attr_str = implode('/', $goods_attr_val);

					$price_log = array();
					$price_log['order_id'] = $order_id;
					$price_log['goods_name'] = $order_status['goods_name'];
					$price_log['goods_attr'] = $goods_attr_str;
					//供应商名字
					$suppliers_table = $GLOBALS['ecs']->table('suppliers');
					$supplier_sql = 'SELECT `suppliers_name` FROM ' . $suppliers_table . ' WHERE `suppliers_id` = ' . $suppers_id;
					$supplier = $GLOBALS['db']->getRow( $supplier_sql );
					$supplier_name = empty( $supplier ) ? '' : $supplier['suppliers_name'];

					$price_log['suppliers_name'] = $supplier_name;
					$price_log['suppliers_price'] = $goods_price_send_saler;
					$price_log['actual_price'] = $goods_price_send_buyer;

					$price_log['shipping_fee'] = $shipping_fee_send_buyer;
					$price_log['financial'] = $financial_send;
					$price_log['total'] = $order_amount_send_buyer;

					//支付方式
					$pay_cfg = C('payment');
					$price_log['payment'] = $pay_cfg[ $pay_id ];
					$price_log['price_date'] = date('Y-m-d H:i:s');

					$price_log_keys = array_keys( $price_log );

					foreach ($price_log_keys as $k => $v) {
						$price_log_sql .= ' `' . $v . '`,';
					}

					$price_log_sql = substr($price_log_sql, 0, -1) . ") VALUES(";

					foreach ($price_log as $v) {
						if( is_string( $v ) )
							$v = '\'' . $v . '\'';
						$price_log_sql = $price_log_sql . $v . ",";
					}
					$price_log_sql = substr($price_log_sql, 0, -1) . ')';
					
					$GLOBALS['db']->query( $price_log_sql );//保存到历史报价


					make_json_response('', '0', '发货改价成功');
				}else{
					make_json_response('', '-1', '发货改价失败');
				}
			}else{
				make_json_response('', '-1', '发货改价失败');
			}

		}

		/**
		 * 接口名称: 到货改价表单数据
		 * 接口地址：http://admin.zj.dev/admin/OrderInfoModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params)：
	     *  {
	     *      "entity": "order_info",
	     *      "command": "initPriceArr",
	     *      "parameters": {
	     *          "params": {
	     *          	"order_id":142//订单ID
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
	     *  	"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "到货改价信息获取成功",
		 *	    "content": 
		 *	    {
		 *	    	"info":
		 *	        { 	
		 *	        	"order_status":"",//订单状态 0 '未确认', 1 '已确认', 2 '待收货', 3 '已完成', 4 '订单取消'
		 *	        	"order_sn":"os122311",//订单编号
		 *	        	"user_id":1,//下单人id 
		 *	        	"user_name":"王x",//下单人
		 *	        	"add_time":"2015-01-01:11",//拆单时间
		 *	        	"contract_sn":"10000",//合同号
		 *	        	"contract_name":"钢材销售",//合同名称
		 *	        	"company_name":"中铁一局",//公司名称
		 *          	"check_status":"",//验签状态
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
		 *	    	"form":
		 *	    	{
		 *	    		"order_id":142,
		 *	    	 	"goods_price_arr_buyer":100,//客户价格.物料单价
		 *	    	  	"goods_number_arr_buyer":100,//客户价格.物料数量
		 *	    	    "suppers_id"://客户价格.实际供应商列表
		 *	    	    [
		 *	    	    {
		 *	    		   "suppliers_id":1,//供应商id
		 *	    		   "suppliers_name":"天津天佑"//供应商名字
		 *	    	    }
		 *	    	    ],
		 *	    	    "default_suppliers_id":1,//供应商id
		 *	    	    "financial_arr_rate":0.0001,//客户价格.金融费率 (数字)
		 *	    	    "shipping_fee_arr_buyer":82,//客户价格.物流费用
		 *	    	    "financial_arr":1,//客户价格.金融费用
		 *      	    "order_amount_arr_buyer":200,//客户价格.发货总价
		 *      	
		 *	    	 	"goods_price_arr_saler":100,//供应商价格.物料单价
		 *	    	 	"goods_number_arr_saler":100,//供应商价格.物料数量
		 *	    	  	"shipping_fee_arr_saler":82,//供应商价格.物流费用
		 *	    	  	"order_amount_arr_saler":82//供应商价格.发货总价
		 *	    	   	"pay_id"://支付方式列表
		 *	    	    [
		 *	    	    {
		 *	    		   "id":1,
		 *	    		   "name":"现金"
		 *	    	    }
		 *	    	    ]
		 *	    	}
		 *	    }  	
	     *  }
		 */
		public function initPriceArrAction()
		{
			$content = $this->content;
			$params = $content['parameters']['params'];

			if( !isset( $params['order_id'] ) ){
				make_json_response('', '-1', '订单ID错误');
			}
			$order_id = intval( $params['order_id'] );

			$order_info_table = $GLOBALS['ecs']->table('order_info');
			$contract_table = $GLOBALS['ecs']->table('contract');//合同
			$goods_table = $GLOBALS['ecs']->table('goods');//物料

			$category_table = $GLOBALS['ecs']->table('category');//物料类别
			$goods_attr_table = $GLOBALS['ecs']->table('goods_attr');//物料属性
			$order_goods_table = $GLOBALS['ecs']->table('order_goods');//订单商品

			$suppliers_table = $GLOBALS['ecs']->table('suppliers');
			$user_table = $GLOBALS['ecs']->table('users');

			//子订单+商品信息
			$order_sql = 'SELECT og.`goods_id`, og.`goods_price_arr_buyer`, og.`goods_number_arr_buyer`, og.`goods_price_arr_saler`, og.`goods_number_arr_saler`,' .
						 ' o.`order_amount_arr_saler`,o.`order_amount_arr_buyer`,o.`suppers_id` as `default_suppliers_id`, g.`cat_id`, ' .
						 ' o.`add_time`,o.`child_order_status`,o.`contract_sn`, o.`order_sn`, ifnull( c.`contract_name`, \'\' ) AS `contract_name`,u.`user_name`, u.`companyName` AS `company_name`,' .//订单信息
						 ' o.`consignee`,o.`address`,o.`mobile`,o.`sign_building`,o.`inv_type`,o.`inv_payee`,' .
						 ' o.`shipping_fee_arr_buyer`, o.`financial_arr`, o.`financial_arr_rate`, o.`shipping_fee_arr_saler` ' .
						 ' FROM ' . $order_info_table . ' AS o LEFT JOIN ' . 
						 $order_goods_table . ' AS og ON og.`order_id` = o.`order_id` LEFT JOIN ' .
						 $goods_table . ' AS g ON og.`goods_id` = g.`goods_id` ' .
						 ' LEFT JOIN ' . $user_table . ' AS u ON u.`user_id` = o.`user_id` ' .
						 ' LEFT JOIN ' . $contract_table . ' AS c ON c.`contract_num` = o.`contract_sn` ' .
						 'WHERE o.`order_id` = ' . $order_id;
			$order_info = $GLOBALS['db']->getRow( $order_sql );

			if( empty( $order_info ) ){
				make_json_response('', '-1', '订单查询失败');
			}

			//总额计算
			/*if( empty( $order_info['price_num'] ) ){
				$order_info['price_num'] = ($order_info['price_rate'] / 100 ) * $order_info['shop_price'];
			}

			$order_info['goods_price_add'] = $order_info['goods_price'] + $order_info['price_num'];
			if( empty( $order_info['financial_arr'] ) ){
				$order_info['financial_arr'] = $order_info['goods_number_arrival'] * $order_info['goods_price_add'] * (double)( $order_info['financial_arr_rate'] );
			}
			$order_info['order_amount_arr_buyer'] = $order_info['goods_number_arrival'] * $order_info['goods_price_add'] + $order_info['shipping_fee_arr_buyer'] + $order_info['financial_arr'];//客户价格.发货总价
			$order_info['order_amount_arr_saler'] = $order_info['goods_number'] * $order_info['goods_price'] + $order_info['shipping_fee_arr_saler'];//供应商价格.发货总价
			*/
			//该类商品的供应商列表
			$suppliers_sql = 'SELECT s.`suppliers_id`, s.`suppliers_name` FROM ' . $goods_table .
							 ' AS g LEFT JOIN ' . $suppliers_table . ' AS s ON g.`suppliers_id` = s.`suppliers_id`' .
							 ' WHERE g.`cat_id` = ' . $order_info['cat_id'] .
							 ' AND s.`suppliers_name` IS NOT NULL' . ' GROUP BY s.`suppliers_id`';
			$suppliers = $GLOBALS['db']->getAll( $suppliers_sql );

			if( empty( $suppliers ) )
				$suppliers = array();
			$order_info['suppers_id'] = $suppliers;

			//订单详情
			$sale_status = C('sale_status');//销售订单状态,用于页面显示
			$childer_order_status = C('childer_order_status');//验签状态
			$order_info['add_time'] = date('Y-m-d H:i:s', $order_info['add_time']);
			switch ( $order_info['child_order_status'] ) {
					case SOS_UNCONFIRMED://未确认
						$order_info['order_status'] = $sale_status[SALE_ORDER_UNCONFIRMED];
						$order_info['check_status'] = $childer_order_status[SOS_UNCONFIRMED];
			
						break;
					case SOS_CONFIRMED://已确认
						$order_info['order_status'] = $sale_status[SALE_ORDER_CONFIRMED];
						$order_info['check_status'] = $childer_order_status[SOS_CONFIRMED];
			
						break;
					case SOS_SEND_CC://客户已验签(发货)
						$order_info['order_status'] = $sale_status[SALE_ORDER_CONFIRMED];
						$order_info['check_status'] = $childer_order_status[SOS_SEND_CC];
			
						break;
					case SOS_SEND_PC://平台已验签(发货)
						$order_info['order_status'] = $sale_status[SALE_ORDER_CONFIRMED];
						$order_info['check_status'] = $childer_order_status[SOS_SEND_PC];
			
						break;
					case SOS_SEND_SC://供应商已验签(发货)
						$order_info['order_status'] = $sale_status[SALE_ORDER_CONFIRMED];
						$order_info['check_status'] = $childer_order_status[SOS_SEND_SC];
			
						break;
					case SOS_SEND_PC2://平台已验签(发货)
						$order_info['order_status'] = $sale_status[SALE_ORDER_CONFIRMED];
						$order_info['check_status'] = $childer_order_status[SOS_SEND_PC2];
			
						break;
					case SOS_ARR_CC://客户已验签(到货)
						$order_info['order_status'] = $sale_status[SALE_ORDER_UNRECEIVE];
						$order_info['check_status'] = $childer_order_status[SOS_ARR_CC];
			
						break;
					case SOS_ARR_PC://平台已验签(到货)
						$order_info['order_status'] = $sale_status[SALE_ORDER_COMPLETE];
						$order_info['check_status'] = $childer_order_status[SOS_ARR_PC];
			
						break;
					case SOS_ARR_SC://供应商已验签(到货)
						$order_info['order_status'] = $sale_status[SALE_ORDER_COMPLETE];
						$order_info['check_status'] = $childer_order_status[SOS_ARR_SC];
			
						break;
					case SOS_ARR_PC2://平台已验签(到货)
						$order_info['order_status'] = $sale_status[SALE_ORDER_COMPLETE];
						$order_info['check_status'] = $childer_order_status[SOS_ARR_PC2];
			
						break;
					case SOS_CANCEL://验签已取消
						$order_info['order_status'] = $sale_status[SALE_ORDER_CANCEL];
						$order_info['check_status'] = $childer_order_status[SOS_CANCEL];
			
						break;
					
					default://未命名状态
						$order_info['order_status'] = '未知';
						$order_info['check_status'] = '未知';
						break;
			}

			$invoice = $info = array();
			$info['order_status'] = $order_info['order_status'];
			$info['order_sn'] = $order_info['order_sn'];
			$info['user_name'] = $order_info['user_name'];
			$info['company_name'] = $order_info['company_name'];
			$info['add_time'] = $order_info['add_time'];
			$info['contract_sn'] = $order_info['contract_sn'];
			$info['contract_name'] = $order_info['contract_name'];
			$info['check_status'] = $order_info['check_status'];
			$info['consignee'] = $order_info['consignee'];
			$info['address'] = $order_info['address'];
			$info['mobile'] = $order_info['mobile'];
			$info['sign_building'] = $order_info['sign_building'];

			unset( $order_info['order_status'] );
			unset( $order_info['order_sn'] );
			unset( $order_info['user_name'] );
			unset( $order_info['company_name'] );
			unset( $order_info['add_time'] );
			unset( $order_info['contract_sn'] );
			unset( $order_info['contract_name'] );
			unset( $order_info['check_status'] );
			unset( $order_info['consignee'] );
			unset( $order_info['address'] );
			unset( $order_info['mobile'] );
			unset( $order_info['sign_building'] );

			$invoice['inv_type'] = $order_info['inv_type'];
			$invoice['inv_payee'] = $order_info['inv_payee'];

			unset( $order_info['inv_type'] );
			unset( $order_info['inv_payee'] );

			//支付方式
			$pay_id = array();
			$pay_cfg = C('payment');
			foreach ($pay_cfg as $i => $v) {
				$pay_id[] = array('id' => $i, 'name' => $v );
			}
			$order_info['pay_id'] = $pay_id;
			$order_info['order_id'] = $order_id;

			$content = array();
			$content['info'] = $info;
			$content['invoice'] = $invoice;
			$content['form'] = $order_info;

			make_json_response($content, '0', '到货改价初始化成功');	

		}

		/**
		 * 接口名称: 到货改价保存
		 * 接口地址：http://admin.zj.dev/admin/OrderInfoModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params)：
		 * {
	     *      "entity": "order_info",
	     *      "command": "updatePriceArr",
	     *      "parameters": {
	     *          "params": {
	     *	    	    "order_id":142,
		*          	"order_id":101,//订单ID
	     *           	"goods_price_arr_buyer": 20200,//客户价格.物料单价
	     *           	"goods_number_arr_buyer": 20200,//客户价格.物料数量
	     *           	"order_amount_arr_buyer": 20200,//客户价格.发货总价
		 *		        "goods_price_arr_saler": "20000.00",//供应商价格.物料单价
		 *		        "goods_number_arr_saler": "20000.00",//供应商价格.物料数量
		 *		        "shipping_fee_arr_saler": "20000.00",//供应商价格.物流费用
		 *		        "order_amount_arr_saler": "20000.00",//供应商价格.发货总价
		 *		        "suppers_id":1,//客户价格.实际供应商id
		 *		        "shipping_fee_arr_buyer": "999.99",//客户价格.物流费用
		 *		        "financial_arr": "0.00",//客户价格.金融费用
		 *		        "financial_arr_rate": "0.00",//客户价格.金融费率 (小数数字)
		 *	    	    "pay_id":0//支付方式id
		 *	    	
	     *           }
	     *      }
	     *  }
		 * 返回数据格式如下 :
		 * 	{
		 * 		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "到货改价成功",
		 *	    "content": {}
		 *	}
		 */
		public function updatePriceArrAction()
		{
			$content = $this->content;
			$params = $content['parameters']['params'];

			if( !isset( $params['order_id'] ) ){
				make_json_response('', '-1', '订单ID错误');
			}
			if( !isset( $params['suppers_id'] ) ){
				make_json_response('', '-1', '请选择供应商');
			}
			$order_id = intval( $params['order_id'] );

			$order_info_table = $GLOBALS['ecs']->table('order_info');
			$contract_table = $GLOBALS['ecs']->table('contract');//合同
			$goods_table = $GLOBALS['ecs']->table('goods');//物料

			$category_table = $GLOBALS['ecs']->table('category');//物料类别
			$goods_attr_table = $GLOBALS['ecs']->table('goods_attr');//物料属性
			$order_goods_table = $GLOBALS['ecs']->table('order_goods');//订单商品

			//检查订单状态
			$order_info_sql = 'SELECT o.`child_order_status` FROM ' .
				 			  $order_info_table . ' AS o LEFT JOIN ' . $order_goods_table . ' AS og ON o.`order_id` = og.`order_id` ' .
				 			  ' LEFT JOIN ' . $goods_table . ' AS g ON g.`goods_id` = og.`goods_id` ' .
					 		  ' WHERE o.`order_id` = ' . $order_id;
			$order_status = $GLOBALS['db']->getRow( $order_info_sql );
			if( !$order_status ){
				make_json_response('', '-1', '订单不存在');
			}

			if( $order_status['child_order_status'] < SOS_SEND_PP ){//未发货
				make_json_response('', '-1', '尚未进行采购下单，无法到货改价');
			}

			if( $order_status['child_order_status'] >= SOS_ARR_PC2 ){//到货验签（平台验签完）
				make_json_response('', '-1', '到货验签完毕，无法到货改价');
			}

			$goods_number_arr_buyer = $params['goods_number_arr_buyer'];
			$goods_price_arr_buyer = (double)( $params['goods_price_arr_buyer'] );
			$goods_number_arr_saler = $params['goods_number_arr_saler'];
			$goods_price_arr_saler = (double)( $params['goods_price_arr_saler'] );

			$suppers_id = intval( $params['suppers_id'] );//销售信息.供货商id

			$shipping_fee_arr_buyer = ( double )( $params['shipping_fee_arr_buyer'] );//销售信息.发货物流费用
			$financial_arr = $params['financial_arr'] ? (double)($params['financial_arr']) : 0;//销售信息.发货金融费
			$financial_arr_rate = ( double )( $params['financial_arr_rate'] / 100 );//销售信息.发货金融费

			$shipping_fee_arr_saler = (double)( $params['shipping_fee_arr_saler'] );//供货信息.发货物流费
			$pay_id = intval( $params['pay_id'] );//支付方式id

			$order_amount_arr_buyer = (double)( $params['order_amount_arr_buyer'] );
			$order_amount_arr_saler = (double)( $params['order_amount_arr_saler'] );

			$order_info = array();
			$order_info['suppers_id'] = $suppers_id;
			$order_info['financial_arr_rate'] = (double)( $params['financial_arr_rate'] );

			$order_info['financial_arr'] = $financial_arr;
			$order_info['shipping_fee_arr_buyer'] = $shipping_fee_arr_buyer;
			$order_info['shipping_fee_arr_saler'] = $shipping_fee_arr_saler;

			$order_info['order_amount_arr_buyer'] = $order_amount_arr_buyer;
			$order_info['order_amount_arr_saler'] = $order_amount_arr_saler;
			$order_info['pay_id'] = $pay_id;

			$order_info_update_sql = 'UPDATE ' . $order_info_table .' SET ';

			foreach ($order_info as $cn => $cv) {
				$order_info_update_sql .= '`' . $cn . '` = ' . $cv . ',';
			}

			$order_info_update_sql = substr( $order_info_update_sql, 0, -1 );
			$order_info_update_sql .= ' WHERE `order_id` = ' . $order_id . ' LIMIT 1';
			$order_info_update = $GLOBALS['db']->query( $order_info_update_sql );// update `order_info`

			if( $order_info_update ) {// update `order_goods`
				$order_goods_update_sql = 'UPDATE ' . $order_goods_table . ' SET ';

				$order_goods = array();
				$order_goods['goods_number_arr_buyer'] = $goods_number_arr_buyer;
				$order_goods['goods_price_arr_buyer'] = $goods_price_arr_buyer;
				$order_goods['goods_number_arr_saler'] = $goods_number_arr_saler;
				$order_goods['goods_price_arr_saler'] = $goods_price_arr_saler;

				foreach ($order_goods as $cn => $cv) {
					$order_goods_update_sql .= '`' . $cn . '` = ' . $cv . ',';
				}

				$order_goods_update_sql = substr( $order_goods_update_sql, 0, -1 );
				$order_goods_update_sql .= ' WHERE `order_id` = ' . $order_id . ' LIMIT 1';
				$order_goods_update = $GLOBALS['db']->query( $order_goods_update_sql );

				if ( $order_goods_update ) {
					make_json_response('', '0', '到货改价成功');
				}else{
					make_json_response('', '-1', '到货改价失败');
				}
			}else{
				make_json_response('', '-1', '到货改价失败');
			}	
			
		}

		/**
		 * 接口名称: 获取供应商
		 * 物料单价
		 * 接口地址：http://admin.zj.dev/admin/OrderInfoModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params)：
	     *  {
	     *      "entity": "order_info",
	     *      "command": "getSuppliersPrice",
	     *      "parameters": {
	     *          "params": {
	     *          	"suppliers_id":101,//供应商ID
	     *          	"cat_id":20,//商品分类id
	     *          	"goods_id":20//商品id
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
	     * 		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "获取客户物料价格成功",
		 *	    "content": 
		 *	    {
		 *	    	"goods_price"://供应商的物料单价
		 *	    }  	
	     *  }
		 */
		public function getSuppliersPriceAction()
		{
			$content = $this->content;
			$params = $content['parameters']['params'];

			$suppliers_id = $params['suppliers_id'];
			$goods_id = $params['goods_id'];
			$cat_id = $params['cat_id'];

			$goods_table = $GLOBALS['ecs']->table('goods');//物料
			//检查订单状态
			$goods_price_sql = 'SELECT `shop_price`, `goods_id` FROM ' . $goods_table . ' WHERE `suppliers_id` = ' .
								$suppliers_id . ' AND `cat_id` = ' . $cat_id;
			$goods_price = $GLOBALS['db']->getAll( $goods_price_sql );

			$content = array();
			if( empty( $goods_price ) ){
				$content['goods_price'] = 0;
				make_json_response($content, '-1', '订单不存在');
			}else{

				$content['goods_price'] = $goods_price[0]['shop_price'];	

				foreach ($goods_price as $gp) {
					if($gp['goods_id'] == $goods_id ){
						$content['goods_price'] = $gp['shop_price'];		
						break;
					}
				}


			}

			make_json_response($content, '0', '获取客户物料价格成功');
					 		  
		}

		/**
		 * @param $suppliers_id
		 * @param $goods_id
		 * @param $cat_id
		 * 
		 * @return $price 供应商报价
		 */
		private function _getSuppliersPrice($suppliers_id = 0 , $goods_id = 0, $cat_id = 0)
		{

			$goods_table = $GLOBALS['ecs']->table('goods');//物料
			//检查订单状态
			$goods_price_sql = 'SELECT `shop_price`, `goods_id` FROM ' . $goods_table . ' WHERE `suppliers_id` = ' .
								$suppliers_id . ' AND `cat_id` = ' . $cat_id;
			$goods_price = $GLOBALS['db']->getAll( $goods_price_sql );

			if( empty( $goods_price ) ){
				return 0;
			}else{

				foreach ($goods_price as $gp) {
					if($gp['goods_id'] == $goods_id ){
						return $gp['shop_price'];
					}
				}

				return $goods_price[0]['shop_price'];

			}
		}

		/**
		 * 接口名称: 获取改价后的金融费用
		 * 接口地址：http://admin.zj.dev/admin/OrderInfoModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params)：
	     *  {
	     *      "entity": "order_info",
	     *      "command": "getFinaceFee",
	     *      "parameters": {
	     *          "params": {
	     *          	"price":101,//商品价格
	     *          	"number":1,//商品数量
	     *          	"rate":1,//金融费比率
	     *          	"contract_sn":"ht20"//合同编号
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
	     * 		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "获取金融费成功",
		 *	    "content": 
		 *	    {
		 *	    	"finace":21//金融费
		 *	    }  	
	     *  }
		 */
		public function getFinaceFeeAction()
		{
			$content = $this->content;
			$params = $content['parameters']['params'];

			$price = (double)( $params['price'] );
			$number = intval( $params['number'] );
			$contract_sn = strval( $params['contract_sn'] );

			$rate = (double)( $params['rate'] );

			$contract_table = $GLOBALS['ecs']->table('contract');

			$amount_sql = 'SELECT `bill_amount_valid`,`cash_amount_valid` FROM ' . $contract_table .' WHERE `contract_num` = \'' .
						  $contract_sn . '\' LIMIT 1';
			$amount = $GLOBALS['db']->getRow( $amount_sql );

			$content = array();
			$finance = 0;

			if( empty( $amount ) ){
				$content['finance'] = $finance;
				make_json_response( $content, '-1', '合同编号错误' );

			}else{
				$total = $price * $number;
				if( $total <= $amount['cash_amount_valid'] )
				{
					$finance = 0;

				}else{
					$total -= $amount['cash_amount_valid'];
					$finance = round( ( $total * $rate ) / 100 , 2);
				}
				
			}

			$content['finance'] = $finance;
			make_json_response( $content, '0', '获取金融费成功' );
		}

		/**
		 * 接口名称:取消主订单
		 * 接口地址：http://admin.zj.dev/admin/OrderInfoModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params)：
	     *  {
	     *      "entity": "order_info",
	     *      "command": "cancelOrder",
	     *      "parameters": {
	     *          "params": {
	     *          	"order_id":49//主订单ID
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
	     * 		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "取消订单成功",
		 *	    "content": {}
	     *  }
		 */
		public function cancelOrderAction()
		{
			$content = $this->content;
			$params = $content['parameters']['params'];

			if( !isset( $params['order_id'] ) ){
				make_json_response('', '-1', '订单ID错误');
			}
			$order_id = intval( $params['order_id'] );

			$order_info_table = $GLOBALS['ecs']->table('order_info');

			$order_total_sql = 'SELECT COUNT(*) AS `total` FROM ' . $order_info_table . ' WHERE `parent_order_id` = ' .
							   $order_id . ' AND `child_order_status` <> ' . SOS_UNCONFIRMED .
							   ' AND `child_order_status` <> ' . SOS_CANCEL;
			$order_total = $GLOBALS['db']->getRow( $order_total_sql );
			if( !empty($order_total) ){
				if( $order_total['total'] == 0 ){
					$cancel_sql = 'UPDATE ' . $order_info_table . ' SET `order_status` = ' .
							  POS_CANCEL . ' WHERE `order_id` = ' . $order_id . ' LIMIT 1';
					$cancel_query = $GLOBALS['db']->query( $cancel_sql );

					if( $cancel_query ){
						make_json_response('', '0', '取消订单成功');
					}else{
						make_json_response('', '-1', '取消订单失败');		
					}

				}else{
					make_json_response('', '-1', '无法取消处理中的订单');
				}
			}else{
				make_json_response('', '-1', '取消订单失败');
			}
		}
		
		/**
		 * 接口名称:取消未处理
		 * 
		 * 接口地址：http://admin.zj.dev/admin/OrderInfoModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params)：
	     *  {
	     *      "entity": "order_info",
	     *      "command": "removeGoods",
	     *      "parameters": {
	     *          "params": {
	     *          	"order_id":49,//主订单ID
	     *          	"goods_id":518//商品id
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
	     * 		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "取消未处理成功",
		 *	    "content": {}  	
	     *  }
		 */
		public function removeGoodsAction()
		{
			$content = $this->content;
			$params = $content['parameters']['params'];

			if( !isset( $params['order_id'] ) ){
				make_json_response('', '-1', '订单ID错误');
			}

			if( !isset( $params['goods_id'] ) ){
				make_json_response('', '-1', '商品ID错误');
			}

			$order_id = intval( $params['order_id'] );
			$goods_id = intval( $params['goods_id'] );
			$order_goods_table = $GLOBALS['ecs']->table('order_goods');//订单商品

			//总数等于已拆分
			$remove_sql = 'UPDATE ' . $order_goods_table . ' SET `goods_number` = `send_number` ' .
						  ' WHERE `order_id` = ' . $order_id . ' AND `goods_id` = ' . $goods_id . ' LIMIT 1';
			$remove_query = $GLOBALS['db']->query( $remove_sql );
			if( $remove_query ){
				make_json_response('', '0', '取消未拆分成功');
			}else{
				make_json_response('', '-1', '取消未拆分失败');
			}
		}
	}
	$content = jsonAction( array( "splitInit", "split", "childerList", "childerDetail", "addShippingInfo", "addShippingLog",
								 "initPriceSend", "updatePriceSend", "initPriceArr", "updatePriceArr", "updateChilderStatus",
								 "getSuppliersPrice", "searchChilderList", "cancelOrder", "removeGoods", "getFinaceFee"
						 ) );
	$orderModel = new OrderInfoModel($content);
	$orderModel->run();
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
			if ($this->command == 'find') {
				//
				$this->findAction();
			}elseif ($this->command == 'page'){
				//
				$this->pageAction();
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
			}elseif ($this->command == 'cancelOrder'){
				//
				$this->cancelOrderAction();
			}elseif ($this->command == 'removeGoods'){
				//
				$this->removeGoodsAction();
			}
		}


	}
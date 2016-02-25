<?php

use Phalcon\Db\Column as Column;
use PhpRudder\Mvc\ControllerBase;
use PhpRudder\Http\ResponseApi;

class PersonalController extends ControllerBase {

	/**
	 * 个人中心
	 * 返回值：
	 * {
	 * 		"body":{
	 * 			"account_info":{
	 * 			
	 * 				"account":"lb",//用户名
	 * 			 	"companyName":"lb",//公司名称
	 * 			  	"companyAddress":"lb",//用户地址
	 * 			   	"billAmountHistory":"111111",//总票据额度
	 * 			    "billAmountValid":"22222",//可用票据额度
	 * 			    "cashAmountHistory":"111111",//可用现金额度
	 * 			    "cashAmountValid":"22222",//可用现金额度
	 * 			 },
	 * 			 "contract_info":
	 * 			 [
	 * 			 	{
	 * 			 		"contract_name":"xxxxxx",//项目名称
	 * 			 		"companyName":"lb",//公司名称
	 * 			 		"contract_amount":"10000",//合同金额
	 * 			 		"contract_num":"10000",//合同编号
	 * 			 		"start_time":"2016/02/25",//合同开始日期
	 * 			 		"start_time":"2016/02/25",//合同结束日期
	 * 			  	}
	 * 			 ]
	 * 		}
	 * }
	 * @
	 */
	public function indexAction()
	{
		$user = $this->get_user();
		$user_arr = get_object_vars( $user );
		$user_account = array();

		foreach ($user_arr as $k => $v) {
			$user_account[$k] = $v;
		}

		//合同数据
		

		$content = array();
		$content['user_account'] = $user_account;

		return ResponseApi::send( $content );
	}

}
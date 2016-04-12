<?php

class OrderInfo extends \PhpRudder\Mvc\ModelBase
{

    /**
     *
     * @var integer
     */
    public $order_id;

    /**
     *
     * @var string
     */
    public $order_sn;

    /**
     *
     * @var integer
     */
    public $user_id;

    /**
     *
     * @var integer
     */
    public $order_status;

    /**
     *
     * @var integer
     */
    public $shipping_status;

    /**
     *
     * @var integer
     */
    public $pay_status;

    /**
     *
     * @var string
     */
    public $consignee;

    /**
     *
     * @var integer
     */
    public $country;

    /**
     *
     * @var integer
     */
    public $province;

    /**
     *
     * @var integer
     */
    public $city;

    /**
     *
     * @var integer
     */
    public $district;

    /**
     *
     * @var string
     */
    public $address;

    /**
     *
     * @var string
     */
    public $zipcode;

    /**
     *
     * @var string
     */
    public $tel;

    /**
     *
     * @var string
     */
    public $mobile;

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var string
     */
    public $best_time;

    /**
     *
     * @var string
     */
    public $sign_building;

    /**
     *
     * @var string
     */
    public $postscript;

    /**
     *
     * @var integer
     */
    public $shipping_id;

    /**
     *
     * @var string
     */
    public $shipping_name;

    /**
     *
     * @var integer
     */
    public $pay_id;

    /**
     *
     * @var string
     */
    public $pay_name;

    /**
     *
     * @var string
     */
    public $pay_orgcode;

    /**
     *
     * @var string
     */
    public $pay_orgname;

    /**
     *
     * @var string
     */
    public $how_oos;

    /**
     *
     * @var string
     */
    public $how_surplus;

    /**
     *
     * @var string
     */
    public $pack_name;

    /**
     *
     * @var string
     */
    public $card_name;

    /**
     *
     * @var string
     */
    public $card_message;

    /**
     *
     * @var string
     */
    public $inv_payee;

    /**
     *
     * @var string
     */
    public $inv_content;

    /**
     *
     * @var double
     */
    public $goods_amount;

    /**
     *
     * @var double
     */
    public $shipping_fee;

    /**
     *
     * @var double
     */
    public $insure_fee;

    /**
     *
     * @var double
     */
    public $pay_fee;

    /**
     *
     * @var double
     */
    public $pack_fee;

    /**
     *
     * @var double
     */
    public $card_fee;

    /**
     *
     * @var double
     */
    public $money_paid;

    /**
     *
     * @var double
     */
    public $surplus;

    /**
     *
     * @var integer
     */
    public $integral;

    /**
     *
     * @var double
     */
    public $integral_money;

    /**
     *
     * @var double
     */
    public $bonus;

    /**
     *
     * @var double
     */
    public $order_amount;

    /**
     *
     * @var integer
     */
    public $from_ad;

    /**
     *
     * @var string
     */
    public $referer;

    /**
     *
     * @var integer
     */
    public $add_time;

    /**
     *
     * @var integer
     */
    public $confirm_time;

    /**
     *
     * @var integer
     */
    public $contract_time;

    /**
     *
     * @var integer
     */
    public $confirms_time;

    /**
     *
     * @var integer
     */
    public $pay_time;

    /**
     *
     * @var integer
     */
    public $shipping_time;

    /**
     *
     * @var integer
     */
    public $contract_effect_time;

    /**
     *
     * @var integer
     */
    public $reconciliation_time;

    /**
     *
     * @var integer
     */
    public $order_time;

    /**
     *
     * @var integer
     */
    public $failure_time;

    /**
     *
     * @var integer
     */
    public $pack_id;

    /**
     *
     * @var integer
     */
    public $card_id;

    /**
     *
     * @var integer
     */
    public $bonus_id;

    /**
     *
     * @var string
     */
    public $invoice_no;

    /**
     *
     * @var string
     */
    public $extension_code;

    /**
     *
     * @var integer
     */
    public $extension_id;

    /**
     *
     * @var string
     */
    public $to_buyer;

    /**
     *
     * @var string
     */
    public $pay_note;

    /**
     *
     * @var integer
     */
    public $agency_id;

    /**
     *
     * @var string
     */
    public $inv_type;

    /**
     *
     * @var double
     */
    public $tax;

    /**
     *
     * @var integer
     */
    public $is_separate;

    /**
     *
     * @var integer
     */
    public $parent_id;

    /**
     *
     * @var double
     */
    public $discount;

    /**
     *
     * @var string
     */
    public $contract_sn;

    /**
     *
     * @var string
     */
    public $erp_sn;

    /**
     *
     * @var string
     */
    public $inv_address;

    /**
     *
     * @var string
     */
    public $parent_order_sn;

    /**
     *
     * @var integer
     */
    public $parent_order_id;

    /**
     *
     * @var integer
     */
    public $is_remaind;

    /**
     *
     * @var string
     */
    public $suppers_id;

    /**
     *
     * @var double
     */
    public $order_contract_count;

    /**
     *
     * @var double
     */
    public $order_check_count;
    
    /**
     * 
     * @var varchar
     */
    public $inv_fax;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'order_id' => 'id',
            'order_sn' => 'orderSn',
            'user_id' => 'userId',
            'order_status' => 'status',
            'shipping_status' => 'shippingStatus',
            'pay_status' => 'payStatus',
            'purchase_pay_status' => 'purchasePayStatus',
            'consignee' => 'name',
            'country' => 'country',
            'province' => 'province',
            'city' => 'city',
            'district' => 'district',
            'address' => 'address',
            'zipcode' => 'zipcode',
            'tel' => 'tel',
            'mobile' => 'phone',
            'email' => 'email',
            'best_time' => 'vtime',
            'sign_building' => 'tag',
            'postscript' => 'remark',
            'shipping_id' => 'shipping_id',
            'shipping_name' => 'shipping_name',
            'pay_id' => 'pay_id',
            'pay_name' => 'pay_name',
            'pay_orgcode' => 'payOrgcode',
            'pay_orgname' => 'pay_orgname',
            'how_oos' => 'how_oos',
            'how_surplus' => 'how_surplus',
            'pack_name' => 'pack_name',
            'card_name' => 'card_name',
            'card_message' => 'card_message',
            'inv_payee' => 'invPayee', //发票抬头
            'inv_content' => 'invContent', //发票内容
            'goods_amount' => 'goodsAmount',
            'shipping_fee' => 'shipping_fee',
            'insure_fee' => 'insure_fee',
            'pay_fee' => 'pay_fee',
            'pack_fee' => 'pack_fee',
            'card_fee' => 'card_fee',
            'money_paid' => 'money_paid',
            'surplus' => 'surplus',
            'integral' => 'integral',
            'integral_money' => 'integral_money',
            'bonus' => 'bonus',
            'order_amount' => 'orderAmount',
            'from_ad' => 'from_ad',
            'referer' => 'referer',
            'add_time' => 'createAt',
            'confirm_time' => 'confirm_time',
            'contract_time' => 'contract_time',
            'confirms_time' => 'confirms_time',
            'pay_time' => 'pay_time',
            'shipping_time' => 'shipping_time',
            'contract_effect_time' => 'contract_effect_time',
            'reconciliation_time' => 'reconciliation_time',
            'order_time' => 'order_time',
            'failure_time' => 'failure_time',
            'pack_id' => 'pack_id',
            'card_id' => 'card_id',
            'bonus_id' => 'bonus_id',
            'invoice_no' => 'invoice_no',
            'extension_code' => 'extension_code',
            'extension_id' => 'extension_id',
            'to_buyer' => 'to_buyer',
            'pay_note' => 'pay_note',
            'agency_id' => 'agency_id',
            'inv_type' => 'invType', //发票类型
            'tax' => 'tax', //开票税额
            'is_separate' => 'is_separate',
            'parent_id' => 'parent_id',
            'discount' => 'discount',
            'contract_sn' => 'contractSn', //合同编号
            'erp_sn' => 'erpSn',
            'inv_address' => 'invAddress', //发票地址
            'parent_order_sn' => 'parent_order_sn',
            'parent_order_id' => 'parentOrderId', //0父订单
        	'is_remaind' => 'isRemaind', //催办订单标识位
        	'suppers_id' => 'suppersId',
            'order_contract_count' => 'contractCount', //合同总金额
            'order_check_count' => 'checkCount', //验收总金额
            'inv_fax' => 'invFax', 
        	'inv_tel' => 'invTel', 
        	'inv_bank_name' => 'invBankName', 
        	'inv_bank_account' => 'invBankAccount', 
        	'inv_bank_address' => 'invBankAddress',
            'inv_company' => 'invCompany',
            'inv_company_addr' => 'invCompanyAddr',
            'inv_license' => 'invLicense',
            'bill_used_days' => 'billUsedDays',

        	'shipping_info'    => 'shippingInfo', 
        	'shipping_log'     => 'shippingLog', 
        	'child_order_status' => 'childOrderStatus', 
        	'financial_send_rate'=> 'financialSendRate', 
        	'financial_send'   => 'financialSend', 
        	'financial_arr_rate' => 'financialArrRate', 
        	'financial_arr'      => 'financialArr', 
        	'shipping_fee_send_buyer' => 'shippingFeeSendBuyer', 
        	'shipping_fee_send_saler' => 'shippingFeeSendSaler', 
        	'shipping_fee_arr_buyer'  => 'shippingFeeArrBuyer', 
        	'shipping_fee_arr_saler'  => 'shippingFeeArrSaler', 
        	'order_amount_send_buyer' => 'orderAmountSendBuyer', 
        	'order_amount_send_saler' => 'orderAmountSendSaler', 
        	'order_amount_arr_buyer'  => 'orderAmountArrBuyer', 
            'order_amount_arr_saler'  => 'orderAmountArrSaler',
            'bill_used'  => 'billUsed',
        	'cash_used'  => 'cashUsed'
        );
    }

    public function initialize() {
		$this->shippingStatus = 0;
		$this->createAt = time();
		$this->isRemaind = 0;
		$this->payStatus = 0;
		$this->parentOrderId = 0;
    	$attributes = array(
    			'country',
    			'province',
    			'city',
    			'district',
    			'zipcode',
    			'tel',
    			'email',
    			'best_time',
    			'postscript',
    			'shipping_id',
    			'shipping_name',
    			'pay_id',
    			'pay_name',
    			'pay_orgname',
    			'how_oos',
    			'how_surplus',
    			'pack_name',
    			'card_name',
    			'card_message',
    			'shipping_fee',
    			'insure_fee',
    			'pay_fee',
    			'pack_fee',
    			'card_fee',
    			'money_paid',
    			'surplus',
    			'integral',
    			'integral_money',
    			'bonus',
    			'from_ad',
    			'referer',
    			'confirm_time',
    			'contract_time',
    			'confirms_time',
    			'pay_time',
    			'shipping_time',
    			'contract_effect_time',
    			'reconciliation_time',
    			'order_time',
    			'failure_time',
    			'pack_id',
    			'card_id',
    			'bonus_id',
    			'invoice_no',
    			'extension_code',
    			'extension_id',
    			'to_buyer',
    			'pay_note',
    			'agency_id',
    			'tax',
    			'is_separate',
    			'parent_id',
    			'discount',
    			'parent_order_sn',
    			'suppersId', 
    	);
    	$this->skipAttributesOnCreate($attributes);
    	$this->skipAttributesOnUpdate($attributes);
    }

}

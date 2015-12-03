<?php

class ExchangeOrderInfo extends \PhpRudder\Mvc\ModelBase
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
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'order_id' => 'orderId',
            'order_sn' => 'orderSn',
            'user_id' => 'userId',
            'order_status' => 'orderStatus',
            'shipping_status' => 'shippingStatus',
            'pay_status' => 'payStatus',
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
            'best_time' => 'best_time',//
            'sign_building' => 'tag',
            'postscript' => 'postscript',
            'shipping_id' => 'shipping_id',
            'shipping_name' => 'shipping_name',
            'pay_id' => 'pay_id',
            'pay_name' => 'pay_name',
            'how_oos' => 'how_oos',
            'how_surplus' => 'how_surplus',
            'pack_name' => 'pack_name',
            'card_name' => 'card_name',
            'card_message' => 'card_message',
            'inv_payee' => 'inv_payee',
            'inv_content' => 'inv_content',
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
            'order_amount' => 'order_amount',
            'from_ad' => 'from_ad',
            'referer' => 'referer',
            'add_time' => 'createAt',
            'confirm_time' => 'confirm_time',
            'pay_time' => 'pay_time',
            'shipping_time' => 'shipping_time',
            'pack_id' => 'pack_id',
            'card_id' => 'card_id',
            'bonus_id' => 'bonus_id',
            'invoice_no' => 'invoice_no',
            'extension_code' => 'extension_code',
            'extension_id' => 'extension_id',
            'to_buyer' => 'to_buyer',
            'pay_note' => 'pay_note',
            'agency_id' => 'agency_id',
            'inv_type' => 'inv_type',
            'tax' => 'tax',
            'is_separate' => 'is_separate',
            'parent_id' => 'parent_id',
            'discount' => 'discount'
        );
    }

    public function initialize() {
    	$attributes = array(
    			'country',
    			'province',
    			'city',
    			'district',
    			'zipcode',
    			'tel',
    			'email',
    			'best_time',//
    			'postscript',
    			'shipping_id',
    			'shipping_name',
    			'pay_id',
    			'pay_name',
    			'how_oos',
    			'how_surplus',
    			'pack_name',
    			'card_name',
    			'card_message',
    			'inv_payee',
    			'inv_content',
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
    			'order_amount',
    			'from_ad',
    			'referer',
    			'confirm_time',
    			'pay_time',
    			'shipping_time',
    			'pack_id',
    			'card_id',
    			'bonus_id',
    			'invoice_no',
    			'extension_code',
    			'extension_id',
    			'to_buyer',
    			'pay_note',
    			'agency_id',
    			'inv_type',
    			'tax',
    			'is_separate',
    			'parent_id',
    			'discount'
    	);
		$this->skipAttributesOnCreate($attributes);
		$this->hasOne('orderId', 'ExchangeOrderGoods', 'orderId');
    }
    public function beforeCreate() {
    	$this->createAt = time();
    }
}

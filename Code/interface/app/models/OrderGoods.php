<?php

class OrderGoods extends \PhpRudder\Mvc\ModelBase
{

    /**
     *
     * @var integer
     */
    public $rec_id;

    /**
     *
     * @var integer
     */
    public $order_id;

    /**
     *
     * @var integer
     */
    public $goods_id;

    /**
     *
     * @var string
     */
    public $goods_name;

    /**
     *
     * @var string
     */
    public $goods_sn;

    /**
     *
     * @var integer
     */
    public $product_id;

    /**
     *
     * @var integer
     */
    public $goods_number;

    /**
     *
     * @var double
     */
    public $market_price;

    /**
     *
     * @var double
     */
    public $goods_price;

    /**
     *
     * @var string
     */
    public $goods_attr;

    /**
     *
     * @var integer
     */
    public $send_number;

    /**
     *
     * @var integer
     */
    public $is_real;

    /**
     *
     * @var string
     */
    public $extension_code;

    /**
     *
     * @var integer
     */
    public $parent_id;

    /**
     *
     * @var integer
     */
    public $is_gift;

    /**
     *
     * @var string
     */
    public $goods_attr_id;

    /**
     *
     * @var string
     */
    public $contract_price;

    /**
     *
     * @var integer
     */
    public $contract_number;

    /**
     *
     * @var string
     */
    public $check_price;

    /**
     *
     * @var integer
     */
    public $check_number;

    /**
     *
     * @var string
     */
    public $cat_code;

    /**
     *
     * @var string
     */
    public $wcode;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'rec_id' => 'id',
            'order_id' => 'orderId',
            'goods_id' => 'goodsId',
            'goods_name' => 'goodsName',
            'goods_sn' => 'goodsSn',
            'product_id' => 'product_id',
            'goods_number' => 'nums',
            'market_price' => 'market_price',
            'goods_price' => 'goodsPrice',
            'goods_attr' => 'goods_attr',
            'send_number' => 'send_number',
            'is_real' => 'is_real',
            'extension_code' => 'extension_code',
            'parent_id' => 'parent_id',
            'is_gift' => 'is_gift',
            'goods_attr_id' => 'goods_attr_id',
            'contract_price' => 'contractPrice', //合同单价
            'contract_number' => 'contractNums',
            'check_price' => 'checkPrice', //验收单价
            'check_number' => 'checkNums', //验收数
            'cat_code' => 'cat_code',
            'wcode' => 'wcode'
        );
    }

    public function initialize() {
    	$attributes = array(
    			'product_id',
    			'market_price',
    			'goods_attr',
    			'send_number',
    			'is_real',
    			'extension_code',
    			'parent_id',
    			'is_gift',
    			'goods_attr_id',
    			'contractPrice', //合同单价
    			'contractNums',
    			'checkPrice', //验收单价
    			'checkNums', //验收数
    			'cat_code',
    			'wcode'
    	);
    	$this->skipAttributesOnCreate($attributes);
    	$this->skipAttributesOnUpdate(array(
    			'product_id',
    			'market_price',
    			'goods_attr',
    			'send_number',
    			'is_real',
    			'extension_code',
    			'parent_id',
    			'is_gift',
    			'goods_attr_id'
    	));
    }

}

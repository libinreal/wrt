<?php

class ExchangeOrderGoods extends \PhpRudder\Mvc\ModelBase
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
     * @var integer
     */
    public $product_id;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'rec_id' => 'rec_id',
            'order_id' => 'orderId',
            'goods_id' => 'goodsId',
            'goods_name' => 'goodsName',
            'goods_sn' => 'goodsSn',
            'goods_number' => 'goodsNum',
            'market_price' => 'market_price',
            'goods_price' => 'goods_price',
            'goods_attr' => 'goods_attr',
            'send_number' => 'send_number',
            'is_real' => 'is_real',
            'extension_code' => 'extension_code',
            'parent_id' => 'parent_id',
            'is_gift' => 'is_gift',
            'goods_attr_id' => 'goods_attr_id',
            'product_id' => 'product_id'
        );
    }

    public function initialize() {
    	$attributes = array(
    			'rec_id' => 'rec_id',
    			'market_price' => 'market_price',
    			'goods_price' => 'goods_price',
    			'goods_attr' => 'goods_attr',
    			'send_number' => 'send_number',
    			'is_real' => 'is_real',
    			'extension_code' => 'extension_code',
    			'parent_id' => 'parent_id',
    			'is_gift' => 'is_gift',
    			'goods_attr_id' => 'goods_attr_id',
    			'product_id' => 'product_id'
    	);
    	$this->skipAttributesOnCreate($attributes);
    }

}

<?php

class Cart extends \PhpRudder\Mvc\ModelBase
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
    public $user_id;

    /**
     *
     * @var string
     */
    public $session_id;

    /**
     *
     * @var integer
     */
    public $goods_id;

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
     * @var string
     */
    public $goods_name;

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
     * @var integer
     */
    public $goods_number;

    /**
     *
     * @var string
     */
    public $goods_attr;

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
    public $rec_type;

    /**
     *
     * @var integer
     */
    public $is_gift;

    /**
     *
     * @var integer
     */
    public $is_shipping;

    /**
     *
     * @var integer
     */
    public $can_handsel;

    /**
     *
     * @var string
     */
    public $goods_attr_id;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'rec_id' => 'id',
            'user_id' => 'userId',
            'session_id' => 'session_id',
            'goods_id' => 'goodsId',
            'goods_sn' => 'goodsSn',
            'product_id' => 'product_id',
            'goods_name' => 'goodsName',
            'market_price' => 'market_price',
            'goods_price' => 'price',
            'goods_number' => 'nums',
            'goods_attr' => 'goods_attr',
            'is_real' => 'is_real',
            'extension_code' => 'extension_code',
            'parent_id' => 'parent_id',
            'rec_type' => 'rec_type',
            'is_gift' => 'is_gift',
            'is_shipping' => 'is_shipping',
            'can_handsel' => 'can_handsel',
            'goods_attr_id' => 'goods_attr_id'
        );
    }

    public function initialize() {
    	$attributes = array('session_id',
    			'product_id',
    			'market_price',
    			'goods_attr',
    			'is_real',
    			'extension_code',
    			'parent_id',
    			'rec_type',
    			'is_gift',
	            'is_shipping',
	            'can_handsel',
	            'goods_attr_id',
    	);
    	$this->skipAttributesOnCreate($attributes);
    	$this->skipAttributesOnUpdate($attributes);
    }

}

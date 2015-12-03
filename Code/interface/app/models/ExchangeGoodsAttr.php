<?php

class ExchangeGoodsAttr extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $goods_attr_id;

    /**
     *
     * @var integer
     */
    public $goods_id;

    /**
     *
     * @var integer
     */
    public $attr_id;

    /**
     *
     * @var string
     */
    public $attr_value;

    /**
     *
     * @var string
     */
    public $attr_price;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'goods_attr_id' => 'goods_attr_id',
            'goods_id' => 'id',
            'attr_id' => 'attr_id',
            'attr_value' => 'attr_value',
            'attr_price' => 'attr_price'
        );
    }

    public function initialize() {
    	$this->hasOne('attr_id', 'ExchangeAttribute', 'attr_id');
    }

}

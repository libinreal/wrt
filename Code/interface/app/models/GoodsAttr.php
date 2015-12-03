<?php

class GoodsAttr extends \Phalcon\Mvc\Model
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
            'goods_attr_id' => 'goodsAttrId',
            'goods_id' => 'goodsId',
            'attr_id' => 'attrId',
            'attr_value' => 'attr_value',
            'attr_price' => 'attr_price'
        );
    }

}

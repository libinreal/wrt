<?php

class ShippingPrice extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $shipping_fee_id;

    /**
     *
     * @var string
     */
    public $goods_category_id;

    /**
     *
     * @var string
     */
    public $suppliers_id;

    /**
     *
     * @var string
     */
    public $shipping_fee;

    /**
     *
     * @var integer
     */
    public $desc;

    

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'shipping_fee_id' => 'id',
            'goods_category_id' => 'catId',
            'suppliers_id' => 'suppliersId',
            'shipping_fee' => 'shippingFee',
            'desc' => 'des'
        );
    }

}

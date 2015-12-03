<?php

class GoodsPriceLog extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $goodsId;

    /**
     *
     * @var string
     */
    public $wcode;

    /**
     *
     * @var double
     */
    public $price;

    /**
     *
     * @var integer
     */
    public $createAt;

    /**
     *
     * @var string
     */
    public $brandId;

    /**
     *
     * @var string
     */
    public $brandName;

    /**
     *
     * @var integer
     */
    public $supplierId;

    /**
     *
     * @var string
     */
    public $vscope;

    /**
     *
     * @var string
     */
    public $cat_code;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id',
            'goodsId' => 'goodsId',
            'wcode' => 'wcode',
            'price' => 'price',
            'createAt' => 'createAt',
            'brandId' => 'brandId',
            'brandName' => 'brandName',
            'supplierId' => 'supplierId',
            'vscope' => 'vscope',
            'cat_code' => 'code'
        );
    }

}

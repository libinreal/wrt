<?php

class BusinessRecommend extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $cat_code;

    /**
     *
     * @var string
     */
    public $goods_name;

    /**
     *
     * @var string
     */
    public $goods_wcode;

    /**
     *
     * @var string
     */
    public $goods_id;

    /**
     *
     * @var string
     */
    public $brand_id;

    /**
     *
     * @var integer
     */
    public $createAt;

    /**
     *
     * @var integer
     */
    public $updateAt;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id',
            'cat_code' => 'code',
            'goods_name' => 'goodsName',
            'goods_wcode' => 'wcode',
            'goods_id' => 'goodsId',
            'brand_id' => 'brandId',
            'createAt' => 'createAt',
            'updateAt' => 'updateAt'
        );
    }

}

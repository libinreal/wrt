<?php

class GoodsGallery extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $img_id;

    /**
     *
     * @var integer
     */
    public $goods_id;

    /**
     *
     * @var string
     */
    public $img_url;

    /**
     *
     * @var string
     */
    public $img_desc;

    /**
     *
     * @var string
     */
    public $thumb_url;

    /**
     *
     * @var string
     */
    public $img_original;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'img_id' => 'img_id',
            'goods_id' => 'goodsId',
            'img_url' => 'img_url',
            'img_desc' => 'img_desc',
            'thumb_url' => 'thumb',
            'img_original' => 'imgOriginal'
        );
    }

}

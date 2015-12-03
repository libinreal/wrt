<?php

class CollectGoods extends \Phalcon\Mvc\Model
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
     * @var integer
     */
    public $goods_id;

    /**
     *
     * @var integer
     */
    public $add_time;

    /**
     *
     * @var integer
     */
    public $is_attention;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'rec_id' => 'recId',
            'user_id' => 'userId',
            'goods_id' => 'goodsId',
            'add_time' => 'createAt',
            'is_attention' => 'is_attention'
        );
    }

    public function initialize() {
    	$this->createAt = time();
    	$this->skipAttributesOnCreate(array('is_attention'));
    }

}

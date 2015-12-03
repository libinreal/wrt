<?php

class ShopConfig extends \Phalcon\Mvc\Model
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
    public $parent_id;

    /**
     *
     * @var string
     */
    public $code;

    /**
     *
     * @var string
     */
    public $type;

    /**
     *
     * @var string
     */
    public $store_range;

    /**
     *
     * @var string
     */
    public $store_dir;

    /**
     *
     * @var string
     */
    public $value;

    /**
     *
     * @var integer
     */
    public $sort_order;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'parent_id' => 'parent_id', 
            'code' => 'code', 
            'type' => 'type', 
            'store_range' => 'store_range', 
            'store_dir' => 'store_dir', 
            'value' => 'value', 
            'sort_order' => 'sort_order'
        );
    }

}

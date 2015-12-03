<?php

class ExchangeGoodsType extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $cat_id;

    /**
     *
     * @var string
     */
    public $cat_name;

    /**
     *
     * @var integer
     */
    public $enabled;

    /**
     *
     * @var string
     */
    public $attr_group;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'cat_id' => 'cat_id', 
            'cat_name' => 'cat_name', 
            'enabled' => 'enabled', 
            'attr_group' => 'attr_group'
        );
    }

}

<?php

class ExchangeAttribute extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $attr_id;

    /**
     *
     * @var integer
     */
    public $cat_id;

    /**
     *
     * @var string
     */
    public $attr_name;

    /**
     *
     * @var integer
     */
    public $attr_input_type;

    /**
     *
     * @var integer
     */
    public $attr_type;

    /**
     *
     * @var string
     */
    public $attr_values;

    /**
     *
     * @var integer
     */
    public $attr_index;

    /**
     *
     * @var integer
     */
    public $sort_order;

    /**
     *
     * @var integer
     */
    public $is_linked;

    /**
     *
     * @var integer
     */
    public $attr_group;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'attr_id' => 'attr_id', 
            'cat_id' => 'cat_id', 
            'attr_name' => 'attr_name', 
            'attr_input_type' => 'attr_input_type', 
            'attr_type' => 'attr_type', 
            'attr_values' => 'attr_values', 
            'attr_index' => 'attr_index', 
            'sort_order' => 'sort_order', 
            'is_linked' => 'is_linked', 
            'attr_group' => 'attr_group'
        );
    }

}

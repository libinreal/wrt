<?php

class Suppliers extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $suppliers_id;

    /**
     *
     * @var string
     */
    public $suppliers_name;

    /**
     *
     * @var string
     */
    public $suppliers_desc;

    /**
     *
     * @var integer
     */
    public $is_check;

    /**
     *
     * @var string
     */
    public $suppliers_code;

    /**
     *
     * @var string
     */
    public $area_name;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'suppliers_id' => 'id',
            'suppliers_name' => 'supplier',
            'suppliers_desc' => 'suppliers_desc',
            'is_check' => 'is_check',
            'suppliers_code' => 'suppliers_code',
            'area_name' => 'areaName'
        );
    }

}

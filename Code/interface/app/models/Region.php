<?php

class Region extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $region_id;

    /**
     *
     * @var integer
     */
    public $parent_id;

    /**
     *
     * @var string
     */
    public $region_name;

    /**
     *
     * @var integer
     */
    public $region_type;

    /**
     *
     * @var integer
     */
    public $agency_id;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'region_id' => 'id',
            'parent_id' => 'parent_id',
            'region_name' => 'name',
            'region_type' => 'region_type',
            'agency_id' => 'agency_id'
        );
    }

}

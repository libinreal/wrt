<?php

class BrandRecommend extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $brand_rid;

    /**
     *
     * @var integer
     */
    public $area_id;

    /**
     *
     * @var integer
     */
    public $cat_code;

    /**
     *
     * @var integer
     */
    public $brand_id;

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
            'brand_rid' => 'rid',
            'area_id' => 'areaId',
            'cat_code' => 'code',
            'brand_id' => 'id',
            'sort_order' => 'sort'
        );
    }

}

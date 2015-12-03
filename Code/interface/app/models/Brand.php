<?php

class Brand extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $brand_id;

    /**
     *
     * @var string
     */
    public $brand_name;

    /**
     *
     * @var string
     */
    public $brand_logo;

    /**
     *
     * @var string
     */
    public $brand_desc;

    /**
     *
     * @var string
     */
    public $site_url;

    /**
     *
     * @var integer
     */
    public $sort_order;

    /**
     *
     * @var integer
     */
    public $is_show;

    /**
     *
     * @var string
     */
    public $brand_code;

    /**
     *
     * @var string
     */
    public $brand_pinyin;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'brand_id' => 'id',
            'brand_name' => 'factoryName',
            'brand_logo' => 'logo',
            'brand_desc' => 'brand_desc',
            'site_url' => 'site_url',
            'sort_order' => 'sort_order',
            'is_show' => 'is_show',
            'brand_code' => 'code',
            'brand_pinyin' => 'brand_pinyin'
        );
    }

}

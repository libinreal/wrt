<?php

class ExchangeCategory extends \Phalcon\Mvc\Model
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
     * @var string
     */
    public $keywords;

    /**
     *
     * @var string
     */
    public $cat_desc;

    /**
     *
     * @var integer
     */
    public $parent_id;

    /**
     *
     * @var integer
     */
    public $sort_order;

    /**
     *
     * @var string
     */
    public $template_file;

    /**
     *
     * @var string
     */
    public $measure_unit;

    /**
     *
     * @var integer
     */
    public $show_in_nav;

    /**
     *
     * @var string
     */
    public $style;

    /**
     *
     * @var integer
     */
    public $is_show;

    /**
     *
     * @var integer
     */
    public $grade;

    /**
     *
     * @var string
     */
    public $filter_attr;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'cat_id' => 'id',
            'cat_name' => 'name',
            'keywords' => 'keywords',
            'cat_desc' => 'cat_desc',
            'parent_id' => 'parent_id',
            'sort_order' => 'sort',
            'template_file' => 'template_file',
            'measure_unit' => 'measure_unit',
            'show_in_nav' => 'show_in_nav',
            'style' => 'style',
            'is_show' => 'is_show',
            'grade' => 'grade',
            'filter_attr' => 'filter_attr'
        );
    }

}

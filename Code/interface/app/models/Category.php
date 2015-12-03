<?php

class Category extends \Phalcon\Mvc\Model
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
     *
     * @var integer
     */
    public $cat_level;

    /**
     *
     * @var integer
     */
    public $code;

    /**
     *
     * @var string
     */
    public $wcode;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'cat_id' => 'id',
            'cat_name' => 'name',
            'keywords' => 'keywords', //分类的关键字,seo
            'cat_desc' => 'cdesc',
            'parent_id' => 'parentId',
            'sort_order' => 'sort',
            'template_file' => 'template_file', //该分类的单独模板文件的名字
            'measure_unit' => 'unit', //分类的计量单位
            'show_in_nav' => 'show_in_nav', //是否显示在导航栏,0不;1显示
            'style' => 'style', //该分类的单独的样式表的包括文件部分的文件路径
            'is_show' => 'is_show',  //是否在前台页面显示 1显示; 0不显示
            'grade' => 'grade', //该分类的最高和最低价之间的价格分级,当大于1时,会根据最大最小价格区间分成区间,会在页面显示价格范围,如0-300,300-600,600-900这种;
            'filter_attr' => 'attr', //如果该字段有值,则该分类将还会按照该值对应在表goods_attr的goods_attr_id所对应的属性筛选，如，封面颜色下有红，黑分类筛选
            'cat_level' => 'level',
            'code' => 'code',
            'wcode' => 'wcode'
        );
    }

}

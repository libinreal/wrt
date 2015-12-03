<?php

class GoodsType extends \Phalcon\Mvc\Model
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
     *
     * @var integer
     */
    public $code;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'cat_id' => 'id',
            'cat_name' => 'name',
            'enabled' => 'enabled', //类型状态1，为可用；0为不可用；不可用的类型，在添加商品的时候选择商品属性将不可选
            'attr_group' => 'group', //商品属性分组，将一个商品类型的属性分成组，在显示的时候也是按组显示。该字段的值显示在属性的前一行，像标题的作用
            'code' => 'code' //对应商品分类编码
        );
    }

    public function initialize() {
    	$this->hasOne('code', 'Attribute', 'code');
    }

}

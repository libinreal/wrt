<?php

class Attribute extends \Phalcon\Mvc\Model
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
     *
     * @var string
     */
    public $attr_code;

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
            'attr_id' => 'id', //主键ID
            'cat_id' => 'catId', //分类ID 和商品分类表关联。
            'attr_name' => 'name', //属性名称
            'attr_input_type' => 'attr_input_type', //属性的数据类型，当添加商品时,该属性的添加类别：0为手功输入;1为选择输入;2为多行文本输入。目前值均为1
            'attr_type' => 'attr_type', //属性是否多选; 0否; 1是 如果可以多选,则可以自定义属性,并且可以根据值的不同定不同的价
            'attr_values' => 'avalues', //即选择输入,则attr_name对应的值的取值就是该这字段值 （多个值用换行做为分割）
            'attr_index' => 'attr_index', //属性是否可以检索;0不需要检索; 1关键字检索2范围检索,该属性应该是如果检索的话,可以通过该属性找到有该属性的商品.默认为1
            'sort_order' => 'sort', //属性显示的顺序,数字越大越靠前,如果数字一样则按id顺序
            'is_linked' => 'is_linked', //是否关联,0 不关联 1关联; 如果关联, 那么用户在购买该商品时,具有有该属性相同的商品将被推荐给用户。默认为1
            'attr_group' => 'attr_group', //属性分组,相同的为一个属性组应该取自ecs_goods_type的attr_group的值的顺序.
            'attr_code' => 'attr_code',
            'code' => 'code'
        );
    }

}

<?php
/**
 * Created by PhpStorm.
 * User: wliu
 * Date: 14-9-6
 * Time: 上午2:46
 */


namespace PhpRudder\Mvc;


class ModelBase extends \Phalcon\Mvc\Model {

    /**
     * 检查对象值
     * true : 检查
     * false : 不检查
     */
    public function initialize() {
        self::setup(array(
            'notNullValidations' => false,
        ));
    }

}
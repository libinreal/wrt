<?php
/**
 * Created by PhpStorm.
 * User: wliu
 * Date: 14-8-31
 * Time: 上午3:29
 */

namespace PhpRudder\Mvc;


class TreeNode {

    public $id;

    public $name;

    public $children;

    public $properties;

    public $parentId;

    public $code;

    public $level;

    public function addChild($node) {
       if(!isset($this->children)) {
           $this->children = array();
       }
       array_push($this->children, $node);
    }

    public function setProperties($properties) {
        $this->properties = $properties;
    }
}
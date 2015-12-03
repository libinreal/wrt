<?php
/**
 * Created by PhpStorm.
 * User: wliu
 * Date: 14-9-6
 * Time: 上午12:28
 */

namespace PhpRudder;


interface Serviceable {
    /**
     *
     * @return \Phalcon\Logger\Adapter\File;
     */
    public function get_logger();

    /**
     *
     * @return \PhpRudder\Cache\Redis;
     */
    public function get_cache();
} 
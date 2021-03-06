<?php

error_reporting(E_ALL);

try {

    /**
     * Read the configuration
     */
    $config = include __DIR__ . "/../app/config/config.php";

    /**
     * Read auto-loader
     */
    include __DIR__ . "/../app/config/loader.php";

    /**
     * Read services
     */
    include __DIR__ . "/../app/config/services.php";

    /**
     * read inc_constant.php
     */
    define('IN_ECS', 1);
    include $config['adminpath'];

    /**
     * Handle the request
     */
    $application = new \Phalcon\Mvc\Application($di);

    echo $application->handle()->getContent();

} catch (Exception $e) {
    echo json_encode(\PhpRudder\Http\ResponseApi::send(null, Message::$_ERROR_SYSTEM, $e->getMessage()));
}

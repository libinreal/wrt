<?php

use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Logger\Adapter\File as Logger;


/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * We register the events manager
 */
$di->set('dispatcher', function() use ($di) {

    $eventsManager = $di->getShared('eventsManager');

    $security = new Security($di);

    /**
     * We listen for events in the dispatcher using the Security plugin
     */
    $eventsManager->attach('dispatch', $security);

    $dispatcher = new Phalcon\Mvc\Dispatcher();
    $dispatcher->setEventsManager($eventsManager);

    return $dispatcher;
});

$di->set('request', function() use ($di) {
	$request = new Phalcon\Http\Request();
	return $request;
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
}, true);

$di->set('dbCfg', function () use ($config) {
    return PhpRudder\CommonUtil::object2Array($config['database']);
}, true);

/**
 * Setting up the view component
 */
$di->set('view', function () use ($config) {

    $view = new View();

    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines(array(
        '.volt' => function ($view, $di) use ($config) {

            $volt = new VoltEngine($view, $di);

            $volt->setOptions(array(
                'compiledPath' => $config->application->cacheDir,
                'compiledSeparator' => '_'
            ));

            return $volt;
        },
        '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
    ));

    return $view;
}, true);

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set('db', function() use ($di, $config) {

    $eventsManager = $di->getShared('eventsManager');

    $logger = new Logger(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . "debug.log");

    //Listen all the database events
    $eventsManager->attach('db', function($event, $connection) use ($logger) {
       if ($event->getType() == 'beforeQuery') {
            $logger->log($connection->getSQLStatement(), LOG_DEBUG);
        }
    });

    $connection = new \Phalcon\Db\Adapter\Pdo\Mysql($di->getdbCfg());

    //Assign the eventsManager to the db adapter instance
    $connection->setEventsManager($eventsManager);
    return $connection;
}, true);

$di->setShared('transactions', function() {
    return new \Phalcon\Mvc\Model\Transaction\Manager();
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->set('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Start the session the first time some component request the session service
 */
$di->set('session', function () use ($config) {
    $session = new \PhpRudder\Http\RedisSession($config->redis);
    $session->start();
    return $session;
});


/**
 * 注册config
 */
$di->set('config', function() use ($config) {
    return $config;
});

$di->set('log', function() {
    $logger = new Logger(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'zjwr.log');
    return $logger;
});

/**
 * Start the session the first time some component request the session service
 */
$di->set('cache', function () use ($config) {
    $redis = new \PhpRudder\Cache\Redis(null, $config->redis);
    return $redis;
});

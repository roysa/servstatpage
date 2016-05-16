<?php

require __DIR__ . '/../vendor/autoload.php';

$config = realpath(__DIR__ . '/../app/config') . '/www.php';

defined('YII_DEBUG') or define('YII_DEBUG', true);

if (YII_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', true);
}

$app =Yii::createWebApplication($config);

$app->run();
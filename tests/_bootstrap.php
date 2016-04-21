<?php
// This is global bootstrap for autoloading
date_default_timezone_set('Europe/Paris');

// ensure we get report on all possible php errors
error_reporting(E_ALL);
define('YII_ENABLE_ERROR_HANDLER', false);
define('YII_DEBUG', true);
$_SERVER['SCRIPT_NAME'] = '/' . __DIR__;
$_SERVER['SCRIPT_FILENAME'] = __FILE__;
require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

define('POSTMARK_FROM', '<sender>');
define('POSTMARK_TOKEN', '<token>');
define('POSTMARK_TO', '<target>');
define('POSTMARK_TEMPLATE', 575741);

define('POSTMARK_TEST_SEND', false);


Yii::setAlias('@tests/unit', __DIR__ . '/unit');
Yii::setAlias('@sweelix/postmark', dirname(__DIR__) .'/src');
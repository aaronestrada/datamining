<?php
/**
 * This is the bootstrap file for lab3 application.
 * This file should be removed when the application is deployed for production.
 */

// change the following paths if necessary
$yii=dirname(__FILE__).'/../../../usr/share/yii-1.1.16/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/lab3.php';

// remove the following line when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);

require_once($yii);
Yii::createWebApplication($config)->run();

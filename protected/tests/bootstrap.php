<?php

// change the following paths if necessary
$yiit=dirname(__FILE__).'/../../../../../usr/share/yii-1.1.16/framework/yiit.php';
$config=dirname(__FILE__).'/../config/lab3.php';

require_once($yiit);
require_once(dirname(__FILE__).'/WebTestCase.php');

Yii::createWebApplication($config);

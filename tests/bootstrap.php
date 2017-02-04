<?php
define('AE_LANG', 'ja');
$loader = require '/xampp/php/composer/vendor/autoload.php';
$loader->add('Ae', realpath(__DIR__ . '/../lib'));
$loader->add('AeTest', realpath(__DIR__ . '/lib'));

<?php
/**
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 */
define('APPHOME', dirname(__DIR__) . '/');
define('AE_LANG', 'ja');
require APPHOME . '../../lib/Ae/autoload.php';

spl_autoload_register(function ($class) {
    $prefix = 'SampleForm\\';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $f = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($f)) {
        require $f;
    }
});

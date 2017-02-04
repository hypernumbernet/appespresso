<?php
/**
 * クラスオートローダー
 * 
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @copyright 2009-2017 Tomohito Inoue
 * @license MIT
 */
spl_autoload_register(function ($class) {
    $prefix = 'Ae\\';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative = substr($class, $len);
    $f = __DIR__ . '/' . str_replace('\\', '/', $relative) . '.php';
    if (file_exists($f)) {
        require $f;
    }
});

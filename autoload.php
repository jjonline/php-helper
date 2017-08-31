<?php
/**
 * jjonline/php-helper额外提供的自动加载文件
 * @authors Jea杨 (JJonline@JJonline.Cn)
 * @date    2017-08-07 10:01:41
 * @version $Id$
 */

/**
 * 注册自动加载函数
 */
spl_autoload_register(function ($class) {
    // 加载静态方法类
    if (0 === stripos($class, "jjonline\\helper\\")) {
        $filename = __DIR__ . DIRECTORY_SEPARATOR . str_replace("jjonline\\helper\\", 'src' . DIRECTORY_SEPARATOR . 'function'.DIRECTORY_SEPARATOR, $class) . '.php';
        file_exists($filename) && require($filename);
    }
    // 加载对象类
    if (0 === stripos($class, "jjonline\\library\\")) {
        $filename = __DIR__ . DIRECTORY_SEPARATOR . str_replace("jjonline\\library\\", 'src' . DIRECTORY_SEPARATOR . 'library'.DIRECTORY_SEPARATOR, $class) . '.php';
        file_exists($filename) && require($filename);
    }
});

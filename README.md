jjonline/php-helper
===================

[![Latest Stable Version](https://poser.pugx.org/jjonline/php-helper/v/stable)](https://packagist.org/packages/jjonline/php-helper)
[![License](https://poser.pugx.org/jjonline/php-helper/license)](https://packagist.org/packages/jjonline/php-helper)
[![Build Status](https://travis-ci.org/jjonline/php-helper.svg?branch=master)](https://travis-ci.org/jjonline/php-helper)

jjonline/php-helper是日常开发过程中积累提炼而来，收集整理了基于静态类的多个常用函数方法和通用的对象类，PHP版本要求至少5.4，基于命名空间，适配composer，方便composer一键安装和管理。

## 安装

1、composer安装

`composer require jjonline/php-helper`


2、直接引用自动加载文件

`require_once './autoload.php';`

> 强烈建议使用composer进行包管理和安装


## 文档

快速示例：
~~~
    use jjonline\helper\FilterValid;
    use jjonline\helper\Tools;
    use jjonline\library\Http;
    
    // 表单效验
    $isMailValid     = FilterValid::is_mail_valid('jjonline@jjonline.cn');
    $isPhoneValid    = FilterValid::is_phone_valid('13800138000');

    // ...

    // 通用方法
    $isMobileBrowser = Tools::is_mobile_browser();
    $status          = Tools::rm_dir('./test');

    // ...

    // 通用http操纵类
    $Http            = Httpd::init();
    $Http->setData('postField','postVallue')
         ->setTimeOut(10)
         ->setReferer('http://blog.jjonline.cn')
         ->setUserAgent('Mozilla/5.0')
         ->setRequestHeader('X-Powered-By','JJonline.Cn')
         ->setRequestCookie('cookieName','cookieValue')
         ->post('http://blog.jjonline.cn');
    if($Http->getErrno === 0)
    {
        $Http->save('/var/www/index.html');
        echo $Http->getBody();
    }
~~~

jjonline/php-helper包含两部分：

* 静态类函数方法
* 对象类
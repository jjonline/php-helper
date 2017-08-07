# php-helper常用PHP工具函数和类

[![Latest Stable Version](https://poser.pugx.org/jjonline/php-helper/v/stable)](https://packagist.org/packages/jjonline/php-helper)
[![License](https://poser.pugx.org/jjonline/php-helper/license)](https://packagist.org/packages/jjonline/php-helper)
[![Build Status](https://travis-ci.org/jjonline/php-helper.svg?branch=master)](https://travis-ci.org/jjonline/php-helper)

## 安装

### 1、composer安装

`composer require jjonline/php-helper`

### 2、直接引用自动加载文件

`require_once './autoload.php';`

> 强烈建议使用composer进行包管理和安装


## 一、函数方法(帮助函数类)

命名空间：`jjonline\helper`

1.1、表单效验

`use jjonline\helper\FilterValid;`

* `FilterValid::is_mail_valid('jjonline@jjonline.cn')`效验是否为一个邮箱地址，邮箱名由：`字母`、`数字`、`+` 、`-`和`.`构成
>效验正确返回true，效验失败返回false
* `FilterValid::is_phone_valid('13800138000')`效验是否为一个天朝的11位手机号，会严格检测号段前三位
>效验正确返回true，效验失败返回false
* `FilterValid::is_url_valid('http://blog.jjonline.cn:80/php-helper/index.php?a=1#b')`效验是否为一个合法的http或https打头的Url
* `FilterValid::is_uid_valid('337339190',4,11)`效验是否为一个合法的数字账户ID，第二个**可选参数**为最小长度，默认值4；第三个**可选参数**为最大长度，默认值11
>效验符合返回true，效验不符返回false
* `FilterValid::is_password_valid('mm123456',6,18)`效验是否为一个合法的密码，第二个**可选参数**为最小长度，默认值6；第三个**可选参数**为最大长度，默认值18，合法密码的逻辑为：必须同时包含字母和数字
>效验符合返回true，效验不符返回false
* `FilterValid::is_chinese_valid('这是中文')`效验是否为UTF8编码下的汉字中文
>效验符合返回true，效验不符返回false
* `FilterValid::is_utf8_valid('o(╯□╰)o')`效验是否为UTF8编码
>效验符合返回true，效验不符返回false
* `FilterValid::is_citizen_id_valid('420521198907031846')`效验是否符合天朝身份证号编码规则，兼容16位老身份证和18位新身份证，符合返回`数组`不符返回`false`
>效验符合编码规则返回数组，例如上例将返回：`array(6) { ["id"]=> string(18) "420521198907031846" ["location"]=> string(6) "湖北" ["Y"]=> string(4) "1989" ["m"]=> string(2) "07" ["d"]=> string(2) "03" ["sex"]=> int(0) }`；不符合返回false

1.2、通用函数

`ues jjonline\helper\Tools;`



## 二、常用Class类(library)

命名空间：`jjonline\library`

2.1、Http各方法封装类

*该类尚未完结*

`use jjonline\library\Http;`
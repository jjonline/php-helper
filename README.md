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


## 函数方法(帮助函数类)

命名空间：`jjonline\helper`

### 1.1、表单效验

`use jjonline\helper\FilterValid;`

* `FilterValid::is_mail_valid('jjonline@jjonline.cn')`效验是否为一个邮箱地址，邮箱名由：`字母`、`数字`、`+` 、`-`和`.`构成
>效验正确返回true，效验失败返回false
* `FilterValid::is_phone_valid('13800138000')`效验是否为一个天朝的11位手机号，会严格检测号段前三位
>效验正确返回true，效验失败返回false
* `FilterValid::is_url_valid('http://blog.jjonline.cn:80/php-helper/index.php?a=1#b')`效验是否为一个合法的http或https打头的Url
>效验符合返回true，效验不符返回false
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

### 1.2、通用函数

`ues jjonline\helper\Tools;`


* `Tools::is_weixin_browser($_SERVER['HTTP_USER_AGENT'])` 检查是否在微信浏览器内打开，**可选参数**传入客户端浏览器UserAgent字符串
>效验是返回true，效验不是返回false

* `Tools::is_mobile_browser()` 检查是否在移动端浏览器内打开，当然这个函数检测的移动端浏览器类型要比`is_weixin_browser`更广
>效验是返回true，效验不是返回false

* `Tools::hide_name('Jea杨')` 用户名、用户昵称或用户姓名脱敏处理
>字符长度小于3返回3个**\***，长度大于3则返回首位各1个字符以及中间的3个**\***号字符，例如本例返回:`J***杨`

* `Tools::hide_ipv4('223.5.5.5')` IPv4地址脱敏处理，譬如：用户的注册IPv4地址、上次登录的IPv4地址
>返回IPv4中间两位脱敏后的字符，例如本例返回：`223.**.**.5`

* `Tools::nl2p("这是一段带有换行符\n\n的字符串")` 将换行符转换成p标签，nl2br的类似函数只不过不是转换为`<br>`标签而是`<p>`成对标签
>返回`<p>`程度标签包裹处理后的字符串，例如本例返回:`<p>这是一段带有换行符</p><p>的字符串</p>`
>关于换行符的说明：win、linux以及mac下的换行符由`\n`和`\r`转义符构成，两者组合也算，多个紧挨着的换行符将当做一个换行符处理
>注意返回值将在`</p>`结尾标签之后加入一个`\n`换行，本例返回值完整表示法为：`"<p>这是一段带有换行符</p>\n<p>的字符串</p>"`，注意PHP中单双引号的区别！

* `Tools::time_ago('1502075022')` 将linux时间戳转换为`xx前`表示法
>返回值依据传入linux时间戳与当前时间戳的差值不同而不同，返回值有如下几种：`刚刚、`1分钟前`、`2小时前`、`3周前`、`5个月前`、`3年前` 等

* `Tools::is_ssl()` 检测当前http请求是否为ssl加密方式
>是ssl加密方式返回true不是返回false，说人话就是：`http协议`返回false，`https协议`返回true

* `Tools::get_client_ip(0,false)` 获取客户端的IPv4地址，第一个**可选参数**1和true返回IPv4数字表示法，0和false返回IPv4字符串，默认值0；第二个**可选参数**是否检测http代理情况，false不检测，true检测，默认值false
>依据第一个参数返回客户端的IPv4地址：例如：`113.87.120.33` 或 `1901557793`

* `Tools::redirect('http://blog.jjonline.cn',0,null)` http重定向，第一个参数为重定向的网址，第二个**可选参数**为等待调整的时间（单位为秒），默认值0，第三个**可选参数**为等待调整时页面显示的文字
>没有返回值，注意调用该函数后脚本将终止运行，函数中使用`exit`

* `Tools::dump('xxx',true,null,true)` 开发时浏览器友好的调试输出任意变量，来源于ThinkPHP
>第二个**可选参数**为false将返回处理好的字符串，为true直接输出没有返回值，默认值为`true`;
>第三个**可选参数**指定本次调试的标签，也就是在输出或返回值前加上这个标签值，便于开发调试，默认值`null`；
>第四个**可选参数**指定本次输出是否严格模式，也就是在输出或返回值的时候是否将html标签转义，默认值`true`；

* `Tools::transfer_encrypt('420521198907031846','man',30)` 来源于discuz的可逆加解密算法，用于有有效期效验的数据交换，加密强度很弱，请甄别使用场景
>第一个参数：待加密的字符串
>第二个参数：本次加密的秘钥
>第三个可选参数：本次加密的有效时间，单位秒，在该参数指定的时间内解密有效----其实超过该时间有秘钥也是能解密出明文的，默认值0表示永不失效
>返回值：`9a501yAwnLi8rf9+mxH8shChfz/sI4NPtbmWO0/OtoPMhq3J5xoCZoUc4YEJuWs` 每次值都不一样的

* `Tools::transfer_decrypt('9a501yAwnLi8rf9+mxH8shChfz/sI4NPtbmWO0/OtoPMhq3J5xoCZoUc4YEJuWs','man')` 将`transfer_encrypt`加密的值解密
>第一个参数：待解密的密文
>第二个参数：解密的秘钥
>返回值：已失效或解密失败返回空值，解密成功返回明文

* `Tools::mbsubstr('xxsd',0,1,'utf-8')` 支持截取中文字符截断，参数与`mb_substr`一致，该方法主要是做了一个兼容处理
>返回参数指定的截取后的字符串

* `Tools::get_rand_string(6,'','')` 获取随机字符串
>第一个**可选参数**指定返回字符串长度，默认值6
>第二个**可选参数**指定返回字符串类型，取值空字符串、0字母、1数字、2大写字母、3小写字母、4中文，默认值空表示返回大小写字母和数字混合的随机字符串，0表示返回大小写字母混合的字符串，以此类推
>第三个**可选参数**指定附加的用于返回值随机字符串的字符元素，函数中的数字、大小写字母去除了易混淆的字母oOLl和数字0和1，若不需要去除通过第三个参数附加进去即可，默认值空字符串

* `Tools::clear_js_code('<p class="sd" ><img src="http://onload=.qq.com/onerror.jpg" alt="test" title=" onError="alert("d")""/></p>')` 去除字符串中的js代码，包括大段的由<script>标签包裹的代码和各html标签属性中`Window事件属性`、`Form事件属性`、`Keyboard事件属性`、`Mouse事件`和较少的`Media事件属性`等属性事件代码
Html事件属性参考：[HTML事件属性](http://www.w3school.com.cn/tags/html_ref_eventattributes.asp)
>返回去除js代码后的字符串，本例返回：`<p class="sd"><img src="http://onload=.qq.com/onerror.jpg" alt="test" title="></p>`
>本例返回值好像有问题啊，但有各种刁钻的绕过属性事件被去除的方式，示例中的参数明显就是非正常的，返回值非正常但安全就ok

* `Tools::to_absolute_url('./../../171.html','http://blog.jjonline.cn/sort/php/area/article/173.html')` 获取相对于挡墙网页中的相对超链接的完整链接
>第一个参数：需要被转换的相对Url
>第二个参数：相对于的绝对Url
>这个方法怎么理解呢？存在这么个网页地址为：`http://blog.jjonline.cn/sort/php/area/article/173.html`，然后这个网页里有一个超链接是`./../../171.html`这种形式的，那么这个超链接的绝对网址也就是带http协议头和域名以及目录后的完整Url是啥呢？这个方法完成
>示例返回值：`http://blog.jjonline.cn/sort/php/171.html`

* `Tools::rm_dir('./test')` 删除非空目录，PHP提供有`bool rmdir ( string $dirname [, resource $context ] )`，但该方法要求这个拟删除的目录必须是空目录
>参数为拟删除目录的路径，相对路径和绝对路径均可，相对路径是相对于直接被执行的PHP入口文件
>返回boolean，ture删除成功，fasle则出现异常，可能是拟删除的目录不存在
>这个方法是比较危险的!!!使用前请搞清楚你要干什么~~

---

>**因为全局函数可能会导致全局函数名污染或与其他库和项目存在的其他全局函数名导致冲突，固本库helper函数采用命名空间下的静态类方法，效果是一样的。**

---

## 二、常用Class类(library)

命名空间：`jjonline\library`

2.1、Http各方法封装类

*该类尚未完结*

`use jjonline\library\Http;`
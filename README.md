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

`uese jjonline\helper\Tools;`


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
>返回值依据传入linux时间戳与当前时间戳的差值不同而不同，返回值有如下几种：`刚刚`、`1分钟前`、`2小时前`、`3周前`、`5个月前`、`3年前` 等

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

> `常用Class类(library)`从v2.0版开始添加！

命名空间：`jjonline\library`

2.1、Http各方法封装类

>基于curl的支持get、post两种常见的http请求方法封装，支持设置cookie、Referer、User-Agent、自定义curl参数、下载保存文件以及post上传文件。
>支持链式操作

`use jjonline\library\Http;`


### 初始化和设置/获取初始化参数

>初始化Http类单例
$http = Http::init();

#### 设置/获取请求的url
`$http->setUrl('http://blog.jjonline.cn');` 和 `$http->getUrl();` 
>设置请求的远程Url网址，或在调用最终`get`、`post`方法时第一个参数传入，请参考`get`、`post`方法说明

>该方法可以链式调用，多次调用后面调用设置的值将覆盖前面调用设置的值

#### 设置连接超时的时间
`$http->setTimeOut(30);` 和 `$http->getTimeOut();` 
>设置连接超时的最大时间，单位：秒

>setTimeOut方法可以链式调用，多次调用后面调用设置的值将覆盖前面调用设置的值

#### 设置/获取http请求体中header头部分的自定义项目
`$http->setRequestHeader('Content-type','text/plain');` 和 `$http->getRequestHeader('Content-type');`
>设置http请求时附加在请求体中header头部分的项目内容该方法第二个参数可选，第一个参数可以是二维数组`[['Content-type'=>'text/plain'],['Content-length'=>'1024']]`，用于一次调用设置多个自定义header头

>setRequestHeader方法可以多次、链式调用，多次调用设置多个header头条目

#### 设置/获取请求体header头的Referer
`$http->setReferer('http://blog.jjonline.cn');` 和 `$http->getReferer();`
>设置请求的eader头的Referer，Referer是什么就不解释了

>setReferer方法可以链式调用，多次调用后面调用设置的值将覆盖前面调用设置的值

#### 设置/获取请求体header头的User-Agent值
`$http->setUserAgent('http://blog.jjonline.cn');` 和 `$http->getUserAgent();`
>设置请求的header头的User-Agent值，User-Agent值是什么就不解释了

>setUserAgent方法可以链式调用，多次调用后面调用设置的值将覆盖前面调用设置的值

#### 设置/获取Post发送的数据key-value
`$http->setData('fieldName','fieldValue');` 和 `$http->getData('fieldName');`
>第二个参数为可选参数，当仅适用1个参数时，必须为key-vallue形式的二维数组，这样一次可以设置多个post数据字段`$http->setData([['fieldName1'=>'fieldValue1'],['fieldName2'=>'fieldValue2']...]);`
>设置Post请求发送的数据体key-value内容

>setData方法可以多次、链式调用，多次调用设置多个Post发送的键值对或覆盖


#### 设置/获取拟发送的数据中附带的cookie键值对
`$http->setRequestCookie('cookieName','cookieValue');` 和 `$http->geetRequestCookie('cookieName');`
>key-vallue形式的二维数组一次设置多个`$http->setData([['cookieName1'=>'cookieValue1'],['cookieName2'=>'cookieValue2']...]);`
>设置请求的本次请求拟发送的cookie键值对；获取已设置的cookie值需要传入获取拟发送的cookie的名字
>第二个参数可选，第一个参数可以有多种形式：第一种`[['cookieName'=>'cookieValue'],['cookieName1`'=>'cookieValue1']...]`一次设置多个cookie的二维数组，第二种符合curl_setopt设置cookie参数格式的字符串，例如：`cookieName=cookieValue; cookieName1=cookieValue1; cookie_c=3`这种形式，注意分号和空格。

>setRequestCookie方法可以多次、链式调用，多次调用设置多个cookie键值对或覆盖

#### 设置Post方法拟上传的文件
`$http->setUploadFile('UploadFileFieldName','FileDir');`
>设置Post方法上传的文件，第一个参数为该form域的名字，第二个参数为拟上传文件的路径

>setUploadFile方法可以多次、链式调用，多次调用设置多个拟上传的文件或覆盖


#### 获取cUrl的设置参数
`$http->setOption(CURLOPT_REFERER);`
>获取用来设置`curl_setopt`函数方法参数的参数值，也可以`$http->setOption('CURLOPT_REFERER');`这样调用，但不建议~

#### 高阶自定义设置：设置cUrl的参数
`$http->setOption(CURLOPT_REFERER,'http://blog.jjonline.cn');`
>该方法的参数与`curl_setopt(resource $ch , int $option , mixed $value )`第2、3两个参数一致即可
>setOption方法第一个参数为常量，可选的常量请参考：[curl_setopt常量](http://php.net/manual/zh/function.curl-setopt.php)
>代码做了兼容处理，`$http->setOption('CURLOPT_REFERER','http://blog.jjonline.cn');`这种写法也是可以的，但不推荐
>注意`setOption`方法是底层实现设置的方法，若要自定义cUrl底层方法，请弄清楚你要做什么，否则可能导致参数覆盖，例如本例中设置的是请求信息header头中的referer，这种方法是可行的但不推荐！推荐使用`$http->setReferer($referer)`方法，基本上常用的设置方法都已做了封装。
>例如：需要启用https的严格效验，就可用通过该方法设置`CURLOPT_SSLCERTTYPE`、`CURLOPT_SSL_VERIFYHOST`、`CURLOPT_CAINFO`或`CURLOPT_CAPATH`等值
>setOption该方法可以多次、链式调用，多次调用设置多个参数值或覆盖

> **设置值时抛出异常请在开发阶段就予以解决，不要试图使用try语句忽略**


### 执行http请求

#### 执行get请求
`$http->get($url);`
>可选的设置方法调用完毕，最后调用`get`方法执行get请求

>方法体返回boolean值，true请求执行成功，false请求执行失败，获取请求成功的响应数据或请求失败的失败信息请继续往下看


#### 执行post请求
`$http->get($url,$data);`
>可选的设置方法调用完毕，最后调用`post`方法执行post请求，两个可选参数，第一个为post的网址，第二个关联二维数组传入本次post提交的键值对数据

>方法体返回boolean值，true请求执行成功，false请求执行失败，获取请求成功的响应数据或请求失败的失败信息请继续往下看

#### 保存请求成功后的数据，或者称之为：下载远程数据
`$http->save($local_file_dir);`
>执行完get或post方法后，可以将执行成功返回的数据保存至本地服务器，`$local_file_dir`指定保存的文件的路径
>save方法返回Boolean值，true保存文件成功，false保存文件失败、或尚未执行get或post方法、或执行get或post方法失败

>需要注意的是save方法需要在get或post方法执行之后另行调用，get和post方法不支持链式调用，所以不要在get或post方法后再链式调用save方法；例如:

    $ret = $http->get($url);
    $ret && $http->save($dir);


### 获取http请求成功后的数据

#### 获取请求成功后返回的包含header头的原始数据
`$http->getResult();`
>返回请求成功后http响应原始数据，若未执行或执行失败返回空，不要依据该方法返回空来判断执行成功还是失败

#### 获取请求成功后返回数据的header头
`$http->getHeader();`
>返回请求成功后http响应头信息，若未执行或执行失败返回空，不要依据该方法返回空来判断执行成功还是失败

#### 获取请求成功后返回数据的body主体内容
`$http->getBody();`
>返回请求成功后http响应的主体内容，譬如：请求某个api后返回的json字符串；若未执行或执行失败返回空，不要依据该方法返回空来判断执行成功还是失败

#### 获取请求成功后返回数据中的cookie键值对数组
`$http->getResponseCookie();`
>该方法有个可选参数，取值true或false，默认值false表示返回处理好的cookie键值对二维数组，例如：`[['JID'=>'so7i7srvbk4c5dd0748df8va23'],['token'=>'6a4ee8169908dc4ec0700008fe0c1085']]`，传值true表示返回header头中cookie键值对的原始表示法的一维数组，用于进一步处理获取一些信息，例如：`['JID=so7i7srvbk4c5dd0748df8va23; path=/; domain=.jjonline.cn; HttpOnly','token=6a4ee8169908dc4ec0700008fe0c1085; path=/; domain=.jjonline.cn; HttpOnly']`
>若未执行或执行失败返回空数组，若请求的响应体中没有cookie也返回空数组，所以不要依据该方法返回空数组来判断执行成功还是失败


### 获取http请求失败后的数据

#### 获取请求失败后的错误描述字符串，`curl_error`的返回值
`$http->getError();`
>若没有出错将返回空字符串，若出错将返回错误描述字符串，不要依据该方法来判断执行成功还是失败

#### 获取请求失败后的错误号，`curl_errno`的返回值
`$http->getErrno();`
>若没有出错将数字0，若出错将返回不为0的数字，可以依据该方法的返回值`全等于0`判断请求成功，`不全等于0`请求失败

### 获取http请求连接资源句柄的信息数组，`curl_getinfo`的无第二个参数返回值
`$http->getInfo();`
>`curl_getinfo`函数的没有第二个参数的返回值


### 重置http单例
`$http->reset(false);`
>一次http请求完毕需要再次请求之前为了防止两次数据乱入，再下一次请求执行之前调用`reset`方法清理掉上一次设置的参数，可选参数表示是否清理掉setOption方法设置的cUrl核心参数，默认值false表示不清理，需要清理传入true


### sample1、get请求晶晶博客

    uese jjonline\helper\Tools;
    use jjonline\library\Http;
    $http = Http::init();
    // [可选的]设置请求时的header头Referer
    $http->setReferer('http://blog.jjonline.cn');
    // [可选的]设置请求时的header头User-Agent值
    $http->setUserAgent('Mozilla/5.0 (Windows NT 6.1; Win64; x64) Chrome/60.0.3112.90 Safari/537.36');
    // [可选的]设置请求时的cookie
    $http->setRequestCookie('JID','so7i7srvbk4c5dd0748df8va23');
    // setData方法在get请求时无效，若需要为get方法传递get变量，请拼接好变量后通过setUrl方法设置
    // 设置请求晶晶的博客首页的Url
    $http->setUrl('http://blog.jjonline.cn');
    // 执行get请求并判断执行状态
    $isSuccess = $http->get();
    /**
    * $http->setUrl('http://blog.jjonline.cn'); 和 $isSuccess = $http->get();也可以简写成
    * $isSuccess = $http->get('http://blog.jjonline.cn');
    */
    /**
    * 上述的代码也可以这样写：
    * $isSuccess = $http->setReferer('http://blog.jjonline.cn')
    *            ->setUserAgent('Mozilla/5.0 (Windows NT 6.1; Win64; x64) Chrome/60.0.3112.90 Safari/537.36')
    *            ->setRequestCookie('JID','so7i7srvbk4c5dd0748df8va23')
    *            ->setUrl('http://blog.jjonline.cn')
    *            ->get();
    */
    if($isSuccess)
    {
       echo '请求成功，header数据为：';
       Tools::dump($http->getHeader());
       echo 'body数据为：';
       Tools::dump($http->getBody());
    }else {
       echo '请求成功失败，curl_error()返回值为：'.$http->getError().'curl_errno()返回值为：'.$http->getErrno();
    }

### sample2、get请求下载图片

    use jjonline\library\Http;
    $http      = Http::init();
    $isSuccess = $http->get('http://blog.jjonline.cn/Images/mm.jpg');
    $isSuccess && $http->save('./m.jpg');//此时若不出现异常和错误，脚本所在目录会看到下载的这张图片


### sample3、post请求

    use jjonline\library\Http;
    $http      = Http::init();
    // 设置参数和post提交的数据
    $http->setOption(CURLOPT_FILETIME,true)
      ->setReferer('http://blog.jjonline.cn')
      ->setUserAgent('Mozilla/5.0 (Windows NT 6.1; Win64; x64) Chrome/60.0.3112.90 Safari/537.36')
      ->setRequestCookie('JID','so7i7srvbk4c5dd0748df8va23')
      ->setData('postField1','这是post发送的名为postField1的值')
      ->setData('postField2','这是post发送的名为postField2的值')
      ->post('http://blog.jjonline.cn');
    // 接下来的代码省略，当然啦我的博客个人首页对post响应与get无异
    
    //再来一个post请求，请求之前需要reset掉设置的请求参数
    $http->reset()
         ->setData('postData','reset之后原先设置的post请求参数不再生效')
         ->post('http://blog.jjonline.cn');

### sample4、post上传文件

    use jjonline\library\Http;
    $http      = Http::init();
    // 设置过程省略一部分...
    $http->setUploadFile('FileField','../mm.jpg')
         ->post('http://blog.jjonline.cn');
    // 当然，这里post之前依然可以调用setOption、setReferer等之类的方法
    // 这里上传文件后假设被请求的服务器端（也就是接收文件上传方）是PHP开发的
    // 那么可以通过$_FILES['FileField']读取到这个上传的文件
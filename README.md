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

// 通用http操纵类Post方法
$Http            = Http::init();
$excuteStatus    = $Http->setData('postField','postVallue')
                        ->setTimeOut(10)
                        ->setReferer('http://blog.jjonline.cn')
                        ->setUserAgent('Mozilla/5.0')
                        ->setRequestHeader('X-Powered-By','JJonline.Cn')
                        ->setRequestCookie('cookieName','cookieValue')
                        ->post('http://blog.jjonline.cn');
if($excuteStatus)
{
    $Http->save('/var/www/index.html');
    echo $Http->getBody();
}

// 通用http操纵类Get方法
$excuteStatus = $Http->reset()->get('http://blog.jjonline.cn');
if($excuteStatus)
{
    $Http->save('/var/www/index1.html');
    print_r($Http->getResponseCookie());
}
~~~

jjonline/php-helper包含两部分：

* 静态类函数方法：`src/function`目录下
* 对象类：`src/library`目录下

----

### FilterValid 表单验证静态类

引入命名空间：`use jjonline\helper\FilterValid`

+ **`boolean FilterValid::is_mail_valid(string $str)`**

  检测传入的字符串是否符合邮箱格式

+ **`boolean FilterValid::is_phone_valid(mixed $str|number)`**

  检测传入的字符串或数字是否符合天朝手机号格式

+ **`boolean FilterValid::is_url_valid(string $url)`**

  检测传入的字符串是否为http或http打头的网址，可包含端口号、get参数和锚点

+ **`boolean FilterValid::is_uid_valid(mixd $uid[[,$min_len = 4],$max_len = 11])`**

  检测传入的数字是否为一个数字ID，第二个可选参数指定最小长度默认值4；第三个可选参数指定最大长度默认值11

+ **`boolean FilterValid::is_password_valid(string $pwd[,$min_len = 6,$max_len = 18])`**

  检测传入的字符串是否为同时包含数字和字母的合法密码，第二个可选参数指定最小长度默认值4；第三个可选参数指定最大长度默认值18

+ **`boolean FilterValid::is_chinese_valid(string $str)`**

  检测传入的字符串是否全部为中文汉字

+ **`boolean FilterValid::is_utf8_valid(string $str)`**

  检测传入的字符串是否utf8编码

+ **`mixd[false|array] FilterValid::is_citizen_id_valid(string $citizen_id)`**

  检测传入的字符串是否为合乎编码规范的天朝身份证号，兼容16位和18位，合乎规范返回从身份证号提取的出生年月、归属地、性别和身份证号组成的数组，否则返回false；注意传入16位老号码合乎规范的话返回数组中的身份证号将转换为18位！

### Tools 常用工具方法静态类

引入命名空间：`use jjonline\helper\Tools`

+ **`boolean Tools::is_weixin_browser([string $userAgent = null])`**

  检测当前浏览器环境是否为微信浏览器，或者传入浏览器userAgent字符串检测是否为微信浏览器，参数可选

+ **`boolean Tools::is_mobile_browser()`**

  检测当前浏览器环境是否为移动端浏览器，当然微信浏览器也会返回true

+ **`string Tools::hide_name(string $userName)`**

  用户名脱敏处理，字符长度小于3返回3个\*，长度大于3则返回首尾各1个字符以及中间的3个\*号字符

+ **`string Tools::hide_ipv4(string $ip)`**

  IPv4地址脱敏处理，返回IPv4中间两位脱敏后的字符，例如：223.\*\*.\*\*.5

+ **`string Tools::nl2p(string $str)`**

  nl2br的类似函数，只不过是将换行符`\n`、`\r`或`\n\r`转换为成对的`p`标签

+ **`string Tools::time_ago(mixed $unixTimeStamp)`**

  将Unix时间戳转换为`xx前`这种模糊表示法，例如：`刚刚`、`1分钟前`、`2小时前`、`3周前`、`1个月前(07-11)`、`3年前(2014-12-02)` 等

+ **`boolean Tools::is_ssl()`**

  检测当前请求是否为https请求

+ **`mixed Tools::get_client_ip([$isLong = 0],[$isAdv = false])`**

  获取客户端的IPv4地址，第一个可选参数1和true返回IPv4数字表示法，0和false返回IPv4字符串，默认值0；第二个可选参数是否检测http代理情况，false不检测，true检测，默认值false

+ **`void Tools::redirect(string $url[[,int $time = 0],$text = null])`**

  网页重定向，也就是网页跳转啦！第一参数跳转的url，第二个可选参数跳转等待时间，第三个可选参数跳转等待时显示的文本

+ **`string|null Tools::dump(string $str[,$echo=true[, $label=null[, $strict=true]]])`**

  开发时浏览器友好的调试输出任意变量，来源于ThinkPHP，一般调试输出仅需要将调试变量当做第一个参数
    >第二个可选参数为false将返回处理好的字符串，为true直接输出没有返回值，默认值为true;

    >第三个可选参数指定本次调试的标签，也就是在输出或返回值前加上这个标签值，便于开发调试，默认值null；

    >第四个可选参数指定本次输出是否严格模式，也就是在输出或返回值的时候是否将html标签转义，默认值true；


+ **`string Tools::transfer_encrypt(string $str[, $key = 'jjonline'[, $expiry = 0]])`**

  来源于discuz的可逆加解密算法，用于有有效期效验的数据交换，加密强度很弱，请甄别使用场景
    >第二个可选参数：本次加密的秘钥，默认加了一个秘钥`jjonline`

    >第三个可选参数：本次加密的有效时间，单位秒，在该参数指定的时间内解密有效，默认值0表示永不失效

+ **string Tools::transfer_decrypt(string $str[, $key = 'jjonline'])`**

  将transfer_encrypt加密的值解密，并效验有效期，成功返回原串，失败或失效返回空字符串
    >第一个参数：待解密的密文

    >第二个参数：解密的秘钥

    >返回值：已失效或解密失败返回空值，解密成功返回解密后明文
  **特别留意：超过过期时间有秘钥也是能解密出明文的，只不过方法体本身效验是否过期，过期就认为解密失败**

+ **`string Tools::mbsubstr(string $str [,$start = 0[, $length[, $charset = "utf-8"]]])`**

  返回参数指定的参数截取字符串，参数与原生函数`mb_substr`一致，这是一个兼容处理函数

+ **`Tools::get_rand_string([$len = 6[, $type = ''[, $addChars = '']]])`**

  获取随机字符串，来源于ThinkPHP
    >第一个**可选参数**指定返回字符串长度，默认值6

    >第二个**可选参数**指定返回字符串类型，取值空字符串、0字母、1数字、2大写字母、3小写字母、4中文，默认值空表示返回大小写字母和数字混合的随机字符串，0表示返回大小写字母混合的字符串，以此类推

    >第三个**可选参数**指定附加的用于返回值随机字符串的字符元素，函数中的数字、大小写字母去除了易混淆的字母oOLl和数字0和1，若不需要去除通过第三个参数附加进去即可，默认值空字符串

+ **`string  Tools::clear_js_code(string $html)`**

  去除字符串中的js代码，包括大段的由<script>标签包裹的代码和各标签属性中on开头的事件属性

+ **`string to_absolute_url(string $sUrl,string $baseUrl)`**

  将当前网页中的相对超链接转换为带域名的完整超链接

+ **`boolean Tools::rm_dir(string $dir)`**

  删除目录以及目录下的所有文件，原生函数`rmdir`只能删除非空目录

----

### Http 网络请求对象类

引入命名空间：`use jjonline\library\Http`

#### 初始化

+ **`HttpObject Http::init()`**

  初始化Http单例类，返回http单例对象，例如：`$http = Http::init()`

#### 设置方法

+ **`HttpObject $http->setUrl(string $url)`**

  设置请求的网址url

+ **HttpObject `$http->setTimeOut(int $time)`**

  设置请求连接上之后的超时时间，一个int型数字，单位：秒

+ **`HttpObject $http->setRequestHeader(mixed $key[,string $value = null])`**

  设置自定义请求头信息，参数比较灵活
    ~~~
    第一种传参方式：第一个参数为header头的名称部分，第二个参数为header头的值部分
    $http->setRequestHeader('X-Powered-By','PHP/7.0.22')

    第二种传参方式：仅第一个参数二维数组，一次可设置多个header头项目
    $http->setRequestHeader([['X-Powered-By'=>'PHP/7.0.22'],['X-User-By'=>'JJonline']])

    第三种传参方式：仅第一个参数字符串，完整的header头
    $http->setRequestHeader('X-Powered-By: PHP/7.0.22')
    ~~~

+ **`HttpObject $http->setReferer([string $key = null])`**

  设置请求头信息中的Referer来源，必须是一个合法的网址

+ **`HttpObject $http->setUserAgent(string $userAgent)`**

  设置请求头信息中的userAgent浏览器头信息

+ **`HttpObject $http->setData(mixed $key[,string $value = null])`**

  设置请求中post发送的表单数据，参数比较灵活
    ~~~
    第一种传参方式：第一个参数为post数据的名称部分，第二个参数为post数据的值部分
    $http->setData('name','Jea')
    $http->setData('sex','男')

    第二种传参方式：仅第一个参数二维数组，一次可设置多个fieldKey-fieldValue
    $http->setData([['name'=>'Jea'],['sex'=>'男']])

    第三种传参方式：仅第一个参数字符串，完整的url拼接格式，注意传值需要urlencode
    $http->setData('name=Jea&sex=%E7%94%B7')
    ~~~

+ **`HttpObject $http->setRequestCookie(mixed $key[,string $value = null])`**

  设置请求时发送的cookie，参数比较灵活
    ~~~
    第一种传参方式：第一个参数为cookie的名称部分，第二个参数为cookie的值部分
    $http->setRequestCookie('cookieName','cookieValue')

    第二种传参方式：仅第一个参数二维数组，一次可设置多个cookie
    $http->setRequestCookie([['cookieName1'=>'cookieValue1'],['cookieName2'=>'cookieValue2']])

    第三种传参方式：仅第一个参数字符串，符合curl_setopt原生设置cookie键值对的字符串
    $http->setRequestCookie('cookie_a=1; cookie_b=2') //注意分号和其后的空格
    ~~~

+ **`HttpObject $http->setUploadFile(string $field_name,string $file_dir)`**

  置Post方法上传的文件
  第一个参数设置表单名，第二个参数设置拟上传文件路径

+ **`HttpObject $http->setOption(mixed $key[,mixed $value = null])`**

  高阶自定义设置cUrl原生参数，直接操纵`curl_setopt`函数的设置项，参数比较灵活，常量参考：[点此](http://php.net/manual/zh/function.curl-setopt.php)
    ~~~
    第一种传参方式：与curl_setopt(resource $ch , int $option , mixed $value )的第2、3两个参数一致即可
    $http->setRequestCookie(CURLOPT_CRLF,true) //第一个参数为常量
    $http->setRequestCookie('CURLOPT_CRLF',true) //该种方法也是可以的，但不推荐

    第二种传参方式：仅第一个参数二维数组，一次可设置多个
    $http->setRequestCookie([['CURLOPT_CRLF'=>true],[CURLOPT_FILETIME=>true]])
    ~~~


#### 获取设置的值方法

+ **`string $http->getUrl()`**

  获取设置的请求url

+ **`int $http->getTimeOut()`**

  获取设置的超时时间

+ **`mixed $http->getRequestHeader([string $key = null])`**

  获取设置的header，可选参数为header名称部分
    ~~~
    string $http->getRequestHeader('X-Powered-By')
    array  $http->getRequestHeader() //返回所有设置的header数组格式
    ~~~

+ **`string $http->getReferer()`**

  获取设置的Referer

+ **`string $http->getUserAgent()`**

  获取设置的UserAgent

+ **`mixed $http->getData([string $key = null])`**

  获取设置的表单数据
    ~~~
    string $http->getData('fieldName')
    array  $http->getData() //返回所有设置的表单数组
    ~~~

+ **`mixed $http->geetRequestCookie([string $key = null])`**

  获取设置的cookie数据
    ~~~
    string $http->geetRequestCookie('cookieName')
    array  $http->geetRequestCookie() //返回所有设置的cookie
    ~~~

+ **`mixed $http->getOption([string $key = null])`**

    获取设置的curl_setopt高阶参数
    ~~~
    string $http->getOption(CURLOPT_FILETIME)
    array  $http->getOption() //返回所有设置的curl_setopt高阶参数
    ~~~

#### 执行请求和写入数据

+ **`boolean $http->get([string $url = null])`**

  执行get请求，返回布尔值，true执行成功，false执行异常
  >可选参数为快捷设置请求的url，可代替setUrl方法

+ **`boolean $http->post([string $url = null[,array $data = []]])`**

  执行post请求，返回布尔值，true执行成功，false执行异常
  >第一个可选参数为设置请求的url，第二个可选数组参数设置表单数据，格式与setData第二种传参方式一致

+ **`boolean $http->save(string $local_file_dir)`**

  成功执行get或post后，存储请求到的数据，俗称：下载
    ~~~
    参数为保存的路径，包括文件名和后缀，例如
    $ret = $http->get($url);
    $ret && $http->save($dir);
    ~~~

#### 获取执行请求后的数据

+ **`string $http->getResult()`**

  获取请求成功后返回的包含header头的原始数据

+ **`string $http->getHeader()`**

  获取请求成功后返回数据的整个header头字符串

+ **`string $http->getBody()`**

  获取请求成功后返回数据的body主体内容，譬如：请求某个api后返回的json字符串

+ **`array $http->getResponseCookie()`**

  获取请求成功后返回数据中的cookie键值对数组
  ~~~
  该方法有个可选参数，取值true或false
  默认值false表示返回处理好的cookie键值对二维数组
  例如：[['JID'=>'so7i7srvbk4c5dd0748df8va23'],['token'=>'6a4ee8169908dc4ec0700008fe0c1085']]
  传值true表示返回header头中cookie键值对的原始表示法的一维数组，用于进一步处理获取一些信息
  例如：[
        'JID=so7i7srvbk4c5dd0748df8va23; path=/; domain=.jjonline.cn; HttpOnly',
        'token=6a4ee8169908dc4ec0700008fe0c1085; path=/; domain=.jjonline.cn; HttpOnly'
        ]
  ~~~

+ **`string $http->getError()`**

  获取请求失败后的错误描述字符串，`curl_error`的返回值

+ **`int $http->getErrno()`**

  获取请求失败后的错误号，`curl_errno`的返回值，没有出错返回数字0，出错返回不为0的数字

+ **`array $http->getInfo()`**

  获取http请求连接资源句柄的信息数组，`curl_getinfo`无第二个参数的返回值

#### 重置数据单例复用

+ **`HttpObject $http->reset([boolean $isResetOption = false])`**

  一次http请求完毕需要再次请求之前为了防止两次数据乱入，再下一次请求执行之前调用`reset`方法清理掉上一次设置的参数，可选参数表示是否清理掉setOption方法设置的cUrl核心参数，默认值false表示不清理，需要清理传入true，传入true后在再次执行新请求前需重新设置各个参数

#### Http类示例

**sample1、get请求晶晶博客**

~~~
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
~~~

**sample2、get请求下载图片**

~~~
use jjonline\library\Http;
$http      = Http::init();
$isSuccess = $http->get('http://blog.jjonline.cn/Images/mm.jpg');
$isSuccess && $http->save('./m.jpg');//此时若不出现异常和错误，脚本所在目录会看到下载的这张图片
~~~


**sample3、post请求**

~~~
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
~~~

**sample4、post上传文件**

~~~
use jjonline\library\Http;
$http      = Http::init();
// 设置过程省略一部分...
$http->setUploadFile('FileField','../mm.jpg')
     ->post('http://blog.jjonline.cn');
// 当然，这里post之前依然可以调用setOption、setReferer等之类的方法
// 这里上传文件后假设被请求的服务器端（也就是接收文件上传方）是PHP开发的
// 那么可以通过$_FILES['FileField']读取到这个上传的文件
~~~
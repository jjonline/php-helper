<?php
/**
 * Http操纵类：
 *     1、发送get请求
 *     2、发送post请求
 *     3、上传文件
 *     4、下载文件
 * @authors Jea杨 (JJonline@JJonline.Cn)
 * @date    2017-08-04 16:51:07
 * @version $Id$
 */
namespace jjonline\library;
use Exception;

class Http {
    // 请求url
    private $url;
    // 请求超时时间，默认30秒
    private $timeout = 30;
    // curl参数--带一些默认值
    private $option  = array(
        // 是否将http请求的header头作为数据量输出，要手动处理cookie此项会强行重置
        CURLOPT_HEADER         => true,
        // userAgent设置 默认为Chrome60 win7x64版
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.90 Safari/537.36',
        // 网络协议的类型，天朝IPv6还未大范围普及
        // 默认值CURL_IPRESOLVE_WHATEVER时有时解析域名时会优先解析到IPv6导致卡顿
        CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,//CURL_IPRESOLVE_WHATEVER、CURL_IPRESOLVE_V4、CURL_IPRESOLVE_V6
        // 等待超时、连接上之后传输数据的超时时间，单位秒
        CURLOPT_TIMEOUT        => 30,
        // HTTP请求头中Accept-Encoding的值，空值发送所有支持的编码类型
        CURLOPT_ENCODING       => '',
        // 将请求结果以字符串返回而不是直接输出
        CURLOPT_RETURNTRANSFER => true,
        // 默认禁用https连接的ssl证书效验，可通过setOption方法自主定制
        CURLOPT_SSL_VERIFYPEER => false,
        // 连接超时，建立连接的超时时间，单位秒
        CURLOPT_CONNECTTIMEOUT => 10,
    );
    // curl发送的数据
    private $data;
    // curl发送的cookie
    private $requestCookie;
    // curl返回的cookie
    private $responseCookie;
    // curl返回http结果的header头部分
    private $header = '';
    // curl返回http结果的body部分
    private $body   = '';
    // curl执行后可能的错误信息
    private $error;
    // curl执行后可能的错误号
    private $errno;
    // curl_getinfo返回的信息
    private $info;
    // curl返回的带header头的字符串结果
    private $result = '';
    private static $instance;

    private function __construct() {}

    /**
     * 初始化实例对象 单例Instance
     * @return self
     */
    public static function init()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * 设置cUrl自定义配置值，通过该方法直接操纵curl配置项
     * 例如设置`CURLOPT_REFERER`传入本次请求的http请求头中的Referer值
     * @param string|array $key   curl配置项的key或者key-value的数组
     * @param mixed        $value curl配置项的的value
     * @return $this
     */
    public function setOption($key,$value = null)
    {
        if(is_array($key))
        {
            // $this->option = array_merge($this->option,$key);
            // 避免array_merge重新组合数组$this->option中的整数键名
            foreach ($key as $_key => $value) {
                $_key = constant(strtoupper($_key));//兼容以字符串形式传入curl设置参数
                if(is_null($_key))
                {
                    throw new Exception('Constant Name Do Not Exist '.$key, 500);
                }
                $this->option[$_key] = $value;
            }
        }else{
            $this->option[$key] = $value;
        }
        return $this;
    }

    /**
     * 获取配置项的值
     * @param  strign $key 配置项key的名称，默认null返回整个配置项数组
     * @return mixed
     */
    public function getOption($key = null)
    {
        if(!empty($key))
        {
            $_key = constant(strtoupper($key));
            if(is_null($_key))
            {
                throw new Exception('Constant Name Do Not Exist '.$key, 500);
            }
            return isset($this->option[$_key]) ? $this->option[$_key] : null;
        }
        return $this->option;
    }

    /**
     * 设置请求体Referer
     * @param string $referer 设置请求体的Referer字符串|一个有效的网址
     */
    public function setReferer($referer)
    {
        if (filter_var($referer, FILTER_VALIDATE_URL)) {
            $this->option[CURLOPT_REFERER] = $referer;
            return $this;
        }
        throw new Exception('Http Referer is invalid.', 500);
    }

    /**
     * 获取已设置的Referer
     * @return string
     */
    public function getReferer()
    {
        return isset($this->option[CURLOPT_REFERER]) ? $this->option[CURLOPT_REFERER] : '';
    }

    /**
     * 设置请求体header头中User-Agent值
     * 本类默认设置useAgent值为win7x64位下的chrome浏览器60版本的头信息
     * @param string $userAgent 设置的User-Agent值
     */
    public function setUserAgent($userAgent)
    {
        $this->option[CURLOPT_USERAGENT] = $userAgent;
        return $this;
    }

    /**
     * 获取设置的userAgent头信息，未手动设置将获取默认值
     * @return string
     */
    public function getUserAgent()
    {
        return isset($this->option[CURLOPT_USERAGENT]) ? $this->option[CURLOPT_USERAGENT] : '';
    }

    /**
     * 设置curl发送的数据
     * @param string|array $key   curl发送数据的key名称或数组包裹的多个key-value或事先已拼接好的数据字符串
     * @param string       $value 第一个参数为string时的value值
     * @return $this
     */
    public function setData($key,$value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data,$key);
        } else {
            if ($value === null) {
                // urlencoded后的字符串形式，例如a=1&b=2&c=3，也就是符合CURLOPT_POSTFIELDS格式要求的字符串
                parse_str(urldecode($value),$parse_data);
                if(is_array($parse_data) && $parse_data)
                {
                    $this->data = array_merge($this->data,$parse_data);
                }
            } else {
                $this->data[$key] = $value;
            }
        }
        return $this;
    }

    /**
     * 获取已设置的curl拟发送的数据
     * @param  string $key 拟发送数据的key名称，默认null获取所有
     * @return mixed
     */
    public function getData($key = null)
    {
        if(!empty($key))
        {
            return isset($this->data[$key]) ? $this->data[$key] : null;
        }
        return $this->data;
    }

    /**
     * 设置发送的cookie键和名
     * 方法体进行了适当的包装，不涉及到文件IO操作
     * @param string|array $key   cookie的key名 | 包装好的key-value二维数组 | CURLOPT_COOKIE参数指定的字符格式
     * @param string       $value cookie的值或
     * @param $this
     */
    public function setRequestCookie($key,$value = null)
    {
        if (is_array($key)) {
            $this->requestCookie = array_merge($this->requestCookie,$key);
        } else {
            if ($value === null) {
                $cookie_str  = $key;//例如`cookie_a=1; cookie_b=2; cookie_c=3`注意分号和空格
                // parse成统一的数组，最后包装处理
                $cookie_item = explode(';', $cookie_str);
                foreach ($cookie_item as $_key => $_value) {
                    list($kkey,$vval)           = explode('=', trim($_value));
                    $this->requestCookie[$kkey] = $vval;
                }
            } else {
                $this->requestCookie[$key] = $value;
            }
        }
        return $this;
    }

    /**
     * 获取设置的cookie值
     * @param  string $key 设置的cookie中的key值
     * @return mixed
     */
    public function getRequstCookie($key = null)
    {
        if(!empty($key))
        {
            return isset($this->requestCookie[$key]) ? $this->requestCookie[$key] : null;
        }
        return $this->requestCookie;
    }

    /**
     * 获取返回结果值的cookie
     * @param  boolean $isOriginCookie 是否获取键值对成字符串的原始cookie
     * @return []
     */
    public function getResponseCookie($isOriginCookie = false)
    {
        if($this->responseCookie)
        {
            return $isOriginCookie === false ? $this->responseCookie['parsed'] : $this->responseCookie['origin'];
        }
        return [];
    }

    /**
     * 上传文件方法
     * @param string $field_name post该文件的name字段名称
     * @param string $file_dir   拟上传文件的路径
     */
    public function setUploadFile($field_name,$file_dir)
    {
        if(!is_file($file_dir))
        {
            throw new Exception($file_dir.' File Not Exists', 500);
        }
        $path      = realpath($file_dir);
        $finfo     = new \finfo(FILEINFO_MIME_TYPE);//FILEINFO_MIME
        $mime_type = $finfo->file($file_dir);
        $base_name = basename($file_dir);
        if(class_exists('\CURLFile'))
        {
            $this->option[CURLOPT_SAFE_UPLOAD]     = true;
            $this->data[$field_name]               = new \CURLFile($path,$mime_type,$base_name);
        }else {
            //5.3、5.4
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                $this->option[CURLOPT_SAFE_UPLOAD] = false;
            }
            $this->data[$field_name] = '@'.$path.";type=".$mime_type.";filename=".$base_name;
        }
        return $this;
    }

    /**
     * 设置cUrl请求连接的超时时间|默认值30秒
     * @param int $time 设置的超时时间，单位：秒
     */
    public function setTimeOut($time)
    {
        $this->timeout = intval($timeout);
        return $this;
    }

    /**
     * 获取设置的链接超时时间，单位：秒
     * @return int
     */
    public function getTimeOut()
    {
        return $this->timeout;
    }

    /**
     * 设置请求的Url
     * @param string $url 请求url
     */
    public function setUrl($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $this->url = $url;
            return $this;
        }
        throw new Exception('Target URL is invalid.', 500);
    }

    /**
     * 读取设置的Url
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * 返回可能的错误描述信息 等价于 curl_error()返回的结果
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 返回可能的错误号 等价于 curl_errno()返回的结果
     * @return int
     */
    public function getErrno()
    {
        return $this->errno;
    }

    /**
     * 返回curl执行完毕的info信息 等价于 curl_getinfo()返回的结果
     * @return []
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * 返回curl执行完毕后的header信息
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * 返回curl执行完毕后的body信息
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * 返回curl执行完毕后的请求体完整字符串包括header和body|执行出错返回空串
     * @return string
     */
    public function getResult()
    {
        return $this->result === false ? '' : $this->result;
    }

    /**
     * 执行get提交
     * @param  string $url get提交的Url
     * @return boolean
     */
    public function get($url = null)
    {
        if(!empty($url))
        {
            $this->setUrl($url);
        }

        // 防止setOption方法重置了参数
        $this->option[CURLOPT_HEADER]         = true;
        $this->option[CURLOPT_HTTPGET]        = true;
        $this->option[CURLOPT_RETURNTRANSFER] = true;
        // 可能的待处理的cookie
        $this->_handleRequestCookie();

        // 链接超时时间
        $this->option[CURLOPT_TIMEOUT]        = $this->timeout;

        // CURLOPT_URL亦可设置请求Url
        $ch     = curl_init($url);
        // 数组方式设置各参数项
        curl_setopt_array($ch, $this->option);

        // execute
        $this->result = curl_exec($ch);
        $this->errno  = curl_errno($ch);
        $this->error  = $this->errno ? curl_error($ch) : '';
        $this->info   = curl_getinfo($ch);

        curl_close($ch);

        if($this->errno == 0)
        {
            list($this->header, $this->body) = explode("\r\n\r\n", $this->result,2);
            $this->_handeleResponseCookie($this->header);
            return true;// 请求成功 通过getXXX的多个方法获得返回的数据
        }
        return false;// 请求出现错误，返回false
    }

    /**
     * 提交post请求
     * @param  string $url  post提交的url
     * @param  array  $data post提交的数据，可通过setData提前设置
     * @return boolean
     */
    public function post($url,$data = [])
    {
        if(!empty($url))
        {
            $this->setUrl($url);
        }
        if(!empty($data))
        {
            $this->setData($data);
        }

        // 防止setOption方法重置了参数
        $this->option[CURLOPT_HEADER]         = true;
        $this->option[CURLOPT_HTTPGET]        = false;
        $this->option[CURLOPT_POST]           = true;
        $this->option[CURLOPT_RETURNTRANSFER] = true;
        // 可能的待处理的cookie
        $this->_handleRequestCookie();

        // 链接超时时间
        $this->option[CURLOPT_TIMEOUT]        = $this->timeout;

        // CURLOPT_URL亦可设置请求Url
        $ch     = curl_init($url);
        // 数组方式设置各参数项
        curl_setopt_array($ch, $this->option);
        // 设置可能的数据
        curl_setopt($ch,CURLOPT_POSTFIELDS,$this->data);
        // post数据超过1024字节时curl会发送两次请求，第一次会返回100合并至header头
        curl_setopt($ch,CURLOPT_HTTPHEADER,['Expect:']);

        // execute
        $this->result = curl_exec($ch);
        $this->errno  = curl_errno($ch);
        $this->error  = $this->errno ? curl_error($ch) : '';
        $this->info   = curl_getinfo($ch);

        if($this->errno == 0)
        {
            list($this->header, $this->body) = explode("\r\n\r\n", $this->result,2);
            $this->_handeleResponseCookie($this->header);
            return true;
        }
        return false;//请求出现错误，返回false
    }

    /**
     * 将返回的body保存至脚本所在服务器文件系统
     * @param  string $local_dir 本地存储的路径，完整的待文件名和文件后缀的相对或绝对路径
     * @return boolean
     */
    public function save($local_dir)
    {
        if ($this->error) {
            return false;
        }
        $fp = @fopen($local_dir, 'w');
        if ($fp === false) {
            return false;
        }
        fwrite($fp, $this->body);
        fclose($fp);
        // fopen -- fwrite -- fclose方法可能出现E_WARNING错误
        // 自定义set_error_handler可捕获处理，此处暂不使用最简单的file_put_contents方法
        // file_put_contents($local_dir, $this->body);
        return true;
    }

    /**
     * 处理发送的cookie
     * @return void
     */
    private function _handleRequestCookie()
    {
        if($this->requestCookie)
        {
            $cookie = [];
            foreach ($this->requestCookie as $key => $value) {
                $cookie[] = $key.'='.urlencode($value);
            }
            $cookie                       = implode('; ', $cookie);
            $this->option[CURLOPT_COOKIE] = $cookie;
        }
    }

    /**
     * 处理返回结果http的header头中的cookie
     * 将解析出来的返回的cookie成键值对$responseCookie中
     * @param  string $header http返回结果集的header头
     * @return void
     */
    private function _handeleResponseCookie($header = '')
    {
        preg_match_all("/set\-cookie:([^\r\n]*)/i", $header, $matches);
        if(isset($matches[1]))
        {
            $cookie  = [];
            $_cookie = [];
            foreach ($matches[1] as $key => $value) {
                $cookie[]            = urldecode(trim($value));
                $parsed              = [];
                $parsed              = explode(';', trim($value),2);
                $parsed              = explode('=', urldecode($parsed[0]));
                // 解析成key-value键值对数组
                $_cookie[$parsed[0]] = $parsed[1];
            }
            $this->responseCookie['origin'] = $cookie;
            $this->responseCookie['parsed'] = $_cookie;
        }
    }
}

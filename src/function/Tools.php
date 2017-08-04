<?php
/**
 * 常用PHP函数方法汇总
 * FileName Tools.php
 * @authors Jea杨 (JJonline@JJonline.Cn)
 * @date    2017-08-03 15:07:09
 * @version $Id$
 */

namespace jjonline\helper;

class Tools
{

    /**
     * 
     * 判断是否微信浏览器内打开
     * @param  string  $userAget 可选参数 用户浏览器useAgent头
     * @return boolean
     */
    public static function is_weixin_browser($userAget = '')
    {
        if(!$userAget)
        {
            $userAget = $_SERVER['HTTP_USER_AGENT'];
        }
        if ( strpos($userAget, 'MicroMessenger') !== false )
        {
            return true;
        }
        return false;
    }

    /**
     * nl2br的类似函数，将(多个)换行替换成p标签
     * @param  string $str
     * @return string
     */
    public static function nl2p($str)
    {
        $str = str_replace(array('<p>', '</p>', '<br>', '<br/>', '<br />'), '', $str);
        return '<p>'.preg_replace("/([\n|\r\n|\r]{1,})/i", "</p>\n<p>", trim($str)).'</p>';
    }

    /**
     * 将一个Unix时间戳转换成“xx前”模糊时间表达方式
     * @param  mixed $timestamp Unix时间戳
     * @return boolean
     */
    public static function time_ago($timestamp)
    {
        $etime = time() - $timestamp;
        if ($etime < 1) return '刚刚';
            $interval = array (
            12 * 30 * 24 * 60 * 60  =>  '年前 ('.date('Y-m-d', $timestamp).')',
            30 * 24 * 60 * 60       =>  '个月前 ('.date('m-d', $timestamp).')',
            7 * 24 * 60 * 60        =>  '周前 ('.date('m-d', $timestamp).')',
            24 * 60 * 60            =>  '天前',
            60 * 60                 =>  '小时前',
            60                      =>  '分钟前',
            1                       =>  '秒前'
        );
        foreach ($interval as $secs => $str) {
            $d = $etime / $secs;
            if ($d >= 1) {
                $r = round($d);
                return $r . $str;
            }
        }
    }

    /**
     * 判断是否SSL协议
     * @return boolean
     */
    public static function is_ssl()
    {
        if(isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS'])))
        {
            return true;
        }elseif(isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'] ))
        {
            return true;
        }
        return false;
    }

    /**
     * 获取客户端IP地址
     * @param  mixed $type 返回类型 0|false 返回IP地址 1|true 返回IPV4地址数字
     * @param  boolean $adv 是否进行高级模式获取（有可能被伪装）---代理情况 
     * @return mixed
     */
    public static function get_client_ip($type = 0,$adv=false)
    {
        $type       =  $type ? 1 : 0;
        static $ip  =   NULL;
        if ($ip !== NULL) return $ip[$type];
        if($adv){
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos    =   array_search('unknown',$arr);
                if(false !== $pos) unset($arr[$pos]);
                $ip     =   trim($arr[0]);
            }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip     =   $_SERVER['HTTP_CLIENT_IP'];
            }elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip     =   $_SERVER['REMOTE_ADDR'];
            }
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u",ip2long($ip));
        $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }

    /**
     * URL重定向 重定向后调用该函数的脚本将终止运行
     * @param string  $url 重定向的URL地址
     * @param integer $time 重定向的等待时间（秒）
     * @param string  $msg 重定向前的提示信息
     * @return void
     */
    public static function redirect($url, $time=0, $msg='')
    {
        //多行URL地址支持
        $url        = str_replace(array("\n", "\r"), '', $url);
        if (empty($msg))
            $msg    = "系统将在{$time}秒之后自动跳转到{$url}！";
        if (!headers_sent()) {
            // redirect
            if (0 === $time) {
                header('Location: ' . $url);
            } else {
                header("refresh:{$time};url={$url}");
                echo($msg);
            }
            exit();
        } else {
            $str      = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
            if ($time != 0)
                $str .= $msg;
            exit($str);
        }
    }

    /**
     * 用于调试:浏览器友好的变量输出
     * @param mixed   $var 变量
     * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
     * @param string  $label 标签 默认为空
     * @param boolean $strict 是否严谨 默认为true
     * @return void|string
     */
    public static function dump($var, $echo=true, $label=null, $strict=true)
    {
        $label = ($label === null) ? '' : rtrim($label) . ' ';
        if (!$strict) {
            if (ini_get('html_errors')) {
                $output = print_r($var, true);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            } else {
                $output = $label . print_r($var, true);
            }
        } else {
            ob_start();
            var_dump($var);
            $output = ob_get_clean();
            if (!extension_loaded('xdebug')) {
                $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            }
        }
        if ($echo) {
            echo($output);
            return null;
        }else
            return $output;
    }

    /**
    * 可逆的字符串加密和解密方法-discuz中的方法
    * 该函数密文的安全性主要在于密匙并且是可逆的
    * 
    * 该可逆加密主要用于一些需要时间有效性效验的数据交换中，加密强度很弱
    * 若用于密码处理建议使用password_hash和password_verfiy
    *
    *                       ###警告###
    * ********过期时间参数并不意味着过期后就无法解密出明文了********
    * 
    * @param  string  $string    明文或密文
    * @param  boolean $isEncode  是否解密，true则为解密 false默认表示加密字符串
    * @param  string  $key       密钥 默认jjonline
    * @param  int     $expiry    密钥有效期 单位：秒 默认0为永不过期
    * @return string 空字符串表示解密失败|密文已过期 
    */ 
    public static function reversible_crypt($string, $isEncode = false, $key = 'jjonline', $expiry = 0)
    {
        $ckey_length            =   4;
        // 密匙
        $key                    =   md5($key ? $key : 'jjonline'); 
        // 密匙a会参与加解密
        $keya                   =   md5(substr($key, 0, 16));
        // 密匙b会用来做数据完整性验证
        $keyb                   =   md5(substr($key, 16, 16));
        // 密匙c用于变化生成的密文
        $keyc                   =   $ckey_length ? ($isEncode ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
        // 参与运算的密匙
        $cryptkey               =   $keya.md5($keya.$keyc);
        $key_length             =   strlen($cryptkey);
        // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性
        // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
        $string                 =   $isEncode ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length          =   strlen($string);
        $result                 =   '';
        $box                    =   range(0, 255);
        $rndkey                 =   array();
        // 产生密匙簿
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i]         =   ord($cryptkey[$i % $key_length]);
        }
        // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上并不会增加密文的强度
        for($j = $i = 0; $i < 256; $i++) {
            $j                  =   ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp                =   $box[$i];
            $box[$i]            =   $box[$j];
            $box[$j]            =   $tmp;
        }
        // 核心加解密部分
        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a                  =   ($a + 1) % 256;
            $j                  =   ($j + $box[$a]) % 256;
            $tmp                =   $box[$a];
            $box[$a]            =   $box[$j];
            $box[$j]            =   $tmp;
            // 从密匙簿得出密匙进行异或，再转成字符
            $result            .=   chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if($isEncode) {
            // substr($result, 0, 10) == 0 验证数据有效性
            // substr($result, 0, 10) - time() > 0 验证数据有效性
            // substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性
            // 验证数据有效性，请看未加密明文的格式
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            // 把动态密匙保存在密文里，这也是为什么同样的明文，生成不同密文后能解密的原因
            // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
            return $keyc.str_replace('=', '', base64_encode($result));
        }
    }

    /**
     * 对时间有效性的数据进行可逆的加密，对reversible_crypt方法的可识别封装
     * @param  string  $string 待加密字符串
     * @param  string  $key    加密秘钥
     * @param  integer $expiry 加密的密文失效时间，0默认表示：永不失效
     * @return string
     */
    public static function transfer_encrypt($string, $key = 'jjonline', $expiry = 0)
    {
        return self::reversible_crypt($string, false, $key, $expiry);
    }

    /**
     * 对时间有效性的数据进行效验并解密
     * 由reversible_encrypt加密的密文进行解密
     * 
     *                       ###警告###
     * ********过期时间参数并不意味着过期后就无法解密出明文了********
     * 
     * 密文过期并不意味着无法解密出明文，只是在密文中加入了一种过期效验机制由方法体自动完成效验罢了
     * 
     * @param  string $string 密文字符串
     * @param  string $key    解密秘钥
     * @return string
     */
    public static function transfer_decrypt($string, $key = 'jjonline')
    {
        return self::reversible_crypt($string, true, $key);
    }

    /**
     * 字符串截取，支持中文和其他编码
     * @param string $str 需要转换的字符串
     * @param string $start 开始位置
     * @param string $length 截取长度
     * @param string $charset 编码格式
     * @return string
     */
    public static function mbsubstr($str, $start = 0, $length, $charset = "utf-8")
    {
        if(function_exists("mb_substr"))
            $slice = mb_substr($str, $start, $length, $charset);
        elseif(function_exists('iconv_substr')) {
            $slice = iconv_substr($str,$start,$length,$charset);
        }else{
            $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312']  = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk']     = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5']    = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("",array_slice($match[0], $start, $length));
        }
        return  $slice;
    }
}

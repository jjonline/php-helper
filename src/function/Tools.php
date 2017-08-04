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
}

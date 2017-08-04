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
    function nl2p($str)
    {
        $str = str_replace(array('<p>', '</p>', '<br>', '<br/>', '<br />'), '', $str);
        return '<p>'.preg_replace("/([\n|\r\n|\r]{1,})/i", "</p>\n<p>", trim($str)).'</p>';
    }

    /**
     * 将一个Unix时间戳转换成“xx前”模糊时间表达方式
     * @param  mixed $timestamp Unix时间戳
     * @return boolean
     */
    function time_ago($timestamp)
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
}

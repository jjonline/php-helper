<?php
/**
 * 常用表单过滤和检测函数
 * FileName FilterValid.php
 * @authors Jea杨 (JJonline@JJonline.Cn)
 * @date    2017-08-03 17:32:38
 * @version $Id$
 */

namespace jjonline\helper;

class FilterValid {
    
    /**
     * 检测传入的变量是否为合法邮箱 提供两种方法 可选内置fliter函数 
     * 默认正则[邮箱用户名(即@符号之前的部分)构成部分为数字、字母、下划线、中划线和点均可，且开头必须是数字或字母]
     * @param  string $mail
     * @return boolean
     */
    public static function is_mail_valid($mail)
    {
        # PHP内置filter_var方式较为宽泛 不予采用
        /* !"#$%&'*+-/0123456789=?@ABCDEFGHIJKLMNOPQRSTUVWXYZ^_ `abcdefghijklmnopqrstuvwxyz{|}~ 的类型均正确
         也就是说 这种格式的邮箱 JJon#?`!#$%&'*+-/line@JJonline.Cn 也会被filter_var认为是合法邮箱 不符合人类认知 暂不采用
         详见：http://www.cs.tut.fi/~jkorpela/rfc/822addr.html
        */
        #return !!filter_var($mail,FILTER_VALIDATE_EMAIL);
        #正则方式 '/^\w+(?:[-+.]\w+)*@\w+(?:[-.]\w+)*\.\w+(?:[-.]\w+)*$/' 邮箱域名顶级后缀至少两个字符
        return preg_match('/^\w+(?:[-+.]\w+)*@\w+(?:[-.]\w+)*\.\w{2,}$/',$mail)===1;
    }

    /**
     * 检测传入的变量是否为天朝手机号
     * @param  mixed $phone
     * @return boolean
     */
    public static function is_phone_valid($phone)
    {
        #Fixed 171 170x
        #详见：http://digi.163.com/15/0812/16/B0R42LSH00162OUT.html
        return preg_match('/^13[\d]{9}$|14^[0-9]\d{8}|^15[0-9]\d{8}$|^18[0-9]\d{8}$|^170[015789]\d{7}|^171[89]\d{7}|^17[678]\d{8}$/',$phone)===1;
    }

    /**
     * 检测Url是否为合法的http或https链接
     * --------
     * 1、仅检测http、https打头的网址字符串
     * 2、网址中可带端口号
     * 3、网址中可带get变量
     * 4、网址中可带锚点
     * --------
     * @param  mixed $url
     * @return boolean
     */
    public static function is_url_valid($url)
    {
        return preg_match('/^http[s]?:\/\/(?:(?:[0-9]{1,3}\.){3}[0-9]{1,3}|(?:[0-9a-z_!~*\'()-]+\.)*(?:[0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\.[a-z]{2,6})(?::[0-9]{1,4})?(?:(?:\/\?)|(?:\/[0-9a-zA-Z_!~\*\'\(?:\)\.;\?:@&=\+\$,%#-\/]*)?)$/i',$url)===1;
    }

    /**
     * 检测传入的变量是否为一个合法的账户id
     * ---------
     * 提供两种方法:函数方法和正则方法
     * 默认数字ID长度为4至11位
     * ---------
     * @param  mixed $uid       待检测的数字ID
     * @param  int   $minLength 允许的uid最短位数 默认4
     * @param  int   $maxLength 允许的uid最长位数 默认11
     * @return boolean
     */
    public static function is_uid_valid($uid, $minLength = 4, $maxLength = 11)
    {
        #正则方式
        return preg_match('/^[1-9]\d{'.( $minLength - 1 ).','.( $maxLengt - 1 ).'}$/',$uid)===1;
        #函数方式 可能未编译ctype扩展不存在ctype_digit内置函数
        return strlen($uid)>=$minLength && strlen($uid)<=$maxLength && ctype_digit((string)$uid);
    }

    /**
     * 检测传入的变量是否为一个合法的账户密码
     * ---------
     * 1、必须同时包含数字和字母
     * 2、通过第二个参数指定最小长度，默认值6
     * 3、通过第三个可选参数指定最大长度，默认值18
     * ---------
     * @param  string $password 需要被判断的字符串
     * @param  int $minLength 允许的账户密码最短位数 默认6
     * @param  int $maxLength 允许的账户密码最长位数 默认16
     * @return boolean
     */
    public static function is_password_valid($password, $minLength = 6, $maxLength = 18)
    {
        if(strlen($password) > $maxLength || strlen($password) < $minLength) {
            return false;
        }
        return preg_match('/\d{1,'.$maxLength.'}/',$password)===1 && preg_match('/[a-zA-Z]{1,'.$maxLength.'}/',$password)===1;
    }
}
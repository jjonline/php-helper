<?php
/**
 * 常用表单过滤和检测函数
 * FileName FilterValid.php
 * @authors Jea杨 (JJonline@JJonline.Cn)
 * @date    2017-08-03 17:32:38
 * @version $Id$
 */
namespace {
    
    /**
     * 检测传入的变量是否为合法邮箱 提供两种方法 可选内置fliter函数 
     * 默认正则[邮箱用户名(即@符号之前的部分)构成部分为数字、字母、下划线、中划线和点均可，且开头必须是数字或字母]
     * @param  string $mail
     * @return boolean
     */
    function is_mail_valid($mail) {
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
    function is_phone_valid($phone) {
        #Fixed 171 170x
        #详见：http://digi.163.com/15/0812/16/B0R42LSH00162OUT.html
        return preg_match('/^13[\d]{9}$|14^[0-9]\d{8}|^15[0-9]\d{8}$|^18[0-9]\d{8}$|^170[015789]\d{7}|^171[89]\d{7}|^17[678]\d{8}$/',$phone)===1;
    }
}
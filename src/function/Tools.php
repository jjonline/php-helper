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
     * 判断是否移动端浏览器
     * @return boolean
     */
    public static function is_mobile_browser()
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if(isset ($_SERVER['HTTP_X_WAP_PROFILE']))
        {
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if(isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap"))
        {
            return  true;
        }
        // userAgent匹配
        if (isset ($_SERVER['HTTP_USER_AGENT']))
        {
            $clientkeywords = array(
                'nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
            );
            if(preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
            {
                return true;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        if(isset ($_SERVER['HTTP_ACCEPT']))
        {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
            {
                return true;
            }
        }
        return false;
    }

    /**
     * 用户名加星隐藏核心信息
     * @param  string $nickname 用户名、昵称
     * @return string 隐藏处理后的用户名、昵称
     */
    public static function hide_name($nickname)
    {
        if(mb_strlen($nickname) <=3)
        {
            return '***';
        }
        $begin = self::mbsubstr($nickname,0,1,'utf8');
        $end   = self::mbsubstr($nickname,-1,1,'utf8');
        return $begin.'***'.$end;
    }

    /**
     * 隐藏ip v4地址的中间两位
     * @param  string $ip_v4 ipV4的地址
     * @return string 处理隐藏后的地址
     */
    public static function hide_ipv4($ip_v4)
    {
        $ip = explode('.', $ip_v4);
        if(count($ip) == 4)
        {
            $ip[1] = '**';
            $ip[2] = '**';
            return implode('.', $ip);
        }
        return $ip_v4;
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
     * @param  string $str 需要转换的字符串
     * @param  int    $start 开始位置
     * @param  int    $length 截取长度
     * @param  string $charset 编码格式，默认utf8
     * @return string
     */
    public static function mbsubstr($str, $start = 0, $length, $charset = "utf-8")
    {
        if(function_exists("mb_substr"))
        {
            $slice         = mb_substr($str, $start, $length, $charset);
        }elseif(function_exists('iconv_substr')) {
            $slice         = iconv_substr($str,$start,$length,$charset);
        }else{
            $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312']  = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk']     = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5']    = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice         = join("",array_slice($match[0], $start, $length));
        }
        return $slice;
    }

    /**
     * 产生随机字串
     * 默认长度6位 字母和数字混合 支持中文
     * @param string $len 长度
     * @param string $type 字串类型
     * 0 字母 1 数字 2大写字母 3小写字母 4中文
     * 默认：大小写字母和数字混合并且去除了容易混淆的字母oOLl和数字01
     * @param string $addChars 额外添加进去的字符
     * @return string
     */
    public static function get_rand_string($len = 6, $type = '', $addChars = '')
    {
        $str ='';
        switch($type) {
            case 0:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars;
                break;
            case 1:
                $chars = str_repeat('0123456789',3);
                break;
            case 2:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars;
                break;
            case 3:
                $chars = 'abcdefghijklmnopqrstuvwxyz'.$addChars;
                break;
            case 4:
                $chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借".$addChars;
                break;
            default :
                // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
                $chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars;
                break;
        }
        if($len>10 ) {//位数过长重复字符串一定次数
            $chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5);
        }
        if($type!=4) {
            $chars   =   str_shuffle($chars);
            $str     =   substr($chars,0,$len);
        }else{
            // 中文随机字
            for($i=0;$i<$len;$i++)
            {
              $str.= self::mbsubstr($chars, floor(mt_rand(0,mb_strlen($chars,'utf-8')-1)),1,'utf-8');
            }
        }
        return $str;
    }

    /**
     * 清理html内容中的js代码和各种标签内包裹的onXX事件--待完善
     * 直接清理掉所有标签内属性即可
     * {
     *     1、清理所有js代码
     *     2、清理所有标签内属性性质的js事件
     * }
     * @param  string $content 待清理的html文本
     * @return string 清理妥善的html文本
     */
    // public static function clear_jscode($content)
    // {
    //     ##去除所有JavaScript代码
    //     $content = preg_replace('/<script(.*?)<\/?script>/is', "", $content);

    //     ##去除所有a标签
    //     // $content = preg_replace('/<\/?a[^>]>/', '', $content); ##a标签相对危害小一些，依据实际情况取消注释

    //     ## 去除标签内的各种属性，保留img标签的src、alt和title属性，保留a标签的href属性
    //     return preg_replace('/<(?!a\s+|img\s+)(\w+)\s+[^>]+>/', '<${1}>', $content);
    // }

    /**
     * 将相对url转换为绝对完整Url
     * <code>
     *     将某一个Url（当前Url）页面中的超链接不同的写法转换为实际完整的Url
     *     
     *     例如1、当前Url为：
     *         http://blog.jjonline.cn/phptech/172.html，该页面中超链接Url为：/view/173.html
     *         则该超链接Url的实际完整Url为：http://blog.jjonline.cn/view/173.html
     *     例如2、当前Url为：
     *         http://blog.jjonline.cn/phptech/172.html，待转换Url为：./173.html 或 173.html
     *         则待转换Url的实际完整Url为：http://blog.jjonline.cn/phptech/173.html
     *     例如3、当前Url为：
     *         http://blog.jjonline.cn/phptech/172.html，待转换Url为：../view/173.html
     *         则待转换Url的实际完整Url为：http://blog.jjonline.cn/view/173.html
     *     例如4、当前Url为：
     *         http://blog.jjonline.cn/phptech/view/172.html，待转换Url为：./../../173.html
     *         则待转换Url的实际完整Url为：http://blog.jjonline.cn/173.html
     *         
     *     当然第3种和第4种比较变态，但这种Url也是可能存在的
     * </code>
     * @param  string $sUrl    页面中的Url，例如：./../../171.html
     * @param  string $baseUrl 该页面的Url，例如：http://blog.jjonline.cn/sort/php/area/article/173.html
     * @return string
     */
    public static function to_absolute_url($sUrl,$baseUrl) 
    {
        $srcinfo = parse_url($sUrl);
        if(isset($srcinfo['scheme'])) {
            ##完整的Url无需转换
            return $sUrl;
        }

        $baseinfo = parse_url($baseUrl);
        $url      = $baseinfo['scheme'].'://'.$baseinfo['host'];##识别出基础的根Url

        ##识别出待转换Url中的路径部分
        if(substr($srcinfo['path'], 0, 1) == '/') {
            $path   = $srcinfo['path'];
        }else{
            $path   = dirname($baseinfo['path']).'/'.$srcinfo['path'];
        }
        $rst        = array();##保存待转换Url中的路径部分，索引数组，一个元素是一个文件夹名或.和.. 下方对.和..进行替换
        $path_array = explode('/', $path);
        if(!$path_array[0]) {
            $rst[]  = '';
        }

        foreach ($path_array as $key => $dir) {
            if ($dir == '..')
            {
                if (end($rst) == '..')
                {
                    $rst[] = '..';
                }elseif(!array_pop($rst)) {
                    $rst[] = '..';
                }
            }elseif($dir && $dir != '.') {
                $rst[]     = $dir;
            }
        }

        if(!end($path_array)) {
            $rst[] = '';
        }
        $url .= implode('/', $rst);
        return str_replace('\\', '/', $url);
    }

    /**
     * 递归删除非空文件夹
     * @param string $delete_dir 拟删除的文件夹路径---相对入口php文件的相对路径或绝对路径
     */
    public static function rm_dir($delete_dir)
    {
        ## 防止空串参数删根目录
        if(empty($delete_dir))
        {
            return false;
        }
        $delete_dir     = rtrim($delete_dir,'/').'/';
        try {
            $iterator   = new \DirectoryIterator($delete_dir);//路径或文件不存在会抛出UnexpectedValueException异常
            while($iterator->valid()) {
                if($iterator->isFile()){
                    unlink($delete_dir.$iterator->getFilename());##删除文件
                }elseif($iterator->isDir() && !$iterator->isDot()) {
                    self::rm_dir($delete_dir.$iterator->getFilename());##递归处理删除子文件夹下的文件
                }
                $iterator->next();
            }
            rmdir($delete_dir);##删除参数文件夹
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: 15213
 * Date: 2020/8/5
 * Time: 16:46
 */

namespace Qttyeah;


class Sfunc
{

    /**
     * 获取客户端IP
     * @return string
     */
    static function getIP()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ip = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ip = getenv('HTTP_FORWARDED_FOR');

        } elseif (getenv('HTTP_FORWARDED')) {
            $ip = getenv('HTTP_FORWARDED');
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * 随机字符串
     * @param int $length
     * @return string
     */
    static function nonceStr($length = 16)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 时间随机字符串
     * @param string $prefix
     * @return string
     */
    static function randomNumber($prefix = '')
    {
        $number = $prefix . date('ymdHis') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        return $number;
    }

    /**
     * 微信签名
     * @param $params
     * @param $KEY
     * @return string
     */
    static function WeChatSign($params, $KEY)
    {
        ksort($params);
        $buff1 = '';
        foreach ($params as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $buff1 .= $k . "=" . $v . "&";
            }
        }
        $buff1 = trim($buff1, "&");
        //签名步骤二：在string后加入KEY
        $string = $buff1 . "&key=" . $KEY;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);

        return $result;
    }

    /**
     * 数组转xml
     * @param $arr
     * @return string
     */
    static function arrayToXml(array $arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= sprintf("<%s>%s</%s>", $key, $val, $key);
            } else {
                $xml .= sprintf("<%s><![CDATA[%s]]></%s>", $key, $val, $key);
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * xml转数组
     * @param $xml
     * @return bool|mixed
     */
    static function xmlToarray($xml)
    {
        if (!$xml) return false;
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $data;
    }

    /**
     * 对象转数组
     * @param $obj
     * @return array
     */
    static function objectToArray($obj)
    {
        if (is_object($obj)) {
            $obj = (array)$obj;
        }
        if (is_array($obj)) {
            foreach ($obj as $key => $value) {
                $obj[$key] = object_array($value);
            }
        }
        return $obj;
    }
	
	/**
     * 字符加密，不可逆
     * @param $str
     * @param string $name
     * @param string $prfix
     * @param string $suffix
     * @param int $len
     * @return string
     */
    static function pwd($str, $name = 'qttyeah', $prfix = 'qtt', $suffix = 'yeah', $len = 32)
    {
        $str = md5($name . $prfix . $str . $suffix);
        return $len > 32 ? $str : substr(md5($str), 32 - abs($len));
    }

    /**
     * 字符串加密
     * @param $txt
     * @param string $key
     * @return string
     */
    static function lockString($txt, $key = 'qttyeah')
    {
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-=+";
        $nh = rand(0, 64);
        $ch = $chars[$nh];
        $mdKey = md5($key . $ch);
        $mdKey = substr($mdKey, $nh % 8, $nh % 8 + 7);
        $txt = base64_encode($txt);
        $tmp = '';
        $i = 0;
        $j = 0;
        $k = 0;
        for ($i = 0; $i < strlen($txt); $i++) {
            $k = $k == strlen($mdKey) ? 0 : $k;
            $j = ($nh + strpos($chars, $txt[$i]) + ord($mdKey[$k++])) % 64;
            $tmp .= $chars[$j];
        }
        return urlencode($ch . $tmp);
    }

    /**
     * 字符串对应key解密
     * @param $txt
     * @param string $key
     * @return string
     */
    static function unlockString($txt, $key = 'qttyeah')
    {
        $txt = urldecode($txt);
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-=+";
        $ch = $txt[0];
        $nh = strpos($chars, $ch);
        $mdKey = md5($key . $ch);
        $mdKey = substr($mdKey, $nh % 8, $nh % 8 + 7);
        $txt = substr($txt, 1);
        $tmp = '';
        $i = 0;
        $j = 0;
        $k = 0;
        for ($i = 0; $i < strlen($txt); $i++) {
            $k = $k == strlen($mdKey) ? 0 : $k;
            $j = strpos($chars, $txt[$i]) - $nh - ord($mdKey[$k++]);
            while ($j < 0) $j += 64;
            $tmp .= $chars[$j];
        }
        return base64_decode($tmp);
    }

    /**
     * 未定义函数
     * @param $name
     * @param $arguments
     * @return bool
     */
    static function __callStatic($name, $arguments)
    {
        // TODO: Implement __callStatic() method.
        return FALSE;
    }


}
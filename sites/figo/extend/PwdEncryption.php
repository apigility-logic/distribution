<?php

class PwdEncryption
{

    /**
     * 密码明文
     * 
     * @var string
     */
    private static $_pwd;

    /**
     * 密码密文
     * 
     * @var string
     */
    private static $_encryptPwd;

    /**
     * 密码盐值
     * 
     * @var string
     */
    private static $_salt;

    /**
     * 盐值的前缀
     * 
     * @var string
     */
    private static $_prefix = '';

    /**
     * 盐值的煽
     * 
     * @var bool
     */
    private static $_entropy = false;

    /**
     * 设置盐值的前缀
     * 
     * @param string $prefix        	
     */
    public static function setSaltPrefix($prefix)
    {
        self::$_prefix = (string) $prefix;
    }

    /**
     * 设置盐值是否使用煽
     * 
     * @param bool $entropy        	
     */
    public static function setSaltEntropy($entropy)
    {
        self::$_entropy = (boolean) $entropy;
    }

    /**
     * 生成盐值
     */
    protected static function genSalt()
    {
        self::$_salt = uniqid(self::$_prefix, self::$_entropy);
    }

    /**
     * 设置密码
     * 
     * @param $password 密码明文        	
     */
    protected static function setPwd($password)
    {
        self::$_pwd = (string) $password;
    }

    /**
     * 字符串倒转
     * 
     * @param $str 字符串        	
     * @return unknown
     */
    protected static function reverseStr($str)
    {
        $tmp = '';
        for ($len = strlen($str), $i = $len - 1; $i >= 0; $i --) {
            $tmp .= $str [$i];
        }
        return $tmp;
    }

    /**
     * 梅花间竹混合字符串
     * 
     * @param
     *        	$str1
     * @param
     *        	$str2
     */
    protected static function mixStr($str1, $str2)
    {
        $len1 = strlen($str1);
        $len2 = strlen($str2);
        $len = min($len1, $len2);
        $long = $len1 > $len2 ? $str1 : $str2;
        $tmp = '';
        $index = 0;
        while ($index < $len) {
            $tmp .= $str1 [$index] . $str2 [$index];
            $index ++;
        }
        $tmp .= substr($long, $index);
        return $tmp;
    }

    /**
     * 生成密码密文并返回盐值和密文
     * 
     * @param $password 密码        	
     */
    public static function genEncryptPwd($password)
    {
        self::genSalt();
        self::setPwd($password);
        self::$_encryptPwd = self::getEncryptPwd(self::$_pwd, self::$_salt);
        return array(
            self::$_encryptPwd,
            self::$_salt
        );
    }

    /**
     * 根据密码明文和盐值，获取加密后的密码
     * 
     * @param $password 密码明文        	
     * @param $salt 密码盐值        	
     */
    public static function getEncryptPwd($password, $salt)
    {
        return md5(substr(self::mixStr(md5($password), self::reverseStr($salt)), 0, 32));
    }

}

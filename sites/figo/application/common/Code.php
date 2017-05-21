<?php

/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 16/3/22
 * Time: 上午11:42
 */
namespace app\common;

class Code {
    // 结果
    const ERROR = - 1; // 系统错误
    const SUCCESS = 0; // 处理成功
    const SCRIPT_ERROR = 10000; // 语法错误

    // 请求参数
    const PARAM_MISSING = 10001; // 缺少请求参数
    const PARAM_ERROR = 10002; // 请求参数值不符合要求
    
    // 系统错误
    const RECORD_EXIST = 10101; //数据已存在
    const RECORD_NOT_EXIST = 10102; // 数据不存在
    const INSERT_ERROR = 10103; // 数据库插入失败
    const UPDATE_ERROR = 10104; //数据库更新记录失败
    const DELETE_ERROR = 10105; //数据库删除记录失败
    const DB_TIMEOUT = 10106; //数据库操作超时
    
    //APP
    const APP_NOT_EXIST = 10201; // APPID错误
    const APP_SIGN_ERROR = 10202; // 签名错误
    const APP_TOKEN_ERROR = 10203; // token错误
    const APP_TOKEN_EMPTY = 10204; // token不能为空
    const APP_TOKEN_TIMEOUT = 10205; // token过期
    const APP_STATUS_ERROR = 10206; //  APPID状态不正常
    const APP_REQUEST_REPEAT = 10207; // 请求重复
    const APP_REQUEST_TIMEOUT = 10208; // 请求超时
    const APP_AUTHORITY_LIMIT = 10209;  //权限限制
    
    // 会员
    const USER_EXIST = 10301; // 用户名已存在
    const USER_MOBILE_EXIST = 10313;
    const USER_NOT_EXIST = 10302; // 该用户不存在
    const USER_STATUS_FROZEN = 10303; // 该用户已冻结
    const USER_CELL_ERROR = 10304; // 手机号格式错误
    const PASSWORD_ERROR = 10305; // 账号与密码不匹配
    const OLDPASSWORD_ERROR = 10306; //原密码错误
    const USER_ADDRESS_NOT_EXIST = 10307; // 用户地址不存在

    //商品
    const GOODS_NOT_EXIST = 10401; // 商品不存在
    const GOODS_NOT_SALE = 10402; // 商品已下架

    //拼团
    const GROUP_ACTION_NOT_EXIST = 10501;   //拼团不存在
    const GROUP_ACTION_END = 10502;     //拼团已结束
    const GROUP_ACTION_FULL = 10503;    //拼团已满人

    // 错误信息定义
    protected static $_errorMessage = array(
        self::ERROR => '系统繁忙，请稍候再试',
        self::SUCCESS => '请求正常',
        self::SCRIPT_ERROR => '语法错误',

        self::PARAM_MISSING => '缺少请求参数',
        self::PARAM_ERROR => '请求参数值不符合要求',
        // 系统错误
        self::RECORD_EXIST => '数据已存在',
        self::RECORD_NOT_EXIST => '数据不存在',
        self::INSERT_ERROR => '数据插入错误',
        self::UPDATE_ERROR => '数据库更新记录失败',
        self::DELETE_ERROR => '数据库删除记录失败',
        self::DB_TIMEOUT => '数据库操作超时',
        //APP
        self::APP_NOT_EXIST => 'APPID不合法',
        self::APP_SIGN_ERROR => '签名错误',
        self::APP_TOKEN_ERROR => '用户认证错误',
        self::APP_TOKEN_EMPTY => 'token不能为空',
        self::APP_TOKEN_TIMEOUT => 'token过期',
        self::APP_STATUS_ERROR => 'APPID状态不正常',
        self::APP_REQUEST_REPEAT => '请求重复',
        self::APP_REQUEST_TIMEOUT => '请求超时',
        self::APP_AUTHORITY_LIMIT => '权限限制',

        // 会员
        self::USER_EXIST => '用户已存在',
        self::USER_MOBILE_EXIST => '手机号已存在',
        self::USER_NOT_EXIST => '该用户不存在',
        self::USER_STATUS_FROZEN => '账号已冻结',
        self::PASSWORD_ERROR => '账号与密码不匹配',
        self::USER_CELL_ERROR => '手机号格式错误',
        self::USER_ADDRESS_NOT_EXIST => '地址不存在',

        //商品
        self::GOODS_NOT_EXIST => '商品不存在',
        self::GOODS_NOT_SALE => '商品已下架',

        //拼团
        self::GROUP_ACTION_NOT_EXIST => '拼团不存在',
        self::GROUP_ACTION_END => '该团已结束',
        self::GROUP_ACTION_FULL => '该团已满人',

    );
    
    /**
     * 返回错误信息
     *
     * @param
     *            $code
     * @return string
     */
    public static function getErrorMessage($code) {
        return isset(self::$_errorMessage[$code]) ? self::$_errorMessage[$code] : '未知错误';
    }
}
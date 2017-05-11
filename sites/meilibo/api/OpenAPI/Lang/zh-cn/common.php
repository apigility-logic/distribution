<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

/**
 * ThinkPHP 简体中文语言包
 * @category   Think
 * @package  Lang
 * @author   liu21st <liu21st@gmail.com>
 */
return array(
    //  核心
    '_MODULE_NOT_EXIST_'    =>  '无法加载模块',
    '_ERROR_ACTION_'        =>  '非法操作',
    '_LANGUAGE_NOT_LOAD_'   =>  '无法加载语言包',
    '_TEMPLATE_NOT_EXIST_'  =>  '模板不存在',
    '_MODULE_'              =>  '模块',
    '_ACTION_'              =>  '操作',
    '_ACTION_NOT_EXIST_'    =>  '控制器不存在或者没有定义',
    '_MODEL_NOT_EXIST_'     =>  '模型不存在或者没有定义',
    '_VALID_ACCESS_'        =>  '没有权限',
    '_XML_TAG_ERROR_'       =>  'XML标签语法错误',
    '_DATA_TYPE_INVALID_'   =>  '非法数据对象！',
    '_OPERATION_WRONG_'     =>  '操作出现错误',
    '_NOT_LOAD_DB_'         =>  '无法加载数据库',
    '_NO_DB_DRIVER_'        =>  '无法加载数据库驱动',
    '_NOT_SUPPORT_DB_'      =>  '系统暂时不支持数据库',
    '_NO_DB_CONFIG_'        =>  '没有定义数据库配置',
    '_NOT_SUPPERT_'         =>  '系统不支持',
    '_CACHE_TYPE_INVALID_'  =>  '无法加载缓存类型',
    '_FILE_NOT_WRITEABLE_'  =>  '目录（文件）不可写',
	'_METHOD_NOT_EXIST_'    =>  '您所请求的方法不存在！',
    '_CLASS_NOT_EXIST_'     =>  '实例化一个不存在的类！',
    '_CLASS_CONFLICT_'      =>  '类名冲突',
    '_TEMPLATE_ERROR_'      =>  '模板引擎错误',
    '_CACHE_WRITE_ERROR_'   =>  '缓存文件写入失败！',
    '_TAGLIB_NOT_EXIST_'    =>  '标签库未定义',
	'_OPERATION_FAIL_'      =>  '操作失败！',
	'_OPERATION_SUCCESS_'   =>  '操作成功！',
	'_SELECT_NOT_EXIST_'    =>  '记录不存在！',
    '_EXPRESS_ERROR_'       =>  '表达式错误',
    '_TOKEN_ERROR_'         =>  '表单令牌错误',
    '_RECORD_HAS_UPDATE_'   =>  '记录已经更新',
    '_NOT_ALLOW_PHP_'       =>  '模板禁用PHP代码',
    '_PARAM_ERROR_'         =>  '参数错误或者未定义',

    //Base
    '_MISS_TOKEN_'          =>  'token不存在',
    '_INVALID_TOKEN_'       =>  '无效的token类型。',
    '_OVERDUE_TOKEN_'       =>  '会话已过期。',
    '_ILLEGAL_TOKEN_'       =>  'token非法',
    '_PARAM_PAGE_AND_SIZE_NOT_INTEGER_'         =>  '参数 [size] 或 [page] 应该是integer',
    '_PARAM_PAGE_NOT_ZERO_'         =>  '参数 [page] 应该大于 0',
    '_PARAM_SIZE_BETWEEN_WITH_'         =>  '参数 [size] 应该在范围',
    '_MUST_BE_POST_'        =>  '必须为POST请求！',
    '_NOT_JSON_'            =>  '数据有误,不是正确的json格式！',


    //Anchor
    '_CITY_TYPE_ERROR_'     =>  '城市类型错误！',
    '_QUERY_NOT_EMPTY_'     =>  'query 不能为空。',
    '_QUERY_TYPE_ERROR_'     =>  'query 类型错误!',
    '_PARAM_ILLEGAL_'       =>  '参数不合法',
    '_UPDATE_ERROR_'        =>  '更新失败,请稍后重试...',
    '_UPDATE_SUCCESS_'      =>  '更新成功！',
    '_USER_ID_ILLEGAL_'     =>  '用户ID不合法！',
    '_PARAM_IS_EMPTY_'      =>  '请求参数为空',

    //API
    '_INTERNAL_INTERFACE_'  =>  '禁止！内部接口！',

    //ApplePay
    '_MISS_APPLE_CONFIG_'   =>  '缺少苹果支付相关配置！',
    '_APPLE_CHECK_FAILED_'  =>  '校验失败，系统将自动补偿，请耐心等待...',
    '_APPLE_SYSTEM_ERROR_'  =>  '哎呀，系统开小差了，系统稍微为您自动恢复...',

    //Auth
    '_UNSUPPORTED_TYPE_'    =>  '暂不支持的类型!',
    '_BIND_WEIXIN_REPEAT_'  =>  '请勿重复绑定！',
    '_BIND_WEIXIN_SUCCESS_' =>  '绑定成功！',
    '_BIND_WEIXIN_FAILED_' =>  '绑定失败！',

    //Avatar
    '_AVATAR_UPLOAD_'       =>  '接口未实现！',

    //Channel
    '_CHANNEL_NOT_DATA_'    =>  '没有数据！',

    //CONFIG
    '_CONFIG_ERROR_'        =>  '配置错误！',

    //Confirm
    '_TOURIST_SIGN_'        =>  '游客验证！',
    '_CONFIRM_NOT_BIND_' =>  '没有绑定第三方用户ID...',
    '_PERSONAL_LIVE_'       =>  '个人直播!',
    '_VERIFY_FAILED_'       =>  '非法操作，验证类型不正确...',
    '_USER_DOES_NOT_EXIST_' =>  '非法操作，不存在该用户！',
    '_USER_MAYBE_NOT_EXIST_' => '用户名或者密码错误，请重试',
    '_USER_NAME_NOT_EXIST_' => '用户名不存在，请重试',
    
    //GIFT
    '_GIFT_GIVE_YOURSELF_'  =>  '不可以给自己发礼物。',
    '_GIFT_DOES_NOT_EXIST_' =>  '无此礼物。',
    '_TOUID_ILLEGAL_'       =>  'to_uid 参数无效',
    '_NO_COIN_'=>   '你的余额不足！',
    '_GIFT_SENT_'           =>  ' 向 ',
    '_A_GIFT_'              =>  ' 赠送礼物 ',     
    '_NOT_GIFT_'            =>  '没有这个礼物！',  
    '_IN_'                  =>  ' 在 ',
    '_SENT_GIFT_'           =>  '的房间送出了 ',

    '_GET_RED_ERROR_'       =>  '获取红包信息错误',
    '_NOT_RED_REGION_'      =>  '不在该红包发放区域内',
    '_ALREADY_RECEIVED_'    =>  '已领取过红包',
    '_RED_IS_NOT_'          =>  '红包已发完',

    '_SEND_FAILED_'         =>  '发送失败。',
    '_SENT_BARRAGE_'        =>  '的房间发送了一条弹幕',
    '_BUY_TICKET_'          =>  '的房间购买了门票',
    //Message
    '_MESSAGE_GIVE_YOURSELF_'=> '不能对自己发消息',
    '_FRIENDSHIP_BROKE_'    =>  '好友关系破裂啦。',


    //all
    '_HOT_'                 =>  '热门',
    '_MARS_'            =>  '火星',
    '_PLEASE_SELECT_'       =>  '请选择...',


    //order
    '_ORDER_DOES_NOT_EXIST_'=>  '订单号不存在',
    '_ALIPAY_'              =>  '支付宝',
    '_WECHAT_'              =>  '微信',


    //Payment
    '_AUTH_WECHAT_'         =>  '请先授权微信',
    '_WECHAT_CASH_COIN_'    =>  '最小提现金额为1元，请输入大于1元的金额数',
    '_WECHAT_CASH_F_'=>'对不起，一个月只能提现一次',
    '_CASH_SUCCESS_'        =>  '提现成功,请等待管理员审核...',
    '_CASH_FAILED_'        =>  '提现失败,请稍后重试...',
    '_CREATE_ORDER_FAILED_'=>   '创建订单失败',
    '_BUY_COIN_'=>'Live Currency purchase',
    '_LIVE_'=>'直播',
    '_LIVE_CASH_'       =>  '直播用户微信提现',

    //private
    '_NOT_OPEN_'        =>  '该房间没有开起直播',
    '_SUCCESS_'         =>  '操作成功！',
    '_FAILED_'         =>  '操作失败！',
    '_WELCOME_'               =>  '欢迎回来！',

    '_PASS_ERROR_'      => '密码错误',
    '_NOT_ENOUGH_LEVEL_'    =>   '等级不够呢，请赶快升级吧...',
    '_VERIFICATION_'    =>  '请先完成签约',
    '_CLOSE_LIVE_TIME_' =>  '该时段暂时关闭直播功能',
    '_PHONENUM_NOT_R_'  =>  '手机号不正确',
    '_OPERATION_TOO_FREQUENT_'  =>'操作太过于频繁',

    '_SMS_MESSAGE_ONE_' =>  '【喵榜直播】您的验证码为:',
    '_SMS_MESSAGE_TOW_' =>  '，5分钟内有效，切勿告知任何人。【喵榜直播】',
    '_CODE_FAILED_' =>  '发送验证码失败,请稍后重试',
    '_CODE_SUCCESS_' =>  '验证码已经发送成功',
    '_CODE_TYPE_FAILED_'    =>  '验证码格式不正确',
    '_CODE_OVERDUE_'    =>  '验证码已过期',
    '_CODE_FAIL_'    =>  '验证码错误',
    '_CODE_SYSTEM_ERROR_'   =>  '系统异常,注册失败',

    //Topic
    '_PRIVATE_SUCCESS_' =>  '添加私密成功',
    '_PRIVATE_FAILED_' =>  '添加私密失败',
    '_PRIVATE_NOT_OPEN_LIVE_' =>  '没有开启直播',
    '_PRIVATE_TYPE_IS_NOT_EXIST_' =>  '不存在类型',
    '_PRIVATE_NOTIS_' =>  '不是私密直播',
    '_ADD_ERROR_'   =>  '添加记录失败',
    '_NO_'      =>  '无',
    '_NO_APPROVE_'  =>  '未认证',

    '_USER_NAME_E_' => '用户名不合法',
    '_REGISTER_E_' => '包含不允许注册的词语',
    '_ALREADY_USERNAME_' => '用户名已经存在',
    '_EMAIL_E_' => 'Email 格式有误',
    '_EMAIL_BAN_' => 'Email 不允许注册',
    '_ALREADY_EMAIL_' => '该 Email 已经被注册',
    '_UNKNOWN_' => '未知错误',
    '_U_P_NOT_EMPTY_'    =>'用户名和密码不可以为空',
    '_PASS_ILLEGAL_'    =>  '密码不合法',

    '_CASH_NUM_ILLEGAL' =>  '提现数字不合法，或者提现金额为零',
    '_NOT_ALIPAY_'  =>  '没有支付宝账号',
    '_UPDATE_ALIPAY_E_' =>  '更新支付宝账号错误',
    '_NOT_CASH_COIN_'   =>  '没有可提现金额',
    '_CASH_COIN_E_'     =>  '提现金额大于实际额度',
    '_SYSTEM_BUSY_' =>  '系统繁忙，请稍后重试',

    //过来人告诉你TMD以后一定要一开始就这样做
    '_NOT_ATTENTION_SELF_'  =>  '不可以关注自己',
    '_ATTENTION_DOES_NOT_EXIST_'    =>  '关注的用户不存在',
    '_ALREADY_ATTENTION_'   =>  '您已关注该用户了',
    '_UNABLE_ATTENTION_'    =>  '暂时无法关注',
    '_BROADCAST_MESSAGE_'   =>  '直播消息：',
    '_ATTENTION_AHCHOR_'    =>  ' 关注了主播',
    '_ATTENTION_SUCCESS_'   =>  '关注成功！',
    '_ATTENTION_FAILED_'   =>  '关注失败！',
    '_ATTENTION_CANCELED_'  =>  '已取消关注',
    '_NICKNAME_ERROR_'      =>  '昵称不能大于15字',
    '_NAME_LONG_ERROR_'          => '昵称过长',
    '_NAME_SHORT_ERROR_'          => '昵称过短',
    '_INTRO_ERROR_'         =>  '个人签名参数不对，不能大于40字。',
    '_SIX_ERROR_'           =>  '性别不对',
    '_BIRTHDAY_ERROR_'      =>  '生日格式不对',
    '_BIRTHDAY_ERROR2_'      =>  '生日不能比现在时间还大',
    '_USER_PULL_BLACK_'     =>  '该用户已被拉黑！',
    '_PULL_BLACK_SUCCESS_'  =>  '拉黑成功',
    '_PULL_BLACK_FAIlED_'  =>  '拉黑失败',
    '_REMOVE_BLACK_' =>  '已将用户移除黑名单',
    '_REMOVE_BLACK_FAIlED_' =>  '移除失败',
    '_TYPE_NOT_'  =>  '暂不支持的类型',
    '_AUTO_UNLOCK_' =>  '自动解封',
    '_FROZEN_'      =>  '您的账号已经被冻结，请联系客服',
    '_CONVERTED_ONE_'    => ' 兑换了 ', 
    '_CONVERTED_TOW_'    => ' 虚拟币到账户 ', 

    '_NOT_REPEAT_'      =>  '您已申请认证，请勿重复申请...',
    '_INFORMATION_FILL_'    => '请填写完整您的信息哟。', 
    '_ID_PHOTO_'        =>  '请上传手持身份证照片',

    '_ID_CARD_'         =>  '请上传身份证正面照片。',


	'_JMS_FIRST_LOGIN_' => '欢迎登陆',
    '_BAN_PLAY_'    =>  '您已被禁止播放',
    '_ALREADY_REPORTED_' => '您已经举报过他了...',

	'_MONEY_UNIT_E_' => '亿',
	'_MONEY_UNIT_W_' => '万',
);

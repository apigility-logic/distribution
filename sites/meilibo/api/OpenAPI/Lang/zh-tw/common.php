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
    '_MODULE_NOT_EXIST_' => '無法加載模塊',
    '_ERROR_ACTION_' => '非法操作',
    '_LANGUAGE_NOT_LOAD_' => '無法加載語言包',
    '_TEMPLATE_NOT_EXIST_' => '範本不存在',
    '_MODULE_' => '模塊',
    '_ACTION_' => '操作',
    '_ACTION_NOT_EXIST_' => '控制器不存在或者沒有定義',
    '_MODEL_NOT_EXIST_' => '模型不存在或者沒有定義',
    '_VALID_ACCESS_' => '沒有許可權',
    '_XML_TAG_ERROR_' => 'XML標籤語法錯誤',
    '_DATA_TYPE_INVALID_' => '非法數據對象！',
    '_OPERATION_WRONG_' => '操作出現錯誤',
    '_NOT_LOAD_DB_' => '無法加載資料庫',
    '_NO_DB_DRIVER_' => '無法加載資料庫驅動',
    '_NOT_SUPPORT_DB_' => '系統暫時不支持資料庫',
    '_NO_DB_CONFIG_' => '沒有定義資料庫配寘',
    '_NOT_SUPPERT_' => '系統不支持',
    '_CACHE_TYPE_INVALID_' => '無法加載緩存類型',
    '_FILE_NOT_WRITEABLE_' => '目錄（檔案）不可寫',
    '_METHOD_NOT_EXIST_' => '您所請求的方法不存在！',
    '_CLASS_NOT_EXIST_' => '實例化一個不存在的類！',
    '_CLASS_CONFLICT_' => '類名衝突',
    '_TEMPLATE_ERROR_' => '範本引擎錯誤',
    '_CACHE_WRITE_ERROR_' => '緩存檔案寫入失敗！',
    '_TAGLIB_NOT_EXIST_' => '標籤庫未定義',
    '_OPERATION_FAIL_' => '操作失敗！',
    '_OPERATION_SUCCESS_' => '操作成功！',
    '_SELECT_NOT_EXIST_' => '記錄不存在！',
    '_EXPRESS_ERROR_' => '運算式錯誤',
    '_TOKEN_ERROR_' => '表單權杖錯誤',
    '_RECORD_HAS_UPDATE_' => '記錄已經更新',
    '_NOT_ALLOW_PHP_' => '範本禁用PHP程式碼',
    '_PARAM_ERROR_' => '參數錯誤或者未定義',

    //Base
    '_MISS_TOKEN_' => 'token不存在',
    '_INVALID_TOKEN_' => '無效的token類型。',
    '_OVERDUE_TOKEN_' => '會話已過期。',
    '_ILLEGAL_TOKEN_' => 'token非法',
    '_PARAM_PAGE_AND_SIZE_NOT_INTEGER_' => '參數[size]或[page]應該是integer',
    '_PARAM_PAGE_NOT_ZERO_' => '參數[page]應該大於0',
    '_PARAM_SIZE_BETWEEN_WITH_' => '參數[size]應該在範圍',
    '_MUST_BE_POST_' => '必須為POST請求！',
    '_NOT_JSON_' => '數據有誤,不是正確的json格式！',


    //Anchor
    '_CITY_TYPE_ERROR_' => '都市類型錯誤！',
    '_QUERY_NOT_EMPTY_' => 'query不能為空。',
    '_QUERY_TYPE_ERROR_' => 'query類型錯誤！',
    '_PARAM_ILLEGAL_' => '參數不合法',
    '_UPDATE_ERROR_' => '更新失敗,請稍後重試…',
    '_UPDATE_SUCCESS_' => '更新成功！',
    '_USER_ID_ILLEGAL_' => '用戶ID不合法！',
    '_PARAM_IS_EMPTY_' => '請求參數為空',
    //API
    '_INTERNAL_INTERFACE_' => '禁止！內部介面！',
    //ApplePay
    '_MISS_APPLE_CONFIG_' => '缺少蘋果支付相關配寘！',
    '_APPLE_CHECK_FAILED_' => '校驗失敗,系統將自動補償,請耐心等待…',
    '_APPLE_SYSTEM_ERROR_' => '哎呀,系統開小差了,系統稍微為您自動恢復…',

    //Auth
    '_UNSUPPORTED_TYPE_' => '暫不支持的類型！',
    '_BIND_WEIXIN_REPEAT_' => '請勿重複綁定！',
    '_BIND_WEIXIN_SUCCESS_' => '綁定成功！',
    '_BIND_WEIXIN_FAILED_' => '綁定失敗！',
    //Avatar
    '_AVATAR_UPLOAD_' => '介面未實現！',
    //Channel
    '_CHANNEL_NOT_DATA_' => '沒有數據！',
    //CONFIG
    '_CONFIG_ERROR_' => '配寘錯誤！',
    //Confirm
    '_TOURIST_SIGN_' => '遊客驗證！',
    '_CONFIRM_NOT_BIND_' => '沒有綁定協力廠商用戶ID…',
    '_PERSONAL_LIVE_' => '個人直播！',
    '_VERIFY_FAILED_' => '非法操作,驗證類型不正確…',
    '_USER_DOES_NOT_EXIST_' => '非法操作,不存在該用戶！',
    
    //GIFT
    '_GIFT_GIVE_YOURSELF_' => '不可以給自己發禮物。',
    '_GIFT_DOES_NOT_EXIST_' => '無此禮物。',
    '_TOUID_ILLEGAL_' => 'to_uid參數無效',
    '_NO_COIN_'=> '你的餘額不足！',
    '_GIFT_SENT_' => '向',
    '_A_GIFT_' => '贈送禮物',
    '_NOT_GIFT_' => '沒有這個禮物！',
    '_IN_' => '在',
    '_SENT_GIFT_' => '的房間送出了',
    '_GET_RED_ERROR_' => '獲取紅包資訊錯誤',
    '_NOT_RED_REGION_' => '不在該紅包發放區域內',
    '_ALREADY_RECEIVED_' => '已領取過紅包',
    '_RED_IS_NOT_' => '紅包已發完',
    '_SEND_FAILED_' => '發送失敗。',
    //Message
    '_MESSAGE_GIVE_YOURSELF_'=> '不能對自己發消息',
    '_FRIENDSHIP_BROKE_' => '好友關係破裂啦。',


    //all
    '_HOT_' => '熱門',
    '_MARS_' => '火星',
    '_PLEASE_SELECT_' => '請選擇…',
    //order
    '_ORDER_DOES_NOT_EXIST_'=> '訂單號不存在',
    '_ALIPAY_' => '支付寶',
    '_WECHAT_' => '微信',
    //Payment
    '_AUTH_WECHAT_' => '請先授權微信',
    '_WECHAT_CASH_COIN_' => '最小提現金額為1元,請輸入大於1元的金額數',
    '_WECHAT_CASH_F_'=>'對不起,一個月只能提現一次',
    '_CASH_SUCCESS_' => '提現成功,請等待管理員稽核…',
    '_CASH_FAILED_' => '提現失敗,請稍後重試…',
    '_CREATE_ORDER_FAILED_'=> '創建訂單失敗',
    '_BUY_COIN_'=>'Live Currency purchase',
    '_LIVE_'=>'直播',
    '_LIVE_CASH_' => '直播用戶微信提現',
    //private
    '_NOT_OPEN_' => '該房間沒有開起直播',
    '_SUCCESS_' => '操作成功！',
    '_FAILED_' => '操作失敗！',
    '_WELCOME_' => '歡迎回來！',

    '_PASS_ERROR_' => '密碼錯誤',
    '_NOT_ENOUGH_LEVEL_' => '等級不够呢,請趕快陞級吧…',
    '_VERIFICATION_' => '請先完成簽約',
    '_CLOSE_LIVE_TIME_' => '該時段暫時關閉直播功能',
    '_PHONENUM_NOT_R_' => '手機號不正確',
    '_OPERATION_TOO_FREQUENT_' =>'操作太過於頻繁',
    '_SMS_MESSAGE_ONE_' => '【美麗播直播服務】您的驗證碼為：',
    '_SMS_MESSAGE_TOW_' => ',5分鐘內有效,切勿告知任何人。【美麗播】',
    '_CODE_FAILED_' => '發送驗證碼失敗,請稍後重試',
    '_CODE_SUCCESS_' => '驗證碼已經發送成功',
    '_CODE_TYPE_FAILED_' => '驗證碼格式不正確',
    '_CODE_OVERDUE_' => '驗證碼已過期',
    '_CODE_FAIL_' => '驗證碼錯誤',
    '_CODE_SYSTEM_ERROR_' => '系統异常,注册失敗',
    //Topic
    '_PRIVATE_SUCCESS_' => '添加私密成功',
    '_PRIVATE_FAILED_' => '添加私密失敗',
    '_PRIVATE_NOT_OPEN_LIVE_' => '沒有開啟直播',
    '_PRIVATE_TYPE_IS_NOT_EXIST_' => '不存在類型',
    '_PRIVATE_NOTIS_' => '不是私密直播',
    '_ADD_ERROR_' => '添加記錄失敗',
    '_NO_' => '無',
    '_NO_APPROVE_' => '未認證',
    '_USER_NAME_E_' => '用戶名不合法',
    '_REGISTER_E_' => '包含不允許注册的詞語',
    '_ALREADY_USERNAME_' => '用戶名已經存在',
    '_EMAIL_E_' => 'Email格式有誤',
    '_EMAIL_BAN_' => 'Email不允許注册',
    '_ALREADY_EMAIL_' => '該Email已經被注册',
    '_UNKNOWN_' => '未知錯誤',
    '_U_P_NOT_EMPTY_' =>'用戶名和密碼不可以為空',
    '_PASS_ILLEGAL_' => '密碼不合法',
    '_CASH_NUM_ILLEGAL' => '提現數位不合法,或者提現金額為零',
    '_NOT_ALIPAY_' => '沒有支付寶帳號',
    '_UPDATE_ALIPAY_E_' => '更新支付寶帳號錯誤',
    '_NOT_CASH_COIN_' => '沒有可提現金額',
    '_CASH_COIN_E_' => '提現金額大於實際額度',
    '_SYSTEM_BUSY_' => '系統繁忙,請稍後重試',

    //過來人告訴你TMD以後一定要一開始就這樣做
    '_NOT_ATTENTION_SELF_' => '不可以關注自己',
    '_ATTENTION_DOES_NOT_EXIST_' => '關注的用戶不存在',
    '_ALREADY_ATTENTION_' => '您已關注該用戶了',
    '_UNABLE_ATTENTION_' => '暫時無法關注',
    '_BROADCAST_MESSAGE_' => '直播消息：',
    '_ATTENTION_AHCHOR_' => '關注了主播',
    '_ATTENTION_SUCCESS_' => '關注成功！',
    '_ATTENTION_FAILED_' => '關注失敗！',
    '_ATTENTION_CANCELED_' => '已取消關注',
    '_NICKNAME_ERROR_' => '昵稱不能大於15字',
    '_INTRO_ERROR_' => '個人簽名參數不對,不能大於40字。',
    '_SIX_ERROR_' => '性別不對',
    '_BIRTHDAY_ERROR_' => '生日格式不對',
    '_BIRTHDAY_ERROR2_' => '生日不能比現在時間還大',
    '_USER_PULL_BLACK_' => '該用戶已被拉黑！',
    '_PULL_BLACK_SUCCESS_' => '拉黑成功',
    '_PULL_BLACK_FAIlED_' => '拉黑失敗',
    '_REMOVE_BLACK_' => '已將用戶移除黑名單',
    '_REMOVE_BLACK_FAIlED_' => '移除失敗',
    '_TYPE_NOT_' => '暫不支持的類型',
    '_AUTO_UNLOCK_' => '自動解封',
    '_FROZEN_' => '您的帳號已經被凍結,請聯系客服',
    '_CONVERTED_ONE_' => '兌換了',
    '_CONVERTED_TOW_' => '虛擬幣到帳戶',
    '_NOT_REPEAT_' => '您已申請認證,請勿重複申請…',
    '_INFORMATION_FILL_' => '請填寫完整您的資訊喲。',
    '_ID_PHOTO_' => '請上傳手持身份證照片',
    '_ID_CARD_' => '請上傳身份證正面照片。',

    '_JMS_FIRST_LOGIN_' => '歡迎登陸',
    '_BAN_PLAY_'    =>  '您已被禁止播放',
    '_ALREADY_REPORTED_' => '您已經舉報過他了...',

    '_MONEY_UNIT_E_' => '億',
    '_MONEY_UNIT_W_' => '萬',
);
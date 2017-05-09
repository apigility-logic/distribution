<?php
namespace Application\Asset;

class AI {

	// AI 用户列表
	public static $AIUser =  array(
			array('user_id'=>900, 'username'=>'AI_1','client_name'=>'机器人1号', 'ucuid'=>843, 'levelid'=>0,'vip'=>0,'AI'=>true),
			// array('user_id'=>844, 'username'=>'AI_2','client_name'=>'机器人2号', 'ucuid'=>783, 'levelid'=>0,'vip'=>1,'AI'=>true),
			array('user_id'=>868, 'username'=>'AI_3','client_name'=>'机器人3号', 'ucuid'=>807, 'levelid'=>0,'vip'=>0,'AI'=>true),
			array('user_id'=>911, 'username'=>'AI_4','client_name'=>'机器人4号', 'ucuid'=>854, 'levelid'=>0,'vip'=>0,'AI'=>true),
			array('user_id'=>788, 'username'=>'AI_5','client_name'=>'机器人5号', 'ucuid'=>911, 'levelid'=>0,'vip'=>2,'AI'=>true),
			array('user_id'=>855, 'username'=>'AI_6','client_name'=>'机器人6号', 'ucuid'=>1077, 'levelid'=>0,'vip'=>0,'AI'=>true),
			array('user_id'=>854, 'username'=>'AI_7','client_name'=>'机器人7号', 'ucuid'=>1033, 'levelid'=>0,'vip'=>0,'AI'=>true),
			array('user_id'=>849, 'username'=>'AI_8','client_name'=>'机器人8号', 'ucuid'=>133, 'levelid'=>0,'vip'=>0,'AI'=>true),
			// array('user_id'=>847, 'username'=>'AI_9','client_name'=>'机器人8号', 'ucuid'=>137, 'levelid'=>18,'vip'=>0,'AI'=>true),
			// array('user_id'=>843, 'username'=>'AI_8','client_name'=>'机器人8号', 'ucuid'=>133, 'levelid'=>8,'vip'=>0,'AI'=>true),
		);

	/**
	 * 系统提示语【中文版】
	 */
	public static $SysMsgCh = "系统提示：我们倡导绿色直播，封面及直播内容均不可包含抽烟、低俗、引诱、暴露等内容。如有发现将被封停账号，网警24小时在线巡查哦！";

	/**
	 * 其他语言，系统提示语
	 */
	public static $SysMsgAother = array(
		'US-en' => "System Tip: we advocate green video, cover and living content can not include smoking, vulgar, temptation, exposure and other content. If found will be closed. Police will watch for 24 hours...",
		'TW-zh' => '系統提示：我們倡導綠色直播，封面及直播內容均不可包含抽煙、低俗、引誘、暴露等內容。如有發現將被封停賬號，網警24小時在線巡查哦！', // 台湾繁体
		'SG-zh' => '系统提示：我们倡导绿色直播，封面及直播内容均不可包含抽烟、低俗、引诱、暴露等内容。如有发现将被封停账号，网警24小时在线巡查哦！', // 新加坡简体
        'CN-zh' => '系统提示：我们倡导绿色直播，封面及直播内容均不可包含抽烟、低俗、引诱、暴露等内容。如有发现将被封停账号，网警24小时在线巡查哦！', // 中文
		'HK-zh' => '系統提示：我們倡導綠色直播，封面及直播內容均不可包含抽煙、低俗、引誘、暴露等內容。如有發現將被封停賬號，網警24小時在線巡查哦！', // 香港繁体
		'MO-zh' => '系統提示：我們倡導綠色直播，封面及直播內容均不可包含抽煙、低俗、引誘、暴露等內容。如有發現將被封停賬號，網警24小時在線巡查哦！', // 澳门繁体
		);

	/**
	* 关键字过滤
	*/
	public static $keyWord = array(
		"毛泽东","毛贼东","东泽毛","mzd","刘少奇","邓小平","平小邓","胡耀邦","耀邦","赵紫阳","江泽民","泽民","江贼","民泽江","胡锦涛","温家宝","习近平","江泽民"
		);
}

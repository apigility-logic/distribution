<?php
namespace Config;

class Site {
	// 域名
	public static $host = 'http://zhibo.mimilove520.com/';

	// Register address
	public static $registerAddress = '127.0.0.1:1236';

	// 是否开启start_room_online_detection.php脚本
	public static $enableRoomOnlineDetect = false;

	// 主播房间推送多少个在线用户头像
	public static $onlineUserDisplayCount = 5;
}

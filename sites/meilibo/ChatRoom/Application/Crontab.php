<?php
namespace Application;

use \Workerman\Autoloader;
use \GatewayWorker\Lib\Mem;
use \GatewayWorker\Lib\Db;
use \Application\Asset\AI;

require_once __DIR__ . '/GatewayClient/Gateway.php';
\Gateway::$registerAddress = \Config\Site::$registerAddress;

/**
 * 更新日志
 * 28/12/2016	初始化随机机器人数量500-800,每五分钟增加0-20
 */

class Crontab {
	// 在线房间人数
	const ROOM_ONLINE_NUM_PREFIX = "ROOM_ONLINE_NUM_";
	// 房间在线用户等级排序列表
	const ROOM_SORTED_CLIENTS_PREFIX = "ROOM_SORTED_CLIENTS_";
	// 热门主播列表
	const ROOM_SORTED_HOT_LIST = "ROOM_SORTED_HOT_LIST";
	// 当前房间数量
	const ROOM_COUNT_ALL = "ROOM_COUNT_ALL";
	// 主播在线标识位
	const ROOM_ANCHOR_ONLIE_PREFIX = "ROOM_ANCHOR_ONLIE_PREFIX_";

	// 随机机器人数量
	public static $baseRobotNum = array('min' => 0, 'max' => 0);

	// 增量增加机器人数量的时间【仅仅是数字而已！】，单位为秒级
	public static $incRobotInterval = 300;

	// 每 {$incRobotInterval} 分钟增加数量
	public static $incRobotNum = array('min' => 0, 'max' => 0);

	// 每个房间开播的时间

	// 缓存热门主播列表标志位
	public static $cacheHotRoomFlag = false;

	// 当前在线房间列表
	public static $currentRoomList = array();

	// 所有当前在线房间 Timer
	public static $allRoomTimer = array();

	// AI用户列表
	public static $AIList = array();

	// AI 所在房间map
	public static $AIMap = array();

	public static function setAnchorOnline($room_id, $user_id) {
		if (!Mem::get(self::ROOM_ANCHOR_ONLIE_PREFIX . $room_id)) {
			RoomStatus::updateAnchorOnLineToDb($user_id);
		}
		Mem::set(self::ROOM_ANCHOR_ONLIE_PREFIX . $room_id, 'online', 0, 30);
	}
	public static function delAnchorOnline($room_id) {
		Mem::delete(self::ROOM_ANCHOR_ONLIE_PREFIX . $room_id);
	}

	/**
	* 初始化AI 用户
	*/

	public static function initAI()
    {
			$allAI = AI::$AIUser;
            //$allAI = Db::instance('dbDefault')->query("select id as user_id, username, nickname, ucuid, vip from ss_member where is_robot = 1 ");
            foreach ($allAI as $thisAI) {
                    $thisAI['AI'] = true;
                    $thisAI['levelid'] = 1;
                    static::$AIList[] = $thisAI;
            }
    }


	/**
	* 获取随机AI用户
	*
	* @return array
	*/
	public static function getRandomAI($room_id)
	{
		// 不要机器人
		// return array();
		
		if (isset(static::$AIMap[$room_id]) && (time() - static::$AIMap[$room_id]['time']) < 8) {
			return static::$AIMap[$room_id]['list'];
		}

		$tmpAI = array();
		//$num = isset(static::$AIMap[$room_id]) ? count(static::$AIMap[$room_id]) : mt_rand(25, 65);
        $virtual = Db::instance('dbDefault')->query("select isvirtual, virtualguest from ss_member where curroomnum = $room_id ");
        $global_robot  = Db::instance('dbDefault')->query("select robot_switch, robot_max from ss_siteconfig where id = 1 "); 
        $countAI  = Db::instance('dbDefault')->query("select count( id ) num from ss_member where is_robot = 1 "); 

        if( $global_robot[0]['robot_switch'] == '1' ){
        	if( $virtual[0]['isvirtual'] == 'y' ){
        		$AInum = $virtual[0]['virtualguest'] > $countAI[0]['num'] ? $countAI[0]['num'] : $virtual[0]['virtualguest'];
                while(count($tmpAI) < $AInum && static::$AIList) {
                        // 随机得到三个AI用户
                        $key = array_rand(static::$AIList);
                        if(!$key) {
                                continue;
                        }
                        $ai = static::$AIList[$key];
                        if (isset($tmpAI[$ai['username']])) {
                                continue ;
                        }
                        $tmpAI[$ai['username']] = $ai;
                }
                static::$AIMap[$room_id] = array('time'=>time(), 'list'=>$tmpAI);
                return $tmpAI;
	        }else{
        		$AInum = $global_robot[0]['robot_max'] > $countAI[0]['num'] ? $countAI[0]['num'] : $global_robot[0]['robot_max'];
                while(count($tmpAI) < $AInum && static::$AIList) {
                        // 随机得到三个AI用户
                        $key = array_rand(static::$AIList);
                        if(!$key) {
                                continue;
                        }
                        $ai = static::$AIList[$key];
                        if (isset($tmpAI[$ai['username']])) {
                                continue ;
                        }
                        $tmpAI[$ai['username']] = $ai;
                }
                static::$AIMap[$room_id] = array('time'=>time(), 'list'=>$tmpAI);
                return $tmpAI;
	        }
        }else{
            return array();
        }
        

	}

	public static function initAllRoom()
	{
		echo "initAllRoom ".PHP_EOL;

		if (static::$cacheHotRoomFlag) {
			arsort(static::$currentRoomList);
			$tmp = count(static::$currentRoomList) <= 300 ? static::$currentRoomList : array_slice(static::$currentRoomList,0,300,true);
			Mem::set(static::ROOM_SORTED_HOT_LIST, json_encode($tmp));
		}
		Mem::set(static::ROOM_COUNT_ALL, count(static::$currentRoomList));

		$onlineRooms = static::getAllOnLineRoom();
		/*
		$allGroupInfo =  \Gateway::getAllClientInfo();
		$tmp = array();
		foreach($allGroupInfo as $client_id => $status) {
			if (empty($status)) {
				continue ;
			}
			if (!isset($tmp[$status['room_id']])) {
				$tmp[$status['room_id']] = array();
			}
			$tmp[$status['room_id']][] = $status;
		}
		unset($allGroupInfo);
		*/
		// 删除不在直播房间的Timer
		foreach (static::$allRoomTimer as $room_id => $timer) {
			if (in_array($room_id,$onlineRooms)) {
				continue ;
			}
			static::removeRoomTimer($room_id);
		}

		$tmp = array();
		foreach ($onlineRooms as $room_id) {
			$tmp[$room_id] = \Gateway::getClientInfoByGroup($room_id);
		}
		static::$currentRoomList = $group_info = array();
		foreach($tmp as $room_id => $group_client) {
			static::$currentRoomList[$room_id] = 0;
			if (count($group_client) == 0) {
				self::removeRoomTimer($room_id);
				continue;
			}
			if (isset(static::$allRoomTimer[$room_id])) {
				$group_info[$room_id] = static::$allRoomTimer[$room_id];
				continue;
			}
			$group_info[$room_id] = \Workerman\Lib\Timer::add(5, array("\Application\Crontab","getClientList"),array($room_id),true);
		}

		// if (!empty(static::$allRoomTimer)) {
		//      $roomOrignal = array_keys(static::$allRoomTimer);
		//      $roomNew = array_keys($group_info);
		//      $arrayDiff = array_diff($roomOrignal, $roomNew);
		//      foreach($arrayDiff as $room_id => $timer) {
		//              self::removeRoomTimer($room_id);
		//      }
		// }
		// unset($tmp);
		static::$allRoomTimer = $group_info;
	}

	public static function getAllOnLineRoom()
	{
		$group = RoomStatus::getOnlineRoom();
		$tmp = array();
		if ( !is_array($group) || empty($group)) {
			return $tmp;
		}

		foreach ($group as $room) {
			if ($room['curroomnum'] <= 0) {
				continue;
			}
			$tmp[] = $room['curroomnum'];
		}
		return $tmp;
	}

	public static function removeRoomTimer($room_id)
	{
		echo "removeRoomTimer {$room_id} ".PHP_EOL;

		if (!isset(static::$allRoomTimer[$room_id])) {
			return ;
		}
		\Workerman\Lib\Timer::del(static::$allRoomTimer[$room_id]);
		unset(static::$allRoomTimer[$room_id]);
	}

	public static function getClientList($room_id)
	{
		echo "getClientList {$room_id} ".PHP_EOL;
		$room_info = \Gateway::getClientInfoByGroup($room_id);
		//$roomAI = self::getRandomAI($room_id);

		$roomAI = array();
		$room_info = array_merge($room_info, $roomAI);

		$adminer_list = array();
		$client_list = array();
		foreach ($room_info as $client_id => $session) {
			if (empty($session)) {
				continue;
			}
			$session['avatar'] = AdminClient::getAvatar($session['user_id']);
			if (isset($session['role'])) {
				if (strcmp($session['role'], 'owner') === 0) {
					continue;
				}
				$adminer_list[] = $session;
			} else {
				$client_list[] = $session;
			}
		}

		$viewer_num = 0;
		$adminer_list = self::sortClient($adminer_list, true, $viewer_num);
		$client_list = self::sortClient($client_list, false, $viewer_num);

		// 真实在线用户数字
        $real_count = count($client_list) + count($adminer_list);

		// 虚拟机器人数量
		$num = json_decode(Mem::get(static::ROOM_ONLINE_NUM_PREFIX . $room_id), true);
		$baseIncRobotNum = 0;
		$robot_time = 0;
		if (is_array($num) && isset($num['robot_num'])) {
			$baseIncRobotNum = $num['robot_num'];
			$robot_time = $num['robot_time'];
			if (time() - $num['robot_time'] >= self::$incRobotInterval) {
				$baseIncRobotNum = $baseIncRobotNum + mt_rand(self::$incRobotNum['min'], self::$incRobotNum['max']);
				$robot_time = time();
			}
		}

		// 所有在线用户，包括机器人
		$all_num = $real_count + $baseIncRobotNum;

        // 推多少个头像
        $online_user_display_count = isset(\Config\Site::$onlineUserDisplayCount) ? \Config\Site::$onlineUserDisplayCount : 5;
        // 人数小于 $online_user_display_count 时，保证在线人数与客户端推的用户头像数是否一致。
        $all_num = $all_num < $online_user_display_count ? count($client_list) + count($adminer_list) : $all_num;

		// 重新生成房间人数数据，并缓存起来
		$num = array(
			'all_num' => $all_num,
			'viewer_num' => $viewer_num,
			'robot_num' => $baseIncRobotNum,
			'robot_time' => !$robot_time ? time() : $robot_time
		);
		Mem::set(static::ROOM_ONLINE_NUM_PREFIX . $room_id, json_encode($num));

        static::$currentRoomList[$room_id] = $num['all_num'] + $viewer_num;

        // 截取 $online_user_display_count 个数据广播给所有客户端
        $adminer_list = array_slice($adminer_list, 0, $online_user_display_count);
        $client_list = array_slice($client_list, 0, $online_user_display_count);
        self::BroadCast($room_id, $num['all_num'], $num['viewer_num'], $adminer_list, $client_list);

		return ;
	}

    public static function sendOnLineList($room_id, $session)
    {
        $num = json_decode(Mem::get(static::ROOM_ONLINE_NUM_PREFIX . $room_id), true);
        $list = json_decode(Mem::get(static::ROOM_SORTED_CLIENTS_PREFIX . $room_id), true);
        $all_num = isset($num['all_num']) ? $num['all_num'] : 0;
        $viewer_num = isset($num['viewer_num']) ? $num['viewer_num'] : 0;

        unset($session['msged']);
        unset($session['time']);
        $session['avatar'] = AdminClient::getAvatar($session['user_id']);


        // 考虑到可能没有缓存的情况，比如主播退出后在线人数数据已被删除，或者观看的回播记录，但是又有一个问题
        // 如果此时主播在直播，但是用户看的是回播就傻逼了！！！
        // 如果是主播在线，就会重置这个数字，不会造成影响
        $baseIncRobotNum = (is_array($num) && isset($num['robot_num'])) ? $num['robot_num'] : mt_rand(self::$baseRobotNum['min'], self::$baseRobotNum['max']);

        $adminer_list = !$list ? array() : $list['adminer_list'];
        $client_list = !$list ? array() : $list['client_list'];

        if (!isset($session['role']) && !self::inOnLineList($session['user_id'], $client_list)) {
            $client_list[] = $session;
            $client_list = self::sortClient($client_list, false, $viewer_num);
            $all_num++;
        } else if (isset($session['role'])) {
            if (strcmp($session['role'], 'owner') !== 0 && !self::inOnLineList($session['user_id'], $adminer_list)) {
                $adminer_list[] = $session;
                $useless = 0;
                $adminer_list = self::sortClient($adminer_list, true, $useless);
                $all_num++;
            } else if (strcmp($session['role'], 'owner') === 0) {
                $baseIncRobotNum = mt_rand(self::$baseRobotNum['min'], self::$baseRobotNum['max']);
                // robot_time 表示上一次生成机器人数量的时间
                Mem::set(static::ROOM_ONLINE_NUM_PREFIX . $room_id, json_encode(array('all_num'=>count($adminer_list) + count($client_list) + $baseIncRobotNum, 'viewer_num'=>$viewer_num, 'robot_num'=>$baseIncRobotNum, 'robot_time'=>time())));
            }
        }

        // 推多少个头像
        $online_user_display_count = isset(\Config\Site::$onlineUserDisplayCount) ? \Config\Site::$onlineUserDisplayCount : 5;
        // 人数小于 $online_user_display_count 时，保证在线人数与客户端推的用户头像数是否一致。
        $all_num = $all_num < $online_user_display_count ? count($client_list) + count($adminer_list) : $all_num;

        $message = array(
            'type' => 'onLineClient',
            'all_num' => $all_num,
            'viewer_num' => $viewer_num,
            'adminer_list' => $adminer_list,
            'client_list' => $client_list,
        );
        \Gateway::sendToClient($session['client_id'],json_encode($message));
        return ;
    }

	public static function inOnLineList($user_id, $user_info)
	{
		foreach ($user_info as $key => $session) {
			if ($session['user_id'] == $user_id) {
				return true;
			}
		}
		return false;
	}

	public static function BroadCast($room_id, $all_num, $viewer_num,$adminer_list, $client_list)
	{
		$message = array(
			'type'=>'onLineClient',
			'all_num'=>$all_num,
			'viewer_num'=>$viewer_num,
			'adminer_list'=>$adminer_list,
			'client_list'=>$client_list,
			);
		\Gateway::sendToGroup($room_id, json_encode($message));

		// 写入房间数量放在房主登录聊天室时候，因为房主开直播肯定要进入房间的
		// Mem::set(static::ROOM_ONLINE_NUM_PREFIX . $room_id, json_encode(array('all_num'=>$all_num,'viewer_num'=>$viewer_num)));
		Mem::set(static::ROOM_SORTED_CLIENTS_PREFIX . $room_id, json_encode(array('adminer_list'=>$adminer_list,'client_list'=>$client_list)));
	}
	public static function sortClient($user_info, $adminer = false , &$viewer_num)
	{
		$room_owner = null;
		$vip1_tmp = $vip2_tmp = $vip0_tmp = array();
		$hasLoginUser = array();
		$AI = array();
		foreach($user_info as $index => $session) {
			if ($adminer && strcmp($session['role'],'owner') == 0) {
				// $room_owner = $session;
				continue ;
			}
			if (!$adminer && $session['user_id'] == -1 && (!isset($session['AI']) || !$session['AI'])) {
				$viewer_num++;
				continue ;
			}
			if (in_array($session['user_id'],$hasLoginUser)) {
				// 在线列表踢出重复登录用户
				continue;
			}
			if (isset($session['AI']) && $session['AI']) {
				$AI[$session['levelid']][] = $session;
				continue;
			}
			unset($session['time']);
			unset($session['msged']);
			switch ((int)$session['vip']) {
				case 1:
					if (!isset($vip1_tmp[$session['levelid']])) {
						$vip1_tmp[$session['levelid']] = array();
					}
					$vip1_tmp[$session['levelid']][] = $session;
					break;
				case 2:
					if (!isset($vip2_tmp[$session['levelid']])) {
						$vip2_tmp[$session['levelid']] = array();
					}
					$vip2_tmp[$session['levelid']][] = $session;
					break;
				case 0:
					if (!isset($vip0_tmp[$session['levelid']])) {
						$vip0_tmp[$session['levelid']] = array();
					}
					$vip0_tmp[$session['levelid']][] = $session;
				default:
					// do nothing
			}
			$hasLoginUser[] = $session['user_id'];
		}

		unset($hasLoginUser);
		krsort($vip1_tmp);
		krsort($vip2_tmp);
		krsort($vip0_tmp);
		krsort($AI);
		$ai_list = $vip1_list = $vip2_list = $vip0_list = array();
		foreach($vip1_tmp as $levelid => $list) {
			$vip1_list = array_merge($vip1_list, $list);
		}
		foreach ($vip2_tmp as $levelid => $list) {
			$vip2_list = array_merge($vip2_list, $list);
		}
		foreach($vip0_tmp as $levelid => $list) {
			$vip0_list = array_merge($vip0_list, $list);
		}
		foreach($AI as $levelid => $list) {
			$ai_list = array_merge($ai_list, $list);
		}

		$all_client_list = $room_owner != null ? array_merge(array($room_owner), array_merge($vip1_list, array_merge($vip2_list, $vip0_list))) : array_merge($vip1_list, array_merge($vip2_list, $vip0_list));
		$all_client_list = array_merge($all_client_list, $ai_list);

		$sort_array = array();
        foreach($all_client_list as $item) {
            $sort_array[] = isset($item['levelid']) ? $item['levelid'] : 0;
        }

        // 根据等级排序
        array_multisort($sort_array, SORT_DESC, SORT_NUMERIC, $all_client_list);

		return $all_client_list;
	}
}

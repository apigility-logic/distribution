<?php
namespace Application;

use \GatewayWorker\Lib\Gateway;
use \GatewayWorker\Lib\Context;
use \Application\RoomStatus;
use \GatewayWorker\Lib\Db;
use \Config\Site;
use \Application\Asset\AI;

class AdminClient
{

  /**
  * 发言频率
  */
  protected static $frequency = array(
      //等级，发言字数，发言频率(秒)
      // adminer,sysAdminer 80, 1
      // owner 无限制
      0=>array('length'=>15,'time'=>10),
      1=>array('length'=>50,'time'=>8),
      2=>array('length'=>50,'time'=>5),
      3=>array('length'=>50,'time'=>5),
      4=>array('length'=>50,'time'=>3),
      5=>array('length'=>50,'time'=>3),
      6=>array('length'=>50,'time'=>3),
      7=>array('length'=>50,'time'=>3),
      8=>array('length'=>50,'time'=>3),
      9=>array('length'=>50,'time'=>3),
      10=>array('length'=>50,'time'=>3),
      11=>array('length'=>80,'time'=>1),
      12=>array('length'=>80,'time'=>1),
      13=>array('length'=>80,'time'=>1),
      14=>array('length'=>80,'time'=>1),
      15=>array('length'=>80,'time'=>1),
      16=>array('length'=>80,'time'=>1),
      17=>array('length'=>80,'time'=>1),
      18=>array('length'=>80,'time'=>1),
      19=>array('length'=>80,'time'=>1),
      20=>array('length'=>80,'time'=>1),
      21=>array('length'=>80,'time'=>1),
      22=>array('length'=>80,'time'=>1),
      23=>array('length'=>80,'time'=>1),
      24=>array('length'=>80,'time'=>1),
      25=>array('length'=>80,'time'=>1),
      26=>array('length'=>80,'time'=>1),
      27=>array('length'=>80,'time'=>1),
      28=>array('length'=>80,'time'=>1),
      29=>array('length'=>80,'time'=>1),
      //房主/管理
      100=>array('length'=>80,'time'=>1)
    );
  /**
  * 添加系统管理员
  */
  public static function addSysAdminer($room_id, $user_id)
  {
      $_SESSION['role'] = 'sysAdminer';
      RoomStatus::addSysAdminer($user_id);
      Gateway::sendToGroup($room_id, json_encode(array('type'=>'right.adminer', 'user_id'=>$user_id,'vip'=>$_SESSION['vip'],'levelid'=>$_SESSION['levelid'])));
  }

  /**
  * 删除系统管理员
  */
  public static function removeSysAdminer($room_id, $user_id)
  {
      if (isset($_SESSION['role']) && strcmp('sysAdminer', $_SESSION['role']) === 0) {
          unset($_SESSION['role']);
          RoomStatus::removeSysAdminer($user_id);
          Gateway::sendToGroup($room_id, json_encode(array('type'=>'right.removeAdminer', 'user_id'=>$user_id,'vip'=>$_SESSION['vip'],'levelid'=>$_SESSION['levelid'])));
      }
  }
  /**
  * 添加管理员
  */
  public static function adminer($room_id, $managed_user_id, $managed_client_id_array, $managed_client_name, $self = false, $upDb = true)
  {
        if ($upDb) {
            // 写入数据库
            $ret = Db::instance('dbDefault')->query("select id as uid from ss_member where curroomnum={$room_id}");
            if (!empty($ret[0])) {
                $uid = $ret[0]['uid'];
                $time = time();
                $exist = Db::instance('dbDefault')->query("select id from ss_roomadmin where uid='{$uid}' and adminuid='{$managed_user_id}'");
                if (empty($exist)) {
                    Db::instance('dbDefault')->query("insert into ss_roomadmin value (null,{$uid},{$managed_user_id},{$time})");
                }
            }
        }

        Gateway::sendToGroup($room_id, json_encode(array('type'=>'right.adminer', 'user_id'=>$managed_user_id,'vip'=>$_SESSION['vip'],'levelid'=>$_SESSION['levelid'])));
        if ($self) {
            $_SESSION['role'] = 'adminer';
        } else {
            $session_for_update = array('role'=>'adminer');
          foreach ($managed_client_id_array as $managed_client_id) {
              \GatewayWorker\Lib\Gateway::updateSession($managed_client_id, $session_for_update);
          }
        }
        return RoomStatus::addAdminer($room_id, $managed_user_id);
  }

  /**
  * 删除管理员
  */
  public static function removeAdminer($room_id, $managed_user_id, $managed_client_id_array, $managed_client_name, $self = false, $upDb = true)
  {
        if ($upDb) {
            // 从数据库删除
            $ret = Db::instance('dbDefault')->query("select id as uid from ss_member where curroomnum={$room_id}");
            if (!empty($ret[0])) {
              $uid = $ret[0]['uid'];
              Db::instance('dbDefault')->query("delete from ss_roomadmin where uid={$uid} and adminuid={$managed_user_id}");
            }
        }

        Gateway::sendToGroup($room_id, json_encode(array('type'=>'right.removeAdminer', 'user_id'=>$managed_user_id,'vip'=>$_SESSION['vip'],'levelid'=>$_SESSION['levelid'])));
        if ($self) {
          unset($_SESSION['role']);
        } else {
          foreach ($managed_client_id_array as $managed_client_id) {
            $session = \GatewayWorker\Lib\Gateway::getSession($managed_client_id);
            if($session) {
                unset($session['role']);
                \GatewayWorker\Lib\Gateway::setSession($managed_client_id, $session);
            }
          }
        }
        return RoomStatus::removeAdminer($room_id, $managed_user_id);
  }

  public static function addKicked($room_id, $managed_user_id, $managed_client_id_array, $managed_client_name)
  {
    // 踢人,让该用户退出房间，并广播至所有人
        RoomStatus::addKicked($room_id, $managed_user_id, time()+3600);
        foreach( $managed_client_id_array as $managed_client_id) {
          Gateway::sendToClient($managed_client_id, json_encode(array('type'=>'error.kicked','content'=>'您已被踢出房间 1 小时')));
          Gateway::closeClient($managed_client_id);
        }

        // $new_message = array(
        //     "type"=>"error",
        //     "kicked_client_id" => $managed_client_id,
        //     'kicked_client_name' => $managed_client_name,
        //     "content"=>"$managed_client_name 已被踢出房间",
        //     );
        // return Gateway::sendToGroup($room_id, json_encode($new_message));
  }

  public static function removeKicked($room_id, $managed_user_id, $managed_client_id_array, $managed_client_name)
  {
    return RoomStatus::removeKicked($room_id, $managed_user_id);
  }

  public static function disableMsg($room_id, $managed_user_id, $managed_client_id_array, $managed_client_name)
  {
    // 禁言，并广播至所有人
        $expire = time() + 300; 
        RoomStatus::disableMsg($room_id, $managed_user_id, $expire);
        $session_for_update = array(
            'disableMsg' => true,
            'expire'     => $expire
        );
        // Gateway::sendToClient($managed_client_id, json_encode(array("type"=>"error.disableMsg","content"=>"您已被管理员禁言")));

        foreach ($managed_client_id_array as $managed_client_id) {
            \GatewayWorker\Lib\Gateway::updateSession($managed_client_id, $session_for_update);
        }
                        
        $new_message = array(
            "type" => "sysmsg",
            "content" => "$managed_client_name 已被管理员禁言5分钟",
            );
        Gateway::sendToGroup($room_id, json_encode($new_message));
  }

  public static function enableMsg($room_id, $managed_user_id, $managed_client_id_array, $managed_client_name)
  {
    // 允许发言
        RoomStatus::enableMsg($room_id, $managed_user_id);

        foreach ($managed_client_id_array as $managed_client_id) {
            $session = \GatewayWorker\Lib\Gateway::getSession($managed_client_id);
            if($session){
                unset($session['disableMsg']);
                unset($session['expire']);
                \GatewayWorker\Lib\Gateway::setSession($managed_client_id, $session);
            }
        }
  }

  /**
   * 判断用户角色
   */
   public static function getClientRole($room_id, $user_id, &$expire = 0)
   {
        $role = null;
        //TODO 从MEM中获取当前房间信息
        $roomStatus = RoomStatus::getRoomStatus($room_id);
        $sysAdminer = RoomStatus::getSysAdminer();
        if (strcmp($user_id, $roomStatus['owner']) === 0) {
            return 'owner';
        }
        if (in_array($user_id, $sysAdminer)) {
          return 'sysAdminer';
        }
        if (is_null($roomStatus)) {
            return $role;
        }
        /*
        $roomStatus = array(
            'owner' => 0000,
            'adminer' => array(
                790=>790
                ),
            'kicked' => array(
                // 790=>1453909804,
                ),
            'disableMsg' => array(
                790 => 1453909804,
                ),
            );
            */
        else if (in_array($user_id, $roomStatus['adminer'])) {
            $role = 'adminer';
        } else if (isset($roomStatus['kicked'][$user_id])) {
            $role = 'kicked';
            $expire = $roomStatus['kicked'][$user_id];
      } else if (isset($roomStatus['disableMsg'][$user_id])) {
          $expire = $roomStatus['disableMsg'][$user_id];
            if ($expire >= time()) {
                $role = 'disableMsg';
            }
        }

        return $role;
   }

   public static function isEnableMsg()
   {
        if (isset($_SESSION['role']) && strcmp($_SESSION['role'], 'adminer') === 0) {
            return true;
        }
        if (isset($_SESSION['disableMsg'])) {
            if ($_SESSION['expire'] >= time()) {
                // 解禁发言时间未到
                return false;
            } else {
                // 禁言时间已过
                unset($_SESSION['disableMsg']);
                unset($_SESSION['expire']);
                return true;
            }
        }
        return true;
   }

   /**
   *是否从网页端登录，并且没有被踢出房间
   */
   public static function isLogged($user_id, $token)
   {
        if (!is_null($token) && !empty($token) && strcmp(RoomStatus::getUserSession($user_id, $token), $token) === 0) {
            return true;
        }
        return false;
   }

   public static function hasVipPrivilete($user_id)
   {
       $privilete = RoomStatus::getVipStatus($user_id);
      if (is_null($privilete)) {
        return 0;
      }
      if (isset($privilete['vip']) && ($privilete['vip'] == 1 || $privilete['vip'] == 2)) {
        return $privilete['vip'];
      }
      return 0;
   }
   /**
   *是否能管理该用户
   */
   public static function ableToManage($user_id, $room_id)
   {
        return RoomStatus::ableToManage($user_id, $room_id);
   }

   /**
   *获取房间管理员
   */
   public static function getRoomAdminer($room_id)
   {
        $ret = array();
        $roomStatus = RoomStatus::getRoomStatus($room_id);
        $ret['roomAdminer'] = isset($roomStatus['adminer']) ? $roomStatus['adminer'] : array();
        $ret['sysAdminer'] = RoomStatus::getSysAdminer();
        return $ret;
   }

   public static function checkUserRoleChanged($user_id, $room_id, $role = null)
   {
        if (!empty(RoomStatus::$changedSysStatus)) {
            if (isset(RoomStatus::$changedSysStatus['add'][$user_id])) {
                unset(RoomStatus::$changedSysStatus['add'][$user_id]);
                if (strcmp($role, 'sysAdminer') !== 0) {
                    return 'addSysAdminer';
                }
            }
            if (isset(RoomStatus::$changedSysStatus['remove'][$user_id])) {
                unset(RoomStatus::$changedSysStatus['remove'][$user_id]);
                if (strcmp($role, 'sysAdminer') === 0) {
                    return 'removeSysAdminer';
                }
            }
        }

        if (!empty(RoomStatus::$changedRoomStatus) && isset(RoomStatus::$changedRoomStatus[$room_id])) {
            if (isset(RoomStatus::$changedRoomStatus[$room_id]['add'][$user_id])) {
                unset(RoomStatus::$changedRoomStatus[$room_id]['add'][$user_id]);
                if (!$role) {
                    return 'adminer';
                }
            }
            if (isset(RoomStatus::$changedRoomStatus[$room_id]['remove'][$user_id])) {
                unset(RoomStatus::$changedRoomStatus[$room_id]['remove'][$user_id]);
                if (strcmp($role, 'adminer') === 0) {
                    return 'removeAdminer';
                }
            }
        }
        return null;
   }

   /**
   * 用户是否在当前房间
   * @param int $user_id
   * @param int $room_id
   * @return bool
   */
   public static function inCurrentRoom($user_id, $room_id)
   {
       return false;
      $clientList = Gateway::getClientInfoByGroup($room_id);
      foreach($clientList as $client) {
        if (!empty($client) && $client['user_id'] == $user_id) {
          return true;
        }
      }
      return false;
   }

   /**
   * 是否可以发私信
   */
   public static function ablePriMsg()
   {
      if (isset($_SESSION['role']) && in_array($_SESSION['role'],array('owner','adminer','sysAdminer'))) {
        return true;
      }
      if ($_SESSION['levelid'] <= 2) {
        return false;
      }
      return true;
   }

   /**
   * 限制发消息频率
   */
   public static function msgFrequency($length, &$msg)
   {
        $time = time();
        if (isset($_SESSION['role']) && in_array($_SESSION['role'],array('adminer','sysAdminer','owner'))) {
        $msg = "房主/管理员每" . static::$frequency[100]['time'] . "秒发一次消息，每条消息不得超过" . static::$frequency[100]['length'] . "字";
        if ($_SESSION['msged']) {
          if ($time - $_SESSION['time'] < static::$frequency[100]['time']) {
            return false;
          } else if ($length > static::$frequency[100]['length']) {
            return false;
          }
          $_SESSION['time'] = $time;
          return true;
        } else if ($length > static::$frequency[100]['length']) {
          $_SESSION['msged'] = true;
          return false;
        }
        $_SESSION['msged'] = true;
        return true;
        }
        // 富豪等级为29以上的，频率限制与主播相同
        $level_id = $_SESSION['levelid'];
        if ($level_id >= 29 || !isset(static::$frequency[$level_id])) {
          $level_id = 100;
        }

        $msg = "富豪等级" . $level_id . "每" . static::$frequency[$level_id]['time'] . "秒发一次消息，每条消息不得超过" . static::$frequency[$level_id]['length'] . "字";
        if ($_SESSION['msged']) {
          if ($time - $_SESSION['time'] < static::$frequency[$level_id]['time']) {
            return false;
          } else if ($length > static::$frequency[$level_id]['length']) {
            return false;
          }
          $_SESSION['time'] = $time;
          return true;
        } else if ($length > static::$frequency[$level_id]['length']) {
          $_SESSION['msged'] = true;
          return false;
        }
        $_SESSION['msged'] = true;
        return true;
   }

   // 判断房间是否满员
   public static function isRoomFull($room_id)
   {
      return false;
      $status = RoomStatus::getRoomStatus($room_id);
      var_dump($status);
      return $status['maxonline'] == 0;
   }

   // 获取房间在线在线人数
   public static function getClientNumByRoom($room_id)
   {
      $num = RoomStatus::getClientNum($room_id);
      return !$num ? 0 : $num;
   }

   public static function getSortedClientByRoom($room_id)
   {
      $sortedClient = RoomStatus::getSortedClient($room_id);
      return is_null($sortedClient) ? array() : $sortedClient;
   }

   public static function setAnchorOffLine($user_id) {
      return ;
      RoomStatus::updateAnchorOfflineToDb($user_id);
   }

   public static function setAnchorOnlie($user_id)
   {
      RoomStatus::updateAnchorOnLineToDb($user_id);
   }

   public static function getAvatar($uid)
   {

      return "/style/avatar/".substr(md5($uid),0,3)."/{$uid}_small.jpg?t=".time();
      /*
      $base = "/passport/avatar.php?uid={$ucuid}&size=small";
      $hash = self::getFileHash($ucuid);
      return $hash ? $base . "&hash={$hash}" : $base;
      */
   }

   /**
   * 获取上传文件路径
   */
   public static function getUploadPath($uid)
   {
      $uid = abs(intval($uid));
      $uid = sprintf("%09d", $uid);
      $dir1 = substr($uid, 0, 3);
      $dir2 = substr($uid, 3, 2);
      $dir3 = substr($uid, 5, 2);
      return "/passport/data/avatar/".$dir1.'/'.$dir2.'/'.$dir3.'/';
  }

  public static function getFileHash($uid)
  {
    $path = Site::$host . self::getUploadPath($ucuid) . substr($ucuid, -2) . '_avatar_small.jpg';
    if (@fopen($path, 'r')) {
        return hash_file('md5', $path);
    }
    return '';
  }

  /**
  * 延迟关闭客户端连接，照顾低配置手机来不及响应问题
  *
  * @param int $time
  * @param string $client_id
  */
  public static function delayCloseClient($time, $client_id)
  {
     \Workerman\Lib\Timer::add($time,function($client_id) {
        Gateway::closeClient($client_id); 
      }, array($client_id), false);
  }

  /**
  * 发系统消息
  *
  * @param $client_id 
  * @param string $lang  语言
  */
  public static function sendSysMsg($client_id = '', $lang = 'US-en')
  {
   
      if(strpos($lang,'zh-Hans') !== false) {
        $lang = 'CN-zh';
      }

      if(strpos($lang,'zh-Hant') !== false) {
        $lang = 'TW-zh';
      }

      $Data = Db::instance('dbDefault')->query("select live_prompt from ss_siteconfig");
      $SysMsg = $Data[0]['live_prompt'];
      if ($SysMsg == '') {
        return ;
      }
      /*
      if (AI::$SysMsg == '') {
        return ;
      }

      $message = array('type'=>'sysmsg', 'content'=>AI::$SysMsg);*/
      $SysMsg = !isset(AI::$SysMsgAother[$lang]) ? $SysMsg : AI::$SysMsgAother[$lang];
      $message = array('type'=>'sysmsg', 'content'=>$SysMsg);
      if ($client_id == '') {
        Gateway::sendToCurrentClient(json_encode($message));
        return ;
      }
      Gateway::sendToClient($client_id, json_encode($message));
  }

  public static function filter($content)
  {
      $keyWord = Db::instance('dbDefault')->query("select key_word from ss_keyword");
      $str = $content;
      /*if (!empty(AI::$keyWord)) {
        foreach (AI::$keyWord as $word) {
          $content = str_replace($word, str_repeat("*", strlen($word)), $content);
        }
      }*/
      if (!empty($keyWord)) {
        foreach ($keyWord as $word) {
          $content = str_replace($word['key_word'], str_repeat("*", strlen($word['key_word'])), $content);
        }
      }
      return $content;
  }

  public function endBroadcast($uid)
  {
    $id = Db::instance('dbDefault')->query("select id from ss_backstream where uid={$uid} order by id desc limit 1");
    $id = $id[0]['id'];
    $time = time();
    Db::instance('dbDefault')->query("update ss_backstream set endtime={$time} where id={$id}");
  }
}

<?php
namespace Application;

use \GatewayWorker\Lib\Mem;
use \GatewayWorker\Lib\Db;

class RoomStatus {

	const SYS_ADMINER_CHANGE = 'SYS_CHANGED_STATUS';
	const ROOM_ADMINER_CHANGE = 'ROOM_CHANGED_STATUS';

	// 在线房间人数
	const ROOM_ONLINE_NUM_PREFIX = "ROOM_ONLINE_NUM_";
	// 房间在线用户等级排序列表
	const ROOM_SORTED_CLIENTS_PREFIX = "ROOM_SORTED_CLIENTS_";

	/**
	* VIP status
	*/
	const MEILIBO_USER_VIP = 'MEILIBO_USER_VIP_';
	/**
	*系统管理员 key 前缀
	*/
	const PHPCHAT_SYS_KEY = 'SYS_ADMINER_KEY';

	/**
	*房间信息key前缀
	*/
	const ROOM_STATUS_PREFIX = 'PHPCHAT_ROOM_';

	/**
	*SESSION 前缀
	*/
	const USER_SESSION_PREFIX = 'PHPCHAT_SESSION_';

	/**
	*系统管理员
	*/
	const INIT_SYS_ADMINER_SQL = 'select id from ss_member where showadmin=1';

	/**
	*房间号 SQL
	*/
	const INIT_ROOM_OWNMER_SQL = 'select id as uid,curroomnum as num,maxonline from ss_member'; //select uid,num from ss_roomnum

	/*
	*房间管理员 SQL
	*/
	const INIT_ROOM_ADMINER_SQL = 'select curroomnum as num,ss_roomadmin.adminuid from ss_member,ss_roomadmin where ss_member.id=ss_roomadmin.uid';

	/**
	* 获取所有VIP会员
	*/
	const INIT_VIP_STATUS_SQL = 'select id,vipexpire from ss_member where vip in (1,2)';

	// 获取在线主播列表 从数据库查，可能要着起
	const GET_ONLINE_ANCHOR_LIST = "select curroomnum from ss_member where broadcasting='y'";

	/**
	*系统管理员状态更改
	*/
	public static $changedSysStatus = array();

	/**
	*房间管理员状态更改
	*/
	public static $changedRoomStatus = array();

	/**
  	*添加系统管理员
  	*/
  	public static function addSysAdminer($user_id)
  	{
    	$sysAdminer = self::getSysAdminer();
      	if (!isset($sysAdminer[$user_id])) {
        	$sysAdminer[$user_id] = $user_id;
        	self::setSysAdminer($sysAdminer);
      	}
      	$tmp = json_decode(Mem::get(static::SYS_ADMINER_CHANGE), true);
      	if (!is_null($tmp)) {
      		unset($tmp['add'][$user_id]);
      		Mem::set(static::SYS_ADMINER_CHANGE, json_encode($tmp));
      	}
  	}

  	/**
  	*删除系统管理员
  	*/
  	public static function removeSysAdminer($user_id)
  	{
      	$sysAdminer = self::getSysAdminer();
      	if (isset($sysAdminer[$user_id])) {
        	unset($sysAdminer[$user_id]);
        	self::setSysAdminer($sysAdminer);
    	}
    	$tmp = json_decode(Mem::get(static::SYS_ADMINER_CHANGE), true);
      	if (!is_null($tmp)) {
      		unset($tmp['remove'][$user_id]);
      		Mem::set(static::SYS_ADMINER_CHANGE, json_encode($tmp));
      	}
  	}

	public static function reloadChangedStatus() {
		$sysStatus = json_decode(Mem::get(static::SYS_ADMINER_CHANGE), true);

		if (!is_null($sysStatus) && !empty($sysStatus)) {
			self::$changedSysStatus = $sysStatus + self::$changedSysStatus;
		}

		$roomStatus = json_decode(Mem::get(static::ROOM_ADMINER_CHANGE), true);
		if (!is_null($roomStatus) && !empty($roomStatus)) {
			self::$changedRoomStatus = $roomStatus + self::$changedRoomStatus;
		}
	}
	public static function init()
	{
		self::initRoomStatus();
	}
	public static function addAdminer($room_id, $user_id)
	{
		$roomStatus = self::getRoomStatus($room_id);
		$roomStatus['adminer'][$user_id] = $user_id;
		self::setRoomStatus($room_id, $roomStatus);

		$tmp = json_decode(Mem::get(static::ROOM_ADMINER_CHANGE), true);
      	if (!is_null($tmp)) {
      		unset($tmp[$room_id]['add'][$user_id]);
      		Mem::set(static::ROOM_ADMINER_CHANGE, json_encode($tmp));
      	}
      	return;
	}
	public static function removeAdminer($room_id, $user_id)
	{
		$roomStatus = self::getRoomStatus($room_id);
		unset($roomStatus['adminer'][$user_id]);
		self::setRoomStatus($room_id, $roomStatus);

		$tmp = json_decode(Mem::get(static::ROOM_ADMINER_CHANGE), true);
      	if (!is_null($tmp)) {
      		unset($tmp[$room_id]['remove'][$user_id]);
      		Mem::set(static::ROOM_ADMINER_CHANGE, json_encode($tmp));
      	}
      	return;
	}
	public static function addKicked($room_id, $user_id, $expire)
	{
		$roomStatus = self::getRoomStatus($room_id);
		$roomStatus['kicked'][$user_id] = $expire;
		return self::setRoomStatus($room_id, $roomStatus);
	}
	public static function removeKicked($room_id, $user_id)
	{
		$roomStatus = self::getRoomStatus($room_id);
		unset($roomStatus['kicked'][$user_id]);
		return self::setRoomStatus($room_id, $roomStatus);
	}
	public static function disableMsg($room_id, $user_id, $expire)
	{
		$roomStatus = self::getRoomStatus($room_id);
		$roomStatus['disableMsg'][$user_id] = $expire;
		return self::setRoomStatus($room_id, $roomStatus);
	}
	public static function enableMsg($room_id, $user_id)
	{
		$roomStatus = self::getRoomStatus($room_id);
		unset($roomStatus['disableMsg'][$user_id]);
		return self::setRoomStatus($room_id, $roomStatus);
	}


	public static function getRoomStatus($room_id)
	{
		return json_decode(Mem::get(static::ROOM_STATUS_PREFIX.$room_id),true);
	}
	public static function setRoomStatus($room_id, $value)
	{
		Mem::delete(static::ROOM_STATUS_PREFIX.$room_id);
		return Mem::set(static::ROOM_STATUS_PREFIX.$room_id, json_encode($value),0,0);
	}
	public static function getSysAdminer()
	{
		$ret = json_decode(Mem::get(static::PHPCHAT_SYS_KEY),true);
		if (is_null($ret)) {
			$ret = array();
		}
		return $ret;
	}
	public static function setSysAdminer($value)
	{
		Mem::delete(static::PHPCHAT_SYS_KEY);
		return Mem::set(static::PHPCHAT_SYS_KEY, json_encode($value));
	}

	public static function ableToManage($user_id, $room_id)
	{
		$sysadminer = self::getSysAdminer();

		// 该用户为系统管理员
		if(in_array($user_id, $sysadminer)) {
			return false;
		}

		$roomStatus = self::getRoomStatus($room_id);
		if (is_null($roomStatus)) {
			// 房间未初始化，或者不存在
			return false;
		}
		// 该用户为房间所有者
		if (strcmp($user_id, $roomStatus['owner']) === 0) {
			return false;
		}

		if (in_array($user_id, $roomStatus['adminer'])) {
			if (in_array($_SESSION['role'], array('owner','sysAdminer'))) {
				return true;
			}
			return false;
		}
		return true;
	}
	/*
	public static function setUserSession($key, $value)
	{
		return Mem::set(static::USER_SESSION_PREFIX.$key, $value, 0, 2500000);
	}
	*/
	public static function getUserSession($user_id, $token)
	{
		return Mem::get(static::USER_SESSION_PREFIX.$user_id.$token);
	}


	/**
	* Get vip status
	*/
	public static function getVipStatus($user_id)
	{
		return json_decode(Mem::get(static::MEILIBO_USER_VIP.$user_id), true);
	}

	public static function initRoomStatus()
	{
		$memToRoomStatus = array();
		$memToSysAdminer = array();
		$status = array();

		$status = Db::instance('dbDefault')->query(static::INIT_SYS_ADMINER_SQL);
		foreach ($status as $uid) {
			$memToSysAdminer[$uid['id']] = $uid['id'];
		}
		self::setSysAdminer($memToSysAdminer);

 		//初始化房间所有者
		$status = Db::instance('dbDefault')->query(static::INIT_ROOM_OWNMER_SQL);
		foreach ($status as $value) {
			if(!isset($memToRoomStatus[$value['num']])) {
				$memToRoomStatus[$value['num']] = array();
			}
			$memToRoomStatus[$value['num']]['owner'] = $value['uid'];
			$memToRoomStatus[$value['num']]['adminer'] = array();
			$memToRoomStatus[$value['num']]['disableMsg'] = array();
			$memToRoomStatus[$value['num']]['kicked'] = array();
			$memToRoomStatus[$value['num']]['maxonline'] = $value['maxonline'];
		}

		//初始化管理员
		$status = array();
		$status = Db::instance('dbDefault')->query(static::INIT_ROOM_ADMINER_SQL);
		foreach ($status as $value) {
			if (!isset($memToRoomStatus[$value['num']])) {
				continue;
			}
			$memToRoomStatus[$value['num']]['adminer'][$value['adminuid']] = $value['adminuid'];
		}

		Db::close('dbDefault');

		foreach ($memToRoomStatus as $room_id => $roomStatus) {
			self::setRoomStatus($room_id, $roomStatus);
		}
	}

	// 获取房间在在线人数
	public static function getClientNum($room_id)
	{
		return Mem::get(static::ROOM_ONLINE_NUM_PREFIX.$room_id);
	}

	// 获取房间在线列表
	public static function getSortedClient($room_id)
	{
		return json_decode(Mem::get(static::ROOM_SORTED_CLIENTS_PREFIX.$room_id), true);
	}

	public static function updateAnchorOfflineToDb($user_id) {
		return ;
		// Db::instance('dbDefault')->update('ss_member')->set('broadcasting','n')->where(array('id'=>$user_id))->execute();
		Db::instance('dbDefault')->query("update ss_member set broadcasting='n' where id={$user_id}");
	}

	public static function updateAnchorOnLineToDb($user_id)
	{		
		Db::instance('dbDefault')->query("update ss_member set broadcasting='y' where id={$user_id}");
	}

	public static function getOnlineRoom()
	{
		return Db::instance('dbDefault')->query(static::GET_ONLINE_ANCHOR_LIST);
	}

	/**
	* 更新在线人数
	*
	* @param int $num
	*/
	public static function updateOnlineNumToDb($room_id, $num = 0)
	{
		return Db::instance('dbDefault')->query("update ss_member set onlinenum={$num} where curroomnum={$room_id}");
	}
}
<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
*可能存在的问题
* sendToClient 可能发送给不是当前房间的用户
*/
/**
 * 聊天主逻辑
 * 主要是处理 onMessage onClose
 */
use \GatewayWorker\Lib\Gateway;
use \GatewayWorker\Lib\Store;
use \GatewayWorker\Lib\Memcache;
use \Application\AdminClient;
use \Application\Crontab;
use \Application\PrivateRoom;

class Events

{
   /**
    * 有消息时
    * @param int $client_id
    * @param string $message
    */
   public static function onMessage($client_id, $message)
   {
        // debug
        //echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']}  client_id:$client_id session:".json_encode($_SESSION)." onMessage:".$message."\n";

        // 客户端传递的是json数据
        $message_data = json_decode($message, true);
        if(!$message_data)
        {
            return ;
        }

        $method = $message_data['_method_'];

        $_SESSION = Gateway::getSession($client_id);
        if (empty($_SESSION) && !in_array($method, array('login','BindUid'))) {
            return ;
        }

        if (!in_array($method, array('login','BindUid'))) {
            if ((!isset($_SESSION['user_id']) || $_SESSION['user_id'] == -1) && in_array($method, array('SendPubMsg','SendPrvMsg','sendGift','Manage'))) {
                Gateway::sendToCurrentClient(json_encode(array('type'=>'sysmsg.alert','content'=>'请登录房间！')));
                return ;
            }
            // 查看当前用户权限是否修改
            $role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
            $action = AdminClient::checkUserRoleChanged($_SESSION['user_id'], $_SESSION['room_id'], $role);
            if (!is_null($action)) {
                if (in_array($action, array('addSysAdminer','removeSysAdminer'))) {
                    AdminClient::$action($_SESSION['room_id'], $_SESSION['user_id']);
                } else if (in_array($action, array('adminer','removeAdminer'))) {
                    AdminClient::$action($_SESSION['room_id'], $_SESSION['user_id'], $client_id, $_SESSION['client_name'], true, false);
                }
            }
        }


        // 根据类型执行不同的业务
        switch($method)
        {
            // 客户端回应服务端的心跳
            case 'pong':
                if (isset($_SESSION['room_id']) &&  isset($_SESSION['role'])  && strcmp($_SESSION['role'], 'owner') === 0) { //isset($message_data['device'])  && strcmp($message_data['device'], 'android')
                    Crontab::setAnchorOnline($_SESSION['room_id'], $_SESSION['user_id']);
                }
                return;
            // 客户端登录 message格式: {_method_ :login, name:xx, room_id:1} ，添加到客户端，广播给所有客户端xx进入聊天室
            case 'login':
            case 'logout':
                // ==== 如果是login操作，首先执行logout再执行login逻辑，避免串消息======
                // 从房间的客户端列表中删除
                if(isset($_SESSION['room_id']))
                {
                   $room_id = $_SESSION['room_id'];
                   if (isset($_SESSION['role']) && strcmp($_SESSION['role'], 'owner') === 0) {
                       $new_message = array('type'=>'logout', 'from_client_id'=>$client_id, 'user_id'=>$_SESSION['user_id'], 'from_client_name'=>$_SESSION['client_name'], 'time'=>date('Y-m-d H:i:s'));
                       $new_message['role'] = 'owner';
                       Gateway::sendToGroup($room_id, json_encode($new_message));
                   }

                   // ==== logout不再解绑uid，有些消息可以是非直播间发的，例如禁用用户消息 ====
                   //Gateway::unbindUid($client_id, $_SESSION['user_id']);
                   Gateway::leaveGroup($client_id, $room_id);

                   $_SESSION = array();
                }
                // 如果是logout，退出
                if ($method == 'logout')
                {
                   return;
                }
                // ====== logout 分支 结束=======
                // ====== 以下 login 分支 =======
                // 判断是否有房间号
                if(!isset($message_data['room_id']))
                {
                    return;
                }
                $viewer = false;
                if (!isset($message_data['user_name']) || !isset($message_data['user_id']) || $message_data['user_id'] <= 0 || $message_data['user_name'] == "") {
                    $viewer = true;
                    $message_data['user_name'] = '游客'.rand(10000,99999);
                    $message_data['user_id'] = -1;
                    $message_data['levelid'] = 0;
                    $message_data['ucuid'] = -1;
                    $message_data['vip'] = 0;
                }
                $client_name = htmlspecialchars($message_data['user_name']);
                $room_id = $message_data['room_id'];
                $user_id = $message_data['user_id'];

                if (!$viewer) {
                    if (!AdminClient::isLogged($user_id, $message_data['token'])) {
                    // close this connection
                        Gateway::sendToCurrentClient(json_encode(array('type'=>'error' , 'content'=>'没有登录')));
                        Gateway::closeClient($client_id);
                        return;
                    }
                }

                $vip = $viewer ? 0 : AdminClient::hasVipPrivilete($user_id);

                if ( $vip == 0 && AdminClient::isRoomFull($room_id)) {
                    Gateway::sendToCurrentClient(json_encode(array('type'=>'error.kicked','content'=>"非VIP用户不能进入满员房间～")));
                    // 延迟关闭连接
                    //AdminClient::delayCloseClient(5,$client_id);
                    // Gateway::closeClient($client_id);
                    return;
                }

                $expire = 0;
                $role = $viewer ? '' : AdminClient::getClientRole($room_id, $user_id, $expire);
                if (!$vip &&strcmp($role, 'kicked') === 0 && $expire > time()) {

                    //TODO 关闭连接 已被踢出房间
                    $left = floor(($expire - time()) / 60);
                    Gateway::sendToCurrentClient(json_encode(array('type'=>'error.kicked','content'=>"您已被踢出房间, {$left}分钟后再进入房间")));
                    // 延迟关闭连接
                    //AdminClient::delayCloseClient(5,$client_id);
                    // Gateway::closeClient($client_id);
                    return;
                }
                // 把房间号昵称放到session中
                $isAdminer = false;
                if (!is_null($role) && (strcmp($role, 'adminer') === 0 || strcmp($role, 'owner') === 0) || strcmp($role, 'sysAdminer') === 0) {
                    $_SESSION['role'] = $role;
                    if (strcmp($role, 'owner') === 0) {
                        AdminClient::setAnchorOnlie($user_id);
                    }
                    $isAdminer = true;
                } else if (!$vip && !is_null($role) && strcmp($role, 'disableMsg') === 0) {
                    $_SESSION['disableMsg'] = 1;
                    $_SESSION['expire'] = $expire;
                }

                $ucuid = $message_data['ucuid'];
                $_SESSION['client_name'] = $client_name;
                $_SESSION['user_id'] = $user_id;
                $_SESSION['room_id'] = $room_id;
                $_SESSION['ucuid'] = $ucuid;
                $_SESSION['client_id'] = $client_id;
                $_SESSION['vip'] = $vip;
                $_SESSION['levelid'] = $message_data['levelid'];
                $_SESSION['time'] = 0;
                $_SESSION['msged'] = false;


                $new_message = array('type'=>$method, 'client_id'=>$client_id, 'client_name'=>htmlspecialchars($client_name), 'user_id'=>$user_id, 'ucuid'=>$ucuid,'vip'=>$vip,'levelid'=>$_SESSION['levelid'], 'time'=>date('H:i'));
                $new_message['levelid'] = $message_data['levelid'];
                if ($isAdminer) {
                    $new_message['role'] = 'adminer';
                }
                if (isset($message_data['daoju'])) {
                    $new_message['daoju'] = $message_data['daoju'];
                }

                // if ((strcmp($role, 'owner') !== 0)) {
                //     Gateway::sendToGroup($room_id, json_encode($new_message));
                // }
                Gateway::joinGroup($client_id, $room_id);
                Gateway::bindUid($client_id, $user_id);
                AdminClient::sendSysMsg($client_id, !isset($message_data['lang']) ? 'ch' : $message_data['lang']);

                // For APP
                Crontab::sendOnLineList($room_id, $_SESSION);

                // 2017-04-12 $user_id是-1的是游客，不广播游客xxx进入聊天室
                if ($user_id != -1) {
                    Gateway::sendToGroup($room_id, json_encode($new_message));
                }
                return;
            case 'BindUid':
                Gateway::bindUid($client_id, $message_data['user_id']);
                return;
            // 客户端发言 message: {_method_:SendPubMsg, to_client_id:xx, content:xx}
            case 'LightHeart':
                if(!isset($_SESSION['room_id']))
                {
                    return;
                }
                $new_message = array(
                    'type'=>'LightHeart',
                    'color'=> isset($message_data['color']) ? $message_data['color'] : 'red',
                    'client_id'=>$client_id,
                    'user_id'=>$_SESSION['user_id'],
                    'client_name'=>$_SESSION['client_name'],
                    'levelid'=>$_SESSION['levelid'],
                    'vip'=>$_SESSION['vip'],
                    'time'=>date('H:i'),
                    );

                return Gateway::sendToGroup($_SESSION['room_id'], json_encode($new_message));
            case 'SendPubMsg':
                // 非法请求
                if(!isset($_SESSION['room_id']))
                {
                    return;
                }
                if (trim($message_data['content']) == "") {
                    Gateway::sendToClient($client_id, json_encode(array("type"=>"error","content"=>"您不能发送空内容")));
                    return ;
                }
                // 是否可以发言
                if (!AdminClient::isEnableMsg()) {
                    Gateway::sendToCurrentClient(json_encode(array("type"=>"error","content"=>"您已被管理员禁言")));
                    return ;
                }
                /*
                if(isset($_SESSION['levelid']) && ($_SESSION['levelid'] < 5)) {
                    Gateway::sendToCurrentClient(json_encode(array("type"=>"error","content"=>"您用户等级过低，无法发送直播间消息")));
                    return ;
                }
                */
                $msg = '';
                if (!AdminClient::msgFrequency(strlen($message_data['content']), $msg)) {
                    Gateway::sendToCurrentClient(json_encode(array("type"=>"error","content"=>$msg)));
                    return ;
                }
                $room_id = $_SESSION['room_id'];
                $client_name = $_SESSION['client_name'];


                $new_message = array(
                    'type'=>'SendPubMsg',
                    'from_user_id'=>$_SESSION['user_id'],
                    'from_client_name' =>$client_name,
                    'vip'=>$_SESSION['vip'],
                    'levelid'=>$_SESSION['levelid'],
                    'avatar'=>AdminClient::getAvatar($_SESSION['user_id']),
                    // 'to_client_id'=>'all',
                    'content'=>nl2br(htmlspecialchars(AdminClient::filter($message_data['content']))),
                    'time'=>date('H:i'),
                );
                // 飞屏特效
                $new_message['fly'] = isset($message_data['fly']) ? $message_data['fly'] : '';

                return Gateway::sendToGroup($room_id ,json_encode($new_message));
            case 'SendPrvMsg':
                // 非法请求
                if(!isset($_SESSION['room_id']))
                {
                    return;
                }
                if (trim($message_data['content']) == "") {
                    Gateway::sendToClient($client_id, json_encode(array("type"=>"error","content"=>"您不能发送空内容")));
                    return ;
                }
                // 是否可以发言
                if (!AdminClient::isEnableMsg()) {
                    Gateway::sendToClient($client_id, json_encode(array("type"=>"error","content"=>"您已被管理员禁言")));
                    return ;
                }
                if (!AdminClient::ablePriMsg()){
                    Gateway::sendToClient($client_id, json_encode(array("type"=>"error","content"=>"富豪等级3以下不能发私信，快快升级吧")));
                    return ;
                }
                $msg = '';
                if (!AdminClient::msgFrequency(strlen($message_data['content']), $msg)) {
                    Gateway::sendToCurrentClient(json_encode(array("type"=>"error","content"=>$msg)));
                    return ;
                }

                $room_id = $_SESSION['room_id'];
                $client_name = $_SESSION['client_name'];

                // 私聊
                $new_message = array(
                    'type'=>'SendPrvMsg',
                    'from_user_id'=>$_SESSION['user_id'],
                    'from_client_name' =>$client_name,
                    'vip'=>$_SESSION['vip'],
                    'levelid'=>$_SESSION['levelid'],
                    'avatar'=> AdminClient::getAvatar($_SESSION['user_id']),
                    'to_client_name'=>$message_data['to_client_name'],
                    'to_user_id'=>isset($message_data['to_user_id']) ? $message_data['to_user_id'] : 0,
                    'pub'=>$message_data['pub'],
                    'content'=>nl2br(htmlspecialchars(AdminClient::filter($message_data['content']))),
                    'time'=>date('H:i'),
                );
                // 飞屏特效
                // $new_message['fly'] = isset($message_data['fly']) ? $message_data['fly'] : '';

                //可公开的私聊
                if ($message_data['pub']) {
                    return Gateway::sendToGroup($room_id ,json_encode($new_message));
                } else {
                    //私密聊天，不公开
                    Gateway::sendToClient($message_data['to_client_id'], json_encode($new_message));
                    // $new_message['content'] = nl2br(htmlspecialchars($message_data['content']));
                    return Gateway::sendToCurrentClient(json_encode($new_message));
                }
            case 'vodSong':
                if (!isset($message_data['songName'])) {
                    Gateway::sendToCurrentClient(json_encode(array('type'=>'error','content'=>'请填写歌曲！')));
                    return ;
                }
                if ($_SESSION['user_id'] == -1) {
                    Gateway::sendToCurrentClient(json_encode(array('type'=>'error','content'=>'游客不能点歌！')));
                    return ;
                }
                $new_message = array('type'=>'vodSong');
                return Gateway::sendToGroup($_SESSION['room_id'] ,json_encode($new_message));
            case 'setBackground':
            case 'cancelBackground':
                $error = "";
                $new_message = array();
                if (!isset($_SESSION['role']) || !in_array($_SESSION['role'],array('owner','sysAdminer'))) {
                    $error = "您不是房主或系统管理员";
                }
                if ( strcmp($method,'setBackground') == 0 && (!isset($message_data['bgimg']) || $message_data['bgimg'] == "")) {
                    $error = "参数错误，请稍后尝试...";
                }
                if ($error != "") {
                    return Gateway::sendToCurrentClient(json_encode(array('type'=>'error','content'=>$error)));
                }
                return Gateway::sendToGroup($_SESSION['room_id'], json_encode(array('type'=>$method,'bgimg'=>$message_data['bgimg'])));
            case 'setSongApply':
                if (!isset($_SESSION['role']) || strcmp($_SESSION['role'],'owner') != 0) {
                    return Gateway::sendToCurrentClient(json_encode(array('type'=>'error','content'=>'您不是房主哦！')));
                }
                if (!isset($message_data['apply'])) {
                    return Gateway::sendToCurrentClient(json_encode(array('type'=>'error','content'=>'参数错误')));
                }
                return Gateway::sendToGroup($_SESSION['room_id'],json_encode(array('type'=>'setSongApply','apply'=>$message_data['apply'])));
            case 'disAgreeSong':
            case 'agreeSong':
                if (!isset($_SESSION['role']) || strcmp($_SESSION['role'], 'owner') !== 0) {
                    // 当前用户不在该房间
                    Gateway::sendToCurrentClient(json_encode(array('type'=>'error','content'=>'您不是房主哦！')));
                    return ;
                }
                $new_message = array('type'=>$method);
                if ($method == 'agreeSong') {
                    $new_message['client_name'] = $message_data['userName'];
                    $new_message['songName'] = $message_data['songName'];
                }
                return Gateway::sendToGroup($_SESSION['room_id'],json_encode($new_message));
            case 'sendGift':
                // 此接口已废弃
                // Gateway::sendToCurrentClient(json_encode(array('type'=>'error','content'=>'此接口已废弃')));
                // return ;

                // 非法请求
                if(!isset($_SESSION['room_id']))
                {
                    return;
                }
                $room_id = $_SESSION['room_id'];
                $client_name = $_SESSION['client_name'];

                // 防止别人冒名发消息
                if (strcmp($client_name, $message_data['userName']) !== 0) {
                    Gateway::sendToCurrentClient(json_encode(array('type'=>'error','content'=>'不能冒名发送礼物')));
                    return ;
                }

                unset($message_data['_method_']);
                unset($message_data['userName']);
                $message_data['from_client_name'] = $client_name;
                $message_data['type'] = 'sendGift';
                $message_data['code'] = 0;
                $message_data['time'] = date('H:i');
                $new_message = $message_data;
                $new_message['from_client_id'] = $client_id;
                $new_message['from_user_id'] = $_SESSION['user_id'];
                $new_message['vip'] = $_SESSION['vip'];
                $new_message['levelid'] = $_SESSION['levelid'];

                return Gateway::sendToGroup($room_id ,json_encode($new_message));
            case 'Manage':
                // 非法请求
                if(!isset($_SESSION['room_id']))
                {
                    return;
                }
                // 是否有管理员权限
                if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], array('adminer','owner','sysAdminer'))) {
                    Gateway::sendToCurrentClient(json_encode(array('type'=>'error','content'=>'您没有管理员权限')));
                    return;
                }

                $room_id = $_SESSION['room_id'];
                $client_name = $_SESSION['client_name'];

                $managed_user_id = $message_data['managed_user_id'];
                // $managed_client_id = $message_data['managed_client_id'];
                // $uidToClientArray = Gateway::getClientIdByUid($managed_user_id);
                // $managed_client_id = array_shift($uidToClientArray);
                $managed_client_id_array = Gateway::getClientIdByUid($managed_user_id);
                $managed_user_name = $message_data['managed_user_name'];
                $type = $message_data['_type_'];
                if ( $managed_user_id == -1 && in_array($type, array('adminer','removeAdminer'))) {
                    Gateway::sendToCurrentClient(json_encode(array('type'=>'sysmsg.alert','content'=>'无法对游客做此操作！')));
                    return ;
                }

                // if (!Gateway::isUidOnline($managed_user_id)) {
                //     // 当前用户不在该房间
                //     Gateway::sendToCurrentClient(json_encode(array('type'=>'error','content'=>'当前用户不在该房间')));
                //     return ;
                // }

                if (in_array($type, array('adminer','removeAdminer','addKicked','removeKicked','disableMsg','enableMsg'))) {
                    if ( $managed_user_id == -1 && in_array($type, array(array('adminer','removeAdminer')))) {
                        Gateway::sendToCurrentClient(json_encode(array('type'=>'sysmsg.alert','content'=>'无法对游客做此操作！')));
                        return ;
                    }
                    // 查看VIP状态
                    if (in_array($type, array('addKicked','disableMsg')) && AdminClient::hasVipPrivilete($managed_user_id)) {
                        Gateway::sendToCurrentClient(json_encode(array('type'=>'sysmsg.alert','content'=>'该用户为 VIP 会员，操作失败！')));
                        return ;
                    }
                    if (!AdminClient::ableToManage($managed_user_id, $room_id)) {
                        Gateway::sendToCurrentClient(json_encode(array('type'=>'error','content'=>'无法操作管理员')));
                        return;
                    }
                    Gateway::sendToCurrentClient(json_encode(array('type'=>'sysmsg.alert','content'=>'操作成功！')));
                    AdminClient::$type($room_id, $managed_user_id, $managed_client_id_array, $managed_user_name);
                }
            //切换到收费房间
            case 'chargeRoom':
                $user_id = $_SESSION['user_id'];
                $room_id = $_SESSION['room_id'];
                $message = array(
                    'type' => 'changeRoomNotice',
                    'content' => '本房间将在10秒后切换到计时收费模式',
                    'serverTime' => time(),
                );
                Gateway::sendToGroup($_SESSION['room_id'], json_encode($message));
                $Timer_id[$room_id] = \workerman\Lib\Timer::add(10, function()use($user_id,&$Timer_id,$room_id) {
                    $change_charge_room = PrivateRoom::changeChargeRoom($user_id);
                    if($change_charge_room == TRUE){
                        $message = array(
                            'type' => 'sysmsg.alert',
                            'content' => '切换房间成功！',
                            'serverTime' => time(),
                        );
                        Gateway::sendToGroup($room_id, json_encode($message));
                    }else{
                        $message = array(
                            'type' => 'error',
                            'content' => '切换房间失败！',
                            'serverTime' => time(),
                        );
                        Gateway::sendToGroup($room_id, json_encode($message));
                    }
                    \workerman\Lib\Timer::del($Timer_id[$room_id]);
                });
                return ;
        }
   }

   /**
    * 当客户端断开连接时
    * @param integer $client_id 客户端id
    */
   public static function onClose($client_id)
   {
       // debug
       // echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']}  client_id:$client_id onClose:''\n";

       // 从房间的客户端列表中删除
       if(isset($_SESSION['room_id']))
       {
            
            if (isset($_SESSION['role']) && strcmp($_SESSION['role'], 'owner') === 0) {
                // Crontab::delAnchorOnline($_SESSION['room_id']);
                // AdminClient::setAnchorOffLine($_SESSION['user_id']);
            }

           // 退出不广播，量大
           /*$room_id = $_SESSION['room_id'];
           $new_message = array('type'=>'logout', 'from_client_id'=>$client_id, 'user_id'=>$_SESSION['user_id'], 'from_client_name'=>$_SESSION['client_name'], 'time'=>date('Y-m-d H:i:s'));
           Gateway::sendToGroup($room_id, json_encode($new_message));*/
       }
   }
}

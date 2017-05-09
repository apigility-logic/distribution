<?php

/**
 * Created by PhpStorm.
 * User: shellvon
 * Date: 16/4/8
 * Time: 下午5:20.
 */
class AnchorAction extends BaseAction
{
    const MAX_RESPONSE_LIST_SIZE = 50;

    private $user;
    protected $default_msg = array(
        'category' => 'anchor_api',
        'ref' => '主播相关API',
        'links' => array(
            'hot_anchor_url' => array(
                'href' => 'v1/anchor/hot',
                'ref' => '热门主播列表',
                'method' => 'GET',
                'parameters' => array('token' => 'string, required'),
            ),
            'online_friends_url' => array(
                'href' => 'v1/anchor/onlineFriends',
                'ref' => '正在直播的好��自己关注的人)',
                'method' => 'GET',
                'parameters' => array('token' => 'string, required'),
            ),
            'recommend_anchor_url' => array(
                'href' => 'v1/anchor/recommend',
                'ref' => '推荐主播列表',
                'method' => 'GET',
                'parameters' => array('token' => 'string, required', 'page' => 'integer, optional, default: 1', 'size' => 'integer, optional, default: 10'),
            ),
            'search_anchor_url' => array(
                'href' => 'v1/anchor/search',
                'ref' => '搜索主播列表',
                'method' => 'GET',
                'parameters' => array('token' => 'string, required', 'query' => 'string, required', 'page' => 'integer, optional, default: 1', 'size' => 'integer, optional, default: 10'),
            ),
            'city_anchor_url' => array(
                'href' => 'v1/anchor/city',
                'ref' => '同城主播列表',
                'method' => 'GET',
                'parameters' => array('token' => 'string, required', 'city' => 'string, required', 'page' => 'integer, optional, default: 1', 'size' => 'integer, optional, default: 10'),
            ),
            'anchor_toggle_live_status_url' => array(
                'href' => 'v1/anchor/live',
                'ref' => '直播状态修改',
                'method' => 'POST',
                'parameters' => array('token' => 'string, required', 'status' => 'string, optional, default:off')
            )
        ),
    );

    public function __construct()
    {
        $this->user = M('member');
        parent::__construct();
    }

    /**
     * 热门主播.
     *
     */
    public function hot()
    {
        $mem_obj = TokenHelper::getInstance();
        $room_cache = $mem_obj->get(C('HOT_ANCHOR_LIST'));
        if ($room_cache !== false) {
            $room_lst = json_decode($room_cache, true);
        } else {
            $room_lst = array();
        }
        $robot = C('ROBOT');
        if (!empty($robot)) {
            foreach ($robot as $room_num => $link) {
                $room_lst[$room_num] = 0;
            }
        }
        $room_num_arr = array();
        foreach ($room_lst as $room_num => $online) {
            $room_num_arr[] = $room_num;
        }
        // 不要交换curroomnum的顺序
        $fields = 'curroomnum, id, sid, nickname, ucuid, snap, city, online, virtualguest, isvirtual,avatartime';
        if (empty($room_num_arr)) {
            $condition = '1 != 1';
        } else {
            $condition = 'curroomnum in ('.implode(',', $room_num_arr).')';
        }
        $anchor_lst = $this->user->where($condition)->getField($fields);
        $hot_anchor = array();
        $anchor_cnt = 0;
        // 使用room_lst是为了保证有��但是如果数据库没有对应的信息,得过滤掉.
        foreach ($room_lst as $room_num => $online) {
            $anchor_info = isset($anchor_lst[$room_num]) ? $anchor_lst[$room_num] : null;
            if ($anchor_info === null || $this->current_uid == $anchor_info['id']) {
                continue;
            }
            //标题
            $userStream = M("backstream")->where(array('streamstatus'=>'1',"roomid"=>$room_num))->field('title,topics')->find();
            //话题数组
            $where['id'] = array("in",$userStream['topics']);
            $topicList = M("topic")->where($where)->field('id,title')->select();

            $virtual_cnt = 0;
            if ($anchor_info['isvirtual'] == 'y' && $anchor_info['virtualguest'] > 0) {
                $virtual_cnt = $anchor_info['virtualguest'];
            }
            if ($anchor_info['city'] == L('_PLEASE_SELECT_') || empty($anchor_info['city'])) {
                $city = L('_MARS_');
            } else {
                $city = $anchor_info['city'];
            }
            $hot_anchor[] = array(
                'id' => $anchor_info['id'],
                'curroomnum' => $room_num,
                'online' => $virtual_cnt + $online,
                'roomTitle' => $userStream['title'],
                'roomTopic' => $topicList,
                'avatar' => getAvatar($anchor_info['avatartime'],$anchor_info['id'], 'middle'),
                'snap' => $anchor_info['avatartime'] <= 0 ? '/style/images/default.gif' : getAvatar($anchor_info['avatartime'],$anchor_info['id'], 'yuan'),
                'city' => $city,
                'nickname' => $anchor_info['nickname'],
                'sid' => $anchor_info['sid'],
            );
            if (++$anchor_cnt > self::MAX_RESPONSE_LIST_SIZE) {
                break;
            }
        }
        $tag_info = M('usersort')->select();

        $categories = array(array('id'=>'0','tag'=>L('_HOT_')));
        foreach ($tag_info as $el) {
            // fuck you !!! backspace..
            // see => http://www.fifi.org/doc/vim/html/digraph.html#digraph-table
            // ^H => backspace => ascii(8) => chr(8)
            // 直接用\b不可
            $categories[] = array(
                'id' => $el['id'],
                'tag' => str_replace(chr(8), '', $el['sortname'])
            );
        }


        //TODO: add banner info.
        $picList = M("rollpic")->where("moc = 'mobile'")->select();
        $banner = array();
        foreach($picList as $pic){
            $banner[] = array(
                'img_url' => $pic['picpath'],
                'target_url' => $pic['linkurl'],
            );
        }

        // $banner = array(
        //     array(
        //         'img_url' => '/style/rollpic/2015-03/550d2d8a0f6dc.png',
        //         'target_url' => 'http://demo.meilibo.net',
        //     ),
        //     array(
        //         'img_url' => '/style/rollpic/2015-03/550d2ae7463be.jpg',
        //         'target_url' => 'http://demo.meilibo.net',
        //     ),
        //     array(
        //         'img_url' => '/style/rollpic/2015-09/560343b286190.jpg',
        //         'target_url' => 'http://demo.meilibo.net',
        //     ),
        //     array(
        //         'img_url' => '/style/Uploads/1436879339.jpg',
        //         'target_url' => 'http://demo.meilibo.net',
        //     ),
        // );

        $resp = array(
            'list' => $hot_anchor,
            'banner' => $banner,
            'category' => $categories,
        );
        $this->responseSuccess($resp);
    }

    /**
     * 推荐主播列表.
     *
     * @param int $page
     * @param int $size
     */
    public function recommend($order = null, $page = 1, $size = self::DEFAULT_PAGE_SIZE)
    {
        list($page, $size) = $this->parsePageAndSize($page, $size);
        // 不显示自己
        // if (strcmp($order, 'time') === 0) {
        $condition = array('id' => array('neq', $this->current_uid),'broadcasting'=>'y');
        $options = array('order'=>'starttime desc');
        // } else {
        //     $condition = array('recommend' => 'y', 'id' => array('neq', $this->current_uid));
        //     $options = array();
        // }
        
        $fields = 'id, nickname, curroomnum, city, bigpic, broadcasting, offlinevideo, earnbean, sex, intro,avatartime, channel_id';
        $recommend = $this->getResultByConditionWithPager($this->user->getModelName(), $fields, $condition, null, $page, $size, $options);
        foreach ($recommend['list'] as &$row) {
            $row['avatar'] = getAvatar($row['avatartime'],$row['id'], 'yuan');
            if ($row['broadcasting'] == 'y') {
                // 直播
            } elseif (!empty($row['offlinevideo'])) {
                // 有录像的..
            }

            if ($row['city'] == L('_PLEASE_SELECT_') || empty($row['city'])) {
                $row['city'] = L('_MARS_');
            }
            $userStream = M("backstream")->where(array('streamstatus'=>'1',"roomid"=>$row['curroomnum']))->field('id,title,topics,starttime')->find();
            //是否是私密房间
            $private = 0;
            if(!empty($userStream['id'])){
                if(count(M('privatelimit')->where("bsid=".$userStream['id'])->select()) > 0){
                    $private = 1;
                }
            }
            $row['channel_id'] = empty($row['channel_id']) && is_null($row['channel_id']) ? 0 : $row['channel_id'];
            $row['sex'] = empty($row['sex']) ? 0 : 1;
            //TODO:方法getEmceelevel需要 total_contribution需要
            $level = getEmceelevel($row['earnbean']);
            $row['emceelevel'] = isset($level[0]['levelid']) && $level[0]['levelid'] ? $level[0]['levelid'] : '1';
            unset($row['earnbean']);
            $record = M('attention')->where("uid = {$this->current_uid} and attuid= {$row['attuid']}")->find();
            $row["snap"] = $row['avatartime'] <= 0 ? '/style/images/default.gif' : getAvatar($row['avatartime'],$row['id'], 'yuan');
            $row['is_attention'] = empty($record) ? 0 : 1;
            $row['private'] = $private;
            $row['starttime'] = $userStream['starttime'];
            
        }
	$recommend['listIcon'] = $this->getListIcon('mobile_wpic');
        $this->responseSuccess($recommend);
    }
    /**
     * 同城主播列表.
     *
     * @param string $city
     * @param int $page
     * @param int $size
     */
    public function city($city = null, $page = 1, $size = self::DEFAULT_PAGE_SIZE)
    {
        $fields = 'id, nickname, curroomnum, ucuid, bigpic, broadcasting, offlinevideo, earnbean, sex, intro, channel_id';
        if ($city === null) {
            // $this->responseError('city can not be empty');
            $city = L('_MARS_');
        }
        if (!is_string($city)) {
            $this->responseError(L('_CITY_TYPE_ERROR_'));
        }

        $parse = null;
        $where = "id != {$this->current_uid} and id != ".C('TOURIST_ID');
        //TODO: SQL inject.
        $where .= " and ( city = '".$city."' or province = '".$city."' )and broadcasting = 'y' ";
        $options = array('order' => 'broadcasting desc, onlinenum desc');

        list($page, $size) = $this->parsePageAndSize($page, $size);
        $search_result = $this->getResultByConditionWithPager($this->user->getModelName(), $fields, $where, $parse, $page, $size, $options);
        foreach ($search_result['list'] as $key => &$row) {
            if($row['id'] == $this->current_uid){
                array_splice($search_result['list'],$key,1);
            }
            $row['avatar'] = getAvatar($row['avatartime'],$row['id'], 'middle');
            if ($row['broadcasting'] == 'y') {
                // 直播��
            } elseif (!empty($row['offlinevideo'])) {
                // 有录像的..
            }

            $userStream = M("backstream")->where(array('streamstatus'=>'1',"roomid"=>$row['curroomnum']))->field('id,title,topics,starttime')->find();
            //是否是私密房间
            $private = 0;
            if(!empty($userStream['id'])){
                if(count(M('privatelimit')->where("bsid=".$userStream['id'])->select()) > 0){
                    $private = 1;
                }
            }
            $row['private'] = $private;
            $row['channel_id'] = empty($row['channel_id']) || is_null($row['channel_id']) ? 0 : $row['channel_id'];
            $row['sex'] = empty($row['sex']) ? 0 : 1;
            //TODO:方法getEmceelevel需要重�� total_contribution需要确��
            $level = getEmceelevel($row['earnbean']);
            $row['emceelevel'] = isset($level[0]['levelid']) && $level[0]['levelid'] ? $level[0]['levelid'] : '1';
            $record = M('attention')->where("uid = {$this->current_uid} and attuid= {$row['attuid']}")->find();
            $row['is_attention'] = empty($record) ? 0 : 1;
            $row["snap"] = $row['avatartime'] <= 0 ? '/style/images/default.gif' : getAvatar($row['avatartime'],$row['id'], 'yuan');
            $row['private'] = $private;
            $row['starttime'] = $userStream['starttime'];
            unset($row['earnbean']);
        }
        $this->responseSuccess($search_result);
    }

    /**
     * 搜索主播接口.
     *
     * @param null $query 用户输入的查询字符串.
     * @param int  $page  页码.
     * @param int  $size  每页大小.
     */
    public function search($query = null, $page = 1, $size = self::DEFAULT_PAGE_SIZE)
    {
        $fields = 'id,spendcoin, nickname, curroomnum, ucuid, bigpic, broadcasting, onlinenum, offlinevideo, earnbean, sex, intro, channel_id';
        if ($query === null) {
            $this->responseError(L('_QUERY_NOT_EMPTY_'));
        }
        if (!is_string($query)) {
            $this->responseError(L('_QUERY_TYPE_ERROR_'));
        }
        $parse = null;
        //TODO: SQL injeact.

        if (ctype_digit($query)) {
            //不准搜索自己.
            $where = "( id = '{$query}' and id != {$this->current_uid} and id != ".C('TOURIST_ID')."  )  or (nickname like'%{$query}%' and id != {$this->current_uid} and id != ".C('TOURIST_ID').")";
        } else {
            $where = "( id = '{$query}' and id != {$this->current_uid} and id != ".C('TOURIST_ID')."  )  or (nickname like'%{$query}%' and id != {$this->current_uid} and id != ".C('TOURIST_ID').")";
        }
        list($page, $size) = $this->parsePageAndSize($page, $size);
        $search_result = $this->getResultByConditionWithPager($this->user->getModelName(), $fields, $where, $parse, $page, $size);
        foreach ($search_result['list'] as $key => &$row) {
            if($row['id'] == $this->current_uid){
                array_splice($search_result['list'],$key,1);
            }
            $row['avatar'] = getAvatar(time(),$row['id'], 'middle');
            if ($row['broadcasting'] == 'y') {
                // 直播��
            } elseif (!empty($row['offlinevideo'])) {
                // 有录像的..
            }
            $userStream = M("backstream")->where(array('streamstatus'=>'1',"roomid"=>$row['curroomnum']))->field('id,title,topics,starttime')->find();
            //是否是私密房间
            $private = 0;
            if(!empty($userStream['id'])){
                if(count(M('privatelimit')->where("bsid=".$userStream['id'])->select()) > 0){
                    $private = 1;
                }
            }
            $row['private'] = $private;
            $row['channel_id'] = empty($row['channel_id']) && is_null($row['channel_id']) ? 0 : $row['channel_id'];
            $row['sex'] = empty($row['sex']) ? 0 : 1;
            //TODO:方法getEmceelevel需要重�� total_contribution需要确��
            $level = getRichlevel($row['spendcoin']);
            $row['emceelevel'] = isset($level[0]['levelid']) && $level[0]['levelid'] ? $level[0]['levelid'] : '1';
            $record = M('attention')->where("uid = {$this->current_uid} and attuid= {$row['id']}")->find();
            $row['is_attention'] = empty($record) ? 0 : 1;
            $row["snap"] = $row['avatartime'] <= 0 ? '/style/images/default.gif' : getAvatar($row['avatartime'],$row['id'], 'yuan');
            unset($row['earnbean']);
        }
        $this->responseSuccess($search_result);
    }
     /**
     * 搜索主播接口(新).
     *
     * @param null $query 用户输入的查询字符串.
     * @param int  $page  页码.
     * @param int  $size  每页大小.
     * @param int  $type  搜索内容类型.(ID 房间号 昵称 话题)
     * zdd
     */
    public function search_detail($query = null, $page = 1, $size = self::DEFAULT_PAGE_SIZE,$type = null)
    {
        $fields = 'id, nickname, curroomnum, ucuid, bigpic, broadcasting, onlinenum, offlinevideo, earnbean, sex, intro, channel_id,avatartime';
        if ($query === null) {
            $this->responseError(L('_QUERY_NOT_EMPTY_'));
        }
        if (!is_string($query)) {
            $this->responseError(L('_QUERY_TYPE_ERROR_'));
        }
        $parse = null;
        $where = " id != ".C('TOURIST_ID');
        //TODO: SQL inject.
        if($type == 'nickname'){
            $where .=" and nickname like '%".$query."%'"; 
        }elseif($type == 'id'){
            $where .=" and id = ".$query; 
        }elseif($type == 'roomnum'){
            $where .=" and curroomnum = ".$query; 
        }elseif($type == 'topic'){
            //这里用原生SQL的方法是因为getFiled不支持连表查询，用tp内置SQL要翻车
            $topic = "select ss_backstream.uid from ss_backstream join ss_topic on ss_topic.id = ss_backstream.topics where ss_topic.title = '".$query."'and ss_backstream.streamstatus <> 0";
            $topic_uid = M()->query($topic);
            if(!empty($topic_uid)){
                $counttopic = count($topic_uid);
                $topic_uidarr = "(";
                foreach($topic_uid as $k => $v){
                    if($k == $counttopic-1){
                        $topic_uidarr .= $v['uid'];
                    }else{
                        $topic_uidarr .= $v['uid'].",";
                    }
                }
                $topic_uidarr .= ")";
            }
            $where .= " and id in".$topic_uidarr;
        }else{
            $where .= " and (curroomnum like '%".$query."%' or id like '%".$query."%' or nickname like"."'%{$query}%' )";
        }
        list($page, $size) = $this->parsePageAndSize($page, $size);
        $search_result = $this->getResultByConditionWithPager($this->user->getModelName(), $fields, $where, $parse, $page, $size);
        foreach ($search_result['list'] as $key => &$row) {
            // if($row['id'] == $this->current_uid){
            //     array_splice($search_result['list'],$key,1);
            // }
            $row['avatar'] = getAvatar($row['avatartime'],$row['id'], 'middle');
            if ($row['broadcasting'] == 'y') {
                // 直播��
            } elseif (!empty($row['offlinevideo'])) {
                // 有录像的..
            }
            $userStream = M("backstream")->where(array('streamstatus'=>'1',"roomid"=>$row['curroomnum']))->field('id,title,topics,starttime')->find();
            //是否是私密房间
            $private = 0;
            if(!empty($userStream['id'])){
                if(count(M('privatelimit')->where("bsid=".$userStream['id'])->select()) > 0){
                    $private = 1;
                }
            }
            $row['private'] = $private;   
            $row['channel_id'] = empty($row['channel_id']) && is_null($row['channel_id']) ? 0 : $row['channel_id'];
            $row['sex'] = empty($row['sex']) ? 0 : 1;
            //TODO:方法getEmceelevel需要重�� total_contribution需要确��
            $level = getEmceelevel($row['earnbean']);
            $row['emceelevel'] = isset($level[0]['levelid']) && $level[0]['levelid'] ? $level[0]['levelid'] : '1';
            $record = M('attention')->where("uid = {$this->current_uid} and attuid= {$row['attuid']}")->find();
            $row['is_attention'] = empty($record) ? 0 : 1;
            $row["snap"] = $row['avatartime'] <= 0 ? '/style/images/default.gif' : getAvatar($row['avatartime'],$row['id'], 'yuan');
            unset($row['earnbean']);
        }
        $this->responseSuccess($search_result);
    }
    /**
     * 修改主播直播的直播状
     *
     * @param string $status 是否直播,on表示开��off表示关闭.
     */
    public function live($status = 'off')
    {
        if (!$this->isPost() && !APP_DEBUG) {
            $this->forbidden();
        }
        if (!is_string($status) || !in_array(strtolower($status), array('on', 'off'))) {
            $this->responseError(L('_PARAM_ILLEGAL_'));
        }
        $data = strtolower($status) == 'on' ? array('broadcasting'=>'y','starttime'=>time()) : array('broadcasting'=>'n');
        $cond = array('id' => $this->current_uid);
        //$result = $this->user->where($cond)->save($data);
        $room = $this->user->where($cond)->getField('curroomnum');
        $key = C('LIVE_STATUS_PREFIX').$room;
        if ($broadcasting == 'y') {
            // 写入memcache标识该APP正在直播.
            TokenHelper::getInstance()->set($key,'y',0);
        } else {
            // 删除key表示该用户退出直播
            TokenHelper::getInstance()->delete($key);
        }
        //if ($result === false) {
        if (false) {
            $this->responseError(L('_UPDATE_ERROR_'));
        } else {
            $this->responseSuccess(L('_UPDATE_SUCCESS_'));
        }
    }


    /**
     * 正在直播的好友列��和热门主播一个样��
     * TODO: hot/onlineFriends是否可以提取公共方法?
     */
    public function onlineFriends()
    {
        $model = M('Attention');
        //TODO: TMD 真SB,为什么getField非要传入俩参数才返回多条记录?
        $records = $model->where(array('uid' => $this->current_uid))->getField('attuid,uid');
        if (empty($records)) {
            $this->responseSuccess(array());
        }
        $condition = array(
            'id' => array(
                'in', array_keys($records) # fuck!!!!
            )
        );
        $fields = 'id,curroomnum,broadcasting, nickname, snap, city, online, virtualguest, isvirtual,avatartime, channel_id';
        $friends = $this->user->where($condition)->getField($fields);
        // 获取当前在线列表.
        $mem_obj = TokenHelper::getInstance();
        $room_cache = $mem_obj->get(C('HOT_ANCHOR_LIST'));
        if (empty($room_cache)) {
            //$this->responseSuccess(array());
        }
        $room_lst = json_decode($room_cache, true);
        $cnt = 0;
        $online_friends = array();
        foreach ($friends as $friend) {
            $virtual_cnt = 0;
            if ($friend['isvirtual'] == 'y' && $friend['virtualguest'] > 0) {
                $virtual_cnt = $friend['virtualguest'];
            }
            if ($friend['city'] == L('_PLEASE_SELECT_') || empty($friend['city'])) {
                $city = L('_MARS_');
            } else {
                $city = $friend['city'];
            }
            $room_num = $friend['curroomnum'];
            if (!isset($room_lst[$room_num])) {
                //不在线的朋友也要
                //continue;
            }
            //是否是私密房间
            $private = 0;
            $userStream = M('backstream')->where('roomid = '.$room_num.' and streamstatus = "1" ')->find();
            if(!empty($userStream['id'])){
                if(count(M('privatelimit')->where("bsid=".$userStream['id'])->select()) > 0){
                    $private = 1;
                }
            }
            
            
	        $online = $room_lst[$room_num];
            $online_friends[] = array(
                'id' => $friend['id'],
                'curroomnum' => $room_num,
                'broadcasting' => $friend['broadcasting'],
                'online' => $virtual_cnt + $online,
                'avatar' => getAvatar($friend['avatartime'],$friend['id'], 'middle'),
                'snap' => $friend['avatartime'] <= 0 ? '/style/images/default.gif' : getAvatar($friend['avatartime'],$friend['id'], 'yuan'),
                'city' => $city,
                'nickname' => $friend['nickname'],
                'channel_id' => empty($friend['channel_id']) && is_null($friend['channel_id']) ? 0 : $friend['channel_id'],
                'private' => $private,
                'starttime' => $userStream['starttime'],
            );

            if (++$cnt > self::MAX_RESPONSE_LIST_SIZE) {
                break;
            }
        }
        $this->responseSuccess($online_friends);
    }

    /**
    * 获取主播秀豆    *
    * @param int $user_id
    *
    * @return int $beanbalance
    */
    public function getAnchorBean($user_id)
    {
        if (!is_numeric($user_id) || $user_id <=0) {
            $this->responseError(L('_USER_ID_ILLEGAL_'));
        }
        $beanbalance = M('Member')->where('id='.$user_id)->getField('beanorignal');

        $beanbalance = !$beanbalance ? 0 : $beanbalance;

        // $config = M('Siteconfig')->find();
        // if ($config['emceededuct'] != 0) {
        //     $beanbalance = $beanbalance * floor(100/$config['emceededuct']);
        // }
        $this->responseSuccess($beanbalance);
    }


    public function getHotList($currProvince = "hot",$sex = NULL){
        //查询所有城��并统计在线人数
        if($currProvince == L('_HOT_')){
            $currProvince = "hot";
        }
        $where['broadcasting'] = "y";
        if($sex == "0" || $sex == "1"){
            $where['sex'] = $data['sex'] = $sex;
        }
        $List = M("member")->where($where)->field("count(id) anchorcnt,province")->group("province")->select();
        $count = 0;
        $currKey = 0;
        foreach ($List as $key => $Anchor) {
            //标题
            if($currProvince != "hot"){
                if($currProvince == $Anchor['province']){
                    $currKey = $key;
                }
            }

            if($Anchor['province'] == L('_PLEASE_SELECT_')) {
                unset($List[$key]);
                continue;
            }

            if ($Anchor['province'] == L('_PLEASE_SELECT_') || empty($Anchor['province']) || $Anchor['province'] == L('_MARS_')) {
                $List[$key]['province'] = L('_MARS_');
            } else {
                $List[$key]['province'] = $Anchor['province'];
            }
            $count += $Anchor['anchorcnt'];
        }
        if($currProvince == "hot"){
            $data['anchorcnt'] = $count;
            $data['province'] = L('_HOT_');
            array_unshift($List,$data);
        }else {
            //直接删除数组中某个元素            //向数组前添加两个数组
            $tempData = array();
            $tempData = $List[$currKey];
            $data['anchorcnt'] = $count;
            $data['province'] = L('_HOT_');
            array_splice($List,$currKey,1); //移除��
            array_unshift($List,$data); //插入热门
            array_unshift($List,$tempData); //插入当前选中
        }
        $this->responseSuccess($List);
    }
    public function getAnchorList($province = NULL,$sex = NULL){
        if($province == NULL && $sex == NULL){
            $this->responseError(L('_PARAM_IS_EMPTY_'));
        }
        $data = array();
        if($province == L('_MARS_')){
            if($sex != NULL){
                $data['sex'] = $sex;
            }
        }else{
            if($province != NULL){
                $data['province'] = $province;
                if($sex == "0" || $sex == "1"){
                    $data['sex'] = $sex;
                }
            }
            if($sex != NULL){
                $data['sex'] = $sex;
                if($province != NULL){
                    $data['province'] = $province;
                }
            }
        }
        $data['broadcasting'] = "y";
        $hot_anchor = array();
        $AnchorList = M("member")->where($data)->field("curroomnum, id, sid, nickname, ucuid, snap,province, city, online, virtualguest, isvirtual,avatartime")->select();
        foreach ($AnchorList as $Anchor) {
            //标题
            $userStream = M("backstream")->where(array('streamstatus'=>'1',"roomid"=>$Anchor['curroomnum']))->field('title,topics')->find();
            //话题数组
            $where['id'] = array("in",$userStream['topics']);
            $topicList = M("topic")->where($where)->field('id,title')->select();

            $virtual_cnt = 0;
            if ($Anchor['isvirtual'] == 'y' && $Anchor['virtualguest'] > 0) {
                $virtual_cnt = $Anchor['virtualguest'];
            }
            if ($Anchor['province'] == L('_PLEASE_SELECT_') || empty($Anchor['province'])) {
                $province = L('_MARS_');
            } else {
                $province = $Anchor['province'];
            }
            if ($Anchor['city'] == L('_PLEASE_SELECT_') || empty($Anchor['city'])) {
                $city = L('_MARS_');
            } else {
                $city = $Anchor['city'];
            }


            $hot_anchor[] = array(
                'id' => $Anchor['id'],
                'curroomnum' => $Anchor['curroomnum'],
                'online' => $virtual_cnt,
                'roomTitle' => $userStream['title'],
                'roomTopic' => $topicList,
                'avatar' => getAvatar($Anchor['avatartime'],$Anchor['id'], 'middle'),
                'snap' => $Anchor['avatartime'] <= 0 ? '/style/images/default.gif' : getAvatar($Anchor['avatartime'],$Anchor['id'], 'yuan'),
                'province' => $province,
                'city' => $city,
                'nickname' => $Anchor['nickname'],
                'sid' => $Anchor['sid'],
            );
        }
        $this->responseSuccess($hot_anchor);
    }
    public function getAnchorListTest($province = NULL,$sex = NULL){


        // 不要交换curroomnum的顺序
        $fields = 'curroomnum,broadcasting ,id,onlinenum, sid, nickname, snap, province, city, online, virtualguest, isvirtual,avatartime, channel_id,avatarroomtime';
        $condition = array();
        $condition[] = " broadcasting='y' ";
        if($province != NULL){
            if($province != L('_HOT_')){
                if($province == L('_PLEASE_SELECT_')){
                    $province = L('_MARS_');
                }
                $condition[] = " province = '{$province}' ";
            }
        }else if($sex != NULL){
            $condition[] = " sex = '{$sex}' ";
        }

        if(isset($_REQUEST['token'])) {
            $token = $_REQUEST['token'];
            $info = TokenHelper::getInstance()->get($token);
            if(!empty($info['uid'])) {
                $condition[] = " id != '{$info['uid']}' ";
            }
        }


        $anchor_lst = $this->user->where(implode(" and ",$condition))->getField($fields);
        $hot_anchor = array();
        $anchor_cnt = 0;
        // 使用room_lst是为了保证有��但是如果数据库没有对应的信息,得过滤掉.
        foreach ($anchor_lst as $online) {
            $room_num = $online['curroomnum'];
            $key = C('LIVE_STATUS_PREFIX').$room_num;
            //if (TokenHelper::getInstance()->get($key) == 'y') {//OBS推流 判断是否web端创建直播间
            $anchor_info = $online;
            //标题
            $userStream = M("backstream")->where(array('streamstatus'=>'1',"roomid"=>$room_num))->field('id,title,topics,starttime')->find();
            //话题数组
            $where['id'] = array("in",$userStream['topics']);
            $topicList = M("topic")->where($where)->field('id,title')->select();
            $record = M('attention')->where("uid = {$this->current_uid} and attuid= {$anchor_info['id']}")->find();

            $virtual_cnt = 0;
            if ($anchor_info['isvirtual'] == 'y' && $anchor_info['virtualguest'] > 0) {
                $virtual_cnt = $anchor_info['virtualguest'];
            }
            if ($anchor_info['city'] == L('_PLEASE_SELECT_') || empty($anchor_info['city'])) {
                $city = L('_MARS_');
            } else {
                $city = $anchor_info['city'];
            }
            if ($anchor_info['province'] == L('_PLEASE_SELECT_') || empty($anchor_info['province'])) {
                $province = L('_MARS_');
            } else {
                $province = $anchor_info['province'];
            }

            //是否是私密房间
            $private = 0;
            if(!empty($userStream['id'])){
                if(count(M('privatelimit')->where("bsid=".$userStream['id'])->select()) > 0){
                    $private = 1;
                }
            }

            $memOnlineNum= 0;
            if ($online_info = $this->mmc->get(C('ROOM_ONLINE_NUM_PREFIX').$room_num)) {
                $online_info = json_decode($online_info, true);
                $memOnlineNum = (int)$online_info['all_num'];
            }

            //检测是否上传直播间专用展示图
            if($anchor_info['avatarroomtime'] > 0){
                $roomPicPath = getRoompic($anchor_info['avatarroomtime'],$anchor_info['id'],'middle');
            }
            $hot_anchor[] = array(
                'id' => $anchor_info['id'],
                'curroomnum' => $room_num,
                'online' => $anchor_info['onlinenum'] + $memOnlineNum,
                'broadcasting' =>  $anchor_info['broadcasting'],
                'roomTitle' =>  $userStream['title'],
                'avatar' => getAvatar($anchor_info['avatartime'],$anchor_info['id'], 'middle'),
                //'snap' => $anchor_info['avatartime'] <= 0 ? '/style/images/default.gif' : getAvatar($anchor_info['avatartime'],$anchor_info['id'], 'yuan'),
                'snap' =>$roomPicPath ? $roomPicPath : ($anchor_info['avatartime'] <= 0 ? '/style/images/default.gif' : getAvatar($anchor_info['avatartime'],$anchor_info['id'], 'yuan')),
                'province' => $province,
                'city' => $city,
                'nickname' => $anchor_info['nickname'],
                'sid' => $anchor_info['sid'],
                'is_attention' => empty($record) ? 0 : 1,
                'starttime' => $userStream['starttime'],
                'bsid'  => $userStream['id'],
                'private' => $private,
                'channel_id' => empty($anchor_info['channel_id']) && is_null($anchor_info['channel_id']) ? 0 : $anchor_info['channel_id'],
            );
            if (++$anchor_cnt > self::MAX_RESPONSE_LIST_SIZE) {
                break;
            }
        }

        
        $tag_info = M('usersort')->select();

        $categories = array(array('id'=>'0','tag'=>L('_HOT_')));
        foreach ($tag_info as $el) {
            // fuck you !!! backspace..
            // see => http://www.fifi.org/doc/vim/html/digraph.html#digraph-table
            // ^H => backspace => ascii(8) => chr(8)
            // 直接用\b不可用
            $categories[] = array(
                'id' => $el['id'],
                'tag' => str_replace(chr(8), '', $el['sortname'])
            );
        }


        //TODO: add banner info.
        $picList = M("rollpic")->where("moc = 'mobile'")->select();
        $banner = array();
        foreach($picList as $pic){
            $banner[] = array(
                'img_url' => $pic['picpath'],
                'target_url' => $pic['linkurl'],
            );
        }

        $resp = array(
            'list' => $this->sortByField($hot_anchor,'online'),
            'banner' => $banner,
            'category' => $categories,
	        'listIcon' => $this->getListIcon('mobile_wpic')
        );
        $this->responseSuccess($resp);
    }

    private function sortByField($arrUsers,$field) {
        $sort = array(
            'direction' => 'SORT_DESC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
            'field'     => $field,       //排序字段
        );
        $arrSort = array();
        foreach($arrUsers AS $uniqid => $row){
            foreach($row AS $key=>$value){
                $arrSort[$key][$uniqid] = $value;
            }
        }
        if($sort['direction']){
            array_multisort($arrSort[$sort['field']], constant($sort['direction']), $arrUsers);
        }
        return $arrUsers;
    }

    public function getListIcon( $type = null ){
        if( empty($type) ){
            $this->responseError(L('_PARAM_IS_EMPTY_'));
        }
        $picList = M("rollpic")->where("moc = '".$type."'")->order('orderno','desc')->find();
        $banner = array();
        $userinfo = M('member')->where('id='.$picList['uid'])->find();
        $data = null;
        if($picList['uid'] != 0 && count($userinfo) > 0){
            $data = array(
                'id' => $userinfo['id'],
                'curroomnum' => $userinfo['curroomnum'],
                'online' => $userinfo['online'],
                'avatar' => getAvatar($userinfo['avatartime'], $userinfo['id']),
                'snap' => getAvatar($userinfo['avatartime'], $userinfo['id'],'big'),
                'city' => $userinfo['city'],
                'nickname'=> $userinfo['nickname'],

                );
        }
        $banner = array(
            'img_url' => $picList['picpath'],
            'target_url' => $picList['linkurl'],
            'info' => $data,
        );
        return $banner;
    }
}

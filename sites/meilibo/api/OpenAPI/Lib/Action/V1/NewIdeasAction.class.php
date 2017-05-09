<?php
/**
* 此API 仅供 新丝路 使用
*
*/
class NewIdeasAction extends BaseAction {
    const MAX_RESPONSE_LIST_SIZE = 50;

	public function __construct()
	{
		parent::__construct();
	}
	/**
	*	获取超模列表
	*	@param string token 	通行证
	*	@param string province 	城市
	*	@param string sex 		性别
	*
	*/
	public function getSupermodelList($province = NULL,$sex = NULL){
        
        $mem_obj = TokenHelper::getInstance();
        $room_cache = $mem_obj->get(C('HOT_ANCHOR_LIST'));
        $room_lst = json_decode($room_cache, true);
        if (!$room_lst || !is_array($room_lst)) {
            // $room_lst = array();
            $room_lst = M('Member')->where(array('broadcasting'=>'y', 'sign' => 'c'))->order('onlinenum desc')->getField('curroomnum,onlinenum');
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
        $fields = 'curroomnum, id, sid, nickname, snap, province, city, online, virtualguest, isvirtual,avatartime, onlinenum, broadcasting';
        if (empty($room_num_arr)) {
            $condition = '1 != 1';
        } else {
            $condition = 'curroomnum in ('.implode(',', $room_num_arr).')';
        }
        if($province != NULL){
            if($province != L('_HOT_')){
                if($province == L('_MARS_')){
                    $province = L('_PLEASE_SELECT_');
                }
                $condition .= " and province = '".$province."'";
            }
            if($sex == "0" || $sex == "1"){
                $condition .= " and sex = '".$sex."'";
            }
        }else if($sex != NULL){

            $condition .= " and sex = '".$sex."'";
            if($province != NULL){
                if($province == L('_MARS_')){
                    $province = L('_PLEASE_SELECT_');
                }
                $condition .= " and province = '".$province."'";
            }
        }
        $condition .= " and  sign = 'c'";
        $anchor_lst = M('member')->where($condition)->getField($fields);
        $hot_anchor = array();
        $hot_anchor1 = array();
        $anchor_cnt = 0;
    
        // 使用room_lst是为了保证有��但是如果数据库没有对应的信息,得过滤掉.
        //TODO: add banner info.
        $picList = M("rollpic")->where("moc = 'mobile_c'")->order('orderno','desc')->select();
        $banner = array();
        foreach($picList as $pic){
            $userinfo = M('member')->where('id='.$pic['uid'])->find();
            $data = null;
            if($pic['uid'] != 0 && count($userinfo) > 0){
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
            $banner[] = array(
                'img_url' => $pic['picpath'],
                'target_url' => $pic['linkurl'],
                'info' => $data,
            );
        }
        foreach ($room_lst as $room_num => $online) {
            $anchor_info = isset($anchor_lst[$room_num]) ? $anchor_lst[$room_num] : null;
            if ($anchor_info === null || $this->current_uid == $anchor_info['id']) {
                continue;
            }
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
            if ($anchor_info['city'] ==  L('_PLEASE_SELECT_') || empty($anchor_info['city'])) {
                $city = L('_MARS_');
            } else {
                $city = $anchor_info['city'];
            }
            if ($anchor_info['province'] ==  L('_PLEASE_SELECT_') || empty($anchor_info['province'])) {
                $province =  L('_MARS_');
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
            $room_cachenum = json_decode($mem_obj->get(C('ROOM_ONLINE_NUM_PREFIX').$room_num), true);
            if (!$room_cachenum) {
                $room_cachenum = array('all_num'=>0,'viewer_num'=>0);
            }
            $hot_anchor[] = array(
                'id' => $anchor_info['id'],
                'curroomnum' => $room_num,
                'online' => 1,      
                'onlinenum' => $room_cachenum['all_num'],
                'roomTitle' =>  $userStream['title'],
                'roomTopic' => !is_array($topicList) ? null : $topicList,
                'avatar' => getAvatar($anchor_info['avatartime'],$anchor_info['id'], 'middle'),
                'snap' => $anchor_info['avatartime'] <= 0 ? '/style/images/default.gif' : getAvatar($anchor_info['avatartime'],$anchor_info['id'], 'yuan'),
                'province' => $province,
                'city' => $city,
                'nickname' => $anchor_info['nickname'],
                'sid' => $anchor_info['sid'],
                'is_attention' => empty($record) ? 0 : 1,
                'starttime' => $userStream['starttime'],
                'private' => $private,                    
                'broadcasting' => $anchor_info1['broadcasting'], 
                // 'banner' => $banner,
            );
            if (++$anchor_cnt > 20) {
                break;
            }
        }

       
        if( count($hot_anchor) < 20 ){
            $condition2 = array();
            if (!empty($room_num_arr)) {
                $condition2 = 'ss_member.curroomnum not in ('.implode(',', $room_num_arr).') and ss_member.id <> '.C('TOURIST_ID')." and sign = 'c' ";
            }
            $fields2 = 'ss_member.curroomnum, ss_member.id, ss_member.sid, ss_member.nickname, ss_member.snap, 
            ss_member.province, ss_member.city, ss_member.online, ss_member.virtualguest, ss_member.isvirtual,
            ss_member.avatartime, ss_member.onlinenum,  ss_member.broadcasting ';
            // $condition2 = " 1 ".$condition1;
            // $anchor_lst1 = $this->user->where($condition2)->order(" onlinenum desc ")->field($fields)->select();
            $anchor_lst1 = M('member')->join("ss_backstream on ss_member.id = ss_backstream.uid")->where($condition2)->group("ss_member.id")->order(" ss_member.onlinenum desc, ss_backstream.starttime desc ")->field($fields2)->select();
            
            foreach ($anchor_lst1 as $anchor_info1) {
                if ( $this->current_uid == $anchor_info1['id'] || in_array($room_num_arr, $anchor_info1['curroomnum'])) {
                    continue;
                }
                //标题
                $userStream = M("backstream")->where(array("roomid"=>$anchor_info1['curroomnum']))->field('id,title,topics,starttime')->order(" id desc ")->find();
                //话题数组
                $where['id'] = array("in",$userStream['topics']);
                $topicList = M("topic")->where($where)->field('id,title')->select();
                $record = M('attention')->where("uid = {$this->current_uid} and attuid= {$anchor_info1['id']}")->find();

                $virtual_cnt = 0;
                if ($anchor_info1['isvirtual'] == 'y' && $anchor_info1['virtualguest'] > 0) {
                    $virtual_cnt = $anchor_info1['virtualguest'];
                }
                if ($anchor_info1['city'] == L('_PLEASE_SELECT_') || empty($anchor_info1['city'])) {
                    $city = L('_MARS_');
                } else {
                    $city = $anchor_info1['city'];
                }
                if ($anchor_info1['province'] == L('_PLEASE_SELECT_') || empty($anchor_info1['province'])) {
                    $province = L('_MARS_');
                } else {
                    $province = $anchor_info1['province'];
                }

                //是否是私密房间
                $private = 0;
                if(!empty($userStream['id'])){
                    if(count(M('privatelimit')->where("bsid=".$userStream['id'])->select()) > 0){
                        $private = 1;
                    }
                }
                $room_cachenum = json_decode($mem_obj->get(C('ROOM_ONLINE_NUM_PREFIX').$anchor_info1['curroomnum']), true);
                if (!$room_cachenum) {
                    $room_cachenum = array('all_num'=>0,'viewer_num'=>0);
                }
                $hot_anchor1[] = array(
                    'id' => $anchor_info1['id'],
                    'curroomnum' => $anchor_info1['curroomnum'],
                    'online' => 0,      
                    'onlinenum' => $anchor_info1['onlinenum'],
                    'roomTitle' =>  $userStream['title'],
                    'roomTopic' => !is_array($topicList) ? null : $topicList,
                    'avatar' => getAvatar($anchor_info1['avatartime'],$anchor_info1['id'], 'middle'),
                    'snap' => $anchor_info1['avatartime'] <= 0 ? '/style/images/default.gif' : getAvatar($anchor_info1['avatartime'],$anchor_info1['id'], 'yuan'),
                    'province' => $province,
                    'city' => $city,
                    'nickname' => $anchor_info1['nickname'],
                    'sid' => $anchor_info1['sid'],
                    'is_attention' => empty($record) ? 0 : 1,
                    'starttime' => $userStream['starttime'],
                    'private' => $private,
                    'broadcasting' => $anchor_info1['broadcasting'], 
                    // 'banner' => $banner,

                );
                if (++$anchor_cnt > 20) {
                    break;
                }
            }
        }
        $hot_anchor = array_merge($hot_anchor, $hot_anchor1);

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
        $resp = array(
            'list' => $hot_anchor,
            'category' => $categories,
            'banner' => $banner,
        );
        $this->responseSuccess($resp);
    }



    /**
     * 附近主播列表.
     *
     * @param string $city
     * @param int $page
     * @param int $size
     */
    public function city($city = null, $page = 1, $size = self::DEFAULT_PAGE_SIZE)
    {
        $fields = 'id, nickname, curroomnum, ucuid, bigpic, broadcasting, offlinevideo, earnbean, sex, intro, channel_id,avatartime';
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
        $where .= " and  broadcasting = 'y' and ( city = '".$city."' or province = '".$city."') ";
        $options = array('order' => 'broadcasting desc, onlinenum desc');

        list($page, $size) = $this->parsePageAndSize($page, $size);
        $search_result = $this->getResultByConditionWithPager("member", $fields, $where, $parse, $page, $size, $options);
        foreach ($search_result['list'] as $key => &$row) {

            if($row['id'] == $this->current_uid){
                array_splice($search_result['list'],$key,1);
            }
            $row['avatar'] = getAvatar($row['avatartime'],$row['id'], 'middle');
            $userStream = M("backstream")->where(array('streamstatus'=>'1',"roomid"=>$row['curroomnum']))->field('id,title,topics,starttime')->find();
            $roomBack = M("backstream")->where(array("roomid"=>$row['curroomnum']))->order("id desc")->limit(1)->field('id,title,topics,starttime')->find();
            if ($row['broadcasting'] == 'y') {
                // 直播��
            } elseif (!empty($roomBack)) {
                // 有录像的..
            }
            $time = RoomAction::getRoomBackByStarttime($roomBack['roomid'], $roomBack['starttime']);
            $row['backstream'] = $time;
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
     * 最新
     * @param string $token 通行证
     * @param int $page
     * @param   int $size
     * 
     */
    public function newest( $page = 1, $size = self::DEFAULT_PAGE_SIZE)
    {
        $fields = 'id, nickname, curroomnum, ucuid, bigpic, broadcasting, offlinevideo, earnbean, sex, intro, channel_id,avatartime,city';

        $parse = null;
        $where = "id != {$this->current_uid} and id != ".C('TOURIST_ID')."  and broadcasting = 'y'";
        //TODO: SQL inject.
        if(M('siteconfig')->where('id=1')->getField('sign_verification') == "1" ){
            $where .= " and sign = 'y'";
        }
        //$options = array('order' => 'id desc');
        $options = array('order' => 'starttime desc');
        list($page, $size) = $this->parsePageAndSize($page, $size);
        $search_result = $this->getResultByConditionWithPager("Member", $fields, $where, $parse, $page, $size, $options);
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
     * 推荐主播列表
     * @param  string $province 城市
     * @param  枚举 $sex      性别
     * @return string   token   通行证
     */
    public function recommend($province = NULL,$sex = NULL){
        $mem_obj = TokenHelper::getInstance();
        $room_cache = $mem_obj->get(C('HOT_ANCHOR_LIST'));
        $room_lst = json_decode($room_cache, true);
        if (!$room_lst || !is_array($room_lst)) {
            // $room_lst = array();
            $room_lst = M('Member')->where(array('broadcasting'=>'y'))->order('onlinenum desc')->getField('curroomnum,onlinenum');
        }
        $robot = C('ROBOT');
        if (!empty($robot)) {
            foreach ($robot as $room_num => $link) {
                if ($room_num == 1333979551) {
                    continue ;
                }
                $room_lst[$room_num] = rand(0,5);
            }
        }
        empty($room_lst) && $room_lst = array();        
        $room_lst = array(1333979551=>3) + $room_lst;
        foreach ($room_lst as $room_num => $online) {
            $room_num_arr[] = $room_num;
        }
        // 不要交换curroomnum的顺序
        $fields = 'curroomnum, id, sid, nickname, snap, province, city, online, onlinenum, virtualguest, isvirtual,avatartime, channel_id,broadcasting';
        if (empty($room_num_arr)) {
            $condition = '1 != 1';
        } else {
            $condition = 'curroomnum in ('.implode(',', $room_num_arr).')';
        }
        if($province != NULL){
            if($province != L('_HOT_')){
                if($province == L('_PLEASE_SELECT_')){
                    $province = L('_MARS_');
                }
                $condition1 .= " and province = '".$province."'";
            }
            if($sex == "0" || $sex == "1"){
                $condition1 .= " and sex = '".$sex."'";
            }
        }else if($sex != NULL){
            $condition1 .= " and sex = '".$sex."'";
            if($province != NULL){
                if($province == L('_PLEASE_SELECT_')){
                    $province = L('_MARS_');
                }
                $condition1 .= " and province = '".$province."'";
            }
        }
        $condition .= $condition1;
        $anchor_lst = M('member')->where($condition)->getField($fields);
        $hot_anchor = array();
        $anchor_cnt = 0;
        // 使用room_lst是为了保证有��但是如果数据库没有对应的信息,得过滤掉.
        foreach ($room_lst as $room_num => $online) {
            $anchor_info = isset($anchor_lst[$room_num]) ? $anchor_lst[$room_num] : null;
            if ($anchor_info === null || $this->current_uid == $anchor_info['id']) {
                continue;
            }
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

            $hot_anchor[] = array(
                'id' => $anchor_info['id'],
                'curroomnum' => $room_num,
                'online' => $anchor_info['broadcasting'] == "y" ? 1 : 0,
                'roomTitle' =>  $userStream['title'],
                'roomTopic' => !is_array($topicList) ? null : $topicList,
                'avatar' => getAvatar($anchor_info['avatartime'],$anchor_info['id'], 'middle'),
                'snap' => $anchor_info['avatartime'] <= 0 ? '/style/images/default.gif' : getAvatar($anchor_info['avatartime'],$anchor_info['id'], 'yuan'),
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
        $recommend_list = M('member')->field($fields)->where(" idxrec = 'y' ")->limit(20)->select();
        foreach ($recommend_list as $key => $recommend_list_one) {
            $recommend_list[$key]['avatar'] = getAvatar($recommend_list_one['avatartime'],$recommend_list_one['id'], 'middle');
            $recommend_list[$key]['snap'] = getAvatar($recommend_list_one['avatartime'],$recommend_list_one['id'], 'yuan');
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
        //$recommend_this_list = M('member')->where(" id in (".C('GM_UID').")")->field($fields)->select();
        $recommend_this_list = M('member')->where(" recommend = 'y'")->order('broadcasting desc')->field($fields)->select();
        if( count( $recommend_this_list ) > 0){
            foreach ($recommend_this_list as $recommend_this_one) {
                //标题
                $userStream = M("backstream")->where(array('streamstatus'=>'1',"roomid"=>$recommend_this_one['curroomnum']))->field('id,title,topics,starttime')->find();
                //话题数组
                $where['id'] = array("in",$userStream['topics']);
                $topicList = M("topic")->where($where)->field('id,title')->select();
                $record = M('attention')->where("uid = {$this->current_uid} and attuid= {$anchor_info['id']}")->find();

                $virtual_cnt = 0;
                if ($recommend_this_one['isvirtual'] == 'y' && $recommend_this_one['virtualguest'] > 0) {
                    $virtual_cnt = $recommend_this_one['virtualguest'];
                }
                if ($recommend_this_one['city'] == L('_PLEASE_SELECT_') || empty($recommend_this_one['city'])) {
                    $city = L('_MARS_');
                } else {
                    $city = $recommend_this_one['city'];
                }
                if ($recommend_this_one['province'] == L('_PLEASE_SELECT_') || empty($recommend_this_one['province'])) {
                    $province = L('_MARS_');
                } else {
                    $province = $recommend_this_one['province'];
                }

                //是否是私密房间
                $private = 0;
                if(!empty($userStream['id'])){
                    if(count(M('privatelimit')->where("bsid=".$userStream['id'])->select()) > 0){
                        $private = 1;
                    }
                }
                $recommend_first_list[] = array(
                    'id' => $recommend_this_one['id'],
                    'curroomnum' => $recommend_this_one['curroomnum'],
                    'online' => $recommend_this_one['broadcasting'] == "y" ? 1 : 0,
                    'roomTitle' =>  $userStream['title'],
                    'roomTopic' => !is_array($topicList) ? null : $topicList,
                    'avatar' => getAvatar($recommend_this_one['avatartime'],$recommend_this_one['id'], 'middle'),
                    'snap' => $recommend_this_one['avatartime'] <= 0 ? '/style/images/default.gif' : getAvatar($recommend_this_one['avatartime'],$recommend_this_one['id'], 'yuan'),
                    'province' => $province,
                    'city' => $city,
                    'nickname' => $recommend_this_one['nickname'],
                    'sid' => $recommend_this_one['sid'],
                    'is_attention' => empty($record) ? 0 : 1,
                    'starttime' => $userStream['starttime'],
                    'bsid'  => $userStream['id'],
                    'private' => $private,
                    'channel_id' => empty($recommend_this_one['channel_id']) && is_null($recommend_this_one['channel_id']) ? 0 : $recommend_this_one['channel_id'],
                    'broadcasting'=>$recommend_this_one['broadcasting'],

                );
                // $recommend_first_list['streamaddress'] = $recommend_this_one['broadcasting'] == "y" ? "" : M('')
            }
        }
        //TODO: add banner info.
        $picList = M("rollpic")->where("moc = 'mobile'")->order('orderno','desc')->select();
        $banner = array();
        foreach($picList as $pic){
            $userinfo = M('member')->where('id='.$pic['uid'])->find();
            $data = null;
            if($pic['uid'] != 0 && count($userinfo) > 0){
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
            $banner[] = array(
                'img_url' => $pic['picpath'],
                'target_url' => $pic['linkurl'],
                'info' => $data,
            );
        }

        $resp = array(
            'recommend_first' => $recommend_first_list,
            'recommend' => $recommend_list ,
            'list' => $hot_anchor,
            'banner' => $banner,
            'category' => $categories,
        );
        $this->responseSuccess($resp);
    }
     /**
     * 频道主播列表
     * @param  string $approve 城市
     * @return string   token   通行证
     */
    public function channelList($approve = null , $token){
        $fields = 'curroomnum, id, sid, nickname, snap, province, city, online, onlinenum, virtualguest, isvirtual,avatartime, channel_id,broadcasting';
        $resp = array();
        if( empty($approve) || $approve == null ){
            $approve_list = M('usersort')->where(' parentid != 0 and isapprove = "1"')->select();
            foreach ($approve_list as $key => $approve_list_one) {
                $member_list = M('member')->where(" approveid = '".$approve_list_one['sortname']."'")->field($fields)->order("broadcasting desc")->limit(2)->select();
                foreach ($member_list as $key => $member_list_one) {
                    $userStream = M("backstream")->where(array('streamstatus'=>'1',"roomid"=>$member_list_one['curroomnum']))->field('id,title,topics,starttime')->find();
                    //是否是私密房间
                    $private = 0;
                    if(!empty($userStream['id'])){
                        if(count(M('privatelimit')->where("bsid=".$userStream['id'])->select()) > 0){
                            $private = 1;
                        }
                    }
                    $member_list[$key]['starttime'] = $userStream['starttime'];
                    $member_list[$key]['title'] = $userStream['title'];
                    $member_list[$key]['topics'] = $userStream['topics'];
                    $member_list[$key]['bsid'] = $userStream['id'];
                    $member_list[$key]['private'] = $private;            
                    $member_list[$key]['avatar'] = getAvatar($member_list_one['avatartime'],$member_list_one['id'], 'middle');
                    $member_list[$key]['snap'] = getAvatar($member_list_one['avatartime'],$member_list_one['id'], 'yuan');
                }
                if( count($member_list) >= 1 ){
                    $resp[] = array(
                        'approve' => $approve_list_one['sortname'],
                        'list'  =>  $member_list, 
    		        );
                }
            }

        }else{
            if( count( M('usersort')->where(' parentid != 0 and isapprove = "1" and sortname="'.$approve.'"')->select() ) > 0 ){
                $resp = M('member')->where(" approveid = '".$approve."'")->order("broadcasting desc")->field($fields)->select();
                foreach ($resp as $key => $resp_one) {
                    $userStream = M("backstream")->where(array('streamstatus'=>'1',"roomid"=>$resp_one['curroomnum']))->field('id,title,topics,starttime')->find();
                    //是否是私密房间
                    $private = 0;
                    if(!empty($userStream['id'])){
                        if(count(M('privatelimit')->where("bsid=".$userStream['id'])->select()) > 0){
                            $private = 1;
                        }
                    }
                    $resp[$key]['starttime'] = $userStream['starttime'];
                    $resp[$key]['title'] = $userStream['title'];
                    $resp[$key]['topics'] = $userStream['topics'];
                    $resp[$key]['bsid'] = $userStream['id'];
                    $resp[$key]['private'] = $private;            
                    $resp[$key]['avatar'] = getAvatar($resp_one['avatartime'],$resp_one['id'], 'middle');
                    $resp[$key]['snap'] = getAvatar($resp_one['avatartime'],$resp_one['id'], 'yuan');
                }
            }else{
                $this->responseError(L('_PARAM_ERROR_'));
            }
        }
        $this->responseSuccess($resp);
    }
    public function followRecommend(){
        $fields = 'curroomnum, id, sid, nickname, snap, province, city, online, virtualguest, isvirtual,avatartime, onlinenum, broadcasting';
        $recommend_list = M('member')->field($fields)->where("idxrec = 'y' ")->limit(20)->select();
        foreach ($recommend_list as $key => $recommend_list_one) {
            $recommend_list[$key]['avatar'] = getAvatar($recommend_list_one['avatartime'],$recommend_list_one['id'], 'middle');
            $recommend_list[$key]['snap'] = getAvatar($recommend_list_one['avatartime'],$recommend_list_one['id'], 'yuan');
        }
        $this->responseSuccess(array('list'=>$recommend_list));
    }
}

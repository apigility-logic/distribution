<?php
/**
 * Created by PhpStorm.
 * User: shellvon
 * Date: 16/4/29
 * Time: 下午10:46
 */

class searchAction extends BaseAction
{

    const DEFAULT_PAGE_SIZE  = 20;

    /**
     * 搜索接口.
     *
     * 参数:query 关键字.
     * 参数:page  当前页,默认1.
     */
    public function index()
    {
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $query = isset($_GET['query']) ? $_GET['query']: null;
        $this->assign('list', array());
        if (!ctype_digit($page)) {
            $page = 1;
        }
        import('ORG.Util.Page');
        if (empty($query) || !is_string($query)) {
            $this->assign('error_msg', '你都不给我房间号和昵称我怎么找呢?');
            $this->display();
            exit();
        }
        if (isset($_SESSION['uid'])) {
            $where = 'id != '. $_SESSION['uid'];
        } else {
            $where = '1=1';
        }
        //TODO: SQL inject.
        if (ctype_digit($query)) {
            //不准搜索自己.
            $where .= ' and ( id = '.$query.' or curroomnum = '.$query.' or nickname like'."'%{$query}%')";
        } else {
            if (preg_match('/\s/u', $query)) {
                $this->assign('error_msg', '输入不对哦~');
                $this->display();
                exit();
            }
            $where .= " and nickname like '%{$query}%'";

        }
        $model =  M('member');
        $count = $model->where($where)->count();
        $page_obj = new Page($count);
        $current_page = $page;
        $list = $model->where($where)->order('earnbean')->page($current_page.', 20')->select();
        $list = !$list ? array() : $list;
        $mem_config = C('MEM_CACHE');
        list($ip, $port) =  explode(':', $mem_config['mem_server']);
        $memc_obj = new Memcached();
        $memc_obj->addServer($ip, $port);
        // TODO: 坑爹.这种效率太低.
        foreach ($list as $k => $anchor) {
            $level = getEmceelevel($anchor['earnbean']);
            $list[$k]['emceelevel'] = isset($level[0]) ? $level[0]['levelid'] : 0;
            $virtual_guest = 0;
            if ($anchor['isvirtual'] == 'y' && $anchor['virtualguest'] > 0) {
                //当前房间虚拟
                $virtual_guest = (int)$anchor['virtualguest'];
            }
            $online_key = C('ROOM_ONLINE_NUM_PREFIX').$anchor['curroomnum'];
            $online_info = $memc_obj->get($online_key);
            if ($online_info !== false) {
                $online_info = json_decode($online_info, true);
                $real_cnt = (int)$online_info['all_num'];
            } else {
                $real_cnt = 0;
            }
            $list[$k]['online'] = $virtual_guest + $real_cnt;
        }
        $show = $page_obj->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('list',$list);// 赋值数据集
        $this->assign('keyword', $query);
        $this->display(); // 输出模板
    }
}
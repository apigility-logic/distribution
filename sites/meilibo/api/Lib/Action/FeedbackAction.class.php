<?php
class FeedbackAction extends BaseAction
{
    function Save() {

        $data = array();
        $data['content'] = empty($_REQUEST['content']) ? '' : trim($_REQUEST['content']);
        $data['version'] = empty($_REQUEST['version']) ? '' : trim($_REQUEST['version']);
        $data['uid'] = empty($_REQUEST['uid']) ? '' : intval($_REQUEST['uid']);
        $data['model'] = empty($_REQUEST['model']) ? '' : trim($_REQUEST['model']);

        $data['date'] = date("Y-m-d H:i:s");

        M("Feed")->add($data);

        echo json_encode(array('status'=>0));
    }

    function fetchData() {
        $pageSize = 20;
        $page = empty($_REQUEST['page']) ? 1 : intval($_REQUEST['page']);

        $startNum = ($page-1)*$pageSize;

        $list = M()->query("select a.content,a.version,a.model,a.date,a.uid,b.nickname from ss_feed as a LEFT JOIN ss_member as b on a.uid = b.id ORDER BY  a.id DESC LIMIT {$startNum},{$pageSize}");

        echo json_encode($list);
    }
}

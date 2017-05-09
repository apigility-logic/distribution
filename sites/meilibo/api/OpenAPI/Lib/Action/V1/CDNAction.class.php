<?php

class CDNAction extends BaseAction {

	public function getPushAddr($roomID)
	{
		if (!is_numeric($roomID)) {
			$this->responseError('房间号错误');
		}

		return "rtmp://wsp1.meilibo.net/live/{$roomID}";
	}

	public function getPullAddr($roomID)
	{
		if (!is_numeric($roomID)) {
			$this->responseError('房间号错误');
		}

		return "rtmp://wsrtmp.meilibo.net/live/{$roomID}";
	}

	public function livestart()
	{
		if (!isset($_GET['id'])) {
			return ;
		}
		M()->execute("update ss_member set broadcasting='y' where curroomnum=".$_GET['id']);
		echo '1';
	}

	public function liveend()
	{
		file_put_contents('/tmp/steam.log', json_encode($_GET), FILE_APPEND);
	}
}
<?php
/**
* Config
*/
define("JM_API_KEY","d6c4ba516dc1702bf597fd3d");

define("JM_API_SER","b67b8a6bc75420df78cb3799");

$res = request("https://api.im.jpush.cn/v1/users/meilibo1383/blacklist","","GET");
var_dump($res);

exit;

//系统极光账号初始化
initAdmin();
exit;
$con = mysql_connect("localhost","root","password");
if (!$con){	die('Could not connect: ' . mysql_error());	}
mysql_query("set character set 'utf8'");//读库 
mysql_select_db("meilibo1", $con);

$result = mysql_query("SELECT id,username,nickname,sex FROM ss_member");
$url = 'https://api.im.jpush.cn/v1/users/';

while($row = mysql_fetch_array($result))
{
	$params = array(
		array('username' => 'meilibo'.$row['id'],
		'password' => 'meilibo'.$row['id'],
		// 'nickname' => $row['nickname'],
		'appkey'   => $apiKey,
		'gender'   => $row['sex'] == 0 ? 1: 2,
        ));
	request($url, json_encode($params), 'POST');

	$params = array(
		'nickname' => $row['nickname'],
        );
	request($url.'meilibo'.$row['id'], json_encode($params), 'PUT');
}
mysql_close($con);





function request($url, $params, $method)
{
	$header[] = 'Authorization: Basic ' . base64_encode(JM_API_KEY.':'.JM_API_SER );
    $header[] = 'Accept: application/json';
    $header[] = 'Content-Type: application/json';
            
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    
	$response = curl_exec($ch);
	curl_close($ch);
    echo "$response\n";
}


function initAdmin() {
	$data = array("username"=>"admin",'password'=>"123456");

	$res = request('https://api.im.jpush.cn/v1/admins/', json_encode($data), 'POST');
	
        $params = array(
                'nickname' => "系统消息",
        );

	request('https://api.im.jpush.cn/v1/users/admin', json_encode($params), 'PUT');
}

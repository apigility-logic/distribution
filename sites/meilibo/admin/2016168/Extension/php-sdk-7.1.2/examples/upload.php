<?php
require_once __DIR__ . '/../autoload.php';

// 引入鉴权类
use Qiniu\Auth;

// 引入上传类
use Qiniu\Storage\UploadManager;
// 'ACCESS_KEY'=>'OoNC8Q_Mft9s_vAfp_Wad0sUD8SKx8OGRhnMl62M',
// 'SECRET_KEY'=>'NmGMF6RJL2o1LNN2Tb_MQ6Jl4VIrtd-ToNyqVLsU',
// 需要填写你的 Access Key 和 Secret Key
$accessKey = 'OoNC8Q_Mft9s_vAfp_Wad0sUD8SKx8OGRhnMl62M';
$secretKey = 'NmGMF6RJL2o1LNN2Tb_MQ6Jl4VIrtd-ToNyqVLsU';

// 构建鉴权对象
$auth = new Auth($accessKey, $secretKey);

// 要上传的空间
$bucket = 'meilibo-video';

// 生成上传 Token
$token = $auth->uploadToken($bucket);

// 要上传文件的本地路径
$filePath = 'E:/study/project/AppServ/www/qiniu/php-sdk-7.1.2/examples/php-logo.png';

// 上传到七牛后保存的文件名
$key = 'my-php-logo.png';

// 初始化 UploadManager 对象并进行文件的上传。
$uploadMgr = new UploadManager();

// 调用 UploadManager 的 putFile 方法进行文件的上传。
list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
echo "\n====> putFile result: \n";
if ($err !== null) {
    var_dump($err);
} else {
    var_dump($ret);
}

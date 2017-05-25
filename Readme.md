# 喵榜直播

## 目录说明

### 数据目录
- `data` Docker容器产生的数据
- `data/mysql/conf`  自定义的mysql.cnf配置项
- `data/mysql/data`  MySQL管理的数据库文件
- `data/php/session` PHP产生的session会话数据

### 网站代码目录
- `sites` 网站代码目录列表
- `sites/meilibo`  美丽播程序根目录
- `sites/meilibo/admin` 美丽播子工程：管理后台
- `sites/meilibo/api`   美丽播子工程：App接口
- `sites/meilibo/admin` 美丽播子工程：聊天室服务端
- `sites/figo` 喵榜二开工程目录：夺宝+拼团功能



## 使用Docker管理运行环境
请查看 docker-compose.yml 文件，了解Docker环境的结构
```bash
# 启动环境
docker-compose up
```
- 访问 http://localhost:8000 打开PC端网站
- 访问 localhost:3388 连接MySQL服务 （帐号密码参考docker-compose.yml中的配置）

## 域名绑定与反向代理
本项目需要4个域名：
- `zhibo.mimilove520.com` APP接口子工程访问
- `admin.mimilove520.com` 管理后台访问
- `chatroom.mimilove520.com` 聊天室服务器访问
- `eadmin.mimilove520.com`  夺宝+拼团功能，二开工程的访问

> 这4个域名中，除了聊天室服务访问外的3个域名都需要用
 nginx做反向代理，`nginx-servers.conf` 文件可以直接include到`nginx.conf`中。

> 在本地开发时，可以通过修改系统hosts文件的方式，把以上4个域名指向到127.0.0.1

## 开发过程如何更新服务器代码
```bash
ssh root@120.76.237.232
cd /var/docker/miaobang_server
git pull
```

## 帐号

```
# 服务器帐号
120.76.237.232(公)
10.26.88.101(内)
root meilibo123$%^
```
```
# 管理后台默认帐号
admin
admin0987
```

```
七牛：
www.qiniu.com
app@figo.cn
Appfigo1607
账号：miao@miaobang520.com
登陆密码：Haomao2017
```
## 异步进程
解决主播在线状态问题的异步脚本
`meilibo/api/cron.php` 
```bash
docker-compose exec meilibo_api bash
nohup php cron.php &
```

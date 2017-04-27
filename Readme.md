# 喵榜直播

## 目录说明

### 数据目录
- data ： Docker容器产生的数据
- data/mysql/conf : 自定义的mysql.cnf配置项
- data/mysql/data : MySQL管理的数据库文件
- data/php/session : PHP产生的session会话数据

### 网站代码目录
- sites : 网站代码目录列表
- sites/tpshop : tpshop程序根目录



##　使用Docker管理运行环境
请查看 docker-compose.yml 文件，了解Docker环境的结构
```bash
# 启动环境
docker-compose up
```
- 访问 http://localhost:8000 打开网站
- 访问 localhost:3388 连接MySQL服务 （帐号密码参考docker-compose.yml中的配置）
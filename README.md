# ThinkPHP WebSocket Register 服务

一个简单的ThinkPHP Socket 扩展中的Register服务
本服务主要负责协调Gateway与Business之间建立TCP长连接通讯

## 安装

~~~
composer require axguowen/think-socket-register
~~~

## 配置

首先配置config目录下的socketregister.php配置文件。
配置项说明：

~~~php
return [
    // Register进程名称, 方便status命令中查看统计
    'name' => 'think-socket-register',
    // Register服务监听IP, 分布式部署时请填写本机内网IP地址
    'listen' => '127.0.0.1',
    // Register服务监听端口，Register端口千万不能开放给外网，否则可能遭受攻击。
    // 客户端不要连接Register服务的端口，Register服务是Socket内部通讯用的。
    'port' => 1236,
    // Gateway通讯密钥
    'secret_key' => '',
    // 是否允许reload
    'reloadable' => false,
    // 是否以守护进程启动
    'daemonize' => false,
];
~~~

## 启动停止

定时任务的启动停止均在命令行控制台操作，所以首先需要在控制台进入tp目录

### 启动命令

~~~
php think socket:register start
~~~

要使用守护进程模式启动可以将配置项deamonize设置为true
或者在启动命令后面追加 -d 参数，如下：
~~~
php think socket:register start -d
~~~

### 停止
~~~
php think socket:register stop
~~~

### 查看进程状态
~~~
php think socket:register status
~~~

## 注意
Windows下不支持多进程设置，也不支持守护进程方式运行，正式生产环境请用Linux
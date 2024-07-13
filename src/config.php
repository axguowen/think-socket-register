<?php
// +----------------------------------------------------------------------
// | ThinkPHP Socket Register [Socket Register Service For ThinkPHP]
// +----------------------------------------------------------------------
// | ThinkPHP Socket Register 服务
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: axguowen <axguowen@qq.com>
// +----------------------------------------------------------------------

return [
    // Register进程名称, 方便status命令中查看统计
    'name' => 'think-socket-register',
    // Register服务监听IP, 分布式部署时请填写本机内网IP地址
    'listen' => '127.0.0.1',
    // Register服务监听端口，Register端口千万不能开放给外网，否则可能遭受攻击。
    // 客户端不要连接Register服务的端口，Register服务是Socket内部通讯用的。
    'port' => 1236,
    // Socket通讯密钥
    'secret_key' => '',
    // 是否允许reload
    'reloadable' => false,
    // 是否以守护进程启动
    'daemonize' => false,
];

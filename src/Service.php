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

namespace think\socket\register;

class Service extends \think\Service
{
    /**
     * 注册服务
     * @access public
     * @return void
     */
    public function register()
    {
        // 设置命令
        $this->commands([
            'socket:register' => Command::class,
        ]);
    }
}

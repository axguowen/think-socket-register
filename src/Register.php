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

use think\App;
use think\console\Output;
use think\console\Input;
use Workerman\Worker;
use GatewayWorker\Register as GatewayRegister;

class Register
{
    /**
     * 配置参数
     * @var array
     */
	protected $options = [
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
        // 内容输出文件路径
        'stdout_file' => '',
        // pid文件路径
        'pid_file' => '',
        // 日志文件路径
        'log_file' => '',
	];

    /**
     * 容器实例
     * @var App
     */
    protected $app;

    /**
     * 架构函数
     * @access public
     * @param App $app 容器实例
     * @return void
     */
    public function __construct(App $app)
    {
        // 记录容器实例
        $this->app = $app;
        // 合并配置
		$this->options = array_merge($this->options, $this->app->config->get('socketregister', []));
        // 初始化
		$this->init();
    }

    /**
     * 初始化
     * @access protected
	 * @return void
     */
	protected function init()
	{
        // 如果监听地址为空
        if(empty($this->options['listen'])){
            // 抛出异常
            throw new \Exception('listen can not be empty');
        }
        // 如果端口不合法
        if(!is_numeric($this->options['port']) || $this->options['port'] < 0 || $this->options['port'] > 65535){
            // 抛出异常
            throw new \Exception('port must be a number between 0 and 65535');
        }
	}

    /**
     * 启动
     * @access public
     * @param Input $input 输入
     * @param Output $output 输出
	 * @return void
     */
	public function start(Input $input, Output $output)
	{
        // 不是控制台模式
        if (!$this->app->runningInConsole()) {
            // 抛出异常
            throw new \Exception('only supports running in cli mode');
        }

        // 如果是守护进程模式
        if ($input->hasOption('daemon')) {
            // 修改配置为守护进程模式
            $this->options['daemonize'] = true;
        }

        // 进程名称为空
		if(empty($this->options['name'])){
            $this->options['name'] = 'think-socket-register';
        }
        // 构造新的运行时目录
		$runtimePath = $this->app->getRuntimePath() . $this->options['name'] . DIRECTORY_SEPARATOR;
        // 设置runtime路径
        $this->app->setRuntimePath($runtimePath);

        // 主进程reload
		Worker::$onMasterReload = function () {
			// 清理opcache
            if (function_exists('opcache_get_status')) {
                if ($status = opcache_get_status()) {
                    if (isset($status['scripts']) && $scripts = $status['scripts']) {
                        foreach (array_keys($scripts) as $file) {
                            opcache_invalidate($file, true);
                        }
                    }
                }
            }
        };

        // 内容输出文件路径
		if(!empty($this->options['stdout_file'])){
			// 目录不存在则自动创建
			$stdout_dir = dirname($this->options['stdout_file']);
			if (!is_dir($stdout_dir)){
				mkdir($stdout_dir, 0755, true);
			}
			// 指定stdout文件路径
			Worker::$stdoutFile = $this->options['stdout_file'];
		}
		// pid文件路径
		if(empty($this->options['pid_file'])){
			$this->options['pid_file'] = $runtimePath . 'worker' . DIRECTORY_SEPARATOR . $this->options['name'] . '.pid';
		}

		// 目录不存在则自动创建
		$pid_dir = dirname($this->options['pid_file']);
		if (!is_dir($pid_dir)){
			mkdir($pid_dir, 0755, true);
		}
		// 指定pid文件路径
		Worker::$pidFile = $this->options['pid_file'];

        // 日志文件路径
		if(empty($this->options['log_file'])){
			$this->options['log_file'] = $runtimePath . 'worker' . DIRECTORY_SEPARATOR . $this->options['name'] . '.log';
		}
		// 目录不存在则自动创建
		$log_dir = dirname($this->options['log_file']);
		if (!is_dir($log_dir)){
			mkdir($log_dir, 0755, true);
		}
		// 指定日志文件路径
		Worker::$logFile = $this->options['log_file'];

        // 如果指定以守护进程方式运行
        if (true === $this->options['daemonize']) {
            Worker::$daemonize = true;
        }

        // 实例化register进程, 必须是text协议
        $register = new GatewayRegister('text://' . $this->options['listen'] . ':' . $this->options['port']);
        // register名称，status方便查看
        $register->name = $this->options['name'];
        // Socket通讯密钥
        $register->secretKey = $this->options['secret_key'];
        // 是否允许reload
        $register->reloadable = $this->options['reloadable'];
        
        // 启动
		Worker::runAll();
	}

    /**
     * 停止
     * @access public
     * @return void
     */
    public function stop()
    {
        Worker::stopAll();
    }
}

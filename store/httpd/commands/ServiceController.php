<?php

namespace store\httpd\commands;

use mix\console\Controller;
use mix\swoole\Process;

/**
 * 服务控制器
 * @author 刘健 <coder.liu@qq.com>
 */
class ServiceController extends Controller
{

    // 是否后台运行
    protected $d = false;

    // 是否热更新
    protected $u = false;

    // 启动服务
    public function actionStart()
    {
        if ($pid = Process::getMasterPid(\Mix::app()->objects['httpServer']['setting']['pid_file'])) {
            return "mix-httpd is running, PID : {$pid}." . PHP_EOL;
        }
        $server = \Mix::app()->createObject('httpServer');
        if ($this->u) {
            $server->setting['max_request'] = 1;
        }
        $server->setting['daemonize'] = $this->d;
        return $server->start();
    }

    // 停止服务
    public function actionStop()
    {
        if ($pid = Process::getMasterPid(\Mix::app()->objects['httpServer']['setting']['pid_file'])) {
            Process::kill($pid);
            while (Process::isRunning($pid)) {
                // 等待进程退出
                usleep(100000);
            }
            return 'mix-httpd stop completed.' . PHP_EOL;
        } else {
            return 'mix-httpd is not running.' . PHP_EOL;
        }
    }

    // 重启服务
    public function actionRestart()
    {
        $this->actionStop();
        $this->actionStart();
    }

    // 查看服务状态
    public function actionStatus()
    {
        if ($pid = Process::getMasterPid(\Mix::app()->objects['httpServer']['setting']['pid_file'])) {
            return "mix-httpd is running, PID : {$pid}." . PHP_EOL;
        } else {
            return 'mix-httpd is not running.' . PHP_EOL;
        }
    }

}

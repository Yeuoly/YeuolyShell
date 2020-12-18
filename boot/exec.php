<?php
namespace YeuolyShellExec;

require_once('../boot/include.php');
require_once('../conf/config.php');
require_once('../boot/class/router.php');

use Request\Request;
use Throwable;
use YeuolyShellConfig\Config;
use YeuolyShellInclude\Includer;
use YeuolyShellRouter\Router;
use YeuolyShellError\Error;
use YeuolyShellDB\DataBase;

class Exec{
    private $router;
    
    private $config;

    private $includer;

    private $request;

    private $db;

    public function __construct()
    {
        //引入基本文件
        $this->config = new Config();
        $this->router = new Router($this->config);
        $this->includer = new Includer($this->config, $this->router);
        $this->includeBaseInfo();
        $this->db = new DataBase($this->config);
        $this->request = new Request($_SERVER);
    }

    public function run(){
        $response = null;
        try{
            $response = $this->router->visitRoute($this->request, $this->db);
        }catch(Throwable $e){
            new Error(500, $e->getMessage());
            exit;
        }
        $response->loadOptions($this->request);
        $response->release($this->config);
    }

    protected function includeBaseInfo(){
        //载入基础类
        $this->includer->loadBaseClass();
        //载入控制器
        $this->includer->loadControllers();
        //载入验证器
        $this->includer->loadValidators();
        //载入路由
        $this->includer->loadRouter();
        //载入中间件
        $this->includer->loadMiddleWare();
    }
}
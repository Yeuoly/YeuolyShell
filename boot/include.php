<?php

namespace YeuolyShellInclude;

use function YeuolyShellRouter\initRoutes\__addRoutes;

class Includer{
    private $controllers_dirs = ['controller'];

    private $validators_dirs = ['validate'];

    private $config;

    private $router;

    public function __construct($config, $router)
    {
        $this->config = $config;
        $this->router = $router;
        $this->controllers_dirs = $config->getNamespaces('controller');
        $this->validators_dirs = $config->getNamespaces('validate');
    }

    public function loadControllers(){
        foreach($this->controllers_dirs as $path){
            $dir = $this->config->getAppPath() . '/controller/' . $path;
            $handle = opendir($dir);

            while($file = readdir($handle)){
                if($file != '...' && substr($file, -4) == '.php'){
                    require_once($dir . '/' . $file);
                }
            }
        }
    }

    public function loadValidators(){
        foreach($this->validators_dirs as $path){
            $dir = $this->config->getAppPath() . '/validate/' . $path;
            $handle = opendir($dir);

            while($file = readdir($handle)){
                if($file != '...' && substr($file, -4) == '.php'){
                    require_once($dir . '/' . $file);
                }
            }
        }
    }

    public function loadBaseClass(){
        $dir = $this->config->getBasePath() . '/boot/class';
        $handle = opendir($dir);

        while($file = readdir($handle)){
            if($file != '...' && substr($file, -4) == '.php'){
                require_once($dir . '/' . $file);
            }
        }
    }

    public function loadRouter(){
        //引入routes文件
        require_once($this->config->getBasePath() . '/conf/routes.php');
        //添加路由
        __addRoutes($this->router);
    }

    public function loadMiddleWare(){
        //引入中间件
        $dir = $this->config->getAppPath() . '/middleware';
        $handle = opendir($dir);

        while($file = readdir($handle)){
            if($file != '...' && substr($file, -4) == '.php'){
                require_once($dir . '/' . $file);
            }
        }
    }
}
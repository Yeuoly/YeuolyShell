<?php

namespace YeuolyShellRouter;

use Error;
use Request\Request;
use Response\Response;
use Throwable;

class Router{
    private $root;
    private $config;

    public function __construct($config)
    {
        $this->root = new Route('root', '');
        $this->config = $config;
    }

    /**
     * 返回一个Route对象，用于最末端的路由，不允许空字符
     */
    public function generateFinalRoute($name, $path, $to){
        if(!$to || !$path){
            return new Error('路由设置错误', 500);
        }
        return new Route($name, $path, $to);
    }

    /**
     * YeuolyShell允许使用树形数组创建路由，拜托了大哥哥们，为了这灵活性我要写吐了，
     * 同时兼容这三种创建路由的方法你知道我有多累吗.jpg
     */
    public function addChildByTree($tree){
        //fxxk  我不想写
    }

    public function addChild($name = 'next', $path = 'next', $to = ''){
        return $this->root->addChild($name, $path, $to);
    }

    /**
     * 这个直接传入url作为路由，不需要像前面addChild一样，这里要求必要有$to
     */
    public function addChildByStr($name = 'next', $path = 'next', $to = ''){
        if(!$to){
            return new Error('路由设置错误', 500);
        }
        //首先拆分路由，并且去除空路由
        $routes = array_filter(explode('/', $path));
        $routes = $routes ? $routes : [''];
        //从root开始往下检索
        $head = $this->root;
        $count = count($routes);
        for($i = 0, $flag = false; $i < $count; $i++, $flag = false){
            //开始搜寻节点子集中是否存在相同路径的路由
            $children = $head->getChildren();
            foreach($children as $t){
                if($t->getCurrentPath() == $routes[$i]){
                    $head = $t;
                    $flag = true;
                    break;
                }
            }
            //找到了，则continue
            if($flag){
                continue;
            }else{
                //没找到就楞加了
                $head = $head->addChild($name, $routes[$i], $i == $count - 1 ? $to : '');
            }
        }
        return $head;
    }

    protected function analysisRoute($origin_route){
        return array_slice(explode('/', explode('?', $origin_route, 2)[0]), 2);
    }

    /**
     * 这个的执行结果会最终被返回到主级中作为请求返回
     */
    public function visitRoute(Request $request, $db){
        $route = $this->analysisRoute($request->path['ROUTE']);
        $route_index_max = count($route) - 1;
        $current_route = $this->root;
        for($i = 0; $i <= $route_index_max; $i++){
            $temp_route = null;
            foreach($current_route->getChildren() as $child){
                if($child->getCurrentPath() == $route[$i]){
                    $temp_route = $child;
                    break;
                }
            }
            if(!$temp_route){
                throw new Error('路由规则错误-1');
            }
            $current_route = $temp_route;
        }
        $target = $current_route->getTargetController();
        if(!$target['method']){
            throw new Error('路由规则错误-2');
        }
        $allow_origin = $this->config->getAllowList('ORIGIN');
        //封装闭包函数，next的返回结果为一个Response对象
        $next = function() use ( $target, $request, $db, $allow_origin ) {
            $response = null;
            try{
                $response = @call_user_func_array(['Controller'.'\\'.$target['controller'].'\\'.$target['controller'],$target['method']],[$request, $db]);
            }catch(Throwable $e){
                throw new Error($e->getMessage());
            }
            if(is_string($response) || !$response){
                $response = new Response($response ? $response : '', 200, ['ACCESS-CONTROL-ALLOW-ORIGIN' => $allow_origin]);
            }else if(is_object($response) && get_class($response) === 'YeuolyShellView\\View'){
                $response = new Response($response->render(), 200);
            }
            return $response;
        };
        //检测中间件
        if($target['middleware']){ 
            try{
                return @call_user_func_array(['MiddleWare'.'\\'.$target['middleware'].'\\'.$target['middleware'], 'handle'], [$request, $next]);
            }catch(Throwable $e){
                throw new Error($e->getMessage());
            }
        }else{
            
            return $next();
        }
    }
}

class Route{
    private $children = [];

    private $name = '';

    private $path = '';

    private $target = [
        'dir' => '',
        'controller' => '',
        'method' => '',
        'middleware' => ''
    ];

    private $full_path = [];

    public function __construct($name = 'next', $path = 'next',  $target = '', Route $parent = null)
    {
        $this->name = $name;
        $this->path = $path;
        
        if($target){
            $this->transTarget($target);
        }
        if($parent){
            $this->full_path = array_merge($parent->getFullPath(),[$path]);
        }
    }

    protected function transTarget($target){
        $split = explode('/', $target);
        if(count($split) !== 3){
            throw new Error('路由参数错误');
            return;
        }
        $this->target['dir'] = $split[0];
        $this->target['controller'] = $split[1];
        $this->target['method'] = $split[2];
    }

    public function getFullPath(){
        return $this->full_path;
    }

    public function getCurrentPath(){
        return $this->path;
    }

    public function getName(){
        return $this->name;
    }

    public function getTargetController(){
        return $this->target;
    }

    public function getChildren(){
        return $this->children;
    }

    public function addChild($name = 'next', $path = 'next', $to = ''){
        //首先要判断是否存在一个一样的child，如果存在就直接返回该路由
        foreach($this->children as $i){
            if($i->getCurrentPath() == $path){
                return $i;
            }
        }
        $child = new Route($name, $path, $to, $this);
        array_push($this->children, $child);
        return $child;
    }

    public function middleware($name){
        $this->target['middleware'] = $name;
    }
}

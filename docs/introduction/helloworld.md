编写index.php为如下：

```php
<?php
namespace Controller\Index;

use Controller\Controller;
use YeuolyShellDB\DataBase;
use Request\Request;

class Index extends Controller{
    public function index(Request $request, DataBase $database){
        return 'Hello World';
    }
}
```

像这样，我们定义了一个`index`方法，其接收两个参数：

`$request`包含了请求信息，请求传参也都在这里<br>
`$database`为数据库操作对象，下面会详细介绍<br>

定义完成后还没完事，我们还需要定义路由<br>
进入到conf/routes.php
```php
<?php

namespace YeuolyShellRouter\initRoutes;

use YeuolyShellRouter\Router;

function __addRoutes(Router $router){
    $router->addChildByStr('index','','controller/index/index');
}
```

注意核心语句：
```php
$router->addChildByStr('index','','controller/index/index');
```
这段代码表示添加一个路由，其名称为`index`，其路径为空，其指定的控制器函数为`controller`空间下的`index`控制器的`index`函数，也就是我我们刚刚返回`Hello World`的那个函数

现在，我们完成了一个最简单的`Hello World`操作，进入我们的网站看一看吧！

![](https://yeuoly.oss-cn-beijing.aliyuncs.com/csust2020/homework/20201220/bb7b1c55b514b4274817f6b314ca0f70.png)

如果你的界面如下，那么恭喜您，成功完成了`YeuolyShell`的配置，并且相信您已经了解了`YeuolyShell`的基本配置
`YeuolyShell`也支持中间件的编写，下面我们演示如果配置与使用中间件

我们首先创建一个`admin`中间件，其目录为`app/middleware/admin.php`

<br>
admin.php

```php
<?php

namespace MiddleWare\Admin;

use Closure;
use Request\Request;
use Response\Response;

class Admin{
    public function handle(Request $request, Closure $next){
        $post = $request->post;
        if($post['username'] != 'root' || $post['password'] != '123456'){
            return new Response('您没有访问权限');
        }
        return $next();
    }
}
```
可以看出来我们这个中间件的逻辑，如果用户名和密码都正确，则继续执行请求（即执行`$next`闭包），否则直接返回一个您没有访问权限

接下来就是把中间件配置到路由上了，去到`conf/routes.php`

<br>
routes.php

```php
<?php

namespace YeuolyShellRouter\initRoutes;

use YeuolyShellRouter\Router;

function __addRoutes(Router $router){
    $router->addChildByStr('index','','controller/index/index')->middleware('admin');
}
```
对比我们一开始的配置，发现我添加了一个`middleware`方法，这个方法就表示我们这个路由使用了`admin`中间件，现在再去访问网站

![](https://yeuoly.oss-cn-beijing.aliyuncs.com/csust2020/homework/20201220/b0c4af3354530906116d66a6062b3700.png)

您没有权限访问几个大字就出现了，那么相信您现在应该已经理解了中间件的使用
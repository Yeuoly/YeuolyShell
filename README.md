# YeuolyShell

大家好，我是Yeuoly，这个憨憨PHP框架的作者，这已经是我第二次写这个文档了。第一次写的时候电脑硬盘全烧了，数据全没了，于是就有了段抱怨的话=，=

进入正题，这是个PHP框架，主打速度和安全，在安全方面我还是下了功夫的，比起TP LA等主流PHP框架，我这个非常轻量，在路由方面，我没有提供直接的从URL到控制器的访问，必须要由开发者定义路由才允许访问目标函数，还有很多其他的功能，我会在接下来一一介绍

## 环境配置
环境配置主要就是伪静态和mysql了，目前YeuolyShell只支持mysql，后续会添加其他的。这里只提供一个nginx的伪静态配置，Windows用户的WEB初学者建议使用PHPStudy

### 伪静态配置
<br>

vhost.conf
```nginx
root /var/www/your_website_path/public;
index index.php;

rewrite ^(.*)$ /index.php?s=$1 last;
rewrite ^/index.php(.*)$ /index.php?s=$1 last;
```

<br>

### 数据库配置

conf/conf.php
```php
const DB_INFO = [
    'DB_HOST' => '127.0.0.1',
    'DB_USER' => '用户名',
    'DB_PASSWORD' => '密码',
    'DB_NAME' => '数据库名',
    'DB_TYPE' => 'mysql',
    'DB_TABLE_PREPEND' => '表前缀',
    'DB_COLUMN_PREPEND' => '列前缀'
];

```
这里列出了两个比较重要的配置，另外俩可以暂时不用管<br>
DN_INFO，故名思意，就是数据库配置，需要注意的是最后两个前缀，当添加前缀之后，YeuolyShell在进行数据库操作时，会默认加上前缀，如：
```
DB_TABLE_PREPEND = np_
```
然后我要查询table表，则YeuolyShell会事先处理表名为np_table，然后再进行查询，列前缀类似

### 跨域配置

conf/conf.php
```php
const ALLOW_LIST = [
    'ORIGIN' => 'www.srmxy.cn'
    // [
    //     'danmuji.srmxy.cn',
    //     'api.ypm.srmxy.cn'
    // ]
];
```
YeuolyShell会先判断访问源是否在ALLOW_LIST中，如果在，就会在HTTP头部添加ACCESS-CONTROL-ALLOW-ORIGIN: domain，如果不在，就会直接返回一个403的状态，ORIGIN可以是字符串也可以是数组

### 控制器空间配置
在YeuolyShell中，我们支持多空间的控制器

conf/conf.php
```php
const YS_NAMESPACE = [
    'controller' => [
        'controller'
    ],
    'validate' => [
        'validate'
    ]
];
```
现在，如上图配置，我们指定了一个控制器空间，其名称为controller，于是，我们可以在app/controller下创建文件夹：controller，这个就是我们刚刚指定的controller空间，于是我们现在的文件结构为：app/controller/controller，然后，在这个空间下创建一个文件：index.php，其路径为：app/controller/controller/index.php，填充如下内容

<br>

index.php
```php
<?php
namespace Controller\Index;

use Controller\Controller;
use YeuolyShellDB\DataBase;
use Request\Request;

class Index extends Controller{

}
```

注意，该文件的空间要求为：Controller/文件名，且要求这个文件下的类名要与文件名一致，文件名小写，类名首字符大写

现在我们完成了一个简单的控制器类定义，接下来我们来实现一个HelloWorld

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

像这样，我们定义了一个index方法，其接收两个参数：

$request包含了请求信息，请求传参也都在这里<br>
$database为数据库操作对象，下面会详细介绍<br>

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
这段代码表示添加一个路由，其名称为index，其路径为空，其指定的控制器函数为controller空间下的index控制器的index函数，也就是我我们刚刚返回Hello World的那个函数

现在，我们完成了一个最简单的Hello World操作，进入我们的网站看一看吧！

![](https://yeuoly.oss-cn-beijing.aliyuncs.com/csust2020/homework/20201220/bb7b1c55b514b4274817f6b314ca0f70.png)

如果你的界面如下，那么恭喜您，成功完成了YeuolyShell的配置，并且相信您已经了解了YeuolyShell的基本配置

## 数据库操作
YeuolyShell支持灵活方便的数据库操作类，它的具体实现在boot/class/db.php中，如果您有兴趣帮助改进YeuolyShell的话，欢迎加入YeuolyShell的开发！<br>
YeuolyShell使用控制器函数中传入的$database进行数据库操作，希望您已经熟知了这一点

### 创建表
```php
$res = $database->query('table')->create([
    'id' => ['INTEGER','NOT NULL','AUTO_INCREMENT'],
    'username' => ['VARCHAR(32)','NOT NULL'],
    'password' => ['VARCHAR(32)','NOT NULL']
]);
```
以上操作创建了一个table表，这个表包含了三个字段id username password，且其类型和属性为后面的数组，同时我们支持不使用数组的创建

```php
'id' => 'INTEGER NOT NULL AUTO_INCREMENT'
```
这样的字符串也是支持的

其中$res为true或false，代表是否成功

### 插入数据
```php
$res = $database->query('table')->insert([
    'id' => 1,
    'username' => 'Yeuoly',
    'password' => '123456'
]);
```
这样就向table表内插入了一行数据

### 查询数据
```php
$data = $database->query('table')->select();
```
这样就获取了table表内的全部数据

其中$data就是查询到的数据结果，如果查询失败，会返回false
### 条件AND查询
```php
$data = $database->query('table')
    ->where(['id','=',1])
    ->select();
```
以上等价于
```mysql
SELECT * FROM table WHERE id = 1
```

### 多条件AND查询
```php
$data = $database->query('table')
    ->where(['id','=',1,'username','=','Yeuoly'])
    ->select();
```
以上等价于
```mysql
SELECT * FROM table WHERE id = 1 AND username = 'Yeuoly'
```

### 多条件OR查询
```php
$data = $database->query('table')
    ->where([
        ['id','=',1],
        ['username','=','Yeuoly']
    ])
    ->select();
```
以上等价于
```mysql
SELECT * FROM table WHERE id = 1 OR username = 'Yeuoly'
```
可以看出来，AND条件就是全部放在一个数组里，OR就是分开为多个数组

### 混合条件查询
```php
$data = $database->query('table')
    ->where([
        [
            'username','=','yeuoly',
            'password','=','123456'
        ],
        [
            [
                'id','=','1',
                'password','=','root'
            ],
            [
                'security','>','3'
            ],
        ],
    ])
    ->select();
```
以上等价于
```mysql
SELECT * FROM table WHERE ( (username = `yeuoly` AND password = `123456`) OR ( ( id = `1` AND password = `root` ) OR ( security > `3` ) ) )
```
这里有必要说一句，一般其实我们也用不到这么复杂的查询条件，之所以开发这么麻烦一个东西，是因为当初在用ThinkPHP的时候，多条件混合查询它特麻烦，搞得我很烦

### 指定列查询
YeuolyShell支持只查询某几列的数据
```php
$database->query('table')->select(['username','password']);
```
以上等价于
```mysql
SELECT username,password from table
```

同时，YeuolyShell支持列名替换
```php
$database->query('table')->select(['username','password'],['uname','pwd']);
```
这样查出来的数据里列名会被更换为后面那个数组里的字符串，于是我们就可以做一些有趣的事情了

### 组合查询
```php
$database->query('table')
    ->groupBy('username')
    ->orderBy('count')
    ->desc()
    ->select(['username','count(*) as count'],['username','times']);
```
以上表示从table中查询同名用户的数量，并且进行非顺序排序，这里我们顺便接介绍了排序操作，最终出来的结果会有两个键：username, times

### limit语句
```php
$database->query('table')
    ->limit($n)
    ->select();
```
表示最多查n行数据

```php
$database->query('table')
    ->limit(1, 20)
    ->select();
```
表示从第1行数据开始，最后查20行

## 视图层操作
YeuolyShell虽然注重于后端，但也提供了视图层功能

首先我们创建一个视图层空间index，其目录为app/view/index，然后在这个空间下创建一个index.html，其目录为app/view/index/index.html

<br>

index.html
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to YeuolyShell</title>
</head>
<body>
    <h1 style="color: pink;">{{ prop }}</h1>
</body>
</html>
```

注意到h1内，我们使用了{{ prop }}这么一个表达式，这个表达式是用来传递参数的

现在我们回到最开始创建的那个index控制器中，修改其为

index.php
```php
<?php
namespace Controller\Index;

use Controller\Controller;
use YeuolyShellDB\DataBase;
use Request\Request;
use YeuolyShellView\View;

class Index extends Controller{
    public function index(Request $request, DataBase $database){
        return new View('index/index',['prop' => 'Hello World']);
    }
}
```

然后再次访问我们的网站

![](https://yeuoly.oss-cn-beijing.aliyuncs.com/csust2020/homework/20201220/731cb8000c85eedcc3574565e3adb5cd.png)

YeuolyShell所做的事很简单，就是把{{ prop }}替换为了Hello World

同时我们支持XSS过滤，但注意，YeuolyShell的XSS过滤建立在开发者不随意使onclick等事件可控的情况下，如果开发者有这个需要，请自己手写XSS过滤。YeuolyShell仅支持javascript伪协议/引号闭合/尖括号闭合等XSS的过滤，如果开发者写一个onclick={{w}}，那YeuolyShell也无能为力了

现在我们来看一看YeuolyShell的XSS过滤，首先修改Hello World为

```php
return new View('index/index',['prop' => '<script>alert()</script>']);
```

现在还没开启XSS过滤，开启的方式很简单，在创建View时添加一个参数true

```php
return new View('index/index',['prop' => '<script>alert()</script>'], true);
```

现在再访问我们的网站

![](https://yeuoly.oss-cn-beijing.aliyuncs.com/csust2020/homework/20201220/bde099120085d6a8572fcce737f39b81.png)

可以发现，script标签已经被过滤，没有生效，YeuolyShell的XSS过滤对<a href="javascript:xxx"></a>也有效，如果您发现了可以绕过的方法，请及时联系我，非常感谢！

## 传参
YeuolyShell的传参也很简单，只需要调用$request中的变量既可，如：
```php
$get = $request->get;
$post = $request->post;
```

得到的时一个数组，这个数组就是传递的参数了

## 中间件
YeuolyShell也支持中间件的编写，下面我们演示如果配置与使用中间件

我们首先创建一个admin中间件，其目录为app/middleware/admin.php

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
可以看出来我们这个中间件的逻辑，如果用户名和密码都正确，则继续执行请求（即执行$next闭包），否则直接返回一个您没有访问权限

接下来就是把中间件配置到路由上了，去到conf/routes.php

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
对比我们一开始的配置，发现我添加了一个middleware方法，这个方法就表示我们这个路由使用了admin中间件，现在再去访问网站

![](https://yeuoly.oss-cn-beijing.aliyuncs.com/csust2020/homework/20201220/b0c4af3354530906116d66a6062b3700.png)

您没有权限访问几个大字就出现了，那么相信您现在应该已经理解了中间件的使用

## 验证器
爆肝开发中，原来的版本有bug还没修好
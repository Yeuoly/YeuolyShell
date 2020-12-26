# YeuolyShell

大家好，我是Yeuoly，这个憨憨PHP框架的作者，这已经是我第二次写这个文档了。第一次写的时候电脑硬盘全烧了，数据全没了，于是就有了段抱怨的话=，=

进入正题，这是个PHP框架，主打速度和安全，在安全方面我还是下了功夫的，比起TP LA等主流PHP框架，我这个非常轻量，在路由方面，我没有提供直接的从URL到控制器的访问，必须要由开发者定义路由才允许访问目标函数，还有很多其他的功能，我会在接下来一一介绍

## 环境配置
环境配置主要就是`伪静态`和`mysql`了，目前`YeuolyShell`只支持`mysql`，后续会添加其他的。这里只提供一个`nginx的伪静态配置`，Windows用户的WEB初学者建议使用`PHPStudy`

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
`DN_INFO`，故名思意，就是数据库配置，需要注意的是最后两个前缀，当添加前缀之后，`YeuolyShell`在进行数据库操作时，会默认加上前缀，如：
```
DB_TABLE_PREPEND = np_
```
然后我要查询`table`表，则`YeuolyShell`会事先处理表名为`np_table`，然后再进行查询，列前缀类似

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
`YeuolyShell`会先判断访问源是否在`ALLOW_LIST`中，如果在，就会在HTTP头部添加`ACCESS-CONTROL-ALLOW-ORIGIN: domain`，如果不在，就会直接返回一个`403`的状态，`ORIGIN`可以是字符串也可以是数组

### 控制器空间配置
在`YeuolyShell`中，我们支持多空间的控制器

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
现在，如上图配置，我们指定了一个控制器空间，其名称为`controller`，于是，我们可以在`app/controller`下创建文件夹：`controller`，这个就是我们刚刚指定的`controller`空间，于是我们现在的文件结构为：`app/controller/controller`，然后，在这个空间下创建一个文件：`index.php`，其路径为：`app/controller/controller/index.php`，填充如下内容

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

注意，该文件的空间要求为：`Controller/文件名`，且要求这个文件下的类名要与文件名一致，文件名小写，类名首字符大写

现在我们完成了一个简单的控制器类定义，接下来我们来实现一个HelloWorld

[来看一看Hello World的实现吧](./docs/introduction/helloworld.md)

# 下面列举了YeuolyShell一些简单的功能的使用

## [数据库操作](./docs/introduction.md)
`YeuolyShell`支持灵活方便的数据库操作类，它的具体实现在`boot/class/db.php`中，如果您有兴趣帮助改进`YeuolyShell`的话，欢迎加入`YeuolyShell`的开发！<br>
YeuolyShell使用控制器函数中传入的$database进行数据库操作，希望您已经熟知了这一点

## [视图层操作](./docs/view.md)
`YeuolyShell`虽然注重于后端，但也提供了视图层功能

## [传参](./docs/parameters.md)
`YeuolyShell`的传参非常常规

## [中间件](./docs/middleware.md)
`YeuolyShell`的中间件和`ThinkPHP`差不太多，也是非常简单的定义与使用

## [验证器](./docs/validate.md)
爆肝开发中，原来的版本有bug还没修好
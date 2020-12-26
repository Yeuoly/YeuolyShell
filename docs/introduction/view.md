首先我们创建一个视图层空间`index`，其目录为`app/view/index`，然后在这个空间下创建一个`index.html`，其目录为`app/view/index/index.html`

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

注意到h1内，我们使用了`{{ prop }}`这么一个表达式，这个表达式是用来传递参数的

现在我们回到最开始创建的那个`index`控制器中，修改其为

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

`YeuolyShell`所做的事很简单，就是把`{{ prop }}`替换为了`Hello World`

同时我们支持XSS过滤，但注意，YeuolyShell的XSS过滤建立在开发者不随意使onclick等事件可控的情况下，如果开发者有这个需要，请自己手写XSS过滤。YeuolyShell仅支持javascript伪协议/引号闭合/尖括号闭合等XSS的过滤，如果开发者写一个onclick={{w}}，那YeuolyShell也无能为力了

现在我们来看一看`YeuolyShell`的`XSS过滤`，首先修改`Hello World`为

```php
return new View('index/index',['prop' => '<script>alert()</script>']);
```

现在还没开启`XSS过滤`，开启的方式很简单，在创建`View`时添加一个参数 *__true__*

```php
return new View('index/index',['prop' => '<script>alert()</script>'], true);
```

现在再访问我们的网站

![](https://yeuoly.oss-cn-beijing.aliyuncs.com/csust2020/homework/20201220/bde099120085d6a8572fcce737f39b81.png)

可以发现，script标签已经被过滤，没有生效，`YeuolyShell的XSS过滤`对<a href="javascript:xxx"></a>也有效，如果您发现了可以绕过的方法，请及时联系我，非常感谢！
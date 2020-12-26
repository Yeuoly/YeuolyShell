`YeuolyShell`的传参也很简单，只需要调用`$request`中的变量既可，如：
```php
$get = $request->get;
$post = $request->post;
```

得到的时一个数组，这个数组就是传递的参数了

比如要我们要获取`GET`请求中的`name`参数，那么只需要
```php
$name = $request->get['name'];
```
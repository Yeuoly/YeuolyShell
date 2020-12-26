# 关于View模块的开发v1
2020/12/19
## 预期效果
用户创建一个View对象，使用类似
```php
return new View('index/home',['name' => 'Yeuoly','id' => 1]);
```
的操作，实现视图层

`index/home`为目标视图层，index目录下的home模板，至于模板，使用html规范编写，参数使用el表达式的格式，只是需要我们自己实现以下

同时启用XSS过滤，提供基础的XSS功能，用户只需要在构造View的时候设置一下是否开启XSS过滤即可

## 具体实现

### 主要功能
使用`preg_replace`进行替换，将`{{name}}`替换为变量Yeuoly，`{{id}}`替换为1

XSS过滤使用正则匹配配合替换完成<br>
对普通标签的过滤只需要编码尖括号就可，对于`javascript伪协议`首先使用一个`preg_replace_callback`匹配到a标签带有为协议的的href属性，再使用一个`preg_replace_callback`将 *__javascript:__* 替换为 *__unsafe-javascript:__*

### 逻辑处理
试图逻辑放在`Router`模块的`visitRoute`方法的`next`闭包中，因为所有控制，包括中间件的返回值都会在`next`闭包中获取，只需要判断返回类型，如果返回值为对象，且类名为`YeuolyShellView`就开始处理视图层逻辑
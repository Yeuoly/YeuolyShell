### 创建表
```php
$res = $database->query('table')->create([
    'id' => ['INTEGER','NOT NULL','AUTO_INCREMENT'],
    'username' => ['VARCHAR(32)','NOT NULL'],
    'password' => ['VARCHAR(32)','NOT NULL']
]);
```
以上操作创建了一个`table`表，这个表包含了三个字段 *__id__*  *__username__* *__password__* ，且其类型和属性为后面的数组，同时我们支持不使用数组的创建

```php
'id' => 'INTEGER NOT NULL AUTO_INCREMENT'
```
这样的字符串也是支持的

其中`$res`为 __true__ 或 __false__ ，代表是否成功

### 插入数据
```php
$res = $database->query('table')->insert([
    'id' => 1,
    'username' => 'Yeuoly',
    'password' => '123456'
]);
```
这样就向`table`表内插入了一行数据

### 查询数据
```php
$data = $database->query('table')->select();
```
这样就获取了`table`表内的全部数据

其中`$data`就是查询到的数据结果，如果查询失败，会返回 __false__
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
可以看出来，*__AND__* 条件就是全部放在一个数组里， *__OR__* 就是分开为多个数组

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
这里有必要说一句，一般其实我们也用不到这么复杂的查询条件，之所以开发这么麻烦一个东西，是因为当初在用`ThinkPHP`的时候，多条件混合查询它特麻烦，搞得我很烦

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
以上表示从`table`中查询同名用户的数量，并且进行非顺序排序，这里我们顺便接介绍了排序操作，最终出来的结果会有两个键： *__username__* , *__times__*<br>
以上约等于
```mysql
SELECT username, count(*) as count FROM table group by username order by count
```
只是`YeuolyShell`会把列名改为 *__username__* 与 *__times__*

### 更新数据
```php
$database->query('table')
    ->where(['id','>',2020])
    ->limit(1)
    ->update([
        'status' => -1,
        'username' => 'unregistered'
    ]);
```
以上等价于
```mysql
UPDATE table SET status = -1 AND username = 'unregistered' WHERE id > 2020 limit 0,1
```

### 删除数据
```php
$database->query('table')
    ->where(['id','<',2020])
    ->limit(1)
    ->delete()
```
以上等价于
```mysql
DELETE FROM table WHERE id < 2020 limit 1
```

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
表示从第`1`行数据开始，最后查`20`行
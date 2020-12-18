<?php

namespace YeuolyShellDB;

use Error;
use Exception;

use function Response\response;

class DataBase{
    private $con;

    private $db_type;

    private $config;

    private $column_prepend;

    public function __construct($config)
    {
        $host = $config->getDBInfo('DB_HOST');
        $account = $config->getDBInfo('DB_USER');
        $passowrd = $config->getDBInfo('DB_PASSWORD');
        $dbname = $config->getDBInfo('DB_NAME');
        $this->db_type = $config->getDBInfo('DB_TYPE');
        $this->column_prepend = $config->getDBInfo('DB_COLUMN_PREPEND');
        $this->config = $config;

        switch($this->db_type){
            case 'mysql':
                $this->con = mysqli_connect($host, $account, $passowrd, $dbname);
                if(!$this->con){
                    throw new Error('数据库链接错误，请检查数据库配置');
                }
            break;
        }
        if(!$this->con){
            throw new Error('数据库连接错误，请检查数据库配置');
        }

        //启动session
        session_start();
    }

    public function query($table){
        return new DBQuery($this->con, $this->config, $table);
    }

    public function fetchSelection($key, $dict){
        return $dict[$this->column_prepend.$key];
    }

    public function __destruct()
    {
        if($this->con){
            mysqli_close($this->con);
        }
    }

    //session操作类似TP5
    public function session($key, $v = null){
        if($v){
            $_SESSION[$key] = $v;
        }else{
            return key_exists($key, $_SESSION) ? $_SESSION[$key] : null;
        }
    }
}

class DBQuery{
    private $condition = [];

    private $type = '';

    private $table;

    private $column_prepend = '';

    private $orderby_a = [
        'key' => '',
        'desc' => false
    ];

    private $limit_a = [
        'start' => 0,
        'length' => 0
    ];

    private $groupby_a = '';

    private $config;

    private $con;

    public function __construct($con, $config, $table)
    {
        $this->config = $config;
        $this->con = $con;
        $this->table = $config->getDBInfo('DB_TABLE_PREPEND').$table;

        $this->column_prepend = $config->getDBInfo('DB_COLUMN_PREPEND');
    }

    public function query($query){
        return mysqli_query($this->con, $query);
    }

    public function create($condition){
        // $condition = [
        //     'id' => 'INT UNSIGHED',
        //     'uname' => [
        //         'VARCHAR(128)',
        //         'NOT NULL'
        //     ],
        //     //$DB_PLACE_KEY_PREPEND._primary键为占用键，如需使用请适当改写，如 primary
        //     '$DB_PLACE_KEY_PREPEND._primary' => 'id'
        // ];
        $query = 'CREATE TABLE ' . $this->table . ' (';
        $primary = '';
        if(key_exists('_primary', $condition)){
            $primary = $condition['_primary'];
            unset($condition['_primary']);
        }
        foreach($condition as $key => $v){
            $s = '`' . $this->column_prepend . $key . '` ';
            if(is_array($v)){
                $s .= implode(' ', $v);
            }elseif(is_string($v)){
                $s .= $v;
            }else{
                throw new Error('创建数据表语句错误');
            }
            $query .= $s . ',';
        }

        if($primary){
            $query .= 'PRIMARY KEY (`' . $this->column_prepend.$primary . '`))';
        }else{
            $query = substr($query, 0, strlen($query) - 1) . ')';
        }

        if(!mysqli_query($this->con, $query)){
            return false;
        }
        return true;
    }

    public function where($condition){
        //格式规范
        //  [
        //      [
        //          'username','=','yeuoly',
        //          'password','=','zhouyu2002.'
        //      ],
        //      [
        //          [
        //              'id','=','1',
        //              'password','=','root'
        //          ],
        //          [
        //              'security','>','3'
        //          ],
        //      ],
        //  ];

        // 'SELECT WHERE ( (username = `yeuoly` AND password = `zhouyu2002.`) OR ( ( id = `1` AND password = `root` ) OR ( security > `3` ) ) )';
        // 这里本来应该做condition的格式检查的，但是由于太菜了还没想好怎么检查，其实最好的方式就是try catch，但是想来想去，貌似也没好try的= =，
        // 所以干脆就在buildWhereQuery中进行检查工作好了
        $this->condition = $condition;
        return $this;
    }

    protected function buildWhereQuery($conditions){
        //首先检测是否为空
        $len = count($conditions);
        if($len == 0){
            return '';
        }
        //然后检测组件类型
        if(is_array($conditions[0])){
            $str = '';
            for($i = 0; $i < $len; $i++){
                $str .= "(" . $this->buildWhereQuery($conditions[$i]) . ")";
                if($i != $len - 1){
                    $str .= " OR ";
                }
            }
            return $str;
        }else if(is_string($conditions[0])){
            if($len % 3 != 0){
                throw new Error('数据库查询条件语句错误');
            }
            $str = '';
            for($i = 0; $i < $len; $i += 3){
                $index_v = $conditions[$i + 2];
                $str .= $this->column_prepend. $conditions[$i] . " " . $conditions[$i + 1] . " " . (is_string($index_v) ? "'$index_v'" : $index_v);
                if($i != $len - 3){
                    $str .= " AND ";
                }
            }
            return $str;
        }else{
            throw new Error('数据库查询条件语句错误');
        }
    }

    protected function buildOrderByQuery(){
        return $this->column_prepend.$this->orderby_a['key'].($this->orderby_a['desc'] ? ' DESC' : '');
    }

    protected function buildWhereQueryEx(){
        if(count($this->condition) == 0){
            return '';
        }else{
            return "WHERE " . $this->buildWhereQuery($this->condition) . " ";
        }
    }

    public function limit($start_or_len, $len = -1){
        if($len == -1){
            $this->limit_a['length'] = $start_or_len;
        }else{
            $this->limit_a['start'] = $start_or_len;
            $this->limit_a['length'] = $len;
        }
        return $this;
    }

    protected function buildLimitQueryEx(){
        if(!$this->limit_a['length']){
            return '';
        }else{
            return "LIMIT " . $this->limit_a['start'] . "," . $this->limit_a['length'] . " ";
        }
    }

    protected function buildOrderByQueryEx(){
        if(!$this->orderby_a['key']){
            return '';
        }else{
            return "ORDER BY ".$this->buildOrderByQuery() . " ";
        }
    }

    protected function buildGroupByQueryEx(){
        if(!$this->groupby_a){
            return '';
        }else{
            return "GROUP BY ".$this->column_prepend.$this->groupby_a." ";
        }
    }

    public function orderBy($key){
        $this->orderby_a['key'] = $key;
        return $this;
    }

    public function groupBy($key){
        $this->groupby_a = $key;
        return $this;
    }

    public function desc(){
        $this->orderby_a['desc'] = true;
        return $this;
    }

    public function insert($resource){
        try{
            $keys = '';
            $vals = '';
            foreach($resource as $key => $v){
                $keys .= $this->column_prepend.$key . ',';
                $vals .= !is_numeric($v) ? "'" . $v . "'," : $v . ',';
            }
            if($len = strlen($keys)){
                $keys = substr($keys, 0, $len - 1);
                $vals = substr($vals, 0, strlen($vals) -1);
            }
            $sql = 'INSERT INTO ' . $this->table . '(' . $keys . ') VALUES (' . $vals . ')';
            $res = mysqli_query($this->con, $sql);
            if(!$res){
                return false;
            }
        }catch(Exception $e){
            throw new Error('数据库查询配置错误');
        }
        return true;
    }

    public function delete(){
        $query = 'DELETE FROM ' . $this->table . ' ' . $this->buildWhereQueryEx() . $this->buildLimitQueryEx();
        $res = mysqli_query($this->con, $query);
        return $res ? true : false;
    }

    /**
     * $members 要查询的参数
     * $out_keys 最终输出key
     */
    public function select(Array $members = [], Array $out_keys = []){
        $keys = $members;
        if($members && !is_array($members)){
            throw new Error('数据库查询参数错误');
        }
        if(is_array($members)){
            $len = count($members);
            for($i = 0; $i < $len; $i++){
                $members[$i] = $this->column_prepend.$members[$i];
            }
        }
        $members = $members ? implode(',', $members) : '*';
        $query = "SELECT $members FROM $this->table " . $this->buildWhereQueryEx() . $this->buildGroupByQueryEx() . $this->buildOrderByQueryEx() . $this->buildLimitQueryEx();
        $res = mysqli_query($this->con, $query);
        $dict = [];
        $v = mysqli_fetch_assoc($res);
        //首先fetch一次，如果为空则直接返回空数组
        if(!$v){
            return [];
        }
        //如果预定了搜索元素，则直接设定key为预定元素，否则构建出所有键
        //优先为out_keys，如果预定了outkeys，直接就用这个，否则再构建keys
        if($out_keys && count($keys) == count($out_keys)){
            $keys = $out_keys;
        }else if(!$keys){
            //获取query结果中的键
            $_keys = array_keys($v);
            //获取前缀长度
            $pre_len = strlen($this->column_prepend);
            foreach($_keys as $key => $val){
                //切割键名为去掉前缀后的键名
                $_keys[$key] = substr($val, $pre_len, strlen($val) - $pre_len);
            }
            $keys = $_keys;
        }

        do{
            array_push($dict, array_combine($keys, $v));
        }while($v = mysqli_fetch_assoc($res));

        return $dict;
    }

    public function update($data){
        if(count($data) == 0){
            return true;
        }
        //首先分离键值
        $keys = array_keys($data);
        $vals = array_values($data);
        //然后拼接prepend和引号，西巴，我为什么要搞这么麻烦一个东西，爷吐了
        foreach($keys as &$v){ $v = $this->column_prepend.$v; }
        foreach($vals as &$v){ $v = is_string($v) ? '\''.$v.'\'' : $v; }
        //然后构建update语句
        $sql = "UPDATE ".$this->table." SET ";
        $count = count($keys);
        for($i = 0; $i < $count; $i++){
            $sql .= $keys[$i]. "=" .$vals[$i];
        }
        $sql .= " ".$this->buildWhereQueryEx() . $this->buildLimitQueryEx();
        $res = mysqli_query($this->con, $sql);
        return $res ? true : false;
    }

    public function increase($key, $num = 1){
        $key = $this->column_prepend.$key;
        $sql = "UPDATE SET $key = $key + $num " . $this->buildWhereQueryEx() . $this->buildLimitQueryEx();
        $res = mysqli_query($this->con, $sql);
        return $res ? true : false;
    }

    public function getRows(){
        $sql = "EXPLAIN SELECT count(*) FROM " . $this->config->DB_INFO['DB_NAME'] . "." . $this->table;
        $res = mysqli_query($this->con, $sql);
        if(!$res || count($res) == 0){
            return response(500, '服务器内部错误');
        }
        return mysqli_fetch_assoc($res)['rows'];
    }
}

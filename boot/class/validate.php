<?php

namespace YeuolyShellValidate;

use Error;
use Exception;

const SKIP_CHEKER_AFTER = 0xf;

class Validate{
    protected $rule = [
        // 'pid' => [
        //     'rule' => 'required|min:0|max:256|number',
        //     'message' => [
        //         'min' => '最小的pid应为0',
        //         'max' => '最大的pid应为256',
        //         'required' => '不能缺少pid参 数'
        //     ]
        // ],
        // 'pname' => [
        //     'rule' => 'required|min:0|max:256|string',
        //     'message' => '……'
        // ],
        // //……
    ];

    protected $error = [];

    protected $p_error = [
        'required' => '缺少必要参数${key_name}',
        'min' => '${key_name}长度或大小过小',
        'max' => '${key_name}长度或大小过大',
        'regx' => '${key_name}验证错误',
        'string' => '${key_name}应为字符串类型',
        'number' => '${key_name}应为数字',
        'email' => '${key_name}应为邮箱格式',
        'length' => '${key_name}长度错误'
    ];

    protected function required($form, $key){
        return key_exists($key, $form);
    }

    protected function unrequired($form, $key){
        if(!key_exists($key, $form)){
            return SKIP_CHEKER_AFTER;
        }
        return true;
    }

    protected function min($form, $key, $len){
        if(is_string($form[$key])){
            if(mb_strlen($form[$key]) >= $len){
                return true;
            }
            return false;
        }elseif(is_numeric($form[$key])){
            if($form[$key] >= $len){
                return true;
            }
            return false;
        }
    }

    protected function max($form, $key, $len){
        if(is_string($form[$key])){
            if(mb_strlen($form[$key]) <= $len){
                return true;
            }
            return false;
        }elseif(is_numeric($form[$key])){
            if($form[$key] <= $len){
                return true;
            }
            return false;
        }
    }

    protected function length($form, $key, $len){
        return mb_strlen($form[$key]) == $len;
    }

    protected function regx($form, $key, $regx){
        return preg_match($regx, $form[$key]);
    }

    protected function string($form, $key){
        return is_string($form[$key]);
    }

    protected function number($form, $key){
        return is_numeric($form[$key]);
    }

    protected function email($form, $key){
        return preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/', $form[$key]);
    }

    public function check($form){
        $_res = true;
        //清空报错信息
        $this->error = [];
        try{
            foreach($this->rule as $key => $v){
                $checkers = explode('|', $v['rule']);
                foreach($checkers as $checker){
                    //拆分检查器组分
                    $args = explode(':', $checker);
                    $checker_name = $args[0];
                    $checker_args = explode(',', $args[1]);
                    //将数字参数转化为数字类型
                    $len = count($checker_args);
                    for($i = 0; $i < $len; $i++){
                        if(is_numeric($checker_args[$i])){
                            $checker_args[$i] = (double)$checker_args[$i];
                        }
                    }
                    //重置报错信息
                    $v['message'][$checker_name] = 
                        $v['message'][$checker_name] ? 
                        $v['message'][$checker_name] : 
                        ($this->p_error[$checker_name] ? 
                            $this->p_error[$checker_name] : 
                            '未知错误'
                        );
                    //检验
                    $pars = array_merge([ $form, $key ], $checker_args);
                    $res = call_user_func_array([$this, $checker_name], $pars);
                    if($res == SKIP_CHEKER_AFTER){
                        //若结果为跳过，则跳过后方检测
                        break;
                    }else if(!$res){
                        //若结果为false，添加报错信息并修改结果为false
                        array_push($this->error, str_replace('${key_name}', $key, $v['message'][$checker_name]));
                        $_res = false;
                    }
                }
            }
        }catch(Exception $e){
            //若检擦器定义错误则抛出报错
            throw new Error('检查器定义错误:'.$e->getMessage());
        }
        return $_res;
    }

    public function getError(){
        return $this->error;
    }
}
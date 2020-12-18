<?php

namespace Response;

use Request\Request;

const HEADER_DICT = [
    'STATUS' => 'HTTP/1.1 ',
    'COOKIE' => 'SET-COOKIE:',
    'ACCESS-CONTROL-ALLOW-ORIGIN' => 'Access-Control-Allow-Origin:'
];

class Response{
    private $headers;

    private $html = '';

    public function __construct($html = 'Welcome to YeuolyShell', $status = 200, $options = [])
    {
        $this->headers = [
            'STATUS' => 200,
            'COOKIE' => ''
        ];
        $this->temp_headers = [];
        $this->html = $html;
        $this->headers['status'] = $status;

        foreach($options as $key => $v){
            if(!key_exists($key ,$this->temp_headers)){
                $this->temp_headers = array_merge($this->temp_headers, [$key => $v]);
            }else{
                $this->temp_headers[$key] = $v;
            }
        }
    }

    public function loadOptions(Request $request){
        foreach($this->temp_headers as $key => $v){
            if(strtoupper($key) == 'ACCESS-CONTROL-ALLOW-ORIGIN'){
                $origin = $request->headers['ORIGIN'];
                if($v == '*'){
                    $this->headers = array_merge($this->headers, ['ACCESS-CONTROL-ALLOW-ORIGIN' => $origin]);
                }else if(is_string($v) && $v != ''){
                    $this->headers = array_merge($this->headers, ['ACCESS-CONTROL-ALLOW-ORIGIN' => $v]);
                }else if(is_array($v)){
                    if(in_array($origin, $v)){
                        $this->headers = array_merge($this->headers, ['ACCESS-CONTROL-ALLOW-ORIGIN' => $origin]);
                    }else{
                        $this->headers = array_merge($this->headers, ['ACCESS-CONTROL-ALLOW-ORIGIN' => 'NOTALLOW']);
                    }
                }
            }else if(!key_exists($key ,$this->headers)){
                $this->headers = array_merge($this->headers, [$key => $v]);
            }else{
                $this->headers[$key] = $v;
            }
        }
        unset($this->temp_headers);
    }

    public function release($config){
        foreach($this->headers as $key => $v){
            if($v && key_exists($key, HEADER_DICT)){
                header(HEADER_DICT[$key].$v);
            }
        }
        if($config->getAppInfo('SHOW_FRAMEWORK')){
            header('FrameWork:YeuolyShell');
        }
        echo $this->html;
    }
}

function response($status = 200, $content = '', $options = []){
    return new Response($content, $status, $options);
}
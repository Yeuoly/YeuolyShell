<?php

namespace Request;
use Exception;

class Request{
    public $post = [];

    public $get = [];

    public $path = [
        'ROUTE' => '',
        'DOC_URL' => ''
    ];

    public $location = [
        'DOMAIN' => '',
        'USER_PORT' => '',
        'USER_ADDR' => '',
        'SERVER_PORT' => '',
        'USER_ADDR' => ''
    ];

    public $basement = [
        'TIME' => 0,
        'SERVER_SOFTWARE' => '',
        'METHOD' => '',
        'HTTP_PROTOCOL' => '',
        'INDEX_ROOT' => ''
    ];

    public $headers = [
        'HTTP_ACCEPT_LANGUAGE' => '',
        'HTTP_ACCEPT_ENCODING' => '',
        'HTTP_ACCEPT' => '',
        'HTTP_USER_AGENT' => '',
        'HTTP_CONNECTION' => '',
        'HTTP_HOST' => '',
        'ORIGIN' => '',
        'REFERER' => ''
    ];

    protected function __fill(&$to,$key1,$from,$key2){
        if(key_exists($key2, $from)){
            $to[$key1] = $from[$key2];
        }
    }

    public function __construct($request)
    {
        $this->path['ROUTE'] = @$request['PATH_INFO'] ? @$request['PATH_INFO'] : ('/' .  $request['REQUEST_URI']);
        $this->__fill($this->path, 'DOC_URL', $request, 'DOCUMENT_URI');

        $this->__fill($this->location, 'DOMAIN', $request, 'SERVER_NAME');
        $this->__fill($this->location, 'USER_PORT', $request, 'REMOTE_PORT');
        $this->__fill($this->location, 'SERVER_PORT', $request, 'SERVER_PORT');
        $this->__fill($this->location, 'USER_ADDR', $request, 'REMOTE_ADDR');
        $this->__fill($this->location, 'SERVER_ADDR', $request, 'SERVER_ADDR');

        $this->__fill($this->basement, 'TIME', $request, 'REQUEST_TIME');
        $this->__fill($this->basement, 'SERVER_SOFTWARE', $request, 'SERVER_SOFTWARE');
        $this->__fill($this->basement, 'METHOD', $request, 'REQUEST_METHOD');
        $this->__fill($this->basement, 'HTTP_PROTOCOL', $request, 'SERVER_PROTOCOL');
        $this->__fill($this->basement, 'INDEX_ROOT', $request, 'DOCUMENT_ROOT');

        $this->__fill($this->headers, 'HTTP_ACCEPT_LANGUAGE', $request, 'HTTP_ACCEPT_LANGUAGE');
        $this->__fill($this->headers, 'HTTP_ACCEPT_ENCODING', $request, 'HTTP_ACCEPT_ENCODING');
        $this->__fill($this->headers, 'HTTP_ACCEPT', $request, 'HTTP_ACCEPT');
        $this->__fill($this->headers, 'HTTP_USER_AGENT', $request, 'HTTP_USER_AGENT');
        $this->__fill($this->headers, 'HTTP_CONNECTION', $request, 'HTTP_CONNECTION');
        $this->__fill($this->headers, 'HTTP_HOST', $request, 'HTTP_HOST');
        $this->__fill($this->headers, 'ORIGIN', $request, 'HTTP_ORIGIN');
        $this->__fill($this->headers, 'REFERER', $request, 'HTTP_REFERER');

        
        $queries = explode('&', urldecode($request['QUERY_STRING']));
        foreach($queries as $val){
            if($val == '')continue;
            $couple = explode('=', $val);
            try{
                $match = [$couple[0] => $couple[1]];
                $this->get = array_merge($this->get, $match);
            }catch(Exception $e){
                if($val != ''){
                    $this->get = array_merge($this->get, $val);
                }
            }
        }

        $this->post = @$_POST;
    }
}

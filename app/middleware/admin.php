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
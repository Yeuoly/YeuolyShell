<?php

namespace YeuolyShellRouter\initRoutes;

use YeuolyShellRouter\Router;

function __addRoutes(Router $router){
    $router->addChildByStr('index','','controller/index/index')->middleware('admin');
}
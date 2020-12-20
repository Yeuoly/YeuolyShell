<?php
namespace Controller\Index;

use Controller\Controller;
use YeuolyShellDB\DataBase;
use Request\Request;
use YeuolyShellView\View;

class Index extends Controller{
    public function index(Request $request, DataBase $database){
        return new View('index/index',['prop' => '<script>alert()</script>'], true);
    }
}
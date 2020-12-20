<?php
namespace YeuolyShellConfig;

require_once('../conf/conf.php');

use const YeuolyShellConf\ALLOW_LIST;
use const YeuolyShellConf\APP_INFO;
use const YeuolyShellConf\APP_SECURITY;
use const YeuolyShellConf\DB_INFO;
use const YeuolyShellConf\YS_NAMESPACE;

class Config{
    private $PATH = '';

    private $DB = DB_INFO;

    private $APP_INFO = APP_INFO;

    private $ALLOW_LIST = ALLOW_LIST;

    private $APP_SECURITY = APP_SECURITY;

    private $NAMESPACE = YS_NAMESPACE;

    public function __construct()
    {   
        $this->PATH = dirname(__DIR__);
        //在GLOBAL中备份一份基础参数
        $GLOBALS['__APP_BASE__']['APP_SECURITY'] = APP_SECURITY;
        $GLOBALS['__APP_BASE__']['APP_INFO'] = APP_INFO;
    }

    public function getSecurity($key){
        return $this->APP_SECURITY[$key];
    }

    public function getBasePath(){
        return $this->PATH;
    }

    public function getAppPath(){
        return $this->PATH . '/app';
    }

    public function getDBInfo($key){
        if(key_exists($key, $this->DB)){
            return $this->DB[$key];
        }
        return '';
    }

    public function getAppInfo($key){
        if(key_exists($key, $this->APP_INFO)){
            return $this->APP_INFO[$key];
        }
        return '';
    }

    public function getNamespaces($key){
        if(key_exists($key, $this->NAMESPACE)){
            return $this->NAMESPACE[$key];
        }
        return [];
    }

    public function getAllowList($key){
        if(key_exists($key, $this->ALLOW_LIST)){
            return $this->ALLOW_LIST[$key];
        }
        return '';
    }
}

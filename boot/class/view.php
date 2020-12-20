<?php
namespace YeuolyShellView;

use Error;

class View{
    private $view;
    private $data;
    private $xssfiler;

    public function __construct($view, $data, $xssfilter = false){
        $this->view = $this->analysisView($view);
        $this->data = $data;
        $this->xssfiler = $xssfilter;
    }

    protected function analysisView($view){
        $path = explode('/', $view);
        if(count($path) != 2 || !$path[0] || !$path[1]){
            throw new Error('视图层配置错误');
        }
        $target = explode('::', $path[1]);
        if($target && count($target) > 2){
            throw new Error('视图层配置错误');
        }
        return [
            'dir' => $path[0],
            'view' => [
                'name' => $target[0],
                'target' => $target[1]
            ]
        ];
    }

    protected function unxss_1($html){
        return preg_replace(
            [
                '/</','/>/','/"/','/\'/'
            ],
            [
                '&#x3c;','&#x3e;','&#x22;','&#x27;'
            ],
        $html);
    }

    protected function unxss_2($html){
        $html = preg_replace_callback('/<a([\s]+|[\s]+[^<>]+[\s]+)href=(\"([^<>"\']*)\"|\'([^<>"\']*)\')[^<>]*>/i', function($matches){
            return preg_replace('/[\r\n\t\f]/i', '', html_entity_decode($matches[0]));
        }, $html);
        $html = preg_replace_callback(['/<a (?!<)* *(href=[\'"]javascript:)([\s\S](?!<))+>/i'], function($matches){
            return preg_replace('/javascript/i','unsafe-javascript', $matches[0]);
        }, $html);
        return $html;
    }

    public function render(){
        $html = file_get_contents('../app/view/'.$this->view['dir'].'/'.$this->view['view']['name'].'.html');
        if(!$html){
            throw new Error('请检查您的视图层模板配置');
        }
        $patterns = [];
        $replace_text = [];
        $replace_arry_keys = [];
        foreach($this->data as $key => $v){
            if(is_string($v)){
                array_push($patterns, '/{{(\s+)*'.$key.'(\s+)*}}/');
                array_push($replace_text, $this->xssfiler ? $this->unxss_1($v) : $v);
            }else{
                array_push($replace_arry_keys, $key);
            }
        }
        $html = preg_replace($patterns, $replace_text, $html);
        if($this->xssfiler){
            $html = $this->unxss_2($html);
        }
        return $html;
    }
}
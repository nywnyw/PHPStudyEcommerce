<?php

namespace dbfolder;

//when calling the class Tpl, will be from rain namespace
use Rain\Tpl;

class Page{

    private $tpl;
    private $options = [];
    private $default=[
        "data"=>[]
    ];

    public function __construct($opts= []){
        $this->options = array_merge($this->default,$opts);
        $config = array(
            "tpl_dir"       => $_SERVER["DOCUMENT_ROOT"]."/views/",
            "cache_dir"     => $_SERVER["DOCUMENT_ROOT"]."/views/cache/",
            "debug"         => false // set to false to improve the speed
           );

        Tpl::configure( $config );
        $this->tpl = new Tpl();

        $this->setData($this->options["data"]);

        $this->tpl->draw("header");
    }

    private function setData($data=[]){
        foreach ($data as $key => $value) {
            $this->tpl->assign($key,$value);
        }
    }

    public function setTpl($name,$data=[],$return=false){
        $this->setData($data);
        return $this->tpl->draw($name,$return);
        
    }
   
    
    public function __destruct(){
        $this->tpl->draw("footer");
        
    }    
    
}



?>
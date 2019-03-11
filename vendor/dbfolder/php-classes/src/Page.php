<?php

namespace dbfolder;

//when calling the class Tpl, will be from rain namespace
use Rain\Tpl;

class Page{

    private $tpl;
    private $options = [];
    private $default=[
        "header"=>true,
        "footer"=>true,
        "data"=>[]
    ];

    public function __construct($opts= [],$tpl_dir = "/views/"){
       
        $this->options = array_merge($this->default,$opts);
        $config = array(
            "tpl_dir"       => $_SERVER["DOCUMENT_ROOT"].$tpl_dir,
            "cache_dir"     => $_SERVER["DOCUMENT_ROOT"]."/views/cache/",
            "debug"         => false // set to false to improve the speed
           );

        Tpl::configure( $config );
        $this->tpl = new Tpl();

        $this->setData($this->options["data"]);

        if($this->options["header"]===true){
            
        $this->tpl->draw("header");
        }
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
        if($this->options["footer"]===true){
        $this->tpl->draw("footer");
        }        
    }    
    
}



?>
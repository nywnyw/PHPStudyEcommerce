<?php

namespace dbfolder;

use \dbfolder\DB\Sql;
use \dbfolder\Model;

class Category extends Model{
    
    //getters and setters create by class Model when setData is called;
   
    public static function listAll(){
        $sql = new Sql();
        return $sql->select("SELECT * FROM tb_categories ORDER BY descategory");
    }

    public function save(){
        $sql = new Sql();
        $result = $sql->select("CALL sp_categories_save(:idcategory, :descategory)",array(
            ":idcategory"=>$this->getidcategory(),
            ":descategory"=>$this->getdescategory()
        ));
        Category::updateHTML();
        $this->setData($result[0]);
    }

    public function get($id){
        $sql = new Sql();
        $result = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :id",array(":id"=>$id));
        $this->setData($result[0]);
    }

    public function delete(){
        $sql = new Sql();
        $sql->query("DELETE FROM tb_categories WHERE idcategory = :id",array(":id"=>$this->getidcategory()));
        Category::updateHTML();
    }

    public static function updateHTML(){

       $categories = Category::listAll();
       $html = [];
       foreach($categories as $row){
           array_push($html,'<li><a href="/category/'.$row["idcategory"].'">'.$row["descategory"].'</a></li>');
       }
       file_put_contents($_SERVER["DOCUMENT_ROOT"].DIRECTORY_SEPARATOR."/views/categories-menu.html",implode("",$html));

    }

   
}

?>
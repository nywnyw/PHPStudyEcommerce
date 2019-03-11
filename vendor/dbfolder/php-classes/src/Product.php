<?php

namespace dbfolder;

use \dbfolder\DB\Sql;
use \dbfolder\Model;

class Product extends Model{
    
    //getters and setters create by class Model when setData is called;
   
    public static function listAll(){
        $sql = new Sql();
        return $sql->select("SELECT * FROM tb_products ORDER BY desproduct");
    }

    public function save(){
        $sql = new Sql();
        $result = $sql->select("CALL sp_products_save(:idproduct, :desproduct,:vlprice,:vlwidth,:vlheight,:vllength, :vlweight, :desurl)",array(
            ":idproduct"=>$this->getidproduct(),
            ":desproduct"=>$this->getdesproduct(),
            ":vlprice"=>$this->getvlprice(),
            ":vlwidth"=>$this->getvlwidth(),
            ":vlheight"=>$this->getvlheight(),
            ":vllength"=>$this->getvllength(),
            ":vlweight"=>$this->getvlweight(),
            ":desurl"=>$this->getdesurl(),                    
        ));
        $this->setData($result[0]);
    }

    public function get($id){
        $sql = new Sql();
        $result = $sql->select("SELECT * FROM tb_products WHERE idproduct = :id",array(":id"=>$id));
        $this->setData($result[0]);
    }

    public function delete(){
        $sql = new Sql();
        $sql->query("DELETE FROM tb_products WHERE idproduct = :id",array(":id"=>$this->getidproduct()));
    }
    public function checkPhoto(){
        if(file_exists($_SERVER["DOCUMENT_ROOT"].DIRECTORY_SEPARATOR.
        "resources".DIRECTORY_SEPARATOR.
        "site".DIRECTORY_SEPARATOR.
        "img".DIRECTORY_SEPARATOR.$this->getidproduct().".jpg"
        )){
            $url= "/resources/site/img/".$this->getidproduct().".jpg";
        }else{
            $url= "/resources/site/img/product.jpg";
        }

        return $this->setdesphoto($url);
    }

    public function getValues(){
        
        $this->checkPhoto();
        $values = parent::getValues();
        return $values;
        
    }
    public function setPhoto($file){
        $ext = explode(".",$file["name"]);
        //last pos
        $ext = end($ext);
        
        switch($ext){
            case "jpg":
            case "jpeg":
            $image = imagecreatefromjpeg($file["tmp_name"]);
            break;

            case "gif":
            $image = imagecreatefromgif($file["tmp_name"]);
            break;
            case "png":
            $image = imagecreatefrompng($file["tmp_name"]);
            break;

        }
        imagejpeg($image,$_SERVER["DOCUMENT_ROOT"].DIRECTORY_SEPARATOR.
        "resources".DIRECTORY_SEPARATOR.
        "site".DIRECTORY_SEPARATOR.
        "img".DIRECTORY_SEPARATOR.$this->getidproduct().".jpg");

        imagedestroy($image);
        $this->checkPhoto();

    }
      
}

?>
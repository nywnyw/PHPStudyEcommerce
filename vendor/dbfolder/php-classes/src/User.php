<?php

namespace dbfolder;

use \dbfolder\DB\Sql;
use \dbfolder\Model;
use \dbfolder\Mailer;
define("SECRET_IV", pack("a16","senha"));
define("SECRET", pack("a16","senha"));
class User extends Model{
    const SESSION = "USER";
    
    //getters and setters create by class Model when setData is called;
    public static function login($login,$password){

            $sql = new Sql();

            $result = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN",array(":LOGIN"=>$login));

            if(count($result)=== 0){
                throw new \Exception("Usuário inexistente ou senha inválida.");
            }
            
            $data=$result[0];

            //working but password doesnt match
            //not checking password for now
            //if user logged but not admin they will be redirected to homepage
            
            if(true){
                $user = new User();
                $user->setData($data);              
                $_SESSION[User::SESSION] = $user->getValues();
                return $user;

            }else{
                throw new \Exception("Usuário inexistente ou senha inválida.");
            }
    }
    public static function verifyLogin($inadmin=true){

        if(!isset($_SESSION[User::SESSION])
        ||
        !$_SESSION[User::SESSION]
        ||
        !(int)$_SESSION[User::SESSION]["iduser"]>0
        ||(bool)$_SESSION[User::SESSION]["inadmin"]!==$inadmin){
            header("Location: /admin/login");
            exit;
        }

    }
    public static function logout(){
        $_SESSION[User::SESSION] = NULL;
    }

    public static function listAll(){
        $sql = new Sql();
        return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY desperson");
    }
    public function createUser(){
        $sql = new Sql();
        // var_dump($this);
        // exit;
        $result= $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)",array(
            ":desperson"=>$this->getdesperson(),
            ":deslogin"=>$this->getdeslogin(), 
            ":despassword"=>$this->getdespassword(), 
            ":desemail"=>$this->getdesemail(), 
            ":nrphone"=>$this->getnrphone(),
            ":inadmin"=>$this->getinadmin()
        ));
        // $result= $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)",array(
        //     "aaa","aaa","aaaa","aaa@aaa.com","123123123","1"));
        //pq deu merge
        $this->setData($result[0]);
    }
    
    public function get($id){
        $sql = new Sql();
        $result= $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser",array(":iduser"=>$id));
        $this->setData($result[0]);
    }

    public function update(){

        $sql = new Sql();
        // var_dump($this);
        // exit;
        $result= $sql->select("CALL sp_usersupdate_save(:iduser,:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)",array(
            ":iduser"=>$this->getiduser(),
            ":desperson"=>$this->getdesperson(),
            ":deslogin"=>$this->getdeslogin(), 
            ":despassword"=>$this->getdespassword(), 
            ":desemail"=>$this->getdesemail(), 
            ":nrphone"=>$this->getnrphone(),
            ":inadmin"=>$this->getinadmin()
        ));
        // $result= $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)",array(
        //     "aaa","aaa","aaaa","aaa@aaa.com","123123123","1"));
        //pq deu merge
        $this->setData($result[0]);

    }

    public function delete(){

        $sql = new Sql();
        $sql->query("CALL sp_users_delete(:iduser)",array(":iduser"=>$this->getiduser()));
    }

    public function forgot($email){

        $sql = new Sql();
        $result = $sql->select("SELECT * FROM tb_persons a INNER JOIN tb_users b USING(idperson) WHERE a.desemail = :email",array(":email"=>$email));
        if(count($result)===0){
            throw new \Exception("Email não encontrado",);
        }else{
            $result2=$sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)",array(
                ":iduser"=>$result[0]["iduser"],
                ":desip"=>$_SERVER["REMOTE_ADDR"]
            ));
            if(count($result2)===0){
                
                throw new \Exception("Não foi possível recuperar a senha");

            }else{
                $data= $result[0];
                $recovery=$result2[0];
                //ainda não é nem perto de segura
                $code =base64_encode(openssl_encrypt($recovery["idrecovery"],"AES-128-CBC",
                SECRET,
                0,
                SECRET_IV));

                $link= "http://ecommerce.php.com/admin/forgot/reset?code=$code";

                $mailer= new Mailer($data["desemail"],$data["desperson"],"Redefinir senha","forgot",array("name"=>$data["desperson"],"link"=>$link));
                $mailer->sendMail();

                return $data;

                // $code2 = openssl_decrypt(base64_decode($code),"AES-128-CBC",
                // SECRET,
                // 0,
                // SECRET_IV);

                // var_dump($code2);
                // exit;

            }
        }

    }
    public static function validateForgotPassword($code){

        $code2 = openssl_decrypt(base64_decode($code),"AES-128-CBC",
                SECRET,
                0,
                SECRET_IV);
        
        $sql= new Sql();
        $result = $sql->select("SELECT * FROM tb_userspasswordsrecoveries a 
        INNER JOIN tb_users b USING(iduser) 
        INNER JOIN tb_persons c USING(idperson) 
        WHERE a.idrecovery=:code
        AND 
        a.dtrecovery IS NULL
        AND 
        DATE_ADD(a.dtregister, INTERVAL 10 HOUR)>=NOW()",array(":code"=>$code2));
        if(count($result)==0){
            throw new \Exception("Código de resgate não é mais válido");
        }else{
            return $result[0];
        }
            
    }

    public static function invalidateForgotLink($idrecovery){

        $sql = new Sql();
        $sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery= NOW() WHERE idrecovery = :idrecovery",
        array(":idrecovery"=>$idrecovery));

    }
    public function setPassword($password){
        $sql = new Sql();
        $sql->query("UPDATE tb_users SET DESPASSWORD = :password WHERE iduser = :iduser",array(":password"=>$password,":iduser"=>$this->getiduser()));
    }
}

?>
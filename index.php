<?php 
session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
use \dbfolder\Page;
use \dbfolder\Adminpage;
use \dbfolder\User;
$app = new Slim();

//remove on release
$app->config('debug', true);

$app->get('/', function() {

	$page = new Page();	
	$page->setTpl("index");
	//$sql = new dbfolder\DB\Sql();
	//$result = $sql->select("SELECT * FROM tb_users");
	//echo json_encode($result);

});
$app->get("/admin", function(){
	User::verifyLogin();
	
	$page = new Adminpage();
	$page->setTpl("index");
});

$app->get("/admin/login", function(){
	$page = new Adminpage([
		"header"=>false,
		"footer"=>false
	]);
	$page->setTpl("login");
});
$app->post("/admin/login",function(){

	User::login($_POST["login"],$_POST["password"]);
	header("Location: /admin");
	exit;
});

$app->get("/admin/logout",function(){

	User::logout();
	header("Location: /admin/login");
	exit;

});
//.htaccess else routes wont work
$app->run();

?>
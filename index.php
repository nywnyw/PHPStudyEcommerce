<?php 
session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
use \dbfolder\Page;
use \dbfolder\Adminpage;
use \dbfolder\User;
use \dbfolder\Category;
use \dbfolder\Product;

$app = new Slim();

//remove on release
$app->config('debug', true);

require_once("index-admin.php");

$app->get('/', function() {

	$page = new Page();	
	$page->setTpl("index");
	//$sql = new dbfolder\DB\Sql();
	//$result = $sql->select("SELECT * FROM tb_users");
	//echo json_encode($result);

});

$app->get("/category/:id",function($id){
	 $category = new Category();
	 $category->get((int)$id);
	 $page = new Page();
	 $page->setTpl("category",["category"=>$category->getValues(),"products"=>[]]);

});

//.htaccess else routes wont work
$app->run();

?>
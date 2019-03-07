<?php 

require_once("vendor/autoload.php");

use \Slim\Slim;
use \dbfolder\Page;
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

$app->run();

?>
<?php

use \Slim\Slim;
use \dbfolder\Page;
use \dbfolder\Adminpage;
use \dbfolder\User;
use \dbfolder\Category;
use \dbfolder\Product;

$app->get("/admin/users/:iduser/delete",function($iduser){

User::verifyLogin();
$user = new User();
$user->get((int)$iduser);
$user->delete();
header("Location: /admin/users");
exit;

});

$app->get("/admin", function(){
User::verifyLogin();

$page = new Adminpage();
$page->setTpl("index");
});
$app->get("/admin/forgot",function(){
$page = new Adminpage([
    "header"=>false,
    "footer"=>false
]);
$page->setTpl("forgot");
});
$app->post("/admin/forgot",function(){
$email = $_POST["email"];
$user = User::forgot($email);
header("Location: /admin/forgot/sent");
exit;
});
$app->get("/admin/forgot/sent",function(){

$page = new Adminpage([
    "header"=>false,
    "footer"=>false
]);

$page->setTpl("forgot-sent");
});
$app->get("/admin/forgot/reset",function(){
$user = User::validateForgotPassword($_GET["code"]);

$page = new Adminpage([
    "header"=>false,
    "footer"=>false
]);

$page->setTpl("forgot-reset",array("name"=>$user["desperson"],"code"=>$_GET["code"]));

});
$app->post("/admin/forgot/reset",function(){

$forgotUser = User::validateForgotPassword($_POST["code"]);

User::invalidateForgotLink($forgotUser["idrecovery"]);

$user = new User();

$user->get((int)$forgotUser["iduser"]);
$password = password_hash($_POST["password"],PASSWORD_DEFAULT, ["cost"=>12]);

$user->setPassword($password);

$page = new Adminpage([
    "header"=>false,
    "footer"=>false
]);

$page->setTpl("forgot-reset-success");
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

$app->get("/admin/users",function(){
User::verifyLogin();
$users = User::listAll();
$page = new Adminpage();
$page->setTpl("users",array("users"=>$users));
});

$app->get("/admin/users/create",function(){
User::verifyLogin();

$page = new Adminpage();
$page->setTpl("users-create");
});

$app->post("/admin/users/create",function(){

User::verifyLogin();
$user = new User();
$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;
$_POST["despassword"] = password_hash($_POST["despassword"],PASSWORD_DEFAULT, ["cost"=>12]);
$user->setData($_POST);


$user->createUser();

header("Location: /admin/users");
exit;


});

$app->get("/admin/users/:iduser",function($iduser){
User::verifyLogin();
$user = new User();
$user->get((int)$iduser);

$page = new Adminpage();
$page->setTpl("users-update",array("user"=>$user->getValues()));
});
$app->post("/admin/users/:iduser",function($iduser){
User::verifyLogin();
$user = new User();
$user->get((int)$iduser);
$_POST["inadmin"]=(isset($_POST["inadmin"]))?1:0;
$user->setData($_POST);
$user->update();
header("Location: /admin/users");
exit;

});

$app->get("/admin/categories",function(){
User::verifyLogin();
$page = new Adminpage();
$categories = Category::listAll();
$page->setTpl("categories",array("categories"=>$categories));

});

$app->get("/admin/categories/create",function(){
User::verifyLogin();
$page = new Adminpage();
$page->setTpl("categories-create");

});
$app->post("/admin/categories/create",function(){
User::verifyLogin();
$category = new Category();
$category->setData($_POST);
$category->save();
header("Location: /admin/categories");
exit;
});

$app->get("/admin/categories/:id/delete",function($id){
User::verifyLogin();	
$category = new Category();
$category->get((int)$id);
$category->delete();

header("Location: /admin/categories");
exit;

});
$app->get("/admin/categories/:id",function($id){
User::verifyLogin();	
$category = new Category();
$category->get((int)$id);

$page = new Adminpage();
$page->setTpl("categories-update",["category"=>$category->getValues()]);
});

$app->post("/admin/categories/:id",function($id){
User::verifyLogin();
$category = new Category();
$category->get((int)$id);

$category->setData($_POST);
$category->save();
header("Location: /admin/categories");
exit;

});

$app->get("/admin/products",function(){
    User::verifyLogin();
    $page= new Adminpage();
    $products = Product::listAll();
    $page->setTpl("products",["products"=>$products]);    

});

$app->get("/admin/products/create",function(){
    User::verifyLogin();
    $page= new Adminpage();
    $page->setTpl("products-create");    
});
$app->post("/admin/products/create",function(){
    User::verifyLogin();
    $product = new Product();
    $product->setData($_POST);
    $product->save();

    header("Location: /admin/products");
    exit;
});
//edit product
$app->get("/admin/products/:id",function($id){
    User::verifyLogin();
    $product = new Product();
    $product->get($id);
    $page= new Adminpage();
    $page->setTpl("products-update",["product"=>$product->getValues()]);

});
//post to update
$app->post("/admin/products/:id",function($id){
    User::verifyLogin();
    $product = new Product();
    $product->get($id);
    $product->setData($_POST);
    $product->save();
    $product->setPhoto($_FILES["file"]);

    header("Location: /admin/products");
    exit;

});

$app->get("/admin/products/:id/delete",function($id){
User::verifyLogin();
$product = new Product();
$product->get((int)$id);
$product->delete();

header("Location: /admin/products");
exit;
});



?>
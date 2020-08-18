<?php

use \Hcode\Page;
use \Hcode\Model\Product;
use \Hcode\Model\Category;
use \Hcode\Model\User;


$app->get("/", function() 
{

	$page = new Page();

	$page->setTpl("index"); 

});


$app->get("/categories/:idcategory", function($idcategory) 
{

	$category = new Category();

	$category->get((int) $idcategory);

	$page = new Page();

	$page->setTpl("category", array(
		"category"=>$category->getValues(),
		"products"=>array()
	)); 

});

?>
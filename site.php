<?php

use \Hcode\Page;
use \Hcode\Model\Product;
use \Hcode\Model\Category;
use \Hcode\Model\User;


$app->get("/", function() 
{

	$products = Product::listAll();

	$page = new Page();

	$page->setTpl("index", array(
		"products"=>Product::checkList($products)
	)); 

});


$app->get("/categories/:idcategory", function($idcategory) 
{

	$nrPage = (isset($_GET['page'])) ? (int) $_GET['page'] : 1;

	$category = new Category();

	$category->get((int) $idcategory);

	$pagination = $category->getProductsPage($nrPage);

	$pages = [];

	for ($i = 1; $i <= $pagination['pages']; $i++) { 

		if ($i === $nrPage) 
		{

			array_push($pages, [
				'link'=>'#',
				'page'=>$i
		]);

		}

		else {

			array_push($pages, [
				'link'=>'/categories/' . $category->getidcategory() . '?page=' . $i,
				'page'=>$i
		]);

		}
	}

//var_dump($pages); exit;

	$page = new Page();

	$page->setTpl("category", [
		'category'=>$category->getValues(),
		'products'=>$pagination["data"],
		'pages'=>$pages,
		'page'=>$nrPage
	]);

});


$app->get("/products/:desurl", function($desurl){

	$product = new Product();

	$product->getFromURL($desurl);

	$page = new Page();

	$page->setTpl("product-detail", [
		'product'=>$product->getValues(),
		'categories'=>$product->getCategories()
	]);

});

?>
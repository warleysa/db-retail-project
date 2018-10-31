<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes - Sam Warley - CSE 3330 - Homework 6

$app->group('/api', function () use ($app) {
	$app->post('/login',
		function ($request, $response) {
			$input = $request->getParsedBody();
			$sql = "SELECT EXISTS(SELECT * FROM Users WHERE email = :email AND password = SHA1(:password)) loginAuth;";
			$sth = $this->dbConn->prepare($sql);
			$sth->bindParam("email", $input['email']);
			$sth->bindParam("password", $input['password']);
			$sth->execute();
			$loginAuth = $sth->fetchObject();
			return $this->response->withJson($loginAuth);
		}
	);

	$app->post('/register',
		function ($request, $response) {
			$input = $request->getParsedBody();
			$sql = "INSERT INTO Users ( password, email, first_name, last_name) VALUES ( SHA1(:password), :email, :first_name, :last_name)";
			$sth = $this->dbConn->prepare($sql);
			$sth->bindParam("email", $input['email']);
			$sth->bindParam("password", $input['password']);
			$sth->bindParam("first_name", $input['first_name']);
			$sth->bindParam("last_name", $input['last_name']);
			$sth->execute();
			$registerOut = $sth->fetchAll(PDO::FETCH_ASSOC);
			return $this->response->withJson($registerOut);
		}
	);

	$app->get('/products', 
		function ($request, $response, $args) {
			$input = $request->getQueryParams();
			$by_color = $input['color'];
		    $by_style = $input['style'];
		    $by_materials = $input['materials'];
		    $by_brand = $input['brand'];

		    $query = "SELECT * FROM Products";
		    $conditions = array();

		    if(! empty($by_color)) {
		      $conditions[] = "color='$by_color'";
		    }
		    if(! empty($by_style)) {
		      $conditions[] = "style='$by_style'";
		    }
		    if(! empty($by_materials)) {
		      $conditions[] = "materials='$by_materials'";
		    }
		    if(! empty($by_brand)) {
		      $conditions[] = "brand='$by_brand'";
		    }

		    $sql = $query;
		    if (count($conditions) > 0) {
		      $sql .= " WHERE " . implode(' AND ', $conditions);
		    }
		    $sth = $this->dbConn->prepare($sql);
			
			$sth->execute();
			$products = $sth->fetchAll(PDO::FETCH_ASSOC);
			return $this->response->withJson($products);
		}
	);

	// $app->get('/products', 
	// 	function ($request, $response, $args) {
	// 		$db = $this->dbConn;
	// 		$sth= $db->prepare(
	// 			"SELECT * FROM Products ORDER BY rating"
	// 		);
	// 		$sth->execute();
	// 		$products = $sth->fetchAll(PDO::FETCH_ASSOC);
	// 		return $this->response->withJson($products);
	// 	}
	// );

});


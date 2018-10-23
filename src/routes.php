<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes - Sam Warley - CSE 3330 - Homework 6

$app->group('/api', function () use ($app) {
	$app->post('/user',
		function ($request, $response) {
			$input = $request->getParsedBody();
			$sql = "SELECT EXISTS(SELECT * FROM Users WHERE email = :email AND password = :password) loginAuth;";
			$sth = $this->dbConn->prepare($sql);
			$sth->bindParam("email", $input['email']);
			$sth->bindParam("password", $input['password']);
			$sth->execute();
			$loginAuth = $sth->fetchObject();
			return $this->response->withJson($loginAuth);
		}
	);

	$app->get('/productsFiltered', 
		function ($request, $response, $args) {
			$db = $this->dbConn;

			$by_color = $this->request->get('color');
		    $by_style = $this->request->get('style');
		    $by_materials = $this->request->get('materials');
		    $by_brand = $this->request->get('brand');

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

		    $sth = $db->prepare($sql);
			
			$sth->execute();
			$products = $sth->fetchAll(PDO::FETCH_ASSOC);
			return $this->response->withJson($products);
		}
	);

	$app->get('/products', 
		function ($request, $response, $args) {
			$db = $this->dbConn;
			$sth= $db->prepare(
				"SELECT * FROM Products ORDER BY rating"
			);
			$sth->execute();
			$products = $sth->fetchAll(PDO::FETCH_ASSOC);
			return $this->response->withJson($products);
		}
	);

});


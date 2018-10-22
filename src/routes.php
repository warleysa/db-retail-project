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
			$sql= $db->prepare(
				"SELECT * from Products WHERE 1=1"
			);

			if ($input['color']) {
			    $sql .= " (color = :color";
			    $sql->bindParam("color", $input['color']);
			}
			if ($input['style']) {
			    $sql .= " AND style = :style";
			    $sql->bindParam("style", $input['style']);
			}
			if ($input['materials']) {
			    $sql .= " AND materials = :materials";
			    $sql->bindParam("materials", $input['materials']);
			}
			if ($input['brand']) {
			    $sql .= " AND brand = :brand";
			    $sql->bindParam("brand", $input['brand']);
			}
			// $sql .= ")";
			
			$sql->execute();
			$products = $sql->fetchAll(PDO::FETCH_ASSOC);
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


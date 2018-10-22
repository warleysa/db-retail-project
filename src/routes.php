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


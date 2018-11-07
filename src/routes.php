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
			$sqlCheck = "SELECT EXISTS(SELECT email FROM Users WHERE email = :email)";
			$sthCheck = $this->dbConn->prepare($sqlCheck);
			$sthCheck->bindParam("email", $input['email']);
			$sthCheck->execute();
			$sqlCheckResult = $sthCheck->fetchObject();
			if($sqlCheckResult == '1') {
				$errorCode = -1;
				return $this->response->withJson($errorCode);
			} else {
				$sql = "INSERT INTO Users (password, email, first_name, last_name) VALUES (SHA1(:password), :email, :first_name, :last_name)";
				$sth = $this->dbConn->prepare($sql);
				$sth->bindParam("email", $input['email']);
				$sth->bindParam("password", $input['password']);
				$sth->bindParam("first_name", $input['first_name']);
				$sth->bindParam("last_name", $input['last_name']);
				$sth->execute();
				$sql2 = "SELECT id, first_name, email, last_name FROM Users WHERE id = LAST_INSERT_ID()";
				$sth2 = $this->dbConn->prepare($sql2);
				$sth2->execute();
				$registerOut = $sth2->fetchObject();
				return $this->response->withJson($registerOut);
			}
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

	$app->post('/follow',
		function ($request, $response) {
			$input = $request->getParsedBody();
			$sql = "INSERT INTO Follows (user_id, brand_id) VALUES (:user_id, :brand_id)";
			$sth = $this->dbConn->prepare($sql);
			$sth->bindParam("user_id", $input['user_id']);
			$sth->bindParam("brand_id", $input['brand_id']);
			$sth->execute();
			$followPost = $sth->fetchObject();
			return $this->response->withJson($followPost);
		}
	);
	$app->delete('/unfollow',
		function ($request, $response) {
			$input = $request->getParsedBody();
			$sql = "DELETE FROM Follows WHERE user_id = :user_id AND brand_id = :brand_id";
			$sth = $this->dbConn->prepare($sql);
			$sth->bindParam("user_id", $input['user_id']);
			$sth->bindParam("brand_id", $input['brand_id']);
			$sth->execute();
			$unfollowDelete = $sth->fetchObject();
			return $this->response->withJson($unfollowDelete);
		}
	);
	$app->post('/viewed',
		function ($request, $response) {
			$input = $request->getParsedBody();
			$sql = "INSERT INTO User_Viewed (user_id, prod_id, date, id) VALUES (:user_id, :prod_id, :date, :id)";
			$sth = $this->dbConn->prepare($sql);
			$sth->bindParam("user_id", $input['user_id']);
			$sth->bindParam("prod_id", $input['prod_id']);
			$sth->bindParam("date", $input['date']);
			$sth->bindParam("id", $input['id']);
			$sth->execute();
			$viewedProduct = $sth->fetchObject();
			return $this->response->withJson($viewedProduct);
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


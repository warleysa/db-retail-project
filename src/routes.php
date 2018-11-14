<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes - Sam Warley - CSE 3330 - Homework 6

$app->group('/api', function () use ($app) {
	$app->post('/login',
		function ($request, $response) {
			$input = $request->getParsedBody();
			$sql = "SELECT email FROM Users WHERE email = :email AND password = SHA1(:password) LIMIT 1";
			$sth = $this->dbConn->prepare($sql);
			$sth->bindParam("email", $input['email']);
			$sth->bindParam("password", $input['password']);
			$sth->execute();
			$sqlCheckResult = $sth->fetchAll(PDO::FETCH_OBJ);
			$num_rows = count($sqlCheckResult);
			if($num_rows == 0) {
				$resultJson->loginAuth = 0;
				return $this->response->withJson($resultJson);
			} else {
				$sqlUser = "SELECT id, first_name, email, last_name, 1 as loginAuth FROM Users WHERE email = :email";
				$sthUser = $this->dbConn->prepare($sqlUser);
				$sthUser->bindParam("email", $input['email']);
				$sthUser->execute();
				$userData = $sthUser->fetchObject();
				return $this->response->withJson($userData);
			}
		}
	);

	$app->post('/register',
		function ($request, $response) {
			$input = $request->getParsedBody();
			$sqlCheck = "SELECT email FROM Users WHERE email = :email LIMIT 1";
			$sthCheck = $this->dbConn->prepare($sqlCheck);
			$sthCheck->bindParam("email", $input['email']);
			$sthCheck->execute();
			$sqlCheckResult = $sthCheck->fetchAll(PDO::FETCH_OBJ);
			$num_rows = count($sqlCheckResult);
			if($num_rows == 1) {
				$resultJson->errorCode = -1;
				return $this->response->withJson($resultJson);
			} else {
				$sql = "INSERT INTO Users (password, email, first_name, last_name) VALUES (SHA1(:password), :email, :first_name, :last_name)";
				$sth = $this->dbConn->prepare($sql);
				$sth->bindParam("email", $input['email']);
				$sth->bindParam("password", $input['password']);
				$sth->bindParam("first_name", $input['first_name']);
				$sth->bindParam("last_name", $input['last_name']);
				$sth->execute();
				$sql2 = "SELECT id, first_name, email, last_name, 1 as errorCode FROM Users WHERE id = LAST_INSERT_ID()";
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
			$sql = "INSERT INTO User_Viewed (user_id, prod_id, id) VALUES (:user_id, :prod_id, :id)";
			$sth = $this->dbConn->prepare($sql);
			$sth->bindParam("user_id", $input['user_id']);
			$sth->bindParam("prod_id", $input['prod_id']);
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


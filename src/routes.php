<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes - Sam Warley - CSE 3330 - Homework 6

// $app->group('/api', function () use ($app) {

	// Get list API Route of customers
	// Using /customers

	$app->get('/customers', 
		function ($request, $response, $args) {
			$db = $this->dbConn;
			$sth= $db->prepare(
				"SELECT * FROM customers ORDER BY customerName"
			);
			$sth->execute();
			$customers = $sth->fetchAll(PDO::FETCH_ASSOC);
			return $this->response->withJson($customers);
		}
	);

	// Get list API Route of orders
	// Using /orders

	$app->get('/orders', 
		function ($request, $response, $args) {
			$db = $this->dbConn;
			$sth= $db->prepare(
				"SELECT orderNumber, status FROM orders ORDER BY orderNumber"
			);
			$sth->execute();
			$customers = $sth->fetchAll(PDO::FETCH_ASSOC);
			return $this->response->withJson($customers);
		}
	);

	// Get single order API Route
	// Using /order/status/...

	$app->get('/order/status/{orderNumber}', 
		function ($request, $response, $args) {
			$db = $this->dbConn;
			$sth= $db->prepare(
				"SELECT * FROM orders where orderNumber = :orderNumber"
			);
			$sth->bindParam("orderNumber", $args['orderNumber']);
			$sth->execute();
			$customers = $sth->fetchAll(PDO::FETCH_ASSOC);
			return $this->response->withJson($customers);
		}
	);

	// Get single customer information - API Route
	// Using /customer/...

	$app->get('/customer/{customerNumber}', 
		function ($request, $response, $args) {
			$sth = $this->dbConn->prepare(
				"SELECT * FROM customers WHERE customerNumber=:customerNumber"
			);
			$sth->bindParam("customerNumber", $args['customerNumber']);
			$sth->execute();
			$customer = $sth->fetchObject();
			return $this->response->withJson($customer);
		}
	);

	// Post a new order with information 
	// Using /orders and raw JSON input information

	$app->post('/orders',
		function ($request, $response) {
			$input = $request->getParsedBody();
			$sql = "INSERT INTO orders (orderNumber, orderDate, requiredDate, shippedDate, status, comments, customerNumber) VALUES (:orderNumber, :orderDate, :requiredDate, :shippedDate, :status, :comments, :customerNumber)";
			$sth = $this->dbConn->prepare($sql);
			$sth->bindParam("orderNumber", $input['orderNumber']);
			$sth->bindParam("orderDate", $input['orderDate']);
			$sth->bindParam("requiredDate", $input['requiredDate']);
			$sth->bindParam("shippedDate", $input['shippedDate']);
			$sth->bindParam("status", $input['status']);
			$sth->bindParam("comments", $input['comments']);
			$sth->bindParam("customerNumber", $input['customerNumber']);

			$sth->execute();
			return $this->response->withJson($input);
		}
	);

	// Change/Put a new status to an existing order
	// Using /order/status/{orderNumber} and raw JSON input information for status

	$app->put('/order/status/{orderNumber}', 
		function ($request, $response, $args) {
			$input = $request->getParsedBody();
			$sql = "UPDATE orders SET status=:status WHERE orderNumber=:orderNumber";
			$sth = $this->dbConn->prepare($sql);
			$sth->bindParam("orderNumber", $args['orderNumber']);
			$sth->bindParam("status", $input['status']);
			$sth->execute();
			$output = $sth->fetchObject();
			return $this->response->withJson($output);
		}
	);

	// Change/Put a new status to an existing order
	// Using /customer/{customerNumber} (NO raw JSON input information is needed)

	$app->delete('/customer/{customerNumber}', 
		function ($request, $response, $args) {

			$sql = "DELETE FROM customers WHERE customerNumber= :customerNumber";
			$sth = $this->dbConn->prepare($sql);
			$sth->bindParam("customerNumber", $args['customerNumber']);
			$sth->execute();
			return $this->response->withJson($args['customerNumber']);
		}
	);


// });







<?php

// Start the PHP session.
session_start();

// Include database connection.
include $_SERVER['DOCUMENT_ROOT'].'/inc/dbconnect.inc.php';


// ADD ITEM TO CART


if($_POST['stage']=="1") {

	// This block of code is responsible for adding items to the cart when posted.

	// Create a flag variable to indicate if there was an issue through executing code.
	$valid=true;

	// For each of the items in the cart
	foreach($_POST as $key=>$value){
		// Check if the value of the item is empty by comparing it to an empty string
		if($value==""){
			// If any of the values are invalid, update the valid flag to false
			$valid=false;
		}
	}

	// If the valid flag was changed to false through checking the data...
	if(!$valid){
		// Set the PHP session message to a user friendly error message
		$_SESSION['message']="Please enter all details";

		//Redirect the user back to the first checkout for them to enter valid details again
		header("location: /checkout1.php");

		//Exit the script and stop executing.
		exit();
	}else{
		//Else the data was valid
		// Create an SQL statement to insert the users details into the customer table

		$sql = "INSERT INTO `customer`
		(`user_id`, `c_fname`, `c_sname`, `c_phonenum`, `c_email`)
		VALUES
		('{$_SESSION['user_id']}',
		'{$_POST['fname']}',
		'{$_POST['sname']}',
		'{$_POST['phone']}',
		'{$_POST['email']}')";

		// We use the SQL statement above and call the mysql_query() function
		// mysqli_query( _database connection_, _sql statement_);
		// database connection - The MySql connection string we create in dbconnect.inc.PHP
		// sql statement - Valid sql query statement
		// We load this function to be called when we call the variable 'customerInsert'

		$customerInsert = mysqli_query($dbconnect, $sql);

		// Here we are checking what 'customerInsert' returns using the '!' we check f its not true

		if(!$customerInsert) {

			// Set the PHP session 'message' to a user friendly error message
			$_SESSION['message']="Problem entering customer;"

			// Redirect the user back to the checkout1 page
			header("location: /checkout1.php");

			// Exit and stop executing code.
			exit();
		}else{
			// Else the database returned true

			//The function mysqli_insert_id( _databse connection_) will return
			//the most recent auto-incremented id in a table.
			//Here it will return the ID from our customers table where we inserted above.
			//We load this ID into the 'customer_id' PHP session data

			$_SESSION['customer_id']=mysqli_insert_id($dbconnect);

			// Create an sql statement to insert the users address details into the address table.

			$sql = "INSERT INTO `address`
			(`customer_id`,
			`ad_line1`,
			`ad_line2`,
			`ad_town`,
			`ad_county`,
			`ad_postcode`)
			VALUES
			('{$_SESSION['customer_id']}',
			'{$_POST['line1']}',
			'{$_POST['line2']}',
			'{$_POST['town']}',
			'{$_POST['county']}',
			'{$_POST['pcode']}')";


			$addressInsert = mysqli_query($dbconnect, $sql);

			if(!$addressInsert) {
				$_SESSION['message']="Problem entering address information";

				header("location: /checkout1.php");

				exit();
			}else{
				$_SESSION['message']="Details Successful!!!";

				$_SESSION['address_id']=mysqli_insert_id($dbconnect);

				header("location: /checkout2.php");

				exit();
			}
		}

	}

	// We now want to set up a non autocommit transaction to the database

	// We use non autocommit transactions to follow ACID principals. We can prepare multiple
	// SQL queries to the database and execute them all at once.
	// If anything were to go wrong with the transaction, we could roll back the transaction
	// as if we never tried in the first place.

	// Disable MySQLi autocommit on query
	mysqli_query($dbconnect, "SET autocomit=0");

	// Start new MySQLi transaction
	mysqli_query($dbconnect, "START TRANSACTION");

	// insert query for sale (header)
	$query = "INSERT INTO `sale`
	(`customer_id`,
	`s_date`,
	`s_total`)
	VALUES
	({$_SESSION['customer_id']},
	NOW(),
	{$_POST['total']})";

	$saleInsert = @mysqli_query($dbconnect, $query);

	if($saleInsert) {
		$saleid = mysqli_insert_id($dbconnect);
			$productid = "";
			$qty="";
			$net="";
			//loop through the post array (sales Row)
		foreach($_POST as $key=>$value){
			if (stristr($key, 'pid')){
				$productid = $value;
			}; //getid

			if (stristr($key, 'qty')){
				$qty = $value;
			}; //get qty

			if (stristr($key, 'net')){
				$net = $value;
			};//get net total

			if($productid && $qty && $net) {
				// insert SALE_ROW Row
				$sql = "INSERT INTO `sale_row`
				(`sale_id`, `product_id`, `sr_qty`, `sr_net`)
				VALUES ({$saleid}, {$productid}, {$qty}, {$net})";
				$rowInsert = @mysqli_query($dbconnect, $sql);

				$productid="";
				$qty="";
				$net="";
				if(!$rowInsert){
					$rowRollback=true; // set rollback flag
				}
			}

		}

		if(!$rowRollback){
			//commit transaction if all gone through
			mysqli_query($dbconnect,"COMMIT");
			$_SESSION['message']="Your order has been confirmed.";
			header("location: /orderconfirm.php");
		}else{
		// rolls back transaction if issues
		mysqli_query($dbconnect, "ROLLBACK");
		$_SESSION['message']="There has been a problem.";
		header("location: /checkout3.php");
		}


	}else{
		mysqli_query($dbconnect, "ROLLBACK");
		$_SESSION['message']="There has been a problem with your order.";
		header("location: /checkout3.php");
	}


};
?>
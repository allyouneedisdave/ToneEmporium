<?php

include( $_SERVER[ 'DOCUMENT_ROOT' ] . '/inc/dbconnect.inc.php' );

session_start();




//REGISTRATION SCRIPT

//Validation
if ( $_POST[ 'mode' ] == "register" ) {

	if ( $_POST[ "r_uname" ] == ""){
		$valid = false;
	}else if ( $_POST[ "r_pword1" ] == ""){
		$valid = false;
	}else if ( $_POST[ "r_pword1" ] != $_POST[ 'r_pword2' ]){
		$valid = false;
	}else{
		$valid = true;
	};


	if (!$valid){
		$_SESSION['message'] = "Please enter valid data";

		header( "location: /?registerSuccess" );

		exit();
	};

	$username = $_POST[ 'r_uname' ];

	// Check database to see if username has been used already.

	$query = "SELECT * FROM `user` WHERE `u_username` = '{$username}'";

	$userCheck = mysqli_query( $dbconnect, $query);


	if ( mysqli_num_rows( $userCheck ) > 0 ) {

		$_SESSION[ 'message' ] = "This username is taken, please choose another.";

		header( "location: /?registerSuccess" );

		exit();
	}
	else
	{
	// Encrypt password using md5
		$password = md5( $_POST[ 'r_pword1' ] );



		$sql = "INSERT INTO `user` (`u_username`,`u_password`,`u_level`) VALUES ('{$username}','{$password}','user')";

		$register = mysqli_query( $dbconnect, $sql );

		if ( $register ){
			// Function returned true, data inserted successfuly.
			$_SESSION['message'] = "Thankyou for registering to Tutto: Happy Shopping! Please Log In!";

		}else{
			$_SESSION['message'] = "There has been a registration error!";

		};

		header( "location: /?registerSuccess" );

		exit();
	};

};



//LOGIN SCRIPT
if ( $_POST[ 'mode' ] == "login" ){
	$valid = true;

	if ( strlen( $_POST[ 'l_uname' ] ) === 0 || strlen( $_POST[ 'l_pword' ] ) === 0 ) {

		echo 'Username or password is empty';

		$valid = false;
	};

	//This array is used as a response to the ajax SCRIPT
	//to manage showing the user an error message or loggin the user in.

	// The array holds 2 values that are set during the script.
	//1. A success boolean to tell the ajax if the login was successfult or not.
	//2. A message string to the user trying to login to alert them of the issue with their credentials.

	$response = array(
		'success' => false,
		'message' => ""
	);

	if (!$valid) {
			
		$response['message'] = "There was an issue with your username and password";
		
		//Here we are echoing a json object created from the response array,
		// this means that we can parse the data at the ajax script and easily read the variables.

		echo json_encode($response);

		exit();

	}else{
		// This section of code validates the user as a user in the database.

		$username = $_POST[ 'l_uname' ];

		$password = md5( $_POST[ 'l_pword' ] );

		$sql = "SELECT * FROM `user` WHERE `u_username` = '{$username}' AND `u_password` = '{$password}'";

		$login = mysqli_query( $dbconnect, $sql);

		if ( mysqli_num_rows( $login ) > 0 ){
			while ( $row = mysqli_fetch_array( $login ) ) {
				$_SESSION[ 'user_id' ] = $row[ 'user_id' ];

				$_SESSION[ 'u_username' ] = $row[ 'u_username' ];

				$_SESSION[ 'u_level' ] = $row[ 'u_level' ];
			};

			$response['success'] = true;

			$response['message'] = "Welcome to the wonderful world of Tutto ".$_SESSION ['u_username']."!";

			echo json_encode($response);

			exit();
		}else{
		$response['message'] = "There was an issue with your username and password";
	
		echo json_encode($response);

		exit();
		};


	};


};

?>
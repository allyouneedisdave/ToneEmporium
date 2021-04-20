<?php

//The purpose of this script it to search the database for a query
// string and return the results in a JSON format.

include( '../inc/dbconnect.inc.php' );

include( '../inc/header.php' );

//Since we are going to search the database from the users search string, it is a good idea
//to remove any html special characters from the string so the database cannot be exploited from SQL injection.


$search = htmlspecialchars($_GET['term']);

$result = mysqli_query( $dbname, "SELECT * FROM 'PRODUCT' WHERE 'p_name' LIKE %{$search}" );

if($result->num_rows > 0){
$mainarray = array();

while ( $row - mysqli_fetch_array( $result ) ) {
$rowarray - array(
	"id" => $row[ 'product_id' ],
	"lable" => $row[ 'p_name' ]
);

array_push( $mainarray, $rowarray );
};
echo json_encode( $mainarray );
}else{
	echo json_encode( $mainarray );
};

?>
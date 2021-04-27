<?php


session_start();
// include database connection
include('inc/dbconnect.inc.php');
// include the header file
include('inc/header.php');
// include the Breadcrumbs file
include('inc/dynamicBreadcrumbs.php');
// include the login Script
include('inc/inc_loginform.php');

?>
	<div class="container" align="center">
		<div class="header-slider">
			<div class="header-slider-image">
				<img class="d-block img-fluid" src="images/Global/Gibson Custom Shop 67 Hero.png" alt="Gibson Custom Shop 67">
			</div>
			<div class="header-slider-image">
				<img class="d-block img-fluid" src="images/Global/LSL Badbone Hero.png" alt="LSL Badbone">
			</div>
			<div class="header-slider-image">
				<img class="d-block img-fluid" src="images/Global/Fender 60s Strat Hero.png" alt="Fender 60s Strat">
			</div>
		</div>
	</div>

	<?php


// Display All Products exclude accessories.
$general_result = mysqli_query( $dbconnect, "SELECT * FROM `product` WHERE `p_category`!='Accessories' ORDER BY RAND() LIMIT 9" );

//-------------------------------------
// Encryption testing/debuggin - remove when done.
// This is in the index page just so i can console log
// variables for testing.

// Hardcoded example of plaintext being hashed.
// then the password_verify function compares the
// plaintext with the hash to see if the hash is correct.
// This works as intended.....however!
$plainPassword = "test";
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

if(password_verify($plainPassword, $hashedPassword)){
	echo("<script>console.log('matched');</script>");
}else{
	echo("<script>console.log('not matched');</script>");
}
//-----------------------------------

//-----------------------------------
// When using the same logic but comparing
// the plaintext with the hash returned from the db
// password_verify does not find a match.

$newSql = "SELECT * FROM `user` WHERE `u_username` = 'w'";

		$newLogin = mysqli_query( $dbconnect, $newSql);

		$returnedHash = "";

		if ( mysqli_num_rows( $newLogin ) > 0 ){
			while ( $newRow = mysqli_fetch_array( $newLogin ) ) {

				$returnedHash = $newRow[ 'u_password' ];

			};
		};

		if(password_verify('w', $returnedHash)){
			echo("<script>console.log('matched');</script>");
		}else{
			echo("<script>console.log('not matched, returned hash is: $returnedHash');</script>");
		}
//-----------------------------------------------
?>

		<!-- Display the product detail in the container -->
		<div class="container">
			<div class="search-container">
			
				<div class="card">
					<h5 class="card-header card text-white mb-3" style="background-color:#D76339">Featured Guitars</h5>
					<div class="row pl-3 pr-3">
<?php
			// Loop through each row from results
			while ( $row = mysqli_fetch_array( $general_result ) ) {
				?>
						<div class="col-lg-4 col-md-6 mb-4">
							<div class="card h-100 border-primary mb-3">
                                
								<div class="image-container">
									<a href="/detail.php?id=<?php echo $row['product_id'] ?>"><img class="card-img-top" style="width:100%; height:100%;" src="<?php echo $row['p_image_thumb'] ?>" alt=""></a>
								</div>

								<div class="card-body">
									<h4 class="card-title">
										<a href="/detail.php?id=<?php echo $row['product_id'] ?>">
											<?php echo $row['p_name']; ?>
										</a>
									</h4>
									<p class="card-text">
										<?php echo $row['p_colour'] ?>
									</p>
									<h3>£
										<?php echo $row['p_sale_price']; ?>
									</h5>
							

								
								</div>
								<div class="card-footer bg-transparent border-primary">
									<div class="btn btn-success quickAdd" data="<?php echo $row['product_id'] ?>">Add <i class="fa fa-shopping-basket"></i>
									</div>
									<!--This is the see product button-->
									<a href="/detail.php?id=<?php echo $row['product_id'] ?>" class="btn btn-primary float-right">See Product</a>
								</div>
							</div>
						</div>
						<?php }; ?>
						<!--Close while loop-->
					</div>
				</div>
		
			</div>
  
			<!--Close search-container-->
		</div>
		<!--Close Row-->
		<!-- include the Footer File -->
		<?php include( 'footer.php' );?>

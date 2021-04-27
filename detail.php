<?php
session_start();

// include database connection
include('inc/dbconnect.inc.php' );
// include the header file
include('inc/header.php');
// include the Breadcrumbs file
include('inc/dynamicBreadcrumbs.php');
// include the Login Form 
include('inc/inc_loginform.php');
// include Cart Display

?>

    <script type="text/javascript">
        $(document).ready(function() {

            $('#addtocartlink').click(function() {

                var randNum = Math.floor(Math.random() * 1000000000);

                $.ajax({
                    url: "/mng/mng_cart.php?action=add&rand=" + randNum,
                    dataType: 'text',
                    type: 'POST',
                    data: 'productid=' + $('#addtocartlink').attr('alt'),

                    beforeSend: function() {
                    $('#cartload').html('Loading...');
                },
                complete: function() {
                    $('#cartload').html('');
                    cartDisplayUpdate();
                    updateHeaderCart()
                    updateCartNumber()
                },
                success: function(result) {

                    if (result === "Error") {
              
                        $('#login-modal').modal('show');
                    } else {

                        $('#headercart').html('').append(result);
                    }

                }
                });
                return false; //stops link clicking
            });

        });
        
        
function confirmChoice(productId) {
    response = confirm ("Are you sure you want to delete?");
    if(response==1){
    console.log("/mng/mng_content.php?action=delete&id="+productId)

        window.location="/mng/mng_content.php?action=delete&id="+productId; 
    } else{
        return false;
    }
}

    </script>
    <?php
//This gets the ID from the database and echos out the result. 
$id = $_GET['id'];
$result = mysqli_query( $dbconnect, "SELECT * FROM `product` WHERE `product_id`={$id}");
echo mysqli_error( $dbconnect );

?>

        <!--Loop through each row from results -->
        <?php while ( $row = mysqli_fetch_array( $result ) )  { ?>
    <!-- Page Content -->
        <div class="container">
           <div class="cartdisplaydiv">
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card mt-4">
                        <div style="width:215px;">
                            <img class="card-img-top img-fluid" style="width:100%; height:100%;" src="<?php echo $row['p_image_one'] ?>" alt="">
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">
                                <?php echo $row['p_name']; ?>
                            </h3>
                             <p class="card-text">
										<?php echo $row['p_colour'] ?>
									</p>
                            <h4>£
                                <?php echo $row['p_sale_price']; ?>
                            </h4>
                            <p class="card-text">
                                <?php echo $row['p_description'] ?>
                            </p>
                
                            <a href="#" id="addtocartlink" class="btn btn-success" alt="<?php echo $row['product_id'] ?>">add to cart</a>
                        
                        <?php 
                        
                            if(isset($_SESSION['u_level'])){
                            if($_SESSION['u_level']=='admin'){
                        ?>
                        <div class="btn btn-danger" onclick="confirmChoice(<?php echo $row['product_id'] ?>)">Delete</div> 
                         
                            <a class="btn btn-warning" href="update.php?action&id=<?php echo $row['product_id'] ?>">Update</a>
                        <?php
                            }
                            }
                        ?>
                    </div>
                    </div>
                    <!-- /.card -->


                </div>
                <!-- /.col-lg-9 -->
            </div>
            
           
            <?php
        // Display All Products
$general_result = mysqli_query( $dbconnect, "SELECT * FROM `product` ORDER BY RAND() LIMIT 3" );
  ?>
            
<!-- Display the product detail in the container -->
	<div class="container mt-3">
			<div class="search-container">
				<div class="home-title-container"> 
				<div class="home-featured-title"> </div>
					<div style="clear:both"></div>
				</div>
    
				<div class="row">
<?php
			// Loop through each row from results
			while ( $row = mysqli_fetch_array( $general_result ) ) {
				?>
						<div class="col-lg-4 col-md-6 mb-4">
							<div class="card h-100 border-primary mb-3">
                                <h5 class="card-header card text-white mb-3" style="background-color:#D76339">Featured Guitars</h5>
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
								</h3>
					
					
							</div>
							<div class="card-footer">
								<!--This is the star ratings-->

								<!--This is the see product button-->

								<div class="btn btn-success quickAdd" data="<?php echo $row['product_id'] ?>">Add <i class="fa fa-shopping-basket"></i></div>
								<a href="/detail.php?id=<?php echo $row['product_id'] ?>" class="btn btn-primary float-right">See Product</a>


							</div>
							<!--Footer close-->
							</div>
						</div>
						<?php }; ?>
						<!--Close while loop-->
				</div>
   		     
        
        
        </div>
            </div>
        <!-- /.container -->
        <?php }; ?>
        <!--Close the loop	-->
        <!--include the Footer File	-->
        <?php include( 'footer.php' );?>
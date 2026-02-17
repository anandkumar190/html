<?php require("connect.php");?>
<?php 
 if(isset($_POST['btnsearch'])){
     print_r($_GET);
     die("form");
 }

$title="Booking"?>
<?php require("header.php");?>

<?php 
$salesmans=array();
$outlets=array();
 $res=mysqli_query($con,"select * from employees where usertype='1'");
  $booking=mysqli_query($con,"select * from booking");
 
 while($row=mysqli_fetch_array($res)){$salesmans[$row['id']]= $row['name'];}
 
  $res1=mysqli_query($con,"select * from outlets");
 
 while($row1=mysqli_fetch_array($res1)){$outlets[$row1['id']]= $row1['name'];}
 

 
 
?>

<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <?php include("content-header.php");?>
<link rel="stylesheet" href="assets/node_modules/datatables/jquery.dataTables.min.css"/>

<link rel="stylesheet" href="dist/dist/css/bootstrapValidator.min.css"/>
    <!-- Main content -->
    <section class="content">
      <!-- Main row -->
      <div class="row">
        <!-- Left col -->
        <section class="col-lg-12">
          <!-- Custom tabs (Charts with tabs)-->
          <div class="nav-tabs-custom">
            <!-- Tabs within a box -->
            <ul class="nav nav-tabs pull-left">
              <li class="active"><a href="#today-activity" data-toggle="tab">Booking Report</a></li>
              
              
            </ul>
            
            <div class="tab-content no-padding">
              
              
              <!--Today Activity tab Start-->
              
              
              <div class="chart tab-pane active" id="today-activity" style="position: relative; min-height: 300px;">
               <!--table start-->  
                    <!-- /.row -->
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
           <div class="box-header">
              <h3 class="box-title">Select Booking Period
                
              </h3>

              <div class="form-inline">
                <form id="searchform" action="booking" method="post">
                <!-- Date range -->
                <div class="form-group">
                   <label>Date Range:</label>

                  <div class="input-group">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" autocomplete="off" class="form-control pull-right" id="reservation" name="reservation">
                  </div>
                 <!-- /.input group -->
                </div>
               <!-- /.form group -->
               <!-- Date and time range -->
              
              <!-- Date range -->
                <div class="form-group">
                   <label>Select Salesman:</label>

                  <div class="input-group">
                    <div class="input-group-addon">
                      <i class="fa fa-user"></i>
                    </div>
                    <?php 
					   $res=mysqli_query($con,"select * from employees where usertype='1'");
					?>
                    <select  class="form-control pull-right " id="employee" name="employee">
                      <option value="">Select Salesman</option>
                      <?php while($row=mysqli_fetch_array($res)){?>
                      <option value="<?php echo $row['id']?>"><?php echo $row['name'].' - ('.$row['empid'].')'?></option>
					  <?php }?>
                    </select>
                  </div>
                 <!-- /.input group -->
                </div>
               <!-- /.form group -->
               <!-- Date and time range -->
              
              <!-- Date range -->
                <div class="form-group">
                   <label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>

                  <div class="input-group">
                    
                    <button type="submit" id="btnsearch" name="btnsearch" value="btnsearch" class="btn btn-default form-control pull-right " id="employee">
                      <i class="fa fa-search"></i> Show Report
                      
                    </button>
                  </div>
                 <!-- /.input group -->
                </div>
               <!-- /.form group -->
               <!-- Date and time range -->
              
                </form>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              
              <!-- Row -->
					<div class="row">
						<div class="col-sm-12">
							<div class="panel panel-default card-view">
								
								<div class="panel-wrapper collapse in">
									<div class="panel-body">
										<div class="table-wrap">
										    
										    		<table id="userstable" class="table" data-paging="false" data-filtering="true" data-sorting="true">
												<thead>
												<tr>
                                            
													<th>Date</th>
													<th>Outlets</th>
													<th>salesman</th>
													   <?php $res=mysqli_query($con,"select id,productid from skus"); while($row=mysqli_fetch_array($res)){?>
                                                        <th><?php echo $row['productid'];?></th>
                                                        <?php }?>
													<th>Total</th>
												</tr>
												</thead>
												<tbody>
									 <?php $total=0;  while($row2=mysqli_fetch_array($booking)){ 
									     $b_id=$row2['id'];
									   $booking_items=mysqli_query($con,"select * from booking_item  where booking_id_fk='$b_id'");
									   $items=array();
									   $row3=0;
									    while( $row3=mysqli_fetch_array($booking_items)){$items[$row3['product_id']]=$row3['qty_no']." / <br>".$row3['new_price'];}
									 ?> 
												<tr>
													<td><?php echo $row2['booking_time'];?></td>
													<td><?php echo $outlets[$row2['outlet_id']];?></td>
													<td><?php echo $salesmans[$row2['user_id']];?></td>
													   <?php $res=mysqli_query($con,"select id,productid from skus"); while($row=mysqli_fetch_array($res)){?>
                                                        <td><?php echo $items[$row['id']];?></td>
                                                        <?php }?>
													<td><?php echo $row2['total_amount'];?></td>
												</tr>
                                            <?php $total+=$row2['total_amount']; }?>  
                                            
                                            
												</tbody>
											</table>
											
									</div>
								</div>
							</div>
							</div>
						</div>
					</div>
					<!-- /Row -->
              
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
      </div>
               <!--table close-->  
              </div>
              
              
              
              <!--TreeView tab Start-->
              
            </div>

          </div>
          <!-- /.nav-tabs-custom -->

        
        </section>
        <!-- /.Left col -->
        
      </div>
      <!-- /.row (main row) -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  


<?php include("footer.php");?>
<?php include("jsscript.php"); ?>

<script src="assets/node_modules/datatables/jquery.dataTables.min.js"></script>
 <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
    <!-- end - This is for export functionality only -->

<script>
    
$(document).ready(function(){
	
	
       $('#reservation').daterangepicker({
		format: 'YYYY-MM-DD',
	    separator: ':',
		opens: 'right'
       });
	
	//$('#reservation').daterangepicker({format:'yyyy-m-dd'});
   $("#empdoj").datepicker({format:'yyyy-m-dd'});
   
   $("#userstable").dataTable(
				{
				  dom: 'Bfrtip',
				  sort:false,
			
				  destroy:true,
				  paging:true,
				  buttons: [
                'copy', { extend: 'csv', title: function () { var printTitle = 'All Visitis'; return printTitle; } }, 'excel', 'pdf', { extend: 'print', title: function () { var printTitle = ''; return printTitle; } }
                   ]
				});
   
});

</script>
<script src="dist/dist/js/bootstrapValidator.min.js"></script>
<script>
$(document).ready(function() {
    $('#searchform')
        .bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                reservation: {
                    message: 'Date Range can\'t be empty',
                    validators: {
                        notEmpty: {
                            message: 'Please Select Date Range'
                        }                        
                    }
                },
                employee: {
                    validators: {
                        notEmpty: {
                            message: 'Please Select employee'
                          }
                        }
                    }
			
	             }
        }) .on('success.form.bv', function(e) {
            // Prevent form submission
            e.preventDefault();
			  e.target.submit();
	          		
          
        });
});

</script>
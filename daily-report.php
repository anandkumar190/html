<?php require("connect.php");?>
<?php $title="Mothly Attendance"?>
<?php require("header.php");?>



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
              <li class="active"><a href="#today-activity" data-toggle="tab">Team Report</a></li>
              
              
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
              <h3 class="box-title">Employees Daily  Report
                
              </h3>

              <div class="form-inline">
                <form id="searchform" action="employee-daily-report-of-selected-date.php" method="post">
                <!-- Date range -->
                <div class="form-group">
                   <label>Select Date:</label>

                  <div class="input-group">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" autocomplete="off" class="form-control pull-right" id="reportdate" name="reportdate">
                  </div>
                 <!-- /.input group -->
                </div>
               <!-- /.form group -->
               <!-- Date and time range -->
              
              <!-- Date range -->
                
               <!-- /.form group -->
               <!-- Date and time range -->
              
              <!-- Date range -->
                <div class="form-group">
                   <label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>

                  <div class="input-group">
                    
                    <button type="submit" id="btnsearch" name="btnsearch" class="btn btn-default form-control pull-right " id="employee">
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
	
	
    //   $('#reservation').daterangepicker({
//		format: 'YYYY-MM-DD',
//	    separator: ':',
//		opens: 'right'
 //      });
	
	//$('#reservation').daterangepicker({format:'yyyy-m-dd'});
   $("#reportdate").datepicker({format:'yyyy-m-dd'});
   
   $("#userstable").dataTable(
				{
				  dom: 'Bfrtip',
				  sort:false,
				  
				  destroy:true,
				  paging:false,
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
                reportdate: {
                    message: 'Date  can\'t be empty',
                    validators: {
                        notEmpty: {
                            message: 'Please Select Date '
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
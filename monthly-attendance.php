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
              <h3 class="box-title">Select Employee And Report Period
                
              </h3>

              <div class="form-inline">
                <form id="searchform" action="employee-report-of-selected-period.php" method="post">
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
                   <label>Select Employee:</label>

                  <div class="input-group">
                    <div class="input-group-addon">
                      <i class="fa fa-user"></i>
                    </div>
                    <?php 
					   $res=mysqli_query($con,"select * from employees where usertype='1' order by name ");
					?>
                    <select  class="form-control pull-right " id="employee" name="employee">
                      <option value="">Select Employee</option>
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
                                <div style="margin-bottom: 10px;">
    <button class="btn btn-primary" onclick="printTable()">🖨️ Print Table</button>
    <button class="btn btn-success" onclick="downloadCSV()">📥 Download CSV</button>
  </div>

                              <div class="table-wrap" id="responseDiv">	
                              </div>

                          </div>			<!-- /panel-body -->
                      </div>			<!-- /panel-wrapper -->
                      
                    </div>			<!-- /Row -->
                  </div>			<!-- /Row -->
                </div>			<!-- /Row --> 
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
		            $('#responseDiv').html("");

            e.preventDefault();
            var $form = $(e.target);
            var formData = $form.serialize();

            // Use AJAX to submit the form
            $.ajax({
                type: 'POST',
                url: $form.attr('action'),  // Make sure the form has an action attribute with the correct endpoint
                data: formData,
                success: function(response) {
                    // Assuming response contains the data you want to display in the div
                    $('#responseDiv').html(response);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Optionally handle errors here
                    $('#responseDiv').html('<p>An error occurred: ' + errorThrown + '</p>');
                }
            });
	          		
          
        });
});

</script>

<script>
// PRINT FUNCTION
function printTable() {
    const tableDiv = document.getElementById("responseDiv");
    if (!tableDiv || !tableDiv.innerHTML.trim()) {
        alert("No table data to print.");
        return;
    }
    const newWin = window.open("");
    newWin.document.write('<html><head><title>Print</title>');
    newWin.document.write('<style>table, th, td { border: 1px solid black; border-collapse: collapse; padding: 5px; }</style>');
    newWin.document.write('</head><body>');
    newWin.document.write(tableDiv.innerHTML);
    newWin.document.write('</body></html>');
    newWin.document.close();
    newWin.print();
}

// CSV DOWNLOAD FUNCTION
function downloadCSV() {
    const table = document.querySelector("#responseDiv table");
    if (!table) {
        alert("No table to export.");
        return;
    }

    const rows = table.querySelectorAll("tr");
    let csv = [];

    rows.forEach(row => {
        const cols = row.querySelectorAll("th, td");
        const rowData = [];
        cols.forEach(col => {
            let text = col.innerText.replace(/\n/g, " ").replace(/,/g, " "); // clean line breaks & commas
            rowData.push('"' + text + '"');
        });
        csv.push(rowData.join(","));
    });

    const blob = new Blob([csv.join("\n")], { type: "text/csv" });
    const link = document.createElement("a");
    link.download = "mothly-attendance-report.csv";
    link.href = URL.createObjectURL(blob);
    link.click();
}
</script>

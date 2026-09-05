<?php require("connect.php");?>
<?php $title="Mothly Attendance Distributor "?>
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
              <li class="active"><a href="#today-activity" data-toggle="tab"> Distributor Team Report  </a></li>
              
              
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
                   <label>Select Distributor:</label>

                  <div class="input-group">
                    <div class="input-group-addon">
                      <i class="fa fa-user"></i>
                    </div>
                    <?php 
					   $res=mysqli_query($con,"select * from employees where usertype='3' order by name ");
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
            <div class="box-body" style="padding: 8px 10px;">
                <div style="margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                    <div>
                        <button type="button" class="btn btn-sm btn-primary" onclick="printTable()"><i class="fa fa-print"></i> 🖨️ Print (Fit to Sheet)</button>
                        <button type="button" class="btn btn-sm btn-success" onclick="downloadCSV()"><i class="fa fa-download"></i> 📥 Download CSV</button>
                    </div>

                </div>

                <div class="table-responsive" id="responseDiv" style="border: none; overflow-x: auto;">	
                </div>
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
// PRINT FUNCTION (Optimized for single-sheet landscape printing)
function printTable() {
    const tableDiv = document.getElementById("responseDiv");
    if (!tableDiv || !tableDiv.innerHTML.trim()) {
        alert("No table data to print.");
        return;
    }
    const newWin = window.open("", "_blank");
    newWin.document.write('<!DOCTYPE html><html><head><title>Monthly Attendance Report</title>');
    newWin.document.write('<style>');
    newWin.document.write('@page { size: A4 landscape; margin: 5mm 4mm; }');
    newWin.document.write('* { box-sizing: border-box; }');
    newWin.document.write('body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; margin: 0; padding: 2px; color: #111; font-size: 8pt; background: #fff; }');
    newWin.document.write('table { width: 100% !important; border-collapse: collapse !important; font-size: 8pt !important; table-layout: auto !important; }');
    newWin.document.write('tr { page-break-inside: avoid; }');
    newWin.document.write('th, td { border: 1px solid #555 !important; padding: 2px 2px !important; line-height: 1.15 !important; }');
    newWin.document.write('th { background-color: #eee !important; font-weight: bold !important; text-align: center !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }');
    newWin.document.write('td.col-num { text-align: right !important; white-space: nowrap !important; }');
    newWin.document.write('td.col-nowrap { text-align: center !important; white-space: nowrap !important; }');
    newWin.document.write('.label { border: 1px solid #888; padding: 0 2px; font-size: 7pt; border-radius: 2px; }');
    newWin.document.write('</style></head><body>');
    newWin.document.write(tableDiv.innerHTML);
    newWin.document.write('</body></html>');
    newWin.document.close();
    setTimeout(function() {
        newWin.focus();
        newWin.print();
    }, 250);
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

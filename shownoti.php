<?php require("connect.php");?>
<?php $title="Send Notification To Team"?>
<?php require("header.php");?>

<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <?php include("content-header.php");?>
<link rel="stylesheet" href="assets/node_modules/datatables/jquery.dataTables.min.css"/>

<link rel="stylesheet" href="assets/bootstrap-toggle.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.2.7/css/select.dataTables.min.css">
  
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
              <li class="active"><a href="#today-activity" data-toggle="tab">Active Users</a></li>
              
              
            </ul>
            
            <div class="tab-content no-padding">
              
              
              <!--Today Activity tab Start-->
              
              
              <div class="chart tab-pane active" id="today-activity" style="position: relative; min-height: 300px;">
               <!--table start-->  
                    <!-- /.row -->
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
         
          <!--  <div class="box-header">
              <h3 class="box-title">Responsive Hover Table</h3>

              <div class="box-tools">
                <div class="input-group input-group-sm" style="width: 150px;">
                  <input type="text" name="table_search" class="form-control pull-right" placeholder="Search">

                  <div class="input-group-btn">
                    <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              
              <!-- Row -->
					<div class="row">
						<div class="col-sm-12">
							<div class="panel panel-default card-view">
								<div class="panel-heading">
									<div class="pull-left">
										<h6 class="panel-title txt-dark">Team</h6>
         <div class="row">
   <div class="col-lg-12">
     <form>
     <div class="form-group-lg">
      <div class="form-inline">
       
       <button type="button" id="deletenoti" class="btn btn-danger"><span class="fa fa-remove"></span> Delete Selected Notification</button>
       </div>
          <div class="progress progress-striped active" id="progress" style="display:none;">
            <div class="progress-bar progress-bar-success" style="width: 100%">
            </div>
          </div>
      </div>
     </form>
   </div>   
  </div>  
                                         
									</div>
									<div class="clearfix"></div>
								</div>
								<div class="panel-wrapper collapse in">
              
									<div class="panel-body">
										<div class="table-wrap">
											<table id="userstable" class="table" data-paging="true" data-filtering="true" data-processing="true" data-sorting="true">
												<thead>
												<tr>
													<th>Select</th>
													<th>UserName</th>
                                                    <th>EmpCode</th>
													<th>Title</th>
                                                    <th>Notification</th>
													<th>Date</th>
													<th>Time</th>
                                                    
												</tr>
												</thead>
												<tbody>
                                                    
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
    <script src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>
    <!-- end - This is for export functionality only -->
<script src="assets/bootstrap-toggle.min.js"></script>
<script>
	function loaddata()
	{	 
          $.ajax({
		  url:"api/sendnoti.php?show",
		  type:"POST",
		  contentType:"application/json; charset=utf-8",
		  success:function(data){
			   //alert(data);
			    data=JSON.parse(data);
	            $("#userstable").dataTable(
				{
					columnDefs: [ {
                    orderable: false,
                    className: 'select-checkbox',
                    targets:   0
                    } ],
                    select: {
                    style:    'os',
                    selector: 'td:first-child'
                    },
                   order: [[ 1, 'asc' ]],
				  dom: 'Bfrtip',	
				  sort:false,
				  
				  data:data,
				  destroy:true,
				  paging:false,
				  processing: true,
                  language: {
                        processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '},
				  
				  buttons: [
                'copy', { extend: 'csv', title: function () { var printTitle = 'All Devices'; return printTitle; } }, 'excel', 'pdf', { extend: 'print', title: function () { var printTitle = ''; return printTitle; } }
            ],
				  columns:[{
					   data:'id',render:function(value){
						   
						  return "<input type='hidden' id='select' value='"+value+"' />";
						  }},{data:'name'},{data:'empid'},{data:'title'},{data:'message'},{data:'date'},{data:'time'}]
		});		   
			  },
		  error:function(e){
			   alert(e.error);
			  }	  
		 });
	}

$(document).ready(function(){
   loaddata();	  
   
   $('#deletenoti').click(function(){
	     if(confirm('Do You want to delete Selected Notification'))
		 {
			 var ids=Array();
			 var table=$("#userstable").DataTable();
		     var data = table.rows('.selected').data();      
         
		     if(data.length<=0)
		     {
			    alert("Please Select any Row in table");
			    return;
		     }
			 for(var i=0;i<data.length;i++)
			 {
				 ids.push(data[i].id);
			 }
			 var progress=$("#progress");
		     progress.fadeIn("slow");
		     $.ajax({
			   url:'api/sendnoti?delete',
			   type:'post',
			   data:{'ids':ids},
			   success: function(data){
				     progress.fadeOut("slow");
					 alert(data);
					 
					 loaddata();
					 
				   },
			   error:function(e){}
			 });
		 
		 }
	   
	   });
   
});

</script>


<?php require("connect.php");?>
<?php $title="state";?>
<?php require("header.php");?>

<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <?php include("content-header.php");?>

   <link href="assets/node_modules/datatables/jquery.dataTables.min.css" rel="stylesheet"/>
   <link rel="stylesheet" href="assets/bootstrap-toggle.min.css"/> 
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
              <li class="active"><a href="#today-activity" data-toggle="tab">State</a></li>
  
              
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
            /.box-header -->
            <div class="box-body table-responsive no-padding">
              
              <!-- Row -->
					<div class="row">
						<div class="col-sm-12">
							<div class="panel panel-default card-view">
								<div class="panel-heading">
									<div class="pull-left">
										<h6 class="panel-title txt-dark">Products</h6>
									</div>
									<div class="clearfix"></div>
								</div>
								<div class="panel-wrapper collapse in">
									<div class="panel-body">
										<div class="table-wrap">
											<table id="userstable" class="table" data-paging="false" data-filtering="true" data-sorting="true">
												<thead>
												<tr>
											
									
												    <th>Edit</th>
													<th>State Name</th>
													<th>No Of City </th>
													<th>No Of Regions</th>
													<th>No Of Route</th>
													<th>No Of Outlet</th>
											
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

 
    <!-- start - This is for export functionality only -->
    <script src="assets/node_modules/datatables/jquery.dataTables.min.js"></script>
     
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>

    <!-- end - This is for export functionality only -->
    <script src="assets/bootstrap-toggle.min.js"></script>

<script>

	function loaddata()
	{
     $.ajax({
		  url:"api/state.php/?show",
		  type:"POST",
		  contentType:"application/json; charset=utf-8",
		  success:function(data){
			   //alert(data);
			    data=JSON.parse(data);
	            $("#userstable").dataTable(
				{

				  dom: 'Bfrtip',	
				  sort:false,
				  data:data,
				  destroy:true,
				  paging:false,
				  processing: true,
				  language: {
                        processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '},
				  
				  buttons: [
                'copy', { extend: 'csv', title: function () { var printTitle = 'All Visitis'; return printTitle; } }, 'excel', 'pdf', { extend: 'print', title: function () { var printTitle = ''; return printTitle; } }
                   ],
				  columns:[
						{
					   data:'id',render:function(value){
						   return "<a href='edit-state?editid="+value+"'><span class='fa fa-edit'></span></a>";
						  }},
			
						  {
							data:'name'	
						  },
						  {
							data:'total_city_count'	
						  },
						  {
							data:'total_region_count'	
						  },
						  {
							data:'total_route_count'	
						  },
						  {
							data:'total_outlet_count'	
						  },
				
					  ]
				});	
				$(function() {
                  $('.status').bootstrapToggle();
               	  //$('.track').bootstrapToggle();
                 });	   
			  },
		  error:function(e){
			   alert(e.error);
			  }	  
		 });
   
	}

$(document).ready(function(){
   loaddata();


   $('#userstable tbody').on( 'click','tr td', function (){
     
	 var ischecked=$(this).children('.toggle').children('.status').prop("checked"); 
	var id=$(this).children('input:hidden').val();
	 
		if(ischecked)
		{	
		  	  	//alert(id);
		         $.ajax({
		         url:"api/state.php?statestatus&status=0&id="+id,
		         type:"POST",
		         contentType:"application/json; charset=utf-8",
			     success: function(data){
			         		if(data=="success")
							{
								loaddata();
								
							}
			 		 },
				error:function(e){
					}	 
			 });
		}
		else{
		  $.ajax({
		         url:"api/state.php?statestatus&status=1&id="+id,
		         type:"POST",
		         contentType:"application/json; charset=utf-8",
			     success: function(data){
			         		if(data=="success")
							{
								loaddata();
								
							}
			 		 },
				error:function(e){
					}	 
			 });
		}
    });


});

</script>



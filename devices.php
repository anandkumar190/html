<?php require("connect.php");?>
<?php $title="User's Devices";?>
<?php require("header.php");?>

<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <?php include("content-header.php");?>
<link rel="stylesheet" href="assets/node_modules/datatables/jquery.dataTables.min.css"/>

<link rel="stylesheet" href="assets/bootstrap-toggle.min.css"/>
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
              <li class="active"><a href="#today-activity" data-toggle="tab">Active Devices</a></li>
              <li><a href="#tear-tree-view" data-toggle="tab">InActive Devices</a></li>
              
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
										<h6 class="panel-title txt-dark">Active Devices</h6>
									</div>
									<div class="clearfix"></div>
								</div>
								<div class="panel-wrapper collapse in">
              
									<div class="panel-body">
										<div class="table-wrap">
											<table id="userstable" class="table" data-paging="false" data-filtering="true" data-processing="true" data-sorting="true">
												<thead>
												<tr>
													<th>Action</th>
													<th>Status</th>
												    <?php if($empemail=="vivek@vivans.co.in"||$empemail=="rohit@fricbergen.com"||$empemail=="admin@vivans.co.in"){ ?>
												    <th>Location</th>
												    <?php } ?>
													<th>UserName</th>
													<th>DeviceName</th>
                                                    <th>ModelNo</th>
													<th>AppVersion</th>
													<th>OsVersion</th>
                                                    <th>LoginDate</th>
                                                    
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
              <div class="chart tab-pane" id="tear-tree-view" style="position: relative; min-height: 300px;">
                
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
										<h6 class="panel-title txt-dark">InActive Devices</h6>
									</div>
									<div class="clearfix"></div>
								</div>
								<div class="panel-wrapper collapse in">                            
                                  
                                
									<div class="panel-body">
										<div class="table-wrap">
                                           
											<table id="todaytable" class="table" data-paging="true" data-filtering="true" data-sorting="true">
												<thead>
												<tr>
													<th>Action</th>
													<th>Status</th>
													<th>UserName</th>
													<th>DeviceName</th>
                                                    <th>ModelNo</th>
													<th>AppVersion</th>
													<th>OsVersion</th>
                                                    <th>LoginDate</th>
                                                    
                                                    
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
            <!-- table -->    
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
    <!-- end - This is for export functionality only -->
<script src="assets/bootstrap-toggle.min.js"></script>
<script>
	function loaddata()
	{	 
          $.ajax({
		  url:"api/login.php?devices&status=1",
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
                'copy', { extend: 'csv', title: function () { var printTitle = 'All Devices'; return printTitle; } }, 'excel', 'pdf', { extend: 'print', title: function () { var printTitle = ''; return printTitle; } }
            ],
				  columns:[{
							 data:'id',render:function(value){
						     return "<a href='api/login?delete_devices&id="+value+"' onclick=\"return confirm('Are you sure you want to delete this Device?');\"><span class='fa fa-trash'></span></a> ";
						  }},{
							   data:{id:'id',status:'status'},render:function(data,type,set){
						   if(data.status=="1")
						   {
						      return "<input type='hidden'  value='"+data.id+"'/>"+"<input type='checkbox' data-toggle='toggle' class='status' data-size='mini' data-on='Active' data-off='Inactive' data-onstyle='success' data-offstyle='danger' checked='checked' value='"+data.status+"' />";
						   }
						   else
						   {
						     return "<input type='hidden' value='"+data.id+"'/>"+"<input type='checkbox' data-toggle='toggle' class='status' data-size='mini' data-on='Active' data-off='Inactive' data-onstyle='success' data-offstyle='danger'  value='"+data.status+"' />";
						   }
						  }},
						  <?php if($empemail=="vivek@vivans.co.in"||$empemail=="rohit@fricbergen.com"||$empemail=="admin@vivans.co.in"){ ?>
						  {
							data:{id:'id',track:'track'},render:function(data,type,set){
								if(data.track==1)
								{
							    return "<input type='hidden' id='id' name='id' value='"+data.id+"'/>"+"<input type='checkbox' data-toggle='toggle' class='track' data-size='mini' data-on='LocationOn' data-off='LocationOff' data-onstyle='info' data-offstyle='warning' checked='checked' value='"+data.track+"' />";
								}
								else
								{
									return "<input type='hidden' id='id' name='id' value='"+data.id+"'/>"+"<input type='checkbox' data-toggle='toggle' class='track' data-size='mini' data-on='LocationOn' data-off='LocationOff' data-onstyle='info' data-offstyle='warning'  value='"+data.track+"' />";
								}
							}
						},
						<?php } ?>
						{data:{username:'username',empid:'empid'},render:function(data,type,set){return data.username+"("+data.empid+")";}},{data:'name'},{data:'modelno'},{data:'appversion'},{data:'osversion'},{data:'logindate'}
					  ]
				
				});
				
				$(function() {
                  $('.status').bootstrapToggle();
               	  $('.track').bootstrapToggle();
                 });
						   
			  },
		  error:function(e){
			   alert(e.error);
			  }	  
		 });
	}


function loaddatatoday()
	{
     $.ajax({
		  url:"api/login.php?devices&status=0",
		  type:"POST",
		  contentType:"application/json; charset=utf-8",
		  success:function(data){
			   //alert(data);
			   data=JSON.parse(data);
	            $("#todaytable").dataTable(
				{
			      dom:'Bfrtip',
				  sort:false,
				  paging:false,
				  data:data,
				  destroy:true,
				  
				  buttons: [
                'copy', { extend: 'csv', title: function () { var printTitle = 'All Devices'; return printTitle; } }, 'excel', 'pdf', { extend: 'print', title: function () { var printTitle = ''; return printTitle; } }
            ],
				 columns:[{
							 data:'id',render:function(value){
						     return "<a href='api/login?delete_devices&id="+value+"' onclick=\"return confirm('Are you sure you want to delete this user?');\"><span class='fa fa-trash'></span></a> ";
						  }},{
					   data:{id:'id',status:'status'},render:function(data,type,set){
						   if(data.status=="1")
						   {
						  return "<input type='hidden' name='id' value='"+data.id+"'/>"+"<input type='checkbox' data-toggle='toggle' class='status' data-size='mini' data-on='Active' data-off='Inactive' data-onstyle='success' data-offstyle='danger' checked='checked' value='"+data.status+"' />";
						   }
						   else
						    {
						   return "<input type='hidden' name='id' value='"+data.id+"'/>"+"<input type='checkbox' data-toggle='toggle' class='status' data-size='mini' data-on='Active' data-off='Inactive' data-onstyle='success' data-offstyle='danger' value='"+data.status+"' />";
							}
						  }},
						  
						{data:{username:'username',empid:'empid'},render:function(data,type,set){return data.username+"("+data.empid+")";}},{data:'name'},{data:'modelno'},{data:'appversion'},{data:'osversion'},{data:'logindate'}
					  ]

				});
				
						   
			  },
		  error:function(e){
			   alert(e.error);
			  }	  
		 });
   
	}



$(document).ready(function(){
   loaddata();
   loaddatatoday();
   $("#empdoj").datepicker({format:'yyyy-m-dd'});
	  
$('#userstable tbody').on( 'click','tr td', function (){
     
	 var ischecked=$(this).children('.toggle').children('.status').prop("checked"); 
	var id=$(this).children('input:hidden').val();
		if(ischecked)
		{		  	
		         $.ajax({
		         url:"api/login.php?devicestatus&status=0&id="+id,
		         type:"POST",
		         contentType:"application/json; charset=utf-8",
			     success: function(data){
			         		if(data=="success")
							{
								loaddata();
								loaddatatoday();
							}
			 		 },
				error:function(e){
					}	 
			 });
		}
		else{
		  $.ajax({
		         url:"api/login.php?devicestatus&status=1&id="+id,
		         type:"POST",
		         contentType:"application/json; charset=utf-8",
			     success: function(data){
			         		if(data=="success")
							{
								loaddata();
								loaddatatoday();
							}
			 		 },
				error:function(e){
					}	 
			 });
		}
    });
   $('#todaytable tbody').on( 'click', 'tr td', function (){
	    var ischecked=$(this).children('.toggle').children('.status').prop("checked");
	var id=$(this).children('input:hidden').val();
		if(ischecked)
		{
		 $.ajax({
		         url:"api/login.php?devicestatus&status=0&id="+id,
		         type:"POST",
		         contentType:"application/json; charset=utf-8",
		 	     success: function(data){
		 	         		if(data=="success")
		 		 			{
		 		 				loaddata();
		 		 				loaddatatoday();
		 		 			}
		 		 			
		 	 	 	 },
		 		error:function(e){
		 		 	
		 		 	}	 
		 	 });
		}
		else{
		  $.ajax({
		         url:"api/login.php?devicestatus&status=1&id="+id,
		         type:"POST",
		         contentType:"application/json; charset=utf-8",
			     success: function(data){
			         		if(data=="success")
							{
								loaddata();
								loaddatatoday();
							}
							
			 		 },
				error:function(e){
					          loaddata();
							  loaddatatoday();
					}	 
			 });

		}
    });

	$('#userstable tbody').on( 'click', 'tr td', function (){
        
		var ischecked=$(this).children('.toggle').children('.track').prop("checked");
	
	var id=$(this).children('input:hidden').val();
	    
		if(ischecked)
		{
		  	
		 $.ajax({
		         url:"api/login.php?devicetrack&track=0&id="+id,
		         type:"POST",
		         contentType:"application/json; charset=utf-8",
			     success: function(data){
			         		
							if(data=="success")
							{
								loaddata();
								loaddatatoday();
							}
							
			 		 },
				error:function(e){
					
					}	 
			 });
		}
		else{
		  $.ajax({
		         url:"api/login.php?devicetrack&track=1&id="+id,
		         type:"POST",
		         contentType:"application/json; charset=utf-8",
			     success: function(data){
			         		
							if(data=="success")
							{
								
								loaddata();
								loaddatatoday();
							}
							
			 		 },
				error:function(e){
					
					}	 
			 });

		}
    });
	
});

</script>
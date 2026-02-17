<?php require("connect.php");?>
<?php $title="Show Route"?>
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
              <li class="active"><a href="#today-activity" data-toggle="tab">Route</a></li>
              <li><a href="#tear-tree-view" data-toggle="tab">Add Route</a></li>
              
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
										<h6 class="panel-title txt-dark">Routes</h6>
                                         
                                              
                                             <form>
                                             <div class="form-group-lg">
                                                <div class="form-inline">
       
                                                 <button type="button" id="deleteroute" class="btn btn-danger"><span class="fa fa-remove"></span> Delete Selected Routes</button>
                                                </div>
                                                <div class="progress progress-striped active" id="progressdel" style="display:none;">
                                                   <div class="progress-bar progress-bar-success" style="width: 100%">
                                                   </div>
                                                 </div>
                                              </div>
                                             </form>
                                         
									</div>
                                    <div class="pull-right">
										<h6 class="panel-title txt-dark">Select Distributor</h6>
                                         
                                              
                                             <form>
                                             <div class="form-group-lg">
                                                <div class="form-inline">
                                                <select name="selectdist" id="selectdist" class="form-control select2">
                                                    <option value="">Select Distributor</option>
                                                   <?php 
				                                        $res=mysqli_query($con,"select id,name  from employees where usertype=3");
				                                         while($row=mysqli_fetch_array($res))
					                                     {
						                                   echo"<option value='$row[0]'>$row[1]</option>";
					                                     }
				                                    ?>
                                                 </select>
                                                 <button type="button" id="assigndist" class="btn btn-info"><span class="fa fa-plug"></span> Assign Routes </button>
                                                </div>
                                                <div class="progress progress-striped active" id="progressassign" style="display:none;">
                                                   <div class="progress-bar progress-bar-success" style="width: 100%">
                                                   </div>
                                                 </div>
                                              </div>
                                             </form>
                                         
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

                                                    <th>Id</th>
													<th>RouteName</th>
                                                    <th>Distributor</th>
													<th>CreatedBy</th>
                                                    
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
                
      <section class="content">
      <div class="row">
        <form id="productform" action="api/routeapi.php?add" role="form" method="post" enctype="multipart/form-data">
        <!-- left column -->
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Add Route</h3>
            </div>
            <!-- /.box-header -->
          <div class="box-body">
           <div class="row">
            <div class="col-md-4">
                <!-- form start -->
            
              
                <div class="form-group controls">                
                  <label for="area">Route Name : </label>
                  <input type="text" class="form-control" name="route" id="route" placeholder="Enter Route Name" required="required"/>
                </div>
                
                
                                
             </div> <!-- col 4 close--> 
                    
            </div>
          </div>
         </div>
              <!-- /.box-body -->
               
              <div class="box-footer">
                <div class="progress progress-striped active" id="progress1" style="display:none;">
                   <div class="progress-bar progress-bar-success" style="width: 100%">
                   </div>
                </div>
                <a href="#today-activity" data-toggle="tab" class="btn btn-danger"><span class="fa fa-backward"></span> Back</a>
                <button type="submit"  id="btnaddproduct" class="btn btn-primary pull-right "><span class="fa fa-edit"></span> Add Route</button>
              </div>
            
          </div>
          <!-- /.box -->
         
        </div>
         </form>
            </div>
            </section>    
              <!-- Section Form close-->
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

  <div class="modal fade" id="updatemodal" role="alert">
    <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <a class="close" data-dismiss="modal">&times;</a>
           <h2>Edit Route Name</h2>
         </div>
         <div class="modal-body">
            <div class="box-body">
             <div class="row">
             <form id="updateform" action="api/routeapi.php?add" role="form" method="post" enctype="multipart/form-data">
               <div class="col-md-4">
                <!-- form start -->
                  <div class="form-group controls">   
                    <input type="hidden" id="routeid" name="routeid"/>             
                    <label for="area">Route Name : </label>
                    <input type="text" class="form-control" name="routename" id="routename" placeholder="Enter Route Name" required="required"/>
                  </div>                                
               </div> <!-- col 4 close-->                     
              </div>
              <div class="box-footer">
                <div class="progress progress-striped active" id="progress1" style="display:none;">
                   <div class="progress-bar progress-bar-success" style="width: 100%">
                   </div>
                </div>
                
                <button type="submit"  id="btnaddproduct" class="btn btn-primary pull-right "><span class="fa fa-edit"></span> Update Route</button>
              </div>
              </form>
            </div>
          
          
         </div>
      </div>
    </div>  
  </div>

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


<script>

       $(".select2").select2();
           function edit(id)
	           {		 
			     //var arr=val.split(",");        
		         $("#routeid").val(id);
				 //$("#routename").val(data.route);				 
		         $("#updatemodal").modal();
	           }
   
	function loaddata()
	{	 
          $.ajax({
		  url:"api/routeapi.php?show",
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
						  }},{							  
					   data:{id:'id',route:'route'},render:function(data,type,set){
	                        											   
						  return '<a href="javascript:void(0);" onclick="edit('+data.id+');"><i class="fa fa-edit"></i></a>';
						  }},{data:'route'},{data:'distid',render:function(value){
							     if(value==null || value=='')
								 {return "Not Map"}
								 else{return value;}
							  }},{data:'createdate'}]
		});		   
			  },
		  error:function(e){
			   alert(e.error);
			  }	  
		 });
	}

$(document).ready(function(){
   loaddata();	
   
      $('#deleteroute').click(function(){
	     if(confirm('Do You want to delete Selected Route'))
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
			 var progress=$("#progressdel");
		     progress.fadeIn("slow");
		     $.ajax({
			   url:'api/routeapi?delete',
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
	   
	   $('#assigndist').click(function(){
	     if(confirm('Do You want to Assign Selected Routes '))
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
			 var id=$("#selectdist").val();
			 var progress=$("#progressassign");
		     progress.fadeIn("slow");
		     $.ajax({
			   url:'api/routeapi?assigndist',
			   type:'post',
			   data:{'ids':ids,'id':id},
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
<script src="dist/dist/js/bootstrapValidator.min.js"></script>
<script>


$(document).ready(function() {
	
	
	
	function updateData(){
        var fd = new FormData();
		var progress=$("#progress");
		var id = $('#routeid').val();		
		var route = $('#routename').val();
		fd.append('id',id);
		fd.append('route',route);
		   progress.fadeIn("slow");
        $.ajax({
            url: 'api/routeapi.php?edit',
            type: 'post',
            data: fd,
            success: function(response){
				
                if(response =="success"){
                    alert("Route Update Successfully...");
					progress.fadeOut("slow");
										
		            $('#routename').val('');
		            loaddata();
					$("#updatemodal").modal('hide');;
                }else{
					progress.fadeOut("slow");
                    alert(response);
                }
            },
			error: function(e){
				progress.fadeOut("slow");
				alert(e.error);
				},
				cache: false,
            contentType: false,
            processData: false
        });
    }


	
    $('#updateform')
        .bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                pname: {
                    message: 'The product name can\'t be empty',
                    validators: {
                        notEmpty: {
                            message: 'The product name is required and can\'t be empty'
                        }
                        
                        /*regexp: {
                            regexp: /^[a-zA-Z0-9_\.]+$/,
                            message: 'The username can only consist of alphabetical, number, dot and underscore'
                        }*/
                    }
                },
                pshort: {
                    validators: {
                        notEmpty: {
                            message: 'The product short name can\'t empty'
                          }
                        }
                    },
			
			    route: {
                 validators: {
                     notEmpty: {
                             message: 'The route name is required and can\'t be empty'
                               }                      
                       }
                    }
			  
                }
        }) .on('success.form.bv', function(e) {
            // Prevent form submission
            e.preventDefault();
			  updateData();
	        
        });
	
	
	
	
function sendData(){
        var fd = new FormData();
		var progress=$("#progress1");
		
		
		var route = $('#route').val();
		
		
		fd.append('route',route);
		   progress.fadeIn("slow");
        $.ajax({
            url: 'api/routeapi.php?add',
            type: 'post',
            data: fd,
            success: function(response){
				
                if(response =="success"){
                    alert("Route Add Successfully...");
					progress.fadeOut("slow");
					
					
		            $('#route').val('');
		            loaddata();
                }else{
					progress.fadeOut("slow");
                    alert(response);
                }
            },
			error: function(e){
				progress.fadeOut("slow");
				alert(e.error);
				},
				cache: false,
            contentType: false,
            processData: false
        });
    }


	
    $('#productform')
        .bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                pname: {
                    message: 'The product name can\'t be empty',
                    validators: {
                        notEmpty: {
                            message: 'The product name is required and can\'t be empty'
                        }
                        
                        /*regexp: {
                            regexp: /^[a-zA-Z0-9_\.]+$/,
                            message: 'The username can only consist of alphabetical, number, dot and underscore'
                        }*/
                    }
                },
                pshort: {
                    validators: {
                        notEmpty: {
                            message: 'The product short name can\'t empty'
                          }
                        }
                    },
			
			    route: {
                 validators: {
                     notEmpty: {
                             message: 'The route name is required and can\'t be empty'
                               }                      
                       }
                    }
			  
                }
        }) .on('success.form.bv', function(e) {
            // Prevent form submission
            e.preventDefault();
			  sendData();
	        
        });
});

</script>


<?php require("connect.php");?>
<?php $title="Super Stockist "?>
<?php require("header.php");?>



<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <?php include("content-header.php");?>
<link rel="stylesheet" href="assets/node_modules/datatables/jquery.dataTables.min.css"/>

<link rel="stylesheet" href="dist/dist/css/bootstrapValidator.min.css"/>
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
              <li class="active"><a href="#today-activity" data-toggle="tab">Super Stockist</a></li>
              <li><a href="#tear-tree-view" data-toggle="tab">Add SS</a></li>
              
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
										<h6 class="panel-title txt-dark">Super Stockists</h6>
                                        <form>
                                             <div class="form-group-lg">
                                                <div class="form-inline">
       
                                                 <button type="button" id="deleteroute" class="btn btn-danger"><span class="fa fa-remove"></span> Delete Selected Stockist</button>
                                                </div>
                                                <div class="progress progress-striped active" id="progressdel" style="display:none;">
                                                   <div class="progress-bar progress-bar-success" style="width: 100%">
                                                   </div>
                                                 </div>
                                              </div>
                                         </form>
									</div>
                                    
                                    <div class="pull-left">
                                        <div class="form-inline">
       &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                  <a href="templates/ImportStockist.csv"  class="btn btn-info"><span class="fa fa-download"></span> Download CSV File</a>
                                        </div>
                                    </div>
                                    
                                   <div class="pull-right">										                                         
                                             
                                              <form action="api/stockist-api.php?import" id="import" method="post" enctype="multipart/form-data" >
                                               <div class="form-group-sm">
                                                 <div class="form-inline controls">
                                                   <label>Choose CSV File</label>
                                                   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                   <input type="file" id="file1" class="form-control" name="file1" required/>
                                                   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                   <button type="submit" id="importstockist" class="btn btn-info"><span class="fa fa-upload"></span> Import Data</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                 </div>
                                                 
                                                 <div class="progress progress-striped active" id="progress2" style="display:none;">
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
													<th>Action</th>
													<th>Name</th>
													<th>DID Id</th>
													<th>Email</th>
													<th>Contact</th>
													<th>Address</th>
                                                    <th>Pan No</th>
                                                    <th>GST No</th>
                                                    
                                                    <th>Image</th>
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
        <form id="productform" action="api/addproduct.php?insert" role="form" method="post" enctype="multipart/form-data">
        <!-- left column -->
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Add User</h3>
            </div>
            <!-- /.box-header -->
          <div class="box-body">
           <div class="row">
            <div class="col-md-4">
                <!-- form start -->
            
              
                <div class="form-group controls">
                <input type="hidden" name="id" id="id" value="<?php echo $id;?>" />
                  <label for="empname">Name : </label>
                  <input type="text" class="form-control" name="empname" id="empname" placeholder="Enter Name" value=""  required="required"/>
                </div>
                
                <div class="form-group">
                  <label for="empemail">Email </label>
                  <input type="email" class="form-control" name="empemail" id="empemail" value="" placeholder="Email Address" required/>
                </div>
                <div class="form-group">
                  <label for="empcontact">Contact </label>
                  <input type="" class="form-control" pattern="^\d{10}?$" data-dv-message="Enter Contact No in 10 digits" name="empcontact" id="empcontact" value="" minlength="10" maxlength="10" placeholder="Contact No" required/>
                </div>
                <div class="form-group">
                  <label for="empaddress">Address </label>
				  <textarea class="form-control" name="empaddress" id="empaddress" placeholder="Address"></textarea>
                </div>
             </div> <!-- col 4 close--> 
             
             <div class="col-md-4">
               
                <div class="form-group">
                  <label for="emparea">Select Area</label>
                  <br/>
                  <select  class="form-control select2" style="width:100%;" name="emparea" id="emparea" required>
                   
                   <option value="">Select Area</option>
                    <?php $res=mysqli_query($con,"select * from area"); while($row=mysqli_fetch_array($res)){?>
                    <option value="<?php echo $row['id'];?>"><?php echo "Region : ".$row['region']." Area (".$row['area'].")";?></option>
                    <?php }?>
                  </select>
                  
                </div>
                
                <div class="form-group">
                  <label for="empcity">City</label>
                  <input type="text"  class="form-control" name="empcity" id="empcity"  value="" required="required" />
                   
                </div>
                
                <div class="form-group">
                  <label for="empstate">State</label>
                  <input type="text"  class="form-control" name="empstate" id="empstate"  value=""  required="required"/>
                   
                </div>
               	
                 <div class="form-group">
                  <label for="emppanno">Pan Number:</label>
                  <input type="text" class="form-control pull-right" name="emppanno" id="emppanno" value="" pattern="^[A-Z]{5}\d{4}[A-Z]{1}" pattern-bv-message="Enter Correct Pan No" required="required"/>
                </div>
            
                 
               <div class="form-group">
                  <label for="empgstno">GST Number:</label>
                  <input type="text" class="form-control pull-right" name="empgstno" id="empgstno" pattern="\d{2}[A-Z]{5}\d{4}[A-Z]{1}\d{1}[A-Z]{1}\d{1}" pattern-bv-message="Enter Correct value for Salary" value="" required="required"/>
                </div>
                			
			  
            </div>
            <!-- col 4 Mid -->
            
            <div class="col-md-4">
               
               <div class="form-group">
                  <label for="emppass">Password</label>
                  <input type="password" class="form-control" name="emppass" id="emppass"  placeholder="Password" required/>
                </div>
                <div class="form-group">
                  <label for="empcpass">Confirm Password</label>
                  <input type="password" class="form-control" name="empcpass" id="empcpass"  placeholder="Confirm Password" required/>
                </div>  
               
             <div class="form-group">
                  <label for="emplat">Latitude:</label>
                  <input type="text" class="form-control pull-right" name="emplat" id="emplat" value=""  />
              </div>
             
            <div class="form-group">
                  <label for="emplng" >Longitude:</label>
                  <input type="text" class="form-control pull-right" name="emplng" id="emplng" value="" />
                  <a href="#" data-target="#locationmodel" data-toggle="model">Set Location</a>
            </div>
             
            
                
                <div class="form-group">
                  <label for="pimage">Select Image</label>
                  <input type="file" id="empimage" name="empimage"/>
                </div>
                    
            </div>
          </div>
         </div>
              <!-- /.box-body -->
               
              <div class="box-footer">
                <div class="progress progress-striped active" id="progress" style="display:none;">
                   <div class="progress-bar progress-bar-success" style="width: 100%">
                   </div>
                </div>
                <button type="submit"  id="btnaddproduct" class="btn btn-primary pull-right "><span class="fa fa-plus-circle"></span> Add New User</button>
              </div>
            
          </div>
          <!-- /.box -->
          </form>
        </div>
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

    <script src="assets/node_modules/datatables/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>
    
<script>

    $(".select2").select2();
	function loaddata()
	{
     $.ajax({
		  url:"api/stockist-api.php?show",
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
				  paging:false,
				  buttons: [
                'copy', { extend: 'csv', title: function () { var printTitle = 'All Visitis'; return printTitle; } }, 'excel', 'pdf', { extend: 'print', title: function () { var printTitle = ''; return printTitle; } }
            ],
				  columns:[{
					   data:'id',render:function(value){ 
						  return "<input type='hidden' id='select' value='"+value+"' />";
						  }},{
					   data:'id',render:function(value){
						  return "<a href='editss?editid="+value+"'><span class='fa fa-edit'></span></a>";
						  }},
						  {
							data:'name'
						},
						{data:'empid'},{data:'email'},{data:'contact'},{data:'address'},{data:'panno'},{data:'gstno'},{data:'image',render:function(value){
							return "<img src='imgusers/"+value+"' class='img img-thumbnail' style='width:100px; height:100px;'/>";
							}}
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
   $("#empdoj").datepicker({format:'yyyy-m-dd'});
   
   $('#deleteroute').click(function(){
	     if(confirm('Do You want to delete Selected Stockist'))
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
			   url:'api/stockist-api.php?delete',
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


function sendData(){
       
	   //var form=$(this).parent("form");
	       
	 
        var fd = new FormData();
		var progress=$("#progress");
	//	$.each(files, function(key, value){
     //       fd.append(key, value);
      //  });
		
        var files = $('#empimage')[0].files[0];
		var empname = $('#empname').val();
		
		var empemail = $('#empemail').val();
		var empcontact = $('#empcontact').val();
		var empaddress = $('#empaddress').val();
		var emparea = $('#emparea').val();
		var empcity = $('#empcity').val();
		var empstate = $('#empstate').val();
		var emppanno = $('#emppanno').val();
		var empgstno = $('#empgstno').val();
		var emplat = $('#emplat').val();
		var emplng = $('#emplng').val();
		var emppass = $('#emppass').val();
        fd.append('empimage',files);
		fd.append('empname',empname);
		
		fd.append('empemail',empemail);
		fd.append('empcontact',empcontact);
		fd.append('empaddress',empaddress);
		fd.append('emparea',emparea);
		fd.append('empcity',empcity);
		fd.append('empstate',empgstno);
		fd.append('emppanno',emppanno);
		fd.append('empgstno',empgstno);
		fd.append('emplat',emplat);
		fd.append('emplng',emplng);
		fd.append('emppass',emppass);
           progress.fadeIn("slow");
        $.ajax({
            url: 'api/stockist-api.php?insert',
            type: 'post',
            data: fd,
            success: function(response){
				if(response=="empcode")
				{
				   alert("Employee Code Already Exist!");
				   return;
			    }
				if(response=="empemail")
				{
				   alert("Employee Email Address Already Exist!");
				   return;
			    }
				
				if(response=="empcontact")
				{
				   alert("Employee Contact No Already Exist!");
				   return;
			    }
				
                if(response =="success"){
                    alert("SS Create Successfully...");
					progress.fadeOut("slow");
					$('#empimage')[0].files[0];
		            $('#empname').val('');
		            
		            $('#empemail').val('');
		            $('#empcontact').val('');
		            $('#empaddress').val('');
		            $('#emparea').val('');
		            $('#empcity').val('');
		            $('#empstate').val('');
		            $('#emppanno').val('');
		            $('#empgstno').val('');
		            $('#emplat').val('0.0');
		            $('#emplng').val('0.0');
		            $('#emppass').val('');
					$('#empcpass').val('');
					
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

</script>
<script src="dist/dist/js/bootstrapValidator.min.js"></script>
<script>
$(document).ready(function() {
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
			
			    emppass: {
                 validators: {
                     notEmpty: {
                             message: 'The password is required and can\'t be empty'
                               },
                      identical: {
                         message: 'The password and its confirm are not the same'
                         }
                       }
                    },
			  empcpass: {
                    validators: {
                    notEmpty: {
                        message: 'The confirm password is required and can\'t be empty'
                      },
                    identical: {
                        field: 'emppass',
                        message: 'The password and its confirm are not the same'
                      }
                    }
                  }
			
                }
        }) .on('success.form.bv', function(e) {
            // Prevent form submission
            e.preventDefault();
			  sendData();
	          		
			
            // Get the form instance
          /*  var $form = $(e.target);

            // Get the BootstrapValidator instance
            var bv = $form.data('bootstrapValidator');

            // Use Ajax to submit form data
            $.post($form.attr('action'), $form.serialize(), function(result) {
                console.log(result);
            }, 'json');*/
        });
		
		
		function importData(){
       
	    var fd = new FormData();
		var progress=$("#progress2");	
        var files = $('#file1')[0].files[0];		
		fd.append('file1',files);
           progress.fadeIn("slow");
        $.ajax({
            url: 'api/stockist-api.php?import',
            type: 'post',
            data: fd,
            success: function(response){
				alert(response);
				if(response=="error-type")
				{
				   alert("Please import Xlsx files only");
				   return;
			    }
				if(response=="error")
				{
				   alert("Contact to Admin Intrernal error");
				   return;
			    }
								
                if(response =="success"){
                    alert("Stckist Import Successfully...");
					loaddata();
					$("#importstockist").removeAttr("disabled");
					progress.fadeOut("slow");					
					$('#file1')[0].files[0];
		            
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
		
    $('#import')
        .bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                
			  file1: {
                    validators: {
                    notEmpty: {
                        message: 'Please Select excel File '
                      },
                    file: {
                        extension: 'csv',
						//type:'text/csv,application/csv,application/x-csv',
                        maxSize:1024*1024*2,						
                        message: 'The password and its confirm are not the same'
                      }
                    }
                  }
			
                }
        }) .on('success.form.bv', function(e) {
            // Prevent form submission
            e.preventDefault();
			importData();
			// var $form = $(e.target);
                 
                    // Get the BootstrapValidator instance
                   // var bv = $form.data('bootstrapValidator');
                    //var progress=$("#progress2");
					//progress.fadeIn("slow");
                    // Use Ajax to submit form data
                    
                   /* $.post($form.attr('action'), $form.serialize(), function(result) {
						alert(result);
                        if (result == "success") {
                           loaddata();
						   progress.fadeOut("slow");
                        }
                        
                    }, 'json');*/
	        
        });
});


       $("#emparea").change(function(){
		     var id=$(this).val();
		     $.getJSON('api/registerarea.php?getarea&id='+id).done(function(data){
				 
				 $.each(data,function(i,user){
				     $("#empcity").val(user.region);
					 $("#empstate").val(user.state);	 
					 });
				 });
		});

</script>
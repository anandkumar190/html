<?php require("connect.php");?>
<?php $title="Distributors"?>
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
              <li class="active"><a href="#today-activity" data-toggle="tab">Distributors</a></li>
              <li><a href="#tear-tree-view" data-toggle="tab">Add Distributor</a></li>
              
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
										<h6 class="panel-title txt-dark">Distributors</h6>
                                        <form>
                                             <div class="form-group-lg">
                                                <div class="form-inline">
       
                                                 <button type="button" id="deletearea" class="btn btn-danger" disabled><span class="fa fa-remove"></span> Delete Selected Distributor</button>
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
                                                  <a href="templates/ImportDistributor.csv"  class="btn btn-info"><span class="fa fa-download"></span> Download CSV File</a>
                                        </div>
                                    </div>
                                    
                                   <div class="pull-right">										                                         
                                             
                                              <form action="api/distributor-api.php?import" id="import" method="post" enctype="multipart/form-data" >
                                               <div class="form-group-sm">
                                                 <div class="form-inline controls">
                                                   <label>Choose CSV File</label>
                                                   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                   <input type="file" id="file1" class="form-control" name="file1" required/>
                                                   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                   <button type="submit" id="importdistributor" class="btn btn-info"><span class="fa fa-upload"></span> Import Data</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
													<th>Edit</th>
													<th>Distributor <br> Name</th>
													<th>Contact <br> Persion</th>
													<th>Phone <br> No.</th>
													<th>State</th>
                          <th>City</th>
                          <th>No Of Routes </th>
                          <th>No Of Outlets </th>
                          <th>Distributor <br> Address </th>
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
              <h3 class="box-title">Add distributor</h3>
            </div>
            <!-- /.box-header -->
          <div class="box-body">
           <div class="row">
            <div class="col-md-4">
                <!-- form start -->
            
              
                <div class="form-group controls">
                <input type="hidden" name="id" id="id" value="<?php echo $id;?>" />
                  <label for="empname">Distributor Name : </label>
                  <input type="text" class="form-control" name="empname" id="empname" placeholder="Enter Distributor Name" value=""  required="required"/>
                </div>

                <div class="form-group controls">
                <input type="hidden" name="id" id="id" value="<?php echo $id;?>" />
                  <label for="empname">Distributor Short Name: </label>
                  <input type="text" class="form-control" name="empsname" id="empsname" placeholder="Enter Distributor Short Name" value=""  required="required"/>
                </div>
                
                <div class="form-group">
                  <label for="empemail">Email </label>
                  <input type="email" class="form-control" name="empemail" id="empemail" value="" placeholder="Email Address" required/>
                </div>

                <div class="form-group controls">
                <input type="hidden" name="id" id="id" value="<?php echo $id;?>" />
                  <label for="empname">Contact Person: </label>
                  <input type="text" class="form-control" name="empcname" id="empcname" placeholder="Enter Contact Person" value=""  required="required"/>
                </div>

                <div class="form-group">
                  <label for="empcontact">Contact </label>
                  <input type="" class="form-control" pattern="^\d{10}?$" data-dv-message="Enter Contact No in 10 digits" name="empcontact" id="empcontact" value="" minlength="10" maxlength="10" placeholder="Contact No" required/>
                </div>
                <div class="form-group">
                  <label for="empaddress">Distributor Address </label>
				     <textarea class="form-control" name="empaddress" id="empaddress" placeholder="Distributor Address"></textarea>
                </div>
             </div> <!-- col 4 close--> 
             
             <div class="col-md-4">

              <div class="form-group">
                <label for="empdesignation">Select State</label>
                <select  class="form-control" name="empstate" id="empstate" required>
                 <option value="">Select State</option>
                  <?php $res=mysqli_query($con,"select id,name from states order by name "); while($row=mysqli_fetch_array($res)){?>
                  <option value="<?php echo $row['id'];?>"><?php echo $row['name'];?></option>
                  <?php }?>
                </select>
              </div>

              <div class="form-group">
                <label for="empmanager">Select City</label>
                <select class="form-control" name="empcity" id="empcity" required>
                <option value="">Select city </option>
                </select>
              </div>
               	<br>
               	<br>
               	<br>
                 <div style="align:center">  <center><a href="#locationmodel" data-target="#locationmodel" data-toggle="modal" style="font-size: x-large;" >Set Location</a>   </center> </div> 
              <div class="form-group">
                  <label for="emplat">Latitude:</label>
                  <input type="text" class="form-control pull-right" name="emplat" id="emplat" value=""/>
              </div>
             
            <div class="form-group">
                  <label for="emplng" >Longitude:</label>
                  <input type="text" class="form-control pull-right" name="emplng" id="emplng" value="" />
            </div>			
			  
            <br>
            <br>
            <br>
            <br>
            <button type="submit"  id="btnaddproduct" class="btn btn-primary pull-right "><span class="fa fa-plus-circle"></span> Add New User</button>
            </div>
            <!-- col 4 Mid -->
            
            <div class="col-md-4">
               
    

             
            
      <!--           
                <div class="form-group">
                  <label for="pimage">Select Image</label>
                  <input type="file" id="empimage" name="empimage"/>
                </div> -->
                    
            </div>
          </div>
         </div>
              <!-- /.box-body -->
               
              <div class="box-footer">
                <div class="progress progress-striped active" id="progress" style="display:none;">
                   <div class="progress-bar progress-bar-success" style="width: 100%">
                   </div>
                </div>
          
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
		  url:"api/distributor-api.php?show",
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
				  buttons: [
                'copy', { extend: 'csv', title: function () { var printTitle = 'All Visitis'; return printTitle; } }, 'excel', 'pdf', { extend: 'print', title: function () { var printTitle = ''; return printTitle; } }
            ],
				  columns:[{
					   data:'id',render:function(value){ 
						  return "<input type='hidden' id='select' value='"+value+"' />";
						  }},{
					   data:'id',render:function(value){
						  return "<a href='editdd?editid="+value+"'><span class='fa fa-edit'></span></a>";
						  }},
						  {
							data:'name'},
						{data:'contactperson'},
            {data:'contact'},
            {data:'state'},
            {data:'city'},
            {data:'no_of_routes'},
            {data:'no_of_outlets'},
            {data:'address'}
            
            // {data:'image',render:function(value){
						// 	return "<img src='imgusers/"+value+"' class='img img-thumbnail' style='width:100px; height:100px;'/>";
						// 	}
            // }
              //{data:'ssname'},{data:'empid'}
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
   
   $("#userstable").on('click', 'tr', function () {
    
      var row = $(this); // Get the clicked row
      var cellValue = row.find('td:nth-child(9)').text().trim(); // Get the 8th column value (adjust index if needed)

      console.log("Value from the <td>: ", cellValue);
      if (cellValue<1) {
        $('#deletearea').prop('disabled', false);
      }else{
        $('#deletearea').prop('disabled', true);
      }
  });

      $('#deletearea').click(function(){
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
			   url:'api/distributor-api.php?delete',
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
		
      
		var empname = $('#empname').val();
		var empemail = $('#empemail').val();
		var empcontact = $('#empcontact').val();
		var empaddress = $('#empaddress').val();
		
		var empcity = $('#empcity').val();
		var empstate = $('#empstate').val();

		var emplat = $('#emplat').val();
		var emplng = $('#emplng').val();

    var empsortname=$('#empsname').val();
    var empcontactname=$('#empcname').val();
	
		fd.append('empname',empname);
		
		fd.append('empemail',empemail);
		fd.append('empcontact',empcontact);
		fd.append('empaddress',empaddress);
	
		fd.append('empcity',empcity);
		fd.append('empstate',empstate);

		fd.append('emplat',emplat);
    fd.append('emplng',emplng);

		fd.append('empsortname',empsortname);
		fd.append('empcontactname',empcontactname);
		
           progress.fadeIn("slow");
        $.ajax({
            url: 'api/distributor-api.php?insert',
            type: 'post',
            data: fd,
            success: function(response){
				if(response=="empcode")
				{
				   alert("Distributor Code Already Exist!");
				   return;
			    }
				if(response=="empemail")
				{
				   alert("Distributor Email Address Already Exist!");
				   return;
			    }
				
				if(response=="empcontact")
				{
				   alert("Distributor Contact No Already Exist!");
				   return;
			    }
				
                if(response =="success"){
                    alert("Distributor Create Successfully...");
					progress.fadeOut("slow");
				
		            $('#empname').val('');
		            $('#empemail').val('');
		            $('#empcontact').val('');
		            $('#empaddress').val(''); 
		            $('#empcity').val('');
		            $('#empstate').val('');
		            $('#emplat').val('0.0');
		            $('#emplng').val('0.0');
                $('#empcname').val('');
                $('#empsname').val('');
					
					
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


        $("#empstate").change(function(){
            var stateid= $(this).val();
          $.get("api/regionapi.php?cities",{s_id:stateid},function(data, status){
        var selectbox = $('#empcity');
           selectbox.empty();
             var list = '<option value="">Select city </option>';
            $.each(JSON.parse(data), function(key,value) { 
             list += "<option value='" +value.id+ "'>" +value.city+ "</option>";
             });
             
           selectbox.html(list);
        
         });
      });


        $("#empcity").change(function(){
            var cityid= $(this).val();
          $.get("api/areaapi.php?areas",{c_id:cityid},function(data, status){
        var selectbox = $('#emparea');
           selectbox.empty();
             var list = '<option value="">Select area </option>';
            $.each(JSON.parse(data), function(key,value) { 
             list += "<option value='" +value.id+ "'>"+'resion:'+value.resion+'Area('+value.area+')'+"</option>";
             });

           selectbox.html(list);
        
         });
      });






});

function importData(){
       
	    var fd = new FormData();
		var progress=$("#progress2");	
        var files = $('#file1')[0].files[0];		
		fd.append('file1',files);
           progress.fadeIn("slow");
        $.ajax({
            url: 'api/distributor-api.php?import',
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
                    alert("Distributors Import Successfully...");
					loaddata();
					$("#importdistributor").removeAttr("disabled");
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

/* $("#emparea").change(function(){
		     var id=$(this).val();
		     $.getJSON('api/registerarea.php?getarea&id='+id).done(function(data){
				 $.each(data,function(i,user){
				     $("#empcity").val(user.region);
					 $("#empstate").val(user.state);	 
					 });
				 });
		});*/



</script>
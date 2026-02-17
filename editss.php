<?php require("connect.php");?>
<?php $title="Edit Stockist"?>
<?php require("header.php");?>

<?php 
        $id='';
		$name='';
		$empid='';
		$email='';
		$contact='';
		$address='';
		$emparea='';
		$empcity='';
		$empstate='';
		$panno='';
		$image='';
		$gstno='';
		$password='';
		$lat='';
		$lng='';	
  if(isset($_GET['editid']))
  {
	  extract($_GET);
	$res=mysqli_query($con,"select * from employees where id='$editid'");
	if($row=mysqli_fetch_array($res))
	{
	
		$id=$row['id'];
		$name=$row['name'];
		$empid=$row['empid'];
		$email=$row['email'];
		$contact=$row['contact'];
		$address=$row['address'];
		$emparea=$row['areaid'];
		$empcity=$row['city'];
		$empstate=$row['state'];
		$panno=$row['battery'];
		$gstno=$row['region'];
		$image=$row['image'];
		$lat=$row['latitude'];
		$lng=$row['longitude'];
		$password=$row['password'];
	}
  }
?>

<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <?php include("content-header.php");?>
<link rel="stylesheet" href="dist/jquery.dataTables.min.css"/>
<link rel="stylesheet" href="dist/dataTables.bootstrap.min.css"/>
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
              
              <li class="active"><a href="#tear-tree-view" data-toggle="tab">Edit S. Stockist <?php echo $name;?></a></li>
              
            </ul>
            
            <div class="tab-content no-padding">
              
              
              <!--Today Activity tab Start-->
              
              
              <!--TreeView tab Start-->
    <div class="chart tab-pane active" id="tear-tree-view" style="position: relative; min-height: 300px;">
                
      <section class="content">
      <div class="row">
        <form id="productform" action="api/addproduct.php?insert" role="form" method="post" enctype="multipart/form-data">
        <!-- left column -->
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Edit Stockist</h3>
            </div>
            <!-- /.box-header -->
          <div class="box-body">
           <div class="row">
            <div class="col-md-4">
                <!-- form start -->
            
              
                <div class="form-group controls">
                <input type="hidden" name="id" id="id" value="<?php echo $id;?>" />
                  <label for="empname">Name : </label>
                  <input type="text" class="form-control" name="empname" id="empname" placeholder="Enter Name" value="<?php echo $name;?>"  required="required"/>
                </div>
                
                <div class="form-group">
                  <label for="empemail">Email </label>
                  <input type="email" class="form-control" name="empemail" id="empemail" value="<?php echo $email;?>" placeholder="Email Address" required/>
                </div>
                <div class="form-group">
                  <label for="empcontact">Contact </label>
                  <input type="" class="form-control" pattern="^\d{10}?$" data-dv-message="Enter Contact No in 10 digits" name="empcontact" id="empcontact" value="<?php echo $contact;?>" minlength="10" maxlength="10" placeholder="Contact No" required/>
                </div>
                <div class="form-group">
                  <label for="empaddress">Address </label>
				  <textarea class="form-control" name="empaddress" id="empaddress" placeholder="Address"><?php echo $address;?></textarea>
                </div>
             </div> <!-- col 4 close--> 
             
             <div class="col-md-4">
               
                <div class="form-group">
                  <label for="emparea">Select Area</label>
                  <select  class="form-control select2" name="emparea" id="emparea" required>
                   
                   <option value="">Select Area</option>
                    <?php $res=mysqli_query($con,"select * from area order by region"); while($row=mysqli_fetch_array($res)){?>
                    <option value="<?php echo $row['id'];?>"><?php echo $row['region']." Area (".$row['area'].")";?></option>
                    <?php }?>
                  </select>
                  <script>
                    var emparea=document.getElementById("emparea");
					emparea.value="<?php echo $emparea;?>";
					
                  </script>
                </div>
                
                <div class="form-group">
                  <label for="empcity">City</label>
                  <input type="text"  class="form-control" name="empcity" id="empcity"  value="<?php echo $empcity;?>" required="required" />
                   
                </div>
                
                <div class="form-group">
                  <label for="empstate">State</label>
                  <input type="text"  class="form-control" name="empstate" id="empstate"  value="<?php echo $empstate;?>"  required="required"/>
                   
                </div>
               				
			  
            </div>
            <!-- col 4 Mid -->
            
            <div class="col-md-4">
                 
                <div class="form-group">
                  <label for="emppanno">Pan Number:</label>
                  <input type="text" class="form-control pull-right" name="emppanno" id="emppanno" value="<?php echo $panno;?>" pattern="^[A-Z]{5}\d{4}[A-Z]{1}" pattern-bv-message="Enter Correct Pan No" required="required"/>
                </div>
            
                 
               <div class="form-group">
                  <label for="empgstno">GST Number:</label>
                  <input type="text" class="form-control pull-right" name="empgstno" id="empgstno" pattern="\d{2}[A-Z]{5}\d{4}[A-Z]{1}\d{1}[A-Z]{1}\d{1}" pattern-bv-message="Enter Correct value for Salary" value="<?php echo $gstno;?>" required="required"/>
                </div>
             <div class="form-group">
                  <label for="emplat">Latitude:</label>
                  <input type="text" class="form-control pull-right" name="emplat" id="emplat" value="<?php echo $lat;?>" />
              </div>
             
            <div class="form-group">
                  <label for="emplng" >Longitude:</label>
                  <input type="text" class="form-control pull-right" name="emplng" id="emplng" value="<?php echo $lng;?>" />
                  <a href="#locationmodel" data-target="#locationmodel" data-toggle="modal">Set Location</a>
            </div>
             
            
                
                <div class="form-group">
                  <label for="pimage">Select Image</label>
                  <input type="file" id="empimage" name="empimage"/><img src="imgusers/<?php echo $image;?>" style="height:200px; width:180px;" class="img img-thumbnail" />
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
                <a href="stockist" class="btn btn-danger"><span class="fa fa-remove"></span> Cancel</a>
                <button type="submit"  id="btnaddproduct" class="btn btn-primary pull-right "><span class="fa fa-edit"></span> Update Details</button>
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

<script src="bower_components/datatables.net/js/jquery.dataTables.min.js">
</script>
<script src="dist/dist/js/bootstrapValidator.min.js"></script>
<script>
$(document).ready(function(){
    $(".select2").select2();
    $("#empdoj").datepicker({format:'yyyy-m-dd',autoclose:true});
	$("#empdol").datepicker({format:'yyyy-m-dd',autoclose:true});
    $(".table").dataTable({sort:false});
	$("#emparea").change(function(){
		     var id=$(this).val();
		     $.getJSON('api/registerarea.php?getarea&id='+id).done(function(data){
				 
				 $.each(data,function(i,user){
				     $("#empcity").val(user.region);
					 $("#empstate").val(user.state);	 
					 });
				 });
		});
});

function sendData(){
       
	 
        var fd = new FormData();
		var progress=$("#progress");
	//	$.each(files, function(key, value){
     //       fd.append(key, value);
      //  });
		
        var files = $('#empimage')[0].files[0];
		var id = $('#id').val();
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
		
        fd.append('empimage',files);
		fd.append('id',id);
		fd.append('empname',empname);
		
		fd.append('empemail',empemail);
		fd.append('empcontact',empcontact);
		fd.append('empaddress',empaddress);
		fd.append('emparea',emparea);
		fd.append('empcity',empcity);
		fd.append('empstate',empstate);
		fd.append('emplat',emplat);
		fd.append('emplng',emplng);
		fd.append('emppanno',emppanno);
		fd.append('empgstno',empgstno);
		//fd.append('empdol',empdol);
           progress.fadeIn("slow");
        $.ajax({
            url: 'api/stockist-api.php?edit',
            type: 'post',
            data: fd,
            success: function(response){
				if(response=="empcode")
				{
				   alert("SS Code Already Exist!");
				   return;
			    }
				if(response=="empemail")
				{
				   alert("SS Email Address Already Exist!");
				   return;
			    }
				
				if(response=="empcontact")
				{
				   alert("SS Contact No Already Exist!");
				   return;
			    }
				
                if(response =="success"){
                    alert("SS Details Update Successfully...");
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
		            
					
					window.location="stockist";
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
});

</script>
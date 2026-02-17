<?php require("connect.php");?>
<?php $title="Edit Users"?>
<?php require("header.php");?>

<?php 
        $id='';
		$name='';
		$empid='';
		$email='';
		$contact='';
		$address='';
		$designationid='';
		$roleid='';
		$managerid='';
		$reportsto='';
		$image='';
		$doj='';
		$dol='';
		$salary='';
		$commission='';
		$password='';
		
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
		$designationid=$row['designationid'];
		$roleid=$row['roleid'];
		$managerid=$row['managerid'];
		$reportsto=$row['reportsto'];
		$image=$row['image'];
		$doj=$row['doj'];
		$dol=$row['dol'];
		$salary=$row['salary'];
		$commission=$row['commission'];
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
              
              <li class="active"><a href="#tear-tree-view" data-toggle="tab">Edit User <?php echo $_SESSION['empname'];?></a></li>
              
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
              <h3 class="box-title">Edit User</h3>
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
                  <label for="empcode">Employee Code (EMP0001)</label>
                  <input type="text" class="form-control" name="empcode" id="empcode" value="<?php echo $empid;?>"  placeholder="Employee Code" required/>
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
                <a href="users" class="btn btn-danger"><span class="fa fa-remove"></span> Cancel</a>
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
     //var files;
	 //$('input[type=file]').on('change', prepareUpload);
      //   function prepareUpload(event){
      //    files = event.target.files;
      //  };
    $("#empdoj").datepicker({format:'yyyy-m-dd',autoclose:true});
	$("#empdol").datepicker({format:'yyyy-m-dd',autoclose:true});
    $(".table").dataTable({sort:false});
});

function sendData(){
       
	   //var form=$(this).parent("form");
	       
	 
        var fd = new FormData();
		var progress=$("#progress");
	//	$.each(files, function(key, value){
     //       fd.append(key, value);
      //  });
		
        var files = $('#empimage')[0].files[0];
		var id = $('#id').val();
		var empname = $('#empname').val();
		var empcode = $('#empcode').val();
		var empemail = $('#empemail').val();
		var empcontact = $('#empcontact').val();
		var empaddress = $('#empaddress').val();
		var empdesignation = $('#empdesignation').val();
		var emprole = $('#emprole').val();
		var empmanager = $('#empmanager').val();
		var empreportto = $('#empreportto').val();
		var empdoj = $('#empdoj').val();
		var empsalary = $('#empsalary').val();
		var empcomm = $('#empcomm').val();
		var empdol = $('#empdol').val();
    var emppass = $('#emppass').val();
        fd.append('empimage',files);
		fd.append('id',id);
		fd.append('empname',empname);
		fd.append('empcode',empcode);
		fd.append('empemail',empemail);
		fd.append('empcontact',empcontact);
		fd.append('empaddress',empaddress);
		fd.append('empdesignation',empdesignation);
		fd.append('emprole',emprole);
		fd.append('empmanager',empmanager);
		fd.append('empreportto',empreportto);
		fd.append('empdoj',empdoj);
		fd.append('empsalary',empsalary);
		fd.append('empcomm',empcomm);
		fd.append('empdol',empdol);
    fd.append('emppass',emppass);
           progress.fadeIn("slow");
        $.ajax({
            url: 'api/adduser.php?edit',
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
                    alert("User Details Update Successfully...");
					progress.fadeOut("slow");
					
					$('#empimage')[0].files[0];
		            $('#empname').val('');
		            $('#empcode').val('');
		            $('#empemail').val('');
		            $('#empcontact').val('');
		            $('#empaddress').val('');
		            $('#empdesignation').val('');
		            $('#emprole').val('');
		            $('#empmanager').val('');
		            $('#empreportto').val('');
		            $('#empdoj').val('');
		            $('#empsalary').val('0.0');
		            $('#empcomm').val('0.0');
		            $('#empdol').val('');
                $('#emppass').val('');
					      $('#empcpass').val('');
					
					window.location="users";
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
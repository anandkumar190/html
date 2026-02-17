<?php require("connect.php");?>
<?php $title="Edit region"?>
<?php require("header.php");?>

<?php 
    
    $id='';
		$name='';
		$stateid='';
		
  if(isset($_GET['editid']))
  {
	  extract($_GET);
	$res=mysqli_query($con,"select * from cities where id='$editid'");
  	if($row=mysqli_fetch_array($res))
	  {
	
		$id=$row['id'];
		$name=$row['city'];
		$stateid=$row['state_id'];
	

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
              
              <li class="active"><a href="#tear-tree-view" data-toggle="tab">Edit City</a></li>
              
            </ul>
            
            <div class="tab-content no-padding">
              
              
              <!--Today Activity tab Start-->
              
              
              <!--TreeView tab Start-->
    <div class="chart tab-pane active" id="tear-tree-view" style="position: relative; min-height: 300px;">
                
      <section class="content">
      <div class="row">
        <form id="productform" action="#" role="form" method="post" enctype="multipart/form-data">
        <!-- left column -->
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Edit City</h3>
            </div>
            <!-- /.box-header -->
          <div class="box-body">
           <div class="row">
               <div class="col-md-4">
               
                <div class="form-group">
                  <label for="empdesignation">Select State</label>
                  <select  class="form-control" name="state_id" id="state_id" required>
                   <option value="">Select State</option>
                    <?php $res=mysqli_query($con,"select id,name from states order by name "); while($row=mysqli_fetch_array($res)){?>
                    <option value="<?php echo $row['id'];?>"><?php echo $row['name'];?></option>
                    <?php }?>
                  </select>
                </div>
        
                              <script>
                             var state=document.getElementById("state_id");
                              state.value="<?php echo $stateid;?>";
                      
                              </script>
            </div>

               
               <div class="col-md-4">
                <!-- form start -->
            
                       <input type="hidden" name="id" id="id" value="<?php echo $id;?>" />
                <div class="form-group controls">
                  <label for="empname">City : </label>
                  <input type="text" class="form-control" name="name"  value="<?php echo $name;?>" id="name" placeholder="Enter Name"  required="required"/>
                </div>
               
      
             </div> <!-- col 4 close--> 
             
             



            
          </div>
         </div>
              <!-- /.box-body -->
               
              <div class="box-footer">
                <div class="progress progress-striped active" id="progress" style="display:none;">
                   <div class="progress-bar progress-bar-success" style="width: 100%">
                   </div>
                </div>
                <a href="city" class="btn btn-danger"><span class="fa fa-remove"></span> Back</a>
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



});

function sendData(){
       
         //var form=$(this).parent("form");
          //var form=$("#productform");
             var fd =new FormData();
        var progress=$("#progress");
          //  $.each(files, function(key, value){
            //       fd.append(key, value);
            //  });
        
        var id = $('#id').val();
        var name = $('#name').val();
        var state_id = $('#state_id').val();


        
     	fd.append('id',id);
        fd.append('name',name);
        fd.append('state_id',state_id);

        
             progress.fadeIn("slow");
             $.ajax({
                 url: 'api/cities.php?edit',
                 type: 'post',
                 data: fd,
                 success: function(response){
            if(response=="name")
            {
               alert("city  Already Exist!");
               return;
              }
      
                      if(response =="success"){
                         alert("City Create Successfully...");
              progress.fadeOut("slow");
        
                    $('#name').val('');
                    $('#state_id').val('');
        
              
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
<?php require("connect.php");?>
<?php $title="Edit Route"?>
<?php require("header.php");?>

<?php 
        $id='';
		$area='';
		$empid='';
		$region='';
		$state='';
    $city='';
		$country='';
		$distributor='';
		$km='';
		$lat='';
		$lng='';	
  if(isset($_GET['id']))
  {
	  extract($_GET);
	$res=mysqli_query($con,"select * from area where id='$id'");
	if($row=mysqli_fetch_array($res))
	{

		$id=$row['id'];
		$area=$row['area'];
		$region=$row['region'];
		$state=$row['state'];
    $city=$row['city'];
		$country=$row['country'];
		$distributor=$row['distributor_id'];
		$km=$row['km'];
		$lat=$row['latitude'];
		$lng=$row['longitude'];
    
		
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
              
              <li class="active"><a href="#tear-tree-view" data-toggle="tab">Edit Route <?php echo $area;?></a></li>
              
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
                  <h3 class="box-title">Edit Route</h3>
                </div>
            <!-- /.box-header -->
          <div class="box-body">
              <div class="row">

                  <div class="col-md-4">               
                    <div class="form-group">
                      <input type="hidden" name="id" id="id" value="<?php echo $id;?>" />
                      <label for="state">State</label>
                      <select  class="form-control"  name="state" id="state"  >
                        <option value="">Select State</option>
                        <?php $res=mysqli_query($con,"select id,name from states order by name"); while($row=mysqli_fetch_array($res)){$sselect=$row['id']==$state ? 'selected' :'';?>
                        <option value="<?php echo $row['id'];?>" <?php echo $sselect;?> ><?php echo $row['name'];?> </option>
                        <?php }?>
                      </select>     
                    </div> 
                  </div>
                  <div class="col-md-4"> 
                      <div class="form-group">
                                <label for="city">City</label>  
                              <select class="form-control"name="city" id="city"  >
                              <option value="">Select city </option>
                              <?php $res=mysqli_query($con,"select id,city from cities where state_id='$state' order by city "); while($row=mysqli_fetch_array($res)){ $cselect=$row['id']==$city ? 'selected' :'';?>
                              <option value="<?php echo $row['id'];?>" <?php echo $cselect;?> ><?php echo $row['city'];?></option>
                              <?php }?>
                          
                            </select>
                          </div>
                  </div>

                    <div class="col-md-4"> 
                          <div class="form-group">
                            <label for="region">Region </label>
                                <select class="form-control" name="region" id="region"  >
                                  <option value="">Select Region </option>
                                  <?php $res=mysqli_query($con,"select id,name from regions where city_id='$city' order by name"); while($row=mysqli_fetch_array($res)){$rselect=$row['id']==$region ? 'selected':'';?>
                                  <option value="<?php echo $row['id'];?>" <?php echo $rselect; ?>><?php echo $row['name'];?></option>
                                  <?php }?>
                                </select>
                          </div>
                    </div>
              </div>
              <div class="row">
                  <div class="col-md-4"> 
                    <div class="form-group controls">
                      <label for="area">Route Name : </label>
                      <input type="text" class="form-control" name="area" id="area" value="<?php echo $area;?>" placeholder="Enter Route Name" required="required"/>
                    </div>
                 </div>

                 <div class="col-md-4"> 
                  <div class="form-group">
                  <label for="area">Distributor : </label>
                  <select  class="form-control"  name="distributor" id="distributor" required>
                      <option value="">Select distributor</option>
                        <?php $res=mysqli_query($con,"select e.id, e.name from employees e where usertype=3 order by  e.name  "); while($row=mysqli_fetch_array($res)){  $sdistributor=$row['id']==$distributor ? 'selected' :''; ?>
                        <option value="<?php echo $row['id'];?>"  <?php echo $sdistributor; ?> ><?php echo $row['name'];?></option>
                        <?php } ?>
                    </select>


                    <!-- <label for="km">KM </label>
                    <input type="" class="form-control"  data-dv-message="Enter KM" name="km" id="km" value="<?php echo $km;?>"  placeholder="Enter KM" required/> -->
                  </div>
               </div>

                <div class="col-md-4">    
                </div>

              </div>

              <div class="row">
                <div style="align:center">  <center><a href="#locationmodel" data-target="#locationmodel" data-toggle="modal" style="font-size: x-large;" >Set Location</a>   </center> </div> 
                  <div class="col-md-6">
                    <div class="form-group">
                          <label for="lat">Latitude:</label>
                          <input type="text" class="form-control pull-right" name="emplat" id="emplat" value="<?php echo $lat;?>"  >
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                          <label for="lng" >Longitude:</label>
                          <input type="text" class="form-control pull-right" name="emplng" id="emplng" value="<?php echo $lng;?>"  >
                          <!-- <a href="#locationmodel" data-target="#locationmodel" data-toggle="modal">Set Location</a> -->
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
                <a href="showarea" class="btn btn-danger"><span class="fa fa-backward"></span> Back</a>
                <button type="submit"  id="btnaddproduct" class="btn btn-primary pull-right "><span class="fa fa-edit"></span> Update Area</button>
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


function sendData(){
        var fd = new FormData();
		var progress=$("#progress");
		
		var id = $('#id').val();
		var area = $('#area').val();
		
		var region = $('#region').val();
		var state = $('#state').val();
    var city = $('#city').val();
    var distributor = $('#distributor').val();
		var country = "INDIA";
		var km = $('#km').val();
		var lat = $('#emplat').val();
		var lng = $('#emplng').val();
        fd.append('id',id);
		fd.append('area',area);
		fd.append('region',region);
		fd.append('state',state);
    fd.append('city',city);
    fd.append('distributor',distributor);
		fd.append('country',country);
		fd.append('km',km);
		fd.append('lat',lat);
		fd.append('lng',lng);

    
		//fd.append('empdol',empdol);
           progress.fadeIn("slow");
        $.ajax({
            url: 'api/areaapi.php?edit',
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
                    alert("Route Details Update Successfully...");
					progress.fadeOut("slow");
					
					
		            $('#area').val('');
		            
		            $('#region').val('');
		            $('#state').val('');
		            $('#country').val('');
		            $('#lat').val('');
		            $('#lng').val('');
		            $('#km').val('');
		            
					window.location="showarea";
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

       
      $("#state").change(function(){
          var stateid= $(this).val();
        $.get("api/regionapi.php?cities",{s_id:stateid},function(data, status){
         var selectbox = $('#city');
         selectbox.empty();
           var list = '<option value="">Select city </option>';
          $.each(JSON.parse(data), function(key,value) { 
           list += "<option value='" +value.id+ "'>" +value.city+ "</option>";
           });
           
         selectbox.html(list);
     
       });
        });
        
        
             
      $("#city").change(function(){
          var stateid= $(this).val();
        $.get("api/regionapi.php?regions",{s_id:stateid},function(data, status){
         var selectbox = $('#region');
         selectbox.empty();
           var list = '<option value="">Select Region </option>';
          $.each(JSON.parse(data), function(key,value) { 
           list += "<option value='" +value.id+ "'>" +value.name+ "</option>";
           });
           
         selectbox.html(list);
     
       });
        });
       





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
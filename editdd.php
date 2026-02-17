<?php require("connect.php");?>
<?php $title="Edit Distributor"?>
<?php require("header.php");?>

<?php 
        $id='';
		$name='';
		$empid='';
		$email='';
		$contact='';
		$sortName=$contactParson='';
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
		$stockistid='';	
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
        $contactParson=$row['contactperson'];
        $sortName=$row['sortname'];
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
        $stockistid=$row['stockistid'];
        
      }
  }


  $result=mysqli_query($con," SELECT  a.area AS areaName,
                                COUNT(DISTINCT o.id) AS total_outlet_count,
                                lv.lastvisit,
                                COALESCE(SUM(b.total_amount), 0) AS total_amount_value
                            FROM area a

                            LEFT JOIN outlets o 
                                ON o.routeid = a.id

                            LEFT JOIN (
                                SELECT 
                                    routeid,
                                    MAX(lastvisit) AS lastvisit
                                FROM outlets
                                GROUP BY routeid
                            ) lv 
                                ON lv.routeid = a.id

                            LEFT JOIN (
                                SELECT 
                                    outlet_id,
                                    DATE(booking_time) AS booking_date,
                                    SUM(total_amount) AS total_amount
                                FROM booking
                                GROUP BY outlet_id, DATE(booking_time)
                            ) b 
                                ON b.outlet_id = o.id
                              AND b.booking_date = DATE(lv.lastvisit)

                            WHERE 
                                a.distributor_id = $editid

                            GROUP BY 
                                a.id,
                                a.area,
                                lv.lastvisit

                            ORDER BY 
                                a.area;
                            ");
                                


  
    $arrayRoute=[];
      $totalOutlets=0;
      $i=0;
    while($outlets=mysqli_fetch_array($result))
    {

    // print_r(['outlets'=>$outlets,'i'=>$i]);
    // echo"<br><br><br><br>";
    $arrayRoute[]=['name'=>$outlets['areaName'],
    'total_amount_value'=>$outlets['total_amount_value'],   
    'total_outlet_count'=>$outlets['total_outlet_count'],
    'lastvisit'=> !empty($outlets['lastvisit'])  ? date("d/m/Y", strtotime($outlets['lastvisit'])) : 'No Visit'];
    

    $totalOutlets+=$outlets['total_outlet_count'];
  
    $i++;

    }
//       echo"<br><br>arrayRoute<br><br>";
//        print_r(['arrayRoute'=>$arrayRoute,]);
//     echo"<br><br>arrayRoute<br><br>";
// die;

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
              <h3 class="box-title">Edit Distributor</h3>
            </div>
            <!-- /.box-header -->
          <div class="box-body">
           <div class="row">
            <div class="col-md-3">
                <!-- form start -->
            
              
                <div class="form-group controls">
                <input type="hidden" name="id" id="id" value="<?php echo $id;?>" />
                  <label for="empname">Distributor Name : </label>
                  <input type="text" class="form-control" name="empname" id="empname" placeholder="Enter Distributor Name" value="<?php echo $name;?>"  required="required"/>
                </div>

                  
                <div class="form-group controls">
                <input type="hidden" name="id" id="id" value="<?php echo $id;?>" />
                  <label for="empname">Distributor Sort Name : </label>
                  <input type="text" class="form-control" name="empsname" id="empsname" placeholder="Enter Distributor Sort Name" value="<?php echo $sortName;?>"  required="required"/>
                </div>
                
                <div class="form-group">
                  <label for="empemail">Email </label>
                  <input type="email" class="form-control" name="empemail" id="empemail" value="<?php echo $email;?>" placeholder="Email Address" required/>
                </div>
                <div class="form-group">
                  <label for="empcontact">Contact </label>
                  <input type="" class="form-control" pattern="^\d{10}?$" data-dv-message="Enter Contact No in 10 digits" name="empcontact" id="empcontact" value="<?php echo $contact;?>" minlength="10" maxlength="10" placeholder="Contact No" required/>
                </div>

                <div class="form-group controls">
                <input type="hidden" name="id" id="id" value="<?php echo $id;?>" />
                  <label for="empname">Contact Person: </label>
                  <input type="text" class="form-control" name="empcname" id="empcname" placeholder="Enter Contact Person" value="<?php echo $contactParson; ?>"  required="required"/>
                </div>
                <div class="form-group">
                  <label for="empaddress">Distributor Address </label>
				  <textarea class="form-control" name="empaddress" id="empaddress" placeholder=" Distributor Address"><?php echo $address;?></textarea>
                </div>
             </div> <!-- col 4 close--> 
             
             <div class="col-md-3">
               
                <div class="form-group">
                  <label for="empstate">State</label>
                  <select  class="form-control" name="empstate" id="empstate" required>
                        <option value="">Select State</option>
                        <?php $res=mysqli_query($con,"select id,name from states order by name "); while($row=mysqli_fetch_array($res)){$sselect=$row['id']==$empstate ? 'selected' :'';?>
                        <option value="<?php echo $row['id'];?>" <?php echo $sselect;?> ><?php echo $row['name'];?> </option>
                        <?php }?>
                      </select> 
                   
                </div>

                <div class="form-group">
                  <label for="empcity">City</label>
                  <select class="form-control" name="empcity" id="empcity" required>
                              <option value="">Select city </option>
                              <?php $res=mysqli_query($con,"select id,city from cities where state_id='$empstate' order by city"); while($row=mysqli_fetch_array($res)){ $cselect=$row['id']==$empcity ? 'selected' :'';?>
                              <option value="<?php echo $row['id'];?>" <?php echo $cselect;?> ><?php echo $row['city'];?></option>
                              <?php }?>
                          
                            </select>
                   
                </div>

                <br>
                <br>
                <br>
                
                 <div style="align:center">  <center><a href="#locationmodel" data-target="#locationmodel" data-toggle="modal" style="font-size: x-large;" >Set Location</a>   </center> </div> 
                <div class="form-group">
                  <label for="emplat">Latitude:</label>
                  <input type="text" class="form-control pull-right" name="emplat" id="emplat" value="<?php echo $lat;?>" />
              </div>
             
            <div class="form-group">
                  <label for="emplng" >Longitude:</label>
                  <input type="text" class="form-control pull-right" name="emplng" id="emplng" value="<?php echo $lng;?>" />

            </div>
            <br>
            <br>
            <br>
            <br>
             
            <button type="submit"  id="btnaddproduct" class="btn btn-primary pull-right "><span class="fa fa-edit"></span> Update Details</button>

            </div>
            <!-- col 4 Mid -->
            
            <div class="col-md-6">
                 
            <table class="col-md-12 " >
												<thead>
												<tr>
                        <th> <h4><b>Route </b></h4></th>
													<th> <h4><b>No. of 
                        <br> Outlets </b></h4></th>
                        <th> <h4><b>  Lastvisit </b></h4></th>
                        <th> <h4><b>  Total (₹) </b></h4></th>
												</tr>
												</thead>
												<tbody>
                        
                           <?php 

                        foreach ($arrayRoute as $arrayRouteValue) {
                            
                            echo "<tr> <td> - ".$arrayRouteValue['name'] ."</td>";
                            echo  "<td>" .$arrayRouteValue['total_outlet_count'] ."</td>";
                             echo  "<td>" .$arrayRouteValue['lastvisit'] ." </td>";
                             echo  "<td class='pull-right' > " .$arrayRouteValue['total_amount_value'] ." </td></tr>";
                        }
                        ?>


												</tbody>


                        <tbody>
												<tr> <td> <br></td> <td></td> <tr>
												<tr> <td> <br> </td> <td></td> <tr>
												<tr> <td> <br> </td> <td></td> <tr>
												<tr> <td> <br> </td> <td></td> <tr>
                        <th> <h4><b> Total No. of Routes </b></h4></th>
													<th> <h4><b> <?php echo $i ;?> </b></h4></th>
												</tr>
                        <tr>
                        <th> <h4><b> Total No. of Routes </b></h4></th>
													<th> <h4><b> <?php echo $totalOutlets ;?> </b></h4></th>
												</tr>
												</tbody>

											</table>

            
       
                    
            </div>
          </div>
         </div>
              <!-- /.box-body -->
               
              <div class="box-footer">
                <div class="progress progress-striped active" id="progress" style="display:none;">
                   <div class="progress-bar progress-bar-success" style="width: 100%">
                   </div>
                </div>
                <a href="distributors" class="btn btn-danger"><span class="fa fa-remove"></span> Cancel</a>

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
       
	   //var form=$(this).parent("form");
	       
	 
        var fd = new FormData();
		var progress=$("#progress");
	//	$.each(files, function(key, value){
     //       fd.append(key, value);
      //  });
		
		var id = $('#id').val();
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
		
       
		fd.append('id',id);
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
            url: 'api/distributor-api.php?edit',
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
                    alert("Distributor Details Update Successfully...");
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
		            $('#stockistid').val('');
		            $('#empsname').val('');
		            $('#empcname').val('');
					
					window.location="distributors";
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
});

</script>
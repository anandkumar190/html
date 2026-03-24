<?php require("connect.php");?>
<?php $title="Edit Outlet"?>
<?php require("header.php");?>

<?php 
        $id='';
		$name='';
		$address='';
		$lastvisitpic='';
		$contactperson='';
		$contact='';
		$pincode='';
		$gstnumber='';
		$outlettype='';
		$outletsubtype='';
		$competitor_presense='';
		$distributorid='';
		$street='';
		$locality='';
		$city='';
		$state='';
		$latitude='';
		$longitude='';
		$areaid='';
		$lastvisit='';
		$creationdate='';
		$createdby='';
			
  if(isset($_GET['editid']))
  {
	  extract($_GET);
	$res=mysqli_query($con,"select 
        o.id,
        o.name,
        o.address,
        o.lastvisitpic,
        o.contactperson,
        o.contact,
        o.lastvisit,
        o.gstnumber,
        o.createdby,
        o.pincode,
        o.outletsubtype,
        o.outlettype,
        o.creationdate,
        o.distrvisitdate,
        o.longitude,
        o.latitude,
        o.competitor_presense,
        a.activitytype,
        a.activitydate,
        a.activitytime,
        a.feedback,
        a.battery,
        a.rating,
        area.area,
        area.id as areaid,
        regions.name AS regionsname,
        cities.city,
        states.name AS state,
        e.name AS empname,
        e.empid ,
        emp.id AS distributorid
      FROM outletactivity a 
           JOIN outlets o ON a.outletid = o.id 
           JOIN employees e ON e.id = a.userid 
           JOIN area ON area.id = o.routeid 
           JOIN employees emp ON emp.id=area.distributor_id
           JOIN regions ON regions.id = area.region 
           JOIN cities ON cities.id = regions.city_id 
           JOIN states ON states.id = cities.state_id where o.id='$editid'");
	if($row=mysqli_fetch_array($res))
	{
	
		$id=$row['id'];
		$name=$row['name'];
		$address=$row['address'];
		$lastvisitpic=$row['lastvisitpic'];
		$contactperson=$row['contactperson'];
		$contact=$row['contact'];
		$pincode=$row['pincode'];
		$gstnumber=$row['gstnumber'];
		$outlettype=$row['outlettype'];
		$outletsubtype=$row['outletsubtype'];
		$competitor_presense=$row['competitor_presense'];
		$distributorid=$row['distributorid'];
		$street=$row['regionsname'];
		$locality=$row['area'];
		$city=$row['city'];
		$state=$row['state'];
		$latitude=$row['latitude'];
		$longitude=$row['longitude'];
		$areaid=$row['areaid'];
		$lastvisit=$row['lastvisit'];
		$creationdate=$row['creationdate'];
		$createdby=@$row['createdby'];
		$distrvisitdate=$row['distrvisitdate'];
		
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
              
              <li class="active"><a href="#tear-tree-view" data-toggle="tab">Edit Outlet <?php echo $name;?></a></li>
              
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
              <h3 class="box-title">Edit Outlet</h3>
            </div>
            <!-- /.box-header -->
          <div class="box-body">
           <div class="row">
            <div class="col-md-4">
                <!-- form start -->
            
              
                <div class="form-group controls">
                <input type="hidden" name="id" id="id" value="<?php echo $id;?>" />
                  <label for="name">Name : </label>
                  <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" value="<?php echo $name;?>"  required="required"/>
                </div>
                
                <div class="form-group">
                  <label for="contactperson">Contact Person : </label>
                  <input type="text" class="form-control" name="contactperson" id="contactperson" value="<?php echo $contactperson;?>" placeholder="Contact Person" required/>
                </div>
                <div class="form-group">
                  <label for="contact">Contact </label>
                  <input type="text" class="form-control" pattern="^\d{10}?$" data-dv-message="Enter Contact No in 10 digits" name="contact" id="contact" value="<?php echo $contact;?>" minlength="10" maxlength="10" placeholder="Contact No" required/>
                </div>
                <div class="form-group">
                  <label for="pincode">Pincode </label>
                  <input type="text" class="form-control" pattern="^\d{6}?$" data-dv-message="Enter Pincode in 6 digits" name="pincode" id="pincode" value="<?php echo $pincode;?>" minlength="6" maxlength="6" placeholder="Pincode" required/>
                </div>
                
                <div class="form-group">
                  <label for="gstnumber">GST Number : </label>
                  <input type="text" class="form-control" name="gstnumber" id="gstnumber" value="<?php echo $gstnumber;?>" pattern="\d{2}[A-Z]{5}\d{4}[A-Z]{1}\d{1}[A-Z]{1}\d{1}" pattern-bv-message="Enter Correct  GST NO" placeholder="GST Number" />
                </div>
                <div class="form-group">
                  <label for="outlettype">Outlet Type : </label>
                  <input type="text" class="form-control" name="outlettype" id="outlettype" value="<?php echo $outlettype;?>" placeholder="OutletType" required />
                </div>
                
                <!-- <div class="form-group">
                  <label for="outletsubtype">OutletSub Type : </label>
                  <input type="text" class="form-control" name="outletsubtype" id="outletsubtype" value="" placeholder="OutletSub Type" required />
                </div>       -->
                
                <div class="form-group">
                  <label for="address">Address </label>
				  <textarea class="form-control" name="address" id="address" placeholder="Address"><?php echo $address;?></textarea>
                </div>
             </div> <!-- col 4 close--> 
             
             <div class="col-md-4">
                
                
                <!-- <div class="form-group">
                  <label for="competitor_presense">Competitor Presense : </label>
                  <input type="text" class="form-control" name="competitor_presense" id="competitor_presense" value="" placeholder="Competitor Presense" required />
                </div> -->
                      
                <div class="form-group">
                  <label for="distributorid">Select Distributor</label>
                  <select  class="form-control select2" name="distributorid" id="distributorid" required>
                   
                   <option value="">Select Distributor</option>
                    <?php $res=mysqli_query($con,"select id,name,empid from employees where usertype order by id"); while($row=mysqli_fetch_array($res)){?>
                    <option value="<?php echo $row['id'];?>"><?php echo $row['name']."  (".$row['empid'].")";?></option>
                    <?php }?>
                  </select>
                  <script>
                    var distributorid=document.getElementById("distributorid");
					distributorid.value="<?php echo $distributorid;?>";
					
                  </script>
                </div>
                
                <div class="form-group">
                  <label for="street">Street</label>
                  <input type="text"  class="form-control" name="street" id="street"  value="<?php echo $street;?>" required="required" />
                   
                </div>
                
                <div class="form-group">
                  <label for="locality">Locality</label>
                  <input type="text"  class="form-control" name="locality" id="locality"  value="<?php echo $locality;?>" required="required" />
                   
                </div>
                
                <div class="form-group">
                  <label for="city">City</label>
                  <input type="text"  class="form-control" name="city" id="city"  value="<?php echo $city;?>" required="required" />
                   
                </div>
                
                <div class="form-group">
                  <label for="state">State</label>
                  <input type="text"  class="form-control" name="state" id="state"  value="<?php echo $state;?>"  required="required"/>
                   
                </div>
                
                <div class="form-group">
                  <label for="areaid">Select Area</label>
                  <select  class="form-control select2" name="areaid" id="areaid" required>
                   
                   <option value="">Select Area</option>
                    <?php $res=mysqli_query($con,"select * from area order by region"); while($row=mysqli_fetch_array($res)){?>
                    <option value="<?php echo $row['id'];?>"><?php echo $row['region']." Area (".$row['area'].")";?></option>
                    <?php }?>
                  </select>
                  <script>
                    var areaid=document.getElementById("areaid");
					areaid.value="<?php echo $areaid;?>";
					
                  </script>
                </div>
                
            </div>
            <!-- col 4 Mid -->
            
            <div class="col-md-4">
                 
                <div class="form-group">
                  <label for="lastvisit">LastVisit:</label>
                  <input type="text" class="form-control pull-right" name="lastvisit" id="lastvisit" value="<?php echo $lastvisit;?>" readonly />
                </div>
                <div class="form-group">
                  <label for="creationdate">Creation Date :</label>
                  <input type="text" class="form-control pull-right" name="creationdate" id="creationdate" value="<?php echo $creationdate;?>" readonly />
                </div>
            
                <!-- <div class="form-group">
                  <label for="lastvisit">Last Distibutor Visit:</label>
                  <input type="text" class="form-control pull-right" name="distrvisitdate" id="distrvisitdate" value="" readonly />
                </div> -->
                 
            
             <div class="form-group">
                  <label for="latitude">Latitude:</label>
                  <input type="text" class="form-control pull-right" name="emplat" id="emplat" value="<?php echo $latitude;?>"  />
              </div>
             
            <div class="form-group">
                  <label for="longitude" >Longitude:</label>
                  <input type="text" class="form-control pull-right" name="emplng" id="emplng" value="<?php echo $longitude;?>"/>
                  <a href="#locationmodel" data-target="#locationmodel" data-toggle="modal">Set Location</a>
            </div>
             
            
                
                <div class="form-group">
                  <label for="pimage">Select Image</label>
                  <input type="file" id="lastvisitpic" name="lastvisitpic"/><img src="imgoutlets/<?php echo $lastvisitpic;?>" style="height:200px; width:180px;" class="img img-thumbnail" />
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
                <a href="activityvisit" class="btn btn-danger"><span class="fa fa-remove"></span> Cancel</a>
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
     //var files;
	 //$('input[type=file]').on('change', prepareUpload);
      //   function prepareUpload(event){
      //    files = event.target.files;
      //  };
    //$("#empdoj").datepicker({format:'yyyy-m-dd',autoclose:true});
	//$("#empdol").datepicker({format:'yyyy-m-dd',autoclose:true});
    //$(".table").dataTable({sort:false});
	/*$("#emparea").change(function(){
		     var id=$(this).val();
		     $.getJSON('api/registerarea.php?getarea&id='+id).done(function(data){
				 
				 $.each(data,function(i,user){
				     $("#empcity").val(user.region);
					 $("#empstate").val(user.state);	 
					 });
				 });
		});*/
});

function sendData(){
       
	   //var form=$(this).parent("form");
	       
	 
        var fd = new FormData();
		var progress=$("#progress");
	//	$.each(files, function(key, value){
     //       fd.append(key, value);
      //  });
		
        var files = $('#lastvisitpic')[0].files[0];
		var id = $('#id').val();
		var name = $('#name').val();
		
		var contactperson = $('#contactperson').val();
		var contact = $('#contact').val();
		var address = $('#address').val();
		var pincode = $('#pincode').val();
		var gstnumber = $('#gstnumber').val();
		var outlettype = $('#outlettype').val();
		var outletsubtype = $('#outletsubtype').val();
		var competitor_presense = $('#competitor_presense').val();
		var distributorid = $('#distributorid').val();
		var street = $('#street').val();
		var locality = $('#locality').val();
		var city = $('#city').val();
		var state = $('#state').val();
		var latitude = $('#emplat').val();
		var longitude = $('#emplng').val();
		var areaid = $('#areaid').val();
		
        fd.append('lastvisitpic',files);
		fd.append('id',id);
		fd.append('name',name);
		fd.append('address',address);
		fd.append('contactperson',contactperson);
		fd.append('contact',contact);
		fd.append('pincode',pincode);
		
		fd.append('gstnumber',gstnumber);
		fd.append('outlettype',outlettype);
		fd.append('outletsubtype',outletsubtype);
		fd.append('competitor_presense',competitor_presense);
		fd.append('distributorid',distributorid);
		fd.append('street',street);
		fd.append('locality',locality);
		fd.append('city',city);
		fd.append('state',state);
		fd.append('latitude',latitude);
		fd.append('longitude',longitude);
		fd.append('areaid',areaid);
           progress.fadeIn("slow");
        $.ajax({
            url: 'api/outlets-web.php?edit',
            type: 'post',
            data: fd,
            success: function(response){
				if(response=="already")
				{
				   alert("Outlet Already Exist with this name city contact outlettype and contact person name !");
				   return;
			    }
				if(response=="filetype")
				{
				   alert("Please Upload Images(PNG,JPG & JPEG) Files Only...");
				   return;
			    }
				if(response=="filesize")
				{
					alert("Image can't be Greater than 800KB .");
				   return;					
				}
				
                if(response =="success"){
                    alert("Outlet Details Update Successfully...");
					progress.fadeOut("slow");
					
					$('#lastvisitpic')[0].files[0];
		            $('#name').val('');
		            $('#address').val('');
		            $('#pincode').val('');
		            $('#contact').val('');
		            $('#contactperson').val('');
		            $('#outlettype').val('');
					$('#outletsubtype').val('');
					$('#competitor_presense').val('');
					$('#street').val('');
					$('#locality').val('');
					$('#lastvisit').val('');
					$('#creationdate').val('');
					$('#distrvisitdate').val('');
		            $('#areaid').val('');
		            $('#city').val('');
		            $('#state').val('');
		            
		            $('#gstnumber').val('');
		            $('#latitude').val('0.0');
		            $('#longitude').val('0.0');
		            $('#distributorid').val('');
					
					//window.location="distributors";
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
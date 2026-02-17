<?php require("connect.php");?>
<?php $title="Products"?>
<?php require("header.php");?>

<?php
  $pid="";
  $pname="";
  $pshort="";
  $punit="";
  $pmrp="";
  $prate="";
  $pimage="";
  $prodective_cell="";

  if(isset($_GET['editid']))
  {
	  extract($_GET);
	$res=mysqli_query($con,"select * from skus where id='$editid'");
	if($row=mysqli_fetch_array($res))
	{


    $pid=$row[0];
		$pname=$row[1];
		$pshort=$row[2];
		$punit=$row[3];
		$pmrp=$row[4];
		$prate=$row[5];

    $catid=$row[8];
    $scatid=$row[9];
    $cunit=$row[10];
    $cmrp=$row[11];
    $unit_no=$row[12];
    $discount=$row[13];

    $pimage=$row[14];
    $prodective_cell=$row[15];
	}
  }
?>

<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <?php include("content-header.php");?>
<link rel="stylesheet" href="assets/node_modules/datatables/jquery.dataTables.min.css"/>

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
              
              <li class="active"><a href="#tear-tree-view" data-toggle="tab">Edit Product</a></li>
              
            </ul>
            
            <div class="tab-content no-padding">
              
              
              <!--Today Activity tab Start-->
              
              
              <!--TreeView tab Start-->
              <div class="chart tab-pane active" id="tear-tree-view" style="position: relative; min-height: 300px;">
                
      <section class="content">
      <div class="row">
        <form id="productform" role="form" method="post" enctype="multipart/form-data">
        <!-- left column -->
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Add Product</h3>
            </div>
            <!-- /.box-header -->
          
          <div class="row">
            <div class="col-md-6">
                <!-- form start -->
            
              <div class="box-body">
                <div class="form-group controls">
                <input type="hidden" class="form-control" name="pid" id="pid" value="<?php echo $pid;?>"/>
                  <label for="pname">Product Name : </label>
                  <input type="text" class="form-control" name="pname" id="pname" value="<?php echo $pname;?>" placeholder="Enter Product Name"  required="required"/>
                </div>
                <div class="form-group">
                  <label for="pshort">Product Short Name</label>
                  <input type="text" class="form-control" name="pshort" id="pshort" value="<?php echo $pshort;?>"  placeholder="Product Short Name" required/>
                </div>
                
                
                <div class="form-group">
                  <label for="punit">Select Category</label>
                  <select  class="form-control" name="catid" id="catid" required>
                     <option value="">Select Product Category</option>
               <?php $res=mysqli_query($con,"select id,name from product_cat order by name "); while($row=mysqli_fetch_array($res)){$ctselect=$row['id']==$catid ? 'selected' :'';?>
               <option value="<?php echo $row['id'];?>"  <?php echo $ctselect;?> > <?php echo $row['name'];?></option>
               <?php }?>
             </select>
                </div>

                
                <div class="form-group">
                  <label for="scatid">Select Sub Category</label>
                  <select  class="form-control" name="scatid" id="scatid" required>

                   <option value="">Select Product Sub Category</option>
                    <?php $res=mysqli_query($con,"SELECT id,name FROM parduct_sub_cat Where cat_id='$catid' order by name "); while($row=mysqli_fetch_array($res)){$sctselect=$row['id']==$scatid ? 'selected' :'';?>
                   <option value="<?php echo $row['id'];?>" <?php echo $sctselect; ?> ><?php echo $row['name'];?></option>
                   <?php }?>

                  </select>
                </div>

                <div class="form-group">
                  <label for="prodective_cell">Order</label>
                  <input type="number" class="form-control" name="prodective_cell" id="prodective_cell" value="<?php echo $prodective_cell;?>" placeholder="Enter Order" min="0"/>
                </div>




              </div>
            </div>
            <div class="col-md-6">
             <div class="box-body">

             

                
                <div class="form-group">
                  <label for="pimage">File input</label>
                  <input type="file" id="pimage" name="pimage"/>        <img src="imgproduct/<?php echo $pimage;?>" style="height:100px; width:100px;" class="img img-thumbnail" />
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
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <a href="skus" class="btn btn-danger"><span class="fa fa-remove"></span> Cancel</a>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <button type="submit"  id="btnaddproduct" class="btn btn-primary pull-right "><span class="fa fa-edit"></span> Save Changes</button> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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

<script src="assets/node_modules/datatables/jquery.dataTables.min.js">
</script>
<script src="dist/dist/js/bootstrapValidator.min.js"></script>
<script>
     
     
    function sendData(){
       
	   //var form=$(this).parent("form");
        var fd = new FormData();
		var progress=$("#progress");
	//	$.each(files, function(key, value){
     //       fd.append(key, value);
      //  });
		
        var files = $('#pimage')[0].files[0];
		var pid=$('#pid').val();
		var pname = $('#pname').val();
		var pshort = $('#pshort').val();
    var catid = $('#catid').val();
    var scatid = $('#scatid').val();
    var prodective_cell = $('#prodective_cell').val();
		if(files!=null)
		{
        fd.append('pimage',files);
		}

		fd.append('pid',pid);
		fd.append('pname',pname);
		fd.append('pshort',pshort);
    fd.append('catid',catid);
    fd.append('scatid',scatid);
    fd.append('prodective_cell',prodective_cell);
		
           progress.fadeIn("slow");
        $.ajax({
            url: 'api/addproduct.php?edit',
            type: 'post',
            data: fd,
            success: function(response){
				if(response=="get_value")
				{
					alert("product Short Name Already exists !");
					return;
				}
                if(response =="success"){
                    alert("Product Changes Saved Successfully...");
					progress.fadeOut("slow");
					$('#pid').val('');
					$('#pname').val('');
					$('#pshort').val('');
					$('#punit').val('0');
					$('#pmrp').val('0.0');
					$('#prate').val('0.0');
					$('#pimage').val(''); 
					window.location="skus";
					
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
                        },
                        
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





        $("#catid").change(function(){
            var catid= $(this).val();
          $.get("api/product-sub_catapi.php?sub_cats",{c_id:catid},function(data, status){

        var selectbox = $('#scatid');
           selectbox.empty();
             var list = '<option value="">Select Sub Category </option>';
            $.each(JSON.parse(data), function(key,value) { 

             console.log(value.id);
             console.log(value.name);
             list += "<option value='" +value.id+ "'>" +value.name+ "</option>";
             });
             
           selectbox.html(list);
        
         });


          });


        $("#cmrp").change(function(){
            var cmrp= parseInt($(this).val());
            var unit_no=parseInt($("#unit_no").val());
         
           $("#pmrp").val(cmrp*unit_no);

          });

         $("#discount").change(function(){

           var discount= parseInt($(this).val());
           var pmrp=parseInt($("#pmrp").val());
           var dis_amount=(discount*pmrp)/100;
           dis_amount=pmrp-dis_amount;
          dis_amount=Math.round(dis_amount);
          
            $("#prate").val(dis_amount);

           });

         
});

</script>

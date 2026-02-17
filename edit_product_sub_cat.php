<?php require("connect.php");?>
<?php $title="Edit Sub Satgory"?>
<?php require("header.php");?>

<?php 
    
    $id='';
		$name='';
		$cat='';
		
  if(isset($_GET['editid']))
  {
	  extract($_GET);
	$res=mysqli_query($con,"select * from parduct_sub_cat where id='$editid'");
  	if($row=mysqli_fetch_array($res))
	  {
	  $cat=$row['cat_id'];
		$id=$row['id'];
		$name=$row['name'];
		$pmrp=$row['pmrp'];
    $punit=$row['punit'];
    $prate=$row['prate'];
    $cunit=$row['cunit'];
    $cmrp=$row['cmrp'];
    $unit_no=$row['unit_no'];
    $discount=$row['discount'];
    $prodective_cell=$row['prodective_cell'];
   
	

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
              
              <li class="active"><a href="#tear-tree-view" data-toggle="tab">Edit Sub Category</a></li>
              
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
              <h3 class="box-title">Edit Sub category</h3>
            </div>
            <!-- /.box-header -->
          <div class="box-body">
           <div class="row">
               <div class="col-md-6">
               
                <div class="form-group">
                  <label for="empdesignation">Select Category</label>
                  <select  class="form-control" name="cat_id" id="cat_id" required>
                   <option value="">Select Category</option>
                    <?php $res=mysqli_query($con,"select id,name from product_cat order by name "); while($row=mysqli_fetch_array($res)){?>
                    <option value="<?php echo $row['id'];?>"><?php echo $row['name'];?></option>
                    <?php }?>
                  </select>
                </div>
                         <div class="form-group">
                           <label for="punit"> Select Retailer Unit</label>
                           <select  class="form-control" name="retailer_unit" id="punit" required>
                               <option value="">Select Unit</option>
                          <?php $res=mysqli_query($con,"select id,name from sku_unit order by name "); while($row=mysqli_fetch_array($res)){?>
                          <option value="<?php echo $row['id'];?>"><?php echo $row['name'];?></option>
                          <?php }?>                 
                           </select>
                         </div>



                         <div class="form-group">
                           <label for="punit"> Select Customer unit</label>
                           <select  class="form-control" name="cunit" id="cunit" required>
                               <option value="">Select Unit</option>
                         <?php $res=mysqli_query($con,"select id,name from sku_unit order by name "); while($row=mysqli_fetch_array($res)){?>
                         <option value="<?php echo $row['id'];?>"><?php echo $row['name'];?></option>
                         <?php }?>   
                           </select>
                         </div>


                      <div class="form-group">
                          <div style="width:25%; float:left;" >
                          <span> Retailer Unit</span>   
                          </div>   
                            <div>   
                            <input type="text"  value="<?php echo $unit_no;?>" name="unit_no" id="unit_no" class="form-control" style="width:30%; float:left;" required/>
                            </div>
                          
                          <div style="width:30%; float:left;text-align: center; ">
                            <span> End Customer Unit</span>
                          </div>

                      </div>
<br>
                         <div class="form-group">
                         </div>


                <div class="form-group">
                  <label for="Order">Order : </label>
                  <input type="number" class="form-control" value="<?php echo $prodective_cell;?>" name="prodective_cell" id="prodective_cell" placeholder="Order"  required="required"/>
                </div>
            



        
                              <script>
                             var state=document.getElementById("cat_id");
                              state.value="<?php echo $cat;?>"; 

                           var punit=document.getElementById("punit");
                              punit.value="<?php echo $punit;?>";
                      
                           var cunit=document.getElementById("cunit");
                              cunit.value="<?php echo $cunit;?>";
                      
                              </script>
            </div>

               
               <div class="col-md-6">
                <!-- form start -->
            
                       <input type="hidden" name="id" id="id" value="<?php echo $id;?>" />
                <div class="form-group controls">
                  <label for="empname">Sub Category : </label>
                  <input type="text" class="form-control" name="name"  value="<?php echo $name;?>" id="name" placeholder="Enter Name"  required="required"/>
                </div>
                  <div class="form-group">
                    <label for="cmrp">MRP For End Customer SKU  : </label>
                    <input type="text"  class="form-control" id="cmrp" placeholder="Product MRP" value="<?php echo $cmrp;?>" pattern="^\d{0,8}(\.\d{0,2})?$" pattern-bv-message="Enter MRP For End Customer SKU" name="cmrp" required/>
                  </div>

                <div class="form-group">
                    <label for="pmrp">MRP For Retailer SKU : </label>
                    <input type="text" class="form-control" id="pmrp" placeholder="Product MRP" value="<?php echo $pmrp;?>" pattern="^\d{0,8}(\.\d{0,2})?$" pattern-bv-message="Enter MRP For Retailer SKU" name="pmrp" required/>
                </div>

                <div class="form-group">
                    <label for="discount">Default Discount (%) : </label>
                    <input type="text" class="form-control" id="discount" placeholder="Product MRP" value="<?php echo $discount;?>" pattern="^\d{0,8}(\.\d{0,2})?$" pattern-bv-message="Enter Default Discount" name="discount" required/>
                </div>

                <div class="form-group">
                    <label for="prate">Default Selling Price (Rs.): </label>
                    <input type="text" class="form-control" id="prate" placeholder="Product MRP" value="<?php echo $prate;?>" pattern="^\d{0,8}(\.\d{0,2})?$" pattern-bv-message="Enter Default Selling Price" name="prate" required/>
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
                <a href="sub-product-cat" class="btn btn-danger"><span class="fa fa-remove"></span> Back</a>
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
        var cat_id = $('#cat_id').val();
        var pmrp = $('#pmrp').val();
        var punit = $('#punit').val();
        var prate = $('#prate').val();
        var cunit = $('#cunit').val();
        var cmrp = $('#cmrp').val();
        var unit_no = $('#unit_no').val();
        var discount = $('#discount').val();
        var prodective_cell =$('#prodective_cell').val();


        
     	fd.append('id',id);
        fd.append('name',name);
        fd.append('cat_id',cat_id);
                    
            fd.append('pmrp',pmrp);
            fd.append('punit',punit);
            fd.append('prate',prate);
            fd.append('cunit',cunit);
            fd.append('cmrp',cmrp);
            fd.append('unit_no',unit_no);
            fd.append('discount',discount);
            fd.append('prodective_cell',prodective_cell);

        
             progress.fadeIn("slow");
             $.ajax({
                 url: 'api/product-sub_catapi.php?edit',
                 type: 'post',
                 data: fd,
                 success: function(response){
            if(response=="name")
            {
               alert("sub category  Already Exist!");
               return;
              }
      
                      if(response =="success"){
                         alert("Sub category Updated Successfully...");
              progress.fadeOut("slow");
        
                    $('#name').val('');
                    $('#cat_id').val('');
        
              
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
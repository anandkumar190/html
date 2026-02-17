<?php require("connect.php");?>
<?php $title="Order Booking";?>
<?php require("header.php");?>

<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <?php include("content-header.php");?>

   <link href="assets/node_modules/datatables/jquery.dataTables.min.css" rel="stylesheet"/>
   <link rel="stylesheet" href="assets/bootstrap-toggle.min.css"/> 
   <link rel="stylesheet" href="dist/dist/css/bootstrapValidator.min.css"/>
   
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
              <li class="active"><a href="#today-activity" data-toggle="tab">Sub Product category </a></li>
              <li><a href="#tear-tree-view" data-toggle="tab">Add Sub Product category</a></li>
              
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
            </div>-->
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              
              <!-- Row -->
					<div class="row">
						<div class="col-sm-12">
							<div class="panel panel-default card-view">
								<div class="panel-heading">
									<div class="pull-left">
										<h6 class="panel-title txt-dark">Products</h6>
									</div>
									<div class="clearfix"></div>
								</div>
								<div class="panel-wrapper collapse in">
									<div class="panel-body">
										<div class="table-wrap">
											<table id="userstable" class="table" data-paging="false" data-filtering="true" data-sorting="true">
												<thead>
												<tr>
                              
													<th>Action</th>
                          <th>Sub Category</th>
                            <th>Category</th>
													
                          <th>Retailer price</th>
                          <th>Customer Price</th>
                          <th>Price</th>
                          <th>Retailer Unit</th>
                          <th>Costomer Unit</th>
                          <th>Number of Unit </th>
                          <th>Discount </th>
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
              <h3 class="box-title">Add Sub Category</h3>
            </div>
            <!-- /.box-header -->
          <div class="box-body">
           <div class="row">
            
             <div class="col-md-6">
              
                <div class="form-group">
                  <label for="empdesignation">Select Category</label>
                  <select  class="form-control" name="cat_id" id="cat_id" required>
                   <option value="">Select Category</option>
                    <?php $res=mysqli_query($con,"select id,name from product_cat"); while($row=mysqli_fetch_array($res)){?>
                    <option value="<?php echo $row['id'];?>"><?php echo $row['name'];?></option>
                    <?php }?>
                  </select>
                </div>
               
                <div class="form-group">
                  <label for="punit"> Select Retailer Unit</label>
                  <select  class="form-control" name="retailer_unit" id="punit" required>
                      <option value="">Select Unit</option>
                 <?php $res=mysqli_query($con,"select id,name from sku_unit"); while($row=mysqli_fetch_array($res)){?>
                 <option value="<?php echo $row['id'];?>"><?php echo $row['name'];?></option>
                 <?php }?>                 
                  </select>
                </div>



                <div class="form-group">
                  <label for="punit"> Select Customer unit</label>
                  <select  class="form-control" name="cunit" id="cunit" required>
                      <option value="">Select Unit</option>
                <?php $res=mysqli_query($con,"select id,name from sku_unit"); while($row=mysqli_fetch_array($res)){?>
                <option value="<?php echo $row['id'];?>"><?php echo $row['name'];?></option>
                <?php }?>   
                  </select>
                </div>


      <div class="form-group">
          <div style="width:25%; float:left;" >
          <span> Retailer Unit</span>   
          </div>   
          <div>
            
          <input type="text" name="unit_no" id="unit_no" class="form-control" style="width:30%; float:left;" required/>
          </div>
          
          <div style="width:30%; float:left;text-align: center; ">
            <span> End Customer Unit</span>
          </div>

      </div>


            
            </div>
            <!-- col 4 Mid -->
            
            
            <div class="col-md-6">
                <!-- form start -->       
              
                <div class="form-group controls">
                  <label for="empname">Sub category : </label>
                  <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name"  required="required"/>
                </div>
                  <div class="form-group">
                    <label for="cmrp">MRP For End Customer SKU  : </label>
                    <input type="text" class="form-control" id="cmrp" placeholder="Product MRP" value="0" pattern="^\d{0,8}(\.\d{0,2})?$" pattern-bv-message="Enter MRP For End Customer SKU" name="cmrp" required/>
                  </div>

                <div class="form-group">
                    <label for="pmrp">MRP For Retailer SKU : </label>
                    <input type="text" class="form-control" id="pmrp" placeholder="Product MRP" value="0" pattern="^\d{0,8}(\.\d{0,2})?$" pattern-bv-message="Enter MRP For Retailer SKU" name="pmrp" required/>
                </div>

                <div class="form-group">
                    <label for="discount">Default Discount (%) : </label>
                    <input type="text" class="form-control" id="discount" placeholder="Product MRP" value="0" pattern="^\d{0,8}(\.\d{0,2})?$" pattern-bv-message="Enter Default Discount" name="discount" required/>
                </div>

                <div class="form-group">
                    <label for="prate">Default Selling Price (Rs.): </label>
                    <input type="text" class="form-control" id="prate" placeholder="Product MRP" value="0" pattern="^\d{0,8}(\.\d{0,2})?$" pattern-bv-message="Enter Default Selling Price" name="prate" required/>
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
                <button type="submit"  id="btnaddproduct" class="btn btn-primary pull-right "><span class="fa fa-plus-circle"></span> Submit</button>
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

 
    <!-- start - This is for export functionality only -->
    <script src="assets/node_modules/datatables/jquery.dataTables.min.js"></script>
     
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
    <!-- end - This is for export functionality only -->
    <script src="assets/bootstrap-toggle.min.js"></script>

<script>

	function loaddata()
	{
     $.ajax({
		  url:"api/product-sub_catapi.php?show",
		  type:"POST",
		  contentType:"application/json; charset=utf-8",
		  success:function(data){
			   //alert(data);
			    data=JSON.parse(data);
	            $("#userstable").dataTable(
				{
				  dom: 'Bfrtip',	
				  sort:false,
				  data:data,
				  destroy:true,
				  paging:false,
				  processing: true,
				  language: {
                        processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '},
				  
				  buttons: [
                'copy', { extend: 'csv', title: function () { var printTitle = 'All Visitis'; return printTitle; } }, 'excel', 'pdf', { extend: 'print', title: function () { var printTitle = ''; return printTitle; } }
                   ],
				  columns:[
				
					      {
							 data:'id',render:function(value){
						     return "<a href='edit_product_sub_cat?editid="+value+"' onclick=\"return confirm('Are you sure you want to delete this Order?');\"><span class='fa fa-edit'></span></a><a href='api/product-sub_catapi?deletescat&id="+value+"'><span class='fa fa-trash'></span></a> ";
						  }},
						 	  {
						 	data:'name'
						},
								{
						   data:'cat'
						},
						{
						   data:'pmrp'
						},
            {
               data:'cmrp'
            },
            {
               data:'prate'
            },
            {
               data:'punit'
            },
            {
               data:'cunit'
            },
         
            {
               data:'unit_no'
            },
           {
               data:'discount'
            }
					
					  ]
				});	
				$(function() {
                  $('.status').bootstrapToggle();
               	  //$('.track').bootstrapToggle();
                 });	   
			  },
		  error:function(e){
			   alert(e.error);
			  }	  
		 });
   
	}

$(document).ready(function(){
   loaddata();
   $("#empdoj").datepicker({format:'yyyy-m-dd',autoclose:true});

   $('#userstable tbody').on( 'click','tr td', function (){
     
	 var ischecked=$(this).children('.toggle').children('.status').prop("checked"); 
	var id=$(this).children('input:hidden').val();
	 
		if(ischecked)
		{	
		  	  	//alert(id);
		         $.ajax({
		         url:"api/login.php?userstatus&status=0&id="+id,
		         type:"POST",
		         contentType:"application/json; charset=utf-8",
			     success: function(data){
			         		if(data=="success")
							{
								loaddata();
								
							}
			 		 },
				error:function(e){
					}	 
			 });
		}
		else{
		  $.ajax({
		         url:"api/login.php?userstatus&status=1&id="+id,
		         type:"POST",
		         contentType:"application/json; charset=utf-8",
			     success: function(data){
			         		if(data=="success")
							{
								loaddata();
								
							}
			 		 },
				error:function(e){
					}	 
			 });
		}
    });


});

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



function sendData(){
       
	   //var form=$(this).parent("form");
	    //var form=$("#productform");
        var fd =new FormData();
		var progress=$("#progress");
    	//	$.each(files, function(key, value){
       //       fd.append(key, value);
       //  });
		
    
		var name = $('#name').val();
		var cat_id = $('#cat_id').val();
    var pmrp = $('#pmrp').val();
    var punit = $('#punit').val();
    var prate = $('#prate').val();
    var cunit = $('#cunit').val();
    var cmrp = $('#cmrp').val();
    var unit_no = $('#unit_no').val();
    var discount = $('#discount').val();


		

		fd.append('name',name);
		fd.append('cat_id',cat_id);
		
    fd.append('pmrp',pmrp);
    fd.append('punit',punit);
    fd.append('prate',prate);
    fd.append('cunit',cunit);
    fd.append('cmrp',cmrp);
    fd.append('unit_no',unit_no);
    fd.append('discount',discount);

        progress.fadeIn("slow");
        $.ajax({
            url: 'api/product-sub_catapi.php?insert',
            type: 'post',
            data: fd,
            success: function(response){
				if(response=="name")
				{
				   alert("city  Already Exist!");
				   return;
			    }
	
	                if(response =="success"){
                    alert(" Sub category Created Successfully...");
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
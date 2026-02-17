<?php require("connect.php");?>
<?php $title="Products"?>
<?php require("header.php");?>



<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <?php include("content-header.php");?>
<link rel="stylesheet" href="assets/node_modules/datatables/jquery.dataTables.min.css"/>

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
              <li class="active"><a href="#today-activity" data-toggle="tab">Products</a></li>
              <li><a href="#tear-tree-view" data-toggle="tab">Add Product</a></li>
              
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
										<h6 class="panel-title txt-dark">Products</h6>
									</div>
									<div class="clearfix"></div>
								</div>
								<div class="panel-wrapper collapse in">
									<div class="panel-body">
										<div class="table-wrap">
											<table id="skutable" class="table" data-paging="true" data-filtering="true" data-sorting="true">
												<thead>
												<tr>
													<th>Action</th>
													<th>Product Name</th>
													<th>Short Name</th>
													<th>UNIT</th>
													<th>MRP</th>
													<th>Price</th>
                                                    <td>Brand Name</td>
                                                    <th>Image</th>
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
              <h3 class="box-title">Add Product</h3>
            </div>
            <!-- /.box-header -->
          
          <div class="row">
            <div class="col-md-6">
                <!-- form start -->
            
              <div class="box-body">
                <div class="form-group controls">
                  <label for="pname">Product Name : </label>
                  <input type="text" class="form-control" name="pname" id="pname" placeholder="Enter Product Name"  required="required"/>
                </div>
                <div class="form-group">
                  <label for="pshort">Product Short Name</label>
                  <input type="text" class="form-control" name="pshort" id="pshort"  placeholder="Product Short Name" required/>
                </div>
                
                <div class="form-group">
                  <label for="punit">Select Unit</label>
                  <select  class="form-control" name="punit" id="punit" required>
                   <option value="">Select Product Unit</option>
                    <option value="Pouch">Pouch</option>
                    <option value="Gms">Gms</option>
                    <option value="Kg">Kg</option>
                    <option value="Pcs">Pcs</option>
                    <option value="Pack">Pack</option>
                    <option value="Bottle">Bottle</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-6">
             <div class="box-body">
              <div class="form-group">
                  <label for="pmrp">Product MRP : </label>
                  <input type="text" class="form-control" id="pmrp" placeholder="Product MRP" value="0.0" pattern="^\d{0,8}(\.\d{0,2})?$" pattern-bv-message="Enter Correct value for MRP" name="pmrp" required/>
                </div>
                
                <div class="form-group">
                  <label for="prate">Product Price : </label>
                  <input type="text" class="form-control" id="prate" placeholder="Product Price" value="0.0" name="prate" pattern="^\d{0,8}(\.\d{0,2})?$" data-bv-message="Enter Correct value for Price" required />
                </div>
                
                
                <div class="form-group">
                  <label for="pimage">File input</label>
                  <input type="file" id="pimage" name="pimage" required/>
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
                <button type="submit"  id="btnaddproduct" class="btn btn-primary pull-right "><span class="fa fa-plus-circle"></span> Add Product</button>
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
    <!-- end - This is for export functionality only -->

<script>

	function loaddata()
	{
     $.ajax({
		  url:"api/addproduct.php?show",
		  type:"POST",
		  contentType:"application/json; charset=utf-8",
		  success:function(data){
			   //alert(data);
			   data=JSON.parse(data);
	            $("#skutable").dataTable(
				{
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
						  return "<a href='editskus?editid="+value+"'><span class='fa fa-edit'></span></a>";
						  }},
						  {
							data:'productname'
						},
						{data:'productid'},{data:'unit'},{data:'mrp'},{data:'rate'},{data:'brandname'},{data:'image',render:function(value){
							return "<img src='imgproduct/"+value+"' class='img img-thumbnail' style='width:100px; height:100px;'/>";
							}}
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
});

function sendData(){
       
	   //var form=$(this).parent("form");
	       
	 
        var fd = new FormData();
		var progress=$("#progress");
	//	$.each(files, function(key, value){
     //       fd.append(key, value);
      //  });
		
        var files = $('#pimage')[0].files[0];
		var pname = $('#pname').val();
		var pshort = $('#pshort').val();
		var pmrp = $('#pmrp').val();
		var punit = $('#punit').val();
		var prate = $('#prate').val();
        fd.append('pimage',files);
		fd.append('pname',pname);
		fd.append('pshort',pshort);
		fd.append('pmrp',pmrp);
		fd.append('punit',punit);
		fd.append('prate',prate);
           progress.fadeIn("slow");
        $.ajax({
            url: 'api/addproduct.php?insert',
            type: 'post',
            data: fd,
            success: function(response){
				if(response=="get_value")
				{
				   alert("Product Short Code Already Exist!");
				   return;
			    }
                if(response =="success"){
                    alert("Product Add Successfully...");
					progress.fadeOut("slow");
					$('#pname').val('');
					$('#pshort').val('');
					$('#punit').val('0');
					$('#pmrp').val('0.0');
					$('#prate').val('0.0');
					$('#pimage').val(''); 
					
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
});

</script>
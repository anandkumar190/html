<?php require("connect.php");?>
<?php $title="Orders"?>
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
              <li class="active"><a href="#today-activity" data-toggle="tab">All Orders</a></li>
              <li><a href="#tear-tree-view" data-toggle="tab">Today Orders</a></li>
              
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
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              
              <!-- Row -->
					<div class="row">
						<div class="col-sm-12">
							<div class="panel panel-default card-view">
								<div class="panel-heading">
									<div class="pull-left">
										<h6 class="panel-title txt-dark">Orders</h6>
									</div>
									<div class="clearfix"></div>
								</div>
								<div class="panel-wrapper collapse in">
                                 <!-- Date and time range -->
              <div class="form-group" style="float:right">
                <label>Select Date Range:</label>

                <div class="input-group">
                  <button type="button" class="btn btn-default pull-right" id="daterange-btn">
                    <span>
                      <i class="fa fa-calendar"></i> Search By date Range
                    </span>
                    <i class="fa fa-caret-down"></i>
                  </button>
                </div>
              </div>
              <!-- /.form group -->
									<div class="panel-body">
										<div class="table-wrap">
											<table id="userstable" class="table" data-paging="true" data-filtering="true" data-processing="true" data-sorting="true">
												<thead>
												<tr>
													<th>Action</th>
													<th>Distributor</th>
													<th>Contact</th>
                                                    <th>Address</th>
                                                    	
                                                    <th>SalesPerson</th>
													<th>Outlet</th>
													<th>Date</th>
                                                    <th>Time</th>
                                                    <th>Status</th>
                                                    <th>Remark</th>
                                                    
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
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              
              <!-- Row -->
					<div class="row">
						<div class="col-sm-12">
							<div class="panel panel-default card-view">
								<div class="panel-heading">
									<div class="pull-left">
										<h6 class="panel-title txt-dark">Today Orders</h6>
									</div>
									<div class="clearfix"></div>
								</div>
								<div class="panel-wrapper collapse in">                            
                                  
                                
									<div class="panel-body">
										<div class="table-wrap">
                                           
											<table id="todaytable" class="table" data-paging="true" data-filtering="true" data-sorting="true">
												<thead>
												<tr>
													<th>Action</th>
													<th>Distributor</th>
													<th>Contact</th>
                                                    <th>Address</th>
                                                    <th>SalesPerson</th>
													<th>Outlet</th>
													
                                                    <th>Date</th>
                                                    <th>Time</th>
                                                    <th>Status</th>
                                                    <th>Remark</th>
                                                    
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
            <!-- table -->    
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
		  url:"api/orderapi.php?show",
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
				  columns:[{
					   data:'id',render:function(value){
						  return "<a href='cancelorder?cancelid="+value+"'><span class='fa fa-remove'></span> Cancel</a>";
						  }},
						  {
							data:{distributorid:'distributorid',distributor:'distributor',orderdate:'orderdate'},render:function(data,type,set){ return "<a href='distributororder?did="+data.distributorid+"&orderdate="+data.orderdate+"'>"+data.distributor+"</a>"; }
						},
						{data:'contact'},{data:'address'},{data:'employee'},{data:'outlet'},{data:'orderdate'},{data:'ordertime'},{data:'status'},{data:'remark'}
					  ]
				
				});		   
			  },
		  error:function(e){
			   alert(e.error);
			  }	  
		 });
   
	}


function loaddatatoday()
	{
     $.ajax({
		  url:"api/orderapi.php?showtoday",
		  type:"POST",
		  contentType:"application/json; charset=utf-8",
		  success:function(data){
			   //alert(data);
			   data=JSON.parse(data);
	            $("#todaytable").dataTable(
				{
			      dom: 'Bfrtip',
				  sort:false,
				  paging:false,
				  data:data,
				  destroy:true,
				  
				 buttons: [
                'copy', { extend: 'csv', title: function () { var printTitle = 'All Visitis'; return printTitle; } }, 'excel', 'pdf', { extend: 'print', title: function () { var printTitle = ''; return printTitle; } }
            ],
				  columns:[{
					   data:'id',render:function(value){
						  return "<a href='cancelorder?cancelid="+value+"'><span class='fa fa-remove'></span> Cancel </a>";
						  }},
						  {
							data:{distributorid:'distributorid',distributor:'distributor',orderdate:'orderdate'},render:function(data,type,set){ return "<a href='distributororder?did="+data.distributorid+"&orderdate="+data.orderdate+"'>"+data.distributor+"</a>"; }
						},
						{data:'contact'},{data:'address'},{data:'employee'},{data:'outlet'},{data:'orderdate'},{data:'ordertime'},{data:'status'},{data:'remark'}
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
   loaddatatoday();
   $("#empdoj").datepicker({format:'yyyy-m-dd'});
   
   $('#daterange-btn').daterangepicker(
      {
        ranges   : {
          'Today'       : [moment(), moment()],
          'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month'  : [moment().startOf('month'), moment().endOf('month')],
          'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate  : moment()
      },
      function (start, end) {
        $('#daterange-btn span').html(start.format('YYYY-MM-DD') + ' : ' + end.format('YYYY-MM-DD'));
		 
		    $.fn.dataTable.ext.search.push(
               function( settings, data, dataIndex ) {
                var date =$('#daterange-btn span').html();
		        
				var dates=date.split(":");
				//var date1=new Date(dates[0]);
			
				//var date2=new Date(dates[1]);
                //var tabledate=new Date(data[7]); 
				var tabledate=data[7]||0;
                //alert(date1.toDateString()+"    "+date2.toDateString());
				//alert((tabledate>date1));
                //if (tabledate>=date1 && tabledate<=date2)
                if((dates[0]==""|| dates[1]=="")||(moment(tabledate).isSameOrAfter(dates[0]) && moment(tabledate).isSameOrBefore(dates[1])))
			    {
                  return true;
                }
                return false;
              }
              );
		 var table = $('#userstable').DataTable();
         table.draw();	
      }
    );

   
   
});


function sendData(){
       
	   //var form=$(this).parent("form");
	       
	 
        var fd = new FormData($("#productform"));
		var progress=$("#progress");
	//	$.each(files, function(key, value){
     //       fd.append(key, value);
      //  });
		
        var files = $('#empimage')[0].files[0];
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
		var emppass = $('#emppass').val();
        fd.append('empimage',files);
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
		fd.append('emppass',emppass);
           progress.fadeIn("slow");
        $.ajax({
            url: 'api/adduser.php?insert',
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
                    alert("User Create Successfully...");
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
		            $('#emppass').val('');
					$('#empcpass').val('');
					
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
<?php require("connect.php");?>
<?php $title="NO ACTIVITY OUTLETS"?>
<?php require("header.php");?>

<?php 
$salesmans=array();
$outlets=array();
 $res=mysqli_query($con,"select * from employees where usertype='1'");
  $booking=mysqli_query($con,"select * from booking");
 
 while($row=mysqli_fetch_array($res)){$salesmans[$row['id']]= $row['name'];}
 
  $res1=mysqli_query($con,"select * from outlets");
 
 while($row1=mysqli_fetch_array($res1)){$outlets[$row1['id']]= $row1['name'];}
 

 
 
?>

<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <?php include("content-header.php");?>
<link rel="stylesheet" href="assets/node_modules/datatables/jquery.dataTables.min.css"/>

<link rel="stylesheet" href="dist/dist/css/bootstrapValidator.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.2.7/css/select.dataTables.min.css">
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
              <li class="active"><a href="#today-activity" data-toggle="tab">Booking Report</a></li>
              
              
            </ul>
            
            <div class="tab-content no-padding">

              
              <!--Today Activity tab Start-->
              
              
              <div class="chart tab-pane active" id="today-activity" style="position: relative; min-height: 300px;">
               <!--table start-->  
                    <!-- /.row -->
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
           <div class="box-header">
              <h3 class="box-title">Select Booking Period
                
              </h3>

            

              <div class="form-inline">
                <form>

                <div class="form-group">
                   <label>Date Range:</label>

                  <div class="input-group">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" autocomplete="off" class="form-control pull-right" id="reservation" name="reservation">
                  </div>
                 <!-- /.input group -->
                </div>

                <br>
                <br>
  
                 <div class="input-group input-group-sm" style="width: 150px;">
                       <label>State</label>
                   <select class="form-control"  name="state" id="state" required>
                   <option value="">Select State</option>
                   <?php 
                        $res=mysqli_query($con,"select  distinct area.state,s.name from area left join states s on s.id=area.state order by s.name ");
                        while($row=mysqli_fetch_array($res))
                      {
                        echo"<option value=".$row[0]." >$row[1]</option>";
                      }
                    ?>
                  </select>
                 </div>

                 <div class="input-group input-group-sm" style="width: 150px;">
                      <label>City</label>
                  <select class="form-control"  name="city" id="city" required>
                  <option value="">Select City</option>
                </select>
                 </div>
                 <div class="input-group input-group-sm" style="width: 150px;">
                      <label>Region</label>
                  <select class="form-control"  name="region" id="region" required>
                  <option value="">Select Region</option>
                </select>
                 </div>

                 <div class="input-group input-group-sm" style="width: 150px;">  
                 <label>Select Route</label>
                  <select class="form-control select2" id="area">
                    <option value="">Select Route</option>
                    
                  </select>
                 </div>

                 <div class="input-group input-group-sm" style="width: 50px ">
          
                 </div>
               
 
                 <div class="input-group input-group-sm" style="width: 150px;">
             
                      <label>Select distributor</label>
                          <select class="form-control select2" id="distributor">
                            <option value="">Select distributor</option>
                          </select>
                 </div>
      
                  <div class="input-group input-group-sm" style="width: 80px;">
                 &nbsp;&nbsp;  <button type="button" id="btnsearch" class="form-control btn btn-default"><i class="fa fa-search"></i> Search</button>
                  </div>

              

                </form>


              </div>
              <br>
              <div class="col-lg-4">
                                          <button type="button" id="deleteselected" class="btn btn-danger"><span class="fa fa-remove"></span> Delete Selected Outlets</button>
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
                      <h6 class="panel-title txt-dark">Outlets</h6>
                                              
                                          <span id="total" class="panel-title txt-dark"></span>
                                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                          <span id="mt" class="panel-title txt-dark"></span>
                                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                          <span id="gt" class="panel-title txt-dark"></span>
                                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                          <span id="mtl" class="panel-title txt-dark"></span>
                                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                          <span id="milkbooth" class="panel-title txt-dark"></span>
                                          
                    </div>
                    		<div class="clearfix"></div>
									</div>
								
								<div class="panel-wrapper collapse in">
									<div class="panel-body">
										<div class="table-wrap">
										    
                    <table id="userstable" class="table" data-paging="true" data-processing="true" data-filtering="true" data-sorting="true">
												<thead>
												<tr>
                          <th>Select</th>
													<th>State</th>
                          <th>City</th>
													<th>Region</th>
													<th>Route</th>
                          <th>Distributor</th>
													<th>Outlet Name</th>										
													<th>Last Visit</th>
                          <th>Last 30 days <br> Orders </th>
                          <th>Past Orders <br> (PerMonth)</th>
                          <th>Contact Person</th>
                          <th>Phone No.</th>
													<th>Outlet Address</th>

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
    <script src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>
    <!-- end - This is for export functionality only -->

<script>
    
function searchdata()
	{
		var state=$("#state").val();
		 
    var reservation = $('#reservation').val();
		var region=$("#region").val();
		var area=$("#area").val();
		// var so=$("#so").val();
		var distributor=$("#distributor").val();
		var routeid='';
		
		$mt=$("#mt");$gt=$("#gt");$mtl=$("#mtl");
		$milkbooth=$("#milkbooth");$total=$("#total");
		var progress=$("#progress");
		 progress.fadeIn("slow");
		 
         $.ajax({
		  url:"api/booking.php?notvist&h&state="+state+"&reservation="+reservation+"&region="+region+"&area="+area+"&distributor="+distributor+"&routeid="+routeid,
		  type:"POST",
		  //&so="+so+
		  contentType:"application/json; charset=utf-8",
		  success:function(data){
			   //alert(data);
			   data=JSON.parse(data);
			   if(data.length==0)
			   {
				   progress.fadeOut("slow");		   
				   alert("No Records Found on Selected Search Pattern...");
				   location.reload();
			   }
	           $mt.html("MTS - "+data[data.length-1].mt);
			   $gt.html("G.T. - "+data[data.length-1].gt);
			   $mtl.html("MTL - "+data[data.length-1].mtl);
			   $milkbooth.html("Milk Booth - "+data[data.length-1].milkbooth);
			   $total.html("Total Outlets - "+data[data.length-1].total);
	           
			    $("#userstable").dataTable(
				{
					columnDefs: [ {
                    orderable: false,
                    className: 'select-checkbox',
                    targets:   0
                    } ],
                    select: {
                    style:    'os',
                    selector: 'td:first-child'
                    },
                   order: [[ 1, 'asc' ]],
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
                        return "<input type='hidden' id='select' value='"+value+"' />";
                        }},
                        {
                        data:'state'
                      }, {
                        data:'city'
                      },
                      {
                        data:'region'
                      },
                      {
                        data:'routename'
                      },
                      {
                        data:'distributor'
                      },
                      {data:'name'},
                      {data:'lastvisit'},
                      {data:'last_30_value'},
                      {data:'past_order_per_month'},
              
                
                      {data:'contactperson'},
                      {data:'contact'},
                      {data:'address'},
              
                  ]
				
				});	
				progress.fadeOut("slow");	   
			  },
		  error:function(e){
			   alert(e.error);
			  }	  
		 });
   
	}

    function state()
	  {
             //alert("Hello");
             $.ajax({
			 url:"api/outlets-web.php?getstate",
			 type:"GET",			 
			 contentType:"application/json; charset=utf-8",
			 success: function(data){
			 data=JSON.parse(data);
			   var state=$("#state");
			   state.empty();
			   var option=$("<option value='' >").html("Select State");
			   state.append(option);
			   $.each(data, function (i, user) {
                        //Create new option
                        option = $('<option value='+user.state+'>').html(user.name);
                        //append city states drop down
                        state.append(option);
                    });
			 }});
	  }

    function city(state)
	  {          
      $.ajax({
			 url:"api/outlets-web.php?getcity&state="+state,
			 type:"GET",			 			 
			 contentType:"application/json; charset=utf-8",
			 success: function(data){
			 //alert(data);
			 data=JSON.parse(data);
			   
			   var state=$("#city");
			   state.empty();
			   var option=$("<option value='' />").html("Select City");
			   state.append(option);
			   $.each(data, function (i, user) {
                        //Create new option
                        option = $('<option value='+user.id+' />').html(user.city);
                        //append city states drop down
                        state.append(option);
                    });
			 }});
	  }

	  
	  function region(city)
	  {          
      $.ajax({
			 url:"api/outlets-web.php?getregion&city="+city,
			 type:"GET",			 			 
			 contentType:"application/json; charset=utf-8",
			 success: function(data){
			 //alert(data);
			 data=JSON.parse(data);
			   
			   var state=$("#region");
			   state.empty();
			   var option=$("<option value='' />").html("Select Region");
			   state.append(option);
			   $.each(data, function (i, user) {
                        //Create new option
                        option = $('<option value='+user.region+' />').html(user.name);
                        //append city states drop down
                        state.append(option);
                    });
			 }});
	  }
	  
	  function area(region)
	  {

      $.ajax({
			 url:"api/outlets-web.php?getrouter&region="+region,
			 type:"GET",			 
			 contentType:"application/json; charset=utf-8",
			 success: function(data){
				 //alert(data);
			 data=JSON.parse(data);
			   
			   var state=$("#area");
			   state.empty();
			   var option=$("<option value='' />").html("Select Area");
			   state.append(option);
			   $.each(data, function (i, user) {
                        //Create new option
                        option = $('<option value='+user.id+'>').html(user.area);
                        //append city states drop down
                        state.append(option);
                    });
			 }});
	  }


    function distributor()
	  {

      $.ajax({
			 url:"api/outlets-web.php?getdistributor",
			 type:"GET",			 
			 contentType:"application/json; charset=utf-8",
			 success: function(data){
				 //alert(data);
			 data=JSON.parse(data);
			   
			   var distributor=$("#distributor");
			   distributor.empty();
			   var option=$("<option value='' />").html("Select distributor");
			   distributor.append(option);
			   $.each(data, function (i, res) {
                        //Create new option
                        option = $('<option value='+res.id+'>').html(res.name);
                        //append city distributors drop down
                        distributor.append(option);
                    });
			 }});
	  }




	  $("#state").change(function(){
		   var state=$("#state option:selected").val();
		   city(state);
		  });
		  
      $("#city").change(function(){
		   var city=$("#city option:selected").val();
    
		   region(city);
		  });


		  $("#region").change(function(){
		   var region=$("#region option:selected").val();
		   area(region);
		  });
		  
      $("#area").change(function(){
		   var areaId=$("#area option:selected").val();
		  
		  });



$(document).ready(function(){
	
	
       $('#reservation').daterangepicker({
		format: 'YYYY-MM-DD',
	    separator: ':',
		opens: 'right'
       });
	
   $("#empdoj").datepicker({format:'yyyy-m-dd'});
   
//   $("#userstable").dataTable(
// 				{
// 				  dom: 'Bfrtip',
// 				  sort:false,
			
// 				  destroy:true,
// 				  paging:true,
// 				  buttons: [
//                 'copy', { extend: 'csv', title: function () { var printTitle = 'All Visitis'; return printTitle; } }, 'excel', 'pdf', { extend: 'print', title: function () { var printTitle = ''; return printTitle; } }
//                   ]
// 				});

$("#btnsearch").click(function(){searchdata();});
          $("#deleteselected").click(function(){

       var ids=Array();
       var table=$("#userstable").DataTable();
       var data = table.rows('.selected').data();      
           //alert("jhgf");
       if(data.length<=0)
       {
         alert("Please Select any Row in table");
         return;
       }
        for (var i=0; i < data.length ;i++)
        {
            ids.push(data[i].id);
          } 
       
       if(!confirm("Are you sure, You want to remove selected Outlets from the Database"))
         {
         return;
         }
       
       var progress=$("#progress");
       progress.fadeIn("slow");
       $.ajax({
           url:'api/outlets-web.php?delete',
           type:'post',
           data:{'ids':ids},
           success: function(data){
               progress.fadeOut("slow");
             alert(data);
                searchdata()
             },
           error:function(e){alert(""+e);}
         });
       
       
       });
  
  
     $("#areaselected").click(function(){
         
       
       var ids=Array();
       var table=$("#userstable").DataTable();
       var data = table.rows('.selected').data();      
           var area=$("#selectarea").val();
       if(data.length<=0)
       {
         alert("Please Select any Row in table");
         return;
       }
       if(area=="")
       {
         alert("Please Select Route");
         return;
       }
       for (var i=0; i < data.length ;i++)
       {
         //alert(data[i].id);
             ids.push(data[i].id);
           } 
       
       if(!confirm("Are you sure, You want to change route of selected Outlets."))
         {
         return;
         }
       
       var progress=$("#progress");
       progress.fadeIn("slow");
       $.ajax({
           url:'api/outlets-web.php?changearea',
           type:'post',
           data:{'ids':ids,'areaid':area},
           success: function(data){
               progress.fadeOut("slow");
             alert(data);
             searchdata()
             },
           error:function(e){alert(""+e);}
         });
       
       
       });

   
});

</script>
<script src="dist/dist/js/bootstrapValidator.min.js"></script>
<script>

$(document).ready(function() {
  //  var reservation = $('#reservation').val();
  //       	var outlet = $('#outlet').val();
  //       	var distibuter = $('#distibuter').val();


});

</script>
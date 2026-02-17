<?php require("connect.php");?>
   <?php $title="Outlets"?>
<?php require("header.php");?>



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
              <li class="active"><a href="#today-activity" data-toggle="tab">Outlets</a></li>
              <!-- <li><a href="#tear-tree-view" data-toggle="tab"> add </a></li> -->
              
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
              <h3 class="box-title">Search Tool
              <small>Searching tool work in linear way. and search state wise SO,Distributor,and Stockist.</small>
              </h3>


              <div class="form-inline">
                <form>
                 <div class="input-group input-group-sm" style="width: 150px;">
                       <label>State</label>
                   <select class="form-control select2 "  name="state" id="state" required>
                   <option value="">Select State</option>
                   <?php 
                        $res=mysqli_query($con,"select  distinct area.state,s.name from area left join states s on s.id=area.state  order by s.name ");
                        while($row=mysqli_fetch_array($res))
                      {
                        echo"<option value=".$row[0]." >$row[1]</option>";
                      }
                    ?>
                  </select>
                 </div>

                 <div class="input-group input-group-sm" style="width: 150px;">
                      <label>City</label>
                  <select class="form-control select2 "  name="city" id="city" required>
                  <option value="">Select City</option>
                </select>
                 </div>
                 <div class="input-group input-group-sm" style="width: 150px;">
                      <label>Region</label>
                  <select class="form-control select2 "  name="region" id="region" required>
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
                                    <div class="pull-left">
                                        <div class="form-inline">
       &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                  <a href="templates/ImportOutlets.csv"  class="btn btn-info"><span class="fa fa-download"></span> Download CSV File</a>
                                        </div>
                                    </div>
                                    
                                   <div class="pull-right">										                                         
                                             
                                              <form action="api/outlets-web.php?import" id="import" method="post" enctype="multipart/form-data" >
                                               <div class="form-group-sm">
                                                 <div class="form-inline controls">
                                                   <label>Choose CSV File</label>
                                                   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                   <input type="file" id="file1" class="form-control" name="file1" required/>
                                                   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                   <button type="submit" id="importoutlet" class="btn btn-info"><span class="fa fa-upload"></span> Import Data</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                 </div>
                                                 
                                                 <div class="progress progress-striped active" id="progress2" style="display:none;">
                                                  <div class="progress-bar progress-bar-success" style="width: 100%">
                                                  </div>
                                                 </div>
                                               </div>
                                             </form>                                         
								      </div>
                                    
									<div class="clearfix"></div>
                                    
                                 </div>
								<div class="panel-wrapper collapse in">
									<div class="panel-body">
										<div class="row">
                                          <div class="col-lg-4">
                                          <button type="button" id="deleteselected" class="btn btn-danger"><span class="fa fa-remove"></span> Delete Selected Outlets</button>
                                          </div>
                                          <div class="col-lg-4">
                                          <select name="selectarea" id="selectarea" class="form-control select2">
                                             <option value="">Select Route</option>
                                             <?php
                                               $res=mysqli_query($con,"select area.id,area.area,regions.name from area join regions on regions.id=area.region  ")or die(mysqli_error($con));                 
                                              while($row=mysqli_fetch_array($res))
                                              {
                                                print_r($row);

                                              echo "<option value='$row[0]'>$row[1] ,$row[2]  </option>"; 
                                              }
                                            ?>
                                           </select>
                                           
                                          </div>
                                          <div class="col-lg-4">
                                            <button type="button" id="areaselected" class="btn btn-info"><span class="fa fa-tasks"></span> Change Route </button>
                                          </div>
                                        </div>
                                      <br>     
                                           
                                                
                                           <br/>
                                           <div class="progress progress-striped active" id="progress" style="display:none;">
                                             <div class="progress-bar progress-bar-success" style="width: 100%"></div>
                                           </div>
                                         <br/>
                                        <div class="table-wrap">
                                        
                                        
											<table id="userstable" class="table" data-paging="true" data-processing="true" data-filtering="true" data-sorting="true">
												<thead>
												<tr>
                          <th>Select</th>
                          <th>Edit</th>
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
              <div class="chart tab-pane" id="tear-tree-view" style="position: relative; min-height: 300px;">
                
                  <section class="content">
                  <div class="row">
                    <form id="productform" action="api/addproduct.php?insert" role="form" method="post" enctype="multipart/form-data">
                    <!-- left column -->
                    <div class="col-md-12">
                      <!-- general form elements -->
                      <div class="box box-primary">
                        <div class="box-header with-border">
                          <h3 class="box-title">Add User</h3>
                        </div>
                        <!-- /.box-header -->
                      <div class="box-body">
                      <div class="row">
                        <div class="col-md-4">
                            <!-- form start -->
                        
                          
                            <div class="form-group controls">
                              <label for="empname">Name : </label>
                              <input type="text" class="form-control" name="empname" id="empname" placeholder="Enter Name"  required="required"/>
                            </div>
                            <div class="form-group">
                              <label for="empcode">Employee Code (EMP0001)</label>
                              <input type="text" class="form-control" name="empcode" id="empcode"  placeholder="Employee Code" required/>
                            </div>
                            <div class="form-group">
                              <label for="empemail">Email </label>
                              <input type="email" class="form-control" name="empemail" id="empemail"  placeholder="Email Address" required/>
                            </div>
                            <div class="form-group">
                              <label for="empcontact">Contact </label>
                              <input type="" class="form-control" pattern="^\d{10}?$" data-dv-message="Enter Contact No in 10 digits" name="empcontact" id="empcontact" minlength="10" maxlength="10" placeholder="Contact No" required/>
                            </div>
                            <div class="form-group">
                              <label for="empaddress">Address </label>
                      <textarea class="form-control" name="empaddress" id="empaddress" placeholder="Address"></textarea>
                            </div>
                        </div> <!-- col 4 close--> 
                        
                        <div class="col-md-4">
                          
                            <div class="form-group">
                              <label for="empdesignation">Select Designation</label>
                              <select  class="form-control select2" name="empdesignation" id="empdesignation" required>
                              <option value="">Select Designation</option>
                                <?php $res=mysqli_query($con,"select * from designation"); while($row=mysqli_fetch_array($res)){?>
                                <option value="<?php echo $row['id'];?>"><?php echo $row['name'];?></option>
                                <?php }?>
                              </select>
                            </div>
                            
                            <div class="form-group">
                              <label for="empdesignation">Select Role</label>
                              <select  class="form-control select2" name="emprole" id="emprole" required>
                              <option value="">Select Role</option>
                                <?php $res=mysqli_query($con,"select * from roles"); while($row=mysqli_fetch_array($res)){?>
                                <option value="<?php echo $row['id'];?>"><?php echo $row['name'];?></option>
                                <?php }?>
                              </select>
                            </div>
                            
                            <div class="form-group">
                              <label for="empmanager">Select Manager</label>
                              <select class="form-control select2" name="empmanager" id="empmanager" required>
                              <option value="">Select Manager</option>
                                <?php $res=mysqli_query($con,"select id,empid,name from employees"); while($row=mysqli_fetch_array($res)){?>
                                <option value="<?php echo $row['id'];?>"><?php echo $row['name']."(".$row['empid'].")";?></option>
                                <?php }?>
                              </select>
                            </div>
                            
                            <div class="form-group">
                              <label for="empmanager">Reports To: </label>
                              <select class="form-control select2" name="empreportto" id="empreportto" required>
                              <option value="">Select</option>
                                <?php $res=mysqli_query($con,"select id,empid,name from employees where designationid=(select id from designation where name='Manager')"); while($row=mysqli_fetch_array($res)){?>
                                <option value="<?php echo $row['id'];?>"><?php echo $row['name']."(".$row['empid'].")";?></option>
                                <?php }?>
                              </select>
                            </div>
                    
                    <div class="form-group">
                            <label>Date of Joining:</label>

                            <div class="input-group date">
                              <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                              </div>
                              <input type="text" class="form-control pull-right" name="empdoj" id="empdoj" required>
                            </div>
                            <!-- /.input group -->
                          </div>
                          
                        </div>
                        <!-- col 4 Mid -->
                        
                        <div class="col-md-4">
                        
                          <div class="form-group">
                              <label for="empsalary">Employee Salary : </label>
                              <input type="text" class="form-control" id="empsalary" placeholder="Employee Salary" value="0.0" pattern="^\d{0,8}(\.\d{0,2})?$" pattern-bv-message="Enter Correct value for Salary" name="empsalary" required/>
                            </div>
                            
                            <div class="form-group">
                              <label for="prate">Comm Percent : </label>
                              <input type="text" class="form-control" id="empcomm" placeholder="EMP Comm" value="0.0" name="empcomm" pattern="^\d{0,8}(\.\d{0,2})?$" data-bv-message="Enter Correct value for Price" required />
                            </div>
                            
                            <div class="form-group">
                              <label for="emppass">Password</label>
                              <input type="password" class="form-control" name="emppass" id="emppass"  placeholder="Password" required/>
                            </div>
                            <div class="form-group">
                              <label for="empcpass">Confirm Password</label>
                              <input type="password" class="form-control" name="empcpass" id="empcpass"  placeholder="Confirm Password" required/>
                            </div>
                            
                            <div class="form-group">
                              <label for="pimage">Select Image</label>
                              <input type="file" id="empimage" name="empimage" required/>
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
                            <button type="submit"  id="btnaddproduct" class="btn btn-primary pull-right "><span class="fa fa-plus-circle"></span> Add New User</button>
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
    <script src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>


<script>

    $(".select2").select2();
	function loaddata()
	{
		$mt=$("#mt");$gt=$("#gt");$mtl=$("#mtl");
		$milkbooth=$("#milkbooth");$total=$("#total");
		 var progress=$("#progress");
		 progress.fadeIn("slow");
		 
     $.ajax({
		  url:"api/outlets-web.php?show",
		  type:"POST",
		  contentType:"application/json; charset=utf-8",
		  success:function(data){
			   //alert(data);
			   data=JSON.parse(data);
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
				  sort:true,
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
                        return "<input type='hidden' id='select' value='"+value+"' /> ";
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
                      {data:'last_30_value'},
                      {data:'past_order_per_month'},
                      {data:'latitude'},
              
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


function loaddataduplicate()
	{
		$mt=$("#mt");$gt=$("#gt");$mtl=$("#mtl");
		$milkbooth=$("#milkbooth");$total=$("#total");
		 var progress=$("#progress");
		 progress.fadeIn("slow");
		 
     $.ajax({
		  url:"api/outlets-web.php?showduplicate",
		  type:"POST",
		  contentType:"application/json; charset=utf-8",
		  success:function(data){
			   //alert(data);
			   data=JSON.parse(data);
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
				  sort:true,
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
					   data:'id',render:function(value){
						  return "<a href='editoutlet?editid="+value+"'><span class='fa fa-edit'></span></a>";
						  }},
						  {
							data:'state'
						}, {
							data:'region'
						},
						 {
							data:'area'
						},
						{
              data:'routename'
						},
						{data:'address'},
						{data:'pincode'},

						 {
							data:'distributor'
						},{
							data:'route'
						},
						{data:'name'},{data:'outlettype'},/*{data:'lastvisitpic',render:function(value){
							return "<a href='imgoutlets/"+value+"' target='_blank'><img src='imgoutlets/"+value+"' class='img img-thumbnail'/></a>";
							}},*/{data:'creationdate'},{data:'lastvisit'},{data:'contactperson'},{data:'contact'},{data:'gstnumber'},{data:'longitude'},{data:'latitude'}	
					  ]
				});
		       progress.fadeOut("slow");  		
			  },
		  error:function(e){
			   alert(e.error);
			  }	  
		 });
   
	}


    function searchdata()
	{
		var state=$("#state").val();
		var city=$("#city").val();
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
		  url:"api/outlets-web.php?search&state="+state+"&city="+city+"&region="+region+"&area="+area+"&distributor="+distributor+"&routeid="+routeid,
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
				   //return;
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
                        }},{
                          data:'id',render:function(value){
                          return "<a href='editoutlet?editid="+value+"'><span class='fa fa-edit'></span></a>";
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
   //loaddata();
   distributor();
   $("#empdoj").datepicker({format:'yyyy-m-dd'});
   $("#btnsearch").click(function(){searchdata();});
   $("#btnduplicate").click(function(){loaddataduplicate();});
   $("#btnalloutlets").click(function(){loaddata();});   
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

     
      // $("#distributerselected").click(function(){
          
        
      //     var ids=Array();
      //     var table=$("#userstable").DataTable();
      //     var data = table.rows('.selected').data();      
      //         var distributeid=$("#select_distributer").val();
      //     if(data.length<=0)
      //     {
      //       alert("Please Select any Row in table");
      //       return;
      //     }
      //     if(area=="")
      //     {
      //       alert("Please Select distribute");
      //       return;
      //     }
      //     for (var i=0; i < data.length ;i++)
      //     {
      //       //alert(data[i].id);
      //           ids.push(data[i].id);
      //         } 
          
      //     if(!confirm("Are you sure, You want to change distribute of selected Outlets."))
      //       {
      //       return;
      //       }
          
      //     var progress=$("#progress");
      //     progress.fadeIn("slow");
      //     $.ajax({
      //         url:'api/outlets-web.php?changedistributeid',
      //         type:'post',
      //         data:{'ids':ids,'distributeid':distributeid},
      //         success: function(data){
      //             progress.fadeOut("slow");
      //           alert(data);
      //           loaddata();
      //           },
      //         error:function(e){alert(""+e);}
      //       });
          
          
      //     });


           
  //  $("#routeselected").click(function(){	 
  //      var ids=Array();
  //      var table=$("#userstable").DataTable();
  //      var data = table.rows('.selected').data();      
  //          var routeid=$("#select_route").val();
  //      if(data.length<=0)
  //      {
  //        alert("Please Select any Row in table");
  //        return;
  //      }
  //      if(area=="")
  //      {
  //        alert("Please Select route");
  //        return;
  //      }
  //      for (var i=0; i < data.length ;i++)
  //      {
  //        //alert(data[i].id);
  //            ids.push(data[i].id);
  //          } 
       
  //      if(!confirm("Are you sure, You want to change route of selected Outlets."))
  //        {
  //        return;
  //        }
       
  //      var progress=$("#progress");
  //      progress.fadeIn("slow");
  //      $.ajax({
  //          url:'api/outlets-web.php?changerouteid',
  //          type:'post',
  //          data:{'ids':ids,'routeid':routeid},
  //          success: function(data){
  //              progress.fadeOut("slow");
  //            alert(data);
  //            loaddata();
  //            },
  //          error:function(e){alert(""+e);}
  //        });
       
       
  //      });
   
  });






function sendData(){
       
	   //var form=$(this).parent("form");
	       
	    var fd = new FormData($("#productform"));
		var progress=$("#progress");
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
	$(".select2").select();
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
                    
            });
		
		
		function importData(){
       
	    var fd = new FormData();
		var progress=$("#progress2");	
        var files = $('#file1')[0].files[0];		
		fd.append('file1',files);
           progress.fadeIn("slow");
        $.ajax({
            url: 'api/outlets-web.php?import',
            type: 'post',
            data: fd,
            success: function(response){
				alert(response);
				if(response=="error-type")
				{
				   alert("Please import CSV files only");
				   return;
			    }
				if(response=="error")
				{
				   alert("Contact to Admin Intrernal error");
				   return;
			    }
								
                if(response =="success"){
                    alert("Outlets Import Successfully...");
					loaddata();
					$("#importoutlet").removeAttr("disabled");
					progress.fadeOut("slow");					
					$('#file1')[0].files[0];
		            
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
		
    $('#import')
        .bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                
			  file1: {
                    validators: {
                    notEmpty: {
                        message: 'Please Select excel File '
                      },
                    file: {
                        extension: 'csv',
						//type:'text/csv,application/csv,application/x-csv',
                        maxSize:1024*1024*2,						
                        message: 'The password and its confirm are not the same'
                      }
                    }
                  }
			
                }
        }) .on('success.form.bv', function(e) {
                // Prevent form submission
                e.preventDefault();
                importData();
             
            });
});

</script>


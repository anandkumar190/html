<?php require("connect.php");?>
<?php $title="Dashboard";?>
<?php require("header.php");
?>

<?php
session_start();
if (empty($_SESSION['tittu'])) {
    header("Location: /login.php?expire=1");
    exit;
}
?>

<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <?php require("content-header.php");?>
    <?php $res=mysqli_query($con,"select count(id) from outlets")or die(mysqli_error($con));
	    $outlets=0;
		if($row=mysqli_fetch_array($res))
		{
			$outlets=$row[0];
		}
		
	?>
    
    <?php $res=mysqli_query($con," select count(distinct st.name )from area a join states st on a.state=st.id  order by st.name  ")or die(mysqli_error($con));
	    $states=0;
		if($row=mysqli_fetch_array($res))
		{
			$states=$row[0];
		}
		
	?>
    <?php $res=mysqli_query($con,"select count(distinct id) from cities")or die(mysqli_error($con));
	    $cities=0;
		if($row=mysqli_fetch_array($res))
		{
			$cities=$row[0];
		}
		
	?>    
    
    <?php $res=mysqli_query($con,"select count(id) from employees where usertype=2")or die(mysqli_error($con));
	    $stockists=0;
		if($row=mysqli_fetch_array($res))
		{
			$stockists=$row[0];
		}
		
	?>
    <?php $res=mysqli_query($con,"select count(id) from employees where usertype=3")or die(mysqli_error($con));
	    $distributors=0;
		if($row=mysqli_fetch_array($res))
		{
			$distributors=$row[0];
		}
		
	?>
    <?php $res=mysqli_query($con,"select count(id) from employees where usertype=1")or die(mysqli_error($con));
	    $employees=0;
		if($row=mysqli_fetch_array($res))
		{
			$employees=$row[0];
		}
		
	?>
    
    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-lg-2 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3><?php echo $outlets;?></h3>

              <p>Total Outlets</p>
            </div>
            <div class="icon">
              <i class="ion ion-bag"></i>
            </div>
            <a href="outlets" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-2 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h3><?php echo $states;?></h3>

              <p>No Of States</p>
            </div>
            <div class="icon">
              <i class="ion ion-android-done-all"></i>
            </div>
            <a href="outlets" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        
        <!-- ./col -->
        <div class="col-lg-2 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua-gradient">
            <div class="inner">
              <h3><?php echo $cities;?></h3>

              <p>No Of Cities</p>
            </div>
            <div class="icon">
              <i class="ion ion-android-done-all"></i>
            </div>
            <a href="outlets" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        


        <!-- ./col -->
        <div class="col-lg-2 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-red">
            <div class="inner">
              <h3><?php echo $distributors;?></h3>

              <p>Distributors</p>
            </div>
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
            <a href="distributors" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->


        <!-- ./col -->
        
        <div class="col-lg-2 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-olive-active">
            <div class="inner">
              <h3><?php echo $employees;?></h3>

              <p>No Of Sales Team</p>
            </div>
            <div class="icon">
              <i class="ion ion-person-add"></i>
            </div>
            <a href="users" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
                <!-- ./col -->
       
        
      </div>
      <!-- /.row -->
      <!-- Main row -->
      <div class="row">
        <!-- Left col -->
        <section class="col-lg-12 connectedSortable">
          <!-- Custom tabs (Charts with tabs)-->
          <div class="nav-tabs-custom">
            <!-- Tabs within a box -->
            <ul class="nav nav-tabs pull-left">
              <li class="active"><a href="#today-activity" data-toggle="tab">Map View</a></li>
         
              
            </ul>
            
            <div class="tab-content no-padding">
              <!--Today Activity tab Start-->
              <div class="chart tab-pane active" id="today-activity" style="position: relative; min-height: 300px;">
                 
                 <iframe src="dashboard-map.php" style="border:none; width:100%;" height="900px" ></iframe>	
                 
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
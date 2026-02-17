<?php
    include("connect.php"); 
	$time=date("H:i:s"); 
    $datetime = date("Y-m-d H:i:s");
    $date=date("Y-m-d"); 
    if(isset($_REQUEST['orderdate']))
	{
	    extract($_REQUEST);
		$orderids=array();
	$res=mysqli_query($con,"select * from outletorder where distributorid='$did' and orderdate='$orderdate' order by orderid asc");
	while($row=mysqli_fetch_array($res))
		{
		  array_push($orderids,$row["orderid"]);
		}
	}
	else
	{
		exit();
		return;
	}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body onload="">
<div class="wrapper">
  <!-- Main content -->
  <section class="invoice">
    <!-- title row -->
    <div class="row">
      <div class="col-xs-12">
        <h2 class="page-header">
          <i class="fa fa-globe"></i> FRIC BERGEN, Inc.
          
          <button onClick="window.print();" style="margin-left:300px;	" class=" btn btn-default"><span class="fa fa-print"></span> Print </button>
          <small class="pull-right">Date: <?php echo $date;?></small>
        </h2>
      </div>
      <!-- /.col -->
    </div>
    <?php 
	   foreach($orderids as $orderid)
	   {
		 $distname="";
		 $distcontact="";
		 $distaddress="";
		 $empname="";
		 $empcontact="";
		 $outletname="";
		 $outletcontact="";
		 $outletaddress="";
		 $orderdate="";
		 $ordertime="";
         $remark="";
		 		
		$res=mysqli_query($con,"select o.orderid,o.orderdate,o.ordertime,o.orderstatus,o.remark,o.distributorid,d.name as 'distributor',d.contact,d.address,e.name as 'employee',e.contact as 'empcontact',ot.name as 'outlet',ot.contact as 'outletcontact', ot.address as 'outletaddress' from outletorder o join employees d on o.distributorid=d.id join employees e on e.id=o.empid join outlets ot on ot.id=o.outletid  where o.orderid='$orderid'");	
		while($row=mysqli_fetch_array($res))
		{
		  
		  $distname=$row["distributor"];
		  $distcontact=$row["contact"]; 
		  $distaddress=$row["address"];
		  $empname=$row["employee"];
		  $empcontact=$row["empcontact"];
		  $outletname=$row["outlet"];
		  $outletcontact=$row["outletcontact"];
		  $outletaddress=$row["outletaddress"];
		  $orderdate=$row["orderdate"];
		  $ordertime=$row["ordertime"];
		   $remark=$row["remark"];      	
		}

	?>
    <!-- info row -->
    <div class="row invoice-info">
      <div class="col-sm-4 invoice-col">
        <strong>Distributor</strong>
        <address>
          <strong> <?php echo $distname;?> </strong><br>
          Address: <?php echo $distaddress;?><br>
          
          Phone: <?php echo $distcontact;?><br>
          
        </address>
      </div>
      <!-- /.col -->
      <div class="col-sm-4 invoice-col">
        <strong>Sales Officer</strong>
        <address>
          <strong><?php echo $empname; ?></strong><br>
          Phone: <?php echo $empcontact;?><br>
             
        </address>
      </div>
      <!-- /.col -->
      <div class="col-sm-4 invoice-col">
        <b>OrderId #<?php echo $orderid;?></b><br>
        <br>
        <b>Outlet Name :</b> <?php echo $outletname;?><br>
        <b>Address:</b> <?php echo $outletaddress;?><br>
        <b>Phone :</b> <?php echo $outletcontact;?>
        <b>Order Date :</b> <?php echo $orderdate;?>
        <b>Order Time :</b> <?php echo $ordertime;?>
      </div>
      <div class="col-sm-12 ">
            <b>Order Remark :</b> <?php echo $remark;?><br>    
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->

    <!-- Table row -->
    <div class="row">
      <div class="col-xs-12 table-responsive">
        <table class="table table-striped">
          <thead>
          <tr>
            <th>ProductName</th>
            <th>ShortName</th>
            <th>Qty</th>
            <th>Image</th>
            
          </tr>
          </thead>
          <tbody>
          <?php
		    $totalqty=0; 
		     $res1=mysqli_query($con,"select o.qty,s.productname,s.productid,s.image from orderitem o join skus s on o.productid=s.id where o.orderid='$orderid'");
			 while($row=mysqli_fetch_array($res1))
			 {
				 $totalqty+=$row["qty"];
		  ?>
          <tr>
            <td><?php echo $row["productname"]; ?></td>
            <td><?php echo $row["productid"]; ?></td>
            <td><?php echo $row["qty"]; ?></td>
            <td><img style="width:120px;height:120px;" src="imgproduct/<?php echo $row['image'];?>"/></td>
          </tr>
          <?php } ?>
          </tbody>
          <tfoot>
           <th colspan="2">
           Total Qty</th>  
           <th colspan="2">
             <?php echo $totalqty; ?>
           </th>         
          </tfoot>
        </table>
      </div>
      <!-- /.col -->
    </div>
    <hr/>
    <!-- /.row -->
    <?php } ?>

    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
<!-- ./wrapper -->
</body>
</html>

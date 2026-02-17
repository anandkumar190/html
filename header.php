<?php

   session_start();
   ob_start();
    // die($_SESSION);
//   if(!isset($_SESSION['tittu']))
//   {
       
//         // die($_SESSION);
// 	   header("location:login?expire");
// 	   echo"<script> window.location='login?expire';</script>";
// 	   exit();
//   }
               $empemail=$_SESSION['tittu'];
			   $empname=$_SESSION['empname'];
			   $empid=$_SESSION['empid'];
			   $id=$_SESSION['id'];
			   $empimage=$_SESSION['image'];
?>
<!DOCTYPE html>
<html>
  <head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Cart Route | Technology are working together</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="icon" href="images/shopping_cart.png"/>
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <!-- daterange picker -->
  <link rel="stylesheet" href="bower_components/bootstrap-daterangepicker/daterangepicker.css">
  <!-- bootstrap datepicker -->
  <link rel="stylesheet" href="bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="plugins/iCheck/all.css">
  
  <!-- Bootstrap time Picker -->
  <link rel="stylesheet" href="plugins/timepicker/bootstrap-timepicker.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="bower_components/select2/dist/css/select2.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css"/>
  <link rel="stylesheet" href="bower_components/datatable-button.css"/>

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head> 
<body class="hold-transition skin-green-light sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
    <a href="home" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>C</b>RT</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>CART</b> ROUTE</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- Messages: style can be found in dropdown.less-->
          
          <!-- Notifications: style can be found in dropdown.less -->
          
          <!-- Tasks: style can be found in dropdown.less -->
          
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="imgusers/<?php echo $empimage;?>"	 class="user-image" alt="User Image">
              <span class="hidden-xs"><?php echo $empname;?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="imgusers/<?php echo $empimage;?>" class="img-circle" alt="User Image">

                <p>
                  <?php echo mb_convert_case($empname,MB_CASE_TITLE,'UTF-8');?> - <?php echo strtoupper($empid);?>
                  <small><?php echo strtolower($empemail);?></small>
                </p>
              </li>
              <!-- Menu Body -->
              
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="#" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="signout" class="btn btn-default btn-flat"><span class="fa fa-sign-out"></span> Sign Out</a>
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
          <li>
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="imgusers/<?php echo $empimage;?>" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p><?php echo $empname;?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      
      
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MAIN NAVIGATION</li>
        <li class="active ">
          <a href="home">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
            
          </a>
          
        </li>
        


        <li class="treeview">
          <a href="#">
            <i class="fa fa-th"></i> <span> Product Masters</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="product-cat"><i class="fa fa-circle-o"></i>Product Category</a></li>
            <li><a href="sub-product-cat"><i class="fa fa-circle-o"></i>Sub Product Category </a></li>
            <li><a href="sku_unit"><i class="fa fa-circle-o"></i>Sku Unit</a></li>
            <li><a href="skus"><i class="fa fa-circle-o"></i> SKUs</a></li>
            
          </ul>
        </li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-th"></i> <span> Area Masters</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="state"><i class="fa fa-circle-o"></i> State</a></li>
            <li><a href="city"><i class="fa fa-circle-o"></i> City</a></li>
            <li><a href="region"><i class="fa fa-circle-o"></i> Region</a></li>
            <li><a href="showarea"><i class="fa fa-circle-o"></i> Route</a></li>
            <!-- <li><a href="showroute"><i class="fa fa-circle-o"></i> old Route Mgmt</a></li> -->
          </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-th"></i> <span> User Masters</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <!-- <li><a href="#"><i class="fa fa-circle-o"></i> Designation</a></li>
            <li><a href="#"><i class="fa fa-circle-o"></i> Roles</a></li> -->
          </ul>
        </li>

        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-opencart"></i>
            <span>Manage Orders</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <!--<li><a href="orders"><i class="fa fa-circle-o"></i> Orders</a></li>-->
            <li><a href="booking2"><i class="fa fa-circle-o"></i> Booking</a></li>
            <li><a href="not_activity_outlets"><i class="fa fa-circle-o"></i> No Activity Outlets </a></li>
            
            <!--<li><a href="cancelorders"><i class="fa fa-circle-o"></i> Cancel Orders</a></li>-->
                  
          </ul>
        </li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-laptop"></i>
            <span>Manage TEAM</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="team-structure"><i class="fa fa-circle-o"></i> Team Structure</a></li>
            <li><a href="users"><i class="fa fa-circle-o"></i> Users</a></li>
            <li><a href="activityvisit"><i class="fa fa-circle-o"></i> Activities</a></li>
            <!-- <li><a href="daily-report"><i class="fa fa-circle-o"></i>Team's Daily Report</a></li> -->
            
            <li><a href="monthly-attendance"><i class="fa fa-circle-o"></i>Team's Monthly Report</a></li>
            <li><a href="monthly-attendance-ds"><i class="fa fa-circle-o"></i>Team's Monthly Report  DS </a></li>

            
          </ul>
        </li>
        <!-- <li class="treeview">
          <a href="#">
            <i class="fa fa-edit"></i> <span>Super Stockist</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="stockist"><i class="fa fa-circle-o"></i> Report</a></li>
           
          </ul>
        </li> -->
        <li class="treeview">
          <a href="#">
            <i class="fa fa-table"></i> <span>Distibutors</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="distributors"><i class="fa fa-circle-o"></i> Report </a></li>
            <!-- <li><a href="#"><i class="fa fa-circle-o"></i> Stock</a></li>
            <li><a href="#"><i class="fa fa-circle-o"></i> Rate List</a></li> -->
          </ul>
        </li>
        
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-folder"></i> <span>Outlets</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="outlets"><i class="fa fa-circle-o"></i> Report </a></li>
            
          </ul>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-bell"></i> <span>Send Notifications</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="shownoti"><i class="fa fa-circle-o"></i> Show Notifications</a></li>
            <li><a href="teamnoti"><i class="fa fa-circle-o"></i> Team</a></li>
            <li><a href="#"><i class="fa fa-circle-o"></i> Retailers</a></li>
             <li><a href="#"><i class="fa fa-circle-o"></i> Distibutors</a></li>
              <!-- <li><a href="#"><i class="fa fa-circle-o"></i> Super Stockist</a></li> -->
          </ul>
        </li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-connectdevelop"></i> <span>Connected Devices</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">  
            <li><a href="devices"><i class="fa fa-circle-o"></i> Show Devices</a></li>
          </ul>
        </li>
        
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>



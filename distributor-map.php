<?php require("connect.php");?>
<?php $title = "Distributor Map";?>
<?php require("header.php");?>



<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <?php require("content-header.php");?>

  <!-- Main content -->
  <section class="content">
    <!-- Main row -->
    <div class="row" id="map-section">
      <section class="col-lg-12 connectedSortable">
        <div class="nav-tabs-custom">
          <ul class="nav nav-tabs pull-left">
            <li class="active"><a href="#distributor-activity" data-toggle="tab"><i class="fa fa-map-marker"></i> Map View</a></li>
            <li><a href="distributors"><i class="fa fa-list"></i> Table Report</a></li>
          </ul>

          <div class="tab-content no-padding">
            <div class="chart tab-pane active" id="distributor-activity" style="position: relative; min-height: 300px;">
              <iframe src="distributorMap.php" style="border:none; width:100%;" height="900px"></iframe>
            </div>
          </div>
        </div>
      </section>
    </div>
    <!-- /.row (main row) -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php include("footer.php");?>
<?php include("jsscript.php"); ?>

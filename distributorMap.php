<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Distributors Map View</title>
    <style>
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        font-family: 'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif;
      }
      .filter-container {
        padding: 10px 15px 5px 15px;
        background: #fdfdfd;
        border-bottom: 1px solid #e1e4e8;
      }
      #map {
        width: 100%;
        height: calc(100vh - 130px);
        min-height: 500px;
      }
      .stat-badge {
        display: inline-block;
        padding: 4px 10px;
        margin-right: 8px;
        margin-bottom: 6px;
        border-radius: 3px;
        font-size: 13px;
        font-weight: 600;
      }
      .badge-total { background-color: #00c0ef; color: #fff; }
      .badge-mapped { background-color: #00a65a; color: #fff; }
      .badge-states { background-color: #f39c12; color: #fff; }
      .badge-cities { background-color: #3c8dbc; color: #fff; }
      .info-window-card {
        padding: 5px;
        max-width: 280px;
        font-size: 13px;
        line-height: 1.4;
      }
      .info-window-card h4 {
        margin: 0 0 5px 0;
        color: #00a65a;
        font-size: 15px;
        font-weight: bold;
      }
      .info-window-card p {
        margin: 3px 0;
      }
      .info-window-card .meta-label {
        font-weight: 600;
        color: #555;
      }
      .info-badge {
        display: inline-block;
        padding: 2px 6px;
        font-size: 11px;
        border-radius: 3px;
        background: #e8f5e9;
        color: #2e7d32;
        margin-top: 4px;
      }
    </style>
    <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
    <link rel="stylesheet" href="bower_components/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  </head>
  <body>
    <div class="filter-container">
      <div class="row">
        <div class="col-md-3 col-sm-6">
          <div class="form-group">
            <label><i class="fa fa-map"></i> Select State</label>
            <select class="form-control select2" id="state" style="width: 100%;">
              <option value="">All States</option>
            </select>
          </div>
        </div>

        <div class="col-md-2 col-sm-6">
          <div class="form-group">
            <label><i class="fa fa-building"></i> Select City</label>
            <select class="form-control select2" id="city" style="width: 100%;">
              <option value="">All Cities</option>
            </select>
          </div>
        </div>

        <div class="col-md-2 col-sm-6">
          <div class="form-group">
            <label><i class="fa fa-map-signs"></i> Select Region</label>
            <select class="form-control select2" id="region" style="width: 100%;">
              <option value="">All Regions</option>
            </select>
          </div>
        </div>

        <div class="col-md-3 col-sm-6">
          <div class="form-group">
            <label><i class="fa fa-truck"></i> Select Distributor</label>
            <select class="form-control select2" id="distributor" style="width: 100%;">
              <option value="">All Distributors</option>
            </select>
          </div>
        </div>

        <div class="col-md-2 col-sm-12">
          <div class="form-group">
            <label>&nbsp;</label><br/>
            <button type="button" class="btn btn-success btn-flat" id="search">
              <i class="fa fa-search"></i> Search
            </button>
            <button type="button" class="btn btn-default btn-flat" id="reset" title="Reset Filters">
              <i class="fa fa-refresh"></i>
            </button>
            <img src="images/spinner.gif" alt="Loading..." id="loader" width="30px" height="30px" style="display:none; margin-left: 5px;"/>
          </div>
        </div>
      </div>

      <div class="row" style="margin-top: 5px;">
        <div class="col-sm-12">
          <span class="stat-badge badge-total" id="total"><i class="fa fa-users"></i> Total: 0</span>
          <span class="stat-badge badge-mapped" id="mapped"><i class="fa fa-map-marker"></i> Mapped: 0</span>
          <span class="stat-badge badge-states" id="states_count"><i class="fa fa-map"></i> States: 0</span>
          <span class="stat-badge badge-cities" id="cities_count"><i class="fa fa-building-o"></i> Cities: 0</span>
        </div>
      </div>
    </div>

    <div id="map"></div>

    <script src="bower_components/jquery/dist/jquery.min.js"></script>
    <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="bower_components/select2/dist/js/select2.full.min.js"></script>

    <script>
      var map;
      var markers = [];
      var activeInfoWindow = null;

      function initGoogleMap() {
        var defaultCenter = { lat: 20.5937, lng: 78.9629 };
        map = new google.maps.Map(document.getElementById('map'), {
          zoom: 5,
          center: defaultCenter,
          mapTypeId: google.maps.MapTypeId.ROADMAP,
          mapTypeControl: true,
          streetViewControl: false
        });

        loadDistributorsOnMap();
      }

      function clearMarkers() {
        for (var i = 0; i < markers.length; i++) {
          markers[i].setMap(null);
        }
        markers = [];
      }

      function loadDistributorsOnMap() {
        $("#loader").show();
        var state = $("#state").val() || '';
        var city = $("#city").val() || '';
        var region = $("#region").val() || '';
        var distributor = $("#distributor").val() || '';

        $.ajax({
          url: 'api/distributor-api.php?showmap=1&state=' + encodeURIComponent(state) + '&city=' + encodeURIComponent(city) + '&region=' + encodeURIComponent(region) + '&distributor=' + encodeURIComponent(distributor),
          type: 'GET',
          dataType: 'json',
          success: function(data) {
            $("#loader").hide();
            clearMarkers();

            if (!data || data.length === 0) {
              $("#total").html('<i class="fa fa-users"></i> Total: 0');
              $("#mapped").html('<i class="fa fa-map-marker"></i> Mapped: 0');
              return;
            }

            var summary = data[data.length - 1];
            var distributorList = data.slice(0, data.length - 1);

            $("#total").html('<i class="fa fa-users"></i> Total: ' + (summary.total || distributorList.length));
            $("#mapped").html('<i class="fa fa-map-marker"></i> Mapped: ' + (summary.mapped || 0));
            $("#states_count").html('<i class="fa fa-map"></i> States: ' + (summary.states_count || 0));
            $("#cities_count").html('<i class="fa fa-building-o"></i> Cities: ' + (summary.cities_count || 0));

            var bounds = new google.maps.LatLngBounds();
            var validCoordinatesCount = 0;

            for (var i = 0; i < distributorList.length; i++) {
              var item = distributorList[i];
              var lat = parseFloat(item.latitude);
              var lng = parseFloat(item.longitude);

              if (isNaN(lat) || isNaN(lng) || (lat === 0 && lng === 0)) {
                continue;
              }

              validCoordinatesCount++;
              var latLng = new google.maps.LatLng(lat, lng);
              bounds.extend(latLng);

              var marker = new google.maps.Marker({
                position: latLng,
                map: map,
                title: item.name,
                animation: google.maps.Animation.DROP,
                icon: {
                  url: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png'
                }
              });

              (function(m, d) {
                var contentString = '<div class="info-window-card">' +
                  '<h4><i class="fa fa-truck"></i> ' + (d.name || 'Distributor') + '</h4>' +
                  (d.empid ? '<p><span class="meta-label">Code:</span> ' + d.empid + '</p>' : '') +
                  (d.contactperson ? '<p><span class="meta-label">Contact Person:</span> ' + d.contactperson + '</p>' : '') +
                  (d.contact ? '<p><span class="meta-label">Phone:</span> <a href="tel:' + d.contact + '">' + d.contact + '</a></p>' : '') +
                  (d.email ? '<p><span class="meta-label">Email:</span> <a href="mailto:' + d.email + '">' + d.email + '</a></p>' : '') +
                  (d.city || d.state ? '<p><span class="meta-label">Location:</span> ' + (d.city ? d.city + ', ' : '') + (d.state || '') + '</p>' : '') +
                  (d.address ? '<p><span class="meta-label">Address:</span> ' + d.address + '</p>' : '') +
                  '<div class="info-badge"><i class="fa fa-shopping-cart"></i> Outlets: ' + (d.no_of_outlets || 0) + ' | Routes: ' + (d.no_of_routes || 0) + '</div>' +
                  '</div>';

                var infowindow = new google.maps.InfoWindow({
                  content: contentString
                });

                m.addListener('click', function() {
                  if (activeInfoWindow) {
                    activeInfoWindow.close();
                  }
                  infowindow.open(map, m);
                  activeInfoWindow = infowindow;
                });
              })(marker, item);

              markers.push(marker);
            }

            if (validCoordinatesCount > 0) {
              if (validCoordinatesCount === 1) {
                map.setCenter(bounds.getCenter());
                map.setZoom(12);
              } else {
                map.fitBounds(bounds);
              }
            }
          },
          error: function(err) {
            $("#loader").hide();
            console.error("Error loading distributors map data:", err);
          }
        });
      }

      function loadStates() {
        $.ajax({
          url: "api/distributor-api.php?getstate=1",
          type: "GET",
          dataType: "json",
          success: function(data) {
            var $state = $("#state");
            $state.empty().append('<option value="">All States</option>');
            $.each(data, function(i, item) {
              $state.append($('<option>', { value: item.state, text: item.name }));
            });
          }
        });
      }

      function loadCities(stateId) {
        $.ajax({
          url: "api/distributor-api.php?getcity=1&state=" + encodeURIComponent(stateId || ''),
          type: "GET",
          dataType: "json",
          success: function(data) {
            var $city = $("#city");
            $city.empty().append('<option value="">All Cities</option>');
            $.each(data, function(i, item) {
              $city.append($('<option>', { value: item.id, text: item.city }));
            });
          }
        });
      }

      function loadRegions(cityId) {
        $.ajax({
          url: "api/distributor-api.php?getregion=1&city=" + encodeURIComponent(cityId || ''),
          type: "GET",
          dataType: "json",
          success: function(data) {
            var $region = $("#region");
            $region.empty().append('<option value="">All Regions</option>');
            $.each(data, function(i, item) {
              $region.append($('<option>', { value: item.region, text: item.name }));
            });
          }
        });
      }

      function loadDistributorsList() {
        $.ajax({
          url: "api/distributor-api.php?getdistributor=1",
          type: "GET",
          dataType: "json",
          success: function(data) {
            var $distributor = $("#distributor");
            $distributor.empty().append('<option value="">All Distributors</option>');
            $.each(data, function(i, item) {
              $distributor.append($('<option>', { value: item.id, text: item.name + (item.empid ? ' (' + item.empid + ')' : '') }));
            });
          }
        });
      }

      $(document).ready(function() {
        $('.select2').select2();

        loadStates();
        loadCities('');
        loadRegions('');
        loadDistributorsList();

        $("#state").on('change', function() {
          var stateId = $(this).val();
          loadCities(stateId);
          loadRegions('');
        });

        $("#city").on('change', function() {
          var cityId = $(this).val();
          loadRegions(cityId);
        });

        $("#search").on('click', function() {
          loadDistributorsOnMap();
        });

        $("#reset").on('click', function() {
          $("#state").val('').trigger('change');
          $("#city").val('').trigger('change');
          $("#region").val('').trigger('change');
          $("#distributor").val('').trigger('change');
          loadDistributorsOnMap();
        });
      });
    </script>

    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCrxsk0fDpJlEqqLXqrdrg833McDrv5apc&callback=initGoogleMap"></script>
  </body>
</html>

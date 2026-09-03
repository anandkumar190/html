<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Outlets Map View</title>
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
      .badge-gt { background-color: #00a65a; color: #fff; }
      .badge-mt { background-color: #f39c12; color: #fff; }
      .badge-mtl { background-color: #3c8dbc; color: #fff; }
      .badge-milkbooth { background-color: #dd4b39; color: #fff; }
      .badge-wholesaler { background-color: #605ca8; color: #fff; }
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
        <div class="col-md-2 col-sm-6">
          <div class="form-group">
            <label><i class="fa fa-map"></i> State</label>
            <select class="form-control select2" id="state" style="width: 100%;">
              <option value="">All States</option>
            </select>
          </div>
        </div>

        <div class="col-md-2 col-sm-6">
          <div class="form-group">
            <label><i class="fa fa-building"></i> City</label>
            <select class="form-control select2" id="city" style="width: 100%;">
              <option value="">All Cities</option>
            </select>
          </div>
        </div>

        <div class="col-md-2 col-sm-6">
          <div class="form-group">
            <label><i class="fa fa-map-signs"></i> Region</label>
            <select class="form-control select2" id="region" style="width: 100%;">
              <option value="">All Regions</option>
            </select>
          </div>
        </div>

        <div class="col-md-2 col-sm-6">
          <div class="form-group">
            <label><i class="fa fa-road"></i> Route / Area</label>
            <select class="form-control select2" id="area" style="width: 100%;">
              <option value="">All Routes</option>
            </select>
          </div>
        </div>

        <div class="col-md-2 col-sm-6">
          <div class="form-group">
            <label><i class="fa fa-truck"></i> Distributor</label>
            <select class="form-control select2" id="distributor" style="width: 100%;">
              <option value="">All Distributors</option>
            </select>
          </div>
        </div>

        <div class="col-md-2 col-sm-6">
          <div class="form-group">
            <label>&nbsp;</label><br/>
            <button type="button" class="btn btn-success btn-flat" id="search">
              <i class="fa fa-search"></i> Search
            </button>
            <button type="button" class="btn btn-default btn-flat" id="reset" title="Reset Filters">
              <i class="fa fa-refresh"></i>
            </button>
            <img src="images/spinner.gif" alt="Loading..." id="loader" width="28px" height="28px" style="display:none; margin-left: 5px; vertical-align: middle;"/>
          </div>
        </div>
      </div>

      <div class="row" style="margin-top: 5px;">
        <div class="col-sm-12">
          <span class="stat-badge badge-total" id="total"><i class="fa fa-shopping-cart"></i> Total Outlets: 0</span>
          <span class="stat-badge badge-gt" id="gt">G.T.: 0</span>
          <span class="stat-badge badge-mt" id="mt">MTS: 0</span>
          <span class="stat-badge badge-mtl" id="mtl">MTL: 0</span>
          <span class="stat-badge badge-milkbooth" id="milkbooth">Milk Booth: 0</span>
          <span class="stat-badge badge-wholesaler" id="wholesaler">Wholesaler: 0</span>
        </div>
      </div>
      <div id="map-notice" style="display:none; padding: 4px 10px; margin-top: 5px; background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: 3px; font-size: 12px;">
        <i class="fa fa-info-circle"></i> <span id="map-notice-text"></span>
      </div>
    </div>

    <div id="map"></div>

    <script src="bower_components/jquery/dist/jquery.min.js"></script>
    <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="bower_components/select2/dist/js/select2.full.min.js"></script>

    <script>
      var map;
      var markers = [];
      var sharedInfoWindow = null;
      var currentRenderTimeout = null;

      function escapeHtml(text) {
        if (!text) return '';
        var map = {
          '&': '&amp;',
          '<': '&lt;',
          '>': '&gt;',
          '"': '&quot;',
          "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
      }

      function initGoogleMap() {
        var defaultCenter = { lat: 20.5937, lng: 78.9629 };
        map = new google.maps.Map(document.getElementById('map'), {
          zoom: 5,
          center: defaultCenter,
          mapTypeId: google.maps.MapTypeId.ROADMAP,
          mapTypeControl: true,
          streetViewControl: false
        });
        sharedInfoWindow = new google.maps.InfoWindow();
      }

      function clearMarkers() {
        if (currentRenderTimeout) {
          clearTimeout(currentRenderTimeout);
          currentRenderTimeout = null;
        }
        for (var i = 0; i < markers.length; i++) {
          markers[i].setMap(null);
        }
        markers = [];
        if (sharedInfoWindow) {
          sharedInfoWindow.close();
        }
      }

      function renderAllMarkersChunked(outletList, startIndex, chunkSize, bounds, onComplete) {
        var endIndex = Math.min(startIndex + chunkSize, outletList.length);
        var iconUrl = 'https://maps.google.com/mapfiles/ms/icons/red-dot.png';

        for (var i = startIndex; i < endIndex; i++) {
          var item = outletList[i];
          var lat = parseFloat(item.latitude);
          var lng = parseFloat(item.longitude);

          if (isNaN(lat) || isNaN(lng) || (lat === 0 && lng === 0)) {
            continue;
          }

          var latLng = new google.maps.LatLng(lat, lng);
          bounds.extend(latLng);

          var marker = new google.maps.Marker({
            position: latLng,
            map: map,
            title: item.name || 'Outlet',
            // icon: {
            //   url: iconUrl
            // }
          });

          (function(m, d) {
            m.addListener('click', function() {
              var contentString = '<div class="info-window-card">' +
                '<h4><i class="fa fa-shopping-bag"></i> ' + escapeHtml(d.name || 'Outlet') + '</h4>' +
                (d.outlettype ? '<p><span class="meta-label">Type:</span> ' + escapeHtml(d.outlettype) + (d.outletsubtype ? ' (' + escapeHtml(d.outletsubtype) + ')' : '') + '</p>' : '') +
                (d.contactperson ? '<p><span class="meta-label">Contact:</span> ' + escapeHtml(d.contactperson) + (d.contact ? ' - <a href="tel:' + encodeURIComponent(d.contact) + '">' + escapeHtml(d.contact) + '</a>' : '') + '</p>' : (d.contact ? '<p><span class="meta-label">Phone:</span> <a href="tel:' + encodeURIComponent(d.contact) + '">' + escapeHtml(d.contact) + '</a></p>' : '')) +
                (d.city || d.state ? '<p><span class="meta-label">Location:</span> ' + (d.city ? escapeHtml(d.city) + ', ' : '') + (d.state ? escapeHtml(d.state) : '') + '</p>' : '') +
                (d.area ? '<p><span class="meta-label">Route:</span> ' + escapeHtml(d.area) + '</p>' : '') +
                (d.address ? '<p><span class="meta-label">Address:</span> ' + escapeHtml(d.address) + '</p>' : '') +
                '</div>';

              if (sharedInfoWindow) {
                sharedInfoWindow.setContent(contentString);
                sharedInfoWindow.open(map, m);
              }
            });
          })(marker, item);

          markers.push(marker);
        }

        if (endIndex < outletList.length) {
          currentRenderTimeout = setTimeout(function() {
            renderAllMarkersChunked(outletList, endIndex, chunkSize, bounds, onComplete);
          }, 4);
        } else {
          currentRenderTimeout = null;
          if (onComplete) onComplete();
        }
      }

      function loadOutletsOnMap() {
        $("#loader").show();
        $("#map-notice").hide();
        var state = $("#state").val() || '';
        var city = $("#city").val() || '';
        var region = $("#region").val() || '';
        var area = $("#area").val() || '';
        var distributor = $("#distributor").val() || '';

        $.ajax({
          url: 'api/outlets-web.php?showmap=1&state=' + encodeURIComponent(state) + '&city=' + encodeURIComponent(city) + '&region=' + encodeURIComponent(region) + '&area=' + encodeURIComponent(area) + '&distributor=' + encodeURIComponent(distributor),
          type: 'GET',
          dataType: 'json',
          success: function(data) {
            $("#loader").hide();
            clearMarkers();

            if (!data || data.length === 0) {
              $("#total").html('<i class="fa fa-shopping-cart"></i> Total Outlets: 0');
              $("#gt").html('G.T.: 0');
              $("#mt").html('MTS: 0');
              $("#mtl").html('MTL: 0');
              $("#milkbooth").html('Milk Booth: 0');
              $("#wholesaler").html('Wholesaler: 0');
              return;
            }

            var summary = {};
            var outletList = [];

            // Check if last element contains summary
            if (data.length > 0 && data[data.length - 1].summary) {
              summary = data[data.length - 1];
              outletList = data.slice(0, data.length - 1);
            } else {
              summary = data[data.length - 1] || {};
              outletList = data.slice(0, data.length - 1);
            }

            var totalCount = summary.total !== undefined ? summary.total : outletList.length;
            $("#total").html('<i class="fa fa-shopping-cart"></i> Total Outlets: ' + totalCount);
            $("#gt").html('G.T.: ' + (summary.gt || 0));
            $("#mt").html('MTS: ' + (summary.mt || 0));
            $("#mtl").html('MTL: ' + (summary.mtl || 0));
            $("#milkbooth").html('Milk Booth: ' + (summary.milkbooth || 0));
            $("#wholesaler").html('Wholesaler: ' + (summary.wholesaler || 0));

            var bounds = new google.maps.LatLngBounds();

            // Render all markers as direct icons (no cluster bubbles) smoothly in non-blocking chunks
            renderAllMarkersChunked(outletList, 0, 500, bounds, function() {
              if (!bounds.isEmpty()) {
                if (markers.length === 1) {
                  map.setCenter(bounds.getCenter());
                  map.setZoom(14);
                } else {
                  map.fitBounds(bounds);
                }
              }
            });
          },
          error: function(err) {
            $("#loader").hide();
            console.error("Error loading outlet map data:", err);
          }
        });
      }

      function loadStates() {
        $.ajax({
          url: "api/outlets-web.php?getstate",
          type: "GET",
          success: function(data) {
            if (typeof data === 'string') {
              try { data = JSON.parse(data); } catch(e) {}
            }
            var $state = $("#state");
            $state.empty().append('<option value="">All States</option>');
            if (Array.isArray(data)) {
              $.each(data, function(i, item) {
                $state.append($('<option>', { value: item.state, text: item.name }));
              });
            }
          }
        });
      }

      function loadCities(stateId) {
        $.ajax({
          url: "api/outlets-web.php?getcity&state=" + encodeURIComponent(stateId || ''),
          type: "GET",
          success: function(data) {
            if (typeof data === 'string') {
              try { data = JSON.parse(data); } catch(e) {}
            }
            var $city = $("#city");
            $city.empty().append('<option value="">All Cities</option>');
            if (Array.isArray(data)) {
              $.each(data, function(i, item) {
                $city.append($('<option>', { value: item.id, text: item.city }));
              });
            }
          }
        });
      }

      function loadRegions(cityId) {
        $.ajax({
          url: "api/outlets-web.php?getregion&city=" + encodeURIComponent(cityId || ''),
          type: "GET",
          success: function(data) {
            if (typeof data === 'string') {
              try { data = JSON.parse(data); } catch(e) {}
            }
            var $region = $("#region");
            $region.empty().append('<option value="">All Regions</option>');
            if (Array.isArray(data)) {
              $.each(data, function(i, item) {
                $region.append($('<option>', { value: item.region, text: item.name }));
              });
            }
          }
        });
      }

      function loadAreas(regionId) {
        $.ajax({
          url: "api/outlets-web.php?getrouter&region=" + encodeURIComponent(regionId || ''),
          type: "GET",
          success: function(data) {
            if (typeof data === 'string') {
              try { data = JSON.parse(data); } catch(e) {}
            }
            var $area = $("#area");
            $area.empty().append('<option value="">All Routes</option>');
            if (Array.isArray(data)) {
              $.each(data, function(i, item) {
                $area.append($('<option>', { value: item.id, text: item.area }));
              });
            }
          }
        });
      }

      function loadDistributors() {
        $.ajax({
          url: "api/outlets-web.php?getdistributor",
          type: "GET",
          success: function(data) {
            if (typeof data === 'string') {
              try { data = JSON.parse(data); } catch(e) {}
            }
            var $distributor = $("#distributor");
            $distributor.empty().append('<option value="">All Distributors</option>');
            if (Array.isArray(data)) {
              $.each(data, function(i, item) {
                $distributor.append($('<option>', { value: item.id, text: item.name }));
              });
            }
          }
        });
      }

      $(document).ready(function() {
        $('.select2').select2();

        loadStates();
        loadDistributors();

        $("#state").on('change', function() {
          var stateId = $(this).val();
          loadCities(stateId);
          $("#city").val('').trigger('change');
          $("#region").empty().append('<option value="">All Regions</option>').trigger('change');
          $("#area").empty().append('<option value="">All Routes</option>').trigger('change');
        });

        $("#city").on('change', function() {
          var cityId = $(this).val();
          loadRegions(cityId);
          $("#region").val('').trigger('change');
          $("#area").empty().append('<option value="">All Routes</option>').trigger('change');
        });

        $("#region").on('change', function() {
          var regionId = $(this).val();
          loadAreas(regionId);
          $("#area").val('').trigger('change');
        });

        $("#search").on('click', function() {
          loadOutletsOnMap();
        });

        $("#reset").on('click', function() {
          $("#state").val('').trigger('change');
          $("#city").val('').trigger('change');
          $("#region").val('').trigger('change');
          $("#area").val('').trigger('change');
          $("#distributor").val('').trigger('change');
          loadOutletsOnMap();
        });
      });
    </script>

    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCrxsk0fDpJlEqqLXqrdrg833McDrv5apc&callback=initGoogleMap"></script>
  </body>
</html>
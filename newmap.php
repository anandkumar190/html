<?php
$id = isset($_GET['userid']) ? htmlspecialchars($_GET['userid']) : '';
?>
<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
  <meta charset="utf-8">
  <title>Employee Location</title>
  <style>
    #map {
      height: 100%;
    }
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
    }
  </style>
</head>
<body>
  <div id="map"></div>

  <script src="bower_components/jquery/dist/jquery.min.js"></script>
  <script>
    var map;
    var marker;

    function initMap() {
      $.ajax({
        url: "api/login.php?get&userid=<?= $id ?>",
        type: "GET",
        dataType: "json",
        success: function(data) {
          var lat = parseFloat(data[0].latitude);
          var lng = parseFloat(data[0].longitude);
          var locationDate = new Date(Date.parse(data[0].locationdate));
          var name = data[0].name;
          var myLatLng = { lat: lat, lng: lng };

          map = new google.maps.Map(document.getElementById('map'), {
            zoom: 14,
            center: myLatLng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
          });

          marker = new google.maps.Marker({
            position: myLatLng,
            map: map,
            title: `${name} - ${locationDate.toDateString()} - ${locationDate.toLocaleTimeString()}`,
            icon: 'map-marker-red.png'
          });
        },
        error: function(xhr) {
          alert("Failed to load data");
          console.error(xhr);
        }
      });
    }

    function updateLocation() {
      $.ajax({
        url: "api/login.php?get&userid=<?= $id ?>",
        type: "GET",  // Changed from POST to GET to match API usage
        dataType: "json",
        success: function(data) {
          var lat = parseFloat(data[0].latitude);
          var lng = parseFloat(data[0].longitude);
          var latlng = new google.maps.LatLng(lat, lng);
          if (marker) {
            marker.setPosition(latlng);
          }
        }
      });
    }

    setInterval(updateLocation, 5000); // Avoid too-frequent updates (was 200ms!)

  </script>
  <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCrxsk0fDpJlEqqLXqrdrg833McDrv5apc&callback=initMap">
  </script>
</body>
</html>

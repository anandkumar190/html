<?php 
//$lat=$_GET['lat'];
//$lng=$_GET['lat'];
?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Employee Location</title>
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 100%;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
	  
	  #description {
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
      }

      #infowindow-content .title {
        font-weight: bold;
      }

      #infowindow-content {
        display: none;
      }

      #map #infowindow-content {
        display: inline;
      }

      
    </style>
  </head>
  <body>
     
    <div id="map"></div>
    
<script src="bower_components/jquery/dist/jquery.min.js"></script>      
    <script>
      var map;
	  var marker;
	  
	      /* if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(showPosition);
                   }*/
	  
	        var strlat='<?php echo $lat?>';
	        var strlng='<?php echo $lng?>';
	         if(strlat=='' || strlng=='' || strlat=="0.0000000000" || strlat=="0.0000000000")
			 {
				 
				 if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(showPosition);
                   }
				   
			 }
	  function showPosition(position)
				   {
					   
					   strlat=position.coords.latitude;
					   strlng=position.coords.longitude;
					   initMap();
					  			   
				   }  	   	  
	  function initMap() {
      
	        
		     var lat=parseFloat(strlat);
			 var lng=parseFloat(strlng);
			 var myLatLng = {lat:lat, lng:lng};
			     
		     map = new google.maps.Map(document.getElementById('map'), {
             zoom: 14,
             center: myLatLng,
		     mapTypeId:google.maps.MapTypeId.ROADMAP
             });
		    
		    marker = new google.maps.Marker({
            position: myLatLng,
			draggable: true,
            animation: google.maps.Animation.DROP,
            map: map,
            
           });
		  
		   marker.addListener('click', toggleBounce);
		   
      }
	  function toggleBounce() {
			$("#emplat").val(marker.getPosition().lat());
            $("#emplng").val(marker.getPosition().lng());
			$("#locationmodel").modal('hide');
        }
	  
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD84GbCm3s_24FlRO1GwuG-vxGsJObga3U&callback=initMap">
    </script>
  </body>
</html>